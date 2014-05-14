<?php

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
    ),
  ),
);
return $actions;
}
 

 
 function dolist_creation($email){
$api=new DolistAPI(variable_get('dolist_api'),variable_get('dolist_id'));
$result=$api->dolistcreation($email);
} 



function rules_fields_dolist_options_list() {
$api=new DolistAPI(variable_get('dolist_api'),variable_get('dolist_id'));
return $api->dolistListFields();
}

function dolist_update($email,$field,$replacement){
  
 $api=new DolistAPI(variable_get('dolist_api'),variable_get('dolist_id'));
 $result=$api->dolistUpdate($email,$field,$replacement);

}

  
 
?>