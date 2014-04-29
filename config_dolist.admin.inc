<?php 
/**
 * @file
 * (config_exemple.admin.inc) Exemple de formulaires de configuration
 */

function config_exemple_admin_form($form, &$form_state) {
 
  $form['config_dolist'] = array(
    '#type' => 'textfield',
    '#title' => 'Email',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );
 
  $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Mettre à jour',
      '#submit' => array('config_dolist_admin_submit'),
  );
 
  return $form;
 
}

?>