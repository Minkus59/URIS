<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

if ($Cnx_Admin===false) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Id=$_GET['id'];

if ((!empty($_GET['id']))&&(isset($_POST['oui']))) {

    $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Document WHERE id=:id");
    $Select->bindParam(':id', $Id, PDO::PARAM_INT);
    $Select->execute();
    $Lien=$Select->fetch(PDO::FETCH_OBJ);

    if ($Lien->type=="PDF") {
        unlink($_SERVER['DOCUMENT_ROOT']."/lib/Document/".basename($Lien->lien));
    }
    if ($Lien->type=="Image") {
        unlink($_SERVER['DOCUMENT_ROOT']."/lib/Photo/".basename($Lien->lien));
    }
    if ($Lien->type=="Video") {
        unlink($_SERVER['DOCUMENT_ROOT']."/lib/Video/".basename($Lien->lien));
    }
    if ($Lien->type=="PPS") {
        unlink($_SERVER['DOCUMENT_ROOT']."/lib/pps/".basename($Lien->lien));
    }

    unlink($_SERVER['DOCUMENT_ROOT']."/lib/Document/".basename($Lien->lien));

    $deleteActu=$cnx->prepare("DELETE FROM ".$Prefix."neuro_Document WHERE id=:id");
    $deleteActu->bindParam(':id', $Id, PDO::PARAM_INT);
    $deleteActu->execute();

    header('Location:'.$Home.'/Admin/Document/');
}

if ((!empty($_GET['id']))&&(isset($_POST['non']))) {  
    header('Location:'.$Home.'/Admin/Document/');
}
?>  

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>
<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".urldecode($Valid)."</font><BR />"; }   ?>

Etes-vous sur de vouloir supprimer ce document ? <BR /><BR />

<table class="Admin" width="300">
<form action="" method="POST">
<TR><TD align="center"><input name="oui" type="submit" value="OUI"></TD><TD align="center"><input name="non" type="submit" value="NON"/></TD></TR>
</form></TABLE>

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>