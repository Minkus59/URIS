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
  $Parammailing=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Predefini WHERE id=:id");  
  $Parammailing->BindParam(":id", $Id, PDO::PARAM_STR); 
  $Parammailing->execute(); 
  $Param=$Parammailing->fetch(PDO::FETCH_OBJ);
}

$ParammailingListe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Predefini");    
$ParammailingListe->execute(); 


if ((isset($_POST['Modifier']))&&(isset($_GET['id']))) {
     
    $_SESSION['mailing']=$_POST['mailing']; 
    $_SESSION['libele']=$_POST['libele']; 

    if (empty($_SESSION['mailing'])) {
        $Erreur="Un message doit etre saisie !";
    }
    elseif (empty($_SESSION['libele'])) {
        $Erreur="Un libélé doit etre saisie afin de retrouver votre mail !";
    }
    else {
      $Insert=$cnx->prepare("UPDATE ".$Prefix."neuro_mailing_Predefini SET mailing=:mailing, libele=:libele WHERE id=:id");
      $Insert->BindParam(":id", $Id, PDO::PARAM_STR);
      $Insert->BindParam(":libele", $_SESSION['libele'], PDO::PARAM_STR);
      $Insert->BindParam(":mailing", $_SESSION['mailing'], PDO::PARAM_STR);
      $Insert->execute();

      if (!$Insert) {
          $Erreur="Erreur serveur, veuillez réessayer ultèrieurement !";
      }
      else  {     
          unset($_SESSION['mailing']); 
          unset($_SESSION['libele']); 
          $Valid="mailing modifier avec succès";
          header("location:".$Home."/Admin/Mailing/Predefini/?valid=".urlencode($Valid));
      }
  } 
}

if ((isset($_POST['Enregistrer4']))&&(!isset($_GET['id']))) {
    $_SESSION['mailing']=$_POST['mailing']; 
    $_SESSION['libele']=$_POST['libele']; 

    if (empty($_SESSION['mailing'])) {
        $Erreur="Un message doit etre saisie !";
    }
    elseif (empty($_SESSION['libele'])) {
        $Erreur="Un libélé doit etre saisie afin de retrouver votre mail !";
    }
    else {
        $InsertParam=$cnx->prepare("INSERT INTO ".$Prefix."neuro_mailing_Predefini (libele, mailing) VALUES(:libele, :mailing)");
        $InsertParam->bindParam(':libele', $_SESSION['libele'], PDO::PARAM_STR);
        $InsertParam->bindParam(':mailing', $_SESSION['mailing'], PDO::PARAM_STR);
        $InsertParam->execute();

        unset($_SESSION['mailing']); 
        unset($_SESSION['libele']);   
        $Valid="mailing ajouter avec succès";
        header("location:".$Home."/Admin/Mailing/Predefini/?valid=".urlencode($Valid));
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
<H1 class="TitreOrange">Mailing Type</H1>

<form name="SelectMode" action="" method="POST">
Message envoyé avec le Mailing <font color='#FF0000'>*</font> <BR /><BR />
<input type="text" name="libele" placeholder="Libellé*" value="<?php if (isset($_SESSION['libele'])) { echo $_SESSION['libele']; }  else { echo $Param->libele; } ?>"/><BR />

<textarea id="message" name="mailing" placeholder="Mailing*" require="required"><?php if (isset($_SESSION['mailing'])) { echo $_SESSION['mailing']; }  else { echo $Param->mailing; } ?></textarea>
<BR />
<span class="col_1"></span>
<?php
if (isset($_GET['id'])) { 
?>
<input type="submit" name="Modifier" value="Modifier" class="normal"/>
<?php } 
else { ?>
<input type="submit" name="Enregistrer4" value="Enregistrer" class="normal"/>
<?php } ?>
</form>
</div>

<H1 class="TitreOrange">Liste des mails enregistré</H1>

<table class="Admin">
<tr>
      <th>Modèle</th>
      <th>Libellé</th>
      <th>Aperçu</th>
      <th>Action</th>
      </tr>
<?php

while ($Liste=$ParammailingListe->fetch(PDO::FETCH_OBJ)) {
?>
   <tr>
   <td><?php echo $Liste->id; ?></td>
   <td><?php echo $Liste->libele; ?></td>
   <td><?php echo $Liste->mailing; ?></td>
   <td>
   <?php 
   echo '<a href="'.$Home.'/Admin/Mailing/Predefini/?id='.$Liste->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a>';
   echo '<a title="Supprimer" href="'.$Home.'/Admin/Mailing/Predefini/supprimer.php?id='.$Liste->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a></td></tr>';
}
?>
</table>
</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>