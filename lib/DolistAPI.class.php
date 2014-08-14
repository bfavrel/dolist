<?php
/**
 * \file         DolistAPI.class.php
 * \author    fcastand    
 * \version   1.0
 * \date      21 mai 2014
 * \brief     Définit la librairie Dolist
 *
 * \details   Définit toutes les fonction réutilisables du module. Ces fonctions permettent l'utilisation 
 *            et la manipulation de l'ensemble des fonctionnalités offert par l'API Dolist en ligne
 */

//Définition des fonctions qui seront utilisées à plusieurs reprises dans le module
class dolistAPI{
//La clé API 
private $apikey;
//L'identifiant du compte
private $account;
//COntrat Authentication Service
private $proxywsdl="http://api.dolist.net/V2/AuthenticationService.svc?wsdl";
private $location="http://api.dolist.net/V2/AuthenticationService.svc/soap1.1";
//COntrat COntact Management Service
private $proxywsdlContact = "http://api.dolist.net/V2/ContactManagementService.svc?wsdl";
private $locationContact = "http://api.dolist.net/V2/ContactManagementService.svc/soap1.1";
//Contrat Segment Service
private $proxywsdlSeg = "http://api.dolist.net/V2/SegmentService.svc?wsdl";
private $locationSeg = "http://api.dolist.net/V2/SegmentService.svc/soap1.1";    
//Contrat Messages Service
private $proxywsdlMessage = "http://api.dolist.net/V2/MessageService.svc?wsdl";
private $locationMessage = "http://api.dolist.net/V2/MessageService.svc/soap1.1";
//Contrat Campagne Service
private $proxywsdlCampaign = "http://api.dolist.net/V2/CampaignManagementService.svc?wsdl";
private $locationCampaign = "http://api.dolist.net/V2/CampaignManagementService.svc/soap1.1";
//Contrat Stats Service 
private $proxywsdlStatsCampaignMail = "http://api.dolist.net/V2/StatisticsService.svc?wsdl";
private $locationStatsCampaignMail =  "http://api.dolist.net/V2/StatisticsService.svc/soap1.1";
//Contrat MessageService
private $proxywsdlSms = "http://api.dolist.net/V2/SMSService.svc?wsdl";
private $locationSms = "http://api.dolist.net/V2/SMSService.svc/soap1.1";
//Contrat FieldManagementService
private $proxywsdlfield="http://api.dolist.net/CustomFieldManagementService.svc?wsdl";
private $locationfield = "http://api.dolist.net/CustomFieldManagementService.svc/soap1.1";



/**
 * \brief      Définit un nouvel objet API
 * \details    Définit les paramètres obligatoire pour toute manipulation de l'API Dolist.
 * \param    apikey         Clé Api de l'utilisateur
 * \param    account         Identifiant du compte de l'utilisateur
 * \return    Un objet de type dolistAPI
 */
function dolistAPI($apikey,$account){
$this->apikey=$apikey;
$this->account=$account;
}

// On génère le client Soap puis on renvoie le token d'authentification
private function authenticationService(){
// Génération du proxy
$client = new SoapClient($this->proxywsdl, array('trace' => 1, 'location' => $this->location));
// Renseigner la clé d'authentification avec l'identifiant client
$authenticationInfos = array('AuthenticationKey' => $this->apikey,'AccountID' => $this->account);
$authenticationRequest = array('authenticationRequest' => $authenticationInfos);
// Demande du jeton d'authentification
$result = $client->GetAuthenticationToken($authenticationRequest);
return $result;
}

// On définit le client Soap pour les fonctions sur les contacts
private function soapClientContactService(){
 // Génération du proxy  
  $clientContact = new SoapClient($this->proxywsdlContact, array('trace' => 1, 'location' => $this->locationContact));
return $clientContact;
}

// On définit le jeton d'authentification pour manipuler les fonctions sur les contacts.
private function jetonContactService(){
  $result=$this->authenticationService();
  $token = array(
  'AccountID' => $this->account,
  'Key' => $result->GetAuthenticationTokenResult->Key
  );
  return $token; 
}

// ON définit le client Soap et le jeton d'authentification pour la partie segment de l'Api
private function authenticationSegment(){
 // Génération du proxy
  $client_auth = new SoapClient($this->proxywsdl, array('trace' => 1, 'location' => $this->location));
  $request_auth = array(
    'authenticationRequest' => array(
      'AuthenticationKey' => $this->apikey,
      'AccountID' => $this->account
    )
  );
// Demande d'authentification
  $result_auth = $client_auth->GetAuthenticationToken($request_auth);
  return $result_auth;
}

// On définit le client soap pour les fonctions sur les segments
private function soapClientSegmentService(){
// Génération du proxy
$client = new SoapClient($this->proxywsdlSeg, array('trace' => 1, 'location' => $this->locationSeg));
return $client;
}

// On définit le jeton d'authentification pour manipuler les fonctions sur les segments
private function jetonSegmentService(){
  $result_auth=$this->authenticationSegment();
  // Création du jeton
      $token = array(
        'AccountID' => $this->account,
        'Key' => $result_auth->GetAuthenticationTokenResult->Key
      );
    return $token;
}

// On définit le client Soap pour les fonctions sur les messages
private function soapMessageService(){
  // Génération du proxy
      $client = new SoapClient($this->proxywsdlMessage, array('trace' => 1, 'location' => $this->locationMessage));
      return $client;  
}

//On définit le jeton d'authentification pour manipuler les fonctions sur les messages
private function jetonMessageService(){
  $result_auth=$this->authenticationSegment();
  // Création du jeton
    $token = array(
        'AccountID' => $this->account,
        'Key' => $result_auth->GetAuthenticationTokenResult->Key
      );
      return $token;
}

// On définit le client Soap pour les fonctions sur les campagnes
private function soapCampaignService(){
     // Génération du proxy  array('soap_version' => SOAP_1_2
      $client = new SoapClient($this->proxywsdlCampaign, array('trace' => 1, 'location' => $this->locationCampaign));
      return $client;
}

//On définit le jeton d'authentification pour manipuler les fonctions sur les campagnes
private function jetonCampaignService(){
  $result_auth=$this->authenticationSegment();
  // Création du jeton
      $token = array(
        'AccountID' => $this->account,
        'Key' => $result_auth->GetAuthenticationTokenResult->Key
      );
      return $token;
}

// On définit le client Soap pour la fonction de stat de campagne mail
private function soapStatsCampaignMail(){
    // Génération du proxy
      $client = new SoapClient($this->proxywsdlStatsCampaignMail, array('trace' => 1, 'location' => $this->locationStatsCampaignMail));
      return $client;
}

//On définit le jeton d'authentification pour manipuler la fonction de stat
private function jetonStatsCampaignMail(){

  $result_auth=$this->authenticationSegment();
  // Création du jeton
  $token = array(
        'AccountID' => $this->account,
        'Key' => $result_auth->GetAuthenticationTokenResult->Key
      );
      return $token;
}

private function soapSmsService(){
  // Génération du proxy
      $client = new SoapClient($this->proxywsdlSms, array('trace' => 1, 'location' => $this->locationSms));
      return $client;
}

private function jetonSmsService(){
$result_auth=$this->authenticationSegment();

  // Création du jeton
      $token = array(
        'AccountID' => $this->account,
        'Key' => $result_auth->GetAuthenticationTokenResult->Key
      );
return $token;      
}

private function soapFieldService(){
  // Génération du proxy
      $client = new SoapClient($this->proxywsdlfield, array('trace' => 1, 'location' => $this->locationfield));
      return $client;
}

private function jetonFieldService(){
$result_auth=$this->authenticationSegment();

  // Création du jeton
      $token = array(
        'AccountID' => $this->account,
        'Key' => $result_auth->GetAuthenticationTokenResult->Key
      );
return $token;      
}

/**
 * \brief      Fonction de Création d'un contact Dolist
 * \details    On créé un nouveau contact que l'on ajoute à sa base de contacts Dolist
 * \param    email           L'adresse mail du contact à ajouter
  */
function createContact($email){
  try
    {
   $result=$this->authenticationService();
    if (!is_null($result->GetAuthenticationTokenResult) and $result->GetAuthenticationTokenResult != '') {
      if ($result->GetAuthenticationTokenResult->Key != '') {
  //Si le token existe on affiche ses informations 
    $clientContact=$this->soapClientContactService();
    $token=$this->jetonContactService();
 //ON CREE UN CONTACT 
  // Dans la fonction de création, on ne crée un contact qu'avec l'email , champ obligatoire. 
  //Tous les autres champs sont vides.
  $fields[] = array(
  'Name' => '',
  'Value' => '');
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
    watchdog('creation_contact','erreur update');
  } 
}
  else {
    watchdog('creation_contact','erreur token authentification');
  
  }
}
  else
  {
    watchdog('creation_contact','le token est null');
  }
}
 //Gestion d'erreur
  catch(SoapFault $fault)
  {
  $detail = $fault->detail;
  watchdog('creation_contact','Erreur Soap');
  watchdog('creation_contact','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('creation_contact','Description : @message',array('@message' => $detail->ServiceException->Description));
  }
}

/**
 * \brief      Mise à jour d'un contact Dolist
 * \details    Mise à jour des différents champs de la fiche contact d'un contact existant.
 * \param    email           L'adresse email du contact à modifier
 * \param    field           La valeur de la modification à appliquer
 * \param    replacement     Le champ de la fiche à modifier
 */
function updateContact($email,$field,$replacement){
 try
  {
  $result= $this->authenticationService();
    if (!is_null($result->GetAuthenticationTokenResult) and $result->GetAuthenticationTokenResult != '') {
      if ($result->GetAuthenticationTokenResult->Key != '') {
  /** Si le token existe on affiche ses informations **/
  $clientContact=$this->soapClientContactService();
  $token=$this->jetonContactService(); 
  
  $fields[] = array(
  'Name' => $replacement,
  'Value' => $field);
 
  $interests[] = array();
  $contact = array(
  'Email' => $email,
  'Fields' => $fields,
  'InterestsToAdd' => $interests, //la liste des identifiants des interets déclarés à associer au contact
  'InterestsToDelete' => $interests //la liste des identifiants des interets déclarés à supprimer sur le contact
  //'OptoutEmail' => 0, //0: inscription, 1:désinscription
  //'OptoutMobile'=> 0 //0: inscription, 1:désinscription
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
    watchdog('update_contact','Erreur update');
  }
  }
  else {
    watchdog('update_contact','Problème sur le token authentification'); 
  
  }
  }
  else
  {
    watchdog('update_contact','Le token est null'); 
    }
  }
  //Gestion d'erreur
  catch(SoapFault $fault)
  {
    $detail = $fault->detail;
  watchdog('update_contact','Erreur Soap');
  watchdog('update_contact','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('update_contact','Description : @message',array('@message' => $detail->ServiceException->Description));
  }    
}

/**
 * \brief      Création de la liste des champs Dolist
 * \details    On génère la liste de l'ensemble des champs d'une fiche contact Dolist,
 *              pour pouvoir choisir ceux à modifier selon le cas
 * \return     Une liste de champs
 */
function createListContactFields() {
  $result=$this->authenticationService();
  if (!is_null($result->GetAuthenticationTokenResult) and $result->GetAuthenticationTokenResult != '') {
    if ($result->GetAuthenticationTokenResult->Key != '') {
       $clientContact=$this->soapFieldService();
      $token=$this->jetonFieldService();
      
      $getFieldListRequest = array(
       ); //Optionnel   
      
      $request = array(
        'token'=> $token,
        'request'=> $getFieldListRequest
      );
      
      // Récupération de tous les contacts
      $result = $clientContact->GetFieldList($request);
      if (!is_null($result->GetFieldListResult) and $result->GetFieldListResult != '')
      {
        $contacts = $result->GetFieldListResult->FieldList->Field;  
        
          $array=array();
          foreach($contacts as $valeur)
          {
         $array[$valeur->Name]=$valeur->Name; 
          }
      }
      else
      {
        watchdog('list_contact_fields','Aucun contact trouvé');
      }     
  }
    else {
    
    }
  }
  else 
  {
    watchdog('list_contact_fields','Le token est null');
  }
  return $array;
}

/**
 * \brief      Abonnement d'un contact à la réception d'emails
 * \details    Cette fonction permet l'abonnement d'un contact à la réception d'emails 
 * \param    email           L'adresse email du contact à modifier
 */
function suscribeContactbyMail ($email){
try
  {
  $result=$this->authenticationService();
    if (!is_null($result->GetAuthenticationTokenResult) and $result->GetAuthenticationTokenResult != '') {
  if ($result->GetAuthenticationTokenResult->Key != '') {
  /** Si le token existe on affiche ses informations **/
 $clientContact=$this->soapClientContactService();
 $token=$this->jetonContactService();
 
  $fields[] = array(
  'Name' =>  '',
  'Value' => '');
 
  $interests[] = array();
  $contact = array(
  'Email' => $email,
  'Fields' => $fields,
  'InterestsToAdd' => $interests, //la liste des identifiants des interets déclarés à associer au contact
  'InterestsToDelete' => $interests, //la liste des identifiants des interets déclarés à supprimer sur le contact
  'OptoutEmail' => 0 //0: inscription, 1:désinscription
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
    watchdog('suscribe_contact','Erreur update');
  }
  }
  else {
    watchdog('suscribe_contact','Problème sur le token authentification'); 
  
  }
  }
  else
  {
    watchdog('suscribe_contact','Le token est null'); 
    }
  }
  //Gestion d'erreur
  catch(SoapFault $fault)
  {
    $detail = $fault->detail;
  watchdog('suscribe_contact','Erreur Soap');
  watchdog('suscribe_contact','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('suscribe_contact','Description : @message',array('@message' => $detail->ServiceException->Description));
  }    

}

/**
 * \brief      Désabonnement d'un contact à la réception d'emails
 * \details    Cette fonction permet de désabonner un contact à la réception d'emails 
 * \param    email           L'adresse email du contact à modifier
 */
function unsuscribeContactbyMail ($email){
try
  {
  $result=$this->authenticationService();
    if (!is_null($result->GetAuthenticationTokenResult) and $result->GetAuthenticationTokenResult != '') {
  if ($result->GetAuthenticationTokenResult->Key != '') {
  /** Si le token existe on affiche ses informations **/
 $clientContact=$this->soapClientContactService();
 $token=$this->jetonContactService();
 
  $fields[] = array(
  'Name' =>  '',
  'Value' => '');
 
  $interests[] = array();
  $contact = array(
  'Email' => $email,
  'Fields' => $fields,
  'InterestsToAdd' => $interests, //la liste des identifiants des interets déclarés à associer au contact
  'InterestsToDelete' => $interests, //la liste des identifiants des interets déclarés à supprimer sur le contact
  'OptoutEmail' => 1 //0: inscription, 1:désinscription
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
    watchdog('suscribe_contact','Erreur update');
  }
  }
  else {
    watchdog('suscribe_contact','Problème sur le token authentification'); 
  
  }
  }
  else
  {
    watchdog('suscribe_contact','Le token est null'); 
    }
  }
  //Gestion d'erreur
  catch(SoapFault $fault)
  {
    $detail = $fault->detail;
  watchdog('suscribe_contact','Erreur Soap');
  watchdog('suscribe_contact','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('suscribe_contact','Description : @message',array('@message' => $detail->ServiceException->Description));
  }    

}

/**
 * \brief      Abonnement d'un contact à la réception de sms
 * \details    Cette fonction permet l'abonnement d'un contact à la réception de sms
 * \param    email           L'adresse email du contact à modifier
 */
function suscribeContactbySms ($email){
try
  {
  $result=$this->authenticationService();
    if (!is_null($result->GetAuthenticationTokenResult) and $result->GetAuthenticationTokenResult != '') {
  if ($result->GetAuthenticationTokenResult->Key != '') {
  /** Si le token existe on affiche ses informations **/
 $clientContact=$this->soapClientContactService();
 $token=$this->jetonContactService();
 
  $fields[] = array(
  'Name' =>  '',
  'Value' => '');
 
  $interests[] = array();
  $contact = array(
  'Email' => $email,
  'Fields' => $fields,
  'InterestsToAdd' => $interests, //la liste des identifiants des interets déclarés à associer au contact
  'InterestsToDelete' => $interests, //la liste des identifiants des interets déclarés à supprimer sur le contact
  'OptoutMobile' => 0 //0: inscription, 1:désinscription
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
    watchdog('suscribe_contact','Erreur update');
  }
  }
  else {
    watchdog('suscribe_contact','Problème sur le token authentification'); 
  
  }
  }
  else
  {
    watchdog('suscribe_contact','Le token est null'); 
    }
  }
  //Gestion d'erreur
  catch(SoapFault $fault)
  {
    $detail = $fault->detail;
  watchdog('suscribe_contact','Erreur Soap');
  watchdog('suscribe_contact','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('suscribe_contact','Description : @message',array('@message' => $detail->ServiceException->Description));
  }    

}

/**
 * \brief      Désabonnement d'un contact à la réception de sms
 * \details    Cette fonction permet le désabonnement d'un contact à la réception de sms 
 * \param    email           L'adresse email du contact à modifier
 */
function unsuscribeContactbySms ($email){
try
  {
  $result=$this->authenticationService();
    if (!is_null($result->GetAuthenticationTokenResult) and $result->GetAuthenticationTokenResult != '') {
  if ($result->GetAuthenticationTokenResult->Key != '') {
  /** Si le token existe on affiche ses informations **/
 $clientContact=$this->soapClientContactService();
 $token=$this->jetonContactService();
 
  $fields[] = array(
  'Name' =>  '',
  'Value' => '');
 
  $interests[] = array();
  $contact = array(
  'Email' => $email,
  'Fields' => $fields,
  'InterestsToAdd' => $interests, //la liste des identifiants des interets déclarés à associer au contact
  'InterestsToDelete' => $interests, //la liste des identifiants des interets déclarés à supprimer sur le contact
  'OptoutMobile' => 1 //0: inscription, 1:désinscription
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
    watchdog('suscribe_contact','Erreur update');
  }
  }
  else {
    watchdog('suscribe_contact','Problème sur le token authentification'); 
  
  }
  }
  else
  {
    watchdog('suscribe_contact','Le token est null'); 
    }
  }
  //Gestion d'erreur
  catch(SoapFault $fault)
  {
    $detail = $fault->detail;
  watchdog('suscribe_contact','Erreur Soap');
  watchdog('suscribe_contact','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('suscribe_contact','Description : @message',array('@message' => $detail->ServiceException->Description));
  }    

}

/**
 * \brief      Recherche sur les contacts
 * \details    Cette fonction permet de rechercher un contact dans la liste des contacts pour l'afficher et/ou le modfier.
 * \param      Email        L'email de contact à rechercher 
  */
function getContacts($email){
try 
{
  $result=$this->authenticationService();
  
  if (!is_null($result->GetAuthenticationTokenResult) and $result->GetAuthenticationTokenResult != '') {
    if ($result->GetAuthenticationTokenResult->Key != '') {
      $clientContact=$this->soapClientContactService();
      $token=$this->jetonContactService();
          
      $contactFilter = array(
        'Email' => $email);
      
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
        watchdog('get_contacts','Nombre total correspondants à la requête : @c', array('@c' => $result->GetContactResult->TotalContactsCount));
       
        foreach ($contacts as $value) {
          if($value->Name == 'email')
          watchdog('get_contacts','Nombre total correspondants à la requête : @c', array('@c' => $value->Value)); 
          elseif ($value->Name == 'lastname') 
             watchdog('get_contacts','Nombre total correspondants à la requête : @c', array('@c' => $value->Value)); 
        }

      }
      else
      {
        watchdog('get_contacts','Aucun contact trouvé');
      }     
      
      
      /******************************************/
      
      
      
    }
    else {
    watchdog('get_contacts','problème authentification');
    }
  }
  else 
  {
    watchdog('get_contacts','Le token authentification est nul');
  }
}
//Gestion d'erreur
catch(SoapFault $fault) 
{
  $detail = $fault->detail;
  watchdog('get_contacts','Erreur Soap');
  watchdog('get_contacts','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('get_contacts','Description : @message',array('@message' => $detail->ServiceException->Description));
}

}

function createSegment(){
//A voir plus tard
}

/**
 * \brief      Récupération de tous les segments existants
 * \details    On récupère tous les segments qui ont été créés sur la plateforme Dolist
*/
function getAllSegments(){
try {
  $result_auth=$this->authenticationSegment();
  if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
    $client=$this->soapClientSegmentService();
    $token=$this->jetonSegmentService();
      
      // Création de la requête
      $getSegmentRequest = array(
        'token' => $token
      );
      
      // Récupération de tous les segments
      $result = $client->GetAllSegments($getSegmentRequest);
      if (!is_null($result->GetAllSegmentsResult) and $result->GetAllSegmentsResult != '')
      {
        $segments = $result->GetAllSegmentsResult->Segment;
        watchdog('get_all_segments', 'Une liste  de @count segments', 
        array('@count' => count($segments)));
        watchdog('get_all_segments','Premier segment de la liste');
        $first_segment = $segments[0];
        watchdog('get_all_segments','Id : @id',
        array('@id' => $first_segment->Id));
        watchdog('get_all_segments','CreationDate : @creation',
        array('@creation' => $first_segment->CreationDate));
        watchdog('get_all_segments','ModifiedDate : @modified',
        array('@modified' => $first_segment->ModifiedDate));
        watchdog('get_all_segments','Name : @name',
        array('@name' => $first_segment->Name));
        watchdog('get_all_segments','ContactsCount : @contact',
        array('@contact' => $first_segment->ContactsCount));
        watchdog('get_all_segments','Groups : @group',
        array('@group' => $first_segment->Groups));
        return $segments;     
      }
      else
      {
        watchdog('get_all_segments','Aucun segment n a été trouvé');
      }
    }
  }
}
//Gestion d'erreur
catch(SoapFault $fault) {
  $Detail = $fault->detail;
  $detail = $fault->detail;
  watchdog('get_all_segments','Erreur Soap');
  watchdog('get_all_segments','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('get_all_segments','Description : @message',array('@message' => $detail->ServiceException->Description));
}
}


// TODO modifier le la récupération pour prendre en compte le paramètre (l'id)
/**
 * \brief      Récupération d'un segment par ID
 * \details    On récupère les infos d'un segment en fournissant son ID
 * \param      id         L'ID du segment à récupérer
 */
function getSegmentbyId($id){
try {
  $result_auth=$this->authenticationSegment();
    if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
    $client=$this->soapClientSegmentService();
    $token=$this->jetonSegmentService();
            
      // Création de la requête
      $getSegmentRequest = array(
        'token' => $token,
        'segmentID' => $id
      ); 
      // Récupération d'un segment
      $result = $client->GetSegmentByID($getSegmentRequest);
      if (!is_null($result->GetSegmentByIDResult) and $result->GetSegmentByIDResult != '')
      {
        $segment = $result->GetSegmentByIDResult;
        watchdog('segment_by_id','Id : @id',
        array('@id' => $segment->Id));
        watchdog('segment_by_id','CreationDate : @creation',
        array('@creation' => $segment->CreationDate));
        watchdog('segment_by_id','ModifiedDate : @modified',
        array('@modified' => $segment->ModifiedDate));
        watchdog('segment_by_id','Name : @name',
        array('@name' => $segment->Name));
        watchdog('segment_by_id','ContactsCount : @contact',
        array('@contact' => $segment->ContactsCount));
        }
      else
      {
        watchdog('segment_by_id','Le segment n a pas été trouvé');
      }
    }
  }
}
//Gestion d'erreur
catch(SoapFault $fault) {
  $detail = $fault->detail;
  watchdog('segment_by_id','Erreur Soap');
  watchdog('segment_by_id','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('segment_by_id','Description : @message',array('@message' => $detail->ServiceException->Description));
}
}

// TODO modifier le la récupération pour prendre en compte le paramètre (le nom)
/**
 * \brief      Récupération d'un segment par Nom
 * \details    On récupère les infos d'un segment en fournissant son Nom
 * \param      Name         Le nom du segment à récupérer
 */
function getSegmentbyName($name){
  try {
  $result_auth=$this->authenticationSegment();  
  if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
    $client=$this->soapClientSegmentService();
    $token=$this->jetonSegmentService();       
     
      // Création de la requête
      $getSegmentRequest = array(
        'token' => $token,
        'segmentName' => $name
      );
      // Récupération d'un segment
      $result = $client->GetSegmentByName($getSegmentRequest);
      if (!is_null($result->GetSegmentByNameResult) and $result->GetSegmentByNameResult != '')
      {
        $segment = $result->GetSegmentByNameResult;
        watchdog('segment_by_name','Id : @id',
        array('@id' => $segment->Id));
        watchdog('segment_by_name','CreationDate : @creation',
        array('@creation' => $segment->CreationDate));
        watchdog('segment_by_name','ModifiedDate : @modified',
        array('@modified' => $segment->ModifiedDate));
        watchdog('segment_by_name','Name : @name',
        array('@name' => $segment->Name));
        watchdog('segment_by_name','ContactsCount : @contact',
        array('@contact' => $segment->ContactsCount));
      }
      else
      {
        watchdog('segment_by_name','Le segment n a pas été trouvé');
      }
    }
  }
}
//Gestion d'erreur
catch(SoapFault $fault) {
watchdog('segment_by_name','Error');
$detail = $fault->detail;
  watchdog('segment_by_name','Erreur Soap');
  watchdog('segment_by_name','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('segment_by_name','Description : @message',array('@message' => $detail->ServiceException->Description));
}
}

/**
 * \brief      Récupération d'un Message par son ID
 * \details    On récupère les infos d'un message en fournissant son ID
 * \param      id        L'id du message à récupérer
 */
function getMessages($id)
{
  try {
    $result_auth=$this->authenticationSegment();
  if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
      $client=$this->soapMessageService();
      $token= $this->jetonMessageService();
      
      $GetMessageRequest = array(
        'MessageID' => $id
      );
      
      // Création de la requête
      $message = array(
        'token' => $token,
        'request' => $GetMessageRequest
      );
      
      // Création d'une campagne
            $result = $client->GetMessage($message);
      if (!is_null($result->GetMessageResult) and $result->GetMessageResult != '')
      {
        $response = $result->GetMessageResult;
        $i = 0;
          if ($response->MessagesCount > 0) {
          foreach($response->MessageList as $msg){
            watchdog('messages','Erreur Soap');
            watchdog('messages','Nom : @nom',array('@nom' => $msg->Name));
            watchdog('messages','Html : @html',array('@html' => $msg->ContentHtml));
            watchdog('messages','Encoding : @encoding',array('@encoding' => $msg->Encoding));
            watchdog('messages','Type : @type',array('@type' => $msg->MessageType)); 


          }
        }
        
      }
    }
  }
}
//Gestion d'erreur
catch(SoapFault $fault) {
  $detail = $fault->detail;
  watchdog('messages','Erreur Soap');
  watchdog('messages','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('messages','Description : @message',array('@message' => $detail->ServiceException->Description));
}
}

/**
 * \brief      Création d'un campagne mail
 * \details    On créé une campagne mail.la liste des paramètres sera à rajouter.
 * \param      fap          Prefixe de l'adresse expéditeur
 * \param      fn           Nom de l'expediteur  
 * \param      ra           Adresse de retour
 * \param      rn           Nom de reponse
 * \param      s            Sujet
 * \param      m            Id du message à envoyer
 * \param      td           Tracking domain
 * \param      c            Culture de la campagne
 * \param      f            Type d'affichage des liens techniques
 */
function createCampaignMail($fap,$fn,$ra,$rn,$s,$m,$td,$c,$f){
try {
 $result_auth=$this->authenticationSegment();
  if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
      $client=$this->soapCampaignService();
      $token=$this->jetonCampaignService();
      
      // Création d'une campagne
      $campaignEmail = array(
        'FromAddressPrefix' => $fap, //ex: florian
        'FromName' => $fn, //saisondor-dev
        'ReplyAddress' => $ra,//benoit@saisondor.com
        'ReplyName' => $rn,//saisondor-dev
        'Subject' => $s,
        'Message' => array(
          'Id' => $m,  //0 car nouveau message, sinon identifiant du message (dans ce cas inutile de renseigner les autres champs du message : Name, ContentHtml, ContentText, Encoding et MessageType)
          'Name' => 'bonjour',
          'ContentHtml' => '<html><body><p></p>Lorem ipsum dolor sit amet, consectetur ...</body></html>',
          'ContentText' => '',
          // Choix possible : utf-8, iso-8859-1, iso-8859-2, iso-8859-3, iso-8859-4, iso-8859-5, iso-8859-6, iso-8859-7, iso-8859-8, iso-8859-9, iso-8859-13, iso-8859-15
          'Encoding' => 'iso-8859-15',
          // Choix possible : IncludeEncodedImages, IncludeImageLinks
          'MessageType' => 'IncludeImageLinks'
        ),
        'TrackingDomain' => $td,//dev.saisondor.com
        'Culture' => $c,//fr
        'VersionOnline' => true,
        'UnsubscribeFormId' => 0,
        // Choix possible : Html, Text
        'FormatLinkTechnical' => $f
      );
      
      // Création de la requête
      $createCampaignRequest = array(
        'token' => $token,
        'campaignEmail' => $campaignEmail
      );
      // Création d'une campagne
      $result = $client->CreateCampaign($createCampaignRequest);

      if (!is_null($result->CreateCampaignResult) and $result->CreateCampaignResult != '')
      {
        $campaignId = $result->CreateCampaignResult;
        watchdog('create_campaign','La demande de création a été prise en compte. Voici son ID : @id',array('@id'=>$campaignId));
      }
    }
  }
}
//Gestion d'erreur
catch(SoapFault $fault) {
  $detail = $fault->detail;
  watchdog('create_campaign','Erreur Soap');
  watchdog('create_campaign','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('create_campaign','Description : @message',array('@message' => $detail->ServiceException->Description));

}
}

/**
 * \brief      Envoi d'une campagne mail
 * \details    Envoi une campagne mail avec un segment donné
 * \param      idCampaign        L'id de la campagne à envoyer
 * \param      idSegment         L'id du segment à utiliser
 * \param      date              La date d'envoi
 * \param      volume            Nombre de messages à envoyer
 * \param      period            periode d'envoi
 */
function sendCampaignMail(){
try {
 $result_auth=$this->authenticationSegment();
 if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
    $client=$this->soapCampaignService();
      $token=$this->jetonCampaignService();
      // Requête : envoie le 24 décembre 2017 à 23:59:00, à 200 messages par 5 minutes
      $sendCampaignRequest = array(
        'token' => $token,
        'campaignId' => 4215467,
        'segmentId' => 182,
        'planning' => array(
          'SendDate' => mktime(23, 59, 0, 12, 24, 2017)
        ),
        'frequency' => array(
          'Volume' => 200,
          'Period' => 300 // 300 secondes soit 5 minutes
        )
      );
        
      // Envoi d'une campagne de test
      $result = $client->SendCampaign($sendCampaignRequest);

      if (!is_null($result->SendCampaignResult) and $result->SendCampaignResult != '')
      {
        $ticket = $result->SendCampaignResult;
        watchdog('send_campaign','La demande d envoi a été prise en compte, pour obtenir le
          statut, voici le ticket : @ticket',array('@ticket'=> $ticket));
      }
    }
  }
}
//Gestion d'erreur
catch(SoapFault $fault) {
 $detail = $fault->detail;
  watchdog('send_campaign','Erreur Soap');
  watchdog('send_campaign','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('send_campaign','Description : @message',array('@message' => $detail->ServiceException->Description));
  
}
}

/**
 * \brief      Mise en pause d'une campagne mail
 * \details    La campagne mail choisie sera mise en pause
 * \param      idCampaign        L'id de la campagne à mettre en pause
 */
 function pauseCampaignMail($id){
try{
  $result_auth=$this->authenticationSegment();

  if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
      $client=$this->soapCampaignService();
      $token=$this->jetonCampaignService();

      // Création de la requête
      $pauseRequest = array(
        'token' => $token,
        'campaignId' => $id
      );
      
$result = $client->PauseCampaign($pauseRequest);
      if (!is_null($result) and $result != '')
      {
        watchdog('pause_campaign','La campagne a été mise en pause');
      }
      else{
        watchdog('pause_campaign','La campagne n a pas pu être mise en pause');
    
  }
  }
}
}
catch(SoapFault $fault){
$detail = $fault->detail;
  watchdog('pause_campaign','Erreur Soap');
  watchdog('pause_campaign','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('pause_campaign','Description : @message',array('@message' => $detail->ServiceException->Description));
}
}


