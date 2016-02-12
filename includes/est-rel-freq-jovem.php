<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php verifyLevel('est'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('RELATÓRIO DE FREQUÊNCIA'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Frequência registrada'); ?>

<p>
<span style="font-weight:bold;">OBS:</span> Ao clicar em cima da frequência é aberta uma caixa de diálogo com a descrição da reunião.<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Quando a Frequência Jovem da última semana nao é registrada, o registro fica com a cor laranja.
</p><br />

<?php
$form_id_estado = $_SESSION['estado'];
?>

<?php
//Busca as igrejas de forma não repetida (DISTINCT)
try{
	$rsIgreja = $conx->prepare('
								SELECT DISTINCT a.fk_q_igreja_id FROM fj_freq_equipe a
								INNER JOIN fj_q_igreja b ON (a.fk_q_igreja_id = b.id)
								WHERE a.fk_estado_id = ?
								AND b.catedral = \'N\'
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
			<td>Igreja</td>
			<td>Cidade</td>
			<td>Gráfico</td>
			<td>Penúltimo</td>
			<td>Último</td>
			<td>Status</td>
		</tr>
	</thead>
	<tbody>

<?php while ($row = $rsIgreja->fetch(PDO::FETCH_ASSOC)) : ?>

<?php
//Busca registros de frequência cadastrados
try{
	$rs = $conx->prepare('
						 SELECT DATE_FORMAT(a.data_reuniao, \'%d/%m/%Y\') as data_reuniao, a.qtd_jovens, a.descricao, b.nome_igreja, c.nome_cidade FROM fj_freq_equipe a
						 INNER JOIN fj_q_igreja b ON (a.fk_q_igreja_id = b.id)
						 INNER JOIN fj_cidade c ON (a.fk_cidade_id = c.id)
						 WHERE a.fk_q_igreja_id = ?
						 ORDER BY a.data_reuniao DESC, c.nome_cidade ASC, b.nome_igreja ASC
						 LIMIT 0,2
						');
	$rs->bindParam(1, $row['fk_q_igreja_id']);
	$rs->execute();
	$rs = $rs->fetchAll(PDO::FETCH_ASSOC);
	
	//VERIIFCA O MAXIMO DE JOVENS QUE A IGREJA JA REGISTROU EM UMA FREQUENCIA JOVEM
	//$rsMaiorFreq = $conx->prepare('SELECT MAX(qtd_jovens) AS qtd_jovens FROM fj_freq_equipe WHERE fk_q_igreja_id = ?');
	//$rsMaiorFreq->bindParam(1, $row['fk_q_igreja_id']);
	//$rsMaiorFreq->execute();
	//$rsMaiorFreq = $rsMaiorFreq->fetchAll(PDO::FETCH_ASSOC);
	//Colocar a linha abaixo na tabela onde deve aparecer o máximo de jovens de uma reunião de uma determinada igreja
	//echo $rsMaiorFreq[0]['qtd_jovens'];
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
if (!isset($rs[0]['nome_igreja'])) $rs[0]['nome_igreja'] = '';
if (!isset($rs[0]['nome_cidade'])) $rs[0]['nome_cidade'] = '';

//Corre o array e preenche os vazios com "" ou "S/ REG"
for ($i = 0; $i <= 1; $i++){
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
	$bgUltReuniao = '';
} else {
	$bgUltReuniao = 'style="background-color: #FF9933;"';
}
?>

		<tr>
			<td><?php echo $rs[0]['nome_igreja']; ?></td>
			<td><?php echo $rs[0]['nome_cidade']; ?></td>
			<td>
				<button class="abrir-dialog-ui">Gráfico</button>
				<!-- Modal -->
				<div class="newdialog-boxes">
					<div class="newdialog"><a href="#" class="close"><img src="img/fechar-modal.png" /></a><br />
						<div>
							<h4 class="newdialog-titulo">Visualização de Gráficos</h4>
							###
						</div>
					</div>
				</div>
				<!-- Modal //-->
			</td>
			<td>
				<?php echo $rs[1]['data_reuniao'] .' (<span style="color: #D00; font-weight: bold; text-decoration: underline; cursor: pointer;" class="abrir-dialog-ui">'. $rs[1]['qtd_jovens'] .'</span>)'; ?>
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
				<?php echo $rs[0]['data_reuniao'] .' (<span style="color: #D00; font-weight: bold; text-decoration: underline; cursor: pointer;" class="abrir-dialog-ui">'. $rs[0]['qtd_jovens'] .'</span>)'; ?>
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
			<td style="text-align: center;">
				<?php
					$qtd = (int) $rs[0]['qtd_jovens'];
					switch($qtd):
						case 0:
							$colorStatus = '#AAAAAA';
							$textStatus = 'SÓ JESUS!';
							break;
						case ($qtd >= 1 && $qtd < 30):
							$colorStatus = '#000000';
							$textStatus = 'CRÍTICO';
							break;
						case ($qtd >= 30 && $qtd < 50):
							$colorStatus = '#990099';
							$textStatus = 'RAZOÁVEL';
							break;
						case ($qtd >= 50 && $qtd < 100):
							$colorStatus = '#990000';
							$textStatus = 'BOM';
							break;
						case ($qtd >= 100 && $qtd < 150):
							$colorStatus = '#339933';
							$textStatus = 'ÓTIMO';
							break;
						case ($qtd >= 150 && $qtd < 200):
							$colorStatus = '#000099';
							$textStatus = 'EXCELENTE';
							break;
						case ($qtd >= 200):
							$colorStatus = '#CC9900';
							$textStatus = 'EXTRAORDINÁRIO';
							break;
						default:
							$colorStatus = '#AAAAAA';
							$textStatus = 'INDEFINIDO';
					endswitch;
				?>
				<span style="color: #FFF; background-color: <?php echo $colorStatus; ?>; font-weight: bold; border-radius: 10px; width: 140px; display: inline-block; padding: 5px 0; text-align: center; cursor: default;" title="Preto">
					<?php echo $textStatus; ?>
				</span>
				<br />
			</td>
		</tr>
<?php endwhile; ?>
	</tbody>
</table>