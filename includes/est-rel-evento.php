<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php verifyLevel('est'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('RELATÓRIO DE EVENTOS'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php getSubTitulo('Eventos registrados'); ?>

<?php
$form_id_estado = $_SESSION['estado'];
?>

<?php
//Busca registros cadastrados
try{
	$rs = $conx->prepare('
						 SELECT a.id, a.nome_evento, DATE_FORMAT(a.data_evento, \'%d/%m/%Y\') as data_evento, a.qtd, a.descricao, b.nome_igreja FROM fj_eventos a
						 INNER JOIN fj_q_igreja b ON (a.fk_q_igreja_id = b.id)
						 WHERE a.fk_estado_id = ? AND b.catedral = \'N\'
						 ORDER BY a.data_evento DESC
						 LIMIT 0, 300
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
			<td>Nome do Evento</td>
			<td>Data do Evento</td>
			<td>Público</td>
			<td>Igreja</td>
			<td>Descrição</td>
		</tr>
	</thead>
	<tbody>
<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
		<tr>
			<td><?php echo $row['nome_evento']; ?></td>
			<td><?php echo $row['data_evento']; ?></td>
			<td><?php echo $row['qtd']; ?></td>
			<td><?php echo $row['nome_igreja']; ?></td>
			<td>
				<button class="abrir-dialog-ui">Visualizar</button>
				<!-- Modal -->
				<div class="newdialog-boxes">
					<div class="newdialog"><a href="#" class="close"><img src="img/fechar-modal.png" /></a><br />
						<div>
							<h4 class="newdialog-titulo">Descrição</h4>
							<?php echo $row['descricao']; ?>
						</div>
					</div>
				</div>
				<!-- Modal //-->
			</td>
		</tr>
<?php endwhile; ?>
	</tbody>
</table>