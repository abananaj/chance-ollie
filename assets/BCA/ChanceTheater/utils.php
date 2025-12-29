<?php
// @codingStandardsIgnoreFile
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: a4621ee514412b3158e7ea54984ff9070f917aca $
 * @link       http://chancetheater.com/
 */

function add_filters($tags, $function)
{
    foreach ($tags as $tag) {
        add_filter($tag, $function);
    }
}

function is_element_empty($element)
{
    $element = trim($element);

    return empty($element) ? false : true;
}
