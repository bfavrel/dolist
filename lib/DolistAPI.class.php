<?php
//Définition des fonctions qui seront utilisées à plusieurs reprises dans le module
class DolistAPI{
//Url du contrat wsdl Authentifictation
var $proxywsdl="http://api.dolist.net/V2/AuthenticationService.svc?wsdl";
//Url soap Authentification
var $location="http://api.dolist.net/V2/AuthenticationService.svc/soap1.1";

//Url du contrat wsdl ContactManagement
var $proxywsdlContact = "http://api.dolist.net/V2/ContactManagementService.svc?wsdl";
//Url soap Authentification Management
var $locationContact = "http://api.dolist.net/V2/ContactManagementService.svc/soap1.1";
      
//La clé API 
var $apikey;
//L'identifiant du compte
var $account;
var $email;


function DolistAPI($apikey,$account){
$this->apikey=$apikey;
$this->account=$account;
}

function DolistCreation($email){
  try
    {
  // Génération du proxy
  $client = new SoapClient($this->proxywsdl, array('trace' => 1, 'location' => $this->location));
 
  // Renseigner la clé d'authentification avec l'identifiant client
  $authenticationInfos = array('AuthenticationKey' => $this->apikey,'AccountID' => $this->account);
  $authenticationRequest = array('authenticationRequest' => $authenticationInfos);
 
  // Demande du jeton d'authentification
  $result = $client->GetAuthenticationToken($authenticationRequest);
 
    if (!is_null($result->GetAuthenticationTokenResult) and $result->GetAuthenticationTokenResult != '') {
      if ($result->GetAuthenticationTokenResult->Key != '') {
  /** Si le token existe on affiche ses informations **/
 

  /** ON CREE UN CONTACT **/
  // Génération du proxy
  $clientContact = new SoapClient($this->proxywsdlContact, array('trace' => 1, 'location' => $this->locationContact));
 
  // Création du jeton
  $token = array(
  'AccountID' => $this->account,
  'Key' => $result->GetAuthenticationTokenResult->Key
  );
  // Dans la fonction de création, on ne crée un contact qu'avec l'email , champ obligatoire. 
  //Tous les autres champs sont vides.
  $fields[] = array(
  'Name' => null,
  'Value' => null);
 
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
  //récuperation de résultat de l'opération (peut ne pas être disponible de suite)
  $resultContact = $clientContact->GetStatusByTicket($contactRequest);
  var_dump($resultContact->GetStatusByTicketResult);
  }
  else
  {
    watchdog('apicreation','erreur update');
  } 
}
  else {
    watchdog('apicreation','erreur token authentification');
  
  }
}

  else
  {
    watchdog('apicreation','le token est null');
  }
}
  //Gestion d'erreur
  catch(SoapFault $fault)
  {
    watchdog('apicreation','Erreur Soap');
  }
}

function DolistUpdate($email,$field,$replacement,$abomail,$abosms){
 try
  {
  // Génération du proxy
  $client = new SoapClient($this->proxywsdl, array('trace' => 1, 'location' => $this->location));
 
  // Renseigner la clé d'authentification avec l'identifiant client
  $authenticationInfos = array('AuthenticationKey' => $this->apikey,'AccountID' => $this->account);
  $authenticationRequest = array('authenticationRequest' => $authenticationInfos);
 
  // Demande du jeton d'authentification
  $result = $client->GetAuthenticationToken($authenticationRequest);
 
  if (!is_null($result->GetAuthenticationTokenResult) and $result->GetAuthenticationTokenResult != '') {
  if ($result->GetAuthenticationTokenResult->Key != '') {
  /** Si le token existe on affiche ses informations **/
  /** ON CREE UN CONTACT **/
  // Génération du proxy
  $clientContact = new SoapClient($this->proxywsdlContact, array('trace' => 1, 'location' => $this->locationContact));
 
  // Création du jeton
  $token = array(
  'AccountID' => $this->account,
  'Key' => $result->GetAuthenticationTokenResult->Key
  );
 
  $fields[] = array(
  'Name' => $replacement,
  'Value' => $field);
 
  $interests[] = array();
  $e=trim($email);
  // Abonne ou désabonne le contact suivant la valeur de la variable
  if($abomail == 'oui')
    $abomail = '0';
  else if ($abomail == 'non') {
    $abomail = '1';
  }  

  if($abosms == 'oui')
    $abosms = '0';
  else if ($abosms == 'non') {
    $abosms = '1';
  }
  $contact = array(
  'Email' => $email,
  'Fields' => $fields,
  'InterestsToAdd' => $interests, //la liste des identifiants des interets déclarés à associer au contact
  'InterestsToDelete' => $interests, //la liste des identifiants des interets déclarés à supprimer sur le contact
  'OptoutEmail' => $abomail, //0: inscription, 1:désinscription
  'OptoutMobile'=> $abosms //0: inscription, 1:désinscription
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
    watchdog('dolist_maj','Erreur update');
  }
  }
  else {
    watchdog('dolist_maj','Problème sur le token authentification'); 
  
  }
  }
  else
  {
    watchdog('dolist_maj','Le token est null'); 
    }
  }
  //Gestion d'erreur
  catch(SoapFault $fault)
  {
    watchdog('dolist_maj','Erreur');
  }    
}

