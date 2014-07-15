<?php

function dolist_campaign_campaign_mail_form($form, &$form_state) {
  $form=array();
  

  return system_settings_form($form);
}

function dolist_campaign_create_mail_form($form, &$form_state) {
  $form=array();
  

   $form['campaign_mail_from_address_prefix'] = array(
    '#type' => 'textfield',
    '#title' => 'Préfixe expéditeur',
    '#description' => 'Préfixe de l adresse de l expéditeur',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

  $form['campaign_mail_from_name'] = array(
    '#type' => 'textfield',
    '#title' => 'Nom expéditeur',
    '#description' => 'Nom de lexpéditeur',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

$form['campaign_mail_reply_adress'] = array(
    '#type' => 'textfield',
    '#title' => 'Adresse réponse',
    '#description' => 'Adresse mail de réponse',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

$form['campaign_mail_reply_name'] = array(
    '#type' => 'textfield',
    '#title' => 'Reply Name',
    '#description' => ' Nom de réponse',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

$form['campaign_mail_subject'] = array(
    '#type' => 'textfield',
    '#title' => 'Subject',
    '#description' => 'Sujet/Objet',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );
  
$form['campaign_mail_message_id'] = array(
    '#type' => 'textfield',
    '#title' => 'Message ID',
    '#description' => 'Message de la campagne',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

$form['campaign_mail_culture'] = array(
    '#type' => 'textfield',
    '#title' => 'Culture',
    '#description' => ' Culture de la campagne',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

$form['campaign_mail_format_link'] = array(
    '#type' => 'textfield',
    '#title' => 'Format Link',
    '#description' => 'Type d affichage pour les liens techniques (Html / Texte)',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );


# the options to display in our checkboxes
$toppings = array(
  'oui' => t('Oui'),
  'non' => t('Non')
);

# the drupal checkboxes form field definition
$form['VersionOnline'] = array(
  '#title' => t('Version Online'),
  '#type' => 'checkboxes',
  '#description' => t(' Ajout du lien pour la version en ligne.'),
  '#options' => $toppings,
);
 $form['#submit'][] = 'create_campaign_mail';
  return system_settings_form($form);
}
function create_campaign_mail($form,&$form_state){
	$fromaddress=$form_state['values']['campaign_mail_from_address_prefix'];
    $fromname=$form_state['values']['campaign_mail_from_name'];
    $replyaddress=$form_state['values']['campaign_mail_reply_adress'];
    $replyname=$form_state['values']['campaign_mail_reply_name'];
    $subject=$form_state['values']['campaign_mail_subject'];
    $messageid=$form_state['values']['campaign_mail_message_id'];
    $culture=$form_state['values']['campaign_mail_culture'];
    $format=$form_state['values']['campaign_mail_format_link'];
    $tracking=variable_get('global_settings_campaign_mail_tracking_domain');

    $api=new dolistAPI(variable_get('global_settings_api_key'),
  variable_get('global_settings_account_id'));
    $result=$api->createCampaignMail($fromaddress,$fromname,$replyaddress,
        $replyname,$subject,$messageid,$tracking,$culture,$format);
    $mail = new stdClass();
    $mail->fromadressprefix = 
    $form_state['values']['campaign_mail_from_address_prefix'];
    $mail->fromadressname = $form_state['values']['campaign_mail_from_name']; 
    $mail->replyadd = $form_state['values']['campaign_mail_reply_adress'];
    $mail->replyname = $form_state['values']['campaign_mail_reply_name'];
    $mail->message = $form_state['values']['campaign_mail_message_id'];

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

