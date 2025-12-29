<?php
  global $user_identity, $req, $comment_author, $comment_author_email;
  global $comment_author_url, $comment_id_fields;
?>

<?php if (comments_open()) : ?>
  <section id="respond">
    <h2>
      See the show?
      <small>Share your experience with us...</small>
    </h2>
    <p class="cancel-comment-reply"><?php cancel_comment_reply_link(); ?></p>
    <?php if (get_option('comment_registration') && !is_user_logged_in()) : ?>
      <p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.', 'roots'), wp_login_url(get_permalink())); ?></p>
    <?php else : ?>
      <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform" class="form">
        <?php if (is_user_logged_in()) : ?>
          <p>
            <?php printf(__('Logged in as <a href="%s/wp-admin/profile.php" class="text-info">%s</a>.', 'roots'), get_option('siteurl'), $user_identity); ?>
            <a href="<?php echo wp_logout_url(get_permalink()); ?>" class="small" title="<?php __('Log out of this account', 'roots'); ?>"><?php _e('Log out &raquo;', 'roots'); ?></a>
          </p>
        <?php else : ?>
          <div class="form-group">
            <label for="author"><?php _e('Full Name', 'roots'); if ($req) _e(' (required)', 'roots'); ?></label>
            <input type="text" class="form-control" name="author" id="author" value="<?php echo esc_attr($comment_author); ?>" size="22" placeholder="John Smith" <?php if ($req) echo 'aria-required="true"'; ?>>
          </div>
          <div class="form-group">
            <label for="email"><?php _e('Email Address', 'roots'); if ($req) _e(' (required)', 'roots'); ?></label>
            <div class="help-block">Don't worry, we'll keep this private.</div>
            <input type="email" class="form-control" name="email" id="email" value="<?php echo esc_attr($comment_author_email); ?>" size="22" placeholder="john@example.com" <?php if ($req) echo 'aria-required="true"'; ?>>
          </div>
        <?php endif; ?>
        <div class="form-group">
          <label for="comment"><?php _e('Your Review', 'roots'); ?></label>
          <div class="help-block">
            Was there a moment that stood out? Did something surprise you? Positive or constructive, your comments are always appreciated.
          </div>
          <textarea name="comment" id="comment" class="form-control" rows="3" aria-required="true"></textarea>
        </div>
        <p><input name="submit" class="btn btn-sm btn-primary" type="submit" id="submit" value="<?php _e('Submit', 'roots'); ?>"></p>
        <?php comment_id_fields(); ?>
        <?php do_action('comment_form', $post->ID); ?>
      </form>
    <?php endif; ?>
  </section><!-- /#respond -->
<?php endif; ?>
