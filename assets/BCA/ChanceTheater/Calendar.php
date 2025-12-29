<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: 07e24a23d94fceb939c5fc3a88963d123afbb865 $
 * @link       http://chancetheater.com/
 */

namespace BCA\ChanceTheater;

use \DateTime;
use \WP_Post;

/**
 * Render a calendar of events.
 */
class Calendar
{
    const ABBREVIATE_DAYS = 'abbrev_days';
    const FORMAT_AGENDA = 'agenda';
    const FORMAT_CALENDAR = 'calendar';
    const FORMAT_PHOTOGRID = 'photogrid';

    /**
     * Array of Event Objects
     *
     * @var array
     */
    private $events = [];

    /**
     * Array of Event Types Shown Presumably Shown in Key Elsewhere
     *
     * @var array
     */
    private $event_types = [];

    /**
     * Add Event to Calendar
     *
     * @param WP_Post $event Event Post Object.
     *
     * @return void
     */
    public function addEvent(WP_Post $event)
    {
        $time = $event->__get('date-start');

        if (!$time) {
            return;
        }

        $date = new DateTime('@'.$time);
        $date->setTimezone(Helpers::getTimezone());

        $year = intval($date->format('Y'));
        $month = intval($date->format('n'));
        $day = intval($date->format('j'));

        // Add ID to timestamp to maintain order and allow events to be at same time.
        $time_adjusted = ($time + $event->ID);

        $this->events[$year][$month][$day][$time_adjusted] = $event;
    }

    /**
     * Get Events for Specified Date
     *
     * @param string|integer $year  Year.
     * @param string|integer $month Month.
     * @param string|integer $day   Day.
     *
     * @return array
     */
    private function getEvents($year, $month, $day)
    {
        $year = intval($year);
        $month = intval($month);
        $day = intval($day);

        if (!$this->hasEvents($year, $month, $day)) {
            return [];
        }

        $data = $this->events[$year][$month][$day];
        ksort($data);

        return $data;
    }

    /**
     * Get HTML to render an event thumbnail.
     *
     * @param WP_Post $event Post object for which thumb should be rendered.
     *
     * @return string         HTML to render thumbnail.
     */
    private function getEventThumbnail(WP_Post $event)
    {
        if (has_post_thumbnail($event->ID)) {
            return get_the_post_thumbnail(
                $event->ID,
                'square-100',
                ['class'=> 'img-responsive']
            );
        } elseif (isset($event->production)
            && has_post_thumbnail($event->production)
        ) {
            return get_the_post_thumbnail(
                $event->production,
                'square-100',
                ['class'=> 'img-responsive']
            );
        }

        return '<img src="/app/assets/img/event-default.png" class="img-responsive">';
    }

    /**
     * Get HTML to render a list of an event's types.
     *
     * @param WP_Post $event Post object for which list should be generated.
     *
     * @return string         HTML to render list.
     */
    private function getEventTypes(WP_Post $event)
    {
        $output = null;
        $types = wp_get_post_terms($event->ID, 'event-type', [
            'fields' => 'ids'
        ]);
        foreach ($this->event_types as $index => $type) {
            if (in_array($type->term_id, $types)) {
                $output.= '<li class="calendar-event-key-item" title="'.$type->name.'">';
                $output.= '<i class="fa fa-square key-'.($index + 1).'"></i>';
                $output.= '</li>';
            }
        }

        return $output;
    }

    /**
     * Check if Specified Date Contains Any Events
     *
     * @param string|integer $year  Year.
     * @param string|integer $month Month.
     * @param string|integer $day   Day.
     *
     * @return boolean
     */
    private function hasEvents($year, $month = null, $day = null)
    {
        $year = intval($year);
        $month = intval($month);
        $day = intval($day);

        if (!$month && !$day) {
            return isset($this->events[$year]);
        } elseif (!$day) {
            return isset($this->events[$year][$month]);
        }

        return isset($this->events[$year][$month][$day]);
    }