/**
 * \brief      Arret d'une campagne mail
 * \details    La campagne mail choisie sera arrêtée
 * \param      idCampaign        L'id de la campagne à arrêter
 */
function cancelCampaignMail($id){
try{
  $result_auth=$this->authenticationSegment();

  if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
      $client=$this->soapCampaignService();
      $token=$this->jetonCampaignService();

      // Création de la requête
      $cancelRequest = array(
        'token' => $token,
        'campaignId' => $id
      );
      
$result = $client->CancelCampaign($cancelRequest);
      if (!is_null($result) and $result != '')
      {
        watchdog('cancel_campaign','La campagne a été arrétée');
      }
      else{
        watchdog('cancel_campaign','La campagne n a pas pu être arrêtée');
    
  }
  }
}
}
catch(SoapFault $fault){
$detail = $fault->detail;
  watchdog('cancel_campaign','Erreur Soap');
  watchdog('cancel_campaign','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('cancel_campaign','Description : @message',array('@message' => $detail->ServiceException->Description));
}
 }

/**
 * \brief      Reprise d'une campagne mail
 * \details    Reprise d'une campagne mail mise en pause
 * \param      idCampaign        L'id de la campagne à relancer
 */
function resumeCampaignMail($id){
try{
  $result_auth=$this->authenticationSegment();

  if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
      $client=$this->soapCampaignService();
      $token=$this->jetonCampaignService();

      // Création de la requête
      $resumeRequest = array(
        'token' => $token,
        'campaignId' => $id,
        'planning' => array(
          'SendDate' => mktime(23, 59, 0, 12, 24, 2017)
          )
      );
      
$result = $client->ResumeCampaign($resumeRequest);
      if (!is_null($result) and $result != '')
      {
        watchdog('resume_campaign','La campagne a bien été redémarrée');
      }
      else{
        watchdog('resume_campaign','La campagne n a pas pu être redemarrée');
    
  }
  }
}
}
catch(SoapFault $fault){
$detail = $fault->detail;
  watchdog('resume_campaign','Erreur Soap');
  watchdog('resume_campaign','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('resume_campaign','Description : @message',array('@message' => $detail->ServiceException->Description));
}
}

