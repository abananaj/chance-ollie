<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: aacbc67591899894e0d23044149caec5a4636110 $
 * @link       http://chancetheater.com/
 */

/**
 * Enqueue webfonts.
 *
 * @return void
 */
function ct_register_fonts()
{
    wp_register_style('ct_fonts', '//fonts.googleapis.com/css?family=Roboto:300,400,400italic,500|Roboto+Condensed:300', false);
}
add_action('admin_enqueue_scripts', 'ct_register_fonts');
add_action('wp_enqueue_scripts', 'ct_register_fonts');

/**
 * Enqueue scripts.
 *
 * @return void
 */
function ct_scripts()
{
    // Do not add line breaks or else Grunt versioning will break.
    wp_enqueue_style('ct_main', get_template_directory_uri().'/assets/css/main.min.css', false, 'd36ecc3809c8246ac9413d753c5ae5e2');
    wp_enqueue_style('ct_fonts');

    /*
       Load jQuery using the same method from HTML5 Boilerplate:
       Grab Google CDN's latest jQuery with a protocol relative URL; fallback to local if offline
       It's kept in the header instead of footer to avoid conflicts with plugins.
    */

    if (!is_admin() && current_theme_supports('jquery-cdn')) {
        //wp_deregister_script('jquery');
        //wp_register_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js', false, null, false);
        add_filter('script_loader_src', 'ct_jquery_local_fallback', 10, 2);
    }

    if (is_single() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    wp_register_script('ct_scripts', get_template_directory_uri().'/assets/js/scripts.min.js', false, '5b425b9004b65bd421a818b3710500bb', true);
    wp_localize_script(
        'ct_scripts',
        'CtAjax',
        [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ct-ajax')
        ]
    );

    wp_enqueue_script('jquery');
    wp_enqueue_script('ct_scripts');
}
add_action('wp_enqueue_scripts', 'ct_scripts', 100);

/**
 * Enqueue admin-only scripts.
 *
 * @return void
 */
function ct_admin_scripts()
{
    wp_enqueue_style('ct_fonts');
    wp_enqueue_style('ct_admin', get_template_directory_uri().'/assets/css/admin.min.css', false, '83ff1e46c50b3b9858f01852d483d76c');
}
add_action('admin_enqueue_scripts', 'ct_admin_scripts', 100);

/**
 * Enqueue development-only scripts.
 *
 * @return void
 */
function ct_dev_scripts()
{
    wp_register_script('livereload', 'http://'.$_SERVER['SERVER_NAME'].':35729/livereload.js?snipver=1');
    wp_enqueue_script('livereload');
}
if (defined('WP_DEBUG') && WP_DEBUG === true) {
    add_action('admin_enqueue_scripts', 'ct_dev_scripts', 100);
    add_action('wp_enqueue_scripts', 'ct_dev_scripts', 100);
}

/**
 * Fallback to local copy of jQuery when CDN not available.
 *
 * @param string $src    URL to script.
 * @param string $handle Internal identifier of script.
 *
 * @return string         URL to script, never modified.
 */
function ct_jquery_local_fallback(string $src, string $handle)
{
    static $add_jquery_fallback = false;

    if ($add_jquery_fallback) {
        echo '<script>window.jQuery || document.write(\'<script src="'.get_template_directory_uri().'/assets/vendor/jquery/jquery.min.js"><\/script>\')</script>'."\n";
        $add_jquery_fallback = false;
    }

    if ($handle === 'jquery') {
        $add_jquery_fallback = true;
    }

    return $src;
}

/**
 * Add Facebook Connect to app.
 *
 * @return void
 */
function ct_facebook()
{
    ?>
    <div id="fb-root"></div>
    <script>(function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=<?php echo FB_APP_ID; ?>";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
    <?php
}
if (FB_APP_ID) {
    add_action('wp_footer', 'ct_facebook', 20);
}

/**
 * Add Google Analytics to app.
 *
 * @return void
 */
function ct_google_analytics()
{
    ?>
    <script>
        (function (i,s,o,g,r,a,m) {i['GoogleAnalyticsObject']=r;i[r]=i[r]||function () {
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', '<?php echo GOOGLE_ANALYTICS_ID; ?>', 'chancetheater.com');
        ga('send', 'pageview');
    </script>
    <?php
}
if (GOOGLE_ANALYTICS_ID) {
    add_action('wp_footer', 'ct_google_analytics', 20);
}

/**
 * Add Twitter share handler to app.
 *
 * @return void
 */
function ct_twitter()
{
    ?>
    <script>!function (d,s,id) {var js,fjs=d.getElementsByTagName(s)[0];
    if (!d.getElementById(id)) {js=d.createElement(s);js.id=id;
    js.src="//platform.twitter.com/widgets.js";
    fjs.parentNode.insertBefore(js,fjs);}}
    (document,"script","twitter-wjs");</script>
    <?php
}
add_action('wp_footer', 'ct_twitter', 20);

/**
 * Add Google Plus handler to app.
 *
 * @return void
 */
function ct_googleplus()
{
    ?>
    <script type="text/javascript">
        (function () {
            var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
            po.src = 'https://apis.google.com/js/platform.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
        })();
    </script>
    <?php
}
add_action('wp_footer', 'ct_googleplus', 20);
