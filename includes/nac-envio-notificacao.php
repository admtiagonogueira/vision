<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php require_once('mail.php'); ?>
<?php verifyLevel('nac'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('ENVIO DE NOTIFICAÇÕES'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Configuração de Envio'); ?>

<?php
$form_envio_nivel = isset($_POST['form-envio-nivel']) ? $_POST['form-envio-nivel'] : '';
$form_envio_assunto = isset($_POST['form-envio-assunto']) ? $_POST['form-envio-assunto'] : '';
$form_envio_msg = isset($_POST['form-envio-msg']) ? nl2br($_POST['form-envio-msg']) : '';
$username = $_SESSION['user_username'];
$nome_user = $_SESSION['nome'];

if (isset($_POST['submited']) && !empty($form_envio_msg) && !empty($form_envio_assunto) && !empty($form_envio_nivel)){
	
	switch($form_envio_nivel){
		case 'loc':
			$destinatario = 'NÍVEL LOCAL';
			break;
		case 'est':
			$destinatario = 'NÍVEL ESTADUAL';
			break;
	}
	
	try{
		$rs = $conx->prepare('INSERT INTO fj_notificacao VALUES(null, ?, ?, ?, ?, ?, ?, ?)');
		$rs->bindParam(1, $username);
		$rs->bindParam(2, $nome_user);
		$rs->bindParam(3, $form_envio_assunto);
		$rs->bindParam(4, $form_envio_msg);
		$rs->bindParam(5, $form_envio_nivel);
		$data_hora = date('Y-m-d H:i:s');
		$rs->bindParam(6, $data_hora);
		$rs->bindParam(7, $destinatario);
		$rs->execute();
		
		getDivResult(PAG_COMMIT_OK, DIV_OK);
	} catch (PDOException $e) {
		getDivResult(PAG_COMMIT_ERR, DIV_ERR);
	}
} elseif (isset($_POST['submited']) && (empty($form_envio_msg) || empty($form_envio_assunto) || empty($form_envio_nivel))){
	getDivResult(PAG_EMPTY_ERR, DIV_ERR);
}
?>

<form method="POST" action="/nac-envio-notificacao">
<table id="vstable">
	<tr>
		<td>Nível<span class="ast">*</span>:</td>
		<td>
			<select name="form-envio-nivel" class="form-selects">
				<option value="">-- Selecione uma opção --</option>
				<option value="loc">NÍVEL LOCAL</option>
				<option value="est">NÍVEL ESTADUAL</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2"><br />Assunto:</td>
	</tr>
	<tr>
		<td colspan="2">
        	<input type="text" name="form-envio-assunto" maxlength="90" />
		</td>
	</tr>
	<tr>
		<td colspan="2"><br />Mensagem:</td>
	</tr>
	<tr>
		<td colspan="2">
        	<textarea name="form-envio-msg" maxlength="3000" cols="50" rows="10"></textarea>
		</td>
	</tr>
</table>
	<input type="submit" name="submited" value="Enviar" />
	<input type="reset" value="Limpar" />
</form>

<?php getSubTitulo('Notoficações enviadas'); ?>

<?php
//Busca registros cadastrados
try{
	$rs = $conx->prepare('SELECT DATE_FORMAT(data_time, \'%d/%m/%Y %H:%i:%s\') AS data_time, nome_user, assunto, destinatario, msg FROM fj_notificacao ORDER BY data_time DESC');
	$rs->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<table class="tab_pagina">
	<thead>
		<tr>
			<td>Envio</td>
			<td>Enviado por</td>
			<td>Assunto</td>
			<td>Destinatário</td>
			<td>#</td>
		</tr>
	</thead>
	<tbody>
<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
		<tr>
			<td><?php echo $row['data_time']; ?></td>
			<td><?php echo $row['nome_user']; ?></td>
			<td><?php echo $row['assunto']; ?></td>
			<td><?php echo $row['destinatario']; ?></td>
			<td>
				<button class="abrir-dialog-ui">visualizar</button>
				<!-- Modal -->
				<div class="newdialog-boxes">
					<div class="newdialog"><a href="#" class="close"><img src="img/fechar-modal.png" /></a><br />
						<div>
							<h4 class="newdialog-titulo">Visualização</h4>
							<?php echo $row['msg']; ?>
						</div>
					</div>
				</div>
				<!-- Modal //-->
			</td>
		</tr>
<?php endwhile; ?>
	</tbody>
</table>

<?php /* JQuery */ ?>