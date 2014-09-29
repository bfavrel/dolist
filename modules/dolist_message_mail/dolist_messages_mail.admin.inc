<?php

/**
 * @file
 * Admin functions for the paragraphs module.
 */

function dolist_messages_mail_admin_bundle_overview() {
  $bundles = messages_bundle_load();

  $header = array(t('Bundle name'), array('data' => t('Operations'), 'colspan' => '5'));
  $rows = array();
  foreach ($bundles as $bundle) {
    $rows[] = array(
      array(
        'data' => $bundle->name . ' (' . $bundle->bundle . ')',

      ),
       array(
        'data' => l(t('instantate bundle'), 'admin/structure/dolist/messagesmail/' . strtr($bundle->bundle, array('_' => '-')) . '/instantate')

      ),
      array(
        'data' => l(t('manage fields'), 'admin/structure/dolist/messagesmail/' . strtr($bundle->bundle, array('_' => '-')) . '/fields')

      ),
      array(
        'data' => l(t('manage display'), 'admin/structure/dolist/messagesmail/' . strtr($bundle->bundle, array('_' => '-')) . '/display')
      ),
      array(
        'data' => l(t('edit bundle'), 'admin/structure/dolist/messagesmail/' . strtr($bundle->bundle, array('_' => '-')) . '/edit')
      ),
      array(
        'data' => l(t('delete bundle'), 'admin/structure/dolist/messagesmail/' . strtr($bundle->bundle, array('_' => '-')) . '/delete')
      ),
    );
  }
  if (empty($rows)) {
    $output = t('No message bundles have been defined yet.');
  }
  else {
    // Sort rows by field name.
    ksort($rows);
    $output = theme('table', array('header' => $header, 'rows' => $rows));
  }
  return $output;
}

function dolist_messages_mail_instances_overview(){
$instances=entity_load('messages_item');
 $rows = array();
 foreach ($instances as $key => $value) {
 
     $rows[] = array(
      $value->bundle,
      $value->item_id,
      l('View instance','admin/structure/dolist/messagesmail/'. strtr($value->item_id, array('_' => '-')) . '/viewinstance')
      );
 }
    return array(
    '#theme' => 'table',
    '#header' => array(
      t('Bundle'),
      t('Instance n°'),
      t('Fields')
    ),
     '#rows' => $rows
  );

}





function dolist_messages_mail_admin_display_instance_form($form,&$form_state,$item_id){
$instances=entity_load('messages_item',array($item_id));
$content[]=array();
foreach ($instances as $key => $value) {
  // on oublie pas d'attacher les fields supplémentaires.
  $content[] = field_attach_view('messages_item', $value ,'full');
}
  return $content;

}

/**
 * Form to create or edit an paragraph bundle.
 */
function dolist_messages_mail_admin_bundle_form($form, &$form_state, $bundle = NULL) {

  if (!isset($bundle) && !$bundle) {
    // This is a new bundle
    $bundle = new stdClass();
    $bundle->name = '';
    $bundle->bundle = '';
    $bundle->locked = 0;
  } else {
    if(!$bundle) {
      drupal_set_message(t('Could not load bundle'), 'error');
      drupal_goto('admin/structure/dolist/messagesmail');
    }
  }

  $form['#messages_bundle'] = $bundle;

  $form['name'] = array(
    '#title' => t('Name'),
    '#type' => 'textfield',
    '#default_value' => $bundle->name,
    '#description' => t('The human-readable name of this bundle. It is recommended that this name begin with a capital letter and contain only letters, numbers, and spaces. This name must be unique.'),
    '#required' => TRUE,
    '#size' => 30,
  );

  if(!$bundle->locked) {

    $form['bundle'] = array(
      '#type' => 'machine_name',
      '#default_value' => $bundle->bundle,
      '#maxlength' => 32,
      '#disabled' => $bundle->locked,
      '#machine_name' => array(
        'exists' => 'messages_bundle_load',
      ),
      '#description' => t('A unique machine-readable name for this paragraph bundle. It must only contain lowercase letters, numbers, and underscores.'),
    );
  }

  $form['locked'] = array(
    '#type' => 'value',
    '#value' => $bundle->locked,
  );

  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save Message bundle'),
    '#weight' => 40,
  );

  return $form;
}

