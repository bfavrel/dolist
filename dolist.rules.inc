<?php 

function dolist_rules_action_info(){
	$actions = array (
		'dolist_actions' => array(
			'label' => t('Action Dolist'),
			'group' => t('Dolist'),
			'parameter' => array(
				'email' => array(
					'type' => 'text',
					'label' => t('Email'),	
						),
				'name' => array (
					'type' => 'text',
					'label' => t('Name'),
				),
				'firstname' => array (
					'type' => 'text',
					'label' => t('Firstname'),
					),
				'societe' => array (
					'type' => 'text',
					'label' => t('Société'),
					),
				'adresse' => array (
					'type' => 'text',
					'label' => t('Adresse'),
					),
				'codepostal' => array (
					'type' => 'text',
					'label' => t('Code Postal'),
					),
				'ville' => array (
					'type' => 'text',
					'label' => t('Ville'),
					),
				'pays' => array (
					'type' => 'text',
					'label' => t('Pays'),
					),
				'telephone' => array (
					'type' => 'text',
					'label' => t('Téléphone'),
					),
				'mobile' => array (
					'type' => 'text',
					'label' => t('Mobile'),
					),
			
				),
		),
		
);
	return $actions;
}
 function dolist_actions($email,$name,$firstname,$societe,$adresse,$codepostal,$ville,$pays,$telephone,$mobile){
 	
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
 	
 		

 	
 		if (!is_null($result->GetAuthenticationTokenResult) and $result->GetAuthenticationTokenResult != '') {
 			if ($result->GetAuthenticationTokenResult->Key != '') {
 				/** Si le token existe on affiche ses informations **/
 				drupal_set_message(t('Informations concernant le token  d authentification : @date ',
 		array('@date' => $result->GetAuthenticationTokenResult->DeprecatedDate)));

 			/** ON CREE UN CONTACT **/
 					
 				// Url du contrat wsdl
 				$proxywsdlContact = "http://api.dolist.net/V2/ContactManagementService.svc?wsdl";
 				$locationContact = "http://api.dolist.net/V2/ContactManagementService.svc/soap1.1";
 					
 				// Génération du proxy
 				$clientContact = new SoapClient($proxywsdlContact, array('trace' => 1, 'location' => $locationContact));
 				
 				// Création du jeton
 				$token = array(
 						'AccountID' => 4930,
 						'Key' => $result->GetAuthenticationTokenResult->Key
 				);
 					

 				drupal_set_message(t('email est @email',
 				array('@email' => $email)));
 				drupal_set_message(t('name est @name',
 				array('@name' => $name)));
 				drupal_set_message(t('firstname est @firstname',
 				array('@firstname' => $firstname)));
 				drupal_set_message(t('societe est @societe',
 				array('@societe' => $societe)));
 				drupal_set_message(t('adresse est @adresse',
 				array('@adresse' => $adresse)));
 				drupal_set_message(t('codepostal est @codepostal',
 				array('@codepostal' => $codepostal)));
 				drupal_set_message(t('ville est @ville',
 				array('@ville' => $ville)));
 				drupal_set_message(t('pays est @pays',
 				array('@pays' => $pays)));
 				drupal_set_message(t('telephone est @telephone',
 				array('@telephone' => $telephone)));
 				drupal_set_message(t('mobile est @mobile',
 				array('@mobile' => $mobile)));
 				
 				$fields[] = array(
 						'Name' => 'firstname',
 						'Value' => 'florian');
 				$fields[] = array(
 						'Name' => 'lastname',
 						'Value' => 'castandet');
 					
 				$interests[] = array();
 					
 				$contact = array(
 						'Email' => 'flo.dorient@hotmail.fr',
 						'Fields' => $fields,
 						'InterestsToAdd' => $interests, //la liste des identifiants des interets déclarés à associer au contact
 						'InterestsToDelete' => $interests, //la liste des identifiants des interets déclarés à supprimer sur le contact
 						'OptoutEmail' => 0, //0: inscription, 1:désinscription
 						'OptoutMobile'=> 0 //0: inscription, 1:désinscription
 				);
 					
 				
 				$contactRequest = array(
 						'token'=> $token,
 						'contact'=> $contact
 				);
 					
 				// Enregistrement du contact
 				$result = $clientContact->SaveContact($contactRequest);
 				
 				if (!is_null($result->SaveContactResult) and $result->SaveContactResult != '')
 				{
 					$ticket = $result->SaveContactResult;
 						
 					$contactRequest = array(
 							'token'=> $token,
 							'ticket'=> $ticket
 					);
 				
 					//recuperation de rsultat de l'opération (peut ne pas être disponible de suite)
 					$resultContact = $clientContact->GetStatusByTicket($contactRequest);
 				
 					var_dump($resultContact->GetStatusByTicketResult);
 				}
 				else
 				{
 					echo "Erreur sur la mise à jour du contact";
 				}
		}
 			
 			else {
 				drupal_set_message(t('Problème sur le token d authentification'));
 				
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