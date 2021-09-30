<?php 
$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
?>

<div id="Content">
<?php if (isset($Mise_a_jour)) { ?>
    <div id="Information">
    <?php echo $Mise_a_jour; ?>
    </div>
<?php } ?> 

<article>
<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".urldecode($Erreur)."</font></p>"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".urldecode($Valid)."</font></p>"; }   ?>

<H1>Contact</H1>

<BR />
<table width="100%">
    <tr>
        <td>
            <a href='<?php echo $ParamLogoHeader->lien; ?>'><img src="<?php echo $ParamLogoHeader->logo; ?>"/></a>
        </td>
        <td>
            <img src='<?php echo $Home; ?>/lib/img/adresse.png'/> <?php echo $InfoDivers->adresse; ?><BR />
            <img src='<?php echo $Home; ?>/lib/img/mail.png'/> <?php echo $EmailDestinataire->email; ?>
        </td>
    </tr>
</table>
<BR />

Pour toutes questions :<BR />
Merci de bien vouloir préciser vos coordonnées et votre demande.<BR /><BR /> 

<form name="form_contact" action="/lib/script/contact.php" method="POST">

<input type="text" value="<?php if (isset($_SESSION['nom'])) { echo $_SESSION['nom']; } ?>" name="nom" placeholder="Nom / Prénom*" required="required"><BR />
<input type="text" value="<?php if (isset($_SESSION['tel'])) { echo $_SESSION['tel']; } ?>" name="tel" placeholder="Numéro de téléphone*" required="required"/><BR />
<input type="text" value="<?php if (isset($_SESSION['cp'])) { echo $_SESSION['cp']; } ?>" name="cp" placeholder="Code postal*" required="required"/><BR /><BR />
<input type="text" value="<?php if (isset($_SESSION['sujet'])) { echo $_SESSION['sujet']; } ?>" name="sujet" placeholder="Sujet*" required="required"/><BR />
<textarea cols="40" rows="10" name="message" placeholder="Message*" required="required"><?php if (isset($_SESSION['message'])) { echo $_SESSION['message']; } ?></textarea><BR />
<input type="email" value="<?php if (isset($_SESSION['email'])) { echo $_SESSION['email']; } ?>" name="email" placeholder="Votre adresse e-mail*" required="required"/><BR /><BR />
<input type="submit" value="envoyer" name="envoyer"/>

</form><BR /><BR />

<font color='#FF0000'>*</font> : Informations requises<BR /><BR />

</article>

<?php
if ($Count>0) {

    while($Actu=$RecupArticle->fetch(PDO::FETCH_OBJ)) { 

        echo '
        <article>';

        echo $Actu->message;
        if ($Cnx_Admin==true) { 
            echo '<a href="'.$Home.'/Admin/Article/Nouveau/?id='.$Actu->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a><a href="'.$Home.'/Admin/Article/supprimer.php?id='.$Actu->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a>';
        } 
        echo '</article>';
    }
}
?>

</div>

