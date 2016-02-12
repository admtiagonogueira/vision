<?php
session_start();
if ($_SESSION['logado'] != true){ $conx = null; header('Location: acesso.php'); exit(); }
	
$p = isset($_POST['p']) ? $_POST['p'] : false;

if ($p != false) {
	include_once('includes/'.$p.'.php');
} else {
	echo 'Ocorreu um erro na inclusão da página!';
}
?>