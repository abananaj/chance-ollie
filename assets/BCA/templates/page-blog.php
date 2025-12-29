<?php
    $query = array(
        'posts_per_page' => 10, 
        'paged' => $paged
    );

    if (isset($wp_query->query_vars['production'])) {
        $query['meta_key'] = 'production';
        $query['meta_value'] = (int) $_GET['production'];
    }

    query_posts($query);
?>


<?php get_template_part('templates/subnav', 'post'); ?>
<?php get_template_part('templates/archive', 'post'); ?>
