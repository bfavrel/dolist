<?php

function campaign_sms_overview_page(){
  $api=new dolistAPI(variable_get('global_settings_api_key'),
  variable_get('global_settings_account_id'));
  $result=$api->getStatsCampaignsSms();
 $rows = array();
 foreach ($result as $key=>$csms) {
      if ($csms->Status=='10000'){
     $rows[] = array(
      $newDate = date("d F Y H:i", strtotime($csms->DateCreate)),
      $csms->Message->Name,
      $csms->NbMembers,
      $csms->Status,
      $csms->TestMode,
      $csms->NbSimulateMembers,
      $csms->NbSimulateMembersRefused,
      $csms->NbSimulateMobileDistinct,
      $csms->NbSimulateSMSDistinct,
      implode(' | ', array(l(t('Simulate'), 'admin/structure/dolist/campaignssms/'.$csms->Id.'/simulate'),
        l(t('BAT'), 'admin/structure/dolist/campaignssms/'.$csms->Id.'/sendtest'),
        l(t('Send'), 'admin/structure/dolist/campaignssms/'.$csms->Id.'/sendreal'),
        ))

      );
   }
 }
    return array(
    '#theme' => 'table',
    '#header' => array(
      t('Date de création'),
      t('Message Associé'),
      t('Nombre de destinataires'),
      t('Statut'),
      t('Mode Test'),
      t('Nombre de contacts destinataires'),
      t('Nombre de contacts refusés'),
      t('Nombre distinct de mobiles'),
      t(' Nombre de SMS à envoyer sur les mobiles uniques'),
      t('Actions')
    ),
     '#rows' => $rows
  );
}

function campaign_sms_send_overview_page(){
  $api=new dolistAPI(variable_get('global_settings_api_key'),
  variable_get('global_settings_account_id'));
  $result=$api->getStatsCampaignsSms();
 $rows = array();
 foreach ($result as $key=>$csms) {
      if ($csms->Status=='10030'){
     $rows[] = array(
      $newDate = date("d F Y H:i", strtotime($csms->DateCreate)),
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
  $form['campaign_sms_segment_id'] = array(
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
  $form['campaign_sms_segment_id'] = array(
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
  $form['campaign_sms_segment_id'] = array(
    '#type' => 'textfield',
    '#title' => 'Segment Id',
    '#description' => 'Saisissez l Id du segment à lier à votre campagne',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );
   $format = 'H:i:s:m:d:Y';
  $form['sending_date'] = array(
     '#type' => 'date_select', // types 'date_text' and 'date_timezone' are also supported. See .inc file.
     '#title' => t('Select a sending date'),
     '#default_value' => '',
     '#date_format' => $format,
     '#date_label_position' => 'within', // See other available attributes and what they do in date_api_elements.inc
     '#date_timezone' => '', // Optional, if your date has a timezone other than the site timezone.
     '#date_increment' => 15, // Optional, used by the date_select and date_popup elements to increment minutes and seconds.
     '#date_year_range' => '-1:+10', // Optional, used to set the year range (back 3 years and forward 3 years is the default).
  );
   $form['#submit'][] = 'send_campaign_sms';
  return system_settings_form($form);
}

 

function dolist_campaign_sms_form($form,&$form_state) {

	 $form=array();
  $form['campaign_sms_message_sms_id'] = array(
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
	$campaignsms=$form_state['values']['campaign_sms_message_sms_id'];
  $api=new dolistAPI(variable_get('global_settings_api_key'),
  variable_get('global_settings_account_id'));
  $result=$api->createCampaignSms($campaignsms);
  entity_form_field_validate('campaignsms', $form, $form_state);
  $sms = new stdClass();
  $sms->idmessage = $form_state['values']['campaign_sms_message_sms_id'];
  
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
  $segment=$form_state['values']['campaign_sms_segment_id'];
 $api=new dolistAPI(variable_get('global_settings_api_key'),
  variable_get('global_settings_account_id'));
  $result=$api->sendCampaignSmsSimulate($id,$segment);
  drupal_goto('admin/structure/dolist/campaignssms');
  
/*
  $bundle = new stdClass();
    $bundle->name = 'test3';
    $bundle->bundle = 'bundle_test3';
    $bundle->locked = 0;



  $status = paragraphs_bundle_save($bundle);

  $t_args = array('%name' => $bundle->name);

  if ($status == SAVED_UPDATED) {
    drupal_set_message(t('The paragraph bundle %name has been updated.', $t_args));
  }
  elseif ($status == SAVED_NEW) {
    drupal_set_message(t('The paragraph bundle %name has been added.', $t_args));
    watchdog('node', 'Added paragraph bundle %name.', $t_args, WATCHDOG_NOTICE, l(t('view'), 'admin/structure/paragraphs'));
  }

  return;

  */
}

function send_campaign_sms_bat($form,&$form_state){
  $id=$form_state['c'];
  drupal_set_message(t('Test rules with parameter with @username',
  array('@username' => $id)));

  $segment=$form_state['values']['campaign_sms_segment_id'];
 $api=new dolistAPI(variable_get('global_settings_api_key'),
  variable_get('global_settings_account_id'));
  $result=$api->sendCampaignSmsTest($id,$segment);
  drupal_goto('admin/structure/dolist/campaignssms');
}

function send_campaign_sms($form,&$form_state){
  $id=$form_state['c'];
  $segment=$form_state['values']['campaign_sms_segment_id'];
  $date=$form_state['values']['sending_date'];
  $date=date("H:i:s:m:d:Y",strtotime($date));
  $tab = explode(":",$date);
  $heure = $tab[0];
  $minute = $tab[1];
  $seconde = $tab[2];
  $mois = $tab[3];
  $jour = $tab[4];
  $annee = $tab[5];
  $date2= mktime($heure,$minute,$seconde,$mois,$jour,$annee);
 $api=new dolistAPI(variable_get('global_settings_api_key'),
  variable_get('global_settings_account_id'));
  $result=$api->sendCampaignSms($id,$segment,$date2);
  drupal_goto('admin/structure/dolist/campaignssms');
}