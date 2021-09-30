<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

if ($Cnx_Admin!=TRUE) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];

$SelectMail=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Historique ORDER BY id DESC");
$SelectMail->execute();  

if (isset($_POST['Modifier'])) {
    $Selection=$_POST['selection'];
    $Compteur=count($Selection);

    for($u=0;$u<$Compteur;$u++) {
        $delete=$cnx->prepare("DELETE FROM ".$Prefix."neuro_mailing_Historique WHERE id=:id");
        $delete->bindParam(':id', $Selection[$u], PDO::PARAM_STR);
        $delete->execute();
    }

    $Valid="E-mail supprimer avec succès";
    header('Location:'.$Home.'/Admin/Mailing/Historique/?valid='.urlencode($Valid));
}
?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".urldecode($Valid)."</font><BR />"; } ?>

<H1>Liste des e-mails envoyé</H1>

<table class="Admin">
<form name="liste" action="" method="POST">
<tr>
    <th>Destinataire</th>
    <th>Objet</th>
    <th>Modèle</th>
    <th>Date</th>
    <th>Action</th>
</tr>
<?php

while ($Mail=$SelectMail->fetch(PDO::FETCH_OBJ)) {
$pattern ='/[, ]/';
$replace = '<BR />';
$Adresse = preg_replace($pattern, $replace, $Mail->destinataire);
?>
   <tr>
   <td><?php echo $Adresse; ?></td>
   <td><?php echo $Mail->objet; ?></td>
   <td><?php echo $Mail->type; ?></td>
   <td><?php echo date("d-m-Y", $Mail->created); ?></td>
    <td>
    <input type="checkbox" name="selection[]" value="<?php echo $Mail->id; ?>"/>
        <?php echo '<a title="Supprimer" href="'.$Home.'/Admin/Mailing/Historique/supprimer.php?id='.$Mail->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a></td></tr>'; ?>
    </td>
   </tr>
    <?php
}
?>

<tr>
    <td></td><td></td><td></td><td></td>
    <td>
        Tout cocher : <input type="checkbox" onclick="cocher1()" />
    </td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td>
    <td>
        <input type="submit" name="Modifier" value="Supprimer" class="normal"/>
    </td>
</tr>
</table>

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>