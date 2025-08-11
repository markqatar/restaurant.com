    </div>
    </main>
    <!--end main wrapper-->


    <!--start overlay-->
    <div class="overlay btn-toggle"></div>
    <!--end overlay-->

    <!-- Footer -->
    <footer class="page-footer">
      <p class="mb-0">Copyright © 2023. <?php echo TranslationManager::t('Restaurant Management System'); ?>. <?php echo TranslationManager::t('All right reserved'); ?>.</p>
    </footer>

    <!-- Local JS Files -->
    <script src="<?php echo $siteUrl; ?>/admin/assets/js/jquery.min.js"></script>
    <script src="<?php echo $siteUrl; ?>/admin/assets/js/bootstrap.bundle.min.js"></script>

    <!-- Plugins JS -->
    <script src="<?php echo $siteUrl; ?>/admin/assets/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="<?php echo $siteUrl; ?>/admin/assets/plugins/metismenu/metisMenu.min.js"></script>
    <script src="<?php echo $siteUrl; ?>/admin/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>

    <script src="<?php echo $siteUrl; ?>/admin/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo $siteUrl; ?>/admin/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/mark.js@8.11.1/dist/mark.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/datatables.mark.js/dist/datatables.mark.min.js"></script>
    <script src="<?php echo $siteUrl; ?>/admin/assets/plugins/tinymce/tinymce.min.js"></script>

    <!-- Main JS -->
    <script src="<?php echo $siteUrl; ?>/admin/assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Page-specific scripts -->
    <?php if (isset($pageScripts)): ?>
      <?php foreach ($pageScripts as $script): ?>
        <script src="<?php echo $script; ?>"></script>
      <?php endforeach; ?>
    <?php endif; ?>
    <script>
    if (window.jQuery && $.fn.dataTable) {
      $.extend(true, $.fn.dataTable.defaults, { responsive: true, mark: true });
    }
    document.addEventListener('init.dt', function(e, settings){
      if(!window.jQuery) return;
      var api = new $.fn.dataTable.Api(settings);
      var ajax = settings.oInit.ajax || settings.ajax; if(!ajax) return;
      var url = typeof ajax === 'string' ? ajax : ajax.url; if(!url) return;
      var tableContainer = $(api.table().container());
      if(tableContainer.prev('.dt-export-bar').length) return;
      var bar = $('<div class="dt-export-bar mb-2 d-flex gap-2 flex-wrap"></div>');
      function buildUrl(fmt){ var search = api.search(); var base = url + (url.indexOf('?')>-1?'&':'?') + 'export=' + fmt; if(search){ base += '&search=' + encodeURIComponent(search); } return base; }
      ['csv','pdf'].forEach(function(fmt){ $('<button type="button" class="btn btn-sm btn-outline-secondary">'+fmt.toUpperCase()+' Export</button>').on('click', function(){ window.open(buildUrl(fmt),'_blank'); }).appendTo(bar); });
      tableContainer.before(bar);
    });
    </script>
    <?php if ($notification = get_notification()): ?>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          Swal.fire({
            icon: '<?php echo $notification['type'] === 'danger' ? 'error' : $notification['type']; ?>',
            title: '<?php echo addslashes($notification['message']); ?>',
            showConfirmButton: true
          });
        });
      </script>
    <?php endif; ?>
    </body>

    </html>