<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php verifyLevel('est'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('CADASTRO DE IGREJA'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Formulário de Cadastro'); ?>

<p>
<span style="font-weight:bold;">OBS:</span> Ao excluir uma igreja, será excluido tudo o que estiver vinculado à ela (tribos, líderes, jovens, etc...).<br />
</p><br />

<?php
/** Verifica, insere, etc, tudo aqui */
$form_id_cidade = isset($_POST['form-cidade']) ? $_POST['form-cidade'] : '';
$form_id_regiao = isset($_POST['form-regiao']) ? $_POST['form-regiao'] : '';
$form_id_bairro = isset($_POST['form-bairro']) ? $_POST['form-bairro'] : '';
$form_nome_igreja = isset($_POST['form-igreja']) ? $_POST['form-igreja'] : '';
$form_catedral = isset($_POST['form-catedral']) ? $_POST['form-catedral'] : '';
$form_dia_reuniao = isset($_POST['form-dia-reuniao']) ? $_POST['form-dia-reuniao'] : '';
$form_id_cidade = anti_injection($form_id_cidade);
$form_id_regiao = anti_injection($form_id_regiao);
$form_id_bairro = anti_injection($form_id_bairro);
$form_nome_igreja = anti_injection($form_nome_igreja);
$form_catedral = anti_injection($form_catedral);
$form_id_estado = $_SESSION['estado'];

//Verifica se usuario digitou alguma coisa
if (!empty($form_nome_igreja) && !empty($form_id_cidade) && !empty($form_id_regiao) && !empty($form_id_bairro) && !empty($form_catedral) && !empty($form_dia_reuniao)){
	try{
		//Verifica se registro ja existe
		$rs = $conx->prepare('SELECT id FROM fj_q_igreja WHERE nome_igreja=? AND fk_cidade_id=? AND fk_regiao_id=? AND fk_bairro_id=?');
		$rs->bindParam(1, $form_nome_igreja);
		$rs->bindParam(2, $form_id_cidade);
		$rs->bindParam(3, $form_id_regiao);
		$rs->bindParam(4, $form_id_bairro);
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
			$rs = $conx->prepare('INSERT INTO fj_q_igreja VALUES(null, ?, ?, ?, ?, ?, ?, ?)');
			$rs->bindParam(1, $form_id_bairro);
			$rs->bindParam(2, $form_id_estado);
			$rs->bindParam(3, $form_id_cidade);
			$rs->bindParam(4, $form_id_regiao);
			$rs->bindParam(5, $form_nome_igreja);
			$rs->bindParam(6, $form_catedral);
			$rs->bindParam(7, $form_dia_reuniao);
			$rs->execute();
			//Se a inserção foi bem-sucedida, mostra mensagem
			getDivResult(PAG_COMMIT_OK, DIV_OK);
		} catch (PDOException $e) {
			getDivResult(PAG_COMMIT_ERR, DIV_ERR);
		}
	} else {
		getDivResult(PAG_DUPLIC_ERR, DIV_ERR);
	}
} else if (isset($_POST['submited']) && (empty($form_nome_igreja) || empty($form_id_cidade) || empty($form_id_regiao) || empty($form_id_bairro) || empty($form_catedral) || empty($form_dia_reuniao))) {
	//Caso campo esteja em branco
	getDivResult(PAG_EMPTY_ERR, DIV_ERR);
}
?>

<?php
/**  Deleta registros */
if (isset($_POST['form-del'])){
	try{
		$rs = $conx->prepare('DELETE FROM fj_q_igreja where id=?');
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

<form method="POST" action="/est-cad-igreja">
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
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Bloco<span class="ast">*</span>:</td>
			<td>
				<select name="form-bloco" id="form-bloco" class="form-selects">
					<option value="">-- Selecione um bloco --</option>
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
				<input type="text" size="22" maxlength="50" placeholder="Nome da Igreja" name="form-igreja" />
			</td>
		</tr>
		<tr>
			<td>Catedral Principal<span class="ast">*</span>:</td>
			<td>
				<input type="radio" name="form-catedral" value="S" />Sim <input type="radio" name="form-catedral" value="N" checked="checked" />Não
			</td>
			<td>Dia de Reunião<span class="ast">*</span>:</td>
			<td>
				<input type="radio" name="form-dia-reuniao" value="1" />Seg
				<input type="radio" name="form-dia-reuniao" value="2" />Ter
				<input type="radio" name="form-dia-reuniao" value="3" />Qua
				<input type="radio" name="form-dia-reuniao" value="4" />Qui
				<br />
				<input type="radio" name="form-dia-reuniao" value="5" />Sex
				<input type="radio" name="form-dia-reuniao" value="6" checked="checked" />Sab
				<input type="radio" name="form-dia-reuniao" value="7" />Dom
			</td>
		</tr>
	</table>
	<input type="submit" name="submited" value="Cadastrar" />
	<input type="reset" value="Limpar" />
</form>

<?php getSubTitulo('Igrejas cadastradas'); ?>

<?php
//Busca igrejas cadastradas
try{
	$rs = $conx->prepare('
						 SELECT a.id, a.nome_igreja, a.catedral, a.dia_freq_jovem, b.nome_bairro, c.nome_regiao, d.nome_cidade, e.nome_estado FROM fj_q_igreja a
						 INNER JOIN fj_bairro b ON (a.fk_bairro_id = b.id)
						 INNER JOIN fj_regiao c ON (a.fk_regiao_id = c.id)
						 INNER JOIN fj_cidade d ON (a.fk_cidade_id = d.id)
						 INNER JOIN fj_estado e ON (a.fk_estado_id = e.id)
						 WHERE e.id = ? ORDER BY b.nome_bairro ASC, a.nome_igreja ASC
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
			<td>Igreja</td>
			<td>Bairro</td>
			<td>Região</td>
			<td>Cidade</td>
			<td>#</td>
			<td>Dia</td>
			<td class="col-tab-remove">Remover</td>
		</tr>
	</thead>
	<tbody>
<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
		<tr>
			<td><?php echo $row['nome_igreja']; ?></td>
			<td><?php echo $row['nome_bairro']; ?></td>
			<td><?php echo $row['nome_regiao']; ?></td>
			<td><?php echo $row['nome_cidade']; ?></td>
			<td><?php echo $row['catedral']; ?></td>
			<td><?php
					switch((int) $row['dia_freq_jovem']):
						case 1:
							echo 'SEG';
							break;
						case 2:
							echo 'TER';
							break;
						case 3:
							echo 'QUA';
							break;
						case 4:
							echo 'QUI';
							break;
						case 5:
							echo 'SEX';
							break;
						case 6:
							echo 'SAB';
							break;
						case 7:
							echo 'DOM';
							break;
						default:
							echo 'ERRO!';
					endswitch;
				?>
			</td>
			<td>
				<form action="/est-cad-igreja" method="POST">
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
		//Popula Bloco
		$.ajax({
			url: "<?php echo get_home(); ?>/json/return-json.php",
			type: "POST",
			data: {opt: "blc", arg: $(this).val()},
			beforeSend: function(){
				$("#form-bloco").html("<option>Carregando...</option>");
			},
			error: function(){
				alert("Erro na requisição! Tente novamente.");
			},
			success: function(result){
				var view = "<option value=\"\">-- Selecione uma opção --</option>";
				$.each($.parseJSON(result), function(key, value){
					view += "<option value=\"" +value.id+ "\">" +value.blc+ "</option>";
				});
				if (view == ""){
					$("#form-bloco").html("<option>Sem registros...</option>");
				} else {
					$("#form-bloco").html(view);
				}
			},
			dataType: "html"
		});
		
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
</script>