<?php
    $footer_menu = array(
    'fallback_cb'     => false,
    'depth'           => 2,
    'walker' => new Walker_Nav_Menu
    );
?>
<footer class="wrapper-footer">
    <div class="content-info container" role="contentinfo">
      <div class="row">
          <div class="col-sm-9">
            <div class="row">
                <div class="col-sm-3">
                    <?php wp_nav_menu(array_merge(array('theme_location' => 'footer_one'), $footer_menu)); ?>
                </div>
                <div class="col-sm-3">
                    <?php wp_nav_menu(array_merge(array('theme_location' => 'footer_two'), $footer_menu)); ?>
                </div>
                <div class="col-sm-3">
                    <?php wp_nav_menu(array_merge(array('theme_location' => 'footer_three'), $footer_menu)); ?>
                </div>
                <div class="col-sm-3">
                    <?php wp_nav_menu(array_merge(array('theme_location' => 'footer_four'), $footer_menu)); ?>
                </div>
            </div>
          </div>
        <div class="col-sm-3">
            <?php dynamic_sidebar('sidebar-footer') ?>
            <p class="copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></p>
        </div>
      </div>
    </div>
</footer>


<?php wp_footer(); ?>
