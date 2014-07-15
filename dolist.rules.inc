<?php
//hook_action_info
//On définit les deux actions de création et d'update d'un contact Dolist
function dolist_rules_action_info(){
$actions = array (
'dolist_creation_contact' => array(
'label' => t('Action for creation contact Dolist'),
'group' => t('Dolist'),
'parameter' => array(
'email' => array(
'type' => 'text',
'label' => t('Email'),
),
),
),
'dolist_update_contact' => array(
'label' => t('Action for updating a contact Dolist'),
'group' => t('Dolist'),
 'parameter' => array(
      'email' => array(
      'type' => 'text',
      'label' => t('Email'),
      ),
      'replacement' => array(
        'type' => 'text',
        'label' => t('Replacement'),
        'description' => t('The replacement for the selected field'),
        'allow_null'=> FALSE,
      ),
      'field' => array(
        'type' => 'text',
        'label' => t('Select the field to update'),
        'description' => t('The field Dolist to ne replaced'),
        'options list' => 'rules_fields_dolist_options_list',
      ),
      'abomail' => array(
        'type' => 'text',
        'label' => t('Abonnement mail'),
      ),
      'abosms' => array(
        'type' => 'text',
        'label' => t('Abonnement sms'),
      ),
    ),
  ),

'dolist_creation_campaign_mail' => array(
'label' => t('Action for creating a campaign mail dolist Dolist'),
'group' => t('Dolist'),
 'parameter' => array(
      'campaign_mail_from_address_prefix' => array(
      'type' => 'text',
      'label' => t('Préfixe expéditeur'),
      ),
      'campaign_mail_from_name' => array(
      'type' => 'text',
      'label' => t('Nom expéditeur'),
      ),
      'campaign_mail_reply_adress' => array(
      'type' => 'text',
      'label' => t('Adresse réponse'),
      ),
      'campaign_mail_reply_name' => array(
      'type' => 'text',
      'label' => t('Nom réponse'),
      ),
      'campaign_mail_message_id' => array(
      'type' => 'text',
      'label' => t('Id du message de la campagne'),
      ),
      'campaign_mail_subject' => array(
      'type' => 'text',
      'label' => t('Sujet / Objet'),
      ),
      'campaign_mail_format_link' => array(
      'type' => 'text',
      'label' => t('Type d affichage pour les liens techniques (Html / Texte)'),
      ),
     'campaign_mail_culture' => array(
      'type' => 'text',
      'label' => t('Culture de la campagne'),
      ),

    ),
  ),

'dolist_send_campaign_mail' => array(
'label' => t('Action for sending a campaign mail Dolist'),
'group' => t('Dolist'),
 'parameter' => array(
      'campaign_id' => array(
      'type' => 'text',
      'label' => t('Id de la campagne à envoyer'),
      ),
      'segment_id' => array(
      'type' => 'text',
      'label' => t('Id du segment'),
      ),
      'send_date' => array(
      'type' => 'text',
      'label' => t('date d envoi'),
      ),
      'volume' => array(
      'type' => 'text',
      'label' => t('volume d envoi'),
      ),
      'period' => array(
      'type' => 'text',
      'label' => t('Periode d envoi'),
      ),

    ),
  ),


);
return $actions;
}

// Création d'un contact Dolist
// Pour que le contact soit créé, l'email doit être unique
function dolist_creation_contact($email){
$api=new dolistAPI(variable_get('global_settings_api_key'),
  variable_get('global_settings_account_id'));
$result=$api->createContact($email);
} 

// On créé la liste des champs actifs modifiables dans l'action de mise à jour d'un contact
function rules_fields_dolist_options_list() {
return variable_get('global_settings_fields_contact_dolist');
}

//Modification d'un contact existant
//On modifie un champ d'un contact de la base de données Dolist.
//On ne peut modifier un champ que si celui ci est actif dans la base Dolist
function dolist_update_contact($email,$field,$replacement,$abomail,$abosms){
$api=new dolistAPI(variable_get('global_settings_api_key'),
  variable_get('global_settings_account_id'));
$result=$api->updateContact($email,$field,$replacement);
}

function dolist_creation_campaign_mail($campaign_mail_from_address_prefix,$campaign_mail_from_name,$campaign_mail_reply_adress,$campaign_mail_reply_name,$campaign_mail_message_id,$campaign_mail_subject,$campaign_mail_format_link,$campaign_mail_culture){
  $tracking=variable_get('global_settings_campaign_mail_tracking_domain');
  $api=new dolistAPI(variable_get('global_settings_api_key'),
  variable_get('global_settings_account_id'));
    $result=$api->createCampaignMail($campaign_mail_from_address_prefix,$campaign_mail_from_name,$campaign_mail_reply_adress,
        $campaign_mail_reply_name,$campaign_mail_subject,$campaign_mail_message_id,$tracking,$campaign_mail_culture,$campaign_mail_format_link);

}
 function dolist_send_campaign_mail($campaign_id,$segment_id,$send_date,$volume,$period){
  //TODO Fonction pour envoi réel quand cela sera possible de la tester.
 }

 
?>