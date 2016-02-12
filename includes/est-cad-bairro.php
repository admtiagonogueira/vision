<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php verifyLevel('est'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('CADASTRO DE BAIRRO'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Formulário de Cadastro'); ?>

<p>
<span style="font-weight:bold;">OBS:</span> Não é possível excluir um bairro se existirem dados vinculados à ele (igreja, líderes, tribos, jovens, etc...).
</p><br />

<?php
/** Verifica, insere, etc, tudo aqui */
$form_id_cidade = isset($_POST['form-cidade']) ? $_POST['form-cidade'] : '';
$form_nome_bairro = isset($_POST['form-bairro']) ? $_POST['form-bairro'] : '';
$form_id_cidade = anti_injection($form_id_cidade);
$form_nome_bairro = anti_injection($form_nome_bairro);
$form_id_estado = $_SESSION['estado'];

//Verifica se usuario digitou alguma coisa
if (!empty($form_nome_bairro) && !empty($form_id_cidade)){
	try{
		//Verifica se registro ja existe
		$rs = $conx->prepare('SELECT id FROM fj_bairro WHERE nome_bairro=? AND fk_cidade_id=?');
		$rs->bindParam(1, $form_nome_bairro);
		$rs->bindParam(2, $form_id_cidade);
		$rs->execute();
		$row = $rs->fetchAll(PDO::FETCH_ASSOC);
		//Conta as linhas para verificação logo abaixo
		$numRows = count($row);
	} catch (PDOException $e) {
		getDivResult(PAG_QUERY_ERR, DIV_ERR);
	}

	//Se não existe registro, insere, se existe, mostra erro de duplicidade
	if ($numRows === 0){
		try{
			//Faz a inserção
			$rs = $conx->prepare('INSERT INTO fj_bairro VALUES(null, ?, ?, ?)');
			$rs->bindParam(1, $form_id_estado);
			$rs->bindParam(2, $form_id_cidade);
			$rs->bindParam(3, $form_nome_bairro);
			$rs->execute();
			//Se a inserção foi bem-sucedida, mostra mensagem
			getDivResult(PAG_COMMIT_OK, DIV_OK);
		} catch (PDOException $e) {
			getDivResult(PAG_COMMIT_ERR, DIV_ERR);
		}
	} else {
		getDivResult(PAG_DUPLIC_ERR, DIV_ERR);
	}
} else if (isset($_POST['submited']) && (empty($form_nome_bairro) || empty($form_id_cidade))) {
	//Caso campo esteja em branco
	getDivResult(PAG_EMPTY_ERR, DIV_ERR);
}
?>

<?php
/**  Deleta registros */
if (isset($_POST['form-del'])){
	try{
		$rs = $conx->prepare('DELETE FROM fj_bairro where id=?');
		$rs->bindParam(1, $_POST['form-del']);
		$rs->execute();
			if ($rs->rowCount() > 0){
				getDivResult(PAG_DEL_OK, DIV_OK);
			}
	} catch (PDOException $e) {
		getDivResult(PAG_DEL_ERR, DIV_ERR);
	}
}
?>

<?php
//Busca as cidades para exibir no select da região abaixo
try{
	$rs = $conx->prepare('SELECT id, nome_cidade FROM fj_cidade WHERE fk_estado_id=?');
	$rs->bindParam(1, $form_id_estado);
	$rs->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<form method="POST" action="/est-cad-bairro">
	<label>
		Cidade<span class="ast">*</span>: 
		<select name="form-cidade">
		<option value="">-- Selecione uma opção --</option>
		<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
			<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_cidade']; ?></option>
		<?php endwhile; ?>
		</select>
	</label>
		<label>Bairro<span class="ast">*</span>: <input type="text" size="50" maxlength="50" placeholder="Nome do Bairro" name="form-bairro" /></label>
		<br />
	<input type="submit" name="submited" value="Cadastrar">
	<input type="reset" value="Limpar">
</form>

<?php getSubTitulo('Bairros cadastrados'); ?>

<?php
//Busca regiões cadastradas
try{
	$rs = $conx->prepare('SELECT a.id, a.nome_bairro, a.fk_estado_id, b.nome_cidade FROM fj_bairro a
						 INNER JOIN fj_cidade b ON (a.fk_cidade_id = b.id)
						 WHERE a.fk_estado_id = ?
						');
	$rs->bindParam(1, $form_id_estado);
	$rs->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<table class="tab_pagina">
	<thead>
		<tr>
			<td>Bairro</td>
			<td>Cidade</td>
			<td class="col-tab-remove">Remover</td>
		</tr>
	</thead>
	<tbody>
<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
		<tr>
			<td><?php echo $row['nome_bairro']; ?></td>
			<td><?php echo $row['nome_cidade']; ?></td>
			<td>
				<form action="/est-cad-bairro" method="POST">
					<input type="hidden" name="form-del" value="<?php echo $row['id']; ?>" />
					<input type="submit" class="form-btn-del" value="excluir" />
				</form>
			</td>
		</tr>
<?php endwhile; ?>
	</tbody>
</table>