    /**
     * Output a HTML5 Calendar Table
     *
     * @param string|integer $calMonth Month of Calendar.
     * @param string|integer $calYear  Year of Calendar.
     * @param array          $options  Array of option constants.
     *
     * @return null
     */
    public function render($calMonth, $calYear, array $options = [self::FORMAT_CALENDAR])
    {
        // Date: Calendar.
        $date = new DateTime('1-'.$calMonth.'-'.$calYear, Helpers::getTimezone());
        $daysInMonth = $date->format('t');
        $dayOffset = ($date->format('w') + 1);
        $calendarWeeks = (ceil(($daysInMonth + $dayOffset) / 7) - 1);

        // Date: Today.
        $today = new DateTime();
        $today->setTimezone(Helpers::getTimezone());

        $calClass = 'calendar';

        if (in_array(self::FORMAT_PHOTOGRID, $options)) {
            $calClass .= ' calendar-format-photogrid';
        } elseif (in_array(self::FORMAT_AGENDA, $options)) {
            $calClass .= ' calendar-format-agenda';
        } elseif (in_array(self::FORMAT_CALENDAR, $options)) {
            $calClass .= ' calendar-format-calendar';
        }

        $output = '<div class="'.$calClass.'">';

        if (in_array(self::FORMAT_CALENDAR, $options)) {
            $calUrl = get_post_type_archive_link('ct-event');
            $calUrl = add_query_arg(
                [
                    'cal_month' => $date->format('m'),
                    'cal_year' => $date->format('Y')
                ],
                $calUrl
            );
            $output.= '<div class="calendar-title">';
            $output.= '<a href="'.$calUrl.'">';
            $output.= $date->format('F Y');
            $output.= '</a>';
            $output.= '</div>';
        }

        if (in_array(self::FORMAT_CALENDAR, $options)) {
            $output.= '<header class="calendar-header">';
            $output.= '<div class="calendar-headings">';
            if (in_array(self::ABBREVIATE_DAYS, $options)) {
                $output.= '<div class="calendar-heading"><abbr title="Sunday">S</abbr></div>';
                $output.= '<div class="calendar-heading"><abbr title="Monday">M</abbr></div>';
                $output.= '<div class="calendar-heading"><abbr title="Tuesday">T</abbr></div>';
                $output.= '<div class="calendar-heading"><abbr title="Wednesday">W</abbr></div>';
                $output.= '<div class="calendar-heading"><abbr title="Thursday">T</abbr></div>';
                $output.= '<div class="calendar-heading"><abbr title="Friday">F</abbr></div>';
                $output.= '<div class="calendar-heading"><abbr title="Saturday">S</abbr></div>';
            } else {
                $output.= '<div class="calendar-heading">Sunday</div>';
                $output.= '<div class="calendar-heading">Monday</div>';
                $output.= '<div class="calendar-heading">Tuesday</div>';
                $output.= '<div class="calendar-heading">Wednesday</div>';
                $output.= '<div class="calendar-heading">Thursday</div>';
                $output.= '<div class="calendar-heading">Friday</div>';
                $output.= '<div class="calendar-heading">Saturday</div>';
            }

            $output.= '</div>';
            $output.= '</header>';
        }

        // Infill Previous Month.
        $date->modify('- '.$dayOffset.' days');

        $output .= '<div class="calendar-days">';
        for ($row_index = 0; $row_index <= $calendarWeeks; $row_index++) {
            $output .= '<div class="calendar-week">';
            for ($colIndex = 1; $colIndex <= 7; $colIndex++) {
                $date->modify('+ 1 day');

                $year = $date->format('Y');
                $month = $date->format('m');
                $day = $date->format('d');

                // DAY CLASSES.
                $day_class = 'calendar-day';

                // Today.
                if (intval($day) === intval($today->format('d'))
                    && intval($month) === intval($today->format('m'))
                    && intval($year) === intval($today->format('Y'))
                ) {
                    $day_class .= ' calendar-day-today';
                }

                // Before/after month start.
                if (intval($date->format('n')) !== intval($calMonth)) {
                    $day_class .= ' calendar-day-inactive';
                }

                // Day contains events?
                if (!$this->hasEvents($year, $month, $day)) {
                    $day_class .= ' calendar-day-noevents';
                    if (!in_array(self::FORMAT_CALENDAR, $options)) {
                        continue;
                    }
                } else {
                    $day_class .= ' calendar-day-hasevents';
                }

                $output.= '<div class="'.$day_class.'"">';
                $output.= '<time datetime="'.$date->format(DateTime::RFC3339).'" class="calendar-day-date">';
                $output.= '<span class="calendar-day-date-dow">'.$date->format('l').'</span>';
                $output.= '<span class="calendar-day-date-separator">, </span>';
                $output.= '<span class="calendar-day-date-month">'.$date->format('F').' </span>';
                $output.= '<span class="calendar-day-date-day">'.$date->format('j').'</span>';
                $output.= '<span class="calendar-day-date-separator">, </span>';
                $output.= '<span class="calendar-day-date-year">'.$date->format('Y').'</span>';
                $output.= '</time>';

                // Events Ordered List.
                if ($this->hasEvents($year, $month, $day)) {
                    $output .= '<ol class="calendar-events">';
                    foreach ($this->getEvents($year, $month, $day) as $event) {
                        $date_event = new DateTime('@'.$event->__get('date-start'));
                        $date_event->setTimezone(Helpers::getTimezone());
                        $output .= '<li class="calendar-event" data-id="'.$event->ID.'">';
                        if (in_array(self::FORMAT_PHOTOGRID, $options)) {
                            $output.= '<div class="calendar-event-thumbnail">';
                            $output.= $this->getEventThumbnail($event);
                            $output.= '</div>';
                        }

                        $output.= '<div class="calendar-event-info">';
                        $event_date_class = 'calendar-event-date';
                        if (intval($date_event->format('i')) !== 0) {
                            $event_date_class .= ' calendar-event-date-minute00';
                        }

                        $output.= '<time datetime="'.$date_event->format(DateTime::RFC3339)
                               .'" class="'.$event_date_class.'">';
                        $output.= '<span class="calendar-event-date-hour">'.$date_event->format('g').'</span>';
                        $output.= '<span class="calendar-event-date-separator">:</span>';
                        $output.= '<span class="calendar-event-date-minute">'.$date_event->format('i').'</span>';
                        $output.= '<span class="calendar-event-date-ampm">'.$date_event->format('a').'</span>';
                        $output.= ' </time>';
                        $output.= '<a href="'.get_permalink($event->ID).'" title="'.$event->post_title
                               .'" class="calendar-event-title">';
                        $output.= $event->post_title;
                        $output.= '</a>';
                        if (!empty($this->event_types)) {
                            $output.= '<ul class="calendar-event-key">';
                            $output.= $this->getEventTypes($event);
                            $output.= '</ul>';
                        }

                        $output .= '</div>';

                        // Class calendar-event-info end.
                        $output .= '</li>';
                    }

                    $output .= '</ol>';
                }

                // Table Cell Close.
                $output .= '</div>';
            }

            $output .= '</div>';
        }

        $output.= '</div>';
        $output.= '</div>';

        return $output;
    }

    /**
     * Set event types to be displayed.
     *
     * @param array $event_types Array of event types.
     *
     * @return void
     */
    public function setEventTypes(array $event_types)
    {
        $this->event_types = $event_types;
    }
}