/**
 * \brief      Récupération des campagnes mail
 * \details    On récupère les campagnes mail pour afficher leurs informations
*/
function getAllCampaignsMail(){
//TODO  : A CORRIGER!
try{
 $result_auth=$this->authenticationSegment();
 if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
       $client=$this->soapCampaignService();
      $token=$this->jetonCampaignService();

      // Création de la requête
      $getCampaignsRequest = array(
        'token' => $token,
        'request' => array(
          'filter' => array(
              'AllCampaigns' => true,
              'Date' => null,
              'LastCampaigns' => null,
              'Offset' => 0
              ),
          )
      );

      $result = $client->GetCampaigns($getCampaignsRequest);
      if (!is_null($result->GetCampaignsResult) and $result->GetCampaignsResult != '')
      {
         $campaigns = $result->GetCampaignsResult->CampaignDetailsList;
      
        watchdog('get_all_campaigns_mail','@mail',array('@mail'=>count($campaigns)));
      }
      else {
        watchdog('get_all_campaigns_mail','les campagnes n ont pas pu être récupérées');
      }
}
}
}

catch(SoapFault $fault){
  $detail = $fault->detail;
  watchdog('get_all_campaigns_mail','Erreur Soap');
  watchdog('get_all_campaigns_mail','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('get_all_campaigns_mail','Description : @message',array('@message' => $detail->ServiceException->Description));
}
}

