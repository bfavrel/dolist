<?php

function campaign_sms_overview_page(){
  $api=new dolistAPI(variable_get('Api'),variable_get('ID'));
  $result=$api->getStatsCampaignsSms();
 $rows = array();
 foreach ($result as $key=>$csms) {
      if ($csms->Status=='10000'){
     $rows[] = array(
      $csms->DateCreate,
      $csms->DefaultPrefix,
      $csms->Message->Name,
      $csms->NbMembers,
      $csms->Status,
      $csms->TestMode,
      $csms->NbSimulateSms,
      implode(' | ', array(l(t('Simulate Sending'), 'admin/config/services/dolist/campaignssms/'.$csms->Id.'/simulate'),
        l(t('SendBAT'), 'admin/config/services/dolist/campaignssms/'.$csms->Id.'/sendtest'),
        l(t('Send'), 'admin/config/services/dolist/campaignssms/'.$csms->Id.'/sendreal'),
        ))

      );
   }
 }
    return array(
    '#theme' => 'table',
    '#header' => array(
      t('Date de création'),
      t('Préfixe téléphonique'),
      t('Message Associé'),
      t('Nombre de destinataires'),
      t('Statut'),
      t('Mode Test'),
      t('Simulation Nombre d envoi Sms'),
      t('Actions')
    ),
     '#rows' => $rows
  );
}

function campaign_sms_send_overview_page(){
  $api=new dolistAPI(variable_get('Api'),variable_get('ID'));
  $result=$api->getStatsCampaignsSms();
 $rows = array();
 foreach ($result as $key=>$csms) {
      if ($csms->Status=='10030'){
     $rows[] = array(
      $csms->DateCreate,
      $csms->DefaultPrefix,
      $csms->Message->Name,
      $csms->NbMembersRefused,
      $csms->NbSMS,
      $csms->Status
      );
   }
 }
    return array(
    '#theme' => 'table',
    '#header' => array(
      t('Date de création'),
      t('Préfixe téléphonique'),
      t('Message Associé'),
      t('Nombre de destinataires refusés'),
      t('Nombre Sms envoyés'),
      t('Statut')
    ),
     '#rows' => $rows
  );
}



function dolist_campaign_campaign_sms_form($form, &$form_state) {
  $form=array();
  

  return system_settings_form($form);
}

 function dolist_campaign_sms_simulate_form($form,&$form_state,$idcsms) {
  $form=array();
  $form_state['c'] = $idcsms;
  $form['segment_id'] = array(
    '#type' => 'textfield',
    '#title' => 'Segment Id',
    '#description' => 'Saisissez l Id du segment à lier à votre campagne',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );
   $form['#submit'][] = 'simulate_campaign_sms';
  return system_settings_form($form);
}
 
 function dolist_campaign_sms_sendtest_form($form,&$form_state,$idcsms) {
  $form=array();
  $form_state['c'] = $idcsms;
  $form['segment_id'] = array(
    '#type' => 'textfield',
    '#title' => 'Segment Id',
    '#description' => 'Saisissez l Id du segment à lier à votre campagne',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );
   $form['#submit'][] = 'send_campaign_sms_bat';
  return system_settings_form($form);
}

function dolist_campaign_sms_sendreal_form($form,&$form_state,$idcsms) {
  $form=array();
  $form_state['c'] = $idcsms;
  $form['segment_id'] = array(
    '#type' => 'textfield',
    '#title' => 'Segment Id',
    '#description' => 'Saisissez l Id du segment à lier à votre campagne',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );
   $form['#submit'][] = 'send_campaign_sms';
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

function simulate_campaign_sms($form,&$form_state){
$id=$form_state['c'];
  $segment=$form_state['values']['segment_id'];
 $api=new dolistAPI(variable_get('Api'),variable_get('ID'));
  $result=$api->sendCampaignSmsSimulate($id,$segment);
  drupal_goto('admin/config/services/dolist/campaignssms');
}

function send_campaign_sms_bat($form,&$form_state){
  $id=$form_state['c'];
  $segment=$form_state['values']['segment_id'];
 $api=new dolistAPI(variable_get('Api'),variable_get('ID'));
  $result=$api->sendCampaignSmsTest($id,$segment);
  drupal_goto('admin/config/services/dolist/campaignsms');
}

function send_campaign_sms($form,&$form_state){
  $id=$form_state['c'];
  $segment=$form_state['values']['segment_id'];
 $api=new dolistAPI(variable_get('Api'),variable_get('ID'));
  $result=$api->sendCampaignSms($id,$segment);
  drupal_goto('admin/config/services/dolist/campaignsms');
}