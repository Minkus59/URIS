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

if (isset($_POST['StatueEvent'])) {
   $_SESSION['StatueEvent']=$_POST['StatueEvent'];
}

if ((!isset($_SESSION['StatueEvent']))||($_SESSION['StatueEvent']=="NULL")) {
     $SelectCalendrier=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Calendrier ORDER BY created ASC");
     $SelectCalendrier->execute();
}
else {
     $SelectCalendrier=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Calendrier WHERE statue=:statue ORDER BY created ASC");
     $SelectCalendrier->bindParam(':statue', $_SESSION['StatueEvent'], PDO::PARAM_STR);
     $SelectCalendrier->execute();
}
    
?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>
<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".urldecode($Valid)."</font><BR />"; }   ?>

<H1>Liste des événements du calendrier</H1>

<form name="FormEvent" action="" method="POST">
<select name="StatueEvent" required="required" onChange="this.form.submit()">    
<option value="NULL" <?php if ($_SESSION['StatueEvent']=="NULL") { echo "selected"; } ?>>Tous</option>
<option value='1' <?php if ($_SESSION['StatueEvent']== "1") { echo "selected"; } ?>>Actif</option>
<option value='0' <?php if ($_SESSION['StatueEvent']== "0") { echo "selected"; } ?>>Inactif</option>
</select>
</form>

<table class="Admin">
<tr><th>Date</th><th>Titre</th><th>Description</th><th>Lien</th><th>Action</th></tr>
<?php
while ($Evenement=$SelectCalendrier->fetch(PDO::FETCH_OBJ)) {
?>
   <tr <?php if ($Evenement->statue==0) { echo "class='rouge'"; } else { echo "class='vert'"; } ?>>
   <td><?php echo date("d-m-Y G:i", $Evenement->created); ?></td>
   <td><?php echo $Evenement->titre; ?></td>
   <td><?php echo $Evenement->description; ?></td>
   <td><?php echo $Evenement->lien; ?></td>
   <td><?php echo '<a href="'.$Home.'/Admin/Calendrier/Nouveau/?id='.$Evenement->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a>';
   if ($Evenement->statue==1) { ?>
        <a title="Désactiver" href="<?php echo $Home; ?>/Admin/Calendrier/desactiver.php?id=<?php echo $Evenement->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/desactiver.png" alt="Désactiver"></a>
  <?php } else { ?>
        <a title="Activer" href="<?php echo $Home; ?>/Admin/Calendrier/activer.php?id=<?php echo $Evenement->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/activer.png" alt="Activer"></a>
  <?php } 
        echo '<a title="Supprimer" href="'.$Home.'/Admin/Calendrier/supprimer.php?id='.$Evenement->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a></td></tr>';
}
?>
</table>

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>