<?php
require($_SERVER['DOCUMENT_ROOT']."/impinfbdd/OvH.inc.php");

try {
    $cnx = new PDO($Ovh_serv_bDd, $uTil_bDd_serv, $mDp_bDd_serv);
}
catch (PDOException $e) {
    die("Erreur de connexion à la base de donnée, veuillez réessayer ultèrieurement !");
}

function taille_champ($champ,$taille_min=0,$taille_max=0) {
    if(!isset($champ)) {
        return false;
  }
    elseif (strlen($champ)<$taille_min) {
        return false;
  }
    elseif(strlen($champ)>$taille_max) {
        return false;
  }
return true; 
}

function FiltreNum($name) {
  $string=filter_input(INPUT_POST,$name, FILTER_SANITIZE_STRIPPED);
  $string=filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $string=preg_replace(array('/,/'), '.', $string);
  
  if($string===false) { 
    $string=array(false, "Erreur, les caractères utilisés ne sont pas conforme");
    return $string;
  }
  else {
    return $string;
  }
}

function FiltreText($name) {
  $string=filter_input(INPUT_POST,$name, FILTER_SANITIZE_STRIPPED);
  $string=filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  
  if($string===false) { 
    $string=array(false, urlencode("Erreur, les caractères utilisés ne sont pas conforme"));
    return $string;
  }
  else { 
    if (strlen(trim($string))<=1) {
      $string=array(false, "Certain champ doivent être saisie !");
      return $string;  
    }
    else {
      return $string;
    }  
  }
}

function FiltreMDP($name) {
  $string=filter_input(INPUT_POST,$name, FILTER_SANITIZE_STRIPPED);
  $string=filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  
  if($string===false) { 
    $string=array(false, "Erreur, les caractères utilisés ne sont pas conforme");
    return $string;
  }
  else {  
    if (!taille_champ($string,8,25)) {
      $string=array(false, "Le mot de passe doit contenir entre 8 et 25 caractères !");
      return $string;  
    }
    elseif (!preg_match("#^[A-Z][A-z-._]+[0-9]+$#", $string)){ 
      $string=array(false, "Le mot de passe ne doit pas comporter d'espace, doit commencer par une majuscule et finir par des chiffres !");
      return $string;  
    }
    else {
      return $string;
    }  
  }
}

