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
        'label' => t('Replacements'),
        'description' => t('The new value to set for the specified field'),
        'allow_null'=> FALSE,
      ),
      'field' => array(
        'type' => 'text',
        'label' => t('Replacements'),
        'description' => t('The new value to set for the specified field'),
        'options list' => 'rules_fields_dolist_options_list',
      ),
    ),
  ),
);
return $actions;
}
 

 
 function dolist_creation($email){

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
  $authenticationInfos = array('AuthenticationKey' => 'yt6nVpjU8fmmiNVq3hI9Ad9pJ+IXtTXvUpWcjbJmpvbw8afiXKIWf6AGsvj7GbgpozidySYBUUZ36dGX0WSS8A==','AccountID' => 4930);
  $authenticationRequest = array('authenticationRequest' => $authenticationInfos);
 
  // Demande du jeton d'authentification
  $result = $client->GetAuthenticationToken($authenticationRequest);
 
 

 
  if (!is_null($result->GetAuthenticationTokenResult) and $result->GetAuthenticationTokenResult != '') {
  if ($result->GetAuthenticationTokenResult->Key != '') {
  /** Si le token existe on affiche ses informations **/
 

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
 

 
 

  $fields[] = array(
  'Name' => 'firstname',
  'Value' => 'florian');
 
  $interests[] = array();
  $e=trim($email);
  
  $contact = array(
  'Email' => $email,
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
 
  //r ecuperation de rsultat de l'opération (peut ne pas être disponible de suite)
  $resultContact = $clientContact->GetStatusByTicket($contactRequest);
 
  var_dump($resultContact->GetStatusByTicketResult);
  }
  else
  {
    drupal_set_message(t('Erreur Mise à jour'));
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



function rules_fields_dolist_options_list() {
  ini_set("soap.wsdl_cache_enabled", "0");
  ini_set("default_socket_timeout", 480);  
  
  // Url du contrat wsdl
  $proxywsdl = "http://api.dolist.net/V2/AuthenticationService.svc?wsdl";
  $location = "http://api.dolist.net/V2/AuthenticationService.svc/soap1.1";
  
  // Génération du proxy
  $client = new SoapClient($proxywsdl, array('trace' => 1, 'location' => $location));            

  // Renseigner la clé d'authentification avec l'identifiant client
  $authenticationInfos  = array('AuthenticationKey' => variable_get('dolist_api'),'AccountID' => variable_get('dolist_id'));
  $authenticationRequest  = array('authenticationRequest' => $authenticationInfos);

  // Demande du jeton d'authentification
  $result = $client->GetAuthenticationToken($authenticationRequest);
  
  if (!is_null($result->GetAuthenticationTokenResult) and $result->GetAuthenticationTokenResult != '') {
    if ($result->GetAuthenticationTokenResult->Key != '') {
      /******************************************/
      /* LECTURE  DES CONTACTS */
      /******************************************/
      
      // Url du contrat wsdl
      $proxywsdlContact = "http://api.dolist.net/V2/ContactManagementService.svc?wsdl";
      $locationContact = "http://api.dolist.net/V2/ContactManagementService.svc/soap1.1";
      // Génération du proxy
      $clientContact = new SoapClient($proxywsdlContact, array('trace' => 1, 'location' => $locationContact));
      // Création du jeton
      $token = array(
        'AccountID' => variable_get('dolist_id'),
        'Key' => $result->GetAuthenticationTokenResult->Key
      );
      //La list des champs d'un contact à retourner.
      $fields[] = array('firstname', 'lastname');
      //Les critères de recherche des contacts
      $contactFilter = array(
        'Email' => variable_get('dolist_email'));
              
    
      $contactRequest = array(
        
        'Offset' => 0, //Optionnel: L'indice du 1er contact retourné. 
        'AllFields' => true, //Indique si on doit retourner tous les champs
        'Interest' => true, //Indique si les interets déclarés sont retourné par la requete
        'LastModifiedOnly' => false, //Indique si la requete doit retourner uniquement les derniers contacts modifiés
        'RequestFilter' => $contactFilter); //Optionnel   
      
      $request = array(
        'token'=> $token,
        'request'=> $contactRequest
      );
      
      // Récupération de tous les contacts
      $result = $clientContact->GetContact($request);

      if (!is_null($result->GetContactResult) and $result->GetContactResult != '')
      {
        $contacts = $result->GetContactResult->ContactList;
        $contacts = $result->GetContactResult->ContactList->ContactData->CustomFields->CustomField;
          
   // ICI ON AFFICHE LES CHAMPS DE LA FICHE CONTACT (MEME LES CHAMPS NON REMPLIS) SEULEMENT SI LE 
          $array=array();
          foreach($contacts as $valeur)
          {
         $array[$valeur->Name]=$valeur->Name; 
          }
      
      }
      else
      {
        echo "Aucun contact n'a été trouvé";
      }     
      
      
      /******************************************/
      
      
  }
    else {
    
    }
  }
  else 
  {
    echo "Le token d'authentification est null.";
  }
  return $array;
}

function dolist_update($email,$field,$replacement){
      drupal_set_message(t('Nombre total de contacts @r',
      array('@r' => $field)));
      drupal_set_message(t('Nombre total de contacts @r',
      array('@r' => $email)));
      drupal_set_message(t('Nombre total de contacts @r',
      array('@r' => $replacement)));
}

  
 
?>