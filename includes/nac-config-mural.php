<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php require_once('includes/bibliotecas/wideimage-11.02.19-lib/WideImage.php'); ?>
<?php verifyLevel('nac'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('CONFIGURAÇÃO DO MURAL'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Gerenciamento de Imagens'); ?>

<p>
<span style="font-weight:bold;">OBS:</span> Imagens com tamanhos diferentes de 730x285 píxels serão redimensionadas automaticamente.
</p><br />

<?php
//Insere imagem
if (isset($_POST['submited']) && $_FILES['form-img-file']['name'] != ''){

	$erro = array();
	$erro[0] = 'Sem erros.';
	$erro[1] = 'O arquivo no upload é maior que o limite do servidor!';
	$erro[2] = 'O arquivo ultrapassa o limite de tamanho especificado!';
	$erro[3] = 'O upload do arquivo foi feito parcialmente!';
	$erro[4] = 'Não foi feito upload de arquivos!';
	$extensoes_aceitas = array('png');
	$tamanho_maximo = 1024 * 1024 * 2; //Em Bytes

	if ($_FILES['form-img-file']['error'] != 0){
		getDivResult($erro[$_FILES['form-img-file']['error']], DIV_ERR);
	} else {
		$img_nome = $_FILES['form-img-file']['name'];
		$array = explode('.', $img_nome);
		$img_extensao = strtolower(end($array));
		
		if (array_search($img_extensao, $extensoes_aceitas) === false){
			getDivResult('São aceitas apenas imagens no formato PNG!', DIV_ERR);
		} else {
			if ($_FILES['form-img-file']['size'] > $tamanho_maximo){
				getDivResult('Tamanho máximo da imagem foi ultrapassdo! Envie uma imagem menor.', DIV_ERR);
			} else {
				$img_descricao = isset($_POST['form-img-descricao']) && $_POST['form-img-descricao'] != '' ? $_POST['form-img-descricao'] : 'S/ DESCRIÇÃO';
				$img_nivel = isset($_POST['form-img-nivel']) && $_POST['form-img-nivel'] != '' ? $_POST['form-img-nivel'] : 'est';
				$img_nome_tmp = $_FILES['form-img-file']['tmp_name'];		
				$img_novo_nome = md5(time()) . '.' . $img_extensao;

				if (move_uploaded_file($img_nome_tmp, DIR_UPLOADS . '/img_orbit_slide/' . $img_novo_nome)){
					try{
						//Redimensiona com a biblioteca Canvas
						WideImage::load(DIR_UPLOADS . '/img_orbit_slide/' . $img_novo_nome)->resize(730, 285, 'fill')->SaveToFile(DIR_UPLOADS . '/img_orbit_slide/' . $img_novo_nome);
						
						//Faz a inserção
						$rs = $conx->prepare('INSERT INTO fj_mural VALUES(null, ?, ?, ?, ?)');
						$rs->bindParam(1, $img_novo_nome);
						$rs->bindParam(2, $img_descricao);
						$rs->bindParam(3, $img_nivel);
						$data = date('Y-m-d H:i:s');
						$rs->bindParam(4, $data);
						$rs->execute();
						//Se a inserção foi bem-sucedida, mostra mensagem
						getDivResult(PAG_COMMIT_OK, DIV_OK);
					} catch (PDOException $e) {
						getDivResult(PAG_COMMIT_ERR, DIV_ERR);
					}
				} else{
					getDivResult(FILE_UP_ERR, DIV_ERR);
				}
			}
		}
	}
}

//Deleta imagem
if (isset($_POST['img-id-del'])){
	$img_id_del = $_POST['img-id-del'];
	
	try{
		//Busca o nome do ID enviado para deletar o arquivo físico
		$rs = $conx->prepare('SELECT img_nome FROM fj_mural WHERE id = ?');
		$rs->bindParam(1, $img_id_del);
		$rs->execute();
		$rs = $rs->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		getDivResult(FILE_QUERY_ERR, DIV_ERR);
	}

	//Deleta o arquivo físico
	$arquivo = DIR_UPLOADS . '/img_orbit_slide/' . $rs[0]['img_nome']; 
	if (unlink($arquivo)){
		getDivResult(FILE_DEL_OK, DIV_OK);
	} else {
		getDivResult(FILE_DEL_ERR, DIV_ERR);
	}
	
	try{
		//Faz a deleção
		$rs = $conx->prepare('DELETE FROM fj_mural WHERE id = ?');
		$rs->bindParam(1, $img_id_del);
		$rs->execute();
		//Se a deleção foi bem-sucedida, mostra mensagem
		getDivResult(PAG_DEL_OK, DIV_OK);
	} catch (PDOException $e) {
		getDivResult(PAG_DEL_ERR, DIV_ERR);
	}
}
?>

<form method="POST" action="/nac-config-mural" enctype="multipart/form-data">
<table id="vstable">
	<tr>
		<td>Nível<span class="ast">*</span>:</td>
		<td>
			<select name="form-img-nivel" class="form-selects">
				<option value="">-- Selecione uma opção --</option>
				<option value="loc">NÍVEL LOCAL</option>
				<option value="est">NÍVEL ESTADUAL</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Arquivo<span class="ast">*</span>:</td>
		<td>
			<input type="file" name="form-img-file" />
		</td>
	</tr>
	<tr>
		<td colspan="2"><br />Descrição:</td>
	</tr>
	<tr>
		<td colspan="2">
        	<textarea name="form-img-descricao" maxlength="90" cols="50" rows="10"></textarea>
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
	$rs = $conx->prepare('SELECT id, img_nome, img_descricao, img_level, DATE_FORMAT(data_inclusao, \'%d/%m/%Y %H:%i:%s\') AS data_inclusao FROM fj_mural ORDER BY data_inclusao DESC');
	$rs->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<table class="tab_pagina">
	<thead>
		<tr>
			<td>Envio</td>
			<td>Descrição</td>
			<td>Nível</td>
			<td>#</td>
			<td class="col-tab-remove">Remover</td>
		</tr>
	</thead>
	<tbody>
<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
		<tr>
			<td><?php echo $row['data_inclusao']; ?></td>
			<td><?php echo $row['img_descricao']; ?></td>
			<td><?php echo $row['img_level']; ?></td>
			<td>
				<button class="abrir-dialog-ui">visualizar</button>
				<!-- Modal -->
				<div class="newdialog-boxes">
					<div class="newdialog"><a href="#" class="close"><img src="img/fechar-modal.png" /></a><br />
						<div>
							<h4 class="newdialog-titulo">Visualização</h4>
							<img src="<?php echo DIR_UPLOADS . '/img_orbit_slide/' . $row['img_nome']; ?>" alt="" />
						</div>
					</div>
				</div>
				<!-- Modal //-->
			</td>
			<td>
				<form action="/nac-config-mural" method="POST">
					<input type="hidden" name="img-id-del" value="<?php echo $row['id']; ?>" />
					<input type="submit" class="form-btn-del" value="excluir" />
				</form>
			</td>
		</tr>
<?php endwhile; ?>
	</tbody>
</table>