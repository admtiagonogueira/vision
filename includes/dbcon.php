<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>

<?php
try {

	$host = 'localhost';
	$database = '';
	$user = '';
	$pass = '';
	
	$conx = new PDO('mysql:host='.$host.';dbname='.$database.';charset=utf8', $user, $pass);
	$conx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
	header ('Content-type: text/html; charset=UTF-8');
	exit('<h2>Erro de conexão com o Banco de Dados!</h2>');
}
?>