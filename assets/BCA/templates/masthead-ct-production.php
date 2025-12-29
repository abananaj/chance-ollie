<?php
  use BCA\ChanceTheater\Helpers;
  use BCA\ChanceTheater\Models\Artists as ArtistsModel;
  use BCA\ChanceTheater\Models\Productions as ProductionsModel;

  $artists = ArtistsModel::getProduction(get_the_id());

  $date_opening = new DateTime('@'.$post->__get('date-opening'));
  $date_opening->setTimezone(Helpers::getTimezone());

  $date_closing = new DateTime('@'.$post->__get('date-closing'));
  $date_closing->setTimezone(Helpers::getTimezone());
?>
<header class="masthead-inner">
  <div class="row">
    <div class="col-sm-2 hidden-xs">
      <?php the_post_thumbnail('postcard-sm', array('class'=>'production-postcard img-responsive')); ?>
    </div>
    <div class="col-sm-5">
      <h1>
        <?php echo roots_title(); ?>
      </h1>
      <div class="production-dates strong">
        <time datetime="<?php echo $date_opening->format('Y-m-d') ?>">
          <?php echo $date_opening->format('F j, Y') ?>
        </time>
        <?php if ($date_opening->format('z') !== $date_closing->format('z')) : ?>
        &mdash;
        <time datetime="<?php echo $date_closing->format('Y-m-d') ?>">
          <?php echo $date_closing->format('F j, Y') ?>
        </time>
        <?php endif; ?>
      </div>
      <div class="production-credits">
        <?php foreach (get_post_meta($post->ID, 'byline', false) as $byline) : ?>
        <div class="credit">
          <?php echo $byline['role'] ?>
          <span class="name">
            <span class="names"><?php echo $byline['name'] ?></span>
          </span>
        </div>
      <?php endforeach; ?>
      <?php
        $groups = array(
          'sponsor-comm' => 'Community Partner',
          'sponsor-corp-2' => 'Presented with Support From',
          'producer-exec-2' => 'Executive Producer',
          'producer-assoc-2' => 'Associate Producer',
          'sponsor-comm-2' => 'Community Partner'
        );

        foreach ($groups as $group => $byline) {
          $credits = Helpers::getArtistsOneline($artists, $byline, $group);
          if (!empty($credits)) {
            echo '<div class="credit-'.$group.'">'.$credits.'</div>';
          }
        }
      ?>
      </div>
      <div class="production-buttons">
        <?php $seasons = get_the_terms($post->ID, 'season'); ?>
        <?php if (of_get_option('subscriptions_btn_enabled') && !empty($seasons) && !$post->__get('hide-subscribe')) : ?>
        <a href="<?php echo home_url(get_page_uri(of_get_option('subscriptions_btn_link_id'))) ?>" class="btn btn-sm btn-secondary">
          <?php echo htmlspecialchars(of_get_option('subscriptions_btn_link_text')) ?>
        </a>
        <?php endif; ?>

        <?php if ($post->__get('ticketing-link')) : ?>
        <a href="<?php echo $post->__get('ticketing-link') ?>" class="btn btn-sm btn-secondary">
          Buy Tickets
        </a>
        <?php endif; ?>
      </div>
    </div>
    <div class="col-md-5">
      <?php $awards = get_post_meta(get_the_id(), 'awards'); ?>
      <?php if (count($awards) > 0) : ?>
      <div class="production-awards">
        <h2 class="sr-only">Awards and Recognition</h2>
        <ul>
          <?php foreach ($awards as $award) : ?>
          <li>
            <strong><?php echo $award['award-headline'] ?></strong> <?php echo $award['award-text'] ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
      <?php $quotes = get_post_meta(get_the_id(), 'quotes'); ?>
      <?php if (count($quotes) > 0) : ?>
      <div class="production-quotes">
        <h2 class="sr-only">Reviewer Quotes</h2>
        <?php foreach ($quotes as $quote) : ?>
        <blockquote>
          <p>
            <?php if ($quote['quote-link']) : ?>
            <a href="<?php echo get_permalink($quote['quote-link']) ?>" title="Read More!" data-toggle="tooltip">
            <?php endif; ?>
              &ldquo;&nbsp;<?php echo htmlspecialchars($quote['quote-text']) ?>&nbsp;&rdquo;
            <?php if ($quote['quote-link']) : ?>
            </a>
            <?php endif; ?>
          </p>
          <small>
            <cite>
              <?php echo $quote['quote-cite'] ?>
            </cite>
          </small>
        </blockquote>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</header>
