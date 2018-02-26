
    <!-- Le javascript
      ================================================== -->
      <!-- Placed at the end of the document so the pages load faster -->
      <?php
      ?>
      <script src="js/index_select_rows.js"> </script>

      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
      <script src="js/style/bootstrap.min.js"></script>
      <script>$(function () {
  $('[data-toggle="popover"]').popover() //active le popover sur info
})</script>

<?php if (!empty($js_for_layout)): ?>
  <?php foreach ($js_for_layout as $v):?>
    <?php if (file_exists('js/'.$v.'.js')){ ?>
    <script src="js/<?= $v; ?>.js"></script>
    <?php }elseif(file_exists('js/'.$v)){ ?>
    <script src="js/<?= $v; ?>"></script>
    <?php }elseif(false !== strpos($v, '<script type="text/javascript">')){ ?>
      <?= $v ?>
      <?php }else{ ?>
      <script type="text/javascript">

      </script>
      <?php } ?>
    <?php endforeach ?>
  <?php endif ?>
</body>
</html>