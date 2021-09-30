<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

if ($Cnx_Admin===false) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];

$Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE sous_menu='0' ORDER BY position ASC");
$Select->execute();
    
?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>
<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".urldecode($Valid)."</font><BR />"; }   ?>

<H1>Mémo</H1>

Les élément du tableau sont détailler ci-dessous :
<ul>
<li><b>Position :</b> Indique la position d'affichage dans le menu, sous-menu, sous-sous-menu; 0 Indique que cette page ne peut être positionné par l'utilisateur, elle est positionné de façon stratégique.</li>   
<li><b>Libélé :</b> Le texte du bouton afficher dans le menu.</li>
<li><b>Lien de page :</b> Le lien de la page sur le domaine.</li>
<li><b>Titre :</b> Le titre de la page (utilisé pour le référencement (ne pas modifier)).</li>
<li><b>Description :</b> La description de la page (utilisé pour le référencement (ne pas modifier)).</li>
<li><b>Date de création :</b> La date de création de la page.</li>
<li><b>Action :</b> Les actions a réaliser sur la page.</li>
</ul>

<H2>Liste des pages</H2>

<table class="Admin">
<tr>
      <th>Position</th>
      <th>Libellé</th>
      <th>Lien de page</th>
      <th>Titre</th>
      <th>Description</th>
      <th>Date de création</th>
      <th>Action</th>
      </tr>
<?php

while ($Page=$Select->fetch(PDO::FETCH_OBJ)) {
      $SelectSousMenu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE parrin=:parrin AND sous_menu='1' ORDER BY position ASC");
      $SelectSousMenu->bindParam(':parrin', $Page->lien, PDO::PARAM_STR);
      $SelectSousMenu->execute();
      $CountSousMenu=$SelectSousMenu->rowCount();
      ?>
      <tr <?php if ($Page->statue==0) { echo "class='rouge'"; } else { echo "class='vert'"; } ?>>
      <td><?php echo $Page->position; ?></td>
      <td><?php echo $Page->libele; ?></td>
      <td><?php echo $Home.$Page->lien; ?></td>
      <td><?php echo $Page->titre; ?></td>
      <td><?php echo $Page->description; ?></td>
      <td><?php echo date("d-m-Y", time($Page->created)); ?></td>
      <td><?php 
      echo '<a title="Apperçu" href="'.$Home.$Page->lien.'"><img src="'.$Home.'/Admin/lib/img/apercu.png"></a>';
      echo '<a href="'.$Home.'/Admin/Page/Nouveau/?id='.$Page->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a>';
      if ($Page->statue==1) { ?>
            <a title="Désactiver" href="<?php echo $Home; ?>/Admin/Page/desactiver.php?id=<?php echo $Page->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/desactiver.png" alt="Désactiver"></a>
      <?php } else { ?>
            <a title="Activer" href="<?php echo $Home; ?>/Admin/Page/activer.php?id=<?php echo $Page->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/activer.png" alt="Activer"></a>
      <?php } 
      echo '<a title="Supprimer" href="'.$Home.'/Admin/Page/supprimer.php?id='.$Page->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a></td></tr>';
      
      if ($CountSousMenu>0) {
            while ($SousMenu=$SelectSousMenu->fetch(PDO::FETCH_OBJ)) { 
                  $SelectSousSousMenu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE parrin=:parrin AND sous_menu='1' ORDER BY position ASC");
                  $SelectSousSousMenu->bindParam(':parrin', $SousMenu->lien, PDO::PARAM_STR);
                  $SelectSousSousMenu->execute();
                  $CountSousSousMenu=$SelectSousSousMenu->rowCount();

                  ?>
                  <tr <?php if ($SousMenu->statue==0) { echo "class='rouge'"; } else { echo "class='vert'"; } ?>>
                  <td><?php echo $SousMenu->position; ?></td>
                  <td><?php echo $SousMenu->libele; ?></td>
                  <td><?php echo $Home.$SousMenu->lien; ?></td>
                  <td><?php echo $SousMenu->titre; ?></td>
                  <td><?php echo $SousMenu->description; ?></td>
                  <td><?php echo date("d-m-Y", time($SousMenu->created)); ?></td>
                  <td><?php 
                  echo '<a title="Apperçu" href="'.$Home.$SousMenu->lien.'"><img src="'.$Home.'/Admin/lib/img/apercu.png"></a>';
                  echo '<a href="'.$Home.'/Admin/Page/Nouveau/?id='.$SousMenu->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a>';
                  if ($SousMenu->statue==1) { ?>
                        <a title="Désactiver" href="<?php echo $Home; ?>/Admin/Page/desactiver.php?id=<?php echo $SousMenu->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/desactiver.png" alt="Désactiver"></a>
                  <?php } else { ?>
                        <a title="Activer" href="<?php echo $Home; ?>/Admin/Page/activer.php?id=<?php echo $SousMenu->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/activer.png" alt="Activer"></a>
                  <?php } 
                  echo '<a title="Supprimer" href="'.$Home.'/Admin/Page/supprimer.php?id='.$SousMenu->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a></td></tr>';

                  if ($CountSousSousMenu>0) {
                        while ($SousSousMenu=$SelectSousSousMenu->fetch(PDO::FETCH_OBJ)) {
                              ?>
                              <tr <?php if ($SousSousMenu->statue==0) { echo "class='rouge'"; } else { echo "class='vert'"; } ?>>
                              <td><?php echo $SousSousMenu->position; ?></td>
                              <td><?php echo $SousSousMenu->libele; ?></td>
                              <td><?php echo $Home.$SousSousMenu->lien; ?></td>
                              <td><?php echo $SousSousMenu->titre; ?></td>
                              <td><?php echo $SousSousMenu->description; ?></td>
                              <td><?php echo date("d-m-Y", time($SousSousMenu->created)); ?></td>
                              <td><?php 
                              echo '<a title="Apperçu" href="'.$Home.$SousSousMenu->lien.'"><img src="'.$Home.'/Admin/lib/img/apercu.png"></a>';
                              echo '<a href="'.$Home.'/Admin/Page/Nouveau/?id='.$SousSousMenu->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a>';
                              if ($SousSousMenu->statue==1) { ?>
                                    <a title="Désactiver" href="<?php echo $Home; ?>/Admin/Page/desactiver.php?id=<?php echo $SousSousMenu->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/desactiver.png" alt="Désactiver"></a>
                              <?php } else { ?>
                                    <a title="Activer" href="<?php echo $Home; ?>/Admin/Page/activer.php?id=<?php echo $SousSousMenu->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/activer.png" alt="Activer"></a>
                              <?php } 
                              echo '<a title="Supprimer" href="'.$Home.'/Admin/Page/supprimer.php?id='.$SousSousMenu->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a></td></tr>';
                        }
                  }
            }
      }
      ?><tr><td colspan="7"></tr><?php
}
?>
</table>

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>