function DolistListFields() {
  // Génération du proxy
  $client = new SoapClient($this->proxywsdl, array('trace' => 1, 'location' => $this->location));            

  // Renseigner la clé d'authentification avec l'identifiant client
  $authenticationInfos  = array('AuthenticationKey' => $this->apikey,'AccountID' => $this->account);
  $authenticationRequest  = array('authenticationRequest' => $authenticationInfos);

  // Demande du jeton d'authentification
  $result = $client->GetAuthenticationToken($authenticationRequest);
  
  if (!is_null($result->GetAuthenticationTokenResult) and $result->GetAuthenticationTokenResult != '') {
    if ($result->GetAuthenticationTokenResult->Key != '') {
      /******************************************/
      /* LECTURE  DES CONTACTS */
      /******************************************/
      
            // Génération du proxy
      $clientContact = new SoapClient($this->proxywsdlContact, array('trace' => 1, 'location' => $this->locationContact));
      // Création du jeton
      $token = array(
        'AccountID' => $this->account,
        'Key' => $result->GetAuthenticationTokenResult->Key
      );
      //Les critères de recherche des contacts
      $contactFilter = array(
        'Email' => variable_get('email'));
              
    
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
        watchdog('dolist_create','Aucun contact trouvé');
      }     
      
      
      /******************************************/
      
      
  }
    else {
    
    }
  }
  else 
  {
    watchdog('dolist_create','Le token est null');
  }
  return $array;
}

// Abonner ou désabonner un contact. 
// $abomail pour abonnement mail, et $abosms pour abonnement sms
function DolistAboDesabo ($email,$abomail,$abosms){
try
  {
  // Génération du proxy
  $client = new SoapClient($this->proxywsdl, array('trace' => 1, 'location' => $this->location));
 
  // Renseigner la clé d'authentification avec l'identifiant client
  $authenticationInfos = array('AuthenticationKey' => $this->apikey,'AccountID' => $this->account);
  $authenticationRequest = array('authenticationRequest' => $authenticationInfos);
 
  // Demande du jeton d'authentification
  $result = $client->GetAuthenticationToken($authenticationRequest);
 
  if (!is_null($result->GetAuthenticationTokenResult) and $result->GetAuthenticationTokenResult != '') {
  if ($result->GetAuthenticationTokenResult->Key != '') {
  /** Si le token existe on affiche ses informations **/
  /** ON CREE UN CONTACT **/
  // Génération du proxy
  $clientContact = new SoapClient($this->proxywsdlContact, array('trace' => 1, 'location' => $this->locationContact));
 
  // Création du jeton
  $token = array(
  'AccountID' => $this->account,
  'Key' => $result->GetAuthenticationTokenResult->Key
  );
 
  $fields[] = array(
  'Name' =>  '',
  'Value' => '');
 
  $interests[] = array();
  $e=trim($email);
  // Abonne ou désabonne le contact suivant la valeur de la variable
  if($abomail == 'oui')
    $abomail = '0';
  else if ($abomail == 'non') {
    $abomail = '1';
  }  

  if($abosms == 'oui')
    $abosms = '0';
  else if ($abosms == 'non') {
    $abosms = '1';
  }
  $contact = array(
  'Email' => $email,
  'Fields' => $fields,
  'InterestsToAdd' => $interests, //la liste des identifiants des interets déclarés à associer au contact
  'InterestsToDelete' => $interests, //la liste des identifiants des interets déclarés à supprimer sur le contact
  'OptoutEmail' => $abomail, //0: inscription, 1:désinscription
  'OptoutMobile'=> $abosms //0: inscription, 1:désinscription
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
    watchdog('dolist_maj','Erreur update');
  }
  }
  else {
    watchdog('dolist_maj','Problème sur le token authentification'); 
  
  }
  }
  else
  {
    watchdog('dolist_maj','Le token est null'); 
    }
  }
  //Gestion d'erreur
  catch(SoapFault $fault)
  {
    watchdog('dolist_maj','Erreur');
  }    

}

}?>