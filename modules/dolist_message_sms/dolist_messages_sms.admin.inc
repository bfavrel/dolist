<?php

function dolist_messages_sms_form($form, &$form_state) {
  $form=array();
  

  return system_settings_form($form);
}

function dolist_messages_sms_add_form($form,&$form_state) {

	 $form=array();
  $form['message_sms_creation_name'] = array(
    '#type' => 'textfield',
    '#title' => 'Name',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );
  $form['message_sms_creation_text'] = array(
     '#type' => 'textfield',
    '#title' => 'Text',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
    );



   $form['#submit'][] = 'create_message_sms';
  return system_settings_form($form);
}

function create_message_sms($form,&$form_state){
	$name=$form_state['values']['message_sms_creation_name'];
  $text=$form_state['values']['message_sms_creation_text'];
  
	$api=new dolistAPI(variable_get('Api'),variable_get('ID'));
  $result=$api->createMessageSms($name,$text);
  entity_form_field_validate('messagesms', $form, $form_state);
  $sms = new stdClass();
  $sms->title = $form_state['values']['message_sms_creation_name'];
  $sms->text = $form_state['values']['message_sms_creation_text'];

  // Enregistrement dans la base "books"
  drupal_write_record('messagesms', $sms);
  // L'objet est "rempli" avec les propriétés issues de form_state
  entity_form_submit_build_entity('messagesms', $sms, $form, $form_state);
  // Laissons aussi une chance à d'autres modules d'intervenir sur les Fields attachés.
  field_attach_submit('messagesms', $sms, $form, $form_state);
  // On insere les données des fields dans la base de données.
  field_attach_insert('messagesms', $sms);
  // Et un petit message de confirmation.
  watchdog('create_sms','nouveau sms @sms',array('@sms' => $sms->title));
  drupal_goto('admin/config/services/dolist/messagessms');
  
}
