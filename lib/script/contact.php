<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");
session_start();

if ((isset($_POST['envoyer']))&&($_POST['envoyer']=="envoyer")) {
   $Nom=FiltreText('nom');
   $Tel=FiltreTel('tel');
   $Cp=FiltreText('cp');
   $Sujet=FiltreText('sujet');
   $Message=FiltreText('message');
   $Email=FiltreEmail('email');

  if ($Nom[0]===false) {
    $Erreur=$Nom[1];
  }  
  else {
    $_SESSION['nom']=$Nom;
  } 
      
  if ($Tel[0]===false) {
    $Erreur=$Tel[1]; 
  }  
  else {
    $_SESSION['tel']=$Tel;
  } 
   
  if ($Cp[0]===false) {
    $Erreur=$Cp[1]; 
  }  
  else {
    $_SESSION['cp']=$Cp;
  } 
   
  if ($Sujet[0]===false) {
    $Erreur=$Sujet[1];
  }  
  else {
    $_SESSION['sujet']=$Sujet;
  } 
   
  if ($Message[0]===false) {
    $Erreur=$Message[1];
  }  
  else {
    $_SESSION['message']=$Message;
  }  
         
  if ($Email[0]===false) {
    $Erreur=$Email[1]; 
  }    
  else {
    $_SESSION['email']=$Email;
  }  
  
  if (!isset($Erreur)) {
    $Body="<H1>Demande de contact</H1>
    Message de : ".$Email."<BR />
    Nom : ".$Nom."<BR />
    Tel : ".$Tel."<BR />
    Code postal : ".$Cp."<BR />
    Sujet : ".$Sujet."<BR />
    <BR />
    Message : ".$Message."</p>";

    if (EnvoiNotification($Nom, $Email, "Demande de contact", $Body, $EmailDestinataire->email)==false) {
        $Erreur="L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
        ErreurLog($Erreur);
        header('location:'.$Home.'/Contact/?erreur='.$Erreur);
    } 
    else {
      session_unset();
      session_destroy();
      $Erreur=urlencode("Votre message à bien été enregistré, il sera traité dans les meilleurs délais !");
      header('location:'.$Home.'/Contact/?valid='.$Erreur);
    }
  }
  else {
    header('location:'.$Home.'/Contact/?erreur='.urlencode($Erreur));
  }
}
else {
  header("location:".$Home."/Contact/");
}
?>