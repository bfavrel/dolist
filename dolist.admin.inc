<?php 
/**
 * @file
 * (config_exemple.admin.inc) Exemple de formulaires de configuration
 */

function dolist_global_settings_form($form, &$form_state) {
  $form=array();
  $form['email'] = array(
    '#type' => 'textfield',
    '#title' => 'Email',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

  $form['Api'] = array(
    '#type' => 'textfield',
    '#title' => 'Clé Api',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

  $form['ID'] = array(
    '#type' => 'textfield',
    '#title' => 'Identifiant du compte',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );
 
  return system_settings_form($form);
 
}
?>