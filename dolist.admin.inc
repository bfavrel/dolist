<?php 
//Page utilisée pour générer la liste des champs actifs de la base Dolist
//email : l'email du contact qui va nous permettre de récupérer la liste des champs. Tous les champs doivent être remplis pour que la liste récupère la totalité de ceux ci 
function dolist_global_settings_form($form, &$form_state) {
  $form=array();
  $form['global_settings_email_test'] = array(
    '#type' => 'textfield',
    '#title' => 'Email',
    '#default_value' => variable_get('global_settings_email_test'),
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

  $form['global_settings_api_key'] = array(
    '#type' => 'textfield',
    '#title' => 'Clé Api',
    '#description' => 'Saisissez la clé API de votre compte Dolist',
    '#default_value' => variable_get('global_settings_api_key'),
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

  $form['global_settings_account_id'] = array(
    '#type' => 'textfield',
    '#title' => 'Identifiant du compte',
    '#description' => 'Saisissez l identifiant de votre compte Dolist',
    '#default_value' => variable_get('global_settings_account_id'),
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

   $form['global_settings_campaign_mail_tracking_domain'] = array(
    '#type' => 'textfield',
    '#title' => 'Tracking',
    '#description' => 'Nom de domaine du tracking',
    '#default_value' => 
    variable_get('global_settings_campaign_mail_tracking_domain'),
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

  $form['global_settings_fields_contact_dolist'] = array(
    '#type' => 'submit',
    '#value' => 'Generer liste des champs',
    '#submit' => array('generate_dolist_fields'),
  );

  return system_settings_form($form);
}

function generate_dolist_fields ($form,&$form_state) 
{ 
variable_set('global_settings_api_key',
  $form_state['values']['global_settings_api_key']);
variable_set('global_settings_account_id',
  $form_state['values']['global_settings_account_id']);
variable_set('global_settings_email_test',
  $form_state['values']['global_settings_email_test']);
variable_set('global_settings_campaign_mail_tracking_domain',
  $form_state['values']['global_settings_campaign_mail_tracking_domain']);
$api=new dolistAPI(variable_get('global_settings_api_key'),
  variable_get('global_settings_account_id'));
variable_set('global_settings_fields_contact_dolist',
  $api->createListContactFields());
drupal_set_message(t('La liste des champs modifiables à été créée.'));
}



?>