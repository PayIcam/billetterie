</div>
</main>
<footer class="page-footer transparent">
    <div class="footer-copyright">
    <div class="container">
      <div class="row">
        <div class="col s6 left-align">
          Payicam Production</div>
        <div class="col s6 right-align">
         <!--  <a href="backoffice/homepage.php">Backoffice</a> -->
        </div>
      </div>
      </div>
    </div>
  </footer>

  <!--  Scripts-->
  <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <script src="<?= $RouteHelper->publicPath ?>js/bootstrap.min.js"></script>

<?php 
if (isset($js_for_layout)){ ?>

  <script src="<?= $RouteHelper->publicPath ?>js/<?= $js_for_layout ?>"></script>

<?php } ?>

  </body>
</html>