/**
 * \brief      Stats d'une campagne mail
 * \details    On affiche les stats d'une campagne mail en cours
 * \param      idCampaign        L'id de la campagne dont on veut les stats
 */
function getStatsCampaignMail($id){
  try{
$result_auth=$this->authenticationSegment();
    if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
    $client=$this->soapStatsCampaignMail();
    $token=$this->jetonStatsCampaignMail();
            
      // Création de la requête
      $getstatsRequest = array(
        'token' => $token,
        'campaignId' => $id
      ); 
      // Récupération d'un segment
      $result = $client->GetStatisticsCampaign($getstatsRequest);
       if (!is_null($result->StatisticsCampaign) and $result->StatisticsCampaign != '')
      {
  $campaigns = $result->StatisticsCampaign;
        watchdog('stats_campaign_mail','@mail',array('@mail'=>$campaigns->ClickedOneOrSeveralLink));
        watchdog('stats_campaign_mail','@mail',array('@mail'=>$campaigns->Complaints));
        watchdog('stats_campaign_mail','@mail',array('@mail'=>$campaigns->ContactOpened));
        watchdog('stats_campaign_mail','@mail',array('@mail'=>$campaigns->MessageDeliverySpending));
        watchdog('stats_campaign_mail','@mail',array('@mail'=>$campaigns->MessageHarbounce));
        watchdog('stats_campaign_mail','@mail',array('@mail'=>$campaigns->MessageSent));
        watchdog('stats_campaign_mail','@mail',array('@mail'=>$campaigns->SentDelivered));
        watchdog('stats_campaign_mail','@mail',array('@mail'=>$campaigns->UnSubscribed));
      }

    else{

  watchdog('stats_campaign_mail','Les statistiques de la campagne n ont pas pu être récupérées');

    }
  }}}
      catch(SoapFault $fault){
  $detail = $fault->detail;
  watchdog('stats_campaign_mail','Erreur Soap');
  watchdog('stats_campaign_mail','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('stats_campaign_mail','Description : @message',array('@message' => $detail->ServiceException->Description));
}
}

