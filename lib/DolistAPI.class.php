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
  $e=trim($email);
  // Abonne ou désabonne le contact suivant la valeur de la variable
  }
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
      /******************************************/
      /* LECTURE  DES CONTACTS */
      /******************************************/
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
        watchdog('list_contact_fields','Aucun contact trouvé');
      }     
      
      
      /******************************************/
      
      
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
 * \brief      Abonnement / Desabonnement d'un contact
 * \details    Cette fonction permet l'abonnement (ou le désabonnement) d'un contact à la réception d'emails ou de sms (ou les deux)
 * \param    email           L'adresse email du contact à modifier
 * \param    abomail         Précise si le contact doit être abonné ou pas aux emails (0 : abonnement, 1 : désabonnement)
 * \param    abosms          Précise si le contact doit être abonné ou pas aux sms (0 : abonnement, 1 : désabonnement)
 */
function suscribeContact ($email,$abomail,$abosms){
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
        'segmentID' => 42
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
        'segmentName' => 'Email de bienvenue'
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
}?>