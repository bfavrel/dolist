<?php

function dolist_contacts_form($form, &$form_state) {
  $form=array();
  

  return system_settings_form($form);
}

function dolist_contacts_add_form($form,&$form_state) {

	 $form=array();
  $form['emailcreation'] = array(
    '#type' => 'textfield',
    '#title' => 'Email',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );
   //$form['#submit'][] = 'create_contact';
  return system_settings_form($form);
}

/*function create_contact($form,&$form_state){
	$email=$form_state['values']['emailcreation'];
	$api=new dolistAPI(variable_get('Api'),variable_get('ID'));
    $result=$api->createContact($email);

}
*/