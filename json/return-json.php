<?php
session_start();
if ($_SESSION['logado'] != true){ header('Location: ../acesso.php'); exit(); }
header('Content-type: text/json');
require_once('../includes/dbcon.php');
require_once('../includes/functions.php');
?>

<?php
$opt = anti_injection_noCase($_POST['opt']);
$arg = (int) anti_injection_noCase($_POST['arg']);

$continue = true;

switch ($opt){
	case 'cid':
				$sql = 'SELECT id, nome_cidade as cid FROM fj_cidade WHERE fk_estado_id=?';
				break;
	case 'blc':
				$sql = 'SELECT id, nome_bloco as blc FROM fj_bloco WHERE fk_cidade_id=?';
				break;
	case 'reg':
				$sql = 'SELECT id, nome_regiao as reg FROM fj_regiao WHERE fk_cidade_id=?';
				break;
	case 'bai':
				$sql = 'SELECT id, nome_bairro as bai FROM fj_bairro WHERE fk_cidade_id=?';
				break;
	case 'igr':
				$sql = 'SELECT id, nome_igreja as igr FROM fj_q_igreja WHERE fk_bairro_id=?';
				break;
	case 'igrbyreg':
				$sql = 'SELECT id, nome_igreja as igr FROM fj_q_igreja WHERE fk_regiao_id=?';
				break;
	case 'tri':
				$sql = 'SELECT id, nome_tribo as tri FROM fj_tribo WHERE fk_equipe_id=?';
				break;
	case 'lde':
				$sql = 'SELECT id, nome as lde FROM fj_lider_equipe WHERE fk_q_igreja_id=?';
				break;
	case 'ldt':
				$sql = 'SELECT id, nome as ldt FROM fj_lider_tribo WHERE fk_equipe_id=?';
				break;
	case 'equ':
				$sql = 'SELECT id, nome_equipe as equ FROM fj_equipe WHERE fk_q_igreja_id=?';
				break;
	default:
				$continue = false;
}

if ($continue){
	$rs = $conx->prepare($sql);
	$rs->bindParam(1, $arg);
	$rs->execute();
	echo json_encode($rs->fetchAll(PDO::FETCH_ASSOC));
}

exit();
?>