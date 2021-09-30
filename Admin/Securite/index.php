<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

if (isset($_POST['Recevoir'])) {

    $Email=FiltreEmail('email');
    $Hash=md5(uniqid(rand(), true));

    if ($Email[0]===false) {
         $Erreur=$Email[1];
    }
    else {

         $VerifEmail=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Admin WHERE email=:email");
         $VerifEmail->bindParam(':email', $Email, PDO::PARAM_STR);
         $VerifEmail->execute();
         $NbRowsEmail=$VerifEmail->rowCount();
         $Data=$VerifEmail->fetch(PDO::FETCH_OBJ);

         $Client=$Data->hash_client;
       
         $VerifSecu=$cnx->prepare("SELECT (email) FROM ".$Prefix."neuro_Admin_secu_mdp WHERE email=:email");
         $VerifSecu->bindParam(':email', $Email, PDO::PARAM_STR);
         $VerifSecu->execute();
         $NbRowsClient=$VerifSecu->rowCount();
    
         if ($NbRowsClient==1) {
            $Erreur="Une procédure de changement de mot de passe à déjà été demander !<br />";
            ErreurLog($Erreur);
         }
        
        elseif ($NbRowsEmail!=1) {          
            $Erreur="Cette adresse n'existe pas !<br />";
            $Erreur.='<input type=button value=Retour onclick=javascript:history.back()><br />'; 
            ErreurLog($Erreur);   
        }

        else {
            $InsertHash=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Admin_secu_mdp (hash, email, created) VALUES (:hash, :email, NOW())");
            $InsertHash->bindParam(':hash', $Hash, PDO::PARAM_STR);
            $InsertHash->bindParam(':email', $Email, PDO::PARAM_STR);
            $InsertHash->execute();
        
            $Body ="<html><head><title>Changement de mot de passe</title>
                </head><body>
                <font color='#9e2053'><H1>Procédure de changement de mot de passe</H1></font>           
                Veuillez cliquer sur le lien suivant pour changer votre mot de passe sur www.neuro-soft.fr .<BR /><BR />                       
                <a href='".$Home."/Admin/Validation/Mdp/?id=$Email&hash=$Hash'>Cliquez ici</a><BR /><BR />
                ____________________________________________________<BR /><BR />
                Cordialement<br />
                www.neuro-soft.fr<BR /><BR />
                <font color='#FF0000'>Cet e-mail contient des informations confidentielles et / ou protégées par la loi. Si vous n'en êtes pas le véritable destinataire ou si vous l'avez reçu par erreur, informez-en immédiatement son expéditeur et détruisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font>                 
                </body></html>";

            if (EnvoiNotification($Nom, $Email, "Demande de contact", $Body, $EmailDestinataire->email)==false) {
                $Erreur="L'e-mail de confirmation n'a pu etre envoyé, vérifiez que vous l'avez entré correctement !<br />";
                $Erreur.='<input type=button value=Retour onclick=javascript:history.back()><br />';           
                ErreurLog($Erreur);  
            }
            else {
                $Valid="Un E-mail de confirmation vous a été envoyé à l'adresse suivante : ".$Email."<br />";
            }
         }
     }
}
?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article class="ArticleAccueilAdmin">

<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".urldecode($Valid)."</font><BR />"; }   ?>

<H1>Changement de mot de passe</H1>

<form id="form_email" action="" method="POST">
<input type="email" placeholder="Adresse e-mail" name="email"required="required"/><img src="<?php echo $Home; ?>/Admin/lib/img/intero.png" title="Adresse e-mail saisie lors de la création du compte"/>
<BR />
<input type="submit" name="Recevoir" value="Recevoir"/>
</form>
</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>