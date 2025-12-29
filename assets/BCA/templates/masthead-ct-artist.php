<?php
  use BCA\ChanceTheater\Helpers;
?>
<header class="masthead-inner">
  <?php
    if (has_post_thumbnail()) {
      echo get_the_post_thumbnail(null, 'square-125', array('class' => 'pull-left'));
    }
  ?>
  <h1>
    <?php the_title(); ?>
    <small><?php echo get_post_meta(get_the_id(), 'profession', true) ?></small>
  </h1>

  <ul class="artist-contact">
    <?php if ($src = get_post_meta(get_the_id(), 'twitter', true)) : ?>
    <li>
      <a href="<?php echo $src ?>" data-toggle="tooltip" data-placement="bottom" title="Twitter">
        <span class="fa-stack">
          <i class="fa fa-circle fa-stack-2x"></i>
          <i class="fa fa-twitter fa-stack-1x fa-inverse"></i>
        </span>
      </a>
    </li>
    <?php endif; ?>
    <?php if ($src = get_post_meta(get_the_id(), 'facebook', true)) : ?>
    <li>
      <a href="<?php echo $src ?>" data-toggle="tooltip" data-placement="bottom" title="Facebook">
        <span class="fa-stack">
          <i class="fa fa-circle fa-stack-2x"></i>
          <i class="fa fa-facebook fa-stack-1x fa-inverse"></i>
        </span>
      </a>
    </li>
    <?php endif; ?>
    <?php if ($src = get_post_meta(get_the_id(), 'linkedin', true)) : ?>
    <li>
      <a href="<?php echo $src ?>" data-toggle="tooltip" data-placement="bottom" title="LinkedIn">
        <span class="fa-stack">
          <i class="fa fa-circle fa-stack-2x"></i>
          <i class="fa fa-linkedin fa-stack-1x fa-inverse"></i>
        </span>
      </a>
    </li>
    <?php endif; ?>
    <?php if ($src = get_post_meta(get_the_id(), 'website', true)) : ?>
    <li>
      <a href="<?php echo $src ?>" data-toggle="tooltip" data-placement="bottom" title="Homepage">
        <span class="fa-stack">
          <i class="fa fa-circle fa-stack-2x"></i>
          <i class="fa fa-home fa-stack-1x fa-inverse"></i>
        </span>
      </a>
    </li>
    <?php endif; ?>
  </ul>
  <?php echo Helpers::getReferrerButton() ?>
  <div class="clearfix"></div>
</header>
