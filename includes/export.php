<?php
/** Shared export helpers (CSV, PDF) */
if (!function_exists('export_csv')) {
function export_csv(string $filename, array $headers, array $rows): void {
  header('Content-Type: text/csv; charset=UTF-8');
  header('Content-Disposition: attachment; filename="'.$filename.'"');
  header('Pragma: no-cache'); header('Expires: 0');
  echo "\xEF\xBB\xBF"; // BOM
  $out=fopen('php://output','w');
  fputcsv($out,$headers);
  foreach($rows as $r){
    $clean=[]; foreach($r as $v){ if(is_null($v)) $v=''; if(is_array($v)) $v=json_encode($v); $clean[]=strip_tags((string)$v); } fputcsv($out,$clean);
  }
  fclose($out); exit;
}}
if (!function_exists('export_pdf')) {
function export_pdf(string $filename,string $title,array $headers,array $rows): void {
  $autoload=__DIR__.'/../vendor/autoload.php'; if(file_exists($autoload)) require_once $autoload; else { export_csv(preg_replace('/\.pdf$/i','.csv',$filename),$headers,$rows); }
  $html='<html><head><meta charset="UTF-8"><style>body{font-family:DejaVu Sans,Arial,sans-serif;font-size:11px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ccc;padding:4px;}th{background:#f5f5f5;}h1{font-size:16px;margin:0 0 10px;}.small{font-size:10px;color:#666;margin-top:4px;}</style></head><body>';
  $html.='<h1>'.htmlspecialchars($title).'</h1><table><thead><tr>'; foreach($headers as $h){ $html.='<th>'.htmlspecialchars($h).'</th>'; } $html.='</tr></thead><tbody>';
  foreach($rows as $r){ $html.='<tr>'; foreach($r as $v){ if(is_array($v)) $v=json_encode($v); $html.='<td>'.htmlspecialchars(strip_tags((string)$v)).'</td>'; } $html.='</tr>'; }
  $html.='</tbody></table><div class="small">Generated on '.date('Y-m-d H:i:s').'</div></body></html>';
  try { $dompdf=new Dompdf\Dompdf(['isRemoteEnabled'=>true]); $dompdf->loadHtml($html,'UTF-8'); $dompdf->setPaper('A4','landscape'); $dompdf->render(); header('Content-Type: application/pdf'); header('Content-Disposition: attachment; filename="'.$filename.'"'); echo $dompdf->output(); }
  catch(Exception $e){ export_csv(preg_replace('/\.pdf$/i','.csv',$filename),$headers,$rows); }
  exit;
}}
?>