/**
 * \brief      Envoie d'une campagne mail de test
 * \details    Envoi d'un BAT pour tester la campagne
 * \param      idCampaign        L'id de la campagne à envoyer en mode test
 * \param      idSegment         L'id du segment à utiliser
 */
function sendCampaignMailTest(){

  try {
  
    $result_auth=$this->authenticationSegment();
  
  if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
     $client=$this->soapCampaignService();
     $token=$this->jetonCampaignService();
      $sendCampaignTestRequest = array(
        'token' => $token,
        'campaignId' => 4215467,
        'segmentId' => 2
      );
        
      // Envoi d'une campagne de test
      $result = $client->SendCampaignTest($sendCampaignTestRequest);

      if (!is_null($result->SendCampaignTestResult) and $result->SendCampaignTestResult != '')
      {
        $ticket = $result->SendCampaignTestResult;
        echo "Le demande d'envoi de la campagne de test a été prise en compte. Pour obtenir le statut voici le ticket : ".$ticket;
        watchdog('send_campaign_mail_test','La demande d envoi de la campagne de test est prise en compte. Voici le ticket : @ticket',array('@ticket'=>$ticket));
      }
    }
  }
}
//Gestion d'erreur
catch(SoapFault $fault) {
   $detail = $fault->detail;
  watchdog('send_campaign_mail_test','Erreur Soap');
  watchdog('send_campaign_mail_test','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('send_campaign_mail_test','Description : @message',array('@message' => $detail->ServiceException->Description));
}
}



