<!DOCTYPE HTML>
<html>

<head>   
<meta charset="utf-8">
<title><?php echo $SOEPage->titre; ?></title>
<meta name="category" content="<?php echo $SOEPage->libele; ?>" />
<meta name="description" content="<?php echo $SOEPage->description; ?>" />
<meta name="robots" content="index, follow"/>
<meta name="author" content="NeuroSoft Team"/>
<meta name="publisher" content="<?php echo $InfoDivers->publisher; ?>"/>
<meta name="viewport" content="width=device-width, initial-scale=0.7" />

<meta content="<?php echo $Societe; ?>" property="og:site_name"/>
<meta content="fr_FR" property="og:locale"/>
<meta content="article" property="og:type"/>
<meta content="<?php echo $SOEPage->titre ?>" property="og:title"/>
<meta content="<?php echo $Home.$_SERVER['SCRIPT_URL']; ?>" property="og:url"/>

<link rel="shortcut icon" href="<?php echo $Home; ?>/lib/img/icone.ico" />
<link rel="stylesheet" type="text/css" media="screen AND (max-width: 960px)" href="<?php echo $Home; ?>/lib/css/responsive.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 961px)" href="<?php echo $Home; ?>/lib/css/misenpapc.css" >

<!-- Calendrier -->
<link rel="stylesheet" href="/lib/calendar/css/eventCalendar.css">
<link rel="stylesheet" href="/lib/calendar/css/eventCalendar_theme_responsive.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $Home; ?>/lib/js/analys.js"></script>
<script src="/lib/calendar/js/moment.js" type="text/javascript"></script>
<script src="/lib/calendar/js/jquery.eventCalendar.js" type="text/javascript"></script>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/cookie.inc.php"); ?>
</head>