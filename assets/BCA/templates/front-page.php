<?php while (have_posts()) : the_post(); ?>
<?php if ( get_page_template_slug(get_the_ID())) : ?>
<?php the_content(); ?>
<?php else: ?>
<div class="row">
  <div class="col-md-5 col-md-push-3">
    <?php if (get_the_content()) : ?>
    <main>
      <?php if (get_the_title()) : ?>
      <header class="page-header">
        <h1><?php echo roots_title() ?></h1>
      </header>
      <?php endif; ?>
      <?php the_content() ?>
    </main>
    <hr>
    <?php endif; ?>
    <aside class="sidebar-front-center">
      <?php dynamic_sidebar('sidebar-front-center'); ?>
    </aside>
  </div>

  <aside class="col-md-4 col-md-push-3 sidebar-front-right">
    <?php dynamic_sidebar('sidebar-front-right'); ?>
  </aside>

  <aside class="col-md-3 col-md-pull-9 sidebar-front-left">
    <?php dynamic_sidebar('sidebar-front-left'); ?>
  </aside>

</div>
<?php endif; ?>
<?php endwhile;
