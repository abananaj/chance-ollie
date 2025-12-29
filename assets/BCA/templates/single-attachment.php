<?php use BCA\ChanceTheater\Helpers; ?>
<article>
<div class="page-header">
  <h1>
    <?php echo roots_title(); ?>
  </h1>
</div>
<?php while (have_posts()) : the_post(); ?>
  <?php $metadata = wp_get_attachment_metadata($post->ID); ?>
  <div class="row">
      <div class="col-sm-8">
        <figure>
          <?php echo wp_get_attachment_image($post->ID, 'attachment'); ?>
          <figcaption><?php the_excerpt(); ?></figcaption>

        </figure>
        <?php the_content(); ?>
      </div>
      <div class="col-sm-4">
          <dl>
              <dt>Filename</dt>
              <dd><?php echo $metadata['file'] ?></dd>
              <?php foreach ($metadata['image_meta'] as $key => $value) : ?>
              <?php if (!empty($value)) : ?>
              <dt><?php echo ucwords(str_replace('_', ' ', $key)) ?></dt>
              <?php
                if ($key === 'created_timestamp') {
                  $date = new DateTime();
                  $date->setTimezone(Helpers::getTimezone());
                  $date->setTimestamp($value);
                  $value = $date->format('F n, Y g:ia');
                }
              ?>
              <dd><?php echo $value ?></dd>
              <?php endif; ?>
              <?php endforeach; ?>
              <dt>Available Sizes</dt>
              <?php
                $sizes = array();
                $sizes['mini'] = @$metadata['sizes']['mini'];
                $sizes['small'] = @$metadata['sizes']['small'];
                $sizes['medium'] = @$metadata['sizes']['medium'];
                $sizes['large'] = @$metadata['sizes']['large'];
                $sizes['full'] = $metadata;
              ?>
              <?php foreach ($sizes as $format => $size) : ?>
              <?php if (is_array($size)) : ?>
              <dd>
                <a href="<?php echo wp_get_attachment_image_src($post->ID, $format)[0] ?>">
                  <?php echo ucfirst($format) ?>
                </a>
                <small class="text-muted">
                  <?php echo $size['width'] ?>x<?php echo $size['height'] ?>
                </small>
              </dd>
              <?php endif; ?>
              <?php endforeach; ?>

          </dl>
      </div>
  </div>
  <?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
<?php endwhile; ?>
</article>
