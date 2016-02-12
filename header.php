<?php /* Sessão já iniciada no index.php */ ?>
<?php /* Ativa/Desativa a visualização de erros */
	ini_set('display_errors', true);
	error_reporting(E_ALL);
	//error_reporting(E_ALL ^ E_WARNIN);
?>
<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: acesso.php'); exit(); } ?>
<?php require_once('includes/dbcon.php'); ?>
<?php require_once('includes/functions.php'); ?>
<?php
date_default_timezone_set('America/Belem');
setlocale(LC_ALL,'pt_BR.UTF8');
mb_internal_encoding('UTF8'); 
mb_regex_encoding('UTF8');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Vision - .:Painel:.</title>
		
		<?php //<!-- Favicon --> ?>
		<link rel="icon" type="image/gif" href="favicon.gif" >

		<?php //<!-- CSS --> ?>
		<link href="css/reset.css" rel="stylesheet" type="text/css" />
		<link href="scripts/jquery-ui-1.11.4.custom/jquery-ui.min.css" rel="stylesheet" type="text/css" />
		<link href="DataTables/DataTables-1.10.6/media/css/jquery.dataTables.css" rel="stylesheet" type="text/css" />
		<link href="scripts/orbit-1.2.3/orbit-1.2.3.css" rel="stylesheet" type="text/css" />
		<link href="css/geral.css" rel="stylesheet" type="text/css" />
		
		<?php //<!-- Javascript --> ?>
		<script src="scripts/jquery-1.11.2.min.js" type="text/javascript"></script>
		<script src="scripts/jquery-ui-1.11.4.custom/jquery-ui.min.js" type="text/javascript"></script>
		<script src="scripts/jquery.inputmask.js" type="text/javascript"></script>
		<script src="DataTables/DataTables-1.10.6/media/js/jquery.dataTables.js" type="text/javascript"></script>
		<script src="scripts/orbit-1.2.3/jquery.orbit-1.2.3.min.js" type="text/javascript"></script>
		<script src="scripts/barraUniversalV2.js" type="text/javascript"></script><!-- Barra Universal -->
		<script src="scripts/geral.js" type="text/javascript"></script>
		
		<?php //<!-- MetaDados --> ?>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="cache-control" content="no-store, no-cache, must-revalidate, Post-Check=0, Pre-Check=0" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="0" />
	</head>
	<body>
		<div id="all">
			<div id="topo-header">
				<div id="social">
				<img src="img/social-facebook.png" />
				<img src="img/social-twitter.png" />
				<img src="img/social-google.png" />
				<img src="img/social-youtube.png" />
				</div>
			</div>
			
			<div id="topo-barra-menu">
			Olá, <?php echo $_SESSION['nome']; ?>. Útimo acesso: <?php echo date('d/m/Y', strtotime($_SESSION['user_ult_acesso'])); ?> às <?php echo date('H:i:s', strtotime($_SESSION['user_ult_acesso'])); ?>.
			<a href="logout.php" alt="Sair" id="link-logout"><span id="botao-logout">Sair</span></a>
			</div>

			<div id="corpo">