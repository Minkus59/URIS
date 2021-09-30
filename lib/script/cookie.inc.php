<?php 
if ((!$_SESSION['cookie'])||($_SESSION['cookie']!=1)) { ?>
<div id="cookie">
<form name="cookie" action="<?php echo $Home; ?>/lib/script/FermeCookie.php" method="POST">
Bienvenue ! En poursuivant votre navigation, vous acceptez l'utilisation de cookies. <a href="<?php echo $Home; ?>/Mentions-legales/">En savoir plus</a><input type="hidden" name="page" value="<?php echo $Home.$_SERVER['REQUEST_URI']; ?>"/><input class="cookie" type="submit" name="OK" value="OK"/>
</form>
</div>
<?php } ?>