/**
 * \brief      Création d'un message sms
 * \details    Création d'un message sms utilisable pour les campagnes sms
 * \param      name        Le nom du message à créer
 * \param      text        Le texte du message
 */
function createMessageSms($name,$text){

  try {
 $result_auth=$this->authenticationSegment();
  
  if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
    $client=$this->soapSmsService();
    $token=$this->jetonSmsService();
      $createMessageRequest = array(
        'token' => $token,
        'message' => array(
          //Nom du message
          'Name' => $name,
          //Contenu du message avec possibilité de personnalisation
          'Text' => $text
        )
      );
    
      // Création du message
      $result = $client->CreateMessage($createMessageRequest);
      if (!is_null($result->CreateMessageResult) and $result->CreateMessageResult != '')
      {
        $message = $result->CreateMessageResult;
        watchdog('create_sms','Le message a été créé');
        watchdog('create_sms','Id : @id',array('@id'=>$message->Id));
        watchdog('create_sms','Text : @text',array('@text'=>$message->Text));
        watchdog('create_sms','UpdateDate : @up',array('@up'=>$message->UpdateDate));
        watchdog('create_sms','Length : @l',array('@l'=>$message->Length));
        watchdog('create_sms','Name : @name',array('@name'=>$message->Name));
      }
      else
      {
        watchdog('create_sms','Le message  n a pas été créé');
      }
    }
  }
}
//Gestion d'erreur
catch(SoapFault $fault) {
 $detail = $fault->detail;
  watchdog('create_sms','Erreur Soap');
  watchdog('create_sms','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('create_sms','Description : @message',array('@message' => $detail->ServiceException->Description));
}
}

