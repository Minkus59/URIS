<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

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

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

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

<BR /><HR /><BR />

<table class="Admin">
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
      </td>
</tr>
<?php
}
?>
</table>

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>