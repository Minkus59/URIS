<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

if ($Cnx_Admin===false) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid']; 

$RecupEmail=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Parametre");
$RecupEmail->execute();
$Info=$RecupEmail->fetch(PDO::FETCH_OBJ);

if (isset($_POST['Valider'])) {
    
    $Email=FiltreEmail('email');
    
    if($Email[0]===false) {
        $Erreur=$Email[1];
    }
    else {
        $VerifEmail=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Parametre");
        $VerifEmail->execute();
        $Rows=$VerifEmail->rowCount();
        
        if ($Rows==0) {
            $InsertEmail=$cnx->prepare("INSERT INTO ".$Prefix."neuro_mailing_Parametre (email) VALUES (:email)");
            $InsertEmail->bindParam(':email', $Email, PDO::PARAM_STR);
            $InsertEmail->execute();  

            $Valid="Enregistrement réussie";
            header("location:".$Home."/Admin/Mailing/Param/?valid=".$Valid);
        }
        else {
            $InsertEmail=$cnx->prepare("UPDATE ".$Prefix."neuro_mailing_Parametre SET email=:email");
            $InsertEmail->bindParam(':email', $Email, PDO::PARAM_STR);
            $InsertEmail->execute();

            $Valid="Enregistrement réussie";
            header("location:".$Home."/Admin/Mailing/Param/?valid=".$Valid);
        }
    }
}
?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>
<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".urldecode($Erreur)."</font><BR /><BR />"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".urldecode($Valid)."</font><BR /><BR />"; }   ?>

<H1>Paramètre</H1>

Veuillez saisir un adresse e-mail pour les retours d'e-mail <BR /><BR />

<form name="FormEmail" action="" method="POST">   
    <input name="email" type="email" value="<?php echo $Info->email; ?>"/><BR /><BR />
    
    <input type="submit" name="Valider" value="Valider" class="normal"/>
</form> 

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>