<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php verifyLevel('est'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('CADASTRO DE EQUIPE'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Formulário de Cadastro'); ?>

<p>
<span style="font-weight:bold;">OBS:</span> Ao excluir uma equipe, será excluido tudo o que estiver vinculado à ela (tribos, jovens, etc...).<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Líderes de equipe não serão excluídos, podendo serem atribuídos à outra equipe.
</p><br />

<?php
/** Verifica, insere, etc, tudo aqui */
$form_id_lider_equipe = isset($_POST['form-lider-equipe']) ? $_POST['form-lider-equipe'] : '';
$form_id_regiao = isset($_POST['form-regiao']) ? $_POST['form-regiao'] : '';
$form_id_cidade = isset($_POST['form-cidade']) ? $_POST['form-cidade'] : '';
$form_id_bairro = isset($_POST['form-bairro']) ? $_POST['form-bairro'] : '';
$form_id_igreja = isset($_POST['form-igreja']) ? $_POST['form-igreja'] : '';
$form_nome_equipe = isset($_POST['form-nome-equipe']) ? $_POST['form-nome-equipe'] : '';
$form_id_lider_equipe = anti_injection($form_id_lider_equipe);
$form_id_regiao = anti_injection($form_id_regiao);
$form_id_cidade = anti_injection($form_id_cidade);
$form_id_bairro = anti_injection($form_id_bairro);
$form_id_igreja = anti_injection($form_id_igreja);
$form_nome_equipe = anti_injection($form_nome_equipe);
$form_id_estado = $_SESSION['estado'];

//Verifica se usuario digitou alguma coisa
if (!empty($form_id_lider_equipe) && !empty($form_id_regiao) && !empty($form_id_cidade) && !empty($form_id_bairro) && !empty($form_id_igreja) && !empty($form_nome_equipe)){
	try{
		//Verifica se registro ja existe
		$rs = $conx->prepare('SELECT id FROM fj_equipe WHERE fk_regiao_id=? AND fk_cidade_id=? AND fk_estado_id=? AND fk_bairro_id=? AND fk_q_igreja_id=? AND nome_equipe=?');
		$rs->bindParam(1, $form_id_regiao);
		$rs->bindParam(2, $form_id_cidade);
		$rs->bindParam(3, $form_id_estado);
		$rs->bindParam(4, $form_id_bairro);
		$rs->bindParam(5, $form_id_igreja);
		$rs->bindParam(6, $form_nome_equipe);
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
			$rs = $conx->prepare('INSERT INTO fj_equipe VALUES(null, ?, ?, ?, ?, ?, ?, ?)');
			$rs->bindParam(1, $form_id_lider_equipe);
			$rs->bindParam(2, $form_id_regiao);
			$rs->bindParam(3, $form_id_cidade);
			$rs->bindParam(4, $form_id_estado);
			$rs->bindParam(5, $form_id_bairro);
			$rs->bindParam(6, $form_id_igreja);
			$rs->bindParam(7, $form_nome_equipe);
			$rs->execute();
			//Se a inserção foi bem-sucedida, mostra mensagem
			getDivResult(PAG_COMMIT_OK, DIV_OK);
		} catch (PDOException $e) {
			getDivResult(PAG_COMMIT_ERR, DIV_ERR);
		}
	} else {
		getDivResult(PAG_DUPLIC_ERR, DIV_ERR);
	}
} else if (isset($_POST['submited']) && (empty($form_id_equipe) || empty($form_id_regiao) || empty($form_id_cidade) || empty($form_id_bairro) || empty($form_id_igreja))) {
	//Caso campo esteja em branco
	getDivResult(PAG_EMPTY_ERR, DIV_ERR);
}
?>

<?php
/**  Deleta registros */
if (isset($_POST['form-del'])){
	try{
		$rs = $conx->prepare('DELETE FROM fj_equipe where id=?');
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
//Busca as cidades (isso mesmo) para exibir no select da região abaixo
try{
	$rs = $conx->prepare('SELECT id, nome_cidade FROM fj_cidade WHERE fk_estado_id=?');
	$rs->bindParam(1, $form_id_estado);
	$rs->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<form method="POST" action="/est-cad-equipe">
	<table id="vstable">
		<tr>
			<td>Cidade<span class="ast">*</span>:</td>
			<td>
				<select name="form-cidade" id="form-cidade" class="form-selects">
					<option value="">-- Selecione uma opção --</option>
				<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
					<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_cidade']; ?></option>
				<?php endwhile; ?>
				</select>
			</td>
			<td>Região<span class="ast">*</span>:</td>
			<td>
				<select name="form-regiao" id="form-regiao" class="form-selects">
					<option value="">-- Selecione uma cidade --</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Bairro<span class="ast">*</span>:</td>
			<td>
				<select name="form-bairro" id="form-bairro" class="form-selects">
					<option value="">-- Selecione uma cidade --</option>
				</select>
			</td>
			<td>Igreja<span class="ast">*</span>:</td>
			<td>
				<select name="form-igreja" id="form-igreja" class="form-selects">
					<option value="">-- Selecione um bairro --</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Líder da Equipe<span class="ast">*</span>:</td>
			<td>
				<select name="form-lider-equipe" id="form-lider-equipe" class="form-selects">
					<option value="">-- Selecione uma igreja --</option>
				</select>
			</td>
			<td>
				<input type="radio" name="radio-eqp-igr" id="radio-equipe" checked="checked" />Equipe
				<input type="radio" name="radio-eqp-igr" id="radio-igreja" />Igreja
			</td>
			<td id="td-eqp-igr">
				Nome<span class="ast">*</span>:&nbsp;&nbsp;
				<input type="text" maxlength="50" placeholder="" name="form-nome-equipe" class="form-text-120" />
			</td>
		</tr>
	</table>
	<input type="submit" name="submited" value="Cadastrar" />
	<input type="reset" value="Limpar" />
</form>

<?php getSubTitulo('Equipes cadastradas'); ?>

<?php
//Busca equipes cadastradas
try{
	$rs = $conx->prepare('
						 SELECT a.id, a.nome_equipe, b.nome_cidade, c.nome, d.nome_igreja FROM fj_equipe a
						 INNER JOIN fj_cidade b ON (a.fk_cidade_id = b.id)
						 INNER JOIN fj_lider_equipe c ON (a.fk_lider_equipe_id = c.id)
						 INNER JOIN fj_q_igreja d ON (a.fk_q_igreja_id = d.id)
						 INNER JOIN fj_estado e ON (a.fk_estado_id = e.id)
						 WHERE e.id = ?
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
			<td>Equipe</td>
			<td>Cidade</td>
			<td>Líder</td>
			<td>Igreja</td>
			<td class="col-tab-remove">Remover</td>
		</tr>
	</thead>
	<tbody>
<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
		<tr>
			<td><?php echo $row['nome_equipe']; ?></td>
			<td><?php echo $row['nome_cidade']; ?></td>
			<td><?php echo $row['nome']; ?></td>
			<td><?php echo $row['nome_igreja']; ?></td>
			<td>
				<form action="/est-cad-equipe" method="POST">
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
	$("#form-cidade").on("change", function(e){
		//Popula Região
		$.ajax({
			url: "<?php echo get_home(); ?>/json/return-json.php",
			type: "POST",
			data: {opt: "reg", arg: $(this).val()},
			beforeSend: function(){
				$("#form-regiao").html("<option>Carregando...</option>");
			},
			error: function(){
				alert("Erro na requisição! Tente novamente.");
			},
			success: function(result){
				var view = "<option value=\"\">-- Selecione uma opção --</option>";
				$.each($.parseJSON(result), function(key, value){
					view += "<option value=\"" +value.id+ "\">" +value.reg+ "</option>";
				});
				if (view == ""){
					$("#form-regiao").html("<option>Sem registros...</option>");
				} else {
					$("#form-regiao").html(view);
				}
			},
			dataType: "html"
		});
		
		//Popula Bairro
		$.ajax({
			url: "<?php echo get_home(); ?>/json/return-json.php",
			type: "POST",
			data: {opt: "bai", arg: $(this).val()},
			beforeSend: function(){
				$("#form-bairro").html("<option>Carregando...</option>");
			},
			error: function(){
				alert("Erro na requisição! Tente novamente.");
			},
			success: function(result){
				var view = "<option value=\"\">-- Selecione uma opção --</option>";
				$.each($.parseJSON(result), function(key, value){
					view += "<option value=\"" +value.id+ "\">" +value.bai+ "</option>";
				});
				if (view == ""){
					$("#form-bairro").html("<option>Sem registros...</option>");
				} else {
					$("#form-bairro").html(view);
				}
			},
			dataType: "html"
		});
	});
	
	$("#form-bairro").on("change", function(e){
		//Popula Igrejas
		$.ajax({
			url: "<?php echo get_home(); ?>/json/return-json.php",
			type: "POST",
			data: {opt: "igr", arg: $(this).val()},
			beforeSend: function(){
				$("#form-igreja").html("<option>Carregando...</option>");
			},
			error: function(){
				alert("Erro na requisição! Tente novamente.");
			},
			success: function(result){
				var view = "<option value=\"\">-- Selecione uma opção --</option>";
				$.each($.parseJSON(result), function(key, value){
					view += "<option value=\"" +value.id+ "\">" +value.igr+ "</option>";
				});
				if (view == ""){
					$("#form-igreja").html("<option>Sem registros...</option>");
				} else {
					$("#form-igreja").html(view);
				}
			},
			dataType: "html"
		});
	});
	
	$("#form-igreja").on("change", function(e){
		//Popula Igrejas
		$.ajax({
			url: "<?php echo get_home(); ?>/json/return-json.php",
			type: "POST",
			data: {opt: "lde", arg: $(this).val()},
			beforeSend: function(){
				$("#form-lider-equipe").html("<option>Carregando...</option>");
			},
			error: function(){
				alert("Erro na requisição! Tente novamente.");
			},
			success: function(result){
				var view = "<option value=\"\">-- Selecione uma opção --</option>";
				$.each($.parseJSON(result), function(key, value){
					view += "<option value=\"" +value.id+ "\">" +value.lde+ "</option>";
				});
				if (view == ""){
					$("#form-lider-equipe").html("<option>Sem registros...</option>");
				} else {
					$("#form-lider-equipe").html(view);
				}
			},
			dataType: "html"
		});
	});

	//Campo de Nome Equipe/Igreja	
	$("#radio-igreja").on("click", function(e){
		$("#td-eqp-igr").css("visibility", "hidden");
		$("#td-eqp-igr > input[name=\"form-nome-equipe\"]").attr("value", "EQUIPE UNICA");
	});
	$("#radio-equipe").on("click", function(e){
		$("#td-eqp-igr").css("visibility", "visible");
		$("#td-eqp-igr > input[name=\"form-nome-equipe\"]").attr("value", "");
	});
</script>