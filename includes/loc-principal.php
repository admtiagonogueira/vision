<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: ../acesso.php'); exit(); } ?>
<?php verifyLevel('loc'); ?>
<?php /* ############ TÍ”ULO DA PGINA ########### */ ?>
<?php getTitulo('PÁGINA INICIAL'); ?>
<?php /* ########## EDITE A PARTIR DAQUI ######### */ ?>

<?php
//Busca vídeo cadastrado
try{
	$rs = $conx->prepare('SELECT video_url FROM fj_video WHERE video_level = ? LIMIT 0,1');
	$nivel = $_SESSION['user_level'];
	$rs->bindParam(1, $nivel);
	$rs->execute();
	$rs = $rs->fetchAll();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<div id="princ-midia">
	<iframe width="398" height="298" src="<?php echo $rs[0]['video_url']; ?>" frameborder="0" allowfullscreen></iframe>
</div>

<?php
//Contagem Jovens
try{
	$rsJovens = $conx->prepare('SELECT COUNT(*) AS qtd FROM fj_jovem LIMIT 0,1');
	$rsJovens->execute();
	$rsJovens = $rsJovens->fetchAll();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}

//Contagem Líderes
try{
	$rsLideres = $conx->prepare('
						  SELECT COUNT(*) AS qtd FROM fj_lider_equipe a
						  INNER JOIN fj_q_igreja b ON (a.fk_q_igreja_id = b.id)
						  WHERE b.catedral = \'N\' LIMIT 0,1
						  ');
	$rsLideres->execute();
	$rsLideres = $rsLideres->fetchAll();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}

//Contagem Obreiros
try{
	$rsObreiros = $conx->prepare('
						  SELECT COUNT(*) AS qtd FROM fj_lider_equipe a
						  INNER JOIN fj_q_igreja b ON (a.fk_q_igreja_id = b.id)
						  WHERE b.catedral = \'N\' AND fk_lider_funcao_id = \'2\' LIMIT 0,1
						  ');
	$rsObreiros->execute();
	$rsObreiros = $rsObreiros->fetchAll();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}

//Contagem Pastores
try{
	$rsPastores = $conx->prepare('
						  SELECT COUNT(*) AS qtd FROM fj_lider_equipe a
						  INNER JOIN fj_q_igreja b ON (a.fk_q_igreja_id = b.id)
						  WHERE b.catedral = \'N\' AND fk_lider_funcao_id = \'3\' LIMIT 0,1
						  ');
	$rsPastores->execute();
	$rsPastores = $rsPastores->fetchAll();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}

//Contagem Igrejas
try{
	$rsIgrejas = $conx->prepare('SELECT COUNT(*) AS qtd FROM fj_q_igreja LIMIT 0,1');
	$rsIgrejas->execute();
	$rsIgrejas = $rsIgrejas->fetchAll();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}

//Contagem ?
/*
try{
	$rs = $conx->prepare('SELECT COUNT(*) AS qtd FROM fj_jovem LIMIT 0,1');
	$rs->execute();
	$rs = $rs->fetchAll();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
*/
?>

<div id="princ-botoes">
	<span class="princ-botao-dados" style="background-color: #036;">
		<span class="btn-dados-grd"><?php echo $rsJovens[0]['qtd']; ?></span>
		JOVENS
	</span>
	<span class="princ-botao-dados" style="background-color: #030;">
		<span class="btn-dados-grd"><?php echo $rsLideres[0]['qtd']; ?></span>
		LÍDERES
	</span>
	<span class="princ-botao-dados" style="background-color: #800;">
		<span class="btn-dados-grd"><?php echo $rsObreiros[0]['qtd']; ?></span>
		OBREIROS
	</span>
	<span class="princ-botao-dados" style="background-color: #C30;">
		<span class="btn-dados-grd"><?php echo $rsPastores[0]['qtd']; ?></span>
		PASTORES
	</span>
	<span class="princ-botao-dados" style="background-color: #333;">
		<span class="btn-dados-grd"><?php echo $rsIgrejas[0]['qtd']; ?></span>
		IGREJAS
	</span>
	<span class="princ-botao-dados" style="background-color: #606;">
		<span class="btn-dados-grd">!!</span>
		???
	</span>
</div>

<div class="clearfix"></div>
<br />

<?php getTitulo('PRÓXIMOS EVENTOS'); ?>

<?php
//Busca imagens do mural
try{
	$rs = $conx->prepare('SELECT id, img_nome FROM fj_mural WHERE img_level = ?');
	$nivel = $_SESSION['user_level'];
	$rs->bindParam(1, $nivel);
	$rs->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<div class="orbit-slide">
	<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
		<img src="<?php echo DIR_UPLOADS . '/img_orbit_slide/' . $row['img_nome']; ?>" data-caption="#caption<?php echo $row['id']; ?>" />
	<?php endwhile; ?>
</div>

<?php
//Busca captions do mural
try{
	$rs = $conx->prepare('SELECT id, img_descricao FROM fj_mural WHERE img_level = ?');
	$rs->bindParam(1, $_SESSION['user_level']);
	$rs->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
	<span class="orbit-caption" id="caption<?php echo $row['id']; ?>"><?php echo $row['img_descricao']; ?></span>
<?php endwhile; ?>

<script>
	$(window).load(function(){
		$(".orbit-slide").orbit({
			captions: true
		});
	});
</script>

<br /><br />

<?php getTitulo('NOTIFICAÇÕES'); ?>

<?php
//Busca notificações cadastradas
try{
	$rs = $conx->prepare('SELECT DATE_FORMAT(data_time, \'%d/%m/%Y\') AS data, nome_user, assunto, level, msg FROM fj_notificacao ORDER BY data_time DESC LIMIT 0,8');
	$rs->bindParam(1, $_SESSION['user_level']);
	$rs->execute();
} catch (PDOException $e) {
	getDivResult(PAG_QUERY_ERR, DIV_ERR);
}
?>

<div class="notify-div">
	<table class="notify-table">
		<tbody>
<?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)) : ?>
			<tr>
				<td>
					<p><?php echo $row['nome_user']; ?></p>
				</td>
				<td>
					<p>
						<?php echo substr($row['msg'], 0, 65); ?>... <span style="color:#888;"><?php echo $row['data']; ?></span>
					</p>
					<a href="#" class="abrir-dialog-ui"><span style="color:#006666;">[Ler mais]</span></a> 
					<!-- Modal -->
					<div class="newdialog-boxes">
						<div class="newdialog" style="width:100px;"><a href="#" class="close"><img src="img/fechar-modal.png" /></a><br />
							<div>
								<h4 class="newdialog-titulo"><?php echo $row['assunto']; ?></h4>
								<?php echo $row['msg']; ?>
							</div>
						</div>
					</div>
					<!-- Modal //-->
				</td>
			</tr>
<?php endwhile; ?>
		</tbody>
	</table>
</div>

<br /><br />

<?php getTitulo('BLOG DO BISPO MACEDO'); ?>

<!-- start feedwind code --><script type="text/javascript">document.write('\x3Cscript type="text/javascript" src="' + ('https:' == document.location.protocol ? 'https://' : 'http://') + 'feed.mikle.com/js/rssmikle.js">\x3C/script>');</script><script type="text/javascript">(function() {var params = {rssmikle_url: "http://blogs.universal.org/bispomacedo/feed/",rssmikle_frame_width: "725",rssmikle_frame_height: "400",frame_height_by_article: "5",rssmikle_target: "_blank",rssmikle_font: "Arial, Helvetica, sans-serif",rssmikle_font_size: "12",rssmikle_border: "off",responsive: "off",rssmikle_css_url: "",text_align: "left",text_align2: "left",corner: "off",scrollbar: "on",autoscroll: "on",scrolldirection: "up",scrollstep: "3",mcspeed: "20",sort: "Off",rssmikle_title: "off",rssmikle_title_sentence: "Blog do Bispo Macedo",rssmikle_title_link: "http://blogs.universal.org/bispomacedo/feed/",rssmikle_title_bgcolor: "#0066FF",rssmikle_title_color: "#033556",rssmikle_title_bgimage: "",rssmikle_item_bgcolor: "#FFFFFF",rssmikle_item_bgimage: "",rssmikle_item_title_length: "55",rssmikle_item_title_color: "#033556",rssmikle_item_border_bottom: "on",rssmikle_item_description: "on",item_link: "off",rssmikle_item_description_length: "150",rssmikle_item_description_color: "#666666",rssmikle_item_date: "gl1",rssmikle_timezone: "Etc/GMT",datetime_format: "%b %e, %Y %l:%M %p",item_description_style: "text",item_thumbnail: "full",item_thumbnail_selection: "auto",article_num: "15",rssmikle_item_podcast: "off",keyword_inc: "",keyword_exc: ""};feedwind_show_widget_iframe(params);})();</script><div style="font-size:10px; text-align:center; width:725px;"><a href="http://feed.mikle.com/" target="_blank" style="color:#CCCCCC;">RSS Feed Widget</a><!--Please display the above link in your web page according to Terms of Service.--></div><!-- end feedwind code --><!--  end  feedwind code -->