function FiltreEmail($name) {
  $string=filter_input(INPUT_POST,$name, FILTER_SANITIZE_STRIPPED);
  $string=filter_var($string, FILTER_SANITIZE_EMAIL);
  $string=filter_var($string, FILTER_VALIDATE_EMAIL);
  $string=strtolower($string);

  if($string===false) { 
    $string=array(false, "L'adresse e-mail n'est pas conforme !");
    return $string;
  }
  else {  
    if (!preg_match("#^[A-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $string)){ 
      $string=array(false, "L'adresse e-mail n'est pas conforme !");
      return $string; 
    } 
    else {
      return $string;
    }  
  }
}


function FiltreTel($name) {  
  $string=filter_input(INPUT_POST,$name, FILTER_SANITIZE_STRIPPED);
  $string=filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $string=preg_replace(array('/\s/','/-/'), '', $string);

  if($string===false) { 
    $string=array(false, "Erreur, les caractères utilisés ne sont pas conforme");
    return $string;
  }
  else {
    if(!preg_match("/^[0-9]{10}$/", $string)) {
      $string=array(false, "Le numéro de téléphone n'est pas valide !");
      return $string;
    }
    else {
      return $string;
    }  
  }
}

function FiltreTextGET($name) {
  $string=filter_input(INPUT_GET,$name, FILTER_SANITIZE_STRIPPED);
  $string=filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  
  if($string===false) { 
    return false;  
  }
  else { 
    if (strlen($string)<=2) {
       return false; 
    }
    else {
      return $string;
    }  
  }
}

function EnvoiNotification($De, $Envoi, $Titre, $Body, $Desti) {
    $Home="https://www.ingenieurs-scientifiques-npdc.fr/";

    $header = "MIME-Version: 1.0\n";
    $header .= "Content-Type:multipart/mixed; boundary=\"$boundary\"\n";
    $header .= "From: \"$De\"<$Envoi>\n";
    $header .= "\n";
    
    $message="Ce message est au format MIME.\n";
    
    $message.="--$boundary\n";
    $message.= "Content-Type: text/html; charset=utf-8\n";
    
    $message.="\n";

    $message.="<html><head><title>".$Titre."</title>
    </head><body>".$Body."
    </tbody>
    </table>
    </body></html>";
    
    $message.="\n\n";
    
    $message.="--$boundary--\n";

    if (!mail($Desti, $Titre, $message, $header)) {
        return false;
    }
    else {   
        return true;
    }
}

function SiteMap($Prefix, $Home, $cnx) {
    $SiteMapFich=$_SERVER['DOCUMENT_ROOT']."/sitemap.xml";

    $SelectSiteMap=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE statue='1'");
    $SelectSiteMap->execute();

    $Ouverture=fopen($SiteMapFich, "w+");
    $Contenu='<?xml version="1.0" encoding="UTF-8"?>';
    $Ecriture=fwrite($Ouverture, $Contenu."\n");
    $Contenu='<urlset xmlns="http://www.google.com/schemas/sitemap/0.9">';
    $Ecriture=fwrite($Ouverture, $Contenu."\n");
    while ($SiteMap=$SelectSiteMap->fetch(PDO::FETCH_OBJ)) {
        $Contenu='<url>';
        $Ecriture=fwrite($Ouverture, $Contenu."\n");
        $Contenu='<loc>'.$Home.$SiteMap->lien.'</loc>';
        $Ecriture=fwrite($Ouverture, $Contenu."\n");
        $Contenu='</url>';
        $Ecriture=fwrite($Ouverture, $Contenu."\n");
    }         
    $Contenu='</urlset>';
    $Ecriture=fwrite($Ouverture, $Contenu."\n");
    fclose();
}

function ErreurLog($ErreurVar) {
    $ErreurLogDoc=$_SERVER['DOCUMENT_ROOT']."/lib/Log/";
    $ErreurLogFich=$ErreurLogDoc."erreurlog.txt";
    $Ip=$_SERVER['REMOTE_ADDR'];

    if(!file_exists($_SERVER['DOCUMENT_ROOT']."/lib/Log/")) {
        mkdir($ErreurLogDoc, 0777);
    }

    $Ouverture=fopen($ErreurLogFich, "a+");
    //Si une session existe
    if (isset($_SESSION['NeuroClient'])) { 
        $Ecriture=fwrite($Ouverture, $SessionClient." ; ".$ErreurVar." ; ".date("d-m-y / G:i:s", time())."\n");
    }
    // si aucune session
    else {
        $Ecriture=fwrite($Ouverture, $Ip." ; ".$ErreurVar." ; ".date("d-m-y / G:i:s", time())."\n");
    }
    fclose();
}

function ErreurLogMail($ErreurVar) {
  $ErreurLogDoc=$_SERVER['DOCUMENT_ROOT']."/lib/Log/";
  $ErreurLogFich1=$ErreurLogDoc."erreurlogMail.txt";
  $Ip=$_SERVER['REMOTE_ADDR'];

  if(!file_exists($_SERVER['DOCUMENT_ROOT']."/lib/Log/")) {
      mkdir($ErreurLogDoc, 0777);
  }

  $Ouverture=fopen($ErreurLogFich1, "a+");
  //Si une session existe
  if (isset($_SESSION['NeuroClient'])) { 
      $Ecriture=fwrite($Ouverture, $SessionClient." ; ".$ErreurVar." ; ".date("d-m-y / G:i:s", time())."\n");
  }
  // si aucune session
  else {
      $Ecriture=fwrite($Ouverture, $Ip." ; ".$ErreurVar." ; ".date("d-m-y / G:i:s", time())."\n");
  }
  fclose();
}

function LogMail($ErreurVar) {
  $ErreurLogDoc=$_SERVER['DOCUMENT_ROOT']."/lib/Log/";
  $ErreurLogFich2=$ErreurLogDoc."logMail.txt";
  $Ip=$_SERVER['REMOTE_ADDR'];

  if(!file_exists($_SERVER['DOCUMENT_ROOT']."/lib/Log/")) {
      mkdir($ErreurLogDoc, 0777);
  }

  $Ouverture=fopen($ErreurLogFich2, "a+");
  //Si une session existe
  if (isset($_SESSION['NeuroClient'])) { 
      $Ecriture=fwrite($Ouverture, $SessionClient." ; ".$ErreurVar." ; ".date("d-m-y / G:i:s", time())."\n");
  }
  // si aucune session
  else {
      $Ecriture=fwrite($Ouverture, $Ip." ; ".$ErreurVar." ; ".date("d-m-y / G:i:s", time())."\n");
  }
  fclose();
}
?>