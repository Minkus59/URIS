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

if (isset($_POST['StatueSocial'])) {
   $_SESSION['StatueSocial']=$_POST['StatueSocial'];
}

if ((!isset($_SESSION['StatueSocial']))||($_SESSION['StatueSocial']=="NULL")) {
     $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Social ORDER BY id ASC");
     $Select->execute();
}
else {
     $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Social WHERE statue=:statue ORDER BY id ASC");
     $Select->bindParam(':statue', $_SESSION['StatueSocial'], PDO::PARAM_STR);
     $Select->execute();
}

$SelectPage=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Social");
$SelectPage->execute();
    
?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>
<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".urldecode($Valid)."</font><BR />"; }   ?>

<H1>Liste des liens Sociaux</H1>

<form name="FormSocial" action="" method="POST">
<select name="StatueSocial" required="required" onChange="this.form.submit()">  
<option value="NULL" <?php if ($_SESSION['StatueSocial']=="NULL") { echo "selected"; } ?>>Tous</option>
<option value='1' <?php if ($_SESSION['StatueSocial']== "1") { echo "selected"; } ?>>Actif</option>
<option value='0' <?php if ($_SESSION['StatueSocial']== "0") { echo "selected"; } ?>>Inactif</option>
</select>
</form>

<table class="Admin">
<tr><th>Image</th><th>Lien</th><th>Action</th></tr>
<?php

while ($Image=$Select->fetch(PDO::FETCH_OBJ)) {
?>
   <tr <?php if ($Image->statue==0) { echo "class='rouge'"; } else { echo "class='vert'"; } ?>>
   <td><?php echo "<img width='40px' src='".$Image->logo."'/>"; ?></td>
   <td><?php echo $Image->lien; ?></td>
   <td><?php echo '<a href="'.$Home.'/Admin/Social/Nouveau/?id='.$Image->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a>';
   if ($Image->statue==1) { ?>
        <a title="Désactiver" href="<?php echo $Home; ?>/Admin/Social/desactiver.php?id=<?php echo $Image->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/desactiver.png" alt="Désactiver"></a>
  <?php } else { ?>
        <a title="Activer" href="<?php echo $Home; ?>/Admin/Social/activer.php?id=<?php echo $Image->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/activer.png" alt="Activer"></a>
  <?php } 
        echo '<a title="Supprimer" href="'.$Home.'/Admin/Social/supprimer.php?id='.$Image->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a></td></tr>';
}
?>
</table>

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>