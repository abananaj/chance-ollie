<?php
  if (post_password_required()) {
    return;
  }
?>

<?php get_template_part('templates/comment-list', get_post_type()); ?>

<?php if (!have_comments() && !comments_open() && !is_page() && post_type_supports(get_post_type(), 'comments')) : ?>
  <section id="comments">
    <div class="alert">
      <?php _e('Comments are closed.', 'roots'); ?>
    </div>
  </section><!-- /#comments -->
<?php endif; ?>

<?php get_template_part('templates/comment-form', get_post_type()); ?>
