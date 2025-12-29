<?php
// @codingStandardsIgnoreFile
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: b2348f91882980f92a16fa2011d6c9ec0b54fb92 $
 * @link       http://chancetheater.com/
 */

/**
 * Define widget locations.
 * @return void
 */
function roots_widgets_init()
{
    register_sidebar([
        'name'          => __('Front Page - Left', 'roots'),
        'id'            => 'sidebar-front-left',
        'before_widget' => '<section class="widget %1$s %2$s"><div class="widget-inner">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ]);

    register_sidebar([
        'name'          => __('Front Page - Center', 'roots'),
        'id'            => 'sidebar-front-center',
        'before_widget' => '<section class="widget %1$s %2$s"><div class="widget-inner">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ]);

    register_sidebar([
        'name'          => __('Front Page - Right', 'roots'),
        'id'            => 'sidebar-front-right',
        'before_widget' => '<section class="widget %1$s %2$s"><div class="widget-inner">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ]);

    register_sidebar([
        'name'          => __('Primary', 'roots'),
        'id'            => 'sidebar-primary',
        'before_widget' => '<section class="widget %1$s %2$s"><div class="widget-inner panel panel-default">',
        'after_widget'  => '</div></div></section>',
        'before_title'  => '<div class="panel-heading"><h2 class="widget-title panel-title">',
        'after_title'   => '</h2></div><div class="panel-body">',
    ]);

    register_sidebar([
        'name'          => __('Blog', 'roots'),
        'id'            => 'sidebar-post',
        'before_widget' => '<section class="widget %1$s %2$s"><div class="widget-inner">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ]);

    register_sidebar([
        'name'          => __('Footer', 'roots'),
        'id'            => 'sidebar-footer',
        'before_widget' => '<section class="widget %1$s %2$s"><div class="widget-inner">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ]);

    register_widget('Roots_Vcard_Widget');
}
add_action('widgets_init', 'roots_widgets_init');

/**
 * Define Vcard widget.
 */
class Roots_Vcard_Widget extends WP_Widget
{

    private $fields = [
    'title'          => 'Title (optional)',
    'street_address' => 'Street Address',
    'locality'       => 'City',
    'region'         => 'State',
    'postal_code'    => 'Zip Code',
    'tel'            => 'Telephone',
    'tax_id'          => 'Tax ID'
    ];

    public function __construct()
    {
        $widget_ops = ['classname' => 'widget_roots_vcard', 'description' => __('Use this widget to add a vCard', 'roots')];

        parent::__construct('widget_roots_vcard', __('Roots: vCard', 'roots'), $widget_ops);
        $this->alt_option_name = 'widget_roots_vcard';

        add_action('save_post', [ & $this, 'flush_widget_cache']);
        add_action('deleted_post', [ & $this, 'flush_widget_cache']);
        add_action('switch_theme', [ & $this, 'flush_widget_cache']);
    }

    public function widget($args, $instance)
    {
        $cache = wp_cache_get('widget_roots_vcard', 'widget');

        if (!is_array($cache)) {
            $cache = [];
        }

        if (!isset($args['widget_id'])) {
            $args['widget_id'] = null;
        }

        if (isset($cache[$args['widget_id']])) {
            echo $cache[$args['widget_id']];

            return;
        }

        ob_start();
        extract($args, EXTR_SKIP);

        $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

        foreach ($this->fields as $name => $label) {
            if (!isset($instance[$name])) {
                $instance[$name] = '';
            }
        }

        echo $before_widget;

    ?>
    <span class="vcard">
      <p class="fn org url" href="<?php echo home_url('/'); ?>"><strong><?php echo ($title) ? nl2br($title) : nl2br(bloginfo('name')); ?></strong></p>
      <address class="adr">
        <span class="street-address"><?php echo $instance['street_address']; ?></span><br>
        <span class="locality"><?php echo $instance['locality']; ?></span>,
        <span class="region"><?php echo $instance['region']; ?></span>
        <span class="postal-code"><?php echo $instance['postal_code']; ?></span>
      </address>
      <p class="tel"><abbr title="Phone">P:</abbr> <span class="value"><?php echo $instance['tel']; ?></span></p>
    </span>
    <p>
      <abbr title="Tax ID">EIN:</abbr>
      <a href="http://www.guidestar.org/PartnerReport.aspx?Partner=foundationsource&amp;ein=<?php echo $instance['tax_id']; ?>">
        <?php echo $instance['tax_id']; ?>
      </a>
    </p>
    <?php
    echo $after_widget;

    $cache[$args['widget_id']] = ob_get_flush();
    wp_cache_set('widget_roots_vcard', $cache, 'widget');
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array_map('strip_tags', $new_instance);

        $this->flush_widget_cache();

        $alloptions = wp_cache_get('alloptions', 'options');

        if (isset($alloptions['widget_roots_vcard'])) {
            delete_option('widget_roots_vcard');
        }

        return $instance;
    }

    public function flush_widget_cache()
    {
        wp_cache_delete('widget_roots_vcard', 'widget');
    }

    public function form($instance)
    {
        $name = 'title';
        $label = $this->fields[$name];
        ?>
      <p>
        <label for="<?php echo esc_attr($this->get_field_id($name)); ?>"><?php _e("{$label}:", 'roots'); ?></label>
      <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id($name)); ?>" name="<?php echo esc_attr($this->get_field_name($name)); ?>" rows="2"><?php echo $instance[$name]; ?></textarea>
    </p>
    <?php
    foreach ($this->fields as $name => $label) {
        if ($name !== 'title') {
            ${$name} = isset($instance[$name]) ? esc_attr($instance[$name]) : '';
            ?>
            <p>
              <label for="<?php echo esc_attr($this->get_field_id($name)); ?>"><?php _e("{$label}:", 'roots'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id($name)); ?>" name="<?php echo esc_attr($this->get_field_name($name)); ?>" type="text" value="<?php echo ${$name}; ?>">
    </p>
    <?php
        }
    }
    }
}
