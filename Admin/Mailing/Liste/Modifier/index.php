<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

if ($Cnx_Admin!=TRUE) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Id=$_GET['id'];

$SelectContact=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste WHERE id=:id");
$SelectContact->bindParam(':id', $Id, PDO::PARAM_STR);
$SelectContact->execute();
$Contact=$SelectContact->fetch(PDO::FETCH_OBJ);

if (isset($_POST['Modifier'])) {
    $Categorie= trim($_POST['categorie']);
    $Alumni= trim($_POST['alumni']);
    $Statut= trim($_POST['statut']);
    $Nom=trim($_POST['nom']);
    $Prenom=trim($_POST['prenom']);
    $Email=FiltreEmail('email');

    if ($Email[0]===false) {
        $Erreur=$Email[1];
        ErreurLog($Erreur);
    }
    else {
        $Update=$cnx->prepare("UPDATE ".$Prefix."neuro_mailing_Liste SET categorie=:categorie, alumni=:alumni, statut=:statut, nom=:nom, prenom=:prenom, email=:email WHERE id=:id");
        $Update->bindParam(':id', $Id, PDO::PARAM_STR);
        $Update->bindParam(':categorie', $Categorie, PDO::PARAM_STR);
        $Update->bindParam(':alumni', $Alumni, PDO::PARAM_STR);
        $Update->bindParam(':statut', $Statut, PDO::PARAM_STR);
        $Update->bindParam(':nom', $Nom, PDO::PARAM_STR);
        $Update->bindParam(':prenom', $Prenom, PDO::PARAM_STR);
        $Update->bindParam(':email', $Email, PDO::PARAM_STR);
        $Update->execute();

        $Valid="Contact modifié avec succès";
        header('Location:'.$Home.'/Admin/Mailing/Liste/?valid='.urlencode($Valid));
    }
}

?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".urldecode($Valid)."</font><BR />"; } ?>
   
<H1>Modifier un contact</H1>   

<form name="form_modif" action="" method="POST">
<select name="categorie">
    <option value="">- Categorie -</option>
    <option value="Mi" <?php if ($Contact->categorie=="Mi") { echo "selected"; } ?> >Mi</option>
    <option value="Pers morale"<?php if ($Contact->categorie=="Pers morale") { echo "selected"; } ?> >Pers morale</option>
    <option value="Membre associé"<?php if ($Contact->categorie=="Membre associé") { echo "selected"; } ?> >Membre associé</option>
    <option value="Junior"<?php if ($Contact->categorie=="Junior") { echo "selected"; } ?> >Junior</option>
    <option value="Institution"<?php if ($Contact->categorie=="Institution") { echo "selected"; } ?> >Institution</option>
</select><BR />
<input type="text" name="alumni" value="<?php echo $Contact->alumni; ?>" placeholder="Alumni :"/><BR />
<select name="statut">
    <option value="">- Statut -</option>
    <option value="Président"<?php if ($Contact->statut=="Président") { echo "selected"; } ?> >Président</option>
    <option value="Trésorier"<?php if ($Contact->statut=="Trésorier") { echo "selected"; } ?> >Trésorier</option>
    <option value="Secrétaire"<?php if ($Contact->statut=="Secrétaire") { echo "selected"; } ?> >Secrétaire</option>
    <option value="Webmaster"<?php if ($Contact->statut=="Webmaster") { echo "selected"; } ?> >Webmaster</option>
</select><BR />
<input type="text" name="nom" value="<?php echo $Contact->nom; ?>" placeholder="Nom :"/><BR />
<input type="text" name="prenom" value="<?php echo $Contact->prenom; ?>" placeholder="Prénom :"/><BR />
<input type="text" name="email" value="<?php echo $Contact->email; ?>" placeholder="Email :"/><BR /><BR />

<input type="submit" name="Modifier" value="Modifier"/>
</form>

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>