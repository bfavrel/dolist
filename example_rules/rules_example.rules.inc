<?php 

function rules_example_rules_action_info() {
	$actions = array (
		'rules_example_actions_test' => array(
		'label' => t('Test Rules Actions'),
		'group' => t('Test Rules'),
	),
			
      	'rules_example_actions_test_user' => array(
		'label' => t('Test Action With Parameter'),
      	'group' => t('Test Rules'),
      	'parameter' => array(
      		'account' => array(
      			'type' => 'user',
      			'label' => t('login'),
      	),	
      ),
	 ),
	);
		return $actions;
}

function rules_example_actions_test(){
	drupal_set_message(t('Test rules actions'));
}


function rules_example_actions_test_user($account){
	drupal_set_message(t('Test rules with parameter with @username',
	array('@username' => $account->name)));
}


?>