/**
 * \brief      Récupération d'un message sms
 * \details    Récupération d'un message sms préalablement créé
 * \param      idsms        L'id du message à récupérer
 */
function getMessageSms(){
 try {
 $result_auth=$this->authenticationSegment();
  if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
      $client=$this->soapSmsService();
      $token=$this->jetonSmsService();
      $getMessageRequest = array(
        'token' => $token,
        'messageId' => 1
      );
    
      // Récupération du message
      $result = $client->GetMessage($getMessageRequest);
      if (!is_null($result->GetMessageResult) and $result->GetMessageResult != '')
      {
        $message = $result->GetMessageResult;
        watchdog('get_sms','Id : @id',array('@id'=>$message->Id));
        watchdog('get_sms','Text : @text',array('@text'=>$message->Text));
        watchdog('get_sms','UpdateDate : @up',array('@up'=>$message->UpdateDate));
        watchdog('get_sms','Length : @l',array('@l'=>$message->Length));
        watchdog('get_sms','Name : @name',array('@name'=>$message->Name));  
      }
      else
      {
        watchdog('get_sms','Le message n a pas été trouvé');
      }
    }
  }
}
//Gestion d'erreur
catch(SoapFault $fault) {
$detail = $fault->detail;
  watchdog('get_sms','Erreur Soap');
  watchdog('get_sms','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('get_sms','Description : @message',array('@message' => $detail->ServiceException->Description));
} 
}

/**
 * \brief      Mise à jour d'un message sms
 * \details    Mise à jour d'un message sms préalablement créé
 * \param      idsms        L'id du message à récupérer
 * \param      name         Le nom à mettre à jour
 * \param      text         Le texte à mettre à jour
 */
function updateMessageSms(){
  try {
    $result_auth=$this->authenticationSegment();
    if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
     $client=$this->soapSmsService();
     $token=$this->jetonSmsService();
      $updateMessageRequest = array(
        'token' => $token,
        'message' => array(
          //Identifiant
          'Id' => 1,
          //Nom du message
          'Name' => 'Nouveau nom du message après MAJ',
          //Contenu du message avec possibilité de personnalisation
          'Text' => 'Ceci est le texte apres MAJ'
        )
      );
    
      // Modification du message
      $result = $client->UpdateMessage($updateMessageRequest);
      watchdog('update_sms','Le message a bien été modifié');
    }
  }
}
//Gestion d'erreur
catch(SoapFault $fault) {
$detail = $fault->detail;
  watchdog('update_sms','Erreur Soap');
  watchdog('update_sms','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('update_sms','Description : @message',array('@message' => $detail->ServiceException->Description));

}
}


/**
 * \brief      Affichage de la liste des expéditeurs
 * \details    Retourne la liste des expéditeurs personnalisés
 */
function getSenders(){
try{
   $result_auth=$this->authenticationSegment();
    if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
     $client=$this->soapSmsService();
     $token=$this->jetonSmsService();
     $getSendersRequest = array(
        'token' => $token
      );
      $result = $client->GetSenders($getSendersRequest);
      $senders = $result->GetSendersResult->SmsSender;
      return $senders;
      watchdog('get_senders','liste récupérée');

}
}
}
//Gestion d'erreur
catch(SoapFault $fault) {
$detail = $fault->detail;
  watchdog('get_senders','Erreur Soap');
  watchdog('get_senders','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('get_senders','Description : @message',array('@message' => $detail->ServiceException->Description));

}
}
/**
 * \brief      Création d'une campagne sms
 * \details    Création d'une campagne sms
 * \param      messageid      L'id du message à intégrer à la campagne
 */
