<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/nouveau.inc.php");

if ($Cnx_Admin===false) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];

if (isset($_POST['StatueCompte'])) {
   $_SESSION['StatueCompte']=$_POST['StatueCompte'];
}

if ((!isset($_SESSION['StatueCompte']))||($_SESSION['StatueCompte']=="NULL")) {
  $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Admin ORDER BY id DESC");
  $Select->execute();
}
else {
  $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Admin WHERE valider=:valider ORDER BY id DESC");
  $Select->bindParam(':valider', $_SESSION['StatueCompte'], PDO::PARAM_STR);
  $Select->execute();
}

    
?>

<!-- ************************************
*** Script réalisé par NeuroSoft Team ***
********* www.neuro-soft.fr *************
**************************************-->

<!DOCTYPE html>
<html>
<head>
<title>NeuroSoft Team - Accès PRO</title>

<META http-equiv="Content-Type" content="text/html;charset=utf-8"> 
<META name="robots" content="noindex, nofollow">

<META name="author".content="NeuroSoft Team">
<META name="publisher".content="Helinckx Michael">
<META name="reply-to" content="contact@neuro-soft.fr">

<META name="viewport" content="width=device-width" >                                                            


<link rel="shortcut icon" href="<?php echo $Home; ?>/Admin/lib/img/icone.ico">

<link rel="stylesheet" type="text/css" media="screen AND (max-width: 480px)" href="<?php echo $Home; ?>/lib/css/misenpatel.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 480px) AND (max-width: 960px)" href="<?php echo $Home; ?>/lib/css/misenpatab.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 960px)" href="<?php echo $Home; ?>/lib/css/misenpapc.css" >

</head>

<body>
<CENTER>
<header>
<div id="int">
<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/head.inc.php"); ?>
</div>
</header>
<div id="MenuAdmin">
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>
</div>

<div id="Center">

<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } ?>

<H1>Liste des comptes administrateurs</H1></p>

<form name="FormStatue" action="" method="POST">
<label class="col_2"  for="StatueCompte">Statut<font color='#FF0000'>*</font> :</label>
<select name="StatueCompte" required="required" onChange="this.form.submit()">
<option value="NULL" <?php if ($_SESSION['StatueCompte']=="NULL") { echo "selected"; } ?>>Tous</option>
<option value="1" <?php if ($_SESSION['StatueCompte']=="1") { echo "selected"; } ?>>Activé</option>
<option value="0" <?php if ($_SESSION['StatueCompte']=="0") { echo "selected"; } ?>>Désactivé</option>
</select>
</form>

<p><HR /></p>

<table>
<tr><th>Nom</th><th>E-mail</th><th>Administrateur</th><th>Date</th><th>Action</th></tr>

<?php
while($Info=$Select->fetch(PDO::FETCH_OBJ)) {
?>

<tr <?php if ($Info->valider==1) { echo "class='vert'"; } else { echo "class='rouge'";} ?> >
<td><?php echo $Info->nom; ?></td>
<td><?php echo $Info->email; ?></td>
<td><?php echo $Info->admin; ?></td>
<td><?php echo $Info->created; ?></td>
          <td>
          <?php if ($Info->valider==1) { ?>
                <a title="Désactiver" href="<?php echo $Home; ?>/Admin/CompteAdmin/desactiver.php?id=<?php echo $Info->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/desactiver.png" alt="Désactiver"></a>
          <?php } else { ?>
                <a title="Activer" href="<?php echo $Home; ?>/Admin/CompteAdmin/activer.php?id=<?php echo $Info->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/activer.png" alt="Activer"></a>
          <?php } ?>
          <?php if ($Info->admin==1) { ?>
                <a title="Enlever le titre d'administrateur" href="<?php echo $Home; ?>/Admin/CompteAdmin/denommer.php?id=<?php echo $Info->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/admin.png" alt="Enlever le titre d'administrateur"></a>
          <?php } else { ?>
                <a title="Nommer Administrateur" href="<?php echo $Home; ?>/Admin/CompteAdmin/nommer.php?id=<?php echo $Info->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/admin.png" alt="Nommer Administrateur"></a>
          <?php } ?>
          <a title="Supprimer" href="<?php echo $Home; ?>/Admin/CompteAdmin/supprimer.php?id=<?php echo $Info->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/supprimer.png" alt="Supprimer"></a>
          <a title="Selection Ehpad" href="<?php echo $Home; ?>/Admin/Relation/?id=<?php echo $Info->hash; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/liste.png" alt="Selection Ehpad"></a>
          </td></tr>
<?php
}
?>

</table>

</article>
</CENTER>
</body>

</html>