<?php

function dolist_campaign_campaign_sms_form($form, &$form_state) {
  $form=array();
  

  return system_settings_form($form);
}

function dolist_campaign_sms_form($form,&$form_state) {

	 $form=array();
  $form['message_sms_id'] = array(
    '#type' => 'textfield',
    '#title' => 'Name',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );
   $form['#submit'][] = 'create_campaign_sms';
  return system_settings_form($form);
}

function create_campaign_sms($form,&$form_state){
	$campaignsms=$form_state['values']['message_sms_id'];
  $api=new dolistAPI(variable_get('Api'),variable_get('ID'));
  $result=$api->createCampaignSms($campaignsms);
  entity_form_field_validate('campaignsms', $form, $form_state);
  $sms = new stdClass();
  $sms->idmessage = $form_state['values']['message_sms_id'];
  
  // Enregistrement dans la base "books"
  drupal_write_record('campaignsms', $sms);
  // L'objet est "rempli" avec les propriétés issues de form_state
  entity_form_submit_build_entity('campaignsms', $sms, $form, $form_state);
  // Laissons aussi une chance à d'autres modules d'intervenir sur les Fields attachés.
  field_attach_submit('campaignsms', $sms, $form, $form_state);
  // On insere les données des fields dans la base de données.
  field_attach_insert('campaignsms', $sms);
  // Et un petit message de confirmation.
  watchdog('create_campaign','nouveau sms @sms',array('@sms' => $result));
  drupal_goto('admin/config/services/dolist/campaignssms');
  
}
