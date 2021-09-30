<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");
 

if (isset($_POST['search'])) { 
    $Search=$_POST['search'];

    $RecupSearch=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Article WHERE statue='1' AND message LIKE :search");
    $RecupSearch->execute(array(':search' => "%".$Search."%"));
    $CountSearch=$RecupSearch->rowCount();       
}    
?>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/header.inc.php"); ?>

<section>
<div id="Center">
<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/nav.inc.php"); ?>

<div id="Content">
   
<?php
if ($CountSearch>0) {
    while($SearchArticle=$RecupSearch->fetch(PDO::FETCH_OBJ)) { 

        echo '<article>';
        echo $SearchArticle->message;
        echo '</article>';
    }
}
else {
    echo '
    <article>Aucun article trouvé !<BR /><BR />';
    echo '</article>';
}
?>

</div>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/asideDroite.inc.php"); ?>
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/piedPage.inc.php"); ?>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
