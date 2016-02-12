<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php verifyLevel('est'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('EDITOR DE PERFIL'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Dados Cadastrais'); ?>

<?php
/** Verifica, insere, etc, tudo aqui */
$form_nome = isset($_POST['form-nome']) ? $_POST['form-nome'] : '';
$form_email = isset($_POST['form-email']) ? $_POST['form-email'] : '';
$form_senha_atual = isset($_POST['form-senha-atual']) ? $_POST['form-senha-atual'] : '';
$form_nova_senha = isset($_POST['form-nova-senha']) ? $_POST['form-nova-senha'] : '';
$form_nome = anti_injection($form_nome);
$form_email = anti_injection_noCase($form_email);
$form_senha_atual = anti_injection_noCase($form_senha_atual);
$form_nova_senha = anti_injection_noCase($form_nova_senha);
$form_id_lider_estado = $_SESSION['lider_estado_id'];
$form_id_estado = $_SESSION['estado'];

/* Update de perfil */
//Verifica se usuario digitou alguma coisa
if (!empty($form_nome) && !empty($form_email)){

	try{
		//Faz a inserção
		$rs = $conx->prepare('UPDATE fj_lider_estado SET nome=?, e_mail=?');
		$rs->bindParam(1, $form_nome);
		$rs->bindParam(2, $form_email);
		$rs->execute();

		//Se a inserção foi bem-sucedida, mostra mensagem
		getDivResult(PAG_UPDATE_OK, DIV_OK);
	} catch (PDOException $e) {
		getDivResult(PAG_UPDATE_ERR, DIV_ERR); // Caso a inserção do registro falhe
	}

} else if (
	isset ($_POST['submited']) &&
		(
			empty($form_nome) ||
			empty($form_email)
		 )
	) {
	//Caso campo esteja em branco
	getDivResult(PAG_EMPTY_ERR, DIV_ERR);
}

/**************************************************************************************/

/* Update de usuário e senha */
//Verifica se usuario digitou alguma coisa
if (!empty($form_senha_atual) && !empty($form_nova_senha)){

	//Verifica se a senha atual confere para poder atualizar a senha antiga
	$rs = $conx->prepare('SELECT password FROM fj_usuario WHERE fk_lider_estado_id=?');
	$rs->bindParam(1, $form_id_lider_estado);
	$rs->execute();
	$rowPasswd = $rs->fetchAll(PDO::FETCH_ASSOC);

	if ($rowPasswd[0]['password'] == md5($form_senha_atual)){
		try{
			//Faz a inserção
			$rs = $conx->prepare('UPDATE fj_usuario SET password=? where fk_lider_estado_id=?');
			$rs->bindParam(1, md5($form_nova_senha));
			$rs->bindParam(2, $form_id_lider_estado);
			$rs->execute();

			//Se a inserção foi bem-sucedida, mostra mensagem
			getDivResult(PASS_UPDATE_OK, DIV_OK);
		} catch (PDOException $e) {
			getDivResult(PASS_UPDATE_ERR, DIV_ERR); // Caso a inserção do registro falhe
		}
	} else {
		getDivResult(PASS_VERIFY_ERR, DIV_ERR); // Caso a senha atual digitada não confira com a do banco de dados
	}

} else if (
	isset ($_POST['submited']) &&
		(
			empty($form_senha_atual) ||
			empty($form_nova_senha)
		 )
	) {
	//Caso campo esteja em branco
	getDivResult(PASS_UPDATE_WAR, DIV_WAR);
}
?>

<?php
//Busca dados do perfil
try{
	$rs = $conx->prepare('
						  SELECT a.nome, a.e_mail, b.nome_estado FROM fj_lider_estado a
						  INNER JOIN fj_estado b ON (a.fk_estado_id = b.id)
						  WHERE a.id=?
						  ');
	$rs->bindParam(1, $form_id_lider_estado);
	$rs->execute();
	$rs = $rs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<form method="POST" action="/est-edt-perfil">
<table id="vstable">
	<tr>
		<td>Nome:</td>
		<td colspan="3">
        	<input type="text" maxlength="90" placeholder="" name="form-nome" class="form-nome" value="<?php echo $rs[0]['nome']; ?>" />
		</td>
	</tr>
	<tr>
		<td>Estado:</td>
		<td>
			<input type="text" placeholder="" name="form-estado" class="form-text-120" value="<?php echo $rs[0]['nome_estado']; ?>" disabled="disabled" />
		</td>
		<td>E-mail:</td>
		<td>
			<input type="text" maxlength="90" placeholder="" name="form-email" class="form-email" value="<?php echo $rs[0]['e_mail']; ?>" />
		</td>
	</tr>
	<tr>
		<td>Senha atual:</td>
		<td>
			<input type="password" maxlength="50" placeholder="" name="form-senha-atual" class="form-text-120" />
		</td>
		<td>Nova senha:</td>
		<td>
			<input type="password" maxlength="32" placeholder="" name="form-nova-senha" class="form-text-120" />
		</td>
	</tr>
</table>
	<input type="submit" name="submited" value="Atualizar" />
</form>