<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

if ($Cnx_Ok===false) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Id=$_GET['id'];

$Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Admin WHERE hash=:hash");
$Select->BindParam(":hash", $SessionAdmin, PDO::PARAM_STR);
$Select->execute();
$Info=$Select->fetch(PDO::FETCH_OBJ);

if (isset($_POST['Modifier'])) {
     $Email = FiltreEmail('email');
     $Nom = $_POST['nom'];

     if ($Email[0]===false) {
        $Erreur=$Email[1];
        ErreurLog($Erreur);
     }
     elseif ((trim($Nom))=="") {
        $Erreur="Veuillez saisir un nom de client !<br />";
        ErreurLog($Erreur);
     }
     else {
        $Insert=$cnx->prepare("UPDATE ".$Prefix."neuro_compte_Admin SET nom=:nom ,email=:email WHERE hash=:hash");
        $Insert->BindParam(":nom", $Nom, PDO::PARAM_STR);
        $Insert->BindParam(":email", $Email, PDO::PARAM_STR);
        $Insert->BindParam(":hash", $SessionAdmin, PDO::PARAM_STR);
        $Insert->execute();

        if (!$Insert) {
            $Erreur="Erreur serveur, veuillez réessayer ultèrieurement !";
        }
        else  {     
            $Valid="Compte modifier avec succès";
            header('location:'.$Home.'/Admin/Mon-compte/?valid='.urlencode($Valid));
        }
    }
} 

?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>

<?php if ($Cnx_Admin==true) { ?>
    <div id="CompteClient"><?php
        echo '<a href="'.$Home.'/Admin/Mon-compte/">Bonjour '.$Admin->nom.'</a>'; ?>
     </div>
<?php } ?>

<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } ?>

<H1>Créer un compte</H1>

<form name="form_inscription" action="" method="POST">
<label class="col_2">Nom du compte<font color='#FF0000'>*</font> :</label>
<input type="text" name="nom" required="required" value="<?php echo $Info->nom; ?>"/><BR />

<label class="col_2">Adresse E-mail<font color='#FF0000'>*</font> :</label>
<input type="email" name="email" required="required" value="<?php echo $Info->email; ?>"/>
<br />
<br />
<input type="submit" name="Modifier" value="Modifier"/>
</form>

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>