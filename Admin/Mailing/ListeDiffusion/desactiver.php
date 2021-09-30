<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

if ($Cnx_Admin!=TRUE) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Id=$_GET['id'];
$Groupe=urldecode($_GET['groupe']);

if ((!empty($_GET['id']))&&(isset($_POST['oui']))) {

    $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste_Diffusion WHERE id=:id");
    $Select->bindParam(':id', $Id, PDO::PARAM_INT);
    $Select->execute();
    $Diffusion=$Select->fetch(PDO::FETCH_OBJ);

    if($Diffusion->diffusion==2) {
        $Erreur="Impossible de désactiver la diffusion sur cette email, le client ne souhaite plus reçevoir d'e-mail";
    }
    else {
        $Update=$cnx->prepare("UPDATE ".$Prefix."neuro_mailing_Liste_Diffusion SET diffusion=0 WHERE id=:id");
        $Update->bindParam(':id', $Id, PDO::PARAM_INT);
        $Update->execute();

        header('Location:'.$Home.'/Admin/Mailing/ListeDiffusion/?groupe='.$Groupe);
    }
}

if (isset($_POST['non'])) {  
    header('Location:'.$Home.'/Admin/Mailing/ListeDiffusion/?groupe='.$Groupe);
}
?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".urldecode($Valid)."</font><BR />"; } ?>

Etes-vous sur de vouloir désactiver cette email ? <BR /><BR />

<table class="Admin" width="300">
<form action="" method="POST">
<TR><TD align="center"><input name="oui" type="submit" value="OUI"></TD><TD align="center"><input name="non" type="submit" value="NON"/></TD></TR>
</form></TABLE>

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>