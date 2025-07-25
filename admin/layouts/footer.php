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