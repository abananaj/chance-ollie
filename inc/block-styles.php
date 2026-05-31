<?php
function ct_unregister_block_styles()
{
  unregister_block_style('core/button', 'brand');
  unregister_block_style('core/button', 'brand=alt');
  unregister_block_style('core/button', 'attention');
  unregister_block_style('core/button', 'donate');
  unregister_block_style('core/button', 'join');
}
/**
 * Register custom block styles.
 */
function ct_register_block_styles()
{
  $block_styles = array(
    'core/heading' => array(
      array('name' => 'uppercase', 'label' => 'Uppercase'),
    ),
    'core/paragraph' => array(
      array('name' => 'uppercase', 'label' => 'Uppercase'),
    ),
    'core/group' => array(
      array('name' => 'no-padding', 'label' => 'No Padding'),
    ),
    'core/button' => array(
      array('name' => 'round', 'label' => 'Round'),
      array('name' => 'no-style', 'label' => 'No Style'),
      array('name' => 'right-to-left', 'label' => 'RL Swipe'),
      array('name' => 'large', 'label' => 'Large'),
      array('name' => 'small', 'label' => 'Small'),
      array('name' => 'tiny', 'label' => 'Tiny'),
      array('name' => 'attention', 'label' => 'Attention'),
      array('name' => 'ghost', 'label' => 'Ghost'),
    ),
    'core/image' => array(
      array('name' => 'no-shadow', 'label' => 'No Shadow'),
      array('name' => 'boxed', 'label' => 'Boxed'),
    ),
    'core/navigation-link' => array(
      array('name' => 'menu-heading', 'label' => 'Menu Heading'),
    ),
    'core/terms-list' => array(
      array('name' => 'no-style', 'label' => 'No Style'),
    ),
    'core/list' => array(
      array('name' => 'no-style', 'label' => 'No Style'),
    ),
    'core/page-link' => array(
      array('name' => 'submenu-heading-page', 'label' => 'Submenu Heading'),
    ),
    'core/term-link' => array(
      array('name' => 'submenu-heading-term', 'label' => 'Submenu Heading'),
    ),
    'core/custom-link' => array(
      array('name' => 'submenu-heading-custom', 'label' => 'Submenu Heading'),
    ),
  );

  foreach ($block_styles as $block => $styles) {
    foreach ($styles as $style) {
      register_block_style($block, $style);
    }
  }
}

add_action('init', 'ct_register_block_styles');
