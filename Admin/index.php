<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid']; 
$Now=time();

if (isset($_POST['OK'])) {

    $Email=FiltreEmail('email');
    $Mdp=FiltreMDP('mdp');
    
    if ($Email[0]===false) {
       $Erreur=$Email[1];
    }

    elseif ($Mdp[0]===false) {
       $Erreur=$Mdp[1]; 
    }
    else {
        $RecupClient=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Admin WHERE email=:email");
        $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
        $RecupClient->execute();
        $RecupC=$RecupClient->fetch(PDO::FETCH_OBJ);
        $NbRowsEmail=$RecupClient->rowCount();

        if ($NbRowsEmail!=1) {
            $Erreur="Cette adresse E-mail ne correspond à aucun compte !<br />";
            ErreurLog($Erreur);
        }
        elseif ($RecupC->activate!=1) {
            $Erreur="le compte n'est pas activé, veuillez activer votre compte avant de vous connecter!<br />";
            $Erreur.="Lors de votre inscription un e-mail vous a été envoyé<br />";
            $Erreur.="Veuillez valider votre adresse e-mail en cliquant sur le lien.<br />";
            $Erreur.="vous pouvais toujours recevoir le mail a nouveau en cliquant sur ' recevoir '<br />";
            $Erreur.="<form action='' method='post'/><input type='hidden' name='email' value='".$RecupC->email."'/><input type='submit' name='Recevoir' value='Recevoir'/></form></p>";
            ErreurLog($Erreur);
        }
        elseif ($RecupC->valider!=1) {
            $Erreur="Le compte n'a pas été activé par l'administrateur, veuillez patienter !<br />";
            ErreurLog($Erreur);
        }
        else {
            $Salt=md5($RecupC->created);
            $Mdp=md5($Mdp);
            $MdpCrypt=crypt($Mdp, $Salt);

            $Mdp=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Admin WHERE mdp=:mdp AND email=:email");
            $Mdp->bindParam(':mdp', $MdpCrypt, PDO::PARAM_STR);
            $Mdp->bindParam(':email', $Email, PDO::PARAM_STR);
            $Mdp->execute();
            $nb_rows=$Mdp->rowCount();

            if ($nb_rows!=1) { 
                $Erreur="Le mot de passe ne correspond pas à cette adresse e-mail !<br />";
                ErreurLog($Erreur);
            }
            else {                 
                $_SESSION['NeuroAdmin']=$RecupC->hash;
                $Valid="Vous êtes connecté ";
                header("location:".$Home."/Admin/?valid=".urlencode($Valid));
            } 
        }
    }
}

if ((isset($_POST['Recevoir']))&&($_POST['Recevoir']=="Recevoir")) {
    
    $Email=trim($_POST['email']);
    $Body ="<H1>Validation d'inscription</H1></font>          
        Veuillez cliquer sur le lien suivant pour valider votre inscription.</p>
        <a href='".$Home."/Admin/Validation/?id=".$Email."&Valid=1'>Cliquez ici</a></p>
        ____________________________________________________</p>
        Cordialement</p>
        <font color='#FF0000'>Cet e-mail contient des informations confidentielles et / ou protégées par la loi. Si vous n'en êtes pas le véritable destinataire ou si vous l'avez reçu par erreur, informez-en immédiatement son expéditeur et détruisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font>";

    $header = "MIME-Version: 1.0\n";
    $header .= "Content-Type:multipart/mixed; boundary=\"$boundary\"\n";
    $header .= "From: \"$Societe\"<$EmailDestinataire->email>\n";
    $header .= "\n";
    
    $message="Ce message est au format MIME.\n";
    
    $message.="--$boundary\n";
    $message.= "Content-Type: text/html; charset=utf-8\n";
    
    $message.="\n";

    $message.="<html><head><title>".$Titre."</title>
    </head><body>
    <table style='width: 800px;' cellspacing='0' cellpadding='0'>
    <tbody>
    <tr>
    <td style='background-color: #ced6e1;'><img src='http://www.ingenieurs-scientifiques-npdc.fr/lib/Photo/9fc6b3a4.png' alt='En-tete' width='800' /></td>
    </tr>
    <tr>
    <td style='background-color: #ced6e1;'><br />".$Body."<br /><br /></td>
    </tr>
    <tr>
    <td style='text-align: center; background-color: #ced6e1;'><img src='http://www.ingenieurs-scientifiques-npdc.fr/lib/Photo/d9c69c3a.png' alt='' width='800' /></td>
    </tr>
    </tbody>
    </table>
    </body></html>";
    
    $message.="\n\n";
    
    $message.="--$boundary--\n";

    if (!mail($Email, "Validation d'inscription", $message, $header)) {                  
        $Erreur="L'e-mail de confirmation n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
    }
                
    else {
        $Erreur="Un E-mail de confirmation vous a été envoyé à l'adresse suivante : ".$Email."<br />";
        $Erreur.="Veuillez valider votre adresse e-mail avant de vous connecter !</p>";                 
    }
}
?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article class="ArticleAccueilAdmin">

<?php if ($Cnx_Admin==true) { ?>
    <div id="CompteClient"><?php
        echo '<a href="'.$Home.'/Admin/Mon-compte/">Bonjour '.$Admin->nom.'</a>'; ?>
     </div>
<?php } ?>

<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".urldecode($Valid)."</font><BR />"; }

if ($Cnx_Admin==false) { ?>

    <H1>Connexion</H1>

    <form name="form_cnx" action="" method="POST">
    <input name="email" type="email" placeholder="E-mail" required="required"/><img src="<?php echo $Home; ?>/Admin/lib/img/intero.png" title="Adresse e-mail saisie lors de la création du compte"/>
    <br />
    <input name="mdp" type="password" placeholder="Mot de passe" required="required"/><img src="<?php echo $Home; ?>/Admin/lib/img/intero.png" title="Mot de passe saisie lors de la création du compte"/>
    <BR /><BR />
    <input type="submit" name="OK" value="OK"/>
    </form>
    <label><a href="<?php echo $Home; ?>/Admin/Securite/">Mot de passe oublié ?</a></label></label> - 
    <label><a href="<?php echo $Home; ?>/Admin/Inscription/">Inscription</a></label><BR /><BR />
<?php }
else { ?>
    <a href="<?php echo $Home; ?>/Admin/lib/script/deconnexion.php">Déconnexion</a>
<?php }
?>
</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>