<?php echo get_avatar($comment, $size = '64'); ?>
<div class="media-body">

  <?php comment_text(); ?>

  <?php if ($comment->comment_approved == '0') : ?>
    <p class="text-info">
      <i class="fa fa-info-circle"></i>
      <?php _e('Your review is awaiting moderation.', 'roots'); ?>
    </p>
  <?php endif; ?>

  <div class="text-muted">&mdash; <?php echo get_comment_author_link(); ?></div>
  <?php edit_comment_link(__('(Edit)', 'roots'), '', ''); ?>
