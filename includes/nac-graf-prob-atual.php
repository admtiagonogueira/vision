<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php require_once('dbcon.php'); ?>
<?php require_once('functions.php'); ?>
<?php verifyLevel('nac'); ?>
<?php /* ############ TÍTULO DA PÁGINA ########### */ ?>
<?php getTitulo('GRÁFICOS DE PROBLEMA (ATUAL) (' . date('d/m/Y', strtotime("-1 year")) . ' A ' .date('d/m/Y', strtotime("today")). ')'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>


<!-- ########### GRÁFICO PAÍS ########### -->
<?php getSubTitulo('Gráfico PAÍS'); ?>

<p>
<span style="font-weight:bold;">OBS:</span> Este gráfico traz uma visão, em porcentagem, dos problemas quando os jovens chegam na igreja, no país todo.<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Para números mais detalhados, consulte a seção de Estatísticas.
</p><br />

<?php
/* CONFIGURAÇÃO DAS BARRAS */
$leftBarra = 40; //Esta é o 'left' da primeira barra, depois este número vai incrementando (em Píxels)
$espacoBarra = 15; //Espaçamento entra as barras (em Píxels)
$larguraBarra = 85; //Largura da barra (em Píxels)
$recuoMedidas = 3; //Texto pequeno que fica colado à esquerda do gráfico. Por conta da fonte, há uma margem que deve ser conpensada (em Píxels)

//Verifica o valor total de um ano para trás a partir do momento da consulta
try{
	$rsTotal = $conx->prepare('SELECT COUNT(*) as qtd FROM fj_jovem WHERE entrada_iurd > DATE_ADD(NOW(), INTERVAL -1 YEAR)');
	$rsTotal->execute();
	$rsTotal = $rsTotal->fetchAll(PDO::FETCH_ASSOC);
	$rsTotal = $rsTotal[0]['qtd'];
	
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<?php
//Busca registros(parâmetro DATE_ADD da query restringe a consulta em um ano para trás a partir do momento da consulta)
try{
	$rs = $conx->prepare('
						SELECT COUNT(*) as qtd, b.area_problema FROM fj_jovem a
						INNER JOIN fj_problemas b ON (a.fk_problema_atual_id = b.id)
						WHERE a.entrada_iurd > DATE_ADD(NOW(), INTERVAL -1 YEAR)
						GROUP BY a.fk_problema_atual_id
						');
	$rs->execute();
	$rs = $rs->fetchAll(PDO::FETCH_ASSOC);
	
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<div class="grafico-pagina">
	<span style="position:absolute; top:calc(0% - <?php echo $recuoMedidas; ?>px); font-size:7px; font-weight:bold;">- 100%</span>
	<span style="position:absolute; top:calc(25% - <?php echo $recuoMedidas; ?>px); font-size:7px; font-weight:bold;">- 75%</span>
	<span style="position:absolute; top:calc(50% - <?php echo $recuoMedidas; ?>px); font-size:7px; font-weight:bold;">- 50%</span>
	<span style="position:absolute; top:calc(75% - <?php echo $recuoMedidas; ?>px); font-size:7px; font-weight:bold;">- 25%</span>

	<?php
	for ($i = 0; $i <= count($rs) - 1; $i++){
		echo '<div class="barra" style="left:' . $leftBarra . 'px; width:' . $larguraBarra . 'px;" data-height="' . ($rs[$i]['qtd'] / $rsTotal) * 100 . '"><div>' . number_format(($rs[$i]['qtd'] / $rsTotal) * 100, 1, ',', '') . '%</div><div class="descricao">' . $rs[$i]['area_problema'] . '</div></div>';
		$leftBarra += ($larguraBarra + $espacoBarra);
	}
	?>
</div>

<br />
<br />
<br />
<!-- ########### FIM GRÁFICO PAÍS ########### -->


<!-- ########### GRÁFICO ESTADOS ########### -->
<?php
//Busca os estados
try{
	$rsEstado = $conx->prepare('SELECT DISTINCT id, nome_comp_estado FROM fj_estado ORDER BY nome_comp_estado ASC');
	$rsEstado->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<?php while ($rowEstado = $rsEstado->fetch(PDO::FETCH_ASSOC)) : ?>

<?php getSubTitulo('Gráfico ' . $rowEstado['nome_comp_estado']); ?>

<p>
<span style="font-weight:bold;">OBS:</span> Este gráfico traz uma visão, em porcentagem, do número de afastados no referido estado.<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Para números mais detalhados, consulte a seção de Estatísticas.
</p><br />

<?php
/* CONFIGURAÇÃO DAS BARRAS */
$leftBarra = 40; //Esta é o 'left' da primeira barra, depois este número vai incrementando (em Píxels)
$espacoBarra = 15; //Espaçamento entra as barras (em Píxels)
$larguraBarra = 85; //Largura da barra (em Píxels)
$recuoMedidas = 3; //Texto pequeno que fica colado à esquerda do gráfico. Por conta da fonte, há uma margem que deve ser conpensada (em Píxels)
$corHex = array(null, '#000066', '#006600', '#660000', '#804d00', '#660066', '#006666', '#000066', '#006600', '#660000', '#804d00', '#660066', '#006666'); // Cor que será usada nas barras dos gráficos (o 1º null é porque corremos o array com next(), que retorna do 2º em diante)

//Verifica o valor total de um ano para trás a partir do momento da consulta
try{
	$rsTotal = $conx->prepare('SELECT COUNT(*) as qtd FROM fj_jovem WHERE entrada_iurd > DATE_ADD(NOW(), INTERVAL -1 YEAR) AND fk_estado_id = ?
								');
	$rsTotal->bindParam(1, $rowEstado['id']);
	$rsTotal->execute();
	$rsTotal = $rsTotal->fetchAll(PDO::FETCH_ASSOC);
	$rsTotal = $rsTotal[0]['qtd'];
	
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<?php
//Busca registros(parâmetro DATE_ADD da query restringe a consulta em um ano para trás a partir do momento da consulta)
try{
	$rs = $conx->prepare('
						SELECT COUNT(*) as qtd, b.area_problema FROM fj_jovem a
						INNER JOIN fj_problemas b ON (a.fk_problema_atual_id = b.id)
						WHERE a.entrada_iurd > DATE_ADD(NOW(), INTERVAL -1 YEAR)
						AND a.fk_estado_id = ?
						GROUP BY a.fk_problema_atual_id
						');
	$rs->bindParam(1, $rowEstado['id']);
	$rs->execute();
	$rs = $rs->fetchAll(PDO::FETCH_ASSOC);
	
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<div class="grafico-pagina">
	<span style="position:absolute; top:calc(0% - <?php echo $recuoMedidas; ?>px); font-size:7px; font-weight:bold;">- 100%</span>
	<span style="position:absolute; top:calc(25% - <?php echo $recuoMedidas; ?>px); font-size:7px; font-weight:bold;">- 75%</span>
	<span style="position:absolute; top:calc(50% - <?php echo $recuoMedidas; ?>px); font-size:7px; font-weight:bold;">- 50%</span>
	<span style="position:absolute; top:calc(75% - <?php echo $recuoMedidas; ?>px); font-size:7px; font-weight:bold;">- 25%</span>

	<?php
	for ($i = 0; $i <= count($rs) - 1; $i++){
		echo '<div class="barra" style="left:' . $leftBarra . 'px; width:' . $larguraBarra . 'px; background-color:' . next($corHex) . ';" data-height="' . ($rs[$i]['qtd'] / $rsTotal) * 100 . '"><div>' . number_format(($rs[$i]['qtd'] / $rsTotal) * 100, 1, ',', '') . '%</div><div class="descricao">' . $rs[$i]['area_problema'] . '</div></div>';
		$leftBarra += ($larguraBarra + $espacoBarra);
	}
	?>
</div>

<br />
<br />
<br />

<?php endwhile; ?>
<!-- ########### FIM GRÁFICO ESTADOS ########### -->

<script>
	for (var i = 0; i <= $('div.grafico-pagina .barra').length - 1; i++){
		$('div.grafico-pagina .barra').eq(i).animate({height:$('div.grafico-pagina .barra').eq(i).attr('data-height')+'%'}, 5000, 'easeInOutSine', function(){});
	}
</script>