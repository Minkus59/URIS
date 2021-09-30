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
    
    $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE id=:id AND statue!='2'");
    $Select->BindParam(":id", $Id, PDO::PARAM_STR);
    $Select->execute();
    $Actu=$Select->fetch(PDO::FETCH_OBJ);
    $NbCount=$Select->rowCount();
    
    if ($NbCount!=0) {
        if(!unlink($_SERVER['DOCUMENT_ROOT']."/".$Actu->lien."/index.php")) {
            $Erreur="Echec lors de la suppression";
        }
        elseif(!rmdir($_SERVER['DOCUMENT_ROOT']."/".$Actu->lien)) {
            $Erreur="Echec lors de la suppression";
        } 
        else {
            $deleteActu=$cnx->prepare("DELETE FROM ".$Prefix."neuro_Page WHERE id=:id AND statue!='2'");
            $deleteActu->bindParam(':id', $Id, PDO::PARAM_INT);
            $deleteActu->execute();

            header('location:'.$Home.'/Admin/Page/');
        }
    }
    else {
        header('location:'.$Home.'/Admin/Page/');
    }
}

if ((!empty($_GET['id']))&&(isset($_POST['non']))) {  
    header('location:'.$Home.'/Admin/Page/');
}
?>  


<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>
<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".urldecode($Valid)."</font><BR />"; }   ?>

Etes-vous sur de vouloir supprimer cette Page ? <BR /><BR />

<table class="Admin" width="300">
<form action="" method="POST">
<TR><TD align="center"><input name="oui" type="submit" value="OUI"></TD><TD align="center"><input name="non" type="submit" value="NON"/></TD></TR>
</form></TABLE>

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>