/**
 * Form validation handler for paragraphs_admin_bundle_form().
 *
 * @see paragraphs_admin_bundle_form_submit()
 */
function dolist_messages_mail_admin_bundle_form_validate($form, &$form_state) {
  $bundle = new stdClass();
  $bundle->name = trim($form_state['values']['name']);

  if (!$form_state['values']['locked']) {
    $bundle->bundle = trim($form_state['values']['bundle']);
    // 'theme' conflicts with theme_node_form().
    // '0' is invalid, since elsewhere we check it using empty().
    if (in_array($bundle->bundle, array('0', 'theme'))) {
      form_set_error('type', t("Invalid machine-readable name. Enter a name other than %invalid.", array('%invalid' => $bundle->bundle)));
    }
  }

}

/**
 * Submit handler for paragraphs_admin_bundle_form().
 *
 * @see paragraphs_admin_bundle_form()
 */
function dolist_messages_mail_admin_bundle_form_submit($form, &$form_state) {

  $bundle = new stdClass();

  if(!$form_state['values']['locked']) {
    $bundle->bundle = trim($form_state['values']['bundle']);
  } else {
    $bundle->bundle = $form['#messages_bundle']->bundle;
  }

  $bundle->locked = 1;

  $bundle->name = trim($form_state['values']['name']);

  $variables = $form_state['values'];

  // Remove everything that's been saved already - whatever's left is assumed
  // to be a persistent variable.
  foreach ($variables as $key => $value) {
    if (isset($bundle->$key)) {
      unset($variables[$key]);
    }
  }

  unset($variables['form_token'], $variables['op'], $variables['submit'], $variables['delete'], $variables['reset'], $variables['form_id'], $variables['form_build_id']);


  $status = dolist_messages_mail_bundle_save($bundle);

  $t_args = array('%name' => $bundle->name);

  if ($status == SAVED_UPDATED) {
    drupal_set_message(t('The message bundle %name has been updated.', $t_args));
  }
  elseif ($status == SAVED_NEW) {
    drupal_set_message(t('The message bundle %name has been added.', $t_args));
    watchdog('node', 'Added message bundle %name.', $t_args, WATCHDOG_NOTICE, l(t('view'), 'admin/structure/dolist/messagesmail'));
  }

  $form_state['redirect'] = 'admin/structure/dolist/messagesmail';
  return;
}

function dolist_messages_mail_admin_import_model_form($form,&$form_state){
   /*$form=array();
  $form['import_model_id'] = array(
    '#type' => 'textfield',
    '#title' => 'Model Id',
    '#description' => 'Saisissez l Id du modèle de message à importer',
    '#default_value' => '',
    '#size' => 100,
    '#maxlength' => 128,
    '#required' => TRUE,
  );
   $form['#submit'][] = 'import_model_dolist';
  return system_settings_form($form);*/
   $bundle = new stdClass();
    $bundle->name = 'creation_prog';
    $bundle->bundle = 'bundle_creation_prog';
    $bundle->locked = 0;



  $status = dolist_messages_mail_bundle_save($bundle);
$field = array(
        'field_name' => 'field_creation_prog',
        'type' => 'text',
    );
    field_create_field($field);


$instance = array(
    'field_name' => 'field_creation_prog',
    'required' => 'true', 
    'label' => 'Field créé programmatiquement',
    'description' => st('field créé programmatiquement'),
    'entity_type' => 'messages_item',
    'bundle' => 'bundle_creation_prog',
    'display' => array(
  'teaser' => array(
    'label' => 'hidden',
    'type' => 'hidden',
  ),
  'default' => array(
    'label' => 'hidden',
    'type' => 'hidden',
  ),

),

);
field_create_instance($instance);

  $t_args = array('%name' => $bundle->name);

  if ($status == SAVED_UPDATED) {
    drupal_set_message(t('The paragraph bundle %name has been updated.', $t_args));
  }
  elseif ($status == SAVED_NEW) {
    drupal_set_message(t('The paragraph bundle %name has been added.', $t_args));
    watchdog('node', 'Added paragraph bundle %name.', $t_args, WATCHDOG_NOTICE, l(t('view'), 'admin/structure/paragraphs'));
  }

  return;
}


