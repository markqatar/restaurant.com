<?php
function resolve_address_field_rules($state_id = null, $delivery_area_id = null){
    static $inRequestCache = [];
    $cacheKey = ($state_id?:'null').'-'.($delivery_area_id?:'null');
    if(isset($inRequestCache[$cacheKey])) return $inRequestCache[$cacheKey];
    // Try APCu cache (5 min)
    // simple per-request cache only (can extend with APCu in production)
    $db = Database::getInstance()->getConnection();
    $sql = "SELECT * FROM address_field_rules WHERE active=1 ORDER BY sort_order, id";
    $rules = $db->query($sql)->fetchAll();
    // Precedence: exact (state+delivery_area) > delivery_area only > state only > global
    $bucket = [];
    foreach ($rules as $r){
        $keyPrec = 4; // lowest
        if(!is_null($r['state_id']) && !is_null($r['delivery_area_id'])) $keyPrec=1;
        elseif(!is_null($r['delivery_area_id'])) $keyPrec=2;
        elseif(!is_null($r['state_id'])) $keyPrec=3;
        else $keyPrec=4;
        // Skip if doesn't match requested context
        if($keyPrec==1 && ($r['state_id']!=$state_id || $r['delivery_area_id']!=$delivery_area_id)) continue;
        if($keyPrec==2 && $r['delivery_area_id']!=$delivery_area_id) continue;
        if($keyPrec==3 && $r['state_id']!=$state_id) continue;
        $field_key = $r['field_key'];
        if(!isset($bucket[$field_key]) || $bucket[$field_key]['prec'] > $keyPrec){
            $bucket[$field_key] = [
                'field_key'=>$field_key,
                'requirement'=>$r['requirement'],
                'label'=>$r['label'],
                'prec'=>$keyPrec,
                'sort_order'=>$r['sort_order'],
            ];
        }
    }
    // Reorder by sort_order
    usort($bucket, fn($a,$b)=>$a['sort_order']<=>$b['sort_order']);
    $result = array_values(array_map(fn($v)=>[
        'field_key'=>$v['field_key'],
        'requirement'=>$v['requirement'],
        'label'=>$v['label']
    ], $bucket));
    $inRequestCache[$cacheKey] = $result;
    // end cache
    return $result;
}

function validate_address_extra(array $extra, array $rules){
    $errors=[];
    $ruleIndex = [];
    foreach($rules as $r){ $ruleIndex[$r['field_key']] = $r; }
    foreach($ruleIndex as $key=>$r){
        if($r['requirement']==='required' && (!isset($extra[$key]) || $extra[$key]==='')){
            $errors[] = "$key required";
        }
    }
    // Remove hidden fields if present
    foreach($ruleIndex as $key=>$r){
        if($r['requirement']==='hidden' && isset($extra[$key])){
            unset($extra[$key]);
        }
    }
    return [$errors, $extra];
}
