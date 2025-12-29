<?php
  if (get_post_type() === 'ct-kiosk') {
    get_template_part('base-ct-kiosk');
    return;
  }
?>

<?php use BCA\ChanceTheater\Models\Productions as ProductionsModel; ?>

<?php get_template_part('templates/head'); ?>
<body <?php body_class() ?>>

  <?php
    $include = realpath(ABSPATH.'/../../ct_body.inc.php');
    if ($include) {
      include_once $include;
    }
    do_action('get_header');
    get_template_part('templates/header-top-navbar');
  ?>

  <div class="masthead hidden-xs">
    <?php
      if (is_singular() && get_post_type() !== 'attachment') :
        // Get Masthead Images
        $mh_images = (array) get_post_meta($post->ID, 'masthead', false);

        $mh_html = array();
        foreach ($mh_images as $mh_item) {
          $mh_item_html = wp_get_attachment_image(
            $mh_item['img'],
            'masthead'
          );

          if ($mh_item_html && !empty($mh_item['url'])) {
            $mh_item_html = '<a href="'.$mh_item['url'].'">'
              .$mh_item_html.'</a>';
          }
          $mh_html[] = $mh_item_html;
        }
    ?>
    <div id="carousel" class="carousel" data-ride="carousel">
      <?php if (count($mh_html) > 1): ?>
      <ol class="carousel-indicators">
        <?php foreach ($mh_html as $carousel_item_id=>$carousel_item): ?>
        <li data-target="#carousel" data-slide-to="<?php echo $carousel_item_id ?>" class="<?php if ($carousel_item_id === 0): ?>active<?php endif; ?>"></li>
        <?php endforeach; ?>
      </ol>
      <?php endif; ?>
      <div class="carousel-inner" role="listbox">
        <?php foreach ($mh_html as $carousel_item_id=>$carousel_item): ?>
        <div class="item <?php if ($carousel_item_id === 0): ?>active<?php endif; ?>">
          <?=$carousel_item?>
        </div>
      <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <?php if (is_404()) : ?>
    <?php include roots_template_path(); ?>
  <?php else: ?>
  <div class="wrap container" role="document">
    <div class="content row">
      <div class="main <?php echo roots_main_class(); ?>" role="main">
        <!--[if lt IE 8]>
        <div class="alert alert-danger">
          <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'roots'); ?>
        </div>
        <![endif]-->
        <?php include roots_template_path(); ?>
      </div><!-- /.main -->
      <?php if (roots_display_sidebar()) : ?>
      <aside class="sidebar-primary <?php echo roots_sidebar_class(); ?>" role="complementary">
        <div class="sidebar-inner">
          <?php include roots_sidebar_path(); ?>
        </div>
      </aside><!-- /.sidebar -->
      <?php endif; ?>
    </div><!-- /.content -->
  </div><!-- /.wrap -->
  <?php endif; ?>

  <?php get_template_part('templates/footer'); ?>

  <!-- Popups Modal -->
  <div>
    <div class="modal fade" id="modal_popup" tabindex="-1" role="dialog" aria-labelledby="modal_popup_label" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"></h4>
          </div>
          <div class="modal-body">
            <iframe width="100%" height="400" frameborder="0">Loading...</iframe>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Mailing Lists Modal -->
  <div>
    <div class="modal fade" id="modal_mailing_list" tabindex="-1" role="dialog" aria-labelledby="modal_mailing_list_label" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modal_mailing_list_label"><?php echo of_get_option('mailing_list_heading', 'Join our mailing lists!') ?></h4>
          </div>
          <div class="modal-body"><?php echo do_shortcode(of_get_option('mailing_list_body', 'This feature has not been configured.')) ?></div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
