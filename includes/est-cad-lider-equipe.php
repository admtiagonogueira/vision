<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php require_once('includes/bibliotecas/wideimage-11.02.19-lib/WideImage.php'); ?>
<?php verifyLevel('est'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('CADASTRO DE LIDER DE EQUIPE/IGREJA'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Formulário de Cadastro'); ?>

<p>
<span style="font-weight:bold;">OBS:</span> Ao excluir um líder de equipe, será excluida a equipe vinculada à ele e tudo que houver abaixo .
</p><br />

<?php
/** Verifica, insere, etc, tudo aqui */
$form_nome = isset($_POST['form-nome']) ? $_POST['form-nome'] : '';
$form_data_nascimento = isset($_POST['form-data-nascimento']) ? $_POST['form-data-nascimento'] : '';
$form_fone_fixo = isset($_POST['form-fone-fixo']) ? $_POST['form-fone-fixo'] : '';
$form_celular = isset($_POST['form-celular']) ? $_POST['form-celular'] : '';
$form_email = isset($_POST['form-email']) ? $_POST['form-email'] : '';
$form_id_estado_civil = isset($_POST['form-estado-civil']) ? $_POST['form-estado-civil'] : '';
$form_id_formacao = isset($_POST['form-formacao']) ? $_POST['form-formacao'] : '';
$form_id_funcao = isset($_POST['form-funcao']) ? $_POST['form-funcao'] : '';
$form_id_profissao = isset($_POST['form-profissao']) ? $_POST['form-profissao'] : '';
$form_id_habilidade = isset($_POST['form-habilidade']) ? $_POST['form-habilidade'] : '';
$form_id_cidade = isset($_POST['form-cidade']) ? $_POST['form-cidade'] : '';
$form_id_regiao = isset($_POST['form-regiao']) ? $_POST['form-regiao'] : '';
$form_id_bairro = isset($_POST['form-bairro']) ? $_POST['form-bairro'] : '';
$form_id_igreja = isset($_POST['form-igreja']) ? $_POST['form-igreja'] : '';
$form_cep = isset($_POST['form-cep']) ? $_POST['form-cep'] : '';
$form_data_entrada_iurd = isset($_POST['form-data-entrada-iurd']) ? $_POST['form-data-entrada-iurd'] : '';
$form_data_entrada_fj = isset($_POST['form-data-entrada-fj']) ? $_POST['form-data-entrada-fj'] : '';
$form_usuario = isset($_POST['form-usuario']) ? $_POST['form-usuario'] : '';
$form_senha = isset($_POST['form-senha']) ? $_POST['form-senha'] : '';
$form_nome = anti_injection($form_nome);
$form_data_nascimento = anti_injection($form_data_nascimento);
$form_fone_fixo = anti_injection($form_fone_fixo);
$form_celular = anti_injection($form_celular);
$form_email = anti_injection_noCase($form_email);
$form_id_estado_civil = anti_injection($form_id_estado_civil);
$form_id_formacao = anti_injection($form_id_formacao);
$form_id_funcao = anti_injection($form_id_funcao);
$form_id_profissao = anti_injection($form_id_profissao);
$form_id_habilidade = anti_injection($form_id_habilidade);
$form_id_cidade = anti_injection($form_id_cidade);
$form_id_regiao = anti_injection($form_id_regiao);
$form_id_bairro = anti_injection($form_id_bairro);
$form_id_igreja = anti_injection($form_id_igreja);
$form_cep = anti_injection($form_cep);
$form_data_entrada_iurd = anti_injection($form_data_entrada_iurd);
$form_data_entrada_fj = anti_injection($form_data_entrada_fj);
$form_usuario = anti_injection_noCase($form_usuario);
$form_senha = md5(anti_injection_noCase($form_senha));
$form_id_estado = $_SESSION['estado'];

//Verifica se usuario digitou alguma coisa
if (!empty($form_nome) && !empty($form_data_nascimento) && !empty($form_id_estado_civil) && !empty($form_id_formacao) && !empty($form_id_funcao) && !empty($form_id_profissao) && !empty($form_id_habilidade) && !empty( $form_id_cidade) && !empty($form_id_regiao) && !empty($form_id_bairro) && !empty($form_id_igreja) && !empty($form_data_entrada_iurd) && !empty($form_data_entrada_fj) && !empty($form_usuario) && !empty($form_senha)){
	try{
		//Verifica se registro ja existe
		$rs = $conx->prepare('SELECT id FROM fj_lider_equipe WHERE nome=? AND fk_q_igreja_id=? AND fk_bairro_id=? AND fk_estado_id=? AND fk_cidade_id=? AND fk_regiao_id=?');
		$rs->bindParam(1, $form_nome);
		$rs->bindParam(2, $form_id_igreja);
		$rs->bindParam(3, $form_id_bairro);
		$rs->bindParam(4, $form_id_estado);
		$rs->bindParam(5, $form_id_cidade);
		$rs->bindParam(6, $form_id_regiao);
		$rs->execute();
		$row = $rs->fetchAll(PDO::FETCH_ASSOC);
		
		//Verifica se USUÁRIO ja existe
		$rs = $conx->prepare('SELECT id FROM fj_usuario WHERE username=?');
		$rs->bindParam(1, $form_usuario);
		$rs->execute();
		$rowUser = $rs->fetchAll(PDO::FETCH_ASSOC);
		
		//Conta as linhas para verificação logo abaixo
		$numRows = count($row);
		$numRowsUser = count($rowUser);
	} catch (PDOException $e) {
		getDivResult(PAG_QUERY_ERR, DIV_ERR);
	}

	//Se não existe registro, insere, se existe, mostra erro de duplicidade
	if ($numRows === 0 AND $numRowsUser === 0){

		//Trata campos de Data, Telefone e CEP
		$form_fone_fixo = str_replace(array(')', '(', '-', ' '), '', $form_fone_fixo);
		$form_celular = str_replace(array(')', '(', '-', ' '), '', $form_celular);
		$form_data_nascimento = explode('/', $form_data_nascimento);
		$form_data_nascimento = $form_data_nascimento[2].'-'.$form_data_nascimento[1].'-'.$form_data_nascimento[0];
		$form_cep = str_replace('-', '', $form_cep);;
		$form_data_entrada_iurd = explode('/', $form_data_entrada_iurd);
		$form_data_entrada_iurd = $form_data_entrada_iurd[2].'-'.$form_data_entrada_iurd[1].'-'.$form_data_entrada_iurd[0];
		$form_data_entrada_fj = explode('/', $form_data_entrada_fj);
		$form_data_entrada_fj = $form_data_entrada_fj[2].'-'.$form_data_entrada_fj[1].'-'.$form_data_entrada_fj[0];

		try {
			//Inicia a transação
			$conx->beginTransaction();
			//Faz a inserção
			$rs = $conx->prepare('INSERT INTO fj_lider_equipe VALUES(null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, null)');
			$rs->bindParam(1, $form_id_formacao);
			$rs->bindParam(2, $form_id_estado_civil);
			$rs->bindParam(3, $form_id_regiao);
			$rs->bindParam(4, $form_id_cidade);
			$rs->bindParam(5, $form_id_estado);
			$rs->bindParam(6, $form_id_bairro);
			$rs->bindParam(7, $form_id_igreja);
			$rs->bindParam(8, $form_id_funcao);
			$rs->bindParam(9, $form_id_profissao);
			$rs->bindParam(10, $form_id_habilidade);
			$rs->bindParam(11, $form_nome);
			$rs->bindParam(12, $form_fone_fixo);
			$rs->bindParam(13, $form_celular);
			$rs->bindParam(14, $form_email);
			$rs->bindParam(15, $form_data_nascimento);
			$rs->bindParam(16, $form_cep);
			$rs->bindParam(17, $form_data_entrada_iurd);
			$rs->bindParam(18, $form_data_entrada_fj);
			$status = $rs->execute();

			$ultimo_id_inserido = $conx->lastInsertId();
			$nivel_usuario = 'loc';
			$ultimo_acesso = '0000-00-00 00:00:00';
			$usuario_ativo = 'S';
			$rs = $conx->prepare('INSERT INTO fj_usuario VALUES(null, ?, null, null, null, ?, ?, ?, ?, ?)');
			$rs->bindParam(1, $ultimo_id_inserido);
			$rs->bindParam(2, $form_usuario);
			$rs->bindParam(3, $form_senha);
			$rs->bindParam(4, $nivel_usuario);
			$rs->bindParam(5, $ultimo_acesso);
			$rs->bindParam(6, $usuario_ativo);
			$statusUser = $rs->execute();

			//Commita a transação
			$conx->commit();
			//Se a inserção foi bem-sucedida, mostra mensagem
			getDivResult(PAG_COMMIT_OK, DIV_OK);
			
			//Insere a imagem de perfil
			if (isset($_FILES) && $_FILES['form-img-file']['name'] != ''){

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
							getDivResult('Tamanho máximo da imagem foi ultrapassado! Envie uma imagem menor.', DIV_ERR);
						} else {
							$img_nome_tmp = $_FILES['form-img-file']['tmp_name'];		
							$img_novo_nome = md5(time()) . '.' . $img_extensao;

							if (move_uploaded_file($img_nome_tmp, DIR_UPLOADS . '/img_fotos_lideres/' . $img_novo_nome)){
								try{
									//Redimensiona com a biblioteca WideImage
									WideImage::load(DIR_UPLOADS . '/img_fotos_lideres/' . $img_novo_nome)->resize(128, 128, 'fill')->SaveToFile(DIR_UPLOADS . '/img_fotos_lideres/' . $img_novo_nome);
									
									//Faz a inserção
									$rs = $conx->prepare('UPDATE fj_lider_equipe SET url_foto = ? WHERE id = ?');
									$rs->bindParam(1, $img_novo_nome);
									$rs->bindParam(2, $ultimo_id_inserido);
									$rs->execute();
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
			
		} catch(PDOException $e) {
			$conx->rollBack();
			getDivResult(PAG_COMMIT_ERR, DIV_ERR); // Caso a inserção do registro falhe
		}
		
	} else {
		if ($numRows != 0) getDivResult(PAG_DUPLIC_ERR, DIV_ERR); // Erro caso o registro já exista
		if ($numRowsUser != 0) getDivResult(USER_DUPLIC_ERR, DIV_ERR); // Erro caso o usuário já exista
	}
} else if (
	isset ($_POST['submited']) &&
		(
			empty($form_nome) ||
			empty($form_data_nascimento) ||
			empty($form_id_estado_civil) ||
			empty($form_id_formacao) ||
			empty($form_id_funcao) ||
			empty($form_id_profissao) ||
			empty($form_id_habilidade) ||
			empty($form_id_cidade) ||
			empty($form_id_regiao) ||
			empty($form_id_bairro) ||
			empty($form_id_igreja) ||
			empty($form_data_entrada_iurd) ||
			empty($form_data_entrada_fj) ||
			empty($form_usuario) ||
			empty($form_senha)
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
		$rs = $conx->prepare('DELETE FROM fj_lider_equipe where id=?');
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
	$rsCidade = $conx->prepare('SELECT id, nome_cidade FROM fj_cidade WHERE fk_estado_id=?');
	$rsCidade->bindParam(1, $form_id_estado);
	$rsCidade->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
//Busca outras coisas para os selects
try{
	$rsFormacao = $conx->prepare('SELECT id, nome_escolaridade FROM fj_formacao');
	$rsFormacao->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}

try{
	$rsFuncao = $conx->prepare('SELECT id, nome_funcao FROM fj_lider_funcao');
	$rsFuncao->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}

try{
	$rsEstCivil = $conx->prepare('SELECT id, nome_estado_civil FROM fj_estado_civil');
	$rsEstCivil->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}

try{
	$rsProfissoes = $conx->prepare('SELECT id, nome_profissao FROM fj_profissoes');
	$rsProfissoes->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}

try{
	$rsHabilidades = $conx->prepare('SELECT id, nome_habilidade FROM fj_habilidades');
	$rsHabilidades->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<form method="POST" action="/est-cad-lider-equipe" enctype="multipart/form-data">
<table id="vstable">
	<tr>
		<td>Nome<span class="ast">*</span>:</td>
		<td colspan="3">
        	<input type="text" maxlength="90" placeholder="Nome do Líder" name="form-nome" class="form-nome" />
		</td>
	</tr>
	<tr>
		<td>Data de nasc.<span class="ast">*</span>:</td>
		<td>
			  <input type="text" placeholder="__/__/____" name="form-data-nascimento" class="form-data" />
		</td>
		<td>Telefone Fixo:</td>
		<td>
			  <input type="text" placeholder="(xx) 9999-9999" name="form-fone-fixo" class="form-text-120 form-fone-fixo" />
		</td>
	</tr>
	<tr>
		<td>Celular:</td>
		<td>
			<input type="text" placeholder="(xx) 99999-9999" name="form-celular" class="form-text-120 form-celular" />
		</td>
		<td>E-mail:</td>
		<td>
			<input type="text" maxlength="90" placeholder="exemplo@exemplo.com.br" name="form-email" class="form-email" />
		</td>
	</tr>
	<tr>
		<td>Estado civil<span class="ast">*</span>:</td>
		<td>
			<select name="form-estado-civil" id="form-estado-civil" class="form-selects">
			  <option value="">-- Selecione uma opção --</option>
			<?php while ($row = $rsEstCivil->fetch(PDO::FETCH_ASSOC)) : ?>
				<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_estado_civil']; ?></option>
			<?php endwhile; ?>
			</select>
		</td>
		<td>Formação<span class="ast">*</span>:</td>
		<td>
			<select name="form-formacao" id="form-formacao" class="form-selects">
			  <option value="">-- Selecione uma opção --</option>
			<?php while ($row = $rsFormacao->fetch(PDO::FETCH_ASSOC)) : ?>
				<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_escolaridade']; ?></option>
			<?php endwhile; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Profissão<span class="ast">*</span>:</td>
		<td>
			<select name="form-profissao" id="form-profissao" class="form-selects">
			  <option value="">-- Selecione uma opção --</option>
			<?php while ($row = $rsProfissoes->fetch(PDO::FETCH_ASSOC)) : ?>
				<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_profissao']; ?></option>
			<?php endwhile; ?>
			</select>
		</td>
		<td>Habilidade<span class="ast">*</span>:</td>
		<td>
			<select name="form-habilidade" id="form-habilidade" class="form-selects">
			  <option value="">-- Selecione uma opção --</option>
			<?php while ($row = $rsHabilidades->fetch(PDO::FETCH_ASSOC)) : ?>
				<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_habilidade']; ?></option>
			<?php endwhile; ?>
			</select>
		</td>
	</tr>
	<tr>
	  <td>Função<span class="ast">*</span>:</td>
		<td>
			<select name="form-funcao" id="form-funcao" class="form-selects">
			  <option value="">-- Selecione uma opção --</option>
			<?php while ($row = $rsFuncao->fetch(PDO::FETCH_ASSOC)) : ?>
				<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_funcao']; ?></option>
			<?php endwhile; ?>
			</select>
		</td>
		<td>Cidade<span class="ast">*</span>:</td>
		<td>
			<select name="form-cidade" id="form-cidade" class="form-selects">
			  <option value="">-- Selecione uma opção --</option>
			<?php while ($row = $rsCidade->fetch(PDO::FETCH_ASSOC)) : ?>
				<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_cidade']; ?></option>
			<?php endwhile; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Região<span class="ast">*</span>:</td>
		<td>
			<select name="form-regiao" id="form-regiao" class="form-selects">
			  <option value="">-- Selecione uma cidade --</option>
			</select>
		</td>
		<td>Bairro<span class="ast">*</span>:</td>
		<td>
			<select name="form-bairro" id="form-bairro" class="form-selects">
			  <option value="">-- Selecione um cidade --</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Igreja<span class="ast">*</span>:</td>
		<td>
			<select name="form-igreja" id="form-igreja" class="form-selects">
			  <option value="">-- Selecione um bairro --</option>
			</select>
		</td>
		<td>CEP:</td>
		<td>
			<input type="text" placeholder="00000-000" name="form-cep" class="form-text-120 form-cep" />
		</td>
	</tr>
	<tr>
		<td>Entrada na Universal<span class="ast">*</span>:</td>
		<td>
			<input type="text" placeholder="__/__/____" name="form-data-entrada-iurd" class="form-data" />
		</td>
		<td>Entrada na FJU<span class="ast">*</span>:</td>
		<td>
			<input type="text" placeholder="__/__/____" name="form-data-entrada-fj" class="form-data" />
		</td>
	</tr>
	<tr>
		<td>Foto do Líder<span class="ast">*</span>:</td>
		<td colspan="3">
			<input type="file" name="form-img-file" />
		</td>
	</tr>
	<tr style="background-color: #eee;">
		<td >Usuário<span class="ast">*</span>:</td>
		<td>
			<input type="text" maxlength="50" placeholder="Ex: xafundiformio" name="form-usuario" class="form-text-120" />
		</td>
		<td>Senha<span class="ast">*</span>:</td>
		<td>
			<input type="password" maxlength="32" placeholder="*******" name="form-senha" class="form-text-120" />
		</td>
	</tr>
</table>
	<input type="submit" name="submited" value="Cadastrar" />
	<input type="reset" value="Limpar" />
</form>

<?php getSubTitulo('Líderes cadastrados'); ?>

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