<?php require_once('includes/dbcon.php'); ?>
<?php require_once('includes/functions.php'); ?>
<?php require_once('includes/login/const.php'); ?>
<?php
session_start();
$user = isset($_POST['user']) ? $_POST['user'] : '';
$pass = isset($_POST['pass']) ? $_POST['pass'] : '';
$user = anti_injection_noCase($user);
$pass = anti_injection_noCase($pass);
$pass = md5($pass);

if ($user != '' && $pass != '') {
	try{
		$rs = $conx->prepare('SELECT * FROM fj_usuario WHERE username=? AND password=? LIMIT 1');
		$rs->bindParam(1, $user);
		$rs->bindParam(2, $pass);
		$rs->execute();
		$row = $rs->fetchAll();
		$numRows = count($row);
		
		//Se achou um usuário com a query
		if ($numRows === 1){
			foreach( $row as $r ){
				$id = $r['id'];
				$fk_lider_equipe = $r['fk_lider_equipe_id'];
				$fk_lider_estado = $r['fk_lider_estado_id'];
				$fk_lider_nacional = $r['fk_lider_nacional_id'];
				$fk_dev = $r['fk_dev_id'];
				$username = $r['username'];
				$level = $r['level'];
				$ult_acesso = $r['ult_acesso'];
				$ativo = $r['ativo'];
				$data_atual = date('Y-m-d H:i:s');
			}
			
			//Verifica se o usuário está ativo
			if ($ativo != 'S'){
				msgLogin(ERR_USU_BLOQ);
			}

			//Grava as sessões de usuário
			$_SESSION['user_id'] = $id;
			$_SESSION['user_username'] = $username;
			$_SESSION['user_level'] = $level;
			$_SESSION['user_ult_acesso'] = $ult_acesso;
			$_SESSION['logado'] = true;
			
			//Atualiza o último acesso (tabela fj_usuario)
			$rs = $conx->prepare('UPDATE fj_usuario SET ult_acesso=? WHERE id=?');
			$rs->bindParam(1, $data_atual);
			$rs->bindParam(2, $id);
			$rs->execute();
			
			//Grava o acesso no log (tabela fj_log_acesso)
			//$rs = $conx->prepare('INSERT INTO fj_log_acesso VALUES(null, ?, ?, ?)');
			//$rs->bindParam(1, $username);
			//$rs->bindParam(2, $data_atual);
			//$rs->bindParam(3, get_client_ip());
			//$rs->execute();
			
			//Muda query de busca, conforme o nível e recupera os dados
			if ($level == 'loc')
			{//LOCAL
				try {
					$rs = $conx->prepare('
					SELECT a.nome, a.fk_q_igreja_id, a.fk_bairro_id, a.fk_regiao_id, a.fk_cidade_id, a.fk_estado_id, b.id AS equipe_id FROM fj_lider_equipe a
					INNER JOIN fj_equipe b ON (a.id = b.fk_lider_equipe_id)
					WHERE  a.id = ? LIMIT 1
					');
					$rs->bindParam(1, $fk_lider_equipe);
					$rs->execute();
					$row = $rs->fetchAll();
						foreach( $row as $r ){
							$_SESSION['nome'] = $r['nome'];
							$_SESSION['lider_equipe'] = $fk_lider_equipe;
							$_SESSION['equipe'] = $r['equipe_id'];
							$_SESSION['igreja'] = $r['fk_q_igreja_id'];
							$_SESSION['bairro'] = $r['fk_bairro_id'];
							$_SESSION['regiao'] = $r['fk_regiao_id'];
							$_SESSION['cidade'] = $r['fk_cidade_id'];
							$_SESSION['estado'] = $r['fk_estado_id'];
						}
				} catch (PDOException $e) {
					msgLogin(ERR_IDENT_USU);
				}
			}
			else if ($level == 'est')
			{//ESTADUAL
				try {
					$rs = $conx->prepare('
					SELECT nome, fk_estado_id FROM fj_lider_estado
					WHERE  id = ? LIMIT 1
					');
					$rs->bindParam(1, $fk_lider_estado);
					$rs->execute();
					$row = $rs->fetchAll();
						foreach( $row as $r ){
							$_SESSION['lider_estado_id'] = $fk_lider_estado;
							$_SESSION['nome'] = $r['nome'];
							$_SESSION['estado'] = $r['fk_estado_id'];
						}
				} catch (PDOException $e) {
					msgLogin(ERR_IDENT_USU);
				}
			}
			else if ($level == 'nac')
			{//NACIONAL
				try {
					$rs = $conx->prepare('SELECT nome FROM fj_lider_nacional WHERE  id = ? LIMIT 1'
					);
					$rs->bindParam(1, $fk_lider_nacional);
					$rs->execute();
					$row = $rs->fetchAll();
						foreach( $row as $r ){
							$_SESSION['lider_nacional_id'] = $fk_lider_nacional;
							$_SESSION['nome'] = $r['nome'];
						}
				} catch (PDOException $e) {
					msgLogin(ERR_IDENT_USU);
				}
			}
			else if ($level == 'dev')
			{//DESENVOLVEDOR
				try {
					$rs = $conx->prepare('SELECT nome FROM fj_dev WHERE  id = ? LIMIT 1');
					$rs->bindParam(1, $fk_dev);
					$rs->execute();
					$row = $rs->fetchAll();
						foreach( $row as $r ){
							$_SESSION['nome'] = $r['nome'];
						}
				} catch (PDOException $e) {
					msgLogin(ERR_IDENT_USU);
				}
			}
			else
			{
				msgLogin(ERR_IDENT_LEVEL);
			}

			header('Location: '.get_home());
			exit();			
			
		} else {
			msgLogin(ERR_VERIFY_DIG);
		}
		
	} catch (PDOException $e){
		exit('<h3>Erro de conexão com o Banco de Dados!</h3>');
	}
} else {
	msgLogin(ERR_VERIFY_DIG);
}
?>