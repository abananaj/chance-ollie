<?php

use BCA\ChanceTheater\Calendar;
use BCA\ChanceTheater\Helpers;

/* Prepare Event Types */
$event_types_enabled = array_keys(array_filter((array) of_get_option('event_types_enabled')));
$event_types = get_terms('event-type', array(
    'hierarchical'  => false,
    'orderby'       => 'name',
    'order'         => 'ASC',
    'number'        => 8,
    'include'       => $event_types_enabled
));

/* Generate Calendar */
$month = (get_query_var('cal_month')) ? get_query_var('cal_month') : date('n');
$year = (get_query_var('cal_year')) ? get_query_var('cal_year') : date('Y');

$date_start = new DateTime();
$date_start->setTimezone(Helpers::getTimezone());
$date_start->setDate(
    $year,
    $month,
    1
);
$date_start->setTime(0, 0, 0);

$date_end = clone $date_start;
$date_end->modify('+ 1 month');

$query = array(
    'post_type' => 'ct-event',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key'=>'date-start',
            'value'=>$date_start->format('U'),
            'compare'=>'>='
        ),
        array(
            'key'=>'date-end',
            'value'=>$date_end->format('U'),
            'compare'=>'<='
        )
    )
);

$query = new WP_Query($query);
$calendar = new Calendar();
$calendar->setEventTypes($event_types);

while ($query->have_posts()) {
      $query->the_post();
      $calendar->addEvent(get_post());
}

$date_month_previous = clone $date_start;
$date_month_previous->modify('- 1 day');

$date_month_next = clone $date_end;
$date_month_next->modify('+ 1 day');

$calendar_url = get_post_type_archive_link('ct-event');
$month_prev_url = add_query_arg(
    array(
        'cal_month' => $date_month_previous->format('m'),
        'cal_year' => $date_month_previous->format('Y')
    ),
    $calendar_url
);
$month_next_url = add_query_arg(
    array(
        'cal_month' => $date_month_next->format('m'),
        'cal_year' => $date_month_next->format('Y')
    ),
    $calendar_url
);

?>

<nav class="calendar-nav">
    <a href="<?php echo $month_prev_url ?>">
        <i class="fa fa-arrow-circle-left"></i>
        <?php echo $date_month_previous->format('F') ?>
    </a>
    <a href="<?php echo $month_next_url ?>">
        <?php echo $date_month_next->format('F') ?>
        <i class="fa fa-arrow-circle-right"></i>
    </a>
</nav>

<?php echo $calendar->render($month, $year); ?>
<br>
<div class="text-muted text-center h4">
    <i class="fa fa-info-circle"></i>
    Helpful Tip: Hover over any event for more information.
</div>
<div class="row calendar-info">
    <div class="col-md-1 icon">
        <i class="fa fa-ticket"></i>
    </div>
    <div class="col-md-5 box-office">
        <h2 class="h3">Box Office</h2>
        <div class="row">
            <div class="col-md-5">
                <h3 class="h4">Ticket Sales</h3>
                <dl class="dl-horizontal">
                    <dt>Phone</dt>
                    <dd>888.455.4212</dd>
                    <dt>Group Sales</dt>
                    <dd>714.900.3284</dd>
                    <dt>Fax</dt>
                    <dd>714.459.7873</dd>
                </dl>
                <a href="<?php echo home_url() ?>" class="btn btn-xs btn-primary">
                    Buy Tickets Online
                </a>
            </div>
            <div class="col-md-7">
                <h3 class="h4">Telephone Hours of Operation</h3>
                <p><strong>Monday-Friday</strong> 11:00am-3:00pm</p>
                <p>
                    If you receive our voicemail during operating hours, please leave
                    a message and we will return your call as soon as possible.
                </p>
            </div>
        </div>
    </div>
        <div class="col-md-1 icon">
        <i class="fa fa-calendar"></i>
    </div>
    <div class="col-md-4 calendar-key">
        <h2 class="h3">Calendar Key</h2>
        <h3 class="h4">Event Categories</h3>
        <ul class="row">
        <?php foreach ($event_types as $event_index => $event_type) : ?>
            <li class="col-xs-6">
                <i class="fa fa-square key-<?php echo $event_index + 1 ?>"></i>
                <?php echo $event_type->name ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>
