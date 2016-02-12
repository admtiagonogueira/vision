<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php verifyLevel('nac'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('RELATÓRIO DE BATISMOS'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Relatório Catedrais'); ?>

<p>
<span style="font-weight:bold;">OBS:</span> Ao clicar em cima da frequência é aberta uma caixa de diálogo com a descrição do batismo.<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Quando o batismo não é recente (até última semana), o registro fica com a cor laranja.
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
			<td>Média</td>
		</tr>
	</thead>
	<tbody>

<?php while ($row = $rsIgreja->fetch(PDO::FETCH_ASSOC)) : ?>

<?php
//Busca registros de frequência cadastrados
try{
	$rs = $conx->prepare('
						 SELECT a.id, DATE_FORMAT(a.data_batismo, \'%d/%m/%Y\') as data_batismo, a.qtd, a.descricao, b.nome_comp_estado FROM fj_batismos a
						 INNER JOIN fj_estado b ON (a.fk_estado_id = b.id)
						 WHERE a.fk_q_igreja_id = ?
						 ORDER BY a.data_batismo DESC
						 LIMIT 0, 4
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
if (!isset($rs[0]['qtd'])) $rs[0]['qtd'] = 'S/ REG.';
if (!isset($rs[0]['data_batismo'])) $rs[0]['data_batismo'] = '';
if (!isset($rs[1]['qtd'])) $rs[1]['qtd'] = 'S/ REG.';
if (!isset($rs[1]['data_batismo'])) $rs[1]['data_batismo'] = '';
if (!isset($rs[2]['qtd'])) $rs[2]['qtd'] = 'S/ REG.';
if (!isset($rs[2]['data_batismo'])) $rs[2]['data_batismo'] = '';
if (!isset($rs[3]['qtd'])) $rs[3]['qtd'] = 'S/ REG.';
if (!isset($rs[3]['data_batismo'])) $rs[3]['data_batismo'] = '';

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
	($rs[0]['data_batismo'] == $hoje) ||
	($rs[0]['data_batismo'] == $ultDomingo) ||
	($rs[0]['data_batismo'] == $ultSegunda) ||
	($rs[0]['data_batismo'] == $ultTerca) ||
	($rs[0]['data_batismo'] == $ultQuarta) ||
	($rs[0]['data_batismo'] == $ultQuinta) ||
	($rs[0]['data_batismo'] == $ultSexta) ||
	($rs[0]['data_batismo'] == $ultSabado)
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
				<?php echo $rs[3]['data_batismo'] .'<br />(<span style="color: #D00; font-weight: bold; text-decoration: underline; cursor: pointer;" class="abrir-dialog-ui">'. $rs[3]['qtd'] .'</span>)'; ?>
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
				<?php echo $rs[2]['data_batismo'] .'<br />(<span style="color: #D00; font-weight: bold; text-decoration: underline; cursor: pointer;" class="abrir-dialog-ui">'. $rs[2]['qtd'] .'</span>)'; ?>
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
				<?php echo $rs[1]['data_batismo'] .'<br />(<span style="color: #D00; font-weight: bold; text-decoration: underline; cursor: pointer;" class="abrir-dialog-ui">'. $rs[1]['qtd'] .'</span>)'; ?>
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
				<?php echo $rs[0]['data_batismo'] .'<br />(<span style="color: #D00; font-weight: bold; text-decoration: underline; cursor: pointer;" class="abrir-dialog-ui">'. $rs[0]['qtd'] .'</span>)'; ?>
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
			<td style="text-align:right;">
				<?php
					$media_qtd_batismos = ($rs[0]['qtd'] + $rs[1]['qtd'] + $rs[2]['qtd'] + $rs[3]['qtd']) / 4;
					echo '<span style="font-weight: bold;">'. $media_qtd_batismos .'</span> P/ REUNIÃO';
				?>
			</td>
		</tr>
<?php endwhile; ?>
	</tbody>
</table>