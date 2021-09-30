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

if (isset($_POST['StatueFlash'])) {
   $_SESSION['StatueFlash']=$_POST['StatueFlash'];
}

if ((!isset($_SESSION['StatueFlash']))||($_SESSION['StatueFlash']=="NULL")) {
     $SelectFlashInfo=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_FlashInfo ORDER BY position ASC");
     $SelectFlashInfo->execute();
}
else {
     $SelectFlashInfo=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_FlashInfo WHERE statue=:statue ORDER BY position ASC");
     $SelectFlashInfo->bindParam(':statue', $_SESSION['StatueFlash'], PDO::PARAM_STR);
     $SelectFlashInfo->execute();
}
    
?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>
<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".urldecode($Valid)."</font><BR />"; }   ?>

<H1>Mémo</H1>

Les élément du tableau sont détailler ci-dessous :
<ul>
<li><b>Position :</b> Indique la position d'affichage.</li>   
<li><b>Description :</b> Le texte à afficher.</li>
<li><b>Action :</b> Les actions a réaliser sur le FlashInfo.</li>
</ul>

<H2>Liste des flash infos</H2>

<form name="FormFlash" action="" method="POST">
<select name="StatueFlash" required="required" onChange="this.form.submit()">    
<option value="NULL" <?php if ($_SESSION['StatueFlash']=="NULL") { echo "selected"; } ?>>Tous</option>
<option value='1' <?php if ($_SESSION['StatueFlash']== "1") { echo "selected"; } ?>>Actif</option>
<option value='0' <?php if ($_SESSION['StatueFlash']== "0") { echo "selected"; } ?>>Inactif</option>
</select>
</form>

<table class="Admin">
<tr><th>Position</th><th>Description</th><th>Action</th></tr>
<?php
while ($Flash=$SelectFlashInfo->fetch(PDO::FETCH_OBJ)) {
?>
   <tr <?php if ($Flash->statue==0) { echo "class='rouge'"; } else { echo "class='vert'"; } ?>>
   <td><?php echo $Flash->position; ?></td>
   <td><?php echo $Flash->description; ?></td>
   <td><?php echo '<a href="'.$Home.'/Admin/FlashInfo/Nouveau/?id='.$Flash->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a>';
   if ($Flash->statue==1) { ?>
        <a title="Désactiver" href="<?php echo $Home; ?>/Admin/FlashInfo/desactiver.php?id=<?php echo $Flash->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/desactiver.png" alt="Désactiver"></a>
  <?php } else { ?>
        <a title="Activer" href="<?php echo $Home; ?>/Admin/FlashInfo/activer.php?id=<?php echo $Flash->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/activer.png" alt="Activer"></a>
  <?php } 
        echo '<a title="Supprimer" href="'.$Home.'/Admin/FlashInfo/supprimer.php?id='.$Flash->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a></td></tr>';
}
?>
</table>

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>