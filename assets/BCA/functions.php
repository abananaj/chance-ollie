<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: f568d0df88764d1818cb7a6d21c89e2a26f49898 $
 * @link       http://chancetheater.com/
 */

require_once locate_template('../vendor/autoload.php');
require_once locate_template('lib/BCA/ChanceTheater/options.php');
require_once locate_template('lib/BCA/ChanceTheater/utils.php');
require_once locate_template('lib/BCA/ChanceTheater/init.php');
require_once locate_template('lib/BCA/ChanceTheater/wrapper.php');
require_once locate_template('lib/BCA/ChanceTheater/sidebar.php');
require_once locate_template('lib/BCA/ChanceTheater/config.php');
require_once locate_template('lib/BCA/ChanceTheater/activation.php');
require_once locate_template('lib/BCA/ChanceTheater/titles.php');
require_once locate_template('lib/BCA/ChanceTheater/clean-wp.php');
require_once locate_template('lib/BCA/ChanceTheater/nav.php');
require_once locate_template('lib/BCA/ChanceTheater/gallery.php');
require_once locate_template('lib/BCA/ChanceTheater/comments.php');
require_once locate_template('lib/BCA/ChanceTheater/relative-urls.php');
require_once locate_template('lib/BCA/ChanceTheater/widgets.php');
require_once locate_template('lib/BCA/ChanceTheater/scripts.php');
require_once locate_template('lib/BCA/ChanceTheater/custom.php');

// ACF Field Groups (replaces CMB)
if (function_exists('acf_add_local_field_group')) {
    require_once locate_template('lib/BCA/ChanceTheater/acf-fields.php');
}

// Comment out CMB if using ACF instead
// require_once locate_template('../vendor/humanmade/custom-meta-boxes/custom-meta-boxes.php');

if (is_admin()) {
    include_once locate_template('../vendor/iandunn/admin-notice-helper/admin-notice-helper.php');
}
