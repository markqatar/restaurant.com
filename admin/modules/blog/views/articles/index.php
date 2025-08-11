<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/ArticleController.php';
require_once '../includes/functions.php';

// Check if user is logged in and has permission
if (!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'], 'articles', 'view')) {
    header('Location: /admin/login');
    exit;
}

$controller = new ArticleController($pdo);

// Handle different actions
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'delete':
        if ($id) { $controller->delete($id); }
        break;
    case 'index':
    default:
        $data = $controller->index();
        break;
}

$pageTitle = TranslationManager::t('articles');
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2><?php echo TranslationManager::t('articles'); ?></h2>
                        <p class="text-muted">Gestisci gli articoli del blog</p>
                    </div>
                    <div>
                        <?php if (has_permission($_SESSION['user_id'], 'categories', 'view')): ?>
                            <a href="categories.php" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-tags"></i> <?php echo TranslationManager::t('categories'); ?>
                            </a>
                        <?php endif; ?>
                        <?php if (has_permission($_SESSION['user_id'], 'articles', 'create')): ?>
                            <a href="articles-create.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> <?php echo TranslationManager::t('add_article'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                                <div class="card">
                                        <div class="card-body">
                                                <div class="row mb-3 g-2 align-items-end">
                                                    <div class="col-md-2">
                                                        <label class="form-label mb-1">Status</label>
                                                        <select id="filterStatus" class="form-select form-select-sm">
                                                            <option value="">--</option>
                                                            <option value="draft">Draft</option>
                                                            <option value="published">Published</option>
                                                            <option value="private">Private</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label mb-1">Categoria</label>
                                                        <select id="filterCategory" class="form-select form-select-sm">
                                                            <option value="">--</option>
                                                            <?php foreach(($data['categories']??[]) as $c): ?>
                                                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-7 text-end">
                                                        <div class="btn-group" role="group">
                                                            <button id="btnExportCsv" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-csv"></i> CSV</button>
                                                            <button id="btnExportPrint" class="btn btn-outline-secondary btn-sm"><i class="fas fa-print"></i> Print</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="table-responsive">
                            <table class="table table-striped" id="articlesTable">
                                <thead>
                                                                        <tr>
                                                                            <th><?php echo TranslationManager::t('id'); ?></th>
                                                                            <th>Anteprima</th>
                                                                            <th><?php echo TranslationManager::t('article_title'); ?></th>
                                                                            <th><?php echo TranslationManager::t('category'); ?></th>
                                                                            <th><?php echo TranslationManager::t('author'); ?></th>
                                                                            <th><?php echo TranslationManager::t('article_status'); ?></th>
                                                                            <th>Visualizzazioni</th>
                                                                            <th><?php echo TranslationManager::t('published_at'); ?></th>
                                                                            <th><?php echo TranslationManager::t('actions'); ?></th>
                                                                        </tr>
                                </thead>
                                                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let dt;
$(document).ready(function(){
    dt=$('#articlesTable').DataTable({
        serverSide:true, processing:true, responsive:true,
        ajax:{ url:'articles.php?datatable=1', data:function(d){ d.status=$('#filterStatus').val(); d.category_id=$('#filterCategory').val(); }, dataSrc:'data'},
        order:[[0,'desc']], pageLength:25,
        columns:[
            {data:'id'},
            {data:'featured_image', render:function(d,t,r){ if(d){ return '<img src="'+escapeHtml(d)+'" class="img-thumbnail" style="max-width:60px;max-height:40px;" />'; } return '<span class="text-muted"><i class="fas fa-image"></i></span>'; }},
            {data:null, render:function(r){ let html='<strong>'+escapeHtml(r.title||'')+'</strong>'; if(r.is_featured==1) html+=' <span class="badge bg-warning text-dark ms-1">Featured</span>'; html+='<br><small class="text-muted">'+escapeHtml(r.slug||'')+'</small>'; if(r.excerpt){ html+='<br><small class="text-muted">'+escapeHtml(r.excerpt.substring(0,80))+'...</small>'; } return html; }},
            {data:'category_name', render:function(d){ return d? '<span class="badge bg-info">'+escapeHtml(d)+'</span>':'<span class="text-muted">Nessuna</span>'; }},
            {data:'author_name', defaultContent:''},
            {data:'status', render:function(d){ let cls=d==='published'?'bg-success':(d==='draft'?'bg-warning':'bg-secondary'); return '<span class="badge '+cls+'">'+escapeHtml(d)+'</span>'; }},
            {data:'views', render:function(d){ return '<span class="badge bg-light text-dark">'+(d||0)+'</span>'; }},
            {data:'published_at', render:function(d){ if(!d) return '<span class="text-muted">--</span>'; return new Date(d).toLocaleDateString(); }},
            {data:null, orderable:false, searchable:false, render:function(r){ let btns=''; if(r.can_edit){ btns+='<a href="articles-edit.php?id='+r.id+'" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>'; } if(r.can_delete){ btns+=' <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('+r.id+',\''+escapeHtml(r.title)+'\')"><i class="fas fa-trash"></i></button>'; } return btns; }}
        ],
            language:{ url:'//cdn.datatables.net/plug-ins/1.13.7/i18n/<?php echo $language; ?>.json' }
    });
        $('#filterStatus, #filterCategory').on('change', ()=> dt.ajax.reload());
        $('#btnExportCsv').on('click', function(){ exportCsv(); });
        $('#btnExportPrint').on('click', function(){ printTable(); });
});
function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]; }); }

    function exportCsv(){
        const data = dt.rows({search:'applied'}).data();
        const headers=['ID','Title','Category','Status','Views','Published'];
        let csv=headers.join(',')+'\n';
        for(let i=0;i<data.length;i++){
            const r=data[i];
            csv += [r.id, '"'+(r.title||'').replace(/"/g,'""')+'"', '"'+(r.category_name||'')+'"', r.status, r.views, r.published_at||''].join(',')+'\n';
        }
        const blob=new Blob([csv],{type:'text/csv;charset=utf-8;'});
        const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='articles.csv'; a.click();
    }
    function printTable(){
        const data = dt.rows({search:'applied'}).data();
        let html='<table border="1" cellspacing="0" cellpadding="4"><thead><tr><th>ID</th><th>Title</th><th>Category</th><th>Status</th><th>Views</th><th>Published</th></tr></thead><tbody>';
        for(let i=0;i<data.length;i++){ const r=data[i]; html+='<tr><td>'+r.id+'</td><td>'+escapeHtml(r.title||'')+'</td><td>'+escapeHtml(r.category_name||'')+'</td><td>'+escapeHtml(r.status||'')+'</td><td>'+(r.views||0)+'</td><td>'+(r.published_at||'')+'</td></tr>'; }
        html+='</tbody></table>';
        const w=window.open('','printWin'); w.document.write('<html><head><title>Articles</title></head><body>'+html+'</body></html>'); w.document.close(); w.print();
    }

function confirmDelete(id, title) {
    Swal.fire({
        title: '<?php echo TranslationManager::t('confirm_delete'); ?>',
        text: 'Sei sicuro di voler eliminare l\'articolo "' + title + '"?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<?php echo TranslationManager::t('yes_delete'); ?>',
        cancelButtonText: '<?php echo TranslationManager::t('cancel'); ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'articles.php?action=delete&id=' + id;
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>