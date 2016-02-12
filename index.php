<?php session_start(); ?>

<?php
if ($_SESSION['logado'] == true){
	require_once('header.php');
	require_once('content.php');
	require_once('footer.php');
} else {
	$conx = null;
	session_destroy();
	header('Location: acesso.php');
	exit();
}
?>