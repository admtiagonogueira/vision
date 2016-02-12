<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php verifyLevel('nac'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('RELATÓRIO DE FREQUÊNCIA'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Frequência Catedrais'); ?>

<p>
<span style="font-weight:bold;">OBS:</span> Ao clicar em cima da frequência é aberta uma caixa de diálogo com a descrição da reunião.<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Quando a Frequência Jovem da última semana nao é registrada, o registro fica com a cor laranja.
</p><br />

<?php
//Busca as igrejas de forma não repetida (DISTINCT)
try{
	$rsIgreja = $conx->prepare('
								SELECT DISTINCT a.fk_q_igreja_id FROM fj_freq_equipe a
								INNER JOIN fj_q_igreja b ON (a.fk_q_igreja_id = b.id)
								WHERE b.catedral = \'S\'
								');
	$rsIgreja->bindParam(1, $form_id_estado);
	$rsIgreja->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<table class="tab_pagina tab_relatorio">
	<thead>
		<tr>
			<td>Estado</td>
			<td>#</td>
			<td>#</td>
			<td>#</td>
			<td>#</td>
			<td>Histórico</td>
		</tr>
	</thead>
	<tbody>

<?php while ($row = $rsIgreja->fetch(PDO::FETCH_ASSOC)) : ?>

<?php
//Verifica o valor máximo para colocar como máximo do gráfico
try{
	$rsMaxGrafico = $conx->prepare('
						 SELECT MAX(qtd_jovens) AS max_grafico
						 FROM fj_freq_equipe
						 WHERE fk_q_igreja_id = ?
						 ORDER BY data_reuniao DESC
						 LIMIT 0,12
						');
	$rsMaxGrafico->bindParam(1, $row['fk_q_igreja_id']);
	$rsMaxGrafico->execute();
	$rsMaxGrafico = $rsMaxGrafico->fetchAll(PDO::FETCH_ASSOC);
	//Define o máximo do gráfico
	$max_grafico = $rsMaxGrafico[0]['max_grafico'] + 50;
	
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}

//Busca registros de frequência cadastrados
try{
	$rs = $conx->prepare('
						 SELECT DATE_FORMAT(a.data_reuniao, \'%d/%m/%y\') as data_reuniao, a.qtd_jovens, a.descricao, b.nome_comp_estado FROM fj_freq_equipe a
						 INNER JOIN fj_estado b ON (a.fk_estado_id = b.id)
						 WHERE a.fk_q_igreja_id = ?
						 ORDER BY a.data_reuniao DESC
						 LIMIT 0,12
						');
	$rs->bindParam(1, $row['fk_q_igreja_id']);
	$rs->execute();
	$rs = $rs->fetchAll(PDO::FETCH_ASSOC);
	
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<?php
/*
Todos os índices $rs[0] são relativos à última reunião e os $rs[1] à penúltima.
Para buscar registro smais antigos, deve-se alterar o LIMIT na query e ir adicionando mais índices: [2], [3], [4], ...
*/

// Caso o índice não exista, preenche com S/ REG ou vazio
//Descrição vai dar erro de indice indefinido se estiver vazio, mas está funcionando
if (!isset($rs[0]['nome_comp_estado'])) $rs[0]['nome_comp_estado'] = '';

//Corre o array e preenche os vazios com "" ou "S/ REG"
for ($i = 0; $i <= 11; $i++){
	if (!isset($rs[$i]['qtd_jovens'])) $rs[$i]['qtd_jovens'] = 'S/ REG.';
	if (!isset($rs[$i]['data_reuniao'])) $rs[$i]['data_reuniao'] = '';
}

//Verifica se registro é do último sábado
$hoje = date('d/m/Y');
$ultDomingo = date('d/m/Y',strtotime('last Sunday'));
$ultSegunda = date('d/m/Y',strtotime('last Monday'));
$ultTerca = date('d/m/Y',strtotime('last Tuesday'));
$ultQuarta = date('d/m/Y',strtotime('last Wednesday'));
$ultQuinta = date('d/m/Y',strtotime('last Thursday'));
$ultSexta = date('d/m/Y',strtotime('last Friday'));
$ultSabado = date('d/m/Y',strtotime('last Saturday'));
/*
Caso o último registro não seja o último sábado, ou último domingo, ou segunda, etc...
Assim, evidencia com cor se a última frequência não estiver dentro da última semana.
*/

if (
	($rs[0]['data_reuniao'] == $hoje) ||
	($rs[0]['data_reuniao'] == $ultDomingo) ||
	($rs[0]['data_reuniao'] == $ultSegunda) ||
	($rs[0]['data_reuniao'] == $ultTerca) ||
	($rs[0]['data_reuniao'] == $ultQuarta) ||
	($rs[0]['data_reuniao'] == $ultQuinta) ||
	($rs[0]['data_reuniao'] == $ultSexta) ||
	($rs[0]['data_reuniao'] == $ultSabado)
	)
{
	$bgUltReuniao = 'style="text-align:center;"';
} else {
	$bgUltReuniao = 'style="text-align:center; background-color: #FF9933;"';
}
?>

		<tr>
			<td style="vertical-align:middle;"><?php echo $rs[0]['nome_comp_estado']; ?></td>
			<td style="text-align:center;">
				<?php echo $rs[3]['data_reuniao'] .'<br />(<span style="color: #D00; font-weight: bold; text-decoration: underline; cursor: pointer;" class="abrir-dialog-ui">'. $rs[3]['qtd_jovens'] .'</span>)'; ?>
				<!-- Modal -->
				<div class="newdialog-boxes">
					<div class="newdialog"><a href="#" class="close"><img src="img/fechar-modal.png" /></a><br />
						<div>
							<h4 class="newdialog-titulo">Descrição</h4>
							<?php echo $rs[3]['descricao']; ?>
						</div>
					</div>
				</div>
				<!-- Modal //-->
			</td>
			<td style="text-align:center;">
				<?php echo $rs[2]['data_reuniao'] .'<br />(<span style="color: #D00; font-weight: bold; text-decoration: underline; cursor: pointer;" class="abrir-dialog-ui">'. $rs[2]['qtd_jovens'] .'</span>)'; ?>
				<!-- Modal -->
				<div class="newdialog-boxes">
					<div class="newdialog"><a href="#" class="close"><img src="img/fechar-modal.png" /></a><br />
						<div>
							<h4 class="newdialog-titulo">Descrição</h4>
							<?php echo $rs[2]['descricao']; ?>
						</div>
					</div>
				</div>
				<!-- Modal //-->
			</td>
			<td style="text-align:center;">
				<?php echo $rs[1]['data_reuniao'] .'<br />(<span style="color: #D00; font-weight: bold; text-decoration: underline; cursor: pointer;" class="abrir-dialog-ui">'. $rs[1]['qtd_jovens'] .'</span>)'; ?>
				<!-- Modal -->
				<div class="newdialog-boxes">
					<div class="newdialog"><a href="#" class="close"><img src="img/fechar-modal.png" /></a><br />
						<div>
							<h4 class="newdialog-titulo">Descrição</h4>
							<?php echo $rs[1]['descricao']; ?>
						</div>
					</div>
				</div>
				<!-- Modal //-->
			</td>
			<td <?php echo $bgUltReuniao; ?>>
				<?php echo $rs[0]['data_reuniao'] .'<br />(<span style="color: #D00; font-weight: bold; text-decoration: underline; cursor: pointer;" class="abrir-dialog-ui">'. $rs[0]['qtd_jovens'] .'</span>)'; ?>
				<!-- Modal -->
				<div class="newdialog-boxes">
					<div class="newdialog"><a href="#" class="close"><img src="img/fechar-modal.png" /></a><br />
						<div>
							<h4 class="newdialog-titulo">Descrição</h4>
							<?php echo $rs[0]['descricao']; ?>
						</div>
					</div>
				</div>
				<!-- Modal //-->
			</td>
			<td>
				<button class="abrir-dialog-ui">Gráfico</button>
				<!-- Modal -->
				<div class="newdialog-boxes">
					<div class="newdialog"><a href="#" class="close"><img src="img/fechar-modal.png" /></a><br />
						<div>
							<h4 class="newdialog-titulo">Visualização de Gráficos</h4>
							<div class="grafico-modal">
								<span style="position:absolute; top:-3px; font-size:7px; font-weight:bold;">- <?php echo round($max_grafico); ?></span>
								<span style="position:absolute; top:59.5px; font-size:7px; font-weight:bold;">- <?php echo round(($max_grafico / 4) * 3); ?></span>
								<span style="position:absolute; top:122px; font-size:7px; font-weight:bold;">- <?php echo round($max_grafico / 2); ?></span>
								<span style="position:absolute; top:184.5px; font-size:7px; font-weight:bold;">- <?php echo round($max_grafico / 4); ?></span>
								<div class="barra" style="height:<?php echo ($rs[11]['qtd_jovens'] / $max_grafico) * 100; ?>%; left:40px;"><div><?php echo $rs[11]['qtd_jovens']; ?><div class="descricao"><?php echo $rs[11]['data_reuniao']; ?></div></div></div>
								<div class="barra" style="height:<?php echo ($rs[10]['qtd_jovens'] / $max_grafico) * 100; ?>%; left:90px;"><div><?php echo $rs[10]['qtd_jovens']; ?><div class="descricao"><?php echo $rs[10]['data_reuniao']; ?></div></div></div>
								<div class="barra" style="height:<?php echo ($rs[9]['qtd_jovens'] / $max_grafico) * 100; ?>%; left:140px;"><div><?php echo $rs[9]['qtd_jovens']; ?><div class="descricao"><?php echo $rs[9]['data_reuniao']; ?></div></div></div>
								<div class="barra" style="height:<?php echo ($rs[8]['qtd_jovens'] / $max_grafico) * 100; ?>%; left:190px;"><div><?php echo $rs[8]['qtd_jovens']; ?><div class="descricao"><?php echo $rs[8]['data_reuniao']; ?></div></div></div>
								<div class="barra" style="height:<?php echo ($rs[7]['qtd_jovens'] / $max_grafico) * 100; ?>%; left:240px;"><div><?php echo $rs[7]['qtd_jovens']; ?><div class="descricao"><?php echo $rs[7]['data_reuniao']; ?></div></div></div>
								<div class="barra" style="height:<?php echo ($rs[6]['qtd_jovens'] / $max_grafico) * 100; ?>%; left:290px;"><div><?php echo $rs[6]['qtd_jovens']; ?><div class="descricao"><?php echo $rs[6]['data_reuniao']; ?></div></div></div>
								<div class="barra" style="height:<?php echo ($rs[5]['qtd_jovens'] / $max_grafico) * 100; ?>%; left:340px;"><div><?php echo $rs[5]['qtd_jovens']; ?><div class="descricao"><?php echo $rs[5]['data_reuniao']; ?></div></div></div>
								<div class="barra" style="height:<?php echo ($rs[4]['qtd_jovens'] / $max_grafico) * 100; ?>%; left:390px;"><div><?php echo $rs[4]['qtd_jovens']; ?><div class="descricao"><?php echo $rs[4]['data_reuniao']; ?></div></div></div>
								<div class="barra" style="height:<?php echo ($rs[3]['qtd_jovens'] / $max_grafico) * 100; ?>%; left:440px;"><div><?php echo $rs[3]['qtd_jovens']; ?><div class="descricao"><?php echo $rs[3]['data_reuniao']; ?></div></div></div>
								<div class="barra" style="height:<?php echo ($rs[2]['qtd_jovens'] / $max_grafico) * 100; ?>%; left:490px;"><div><?php echo $rs[2]['qtd_jovens']; ?><div class="descricao"><?php echo $rs[2]['data_reuniao']; ?></div></div></div>
								<div class="barra" style="height:<?php echo ($rs[1]['qtd_jovens'] / $max_grafico) * 100; ?>%; left:540px;"><div><?php echo $rs[1]['qtd_jovens']; ?><div class="descricao"><?php echo $rs[1]['data_reuniao']; ?></div></div></div>
								<div class="barra" style="height:<?php echo ($rs[0]['qtd_jovens'] / $max_grafico) * 100; ?>%; left:590px;"><div><?php echo $rs[0]['qtd_jovens']; ?><div class="descricao"><?php echo $rs[0]['data_reuniao']; ?></div></div></div>
							</div>
							</div>
						</div>
					</div>
				</div>
				<!-- Modal //-->
			</td>
		</tr>
<?php endwhile; ?>
	</tbody>
</table>