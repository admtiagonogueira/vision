<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php verifyLevel('loc'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('CADASTRO DE LIDER DE TRIBO'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Formulário de Cadastro'); ?>

<p>
<span style="font-weight:bold;">OBS:</span> Ao excluir um líder de etribo, será excluida a tribo vinculada à ele e tudo o que estiver abaixo.<br />
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
$form_cep = isset($_POST['form-cep']) ? $_POST['form-cep'] : '';
$form_data_entrada_iurd = isset($_POST['form-data-entrada-iurd']) ? $_POST['form-data-entrada-iurd'] : '';
$form_data_entrada_fj = isset($_POST['form-data-entrada-fj']) ? $_POST['form-data-entrada-fj'] : '';
$form_nome = anti_injection($form_nome);
$form_data_nascimento = anti_injection($form_data_nascimento);
$form_fone_fixo = anti_injection($form_fone_fixo);
$form_celular = anti_injection($form_celular);
$form_email = anti_injection_noCase($form_email);
$form_id_estado_civil = anti_injection($form_id_estado_civil);
$form_id_formacao = anti_injection($form_id_formacao);
$form_id_funcao = anti_injection($form_id_funcao);
$form_cep = anti_injection($form_cep);
$form_data_entrada_iurd = anti_injection($form_data_entrada_iurd);
$form_data_entrada_fj = anti_injection($form_data_entrada_fj);
$form_id_estado = $_SESSION['estado'];
$form_id_cidade = $_SESSION['cidade'];
$form_id_regiao = $_SESSION['regiao'];
$form_id_bairro = $_SESSION['bairro'];
$form_id_igreja = $_SESSION['igreja'];
$form_id_equipe = $_SESSION['equipe'];

//Verifica se usuario digitou alguma coisa
if (!empty($form_nome) && !empty($form_data_nascimento) && !empty($form_id_estado_civil) && !empty($form_id_formacao) && !empty($form_id_funcao) && !empty($form_data_entrada_iurd) && !empty($form_data_entrada_fj)){
	try{
		//Verifica se registro ja existe
		$rs = $conx->prepare('SELECT id FROM fj_lider_tribo WHERE nome=? AND fk_q_igreja_id=? AND fk_bairro_id=? AND fk_estado_id=? AND fk_cidade_id=? AND fk_regiao_id=?');
		$rs->bindParam(1, $form_nome);
		$rs->bindParam(2, $form_id_igreja);
		$rs->bindParam(3, $form_id_bairro);
		$rs->bindParam(4, $form_id_estado);
		$rs->bindParam(5, $form_id_cidade);
		$rs->bindParam(6, $form_id_regiao);
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
			//Trata campos de Data, Telefone e CEP
			$form_fone_fixo = str_replace(array(')', '(', '-', ' '), '', $form_fone_fixo);
			$form_celular = str_replace(array(')', '(', '-', ' '), '', $form_celular);
			$form_data_nascimento = explode('/', $form_data_nascimento);
			$form_data_nascimento = $form_data_nascimento[2].'-'.$form_data_nascimento[1].'-'.$form_data_nascimento[0];
			$form_cep = str_replace('-', '', $form_cep);
			$form_data_entrada_iurd = explode('/', $form_data_entrada_iurd);
			$form_data_entrada_iurd = $form_data_entrada_iurd[2].'-'.$form_data_entrada_iurd[1].'-'.$form_data_entrada_iurd[0];
			$form_data_entrada_fj = explode('/', $form_data_entrada_fj);
			$form_data_entrada_fj = $form_data_entrada_fj[2].'-'.$form_data_entrada_fj[1].'-'.$form_data_entrada_fj[0];

			//Faz a inserção
			$rs = $conx->prepare('INSERT INTO fj_lider_tribo VALUES(null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?, null)');
			$rs->bindParam(1, $form_id_funcao);
			$rs->bindParam(2, $form_id_formacao);
			$rs->bindParam(3, $form_id_estado_civil);
			$rs->bindParam(4, $form_id_regiao);
			$rs->bindParam(5, $form_id_cidade);
			$rs->bindParam(6, $form_id_estado);
			$rs->bindParam(7, $form_id_bairro);
			$rs->bindParam(8, $form_id_igreja);
			$rs->bindParam(9, $form_id_equipe);
			$rs->bindParam(10, $form_nome);
			$rs->bindParam(11, $form_fone_fixo);
			$rs->bindParam(12, $form_celular);
			$rs->bindParam(13, $form_email);
			$rs->bindParam(14, $form_cep);
			$rs->bindParam(15, $form_data_nascimento);
			$rs->bindParam(16, $form_data_entrada_iurd);
			$rs->bindParam(17, $form_data_entrada_fj);
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
			empty($form_nome) ||
			empty($form_data_nascimento) ||
			empty($form_id_estado_civil) ||
			empty($form_id_formacao) ||
			empty($form_id_funcao) ||
			empty($form_id_cidade) ||
			empty($form_id_regiao) ||
			empty($form_id_bairro) ||
			empty($form_id_igreja) ||
			empty($form_data_entrada_iurd) ||
			empty($form_data_entrada_fj)
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
		$rs = $conx->prepare('DELETE FROM fj_lider_tribo where id=?');
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
?>

<form method="POST" action="/loc-cad-lider-tribo">
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
			<?php while ($row = $rsRegiao->fetch(PDO::FETCH_ASSOC)) : ?>
				<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_regiao']; ?></option>
			<?php endwhile; ?>
			</select>
		</td>
		<td>Bairro<span class="ast">*</span>:</td>
		<td>
			<select name="form-bairro" id="form-bairro" class="form-selects">
			<?php while ($row = $rsBairro->fetch(PDO::FETCH_ASSOC)) : ?>
				<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_bairro']; ?></option>
			<?php endwhile; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Igreja<span class="ast">*</span>:</td>
		<td>
			<select name="form-igreja" id="form-igreja" class="form-selects">
			<?php while ($row = $rsIgreja->fetch(PDO::FETCH_ASSOC)) : ?>
				<option value="<?php echo $row['id']; ?>"><?php echo $row['nome_igreja']; ?></option>
			<?php endwhile; ?>
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
</table>
	<input type="submit" name="submited" value="Cadastrar" />
	<input type="reset" value="Limpar" />
</form>

<?php getSubTitulo('Líderes cadastrados'); ?>

<?php
//Busca líderes cadastrados
try{
	$rs = $conx->prepare('
						 SELECT a.id, a.nome, a.tel_cel, a.tel_fx, a.data_nascimento, b.nome_funcao FROM fj_lider_tribo a
						 INNER JOIN fj_lider_funcao b ON (a.fk_lider_funcao_id = b.id)
						 WHERE a.fk_q_igreja_id = ?
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
			<td>Líder</td>
			<td>Função</td>
			<td>Celular</td>
			<td>Fone fixo</td>
			<td>Idade</td>
			<td class="col-tab-remove">Remover</td>
		</tr>
	</thead>
	<tbody>
<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
		<tr>
			<td><?php echo $row['nome']; ?></td>
			<td><?php echo $row['nome_funcao']; ?></td>
			<td><?php echo $row['tel_cel']; ?></td>
			<td><?php echo $row['tel_fx']; ?></td>
			<td><?php calc_idade($row['data_nascimento']); ?></td>
			<td>
				<form action="/loc-cad-lider-tribo" method="POST">
					<input type="hidden" name="form-del" value="<?php echo $row['id']; ?>" />
					<input type="submit" class="form-btn-del" value="excluir" />
				</form>
			</td>
		</tr>
<?php endwhile; ?>
	</tbody>
</table>