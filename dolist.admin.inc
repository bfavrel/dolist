<?php 
//Page utilisée pour générer la liste des champs actifs de la base Dolist
//email : l'email du contact qui va nous permettre de récupérer la liste des champs. Tous les champs doivent être remplis pour que la liste récupère la totalité de ceux ci 
function dolist_global_settings_form($form, &$form_state) {
  $form=array();
  $form['email'] = array(
    '#type' => 'textfield',
    '#title' => 'Email',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

  $form['Api'] = array(
    '#type' => 'textfield',
    '#title' => 'Clé Api',
    '#description' => 'Saisissez la clé API de votre compte Dolist',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

  $form['ID'] = array(
    '#type' => 'textfield',
    '#title' => 'Identifiant du compte',
    '#description' => 'Saisissez l identifiant de votre compte Dolist',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );

  $form['fields'] = array(
    '#type' => 'submit',
    '#value' => 'Generer liste des champs',
    '#submit' => array('generate_dolist_fields'),
  );

  return system_settings_form($form);
}

function generate_dolist_fields ($form,&$form_state) 
{ 
variable_set('Api',$form_state['values']['Api']);
variable_set('ID',$form_state['values']['ID']);
variable_set('email',$form_state['values']['email']);
$api=new dolistAPI(variable_get('Api'),variable_get('ID'));
variable_set('list_fields',$api->createListContactFields());
drupal_set_message(t('La liste des champs modifiables à été créée.'));
}



?>