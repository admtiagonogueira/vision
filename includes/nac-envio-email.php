<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php require_once('mail.php'); ?>
<?php verifyLevel('nac'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('ENVIO DE E-MAILS'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Configuração de Envio'); ?>

<?php
$form_envio_nivel = isset($_POST['form-envio-nivel']) ? $_POST['form-envio-nivel'] : '';
$form_envio_unico = isset($_POST['form-envio-unico']) ? $_POST['form-envio-unico'] : '';
$form_envio_assunto = isset($_POST['form-envio-assunto']) ? $_POST['form-envio-assunto'] : '';
$form_envio_msg = isset($_POST['form-envio-msg']) ? nl2br($_POST['form-envio-msg']) : '';
$username = $_SESSION['user_username'];

if (isset($_POST['submited']) && !empty($form_envio_msg) && !empty($form_envio_assunto)){
	
	if (!empty($form_envio_nivel) && empty($form_envio_unico)){
		
		//Caso seja e-mail em massa
		switch($form_envio_nivel){
			case 'loc':
				$query = '
						  SELECT a.e_mail FROM fj_lider_equipe a
						  INNER JOIN fj_q_igreja b ON (a.fk_q_igreja_id = b.id)
						  WHERE b.catedral = \'N\'
						  ';
				$destinatario = 'NÍVEL LOCAL';
				break;
			case 'est':
				$query = 'SELECT e_mail FROM fj_lider_estado';
				$destinatario = 'NÍVEL ESTADUAL';
				break;
		}
		
		try{
			$rs = $conx->prepare($query);
			$rs->execute();
			
			$envios = 0;
			$erros = 0;
			
			while ($row = $rs->fetch(PDO::FETCH_ASSOC)){
				if (sendEmail($row['e_mail'], $form_envio_assunto, $form_envio_msg)){
					$envios++;
				} else {
					$erros++;
				}
			}
			
			try{
				$rs = $conx->prepare('INSERT INTO fj_email VALUES(null, ?, ? ,? ,?, ?, ?)');
				$rs->bindParam(1, $username);
				$rs->bindParam(2, $form_envio_assunto);
				$rs->bindParam(3, $form_envio_msg);
				$rs->bindParam(4, $form_envio_nivel);
				$data_hora = date('Y-m-d H:i:s');
				$rs->bindParam(5, $data_hora);
				$rs->bindParam(6, $destinatario);
				$rs->execute();
			} catch (PDOException $e) {
				getDivResult(PAG_QUERY_ERR, DIV_ERR);
			}
			
			getDivResult('Envios com sucesso: ' . $envios . ' | Erros: ' . $erros, DIV_WAR);
		} catch (PDOException $e) {
			getDivResult(PAG_QUERY_ERR, DIV_ERR);
		}
		
	} elseif (!empty($form_envio_unico) && empty($form_envio_nivel)){
		
		//Caso seja e-mail único
		if (sendEmail($form_envio_unico, $form_envio_assunto, $form_envio_msg)){
			getDivResult(EMAIL_SEND_OK, DIV_OK);
			
			try{
				$rs = $conx->prepare('INSERT INTO fj_email VALUES(null, ?, ? ,? ,?, ?, ?)');
				$rs->bindParam(1, $username);
				$rs->bindParam(2, $form_envio_assunto);
				$rs->bindParam(3, $form_envio_msg);
				$nivel = 'uni';
				$rs->bindParam(4, $nivel);
				$data_hora = date('Y-m-d H:i:s');
				$rs->bindParam(5, $data_hora);
				$rs->bindParam(6, $form_envio_unico);
				$rs->execute();
			} catch (PDOException $e) {
				getDivResult(PAG_QUERY_ERR, DIV_ERR);
			}
		} else {
			getDivResult(EMAIL_SEND_ERR, DIV_ERR);
		}
		
	} else {
		getDivResult(PAG_EMPTY_ERR, DIV_ERR);
	}

} elseif (isset($_POST['submited']) && (empty($form_envio_msg) || empty($form_envio_assunto))){
	getDivResult(PAG_EMPTY_ERR, DIV_ERR);
}
?>

<form method="POST" action="/nac-envio-email">
<table id="vstable">
	<tr>
		<td>Tipo<span class="ast">*</span>:</td>
		<td>
			<input type="radio" name="radio-envio" id="radio-envio-massa" checked="checked" />Envio em massa 
			<input type="radio" name="radio-envio" id="radio-envio-unico" />Envio único
		</td>
	</tr>
	<tr id="tr-envio-massa">
		<td>Nível<span class="ast">*</span>:</td>
		<td>
			<select name="form-envio-nivel" id="sel-nivel" class="form-selects">
				<option value="">-- Selecione uma opção --</option>
				<option value="loc">NÍVEL LOCAL</option>
				<option value="est">NÍVEL ESTADUAL</option>
			</select>
		</td>
	</tr>
	<tr id="tr-envio-unico">
		<td>E-mail<span class="ast">*</span>:</td>
		<td>
			<input type="text" name="form-envio-unico" id="txt-email" maxlength="90" />
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

<?php getSubTitulo('E-mails enviados'); ?>

<?php
//Busca registros cadastrados
try{
	$rs = $conx->prepare('SELECT DATE_FORMAT(data_time, \'%d/%m/%Y %H:%i:%s\') AS data_time, assunto, destinatario, msg FROM fj_email ORDER BY data_time DESC');
	$rs->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<table class="tab_pagina">
	<thead>
		<tr>
			<td>Envio</td>
			<td>Assunto</td>
			<td>Destinatário</td>
			<td>#</td>
		</tr>
	</thead>
	<tbody>
<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
		<tr>
			<td><?php echo $row['data_time']; ?></td>
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
<script>
	//Configurações de envio, inicialmente para envio e massa
	$("#tr-envio-unico").css("visibility", "hidden");
	$("#tr-envio-unico #txt-email").attr("name", "");
	
	$("#radio-envio-massa").on("click", function(e){
		$("#tr-envio-unico").css("visibility", "hidden");
		$("#tr-envio-massa").css("visibility", "visible");
		$("#tr-envio-unico #txt-email").attr("name", "");
		$("#tr-envio-massa #sel-nivel").attr("name", "form-envio-massa");
	});
	$("#radio-envio-unico").on("click", function(e){
		$("#tr-envio-massa").css("visibility", "hidden");
		$("#tr-envio-unico").css("visibility", "visible");
		$("#tr-envio-massa #sel-nivel").attr("name", "");
		$("#tr-envio-unico #txt-email").attr("name", "form-envio-unico");
	});
</script>