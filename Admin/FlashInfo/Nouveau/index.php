<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

if ($Cnx_Admin===false) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Id=$_GET['id'];

if (isset($_GET['id'])) { 
    $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_FlashInfo WHERE id=:id");
    $Select->BindParam(":id", $Id, PDO::PARAM_STR);
    $Select->execute();
    $Flash=$Select->fetch(PDO::FETCH_OBJ);
}

if ((isset($_POST['Ajouter']))&&(!isset($_GET['id']))) {
    $Description=$_POST['description'];

    //verifier si 1er article sinon position +1
    $Verif=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_FlashInfo");
    $Verif->execute();
    $NbFlash=$Verif->rowCount();
    $Position=$NbFlash+1;

    if (trim($Description)=="") {
        $Erreur="Le contenue Description est vide !";
        ErreurLog($Erreur);
    }

    else {
        $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_FlashInfo (position, description) VALUES(:position, :description)");
        $Insert->BindParam(":description", $Description, PDO::PARAM_STR);
        $Insert->BindParam(":position", $Position, PDO::PARAM_STR);
        $Insert->execute();

         if ($Insert==false) {
            $Erreur="Erreur serveur, veuillez réessayer ultèrieurement !";
         }
         else  {
            $Valid="Flash info ajouter avec succès";
            header('location:'.$Home.'/Admin/FlashInfo/Nouveau/?valid='.urlencode($Valid));
         }
    }
}

if ((isset($_POST['Modifier']))&&(isset($_GET['id']))) {
    $Description=$_POST['description'];
    $Position=$_POST['position'];

    if (trim($Description)=="") {
        $Erreur="Le contenue Description est vide !";
        ErreurLog($Erreur);
    }
    else {
        $Insert=$cnx->prepare("UPDATE ".$Prefix."neuro_FlashInfo SET position=:position, description=:description WHERE id=:id");
        $Insert->BindParam(":id", $Id, PDO::PARAM_STR);
        $Insert->BindParam(":description", $Description, PDO::PARAM_STR);  
        $Insert->BindParam(":position", $Position, PDO::PARAM_STR);      
        $Insert->execute();

        if (!$Insert) {
            $Erreur="Erreur serveur, veuillez réessayer ultèrieurement !";
            ErreurLog($Erreur);
        }
        else  {     
            $Valid="Flash info modifier avec succès";
            header('location:'.$Home.'/Admin/FlashInfo/Nouveau/?id='.$Id.'&valid='.urlencode($Valid));
        }
    }
} 
    
?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>
<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".urldecode($Valid)."</font><BR />"; }   ?>

<?php if (isset($_GET['id'])) { ?>
      <H1>Modifier un flash info</H1> <?php
} else { ?>
  <H1>Ajouter un flash info</H1><?php
} ?>

<form name="form_Flash" action="" method="POST">

<?php if (isset($_GET['id'])) { ?>
<input type="text" name="position" placeholder="Position*" require="required" value="<?php if (isset($_GET['id'])) { echo $Flash->position; } ?>"><BR />
<?php } ?>

<textarea id="message" name="description" placeholder="Description : *" require="required"><?php if (isset($_GET['id'])) { echo $Flash->description; } ?></textarea><BR /><BR />

<?php if (isset($_GET['id'])) { ?><input type="submit" name="Modifier" value="Modifier"/> <?php } else { ?><input type="submit" name="Ajouter" value="Ajouter"/><?php } ?>
</form>
<p><font color='#FF0000'>*</font> Champ de saisie requis<BR />

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>