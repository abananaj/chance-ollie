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
  register_block_style('core/heading', array(
    'name'  => 'uppercase',
    'label' => 'Uppercase',
  ));
  register_block_style('core/paragraph', array(
    'name'  => 'uppercase',
    'label' => 'Uppercase',
  ));
  register_block_style('core/group', array(
    'name'  => 'no-padding',
    'label' => 'No Padding',
  ));

  // BUTTONS
  
  register_block_style('core/button', array(
    'name'  => 'round',
    'label' => 'Round',
  ));
  register_block_style('core/button', array(
    'name'  => 'no-style',
    'label' => 'No Style',
  ));
  register_block_style('core/button', array(
    'name'  => 'right-to-left',
    'label' => 'RL Swipe',
  ));
  register_block_style('core/image', array(
    'name'  => 'no-shadow',
    'label' => 'No Shadow',
  ));
  register_block_style('core/button', array(
    'name'  => 'large',
    'label' => 'Large',
  ));
  register_block_style('core/button', array(
    'name'  => 'small',
    'label' => 'Small',
  ));
  register_block_style('core/button', array(
    'name'  => 'tiny',
    'label' => 'Tiny',
  ));
  register_block_style('core/button', array(
    'name'  => 'attention',
    'label' => 'Attention',
  ));

  register_block_style('core/navigation-link', array(
    'name'  => 'menu-heading',
    'label' => 'Menu Heading',
  ));
  register_block_style('core/terms-list', array(
    'name'  => 'no-style',
    'label' => 'No Style',
  ));
  register_block_style('core/list', array(
    'name'  => 'no-style',
    'label' => 'No Style',
  ));
  register_block_style('core/page-link', array(
    'name'  => 'submenu-heading-page',
    'label' => 'Submenu Heading',
  ));
  register_block_style('core/term-link', array(
    'name'  => 'submenu-heading-term',
    'label' => 'Submenu Heading',
  ));
  register_block_style('core/custom-link', array(
    'name'  => 'submenu-heading-custom',
    'label' => 'Submenu Heading',
  ));
}

add_action('init', 'ct_register_block_styles');
