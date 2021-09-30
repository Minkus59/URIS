<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

if ($Cnx_Admin!=TRUE) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Id=$_GET['id'];

if (isset($_GET['id'])) { 
  $ParamSignature=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Signature WHERE id=:id");  
  $ParamSignature->BindParam(":id", $Id, PDO::PARAM_STR); 
  $ParamSignature->execute(); 
  $SignatureParam=$ParamSignature->fetch(PDO::FETCH_OBJ);
}

$SignatureListe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Signature");    
$SignatureListe->execute(); 


if ((isset($_POST['Modifier']))&&(isset($_GET['id']))) {
     
    $_SESSION['signature']=$_POST['signature']; 
    $_SESSION['libele']=$_POST['libele']; 

    if (empty($_SESSION['signature'])) {
        $Erreur="Un message doit etre saisie !";
    }
    elseif (empty($_SESSION['libele'])) {
        $Erreur="Un libélé doit etre saisie afin de retrouver votre mail !";
    }
    else {
      $Insert=$cnx->prepare("UPDATE ".$Prefix."neuro_mailing_Signature SET signature=:signature, libelle=:libelle WHERE id=:id");
      $Insert->BindParam(":id", $Id, PDO::PARAM_STR);
      $Insert->BindParam(":libelle", $_SESSION['libele'], PDO::PARAM_STR);
      $Insert->BindParam(":signature", $_SESSION['signature'], PDO::PARAM_STR);
      $Insert->execute();

      if (!$Insert) {
          $Erreur="Erreur serveur, veuillez réessayer ultèrieurement !";
      }
      else  {     
          unset($_SESSION['signature']); 
          unset($_SESSION['libele']); 
          $Valid="signature modifier avec succès";
          header("location:".$Home."/Admin/Mailing/Signature/?valid=".urlencode($Valid));
      }
  } 
}

if ((isset($_POST['Enregistrer4']))&&(!isset($_GET['id']))) {
    $_SESSION['signature']=$_POST['signature']; 
    $_SESSION['libele']=$_POST['libele']; 

    if (empty($_SESSION['signature'])) {
        $Erreur="Un message doit etre saisie !";
    }
    elseif (empty($_SESSION['libele'])) {
        $Erreur="Un libélé doit etre saisie afin de retrouver votre mail !";
    }
    else {
        $InsertParam=$cnx->prepare("INSERT INTO ".$Prefix."neuro_mailing_Signature (libelle, signature) VALUES(:libelle, :signature)");
        $InsertParam->bindParam(':libelle', $_SESSION['libele'], PDO::PARAM_STR);
        $InsertParam->bindParam(':signature', $_SESSION['signature'], PDO::PARAM_STR);
        $InsertParam->execute();

        unset($_SESSION['signature']); 
        unset($_SESSION['libele']);   
        $Valid="Signature ajouter avec succès";
        header("location:".$Home."/Admin/Mailing/Signature/?valid=".urlencode($Valid));
    }
}
    
?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".urldecode($Valid)."</font><BR />"; } ?>

<div id="Form_Middle5">
<H1 class="TitreOrange">Signature Type</H1>

<form name="SelectMode" action="" method="POST">
Message envoyé avec le Mailing <font color='#FF0000'>*</font> <BR /><BR />
<input type="text" name="libele" placeholder="Libellé*" value="<?php if (isset($_SESSION['libele'])) { echo $_SESSION['libele']; }  else { echo $SignatureParam->libelle; } ?>"/><BR />

<textarea id="message" name="signature" placeholder="Signature*" require="required"><?php if (isset($_SESSION['signature'])) { echo $_SESSION['signature']; }  else { echo $SignatureParam->signature; } ?></textarea>
<BR />
<span class="col_1"></span>
<?php
if (isset($_GET['id'])) { 
?>
<input type="submit" class="ButtonOrange" name="Modifier" value="Modifier"/>
<?php } 
else { ?>
<input type="submit" class="ButtonOrange" name="Enregistrer4" value="Enregistrer"/>
<?php } ?>
</form>
</div>

<H1 class="TitreOrange">Liste des signatures enregistrée</H1>

<table class="Admin">
<tr>
      <th>Libellé</th>
      <th>Aperçu</th>
      <th>Action</th>
      </tr>
<?php

while ($Signature=$SignatureListe->fetch(PDO::FETCH_OBJ)) {
?>
   <tr>
   <td><?php echo $Signature->libelle; ?></td>
   <td><?php echo $Signature->signature; ?></td>
   <td>
   <?php 
   echo '<a href="'.$Home.'/Admin/Mailing/Signature/?id='.$Signature->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a>';
   echo '<a title="Supprimer" href="'.$Home.'/Admin/Mailing/Signature/supprimer.php?id='.$Signature->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a></td></tr>';
}
?>
</table>
</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>