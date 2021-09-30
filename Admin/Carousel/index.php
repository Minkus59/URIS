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

if (isset($_POST['StatueCarousel'])) {
   $_SESSION['StatueCarousel']=$_POST['StatueCarousel'];
}

if ((!isset($_SESSION['StatueCarousel']))||($_SESSION['StatueCarousel']=="NULL")) {
     $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Carousel ORDER BY position ASC");
     $Select->execute();
}
else {
     $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Carousel WHERE statue=:statue ORDER BY position ASC");
     $Select->bindParam(':statue', $_SESSION['StatueCarousel'], PDO::PARAM_STR);
     $Select->execute();
}

$SelectPage=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Carousel");
$SelectPage->execute();
    
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
<li><b>Image :</b> Un aperçu de l'image.</li>  
<li><b>Position :</b> Indique la position d'affichage dans le carousel.</li>   
<li><b>Lien :</b> Le lien interne de l'image.</li>
<li><b>Description :</b>Contien la balise alternative de l'image (Pour le référencement, et les non-voyants).</li>
<li><b>Action :</b> Les actions a réaliser sur l'image.</li>
</ul>

<H2>Liste des images du carousel</H2>

<form name="FormCarousel" action="" method="POST">
<select name="StatueCarousel" required="required" onChange="this.form.submit()">  
<option value="NULL" <?php if ($_SESSION['StatueCarousel']=="NULL") { echo "selected"; } ?>>Tous</option>
<option value='1' <?php if ($_SESSION['StatueCarousel']== "1") { echo "selected"; } ?>>Actif</option>
<option value='0' <?php if ($_SESSION['StatueCarousel']== "0") { echo "selected"; } ?>>Inactif</option>
</select>
</form>

<table class="Admin">
<tr><th>Image</th><th>Position</th><th>Lien</th><th>Description</th><th>Action</th></tr>
<?php

while ($Image=$Select->fetch(PDO::FETCH_OBJ)) {
?>
   <tr <?php if ($Image->statue==0) { echo "class='rouge'"; } else { echo "class='vert'"; } ?>>
   <td><?php echo "<img width='220px' src='".$Image->lien."'/>"; ?></td>
   <td><?php echo $Image->position; ?></td>
   <td><?php echo $Image->lien; ?></td>
   <td><?php echo $Image->alt; ?></td>
   <td><?php echo '<a href="'.$Home.'/Admin/Carousel/Nouveau/?id='.$Image->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a>';
   if ($Image->statue==1) { ?>
        <a title="Désactiver" href="<?php echo $Home; ?>/Admin/Carousel/desactiver.php?id=<?php echo $Image->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/desactiver.png" alt="Désactiver"></a>
  <?php } else { ?>
        <a title="Activer" href="<?php echo $Home; ?>/Admin/Carousel/activer.php?id=<?php echo $Image->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/activer.png" alt="Activer"></a>
  <?php } 
        echo '<a title="Supprimer" href="'.$Home.'/Admin/Carousel/supprimer.php?id='.$Image->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a></td></tr>';
}
?>
</table>

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>