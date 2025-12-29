<?php get_template_part('templates/head'); ?>
<body <?php body_class() ?>>

  <br>
  <div class="container" role="document">
    <div class="content row">
      <div class="col-xs-3">
        <div class="left">
          <img src="/app/assets/img/BATAC.png" alt="<?php bloginfo('name'); ?>" class="logo">
          <?php
            $menu = ['fallback_cb' => false, 'depth' => 1, 'walker' => new Walker_Nav_Menu];
            wp_nav_menu(array_merge(['theme_location' => 'kiosk_main'], $menu));
          ?>
        </div>
      </div>
      <div class="main col-xs-9" role="main">
        <?php include roots_template_path(); ?>
      </div><!-- /.main -->
    </div><!-- /.content -->
    <div class="content row">
      <div class="col-xs-3"></div>
      <div class="col-xs-9">
        <footer>Copyright &copy; <?php echo date('Y')?> Chance Theater. All rights reserved.</footer>
      </div>
    </div>
  </div><!-- /.wrap -->


</body>
</html>