function import_model_dolist($form,&$form_state){
  //TODO : domdocument import et création bundle
  $idmodel=$form_state['values']['import_model_id'];
 
   $api=new dolistAPI(variable_get('global_settings_api_key'),
  variable_get('global_settings_account_id'));
  $result=$api->getMessages($idmodel);
   watchdog('import','Id : @id',array('@id' => $result));
   $doc = new DOMDocument();
    $doc->loadHTML($result);
    $books = $doc->getElementsByTagName('body');
 foreach ($books as $book) {
      watchdog('import','Id : @id',array('@id' => $book->nodeValue));
}
   
}


 function dolist_messages_mail_admin_bundle_instantate_form($form,&$form_state,$bundle){
 $instance = new stdClass();
  $instance->bundle = $bundle->name;
  // l'object book lui même
  $form['instance'] = array(
    '#type' => 'value',
    '#value' => $bundle
  );

   $form['bundle'] = array(
    '#type' => 'value',
    '#value' => $instance->bundle
  );

  field_attach_form('messages_item',$bundle, $form, $form_state);
  
  $form['actions'] = array('#type' => 'actions');
  $form['actions']['add'] = array(
    '#type' => 'submit',
    '#value' => 'add'
  );
  return $form;

 }
/**
 * Validation des attached fields
 */
function dolist_messages_mail_admin_bundle_instantate_form_validate($form, &$form_state) {
  drupal_set_message(t('Test rules actions'));
  entity_form_field_validate('messages_item', $form, $form_state);
}

function dolist_messages_mail_admin_bundle_instantate_form_submit($form, &$form_state) {
 $bundles = new stdClass();
 $bundles = $form_state['values']['instance'];
 $bundles->bundle = $form_state['values']['bundle'];
  
  // Enregistrement dans la base "books"
  drupal_write_record('messages_item', $bundles);
  // L'objet est "rempli" avec les propriétés issues de form_state
  entity_form_submit_build_entity('messages_item', $bundles, $form, $form_state);
  // Laissons aussi une chance à d'autres modules d'intervenir sur les Fields attachés.
  field_attach_submit('messages_item', $bundles, $form, $form_state);
  // On insere les données des fields dans la base de données.
  field_attach_insert('messages_item', $bundles);
  // Et un petit message de confirmation.
  drupal_set_message( 
    t('new @message got added' ,
    array('@message' => $bundles->bundle))
  );
  $form_state['redirect'] = 'admin/structure/dolist/messagesmail';
  return;
}
/**
 * Menu callback; delete a single paragraph bundle
 *
 * @ingroup forms
 */
function dolist_messages_mail_admin_bundle_delete_form($form, &$form_state, $bundle) {
      
  if(!$bundle) {
    drupal_set_message(t('Could not load bundle'), 'error');
    drupal_goto('admin/structure/dolist/messagesmail');
  }

  $form['type'] = array('#type' => 'value', '#value' => $bundle->bundle);
  $form['name'] = array('#type' => 'value', '#value' => $bundle->name);

  $message = t('Are you sure you want to delete the message bundle %bundle?', array('%bundle' => $bundle->name));
  $caption = '';
  $caption .= '<p>' . t('This action cannot be undone. Content using the bundle will be broken.') . '</p>';

  return confirm_form($form, $message, 'admin/structure/dolist/messagesmail', $caption, t('Delete'));
}

/**
 * Process paragraph bundle delete confirm submissions.
 *
 * @see paragraphs_admin_bundle_delete_form()
 */
function dolist_messages_mail_admin_bundle_delete_form_submit($form, &$form_state) {
  dolist_messages_mail_bundle_delete($form_state['values']['type']);

  $t_args = array('%name' => $form_state['values']['name']);
  drupal_set_message(t('The message bundle %name has been deleted.', $t_args));
  watchdog('node', 'Deleted message bundle %name.', $t_args, WATCHDOG_NOTICE);


  $form_state['redirect'] = 'admin/structure/dolist/messagesmail';
  return;
}