function createCampaignSms($messageId){
try {
 $result_auth=$this->authenticationSegment();
  if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
     $client=$this->soapSmsService();
     $token=$this->jetonSmsService();
        $createCampaignRequest = array(
        'token' => $token,
        'messageId' => $messageId,
        'listUsersGroups' => null
      );
    
      // Création d'une campagne
      $result = $client->CreateCampaign($createCampaignRequest);
      if (!is_null($result->CreateCampaignResult) and $result->CreateCampaignResult != '')
      {
        $campaign = $result->CreateCampaignResult;
        watchdog('create_campaign_sms','création effectuée');
      }
      else
      {
        watchdog('create_campaign_sms','La campagne n a pas été créée');
      }
    }
  }
}
//Gestion d'erreur
catch(SoapFault $fault) {
$detail = $fault->detail;
  watchdog('create_campaign_sms','Erreur Soap');
  watchdog('create_campaign_sms','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('create_campaign_sms','Description : @message',array('@message' => $detail->ServiceException->Description));

}
}

/**
 * \brief      Envoi d'une campagne sms
 * \details    Envoi d'une campagne sms
 * \param      segment      Id du segment à intégrer à la campagne
 * \param       id          Id de la campagne à envoyer
 */
function sendCampaignSms($id,$segment,$date){
try {
  $result_auth=$this->authenticationSegment();
  if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
        $client=$this->soapSmsService();
        $token=$this->jetonSmsService();
    
      //Envoi de la campagne le 1 mai 2020 à 10h45
      $sendCampaignRequest = array(
        'token' => $token,
        'campaignId' => $id,
        'segmentId' => $segment,
        'defaultPrefix' => '+33',
        'sendDate' => $date
      );
      
        // Envoi d'une campagne sms
      $result = $client->SendCampaign($sendCampaignRequest);
      watchdog('send_campaign_sms','La demande d envoi de la campagne sms a été prise en compte');
    }
  }
}
//Gestion d'erreur
catch(SoapFault $fault) {
$detail = $fault->detail;
  watchdog('send_campaign_sms','Erreur Soap');
  watchdog('send_campaign_sms','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('send_campaign_sms','Description : @message',array('@message' => $detail->ServiceException->Description));


}
}

/**
 * \brief      Envoi d'une campagne sms de test
 * \details    Envoi d'une campagne sms de test, envoi d'un BAT
 * \param      segment      Id du segment à intégrer à la campagne
 * \param      id           Id de la campagne à tester    
 */
function sendCampaignSmsTest($id,$segment){
try {
 $result_auth=$this->authenticationSegment();
  if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
      $client=$this->soapSmsService();
      $token=$this->jetonSmsService();
      $sendCampaignBATRequest = array(
        'token' => $token,
        'campaignId' => $id,
        'segmentId' => $segment,
        'defaultPrefix' => '+33'
      );
    
      // Envoi d'une campagne sms en bat
      $result = $client->SendCampaignBAT($sendCampaignBATRequest);
      watchdog('send_campaign_sms_test','La demande d envoi de la campagne sms en bat est prise en compte');
      
    }
  }
}
//Gestion d'erreur
catch(SoapFault $fault) {
$detail = $fault->detail;
  watchdog('send_campaign_sms_test','Erreur Soap');
  watchdog('send_campaign_sms_test','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('send_campaign_sms_test','Description : @message',array('@message' => $detail->ServiceException->Description));
}
}

/**
 * \brief      Simulation d'envoi d'une campagne SMS
 * \details    simuler l'envoi d’une campagne SMS afin d'évaluer le nombre de contacts et de SMS générés avant l'envoi.
  * \param      segment      Id du segment à intégrer à la campagne
 */
function sendCampaignSmsSimulate($id,$segment){
try {
 $result_auth=$this->authenticationSegment();
  if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
      $client=$this->soapSmsService();
      $token=$this->jetonSmsService();
      $sendCampaignSimulateRequest = array(
        'token' => $token,
        'request'=>array(
          'CampaignId'=>$id,
          'DefaultPrefix' => '+33',
          'SegmentId' => $segment,
          )
        );
    
      // Envoi d'une campagne sms en bat
      $result = $client->SendCampaignSimulate($sendCampaignSimulateRequest);
      watchdog('send_campaign_sms_simulate','La demande de simulation de la campagne sms est prise en compte');
      
    }
  }
}
//Gestion d'erreur
catch(SoapFault $fault) {
$detail = $fault->detail;
  watchdog('send_campaign_sms_simulate','Erreur Soap');
  watchdog('send_campaign_sms_simulate','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('send_campaign_sms_simulate','Description : @message',array('@message' => $detail->ServiceException->Description));
}
}




/**
 * \brief      Stats d'une campagne sms et récupération de toutes les campagnes
 * \details    Affichage des stats d'une campagne sms ou récupération des campagnes sms
 * \param      AllCampaigns     Si on veut renvoyer toutes les campagnes ou pas
 * \param      CampaignsRecentlySentCount     Renvoi des X dernières campagnes
 * \param      Date             Renvoi des campagnes à partir de cette date
 */
function getStatsCampaignsSms(){
try {
 $result_auth=$this->authenticationSegment();
  if (!is_null($result_auth->GetAuthenticationTokenResult) and $result_auth->GetAuthenticationTokenResult != '') {
    if ($result_auth->GetAuthenticationTokenResult->Key != '') {
      $client=$this->soapSmsService();
      $token=$this->jetonSmsService();
     
       $sendCampaignStatsRequest = array(
        'token' => $token,
        'filters' => array(
          'AllCampaigns'=>true,
          'CampaignsRecentlySentCount'=>null,
          'Date'=>null
          )
      );
    
      $result = $client->GetCampaigns($sendCampaignStatsRequest);
       if (!is_null($result->GetCampaignsResult) and $result->GetCampaignsResult != '')
      {

        $stats = $result->GetCampaignsResult;
        $statscamp=$stats->SmsCampaign;
        return $statscamp;      
      }
      else
      {
        watchdog('stats_campaign_sms','La campagne n a pas été créée');
      }
}}}
catch(SoapFault $fault) {
$detail = $fault->detail;
  watchdog('stats_campaign_sms','Erreur Soap');
  watchdog('stats_campaign_sms','Message : @message',array('@message' => $detail->ServiceException->Message));
  watchdog('stats_campaign_sms','Description : @message',array('@message' => $detail->ServiceException->Description));
}
}

}?>