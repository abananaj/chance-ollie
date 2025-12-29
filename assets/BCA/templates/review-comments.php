<?php
  if (post_password_required()) {
    return;
  }
?>

<?php get_template_part('templates/comment-list', get_post_type()); ?>
