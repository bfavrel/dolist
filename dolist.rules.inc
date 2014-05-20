<?php
//hook_action_info
//On définit les deux actions de création et d'update d'un contact Dolist
function dolist_rules_action_info(){
$actions = array (
'dolist_creation' => array(
'label' => t('Action for creation contact Dolist'),
'group' => t('Dolist'),
'parameter' => array(
'email' => array(
'type' => 'text',
'label' => t('Email'),
),
),
),
'dolist_update' => array(
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
);
return $actions;
}

// Création d'un contact Dolist
// Pour que le contact soit créé, l'email doit être unique
function dolist_creation($email){
$api=new DolistAPI(variable_get('dolist_Api'),variable_get('ID'));
$result=$api->dolistcreation($email);
} 

// On créé la liste des champs actifs modifiables dans l'action de mise à jour d'un contact
function rules_fields_dolist_options_list() {
return variable_get('list_fields');
}

//Modification d'un contact existant
//On modifie un champ d'un contact de la base de données Dolist.
//On ne peut modifier un champ que si celui ci est actif dans la base Dolist
function dolist_update($email,$field,$replacement,$abomail,$abosms){
$api=new DolistAPI(variable_get('Api'),variable_get('ID'));
$result=$api->dolistUpdate($email,$field,$replacement,$abomail,$abosms);
}

  
 
?>