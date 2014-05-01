<?php 
/**
 * @file
 * (config_exemple.admin.inc) Exemple de formulaires de configuration
 */

function dolist_admin_form($form, &$form_state) {
 
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
 
  $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Mettre à jour',
      '#submit' => array('dolist_submit'),
  );
 
  return $form;
 
}

function dolist_submit($form, &$form_state) {
 
  $email = variable_get('dolist_email', NULL);
  $api = variable_get('dolist_api', NULL);
  $id = variable_get('dolist_id', NULL);
  $valeur_transmise = $form_state['values']['email'];
  $valeur_transmise2 = $form_state['values']['Api'];
  $valeur_transmise3 = $form_state['values']['ID'];
  if ($email != $valeur_transmise) {
    variable_set('dolist_email', $valeur_transmise);
    drupal_set_message('Email mis à jour avec ' . $valeur_transmise);
  }
  else {
   
  }
if ($api != $valeur_transmise2) {
    variable_set('dolist_api', $valeur_transmise2);
    drupal_set_message('Clé Api mise à jour avec ' . $valeur_transmise2);
  }
  else {
    
  }
 
 if ($id != $valeur_transmise3) {
    variable_set('dolist_id', $valeur_transmise3);
    drupal_set_message('Identifiant du compte mis à jour avec ' . $valeur_transmise3);
  }
  else {
   
  }
}
?>