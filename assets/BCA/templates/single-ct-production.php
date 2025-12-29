<?php
  use BCA\ChanceTheater\Helpers;
  use BCA\ChanceTheater\Inflect;
  use BCA\ChanceTheater\Models\Artists as ArtistsModel;
  use BCA\ChanceTheater\Models\Events as EventsModel;
  use BCA\ChanceTheater\Models\Productions as ProductionsModel;

  $credits = ArtistsModel::getProduction(get_the_id());

  while (have_posts()) : the_post();
?>
  <div class="row">
    <div class="col-sm-7">
      <ul class="nav nav-tabs nav-justified">

        <li class="active"><a href="#production_sys_summary" data-toggle="tab"><?php echo of_get_option('production_tab_synopsis', 'Synopsis') ?></a></li>




        <?php if ($post->__get('video-trailer-url')) : ?>
        <li><a href="#production_sys_video" data-toggle="tab"><?php echo of_get_option('production_tab_video', 'Video') ?></a></li>
        <?php endif; ?>

        <?php $prodtabs = (array) get_post_meta($post->ID, 'tabs') ?>
        <?php foreach($prodtabs as $id => &$tab) : ?>
        <?php if (empty($tab['slug'])) $tab['slug'] = uniqid(); ?>
        <li>
          <a href="#production_<?php echo $tab['slug'] ?>" data-toggle="tab">
            <?php echo $tab['title'] ?>
          </a>
        </li>
        <?php endforeach; ?>

        <li>
          <a href="#production_sys_reviews" data-toggle="tab"><?php echo of_get_option('production_tab_buzz', 'Buzz') ?></a>
        </li>

      </ul>
      <div class="tab-content">

        <div class="tab-pane fade in active" id="production_sys_summary">
          <?php the_content(); ?>
        </div>

        <?php if ($post->__get('video-trailer-url')) : ?>
        <div class="tab-pane fade" id="production_sys_video">
          <?php
            echo apply_filters(
              'embed_oembed_html',
              wp_oembed_get($post->__get('video-trailer-url')),
              array()
            )
          ?>
        </div>
        <?php endif; ?>

        <?php foreach($prodtabs as $id => &$tab) : ?>
        <div class="tab-pane fade" id="production_<?php echo $tab['slug'] ?>">
          <?php echo do_shortcode($tab['content']) ?>
        </div>
        <?php endforeach; ?>

        <div class="tab-pane fade" id="production_sys_reviews">
          <h2 class="h4">Articles &amp; Reviews</h2>
          <?php $related = ProductionsModel::getRelatedPosts($post->ID, 20) ?>
          <?php if ($related->have_posts()) : ?>
          <?php while ($related->have_posts()) : $related->the_post(); ?>
            <?php get_template_part('templates/headline-media', get_post_format()); ?>
          <?php endwhile; ?>
          <?php wp_reset_query(); ?>
          <a class="btn btn-primary btn-sm" href="<?php echo get_permalink(get_page_by_path('blog')); ?>?production=<?php echo $post->ID ?>">
            Read More on the Blog
          </a>
          <?php else: ?>
          <p>Reviews coming soon!</p>
          <?php endif; ?>

          <?php if (get_comments_number() > 0 || comments_open()) : ?>
          <h2 class="h4">Audience Buzz</h2>
          <?php if (get_comments_number() > 0) : ?>
          <ol class="media-list">
            <?php comments_template('/templates/review-comments.php') ?>
          </ol>
          <?php elseif (comments_open()) : ?>
          <p>Have something to say? Share your comments below!</p>
          <?php endif; endif; ?>
        </div>

      </div>
      <div class="production-venue">
        <h2>
          Venue
          <small><?php echo get_the_title($post->venue) ?></small>
        </h2>
        <?php
          $venue_image_size = 'banner-wide';
          $venue_src = wp_get_attachment_image_src(
            get_post_thumbnail_id($post->venue),
            $venue_image_size
          )[0];
          if (!has_post_thumbnail($post->venue)) {
            $venue_src = Helpers::getVenueMapSrc(
              $post->venue,
              $venue_image_size
            );
          }
        ?>
        <img src="<?php echo $venue_src ?>" class="img-responsive img-rounded">
        <div class="row">
          <div class="col-xs-6">
            <?php echo Helpers::getVenueHcard($post->venue) ?>
            <a href="<?php echo get_permalink($post->venue) ?>" class="btn btn-primary btn-xs">
              More Information
            </a>
            <a href="<?php echo get_permalink($post->venue) ?>" class="btn btn-sm">
              Get Directions
            </a>
          </div>
          <div class="col-xs-6 venue-amenities">
            <?php $amenities = Helpers::getVenueAmenitiesList($post->venue) ?>
            <?php if ($amenities) : ?>
            <strong>Venue Amenities</strong>
            <?php echo $amenities ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="production-review-form">
        <?php get_template_part('templates/comment-form', get_post_type()); ?>
      </div>
    </div>
    <div class="col-sm-5">
      <div class="production-overview">
        <h2 class="sr-only">At a Glance</h2>
        <dl>
          <?php $performances = EventsModel::getProductionFuture($post->ID, 3) ?>
          <?php if ($performances->have_posts()) : ?>
          <dt>Upcoming Performances</dt>
          <?php while ($performances->have_posts()) : ?>
          <?php
            $performance = $performances->next_post();
            $performance_start = new DateTime();
            $performance_start->setTimezone(Helpers::getTimezone());
            $performance_start->setTimestamp($performance->__get('date-start'));
          ?>
          <dd class="production-events">
            <a href="<?php echo get_permalink($performance->ID) ?>">
              <time datetime="<?php echo $performance_start->format(DateTime::RFC3339) ?>">
              <?php echo $performance_start->format('l, F j') ?>
              <small class="text-muted"><?php echo $performance_start->format('g:i a') ?></small>
              </time>
            </a>
          </dd>
          <?php endwhile; ?>
          <?php endif; ?>

          <?php $promos = EventsModel::getProductionPromos($post->ID, 3) ?>
          <?php if ($promos->have_posts()) : ?>
          <dt>Special Events</dt>
          <?php while ($promos->have_posts()) : ?>
          <?php
            $performance = $promos->next_post();
            $performance_start = new DateTime();
            $performance_start->setTimezone(Helpers::getTimezone());
            $performance_start->setTimestamp($performance->__get('date-start'));
          ?>
          <dd class="production-events production-promos event-wrapper">
            <a href="<?php echo get_permalink($performance->ID) ?>">
              <div class="production-title event-title"><?php echo $performance->post_title ?></div>
              <time datetime="<?php echo $performance_start->format(DateTime::RFC3339) ?>">
                <small class="text-muted">
                  <span class="event-date"><?php echo $performance_start->format('l, F j') ?></span>
                  <span class="event-time"><?php echo $performance_start->format('g:i a') ?></span>
                </small>
              </time>
              <p class="production-excerpt description"><?php echo $performance->post_excerpt ?></p>
            </a>
          </dd>
          <?php endwhile; ?>
          <?php endif; ?>

          <?php if ($post->runtime) : ?>
          <dt>Approximate Running Time:</dt>
          <?php $runtime = Helpers::getTimespan(0, $post->runtime*60); ?>
          <dd>
            <?php echo $runtime ?>
            <?php $number_words = array('no', '', 'two', 'three') ?>
            <small>
              with <?php echo $number_words[$post->intermissions] ?>
              <?php echo Inflect::pluralizeIf($post->intermissions, 'intermission') ?>
            </small>
          </dd>
          <?php endif; ?>

          <?php if ($post->rating) : ?>
          <dt class="sr-only">Content Rating:</dt>
          <dd class="production-rating">
            <i class="rating-<?php echo $post->rating ?>"></i>
            <?php if ($post->rating === 'e'): ?>
            <strong>Good for Everybody</strong>
            <small>
              This show's content is family-friendly and suitable for all ages.
            </small>
            <?php elseif ($post->rating === 'e10'): ?>
            <strong>Good for Everybody 10+</strong>
            <small>
              Content may not be suitable for children under the age of 10.
            </small>
            <?php elseif ($post->rating === 't'): ?>
            <strong>Teens and Adults Only</strong>
            <small>
              Content may not be suitable for young children.
            </small>
            <?php elseif ($post->rating === 'm'): ?>
            <strong>Mature Audiences Only</strong>
            <small>
              Show contains subject matter suitable for adults only.
            </small>
            <?php endif; ?>
            <div class="clearfix"></div>
          </dd>
          <?php endif; ?>

          <?php $notes = get_post_meta($post->ID, 'notes', false) ?>
          <?php if ($notes) : ?>
          <dt class="sr-only">Special Notes:</dt>
          <dd class="production-notes">
            <ul class="fa-ul">
              <?php foreach ($notes as $note) : ?>
              <li>
                <i class="fa-li fa <?php echo $note['icon'] ?>"></i>
                <?php if (!empty($note['link'])) : ?>
                <a href="<?php echo get_permalink($note['link']) ?>">
                <?php endif; ?>
                <?php echo $note['desc'] ?>
                <?php if (isset($note['link'])) : ?></a><?php endif; ?>
              </li>
              <?php endforeach; ?>
            </ul>
          </dd>
          <?php endif; ?>
        </dl>
      </div>
      <?php if ($credits->have_posts()) : ?>
      <div class="production-artists">
        <h2>Featured Artists</h2>

        <?php
          $cast = Helpers::getArtistThumbs($credits, 'square-100', 'actor');

          $production_team = '';
          $roles = array(
            'composer',
            'writer-book',
            'writer-lyrics',
            'writer',
            'director',
            'choreographer',
            'director-music',
            'designer',
            'director-asst',
            'choreographer-asst',
            'designer-asst',
            'other'
          );
          foreach ($roles as $role) {
            $production_team.= Helpers::getArtistThumbs($credits, 'square-100', $role);
          }

          $partners = '';
          $roles = array(
            'sponsor-corp',
            'producer',
            'producer-exec',
            'producer-assoc',
            'sponsor-comm',
            'sponsor-corp-2',
            'producer-exec-2',
            'producer-assoc-2',
            'sponsor-comm-2'
          );
          foreach ($roles as $role) {
            $partners.= Helpers::getArtistThumbs($credits, 'square-100', $role);
          }
        ?>

        <?php if (!empty($cast)) : ?>
        <h3>Cast Members</h3>
        <?php echo $cast ?>
        <?php endif; ?>

        <?php if (!empty($production_team)) : ?>
        <h3>Production Team</h3>
        <?php echo $production_team ?>
        <?php endif; ?>

        <?php if (!empty($partners)) : ?>
        <h3>Partners</h3>
        <?php echo $partners ?>
        <?php endif; ?>

        <?php
          $has_resident_artist = false;
          while ($credits->have_posts()) {
            $credit = $credits->next_post();
            $resident = get_post_meta((int) $credit->artist, 'resident', true);
            if ($resident) {
              $has_resident_artist = true;
            }
          }
        ?>
        <?php if ($has_resident_artist) : ?>
        <small class="production-resident-artist text-primary">
          <i class="fa fa-asterisk"></i>
          Chance Theater Resident Artist
        </small>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
<?php endwhile;
