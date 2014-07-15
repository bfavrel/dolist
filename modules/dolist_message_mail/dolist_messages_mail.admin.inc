<?php

function dolist_messages_mail_form($form, &$form_state) {
  $form=array();
  

  return system_settings_form($form);
}

function dolist_create_message_mail_form($form,&$form_state) {

   //$form['#submit'][] = 'create_contact';
  return system_settings_form($form);
}

function dolist_messages_sms_add_type_form($form,&$form_state) {

   
	 $form=array();
  $form['message_mail_creation_type'] = array(
    '#type' => 'textfield',
    '#title' => 'Type Name',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

   $form['#submit'][] = 'dolist_messages_mail_entity_info';
  return system_settings_form($form);
}

function dolist_messages_mail_entity_info($form,&$form_state) {
$name=$form_state['values']['message_mail_creation_type'];
  $entity = array();
  $entity['messagesmail'] = array (
    'label' => t('Messages'),
    // la table definie dans books.install
    'base table' => 'messagesmail',
    'uri callback' => 'dolist_messages_mail_uri',
    // on peut y attacher des fields
    'fieldable' => TRUE,
    'entity keys' => array(
      // utilisé par entity_load pour requeter la base table
      'id' => 'bid' ,
      // utilisé par field_attach api pour charger les fields attachés
      'bundle' => 'type'
    ),
    'bundle keys' => array('bundle' => 'type'),
    'bundles' => array()
  );
  // Message simple bundle
  $entity['messagesmail']['bundles']['message_simple'] = array(
    'label' => t('Message Simple'),
    'admin' => array(
      // le chemin defini dans message_mail_menu
      // Le Field api utilise ceci pour ajouter ses éléments MENU_LOCAL_TASK
      // qui servent a administer et afficher les fields
      'path' => 'admin/structure/dolist/messagesmail/%message_type',
      // le "real path" une fois l'argument chargé
      'real path' => 'admin/structure/dolist/messagesmail/message_simple',
      // %books_type, c'est arg(2), donc on le passe ...
      'bundle argument' => 5,
      // on reste en "administer nodes" juste pour ne pas s'embeter avec les permissions :)
      'access arguments' => array('administer nodes')
    )
  );

  
  return $entity;
}

function dolist_messages_mail_uri($messagesmail) {
  return array('path' => 'structure/dolist/messagesmail/' . $messagesmail->bid );
}

/**
 * Argument loader for %message_type
 * Verification de ce qu'on passe via %message_type afin d'eviter toute tentative d'injection.
 */
function message_type_load($message_type) {
  switch ($message_type) {
  
  case 'message_simple':
    return $message_type;
  default:
    return FALSE;
  }
}

/**
 * On retourne juste le type de l'object
 */
function message_type_title($message_type) {
  return $message_type;
}



/**
 * La page d'administration de nos Bundles message
 * Par defaut, on liste les bundles, si on clic sur un bundle,
 * on obtiens la page d'administration du bundle en question.
 * Il s'agit de la page où l'on va retrouver les tabs permettant
 * de gerer les fields attaché et l'affichage de ceux-ci.
 */
function message_admin($type = NULL) {
  if ($type) {
    return drupal_get_form('message_add', $type);
  }
  else{
    // pour faire plus simple, on hardcode les bundles existants.
    $rows = array();
    $rows[] = array(l(t('Message Simple'), 'admin/structure/dolist/messagesmail/message_simple'));
    $header =  array('Type');
    $content = array(
      '#theme' => 'table', // on veut un affichage tableau
      '#header' => $header, // Un peu d'informations pour les headers, c'est joli
      '#rows' => $rows, // et les links vers les 2 bundles existants
    );
    return $content;
  }
}

/**
 * Ajout d'un nouveau book, du type selectionné.
 */
function message_add($form ,&$form_state, $type) {
  $form['name'] = array(
    '#type' => 'textfield',
    '#title' => 'name'
  );
 
  $message = new stdClass();
  $message->type = $type;
  // l'object book lui même
  $form['messagesmail'] = array(
    '#type' => 'value',
    '#value' => $message
  );
  // le type du bundle, indispensable pour passer la validation
  $form['type'] = array(
    '#type' => 'value',
    '#value' => $type
  );
  // On attache les formulaires des fields attachés
  field_attach_form('messagesmail',$message, $form, $form_state);
 
  $form['actions'] = array('#type' => 'actions');
  $form['actions']['add'] = array(
    '#type' => 'submit',
    '#value' => 'add'
  );
  return $form;
}

function message_add_validate($form, &$form_state) {
  entity_form_field_validate('messagesmail', $form, $form_state);
}

function message_add_submit($form, &$form_state) {
  $message = $form_state['values']['messagesmail'];
  $message->name = $form_state['values']['name'];
  // Enregistrement dans la base "messagesmail"
  drupal_write_record('messagesmail', $message);
  // L'objet est "rempli" avec les propriétés issues de form_state
  entity_form_submit_build_entity('messagesmail', $message, $form, $form_state);
  // Laissons aussi une chance à d'autres modules d'intervenir sur les Fields attachés.
  field_attach_submit('messagesmail', $message, $form, $form_state);
  // On insere les données des fields dans la base de données.
  field_attach_insert('messagesmail', $message);
  // Et un petit message de confirmation.
  drupal_set_message(
    t('new @type got added' ,
    array('@type' => $message->type))
  );
}