<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php verifyLevel('loc'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('FREQUÊNCIA JOVEM'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Registro de frequência'); ?>

<?php
/** Verifica, insere, etc, tudo aqui */
$form_id_equipe = isset($_POST['form-equipe']) ? $_POST['form-equipe'] : '';
$form_data_reuniao = date('Y-m-d');
$form_qtd_jovens = isset($_POST['form-qtd-jovens']) ? $_POST['form-qtd-jovens'] : '';
$form_descricao = isset($_POST['form-descricao']) ? $_POST['form-descricao'] : '';
//Se descrição for em branco, coloca um texto
if ($form_descricao == '') $form_descricao = 'S/ DESCRIÇÃO';
$form_data_reuniao = anti_injection($form_data_reuniao);
$form_id_equipe = anti_injection($form_id_equipe);
$form_qtd_jovens = anti_injection($form_qtd_jovens);
$form_descricao = anti_injection($form_descricao);
$form_id_estado = $_SESSION['estado'];
$form_id_cidade = $_SESSION['cidade'];
$form_id_regiao = $_SESSION['regiao'];
$form_id_bairro = $_SESSION['bairro'];
$form_id_igreja = $_SESSION['igreja'];

//Verifica se usuario digitou alguma coisa
if (!empty($form_id_equipe) && !empty($form_data_reuniao) && !empty($form_qtd_jovens)){
	try{
		//Verifica se registro ja existe
		$rs = $conx->prepare('SELECT id FROM fj_freq_equipe WHERE fk_equipe_id=? AND fk_q_igreja_id=? AND fk_bairro_id=? AND fk_estado_id=? AND fk_cidade_id=? AND fk_regiao_id=? AND data_reuniao=?');
		$rs->bindParam(1, $form_id_equipe);
		$rs->bindParam(2, $form_id_igreja);
		$rs->bindParam(3, $form_id_bairro);
		$rs->bindParam(4, $form_id_estado);
		$rs->bindParam(5, $form_id_cidade);
		$rs->bindParam(6, $form_id_regiao);
		$rs->bindParam(7, $form_data_reuniao);
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
			$rs = $conx->prepare('INSERT INTO fj_freq_equipe VALUES(null, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
			$rs->bindParam(1, $form_id_equipe);
			$rs->bindParam(2, $form_id_igreja);
			$rs->bindParam(3, $form_id_bairro);
			$rs->bindParam(4, $form_id_estado);
			$rs->bindParam(5, $form_id_cidade);
			$rs->bindParam(6, $form_id_regiao);
			$rs->bindParam(7, $form_data_reuniao);
			$rs->bindParam(8, $form_qtd_jovens);
			$rs->bindParam(9, $form_descricao);
			$rs->execute();
			//Se a inserção foi bem-sucedida, mostra mensagem
			getDivResult(PAG_COMMIT_OK, DIV_OK);
		} catch (PDOException $e) {
			getDivResult(PAG_COMMIT_ERR, DIV_ERR);
		}
	} else {
		getDivResult(PAG_DUPLIC_ERR, DIV_ERR);
	}
} else if (
	isset ($_POST['submited']) &&
		(
			empty($form_qtd_jovens) ||
			empty($form_data_reuniao) ||
			empty($form_id_equipe)
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
		$rs = $conx->prepare('DELETE FROM fj_freq_equipe where id=?');
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
//Busca a igreja
try{
	$rsIgreja = $conx->prepare('SELECT id, nome_igreja FROM fj_q_igreja WHERE id=?');
	$rsIgreja->bindParam(1, $form_id_igreja);
	$rsIgreja->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
//Busca o dia de reunião
try{
	$rsDiaReuniao = $conx->prepare('SELECT dia_freq_jovem FROM fj_q_igreja WHERE id=?');
	$rsDiaReuniao->bindParam(1, $form_id_igreja);
	$rsDiaReuniao->execute();
	$diaReuniao = $rsDiaReuniao->fetchAll(PDO::FETCH_COLUMN, 0);
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
//Busca a equipe
try{
	$rsEquipe = $conx->prepare('SELECT id, nome_equipe FROM fj_equipe WHERE fk_q_igreja_id=?');
	$rsEquipe->bindParam(1, $form_id_igreja);
	$rsEquipe->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<?php
//Só mostra formulário se for o dia de Frequência Jovem daquela igreja
if ($diaReuniao[0] == date('N')){
	echo '<form method="POST" action="/loc-cad-freq-jovem">';
	echo '<table id="vstable">';
	echo '	<tr>';
	echo '		<td>Igreja<span class="ast">*</span>:</td>';
	echo '		<td>';
	echo '			<select name="form-igreja" id="form-igreja" class="form-selects">';
						while ($row = $rsIgreja->fetch(PDO::FETCH_ASSOC)) :
	echo '				<option value="' .$row['id']. '">' .$row['nome_igreja']. '</option>';
						endwhile;
	echo '			</select>';
	echo '		</td>';
	echo '		<td>Equipe<span class="ast">*</span>:</td>';
	echo '		<td>';
	echo '			<select name="form-equipe" id="form-equipe" class="form-selects">';
	echo '			  <option value="">-- Selecione uma opção --</option>';
						while ($row = $rsEquipe->fetch(PDO::FETCH_ASSOC)) :
	echo '				<option value="' .$row['id']. '">' .$row['nome_equipe']. '</option>';
						endwhile;
	echo '			</select>';
	echo '		</td>';
	echo '	</tr>';
	echo '	<tr>';
	echo '		<td>Data da reunião<span class="ast">*</span>:</td>';
	echo '		<td>';
	echo '			  <input type="text" placeholder="" name="form-data-reuniao" class="form-data" value="' .date('d/m/Y'). '" disabled="disabled" />';
	echo '		</td>';
	echo '		<td>Quantidade de jovens<span class="ast">*</span>:</td>';
	echo '		<td>';
	echo '			  <input type="text" placeholder="" name="form-qtd-jovens" class="form-text-120" />';
	echo '		</td>';
	echo '	</tr>';

	echo '	<tr>';
	echo '		<td>Descrição (Opcional):</td>';
	echo '		<td colspan="3">';
	echo '			  <input type="text" placeholder="" name="form-descricao" maxlength="500" style="width: 546px;" />';
	echo '		</td>';
	echo '	</tr>';

	echo '</table>';
	echo '	<input type="submit" name="submited" value="Cadastrar" />';
	echo '	<input type="reset" value="Limpar" />';
	echo '</form>';
} else {
	getDivResult('Somente permitido registrar e deletar em seu dia de reunião!', DIV_ERR);
}
?>

<?php getSubTitulo('Frequência registrada'); ?>

<?php
//Busca registros cadastrados
try{
	$rs = $conx->prepare('
						 SELECT a.id, a.qtd_jovens, DATE_FORMAT(a.data_reuniao, \'%d/%m/%Y\') as data_reuniao, b.nome_equipe, c.nome_igreja FROM fj_freq_equipe a
						 INNER JOIN fj_equipe b ON (a.fk_equipe_id = b.id)
						 INNER JOIN fj_q_igreja c ON (a.fk_q_igreja_id = c.id)
						 WHERE a.fk_q_igreja_id = ? ORDER BY a.data_reuniao DESC
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
			<td>Equipe</td>
			<td>Nº de Jovens</td>
			<td>Data da Reunião</td>
			<td>Igreja</td>
			<td class="col-tab-remove">Remover</td>
		</tr>
	</thead>
	<tbody>
<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
		<tr>
			<td><?php echo $row['nome_equipe']; ?></td>
			<td><?php echo $row['qtd_jovens']; ?></td>
			<td><?php echo $row['data_reuniao']; ?></td>
			<td><?php echo $row['nome_igreja']; ?></td>
			<td>
				<?php
					if ($row['data_reuniao'] == date('d/m/Y')){
						echo '
							<form action="/loc-cad-freq-jovem" method="POST">
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