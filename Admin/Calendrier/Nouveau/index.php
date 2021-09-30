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
    $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Calendrier WHERE id=:id");
    $Select->BindParam(":id", $Id, PDO::PARAM_STR);
    $Select->execute();
    $Event=$Select->fetch(PDO::FETCH_OBJ);
}

if ((isset($_POST['Ajouter']))&&(!isset($_GET['id']))) {
    $Titre=$_POST['titre'];
    $Lien=$_POST['lien'];

    $search  = array('"');
    $replace = array("'");
    $Description=str_replace($search, $replace, $_POST['description']);

    $Date=$_POST['date'];
    $Heure=$_POST['heure'];
    $Minute=$_POST['minute'];

    if (trim($Titre)=="") {
        $Erreur="Le contenue Titre est vide !";
        ErreurLog($Erreur);
    }
    elseif (trim($Description)=="") {
        $Erreur="Le contenue Description est vide !";
        ErreurLog($Erreur);
    }
    elseif (trim($Date)=="") {
        $Erreur="Le contenue Date est vide !";
        ErreurLog($Erreur);
    }    
    elseif (trim($Heure)=="") {
        $Erreur="Le contenue Heure est vide !";
        ErreurLog($Erreur);
    }    
    elseif (trim($Minute)=="") {
        $Erreur="Le contenue Minute est vide !";
        ErreurLog($Erreur);
    }
    else {
        $Date=explode("/",$Date);
        $Date=mktime($Heure,$Minute,0,$Date[1],$Date[0],$Date[2]);

        $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Calendrier (titre, description, created, lien) VALUES(:titre, :description, :created, :lien)");
        $Insert->BindParam(":titre", $Titre, PDO::PARAM_STR);
        $Insert->BindParam(":description", $Description, PDO::PARAM_STR);        
        $Insert->BindParam(":created", $Date, PDO::PARAM_STR);      
        $Insert->BindParam(":lien", $Lien, PDO::PARAM_STR);
        $Insert->execute();

         if ($Insert==false) {
            $Erreur="Erreur serveur, veuillez réessayer ultèrieurement !";
         }
         else  {
            $Valid="Événement ajouter avec succès";
            header('location:'.$Home.'/Admin/Calendrier/Nouveau/?valid='.urlencode($Valid));
         }
    }
}

if ((isset($_POST['Modifier']))&&(isset($_GET['id']))) {
    $Titre=$_POST['titre'];
    $Lien=$_POST['lien'];

    $search  = array('"');
    $replace = array("'");
    $Description=str_replace($search, $replace, $_POST['description']);

    $Date=$_POST['date'];
    $Heure=$_POST['heure'];
    $Minute=$_POST['minute'];

    if (trim($Titre)=="") {
        $Erreur="Le contenue Titre est vide !";
        ErreurLog($Erreur);
    }
    elseif (trim($Description)=="") {
        $Erreur="Le contenue Description est vide !";
        ErreurLog($Erreur);
    }
    elseif (trim($Date)=="") {
        $Erreur="Le contenue Date est vide !";
        ErreurLog($Erreur);
    }
    elseif (trim($Heure)=="") {
        $Erreur="Le contenue Heure est vide !";
        ErreurLog($Erreur);
    }    
    elseif (trim($Minute)=="") {
        $Erreur="Le contenue Minute est vide !";
        ErreurLog($Erreur);
    }
    else {
        $Date=explode("/",$Date);
        $Date=mktime($Heure,$Minute,0,$Date[1],$Date[0],$Date[2]);

        $Insert=$cnx->prepare("UPDATE ".$Prefix."neuro_Calendrier SET titre=:titre ,description=:description, lien=:lien, created=:created WHERE id=:id");
        $Insert->BindParam(":id", $Id, PDO::PARAM_STR);
        $Insert->BindParam(":titre", $Titre, PDO::PARAM_STR);
        $Insert->BindParam(":description", $Description, PDO::PARAM_STR);        
        $Insert->BindParam(":created", $Date, PDO::PARAM_STR);      
        $Insert->BindParam(":lien", $Lien, PDO::PARAM_STR);
        $Insert->execute();

        if (!$Insert) {
            $Erreur="Erreur serveur, veuillez réessayer ultèrieurement !";
            ErreurLog($Erreur);
        }
        else  {     
            $Valid="Événement modifier avec succès";
            header('location:'.$Home.'/Admin/Calendrier/Nouveau/?id='.$Id.'&valid='.urlencode($Valid));
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
      <H1>Modifier un événement</H1> <?php
} else { ?>
  <H1>Ajouter un nouvel événement</H1><?php
} ?>

<form name="form_Event" action="" method="POST">

<input type="text" name="date" id="datepicker" placeholder="Date*" require="required" value="<?php if (isset($_GET['id'])) { echo date("d/m/Y",$Event->created); } ?>"><BR />
<select name="heure" class="SelectMini">
    <option value="NULL">h</option>
    <?php for($h=0;$h!=24;$h++) { ?>
        <option value="<?php echo sprintf("%'.02d\n", $h); ?>" <?php if ((isset($_GET['id']))&&(date("G",$Event->created))==$h) { echo "selected"; } ?>><?php echo sprintf("%'.02d\n", $h); ?></option>
<?php } ?>
</select>
<select name="minute" class="SelectMini">
    <option value="NULL">min</option>
    <?php for($m=0;$m!=60;$m++) { ?>
        <option value="<?php echo sprintf("%'.02d\n", $m); ?>" <?php if ((isset($_GET['id']))&&(date("i",$Event->created))==$m) { echo "selected"; } ?>><?php echo sprintf("%'.02d\n", $m); ?></option>
<?php } ?>
</select><BR /><BR />

<input type="text" name="titre" placeholder="Titre*" require="required" value="<?php if (isset($_GET['id'])) { echo $Event->titre; } ?>"><BR />

<textarea id="message" name="description" placeholder="Description : *" require="required"><?php if (isset($_GET['id'])) { echo $Event->description; } ?></textarea><BR /><BR />

<input class="Long" type="text" name="lien" placeholder="Lien*" value="<?php if (isset($_GET['id'])) { echo $Event->lien; } ?>"><BR /><BR />

<?php if (isset($_GET['id'])) { ?><input type="submit" name="Modifier" value="Modifier"/> <?php } else { ?><input type="submit" name="Ajouter" value="Ajouter"/><?php } ?>
</form>
<p><font color='#FF0000'>*</font> Champ de saisie requis<BR />

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>