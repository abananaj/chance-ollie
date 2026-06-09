<?php
function ct_unregister_block_styles()
{
  unregister_block_style('core/button', 'button-brand');
  unregister_block_style('core/button', 'button-brand-alt');
  unregister_block_style('core/button', 'button-dark');
  unregister_block_style('core/button', 'button-light');
  unregister_block_style('core/button', 'secondary-button');
}
add_action('init', 'ct_unregister_block_styles', 20);
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
      array('name' => 'no-style', 'label' => 'No Style'),
      array('name' => 'attention', 'label' => 'Attention'),
      array('name' => 'blur', 'label' => 'Blur'),
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
