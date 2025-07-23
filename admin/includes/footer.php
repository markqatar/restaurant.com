    </div>
  </main>
  <!--end main wrapper-->


    <!--start overlay-->
    <div class="overlay btn-toggle"></div>
    <!--end overlay-->

      <!-- Footer -->
      <footer class="page-footer">
              <p class="mb-0">Copyright © 2023. <?php echo translate('Restaurant Management System'); ?>. <?php echo translate('All right reserved'); ?>.</p>
      </footer>

  <!-- Local JS Files -->
  <script src="<?php echo $siteUrl; ?>/admin/assets/js/jquery.min.js"></script>
  <script src="<?php echo $siteUrl; ?>/admin/assets/js/bootstrap.bundle.min.js"></script>
  
  <!-- Plugins JS -->
  <script src="<?php echo $siteUrl; ?>/admin/assets/plugins/simplebar/js/simplebar.min.js"></script>
  <script src="<?php echo $siteUrl; ?>/admin/assets/plugins/metismenu/metisMenu.min.js"></script>
  <script src="<?php echo $siteUrl; ?>/admin/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
  
  <?php if (basename($_SERVER['PHP_SELF']) == 'branches.php' || basename($_SERVER['PHP_SELF']) == 'users.php'): ?>
  <!-- DataTable JS -->
  <script src="<?php echo $siteUrl; ?>/admin/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo $siteUrl; ?>/admin/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.datatable').DataTable();
    });
  </script>
  <?php endif; ?>
  
  <!-- Main JS -->
  <script src="<?php echo $siteUrl; ?>/admin/assets/js/main.js"></script>

  <!-- IonIcons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

  <!-- Page-specific scripts -->
  <?php if (isset($pageScripts)): ?>
    <?php foreach ($pageScripts as $script): ?>
      <script src="<?php echo $siteUrl; ?>/<?php echo $script; ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>

</body>
</html>