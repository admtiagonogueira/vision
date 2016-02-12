<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php verifyLevel('est'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('LISTAGEM DE USUÁRIOS'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Usuários Cadastrados'); ?>

<?php
/** Verifica, insere, etc, tudo aqui */
$form_id_estado = $_SESSION['estado'];
?>

<?php
//Busca líderes cadastrados
try{
	$rs = $conx->prepare('
						 SELECT a.id, a.nome, b.nome_igreja, c.nome_bairro, d.nome_regiao, e.nome_cidade FROM fj_lider_equipe a
						 INNER JOIN fj_q_igreja b ON (a.fk_q_igreja_id = b.id)
						 INNER JOIN fj_bairro c ON (a.fk_bairro_id = c.id)
						 INNER JOIN fj_regiao d ON (a.fk_regiao_id = d.id)
						 INNER JOIN fj_cidade e ON (a.fk_cidade_id = e.id)
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
			<td>Líder</td>
			<td>Igreja</td>
			<td>Bairro</td>
			<td>Região</td>
			<td>Cidade</td>
			<td>Editar</td>
			<td class="col-tab-remove">Remover</td
		</tr>
	</thead>
	<tbody>
<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
		<tr>
			<td><?php echo $row['nome']; ?></td>
			<td><?php echo $row['nome_igreja']; ?></td>
			<td><?php echo $row['nome_bairro']; ?></td>
			<td><?php echo $row['nome_regiao']; ?></td>
			<td><?php echo $row['nome_cidade']; ?></td>
			<td>
				<form action="/est-cad-lider-equipe" method="POST">
					<input type="hidden" name="form-del" value="<?php echo $row['id']; ?>" />
					<input type="submit" value="editar" />
				</form>
			</td>
			<td>
				<form action="/est-cad-lider-equipe" method="POST">
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
</script>