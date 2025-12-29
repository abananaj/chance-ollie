<?php
// @codingStandardsIgnoreFile
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: 0bf887cb20689a49fdcd65deee0de03c97d8cd5a $
 * @link       http://chancetheater.com/
 */

function roots_template_path()
{
    return Roots_Wrapping::$main_template;
}

function roots_sidebar_path()
{
    return new Roots_Wrapping('templates/sidebar.php');
}

class Roots_Wrapping
{

    // Stores the full path to the main template file
    public static $main_template;

    // Stores the base name of the template file; e.g. 'page' for 'page.php' etc.
    public static $base;

    public function __construct($template = 'base.php')
    {
        $this->slug = basename($template, '.php');
        $this->templates = [$template];

        if (self::$base) {
            $str = substr($template, 0, -4);
            array_unshift($this->templates, sprintf($str.'-%s.php', self::$base));
        }
    }

    public function __toString()
    {
        $this->templates = apply_filters('roots_wrap_'.$this->slug, $this->templates);

        return locate_template($this->templates);
    }

    public static function wrap($main)
    {
        self::$main_template = $main;
        self::$base = basename(self::$main_template, '.php');

        if (self::$base === 'index') {
            self::$base = false;
        }

        return new Roots_Wrapping();
    }
}

add_filter('template_include', ['Roots_Wrapping', 'wrap'], 99);
