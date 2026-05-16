<?php

/**
 * Add tag support to all post types.
 *
 * @return void
 */

 
 function ct_tag_support_pages()
 {
     /*add categories and tags to pages*/
     register_taxonomy_for_object_type('category', 'page');
     register_taxonomy_for_object_type('post_tag', 'page');
     register_taxonomy_for_object_type('post_tag', 'attachment');
 }
 add_action('init', 'ct_tag_support_pages');