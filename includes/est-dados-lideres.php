<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php verifyLevel('est'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('DADOS DE LÍDERES'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Filtro de Busca'); ?>

<p>
<span style="font-weight:bold;">OBS:</span> Selecione as opções nos filtros abaixo.
</p><br />

<?php
$form_id_estado = $_SESSION['estado'];
?>

<?php
//Busca as cidades (isso mesmo) para exibir no select da região abaixo
try{
	$rsCidade = $conx->prepare('SELECT id, nome_cidade FROM fj_cidade WHERE fk_estado_id = ?');
	$rsCidade->bindParam(1, $form_id_estado);
	$rsCidade->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<form method="POST" action="/est-dados-lideres">
<table id="vstable">
	<tr>
		<td>Cidade<span class="ast">*</span>:</td>
		<td>
			<select name="form-cidade" id="form-cidade" class="form-selects">
			  <option value="">-- Selecione uma opção --</option>
			<?php while ($row = $rsCidade->fetch(PDO::FETCH_ASSOC)) : ?>
				<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_cidade']; ?></option>
			<?php endwhile; ?>
			</select>
		</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>Região<span class="ast">*</span>:</td>
		<td>
			<select name="form-regiao" id="form-regiao" class="form-selects">
			  <option value="">-- Selecione uma cidade --</option>
			</select>
		</td>
		<td>Igreja<span class="ast">*</span>:</td>
		<td>
			<select name="form-igreja" id="form-igreja" class="form-selects">
			  <option value="">-- Selecione uma região --</option>
			</select>
		</td>
	</tr>
	<!--
	<tr>
		<td>Nome:</td>
		<td colspan="3">
        	<input type="text" maxlength="90" placeholder="Nome do Líder" name="form-nome" class="form-nome" />
		</td>
	</tr>
	-->
</table>
	<input type="submit" name="submited" value="Pesquisar" />
	<input type="reset" value="Limpar" />
</form>

<?php getSubTitulo('Resultado da Pesquisa'); ?>

<?php
$form_id_igreja = isset($_POST['form-igreja']) ? $_POST['form-igreja'] : '';
//$form_nome = isset($POST['form-nome']) && $POST['form-nome'] != '' ? $POST['form-nome'] : '';

if (isset($_POST['submited'])){
	
	if ($form_id_igreja != ''){
		
		//Busca líderes cadastrados
		try{
			$rs = $conx->prepare('
								 SELECT a.id, a.nome, a.tel_fx, a.tel_cel, a.e_mail, DATE_FORMAT(a.data_nascimento, \'%d/%m/%Y\') as data_nascimento, a.cep, DATE_FORMAT(a.entrada_iurd, \'%d/%m/%Y\') as entrada_iurd, DATE_FORMAT(a.entrada_fj, \'%d/%m/%Y\') as entrada_fj, b.nome_igreja, c.nome_bairro, d.nome_regiao, e.nome_cidade, f.nome_escolaridade, g.nome_estado_civil, h.nome_funcao FROM fj_lider_equipe a
								 INNER JOIN fj_q_igreja b ON (a.fk_q_igreja_id = b.id)
								 INNER JOIN fj_bairro c ON (a.fk_bairro_id = c.id)
								 INNER JOIN fj_regiao d ON (a.fk_regiao_id = d.id)
								 INNER JOIN fj_cidade e ON (a.fk_cidade_id = e.id)
								 INNER JOIN fj_formacao f ON (a.fk_formacao_id = f.id)
								 INNER JOIN fj_estado_civil g ON (a.fk_estado_civil_id = g.id)
								 INNER JOIN fj_lider_funcao h ON (a.fk_lider_funcao_id = h.id)
								 WHERE a.fk_q_igreja_id = ?
								');
			$rs->bindParam(1, $form_id_igreja);
			$rs->execute();
		} catch (PDOException $e) {
			getDivResult(PAG_QUERY_ERR, DIV_ERR);
		}

	} else {
		getDivResult(PAG_EMPTY_ERR, DIV_ERR);
	}
}
?>

<table class="tab_pagina">
	<thead>
		<tr>
			<td>Nome</td>
			<td>Igreja</td>
			<td>Região</td>
			<td>Cidade</td>
			<td>+Info</td
		</tr>
	</thead>
	<tbody>
<?php if (isset($_POST['submited']) && $form_id_igreja != ''){ ?>
<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
		<tr>
			<td><?php echo $row['nome']; ?></td>
			<td><?php echo $row['nome_igreja']; ?></td>
			<td><?php echo $row['nome_regiao']; ?></td>
			<td><?php echo $row['nome_cidade']; ?></td>
			<td>
				<button class="abrir-dialog-ui">+Info</button>
				<!-- Modal -->
				<div class="newdialog-boxes">
					<div class="newdialog"><a href="#" class="close"><img src="img/fechar-modal.png" /></a><br />
						<div>
							<h4 class="newdialog-titulo">Informações do Líder</h4>
							<!-- Início Tabela + Info -->
							<table id="vstable">
								<tr>
									<td>Nome:</td>
									<td colspan="3">
										<input type="text" class="form-nome" value="<?php echo $row['nome']; ?>" />
									</td>
								</tr>
								<tr>
									<td>Data de nasc.:</td>
									<td>
										  <input type="text" class="form-data" value="<?php echo $row['data_nascimento']; ?>" />
									</td>
									<td>Telefone Fixo:</td>
									<td>
										  <input type="text" class="form-text-120 form-fone-fixo" value="<?php echo $row['tel_fx']; ?>" />
									</td>
								</tr>
								<tr>
									<td>Celular:</td>
									<td>
										<input type="text" class="form-text-120 form-celular" value="<?php echo $row['tel_cel']; ?>" />
									</td>
									<td>E-mail:</td>
									<td>
										<input type="text" class="form-email" value="<?php echo $row['e_mail']; ?>" />
									</td>
								</tr>
								<tr>
									<td>Estado civil:</td>
									<td>
										<input type="text" class="form-text-180" value="<?php echo $row['nome_estado_civil']; ?>" />
									</td>
									<td>Formação:</td>
									<td>
										<input type="text" class="form-text-180" value="<?php echo $row['nome_escolaridade']; ?>" />
									</td>
								</tr>
								<tr>
								  <td>Função:</td>
									<td>
										<input type="text" class="form-text-180" value="<?php echo $row['nome_funcao']; ?>" />
									</td>
									<td>Cidade:</td>
									<td>
										<input type="text" class="form-text-180" value="<?php echo $row['nome_cidade']; ?>" />
									</td>
								</tr>
								<tr>
									<td>Região:</td>
									<td>
										<input type="text" class="form-text-180" value="<?php echo $row['nome_regiao']; ?>" />
									</td>
									<td>Bairro:</td>
									<td>
										<input type="text" class="form-text-180" value="<?php echo $row['nome_bairro']; ?>" />
									</td>
								</tr>
								<tr>
									<td>Igreja:</td>
									<td>
										<input type="text" class="form-text-180" value="<?php echo $row['nome_igreja']; ?>" />
									</td>
									<td>CEP:</td>
									<td>
										<input type="text" class="form-text-120 form-cep" value="<?php echo $row['cep']; ?>" />
									</td>
								</tr>
								<tr>
									<td>Entrada na Universal:</td>
									<td>
										<input type="text" class="form-data" value="<?php echo $row['entrada_iurd']; ?>" />
									</td>
									<td>Entrada na FJU:</td>
									<td>
										<input type="text" class="form-data" value="<?php echo $row['entrada_fj']; ?>" />
									</td>
								</tr>
							</table>
							<!-- Fim Tabela + Info -->
						</div>
					</div>
				</div>
				<!-- Modal //-->
			</td>
		</tr>
<?php endwhile; ?>
<?php } ?>
	</tbody>
</table>

<?php /* JQuery */ ?>
<script>
	$("#form-cidade").on("change", function(e){
		//Popula Regiões
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
	});
	
	$("#form-regiao").on("change", function(e){
		//Popula Igrejas
		$.ajax({
			url: "<?php echo get_home(); ?>/json/return-json.php",
			type: "POST",
			data: {opt: "igrbyreg", arg: $(this).val()},
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
</script>