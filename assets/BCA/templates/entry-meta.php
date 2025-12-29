<div class="metadata">
    <div class="pubdate">
        <span class="fa-stack">
            <i class="fa fa-circle fa-stack-2x"></i>
            <i class="fa fa-calendar fa-stack-1x fa-inverse"></i>
        </span>
        <time class="published" datetime="<?php echo get_the_time('c'); ?>"><?php echo get_the_date(); ?></time>
    </div>
    <div class="byline author vcard">
        <span class="fa-stack">
            <i class="fa fa-circle fa-stack-2x"></i>
            <i class="fa fa-user fa-stack-1x fa-inverse"></i>
        </span>
        <?php echo __('By', 'roots'); ?>
        <a href="<?php echo get_author_posts_url($post->post_author); ?>" rel="author" class="fn">
          <?php the_author(); ?>
        </a>
    </div>
</div>
