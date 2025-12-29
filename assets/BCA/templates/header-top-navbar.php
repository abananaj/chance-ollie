<header class="banner navbar navbar-fixed-top navbar-default navbar-static-top" role="banner">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo home_url(); ?>/">
        <img src="/app/assets/img/logo.svg" alt="<?php bloginfo('name'); ?>" class="brand">
        <span class="brand sr-only"><?php bloginfo('name'); ?></span>
      </a>
    </div>

    <nav class="collapse navbar-collapse" role="navigation">
      <ul class="nav navbar-nav navbar-right navbar-icons">
        <?php if (is_user_logged_in()) : ?>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="/sample-page/">
            <i class="fa fa-user hidden-collapse dropdown-icon"></i>
            <span class="visible-collapse">
              WordPress Admin
              <i class="fa fa-angle-down"></i>
            </span>
          </a>
          <ul class="dropdown-menu">
            <li role="presentation" class="dropdown-header">
              <?php $user = wp_get_current_user() ?>
              <div class="user">
                <div class="user-avatar">
                  <?php echo get_avatar($user->ID, array(20, 20)) ?>
                </div>
                <div class="user-name">
                  <?php echo $user->display_name; ?>
                </div>
              </div>
            </li>
            <li><a href="<?php echo admin_url() ?>">My Dashboard</a></li>
            <li><?php edit_post_link('Edit This Page'); ?></li>
            <li><a href="<?php echo wp_logout_url(get_permalink()) ?>">Logout</a></li>
          </ul>
        </li>
        <?php endif; ?>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="/sample-page/">
            <i class="fa fa-search  hidden-collapse dropdown-icon"></i>
            <span class="visible-collapse">
              Search
              <i class="fa fa-angle-down"></i>
            </span>
          </a>
          <div class="dropdown-menu dropdown-search">
            <?php get_search_form(); ?>
          </div>
        </li>
      </ul>
      <?php wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'nav navbar-primary navbar-nav navbar-right')) ?>
    </nav>
  </div>
</header>
