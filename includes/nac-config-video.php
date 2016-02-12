<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php verifyLevel('nac'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('CONFIGURAÇÃO DO MURAL'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Gerenciamento de Imagens'); ?>

<p>
<span style="font-weight:bold;">OBS:</span> Vídeos com tamanhos diferentes de 398x298 píxels serão redimensionadas automaticamente.
</p><br />

<?php
//Insere imagem
if (isset($_POST['submited']) && isset($_POST['form-video-url']) && $_POST['form-video-url'] != ''){
	$video_url = $_POST['form-video-url'];
	$video_nivel = isset($_POST['form-video-nivel']) && $_POST['form-video-nivel'] != '' ? $_POST['form-video-nivel'] : 'est';

	try{
		//Faz o update
		$rs = $conx->prepare('UPDATE fj_video SET video_url = ?, data_inclusao = ? WHERE video_level = ?');
		$rs->bindParam(1, $video_url);
		$data = date('Y-m-d H:i:s');
		$rs->bindParam(2, $data);
		$rs->bindParam(3, $video_nivel);
		$rs->execute();
		//Se o update foi bem-sucedida, mostra mensagem
		getDivResult(PAG_COMMIT_OK, DIV_OK);
	} catch (PDOException $e) {
		getDivResult(PAG_COMMIT_ERR, DIV_ERR); echo $e->getMessage();
	}
}
?>

<form method="POST" action="/nac-config-video">
<table id="vstable">
	<tr>
		<td>Nível<span class="ast">*</span>:</td>
		<td>
			<select name="form-video-nivel" class="form-selects">
				<option value="">-- Selecione uma opção --</option>
				<option value="loc">NÍVEL LOCAL</option>
				<option value="est">NÍVEL ESTADUAL</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>URL do Youtube<span class="ast">*</span>:</td>
		<td>
			<input type="text" name="form-video-url" />
		</td>
	</tr>
</table>
	<input type="submit" name="submited" value="Enviar" />
	<input type="reset" value="Limpar" />
</form>

<?php getSubTitulo('Uploads Realizados'); ?>

<?php
//Busca registros cadastrados
try{
	$rs = $conx->prepare('SELECT video_url, video_level, DATE_FORMAT(data_inclusao, \'%d/%m/%Y %H:%i:%s\') AS data_inclusao FROM fj_video ORDER BY data_inclusao DESC');
	$rs->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<table class="tab_pagina">
	<thead>
		<tr>
			<td>Envio</td>
			<td>Nível</td>
			<td>#</td>
		</tr>
	</thead>
	<tbody>
<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
		<tr>
			<td><?php echo $row['data_inclusao']; ?></td>
			<td><?php echo $row['video_level']; ?></td>
			<td>
				<button class="abrir-dialog-ui">visualizar</button>
				<!-- Modal -->
				<div class="newdialog-boxes">
					<div class="newdialog"><a href="#" class="close"><img src="img/fechar-modal.png" /></a><br />
						<div style="text-align:center;">
							<h4 class="newdialog-titulo">Visualização</h4>
							<iframe width="398" height="298" src="<?php echo $row['video_url']; ?>" frameborder="0" allowfullscreen></iframe>
						</div>
					</div>
				</div>
				<!-- Modal //-->
			</td>
		</tr>
<?php endwhile; ?>
	</tbody>
</table>