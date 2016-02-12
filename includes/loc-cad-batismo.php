<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php verifyLevel('loc'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('BATISMOS'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Registro de batismos'); ?>

<?php
/** Verifica, insere, etc, tudo aqui */
$form_descricao = isset($_POST['form-descricao']) ? $_POST['form-descricao'] : '';
$form_data_batismo = isset($_POST['form-data-batismo']) ? $_POST['form-data-batismo'] : '';
$form_qtd_jovens = isset($_POST['form-qtd-jovens']) ? $_POST['form-qtd-jovens'] : '';
$form_descricao = anti_injection($form_descricao);
$form_data_batismo = anti_injection($form_data_batismo);
$form_qtd_jovens = anti_injection($form_qtd_jovens);
$form_data_cad_batismo = date('Y-m-d');
$form_id_estado = $_SESSION['estado'];
$form_id_cidade = $_SESSION['cidade'];
$form_id_regiao = $_SESSION['regiao'];
$form_id_bairro = $_SESSION['bairro'];
$form_id_igreja = $_SESSION['igreja'];

//Verifica se usuario digitou alguma coisa
if (!empty($form_data_batismo) && !empty($form_qtd_jovens)){
	try{
		//Verifica se registro ja existe
		$rs = $conx->prepare('SELECT id FROM fj_batismos WHERE fk_q_igreja_id=? AND fk_bairro_id=? AND fk_estado_id=? AND fk_cidade_id=? AND fk_regiao_id=? AND data_batismo=?');
		$rs->bindParam(1, $form_id_igreja);
		$rs->bindParam(2, $form_id_bairro);
		$rs->bindParam(3, $form_id_estado);
		$rs->bindParam(4, $form_id_cidade);
		$rs->bindParam(5, $form_id_regiao);
		$rs->bindParam(6, $form_data_batismo);
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
			//Trata campos de Data
			$form_data_batismo = explode('/', $form_data_batismo);
			$form_data_batismo = $form_data_batismo[2].'-'.$form_data_batismo[1].'-'.$form_data_batismo[0];
			
			//Faz a inserção
			$rs = $conx->prepare('INSERT INTO fj_batismos VALUES(null, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
			$rs->bindParam(1, $form_id_regiao);
			$rs->bindParam(2, $form_id_cidade);
			$rs->bindParam(3, $form_id_estado);
			$rs->bindParam(4, $form_id_bairro);
			$rs->bindParam(5, $form_id_igreja);
			$rs->bindParam(6, $form_data_batismo);
			$rs->bindParam(7, $form_qtd_jovens);
			$rs->bindParam(8, $form_descricao);
			$rs->bindParam(9, $form_data_cad_batismo);
			$rs->execute();
			//Se a inserção foi bem-sucedida, mostra mensagem
			getDivResult(PAG_COMMIT_OK, DIV_OK);
		} catch (PDOException $e) {
			getDivResult(PAG_COMMIT_ERR, DIV_ERR); echo $e->getMessage();
		}
	} else {
		getDivResult(PAG_DUPLIC_ERR, DIV_ERR);
	}
} else if (
	isset ($_POST['submited']) &&
		(
			empty($form_descricao) ||
			empty($form_data_batismo) ||
			empty($form_qtd_jovens)
		 )
	) {
	//Caso campo esteja em branco
	getDivResult(PAG_EMPTY_ERR, DIV_ERR);
}
?>

<?php
/**  Deleta registros */
if (isset($_POST['form-del'])){
	try{
		$rs = $conx->prepare('DELETE FROM fj_batismos where id=?');
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
//Busca a região
try{
	$rsRegiao = $conx->prepare('
								SELECT a.fk_regiao_id as id, b.nome_regiao FROM fj_q_igreja a
								INNER JOIN fj_regiao b ON (a.fk_regiao_id = b.id)
								WHERE a.id=?
								');
	$rsRegiao->bindParam(1, $form_id_igreja);
	$rsRegiao->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
//Busca a igreja
try{
	$rsIgreja = $conx->prepare('SELECT id, nome_igreja FROM fj_q_igreja WHERE id=?');
	$rsIgreja->bindParam(1, $form_id_igreja);
	$rsIgreja->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<form method="POST" action="/loc-cad-batismo">
<table id="vstable">
	<tr>
		<td>Região:</td>
		<td>
			<select name="form-regiao" id="form-regiao" class="form-selects">
			<?php while ($row = $rsRegiao->fetch(PDO::FETCH_ASSOC)) : ?>
				<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_regiao']; ?></option>
			<?php endwhile; ?>
			</select>
		</td>
		<td>Igreja:</td>
		<td>
			<select name="form-igreja" id="form-igreja" class="form-selects">
			<?php while ($row = $rsIgreja->fetch(PDO::FETCH_ASSOC)) : ?>
				<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_igreja']; ?></option>
			<?php endwhile; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Data do batismo<span class="ast">*</span>:</td>
		<td>
			<input type="text" placeholder="__/__/____" name="form-data-batismo" class="form-data" />
		</td>
		<td>Quantidade de jovens<span class="ast">*</span>:</td>
		<td>
			  <input type="text" placeholder="" name="form-qtd-jovens" class="form-text-120" />
		</td>
	<tr>
		<td colspan="4"><br />Descrição do batismo:</td>
	</tr>
	<tr>
		<td colspan="4">
			<textarea name="form-descricao" maxlength="500" cols="50" rows="10"></textarea>
		</td>
	</tr>
	</tr>
</table>
	<input type="submit" name="submited" value="Cadastrar" />
	<input type="reset" value="Limpar" />
</form>

<?php getSubTitulo('Batismos registrados'); ?>

<?php
//Busca registros cadastrados
try{
	$rs = $conx->prepare('
						 SELECT a.id, DATE_FORMAT(a.data_batismo, \'%d/%m/%Y\') as data_batismo, a.qtd, DATE_FORMAT(a.data_cadastro, \'%d/%m/%Y\') as data_cadastro, b.nome_regiao, c.nome_igreja FROM fj_batismos a
						 INNER JOIN fj_regiao b ON (a.fk_regiao_id = b.id)
						 INNER JOIN fj_q_igreja c ON (a.fk_q_igreja_id = c.id)
						 WHERE a.fk_q_igreja_id = ? ORDER BY a.data_batismo DESC
						 LIMIT 0, 300
						');
	$rs->bindParam(1, $form_id_igreja);
	$rs->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<table class="tab_pagina">
	<thead>
		<tr>
			<td>Data do Batismo</td>
			<td>Jovens Batizados</td>
			<td>Região</td>
			<td>Igreja</td>
			<td class="col-tab-remove">Remover</td>
		</tr>
	</thead>
	<tbody>
<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
		<tr>
			<td><?php echo $row['data_batismo']; ?></td>
			<td><?php echo $row['qtd']; ?></td>
			<td><?php echo $row['nome_regiao']; ?></td>
			<td><?php echo $row['nome_igreja']; ?></td>
			<td>
				<?php
					if ($row['data_cadastro'] == date('d/m/Y')){
						echo '
							<form action="/loc-cad-batismo" method="POST">
								<input type="hidden" name="form-del" value="' .$row['id']. '" />
								<input type="submit" class="form-btn-del" value="excluir" />
							</form>
						';
					} else {
						echo '<button class="btn-disabled">excluir</button>';
					}
				?>
			</td>
		</tr>
<?php endwhile; ?>
	</tbody>
</table>