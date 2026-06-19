<?php

// Unregister Ollie patterns & Pattern Categories
function chance_unregister_ollie_patterns()
{
  // Unregister all patterns registered by the Ollie parent theme
  $ollie_patterns = array(
    'ollie/author-box',
    'ollie/big-text-call-to-action-card',
    'ollie/blog-page',
    'ollie/blog-post-card',
    'ollie/blog-post-columns',
    'ollie/blog-post-columns-single',
    'ollie/call-to-action-card-with-buttons',
    'ollie/card-call-to-action',
    'ollie/card-contact',
    'ollie/card-pricing-table-dark',
    'ollie/card-testimonial',
    'ollie/card-text-and-call-to-action',
    'ollie/contact-details',
    'ollie/details-card',
    // 'ollie/faq',
    'ollie/feature-boxes-with-button',
    'ollie/feature-boxes-with-icon-dark',
    'ollie/features-with-emojis',
    'ollie/footer-dark',
    'ollie/footer-dark-centered',
    'ollie/footer-dark-minimal',
    'ollie/footer-light',
    'ollie/footer-light-centered',
    'ollie/footer-light-minimal',
    'ollie/header-dark',
    'ollie/header-dark-with-banner',
    'ollie/header-dark-with-buttons',
    'ollie/header-light',
    'ollie/header-light-with-banner',
    'ollie/header-light-with-buttons',
    'ollie/hero-call-to-action-buttons',
    'ollie/hero-call-to-action-buttons-light',
    'ollie/hero-dark',
    'ollie/hero-light',
    'ollie/hero-text-image-and-logos',
    'ollie/image-and-numbered-features',
    'ollie/image-and-text-card',
    'ollie/job-openings',
    'ollie/large-text-and-text-boxes',
    'ollie/lead-magnet-card',
    'ollie/menu-card-1',
    'ollie/menu-card-2',
    'ollie/menu-card-3',
    'ollie/menu-card-4',
    'ollie/menu-panel-1',
    'ollie/menu-panel-2',
    'ollie/menu-panel-3',
    'ollie/menu-panel-4',
    'ollie/menu-panel-5',
    'ollie/menu-panel-6',
    'ollie/mobile-menu-1',
    'ollie/mobile-menu-2',
    'ollie/mobile-menu-3',
    'ollie/mobile-menu-4',
    'ollie/mobile-menu-5',
    'ollie/mobile-menu-6',
    'ollie/numbers',
    'ollie/numbers-stacked',
    'ollie/page-about',
    'ollie/page-download',
    'ollie/page-features',
    'ollie/page-home',
    'ollie/page-pricing',
    'ollie/post-comments',
    'ollie/post-list-card',
    'ollie/post-loop-grid',
    'ollie/post-loop-grid-default',
    'ollie/post-loop-list',
    'ollie/pricing-table',
    'ollie/pricing-table-3-column',
    'ollie/pricing-table-card',
    'ollie/pricing-table-with-testimonials',
    'ollie/profile-box',
    'ollie/single-testimonial',
    'ollie/social-profile-card',
    'ollie/team-members',
    'ollie/template-index-grid',
    'ollie/template-index-list',
    'ollie/template-page-404',
    'ollie/template-page-archive',
    'ollie/template-page-centered',
    'ollie/template-page-full',
    'ollie/template-page-left-sidebar',
    'ollie/template-page-right-sidebar',
    'ollie/template-page-search',
    'ollie/template-page-wide',
    'ollie/template-post-centered',
    'ollie/template-post-left-sidebar',
    'ollie/template-post-right-sidebar',
    'ollie/template-post-sticky',
    'ollie/template-post-wide',
    'ollie/testimonial-highlight',
    'ollie/testimonials-and-logos',
    'ollie/testimonials-with-big-text',
    'ollie/testimonials-with-social-links',
    'ollie/text-and-details-card',
    'ollie/text-and-image-columns-with-icons',
    'ollie/text-and-image-columns-with-testimonial',
    'ollie/text-box-with-link-card',
    'ollie/text-call-to-action',
    'ollie/text-call-to-action-buttons',
    'ollie/woo-cart',
    'ollie/woo-checkout',
    'ollie/woo-coming-soon',
    // 'woocommerce/add-to-cart-with-options-variation-description',
    // 'woocommerce/add-to-cart-with-options-variation-selector',
    // 'woocommerce/product-button',
    // 'woocommerce/add-to-cart-with-options-quantity-selector',
    'ollie/woo-header',
    'ollie/woo-order-confirmation',
    'ollie/woo-product-archive',
    'ollie/woo-product-archive-sidebar',
    'ollie/woo-product-card',
    'ollie/woo-product-card-2',
    'ollie/woo-product-card-3',
    'ollie/woo-product-card-4',
    'ollie/woo-product-card-5',
    'ollie/woo-product-search',
    'ollie/woo-single-product',
    
  );

  foreach ($ollie_patterns as $pattern) {
    unregister_block_pattern($pattern);
  }

  // Unregister Ollie pattern categories
  $ollie_categories = array(
    'ollie/call-to-action',
    'ollie/features',
    'ollie/hero',
    'ollie/pages',
    'ollie/posts',
    'ollie/pricing',
    'ollie/testimonial',
    'footer',
    'header',
  );

  foreach ($ollie_categories as $category) {
    unregister_block_pattern_category($category);
  }
}
// add_action('init', 'chance_unregister_ollie_patterns', 20);


// function chance_register_block_patterns()
// {
//   $pattern_dir = get_stylesheet_directory() . '/patterns';
//   $patterns = array(
//     'pattern-example' => array(
//       'title'       => __('Pattern Example', 'chance-ollie'),
//       'description' => __('Example Pattern', 'chance-ollie'),
//       'categories'  => array('media'),
//       'synced'      => true,
//     ),
//   );

//   foreach ($patterns as $slug => $properties) {
//     $pattern_file = $pattern_dir . '/' . $slug . '.html';

//     if (file_exists($pattern_file)) {
//       $content = file_get_contents($pattern_file);
//       if ($content !== false) {
//         $pattern_properties = array_merge(
//           $properties,
//           array(
//             'content' => $content,
//           )
//         );
//         register_block_pattern('chance-ollie/' . $slug, $pattern_properties);
//       }
//     }
//   }
// }
// add_action('init', 'chance_register_block_patterns', 21);
