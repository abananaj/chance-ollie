<?php

get_template_part('templates/subnav', get_post_type());

$archive_type = get_post_type();
if (is_tax()) {
  global $wp_query;
  $term = $wp_query->get_queried_object();
  $archive_type = $term->taxonomy;
}

get_template_part('templates/archive', $archive_type);
