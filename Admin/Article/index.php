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
$Now=time();

if (isset($_POST['StatuePage'])) {
   $_SESSION['StatuePage']=$_POST['StatuePage'];
}

if ((!isset($_SESSION['StatuePage']))||($_SESSION['StatuePage']=="NULL")) {
     $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Article ORDER BY page ASC");
     $Select->execute();
}
else {
     $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Article WHERE page=:page ORDER BY position ASC");
     $Select->bindParam(':page', $_SESSION['StatuePage'], PDO::PARAM_STR);
     $Select->execute();
}

$SelectPage=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page");
$SelectPage->execute();
    
?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>
<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".urldecode($Valid)."</font><BR />"; }   ?>

<H1>Liste des articles</H1>

<form name="FormPage" action="" method="POST">
<select name="StatuePage" required="required" onChange="this.form.submit()">
      
<option value="NULL" <?php if ($_SESSION['StatuePage']=="NULL") { echo "selected"; } ?>>Tous</option>
<?php while ($Page=$SelectPage->fetch(PDO::FETCH_OBJ)) { ?>
<option value='<?php echo $Page->lien; ?>' <?php if ($_SESSION['StatuePage']== $Page->lien) { echo "selected"; } ?>><?php echo $Page->lien; ?></option>
<?php } ?>

</select>
</form>

<table class="Admin">
<tr><th>Position</th><th>Lien de page</th><th>Date de création</th><th>Action</th></tr>
<?php

while ($Article=$Select->fetch(PDO::FETCH_OBJ)) {
?>
   <tr <?php if ($Article->statue==0) { echo "class='rouge'"; } else { echo "class='vert'"; } ?>>
   <td><?php echo $Article->position; ?></td>
   <td><?php echo $Article->page; ?></td>
   <td><?php echo date("d-m-Y", time($Article->created)); ?></td>
   <td><?php echo '<a title="Apperçu" href="'.$Home.$Article->page.'"><img src="'.$Home.'/Admin/lib/img/apercu.png"></a>';
   echo '<a href="'.$Home.'/Admin/Article/Nouveau/?id='.$Article->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a>';
   if ($Article->statue==1) { ?>
        <a title="Désactiver" href="<?php echo $Home; ?>/Admin/Article/desactiver.php?id=<?php echo $Article->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/desactiver.png" alt="Désactiver"></a>
  <?php } else { ?>
        <a title="Activer" href="<?php echo $Home; ?>/Admin/Article/activer.php?id=<?php echo $Article->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/activer.png" alt="Activer"></a>
  <?php } 
        echo '<a title="Supprimer" href="'.$Home.'/Admin/Article/supprimer.php?id='.$Article->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a></td></tr>';
}
?>
</table>

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>