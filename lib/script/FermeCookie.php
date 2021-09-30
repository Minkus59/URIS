<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

session_start();
if (!$_SESSION['cookie']) { 
    $_SESSION['cookie']=1;
} 
header("location:".$_POST['page']);
?>