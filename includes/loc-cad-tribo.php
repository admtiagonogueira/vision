<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php verifyLevel('loc'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('CADASTRO DE TRIBO'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Formulário de Cadastro'); ?>

<p>
<span style="font-weight:bold;">OBS:</span> Ao excluir uma tribo, será excluido tudo o que estiver vinculado à ela (jovens, etc...).<br />
</p><br />

<?php
/** Verifica, insere, etc, tudo aqui */
$form_id_equipe = isset($_POST['form-equipe']) ? $_POST['form-equipe'] : '';
$form_id_lider_tribo = isset($_POST['form-lider-tribo']) ? $_POST['form-lider-tribo'] : '';
$form_nome_tribo = isset($_POST['form-nome-tribo']) ? $_POST['form-nome-tribo'] : '';
$form_id_equipe = anti_injection($form_id_equipe);
$form_id_lider_tribo = anti_injection($form_id_lider_tribo);
$form_nome_tribo = anti_injection($form_nome_tribo);
$form_id_estado = $_SESSION['estado'];
$form_id_cidade = $_SESSION['cidade'];
$form_id_regiao = $_SESSION['regiao'];
$form_id_bairro = $_SESSION['bairro'];
$form_id_igreja = $_SESSION['igreja'];

//Verifica se usuario digitou alguma coisa
if (!empty($form_id_equipe) && !empty($form_id_lider_tribo) && !empty($form_id_regiao) && !empty($form_id_cidade) && !empty($form_id_bairro) && !empty($form_id_igreja) && !empty($form_nome_tribo)){
	try{
		//Verifica se registro ja existe
		$rs = $conx->prepare('SELECT id FROM fj_tribo WHERE fk_equipe_id=? AND fk_q_igreja_id=? AND fk_bairro_id=? AND fk_estado_id=? AND fk_cidade_id=? AND fk_regiao_id=? AND nome_tribo=?');
		$rs->bindParam(1, $form_id_equipe);
		$rs->bindParam(2, $form_id_igreja);
		$rs->bindParam(3, $form_id_bairro);
		$rs->bindParam(4, $form_id_estado);
		$rs->bindParam(5, $form_id_cidade);
		$rs->bindParam(6, $form_id_regiao);
		$rs->bindParam(7, $form_nome_tribo);
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
			$rs = $conx->prepare('INSERT INTO fj_tribo VALUES(null, ?, ?, ?, ?, ?, ?, ?, ?)');
			$rs->bindParam(1, $form_id_equipe);
			$rs->bindParam(2, $form_id_lider_tribo);
			$rs->bindParam(3, $form_id_igreja);
			$rs->bindParam(4, $form_id_bairro);
			$rs->bindParam(5, $form_id_estado);
			$rs->bindParam(6, $form_id_cidade);
			$rs->bindParam(7, $form_id_regiao);
			$rs->bindParam(8, $form_nome_tribo);
			$rs->execute();
			//Se a inserção foi bem-sucedida, mostra mensagem
			getDivResult(PAG_COMMIT_OK, DIV_OK);
		} catch (PDOException $e) {
			getDivResult(PAG_COMMIT_ERR, DIV_ERR);
		}
	} else {
		getDivResult(PAG_DUPLIC_ERR, DIV_ERR);
	}
} else if (isset($_POST['submited']) && (empty($form_id_equipe) || empty($form_id_lider_tribo) || empty($form_id_regiao) || empty($form_id_cidade) || empty($form_id_bairro) || empty($form_id_igreja) || empty($form_nome_tribo))) {
	//Caso campo esteja em branco
	getDivResult(PAG_EMPTY_ERR, DIV_ERR);
}
?>

<?php
/**  Deleta registros */
if (isset($_POST['form-del'])){
	try{
		$rs = $conx->prepare('DELETE FROM fj_tribo where id=?');
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
//Busca a cidade
try{
	$rsCidade = $conx->prepare('SELECT id, nome_cidade FROM fj_cidade WHERE id=?');
	$rsCidade->bindParam(1, $form_id_cidade);
	$rsCidade->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
//Busca a região
try{
	$rsRegiao = $conx->prepare('SELECT id, nome_regiao FROM fj_regiao WHERE id=?');
	$rsRegiao->bindParam(1, $form_id_regiao);
	$rsRegiao->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
//Busca o bairro
try{
	$rsBairro = $conx->prepare('SELECT id, nome_bairro FROM fj_bairro WHERE id=?');
	$rsBairro->bindParam(1, $form_id_bairro);
	$rsBairro->execute();
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
//Busca as equipes
try{
	$rsEquipe = $conx->prepare('SELECT id, nome_equipe FROM fj_equipe WHERE fk_q_igreja_id=?');
	$rsEquipe->bindParam(1, $form_id_igreja);
	$rsEquipe->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<form method="POST" action="/loc-cad-tribo">
	<table id="vstable">
		<tr>
			<td>Cidade<span class="ast">*</span>:</td>
			<td>
				<select name="form-cidade" id="form-cidade" class="form-selects">
				<?php while ($row = $rsCidade->fetch(PDO::FETCH_ASSOC)) : ?>
					<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_cidade']; ?></option>
				<?php endwhile; ?>
				</select>
			</td>
			<td>Região<span class="ast">*</span>:</td>
			<td>
				<select name="form-regiao" id="form-regiao" class="form-selects">
				<?php while ($row = $rsRegiao->fetch(PDO::FETCH_ASSOC)) : ?>
					<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_regiao']; ?></option>
				<?php endwhile; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Bairro<span class="ast">*</span>:</td>
			<td>
				<select name="form-bairro" id="form-bairro" class="form-selects">
				<?php while ($row = $rsBairro->fetch(PDO::FETCH_ASSOC)) : ?>
					<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_bairro']; ?></option>
				<?php endwhile; ?>
				</select>
			</td>
			<td>Igreja<span class="ast">*</span>:</td>
			<td>
				<select name="form-igreja" id="form-igreja" class="form-selects">
				<?php while ($row = $rsIgreja->fetch(PDO::FETCH_ASSOC)) : ?>
					<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_igreja']; ?></option>
				<?php endwhile; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Nome da Equipe<span class="ast">*</span>:</td>
			<td>
				<select name="form-equipe" id="form-equipe" class="form-selects">
					<option value="">-- Selecione uma opção --</option>
				<?php while ($row = $rsEquipe->fetch(PDO::FETCH_ASSOC)) : ?>
					<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_equipe']; ?></option>
				<?php endwhile; ?>
				</select>
			</td>
			<td>Líder da Tribo<span class="ast">*</span>:</td>
			<td>
				<select name="form-lider-tribo" id="form-lider-tribo" class="form-selects">
					<option value="">-- Selecione uma equipe --</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Nome da Tribo<span class="ast">*</span>:</td>
			<td>
				<input type="text" maxlength="50" placeholder="" name="form-nome-tribo" class="form-text-120" />
			</td>
		</tr>
	</table>
	<input type="submit" name="submited" value="Cadastrar" />
	<input type="reset" value="Limpar" />
</form>

<?php getSubTitulo('Tribos cadastradas'); ?>

<?php
//Busca tribos cadastradas
try{
	$rs = $conx->prepare('
						 SELECT a.id, a.nome_tribo, b.nome_cidade, c.nome, d.nome_igreja, e.nome_equipe FROM fj_tribo a
						 INNER JOIN fj_cidade b ON (a.fk_cidade_id = b.id)
						 INNER JOIN fj_lider_tribo c ON (a.fk_lider_tribo_id = c.id)
						 INNER JOIN fj_q_igreja d ON (a.fk_q_igreja_id = d.id)
						 INNER JOIN fj_equipe e ON (a.fk_equipe_id = e.id)
						 INNER JOIN fj_estado f ON (a.fk_estado_id = f.id)
						 WHERE f.id = ?
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
			<td>Tribo</td>
			<td>Líder</td>
			<td>Equipe</td>
			<td>Igreja</td>
			<td class="col-tab-remove">Remover</td>
		</tr>
	</thead>
	<tbody>
<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
		<tr>
			<td><?php echo $row['nome_tribo']; ?></td>
			<td><?php echo $row['nome']; ?></td>
			<td><?php echo $row['nome_equipe']; ?></td>
			<td><?php echo $row['nome_igreja']; ?></td>
			<td>
				<form action="/loc-cad-tribo" method="POST">
					<input type="hidden" name="form-del" value="<?php echo $row['id']; ?>" />
					<input type="submit" class="form-btn-del" value="excluir" />
				</form>
			</td>
		</tr>
<?php endwhile; ?>
	</tbody>
</table>

<?php /* JQuery */ ?>
<script>
	$("#form-equipe").on("change", function(e){
		//Popula Líderes de Tribo
		$.ajax({
			url: "<?php echo get_home(); ?>/json/return-json.php",
			type: "POST",
			data: {opt: "ldt", arg: $(this).val()},
			beforeSend: function(){
				$("#form-lider-tribo").html("<option>Carregando...</option>");
			},
			error: function(){
				alert("Erro na requisição! Tente novamente.");
			},
			success: function(result){
				var view = "<option value=\"\">-- Selecione uma opção --</option>";
				$.each($.parseJSON(result), function(key, value){
					view += "<option value=\"" +value.id+ "\">" +value.ldt+ "</option>";
				});
				if (view == ""){
					$("#form-lider-tribo").html("<option>Sem registros...</option>");
				} else {
					$("#form-lider-tribo").html(view);
				}
			},
			dataType: "html"
		});
	});
</script>