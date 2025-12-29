<?php
// @codingStandardsIgnoreFile

/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: 7f1404aa0da4d9c791837733c5c55f92fbc79714 $
 * @link       http://chancetheater.com/
 */
class Roots_Walker_Comment extends Walker_Comment
{
    public function start_lvl(&$output, $depth = 0, $args = [])
    {
        $GLOBALS['comment_depth'] = ($depth + 1); ?>
      <ul <?php comment_class('media unstyled comment-'.get_comment_ID()); ?>>
        <?php
    }

    public function end_lvl(&$output, $depth = 0, $args = [])
    {
        $GLOBALS['comment_depth'] = ($depth + 1);
        echo '</ul>';
    }

    public function start_el(&$output, $comment, $depth = 0, $args = [], $id = 0)
    {
        $depth++;
        $GLOBALS['comment_depth'] = $depth;
        $GLOBALS['comment'] = $comment;

        if (!empty($args['callback'])) {
            call_user_func($args['callback'], $comment, $args, $depth);

            return;
        }

        extract($args, EXTR_SKIP);
    ?>

    <li id="comment-<?php comment_ID(); ?>" <?php comment_class('media comment-'.get_comment_ID()); ?>>
        <?php
        $template = 'templates/comment.php';
        $template_posttype = 'templates/comment-'.get_post_type().'.php';
        if (locate_template($template_posttype)) {
            $template = $template_posttype;
        }

        include locate_template($template);
        ?>
    <?php
    }

    public function end_el(&$output, $comment, $depth = 0, $args = [])
    {
        if (!empty($args['end-callback'])) {
            call_user_func($args['end-callback'], $comment, $args, $depth);

            return;
        }

        echo "</div></li>\n";
    }
}

function roots_get_avatar($avatar)
{
    $avatar = str_replace("class='avatar", "class='avatar pull-left media-object", $avatar);

    return $avatar;
}
add_filter('get_avatar', 'roots_get_avatar');
