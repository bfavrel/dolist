<?php 

function dolist_rules_action_info(){
	$actions = array (
		'dolist_actions' => array(
			'label' => t('Action Dolist'),
			'group' => t('Dolist'),
			),
);
	return $actions;
}
 
 
 function dolist_actions(){
 	try
 	{
 		ini_set("soap.wsdl_cache_enabled", "0");
 		ini_set("default_socket_timeout", 480);
 	
 	
 		// Url du contrat wsdl
 		$proxywsdl = "http://api.dolist.net/V2/AuthenticationService.svc?wsdl";
 		$location = "http://api.dolist.net/V2/AuthenticationService.svc/soap1.1";
 	
 		// Génération du proxy
 		$client = new SoapClient($proxywsdl, array('trace' => 1, 'location' => $location));
 	
 		// Renseigner la clé d'authentification avec l'identifiant client
 		$authenticationInfos	= array('AuthenticationKey' => '7PI2bqJaQcr7XnoG5NGAnXUJDfGis+8+dtdf+lAfXZSGPebEqTTLpz2sP4GodFcbJg6MtKSpwnpJNghu1FBU2g==','AccountID' => 4930);
 		$authenticationRequest	= array('authenticationRequest' => $authenticationInfos);
 	
 		// Demande du jeton d'authentification
 		$result = $client->GetAuthenticationToken($authenticationRequest);
 	
 		drupal_set_message(t('Authentification OK'));

 	
 		if (!is_null($result->GetAuthenticationTokenResult) and $result->GetAuthenticationTokenResult != '') {
 			if ($result->GetAuthenticationTokenResult->Key != '') {
 				drupal_set_message(t('Informations concernant le token  d authentification : @date ',
 		array('@date' => $result->GetAuthenticationTokenResult->DeprecatedDate)));
 				
 				
 			    
 				/*
 				print "<pre>";
 				print_r($result->GetAuthenticationTokenResult);
 			*/}
 			
 			else {
 				drupal_set_message(t('Problème sur le token d authentification'));
 				// Afficher le token
 				/*
 				print "<pre>";
 				print_r($result->AuthentificationTokenResponse);*/
 			}
 		}
 		else
 		{
 			drupal_set_message(t('Le token est null'));
 		}
 	}
 	//Gestion d'erreur
 	catch(SoapFault $fault)
 	{
 		drupal_set_message(t('Erreur'));
 	}
 }

?>