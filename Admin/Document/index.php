<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

if ($Cnx_Admin===false) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];

$SelectDocument=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Document ORDER BY libele ASC");
$SelectDocument->execute();

?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>
<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".urldecode($Valid)."</font><BR />"; }   ?>

<H1>Ajouter un document</H1>

<div id="fileuploader">Document</div>
<div class="ajax-file-upload-green" id="extrabutton">Start Upload</div>
<script>

var extraObj = $("#fileuploader").uploadFile({
    url:"<?php echo $Home; ?>/Admin/lib/script/upload.php",
    fileName:"document",
    extraHTML:function()
    {
        var html = "<div><b>Libellé : </b><input type='text' name='libele' value='' /> <br/>";
        html += "</div>";
        return html;    		
    },
    autoSubmit:false,
    returnType: "json"
});

$("#extrabutton").click(function() {
    extraObj.startUpload();
});
 
</script>

<BR /><HR /><BR />

<H1>Document</H1>

<table class="Admin">
<tr><th>Libellé</th><th>Type</th><th>Aperçu</th><th>Lien</th><th>Action</th></tr>
<?php

while ($Document=$SelectDocument->fetch(PDO::FETCH_OBJ)) {
      echo "<TR>";
      echo "<TD>".$Document->libele."</TD>";
      echo "<TD>".$Document->type."</TD>";
      echo "<TD>";
      if ($Document->type=="Image") {
          $Taille=getimagesize($Document->lien);
          if (($Taille[0]>250)||($Taille[1]>250)) {
            if ($Taille[0]>=$Taille[1]) {
                echo "<img width='250px' src='$Document->lien'/>";
            }
            else {
                echo "<img width='250px' src='$Document->lien'/>";
            }
        }
        else {
            echo "<img src='$Document->lien'/>";
        }
      }
      echo "</TD>";
      echo "<TD>".$Document->lien."</TD>";
      echo "<TD>";
      if ($Document->type=="PDF") {
        echo "<a href='$Document->lien'><img src='$Home/Admin/lib/img/apercu.png'></a>";
      }
      if ($Document->type=="PPS") {
        echo "<a href='$Document->lien'><img src='$Home/Admin/lib/img/apercu.png'></a>";
      }
      echo "<a href='$Home/Admin/Document/supprimer.php?id=$Document->id'><img src='$Home/Admin/lib/img/supprimer.png'></a>";
      echo "</TD></TR>";
}
?>
</table>

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>