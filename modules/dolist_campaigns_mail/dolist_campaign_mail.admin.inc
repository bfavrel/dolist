<?php

function dolist_campaign_campaign_mail_form($form, &$form_state) {
  $form=array();
  

  return system_settings_form($form);
}

function dolist_campaign_create_mail_form($form, &$form_state) {
  $form=array();
  

   $form['FromAdressPrefix'] = array(
    '#type' => 'textfield',
    '#title' => 'From Adress Prefix',
    '#description' => 'Préfixe de l adresse de l expéditeur',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

  $form['FromName'] = array(
    '#type' => 'textfield',
    '#title' => 'From Name',
    '#description' => 'Nom de lexpéditeur',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

$form['ReplyAddress'] = array(
    '#type' => 'textfield',
    '#title' => 'Reply Adress',
    '#description' => 'Saisissez la clé API de votre compte Dolist',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

$form['ReplyName'] = array(
    '#type' => 'textfield',
    '#title' => 'Reply Name',
    '#description' => ' Nom de réponse',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

$form['Subject'] = array(
    '#type' => 'textfield',
    '#title' => 'Subject',
    '#description' => 'Sujet/Objet',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );
  
$form['MessageID'] = array(
    '#type' => 'textfield',
    '#title' => 'Message ID',
    '#description' => 'Message de la campagne',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

$form['Culture'] = array(
    '#type' => 'textfield',
    '#title' => 'Culture',
    '#description' => ' Culture de la campagne',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

$form['FormatLink'] = array(
    '#type' => 'textfield',
    '#title' => 'Format Link',
    '#description' => 'format d affichage des données techniques',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );
 $form['#submit'][] = 'create_campaign_mail';
  return system_settings_form($form);
}
function create_campaign_mail($form,&$form_state){
	$fromaddress=$form_state['values']['FromAdressPrefix'];
    $fromname=$form_state['values']['FromName'];
    $replyaddress=$form_state['values']['ReplyAddress'];
    $replyname=$form_state['values']['ReplyName'];
    $subject=$form_state['values']['Subject'];
    $messageid=$form_state['values']['MessageID'];
    $culture=$form_state['values']['Culture'];
    $format=$form_state['values']['FormatLink'];
    $tracking=variable_get('tracking');

    $api=new dolistAPI(variable_get('Api'),variable_get('ID'));
    $result=$api->createCampaignMail($fromaddress,$fromname,$replyaddress,
        $replyname,$subject,$messageid,$tracking,$culture,$format);
    $mail = new stdClass();
    $mail->fromadressprefix = $form_state['values']['FromAdressPrefix'];
    $mail->fromadressname = $form_state['values']['FromName']; 
    $mail->replyadd = $form_state['values']['ReplyAddress'];
    $mail->replyname = $form_state['values']['ReplyName'];
    $mail->message = $form_state['values']['MessageID'];

    // Enregistrement dans la base "books"
  drupal_write_record('campaignmail', $mail);
  // L'objet est "rempli" avec les propriétés issues de form_state
  entity_form_submit_build_entity('campaignmail', $mail, $form, $form_state);
  // Laissons aussi une chance à d'autres modules d'intervenir sur les Fields attachés.
  field_attach_submit('campaignmail', $mail, $form, $form_state);
  // On insere les données des fields dans la base de données.
  field_attach_insert('campaignmail', $mail);
  drupal_goto('admin/config/services/dolist/campaignsmail');
    
  }

