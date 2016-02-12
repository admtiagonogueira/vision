<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: acesso.php'); exit(); } ?>

<?php
try {
	$rs_cat = $conx->prepare('SELECT id, nome_categoria, url_icone FROM fj_categoria WHERE level = ?');
	$rs_cat->bindParam(1, $_SESSION['user_level']);
	$rs_cat->execute();
} catch (PDOException $e) {
	exit('<h3>Erro de conexão com o Banco de Dados!</h3>');
}
?>

<div id="corpo">
	<div id="menu-principal"><!-- -->

<!-- Btn Home -->
<a href="<?php echo get_home(); ?>">
	<div class="cat-menu">
		<div class="cat-h1">
		<img src="img/icon_home.png" id="icone" />
		<span class="cat-txt">PÁGINA INICIAL</span></div>
	</div>
</a>
		
<?php
	while($row_cat = $rs_cat->fetch(PDO::FETCH_ASSOC)){
		echo '<div class="cat-menu">
		<div class="cat-h1">
		<img src="'.$row_cat['url_icone'].'" id="icone" />
		<img src="img/seta-cima-menu.png" id="seta" />
		<span class="cat-txt">'.$row_cat['nome_categoria'].'</span></div>
		<ul class="item">';
			$rs_bot = $conx->prepare('SELECT nome_botao, link FROM fj_botoes WHERE level = ? AND fk_categoria_id = ?');
			$rs_bot->bindParam(1, $_SESSION['user_level']);
			$rs_bot->bindParam(2, $row_cat['id']);
			$rs_bot->execute();
			while($row_bot = $rs_bot->fetch(PDO::FETCH_ASSOC)){
				echo '<a href="'.$row_bot['link'].'">
				<li class="sub-item">'.$row_bot['nome_botao'].'</li>
				</a>';
			}
		echo '</ul></div>';
	}
?>

<!-- BANNER E ADS -->
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="230" height="230" align="middle" style="margin-top:25px;">
				<param name="movie" value="http://blogs.universal.org/renatocardoso/assets/250x250.swf">
				<param name="quality" value="high">
				<param name="bgcolor" value="#ffffff">
				<param name="play" value="true">
				<param name="loop" value="true">
				<param name="wmode" value="window">
				<param name="scale" value="showall">
				<param name="menu" value="true">
				<param name="devicefont" value="false">
				<param name="salign" value="">
				<param name="allowScriptAccess" value="sameDomain">
				<param name="autoPlay" value="true"></param>
				<!--[if !IE]>-->
				<object type="application/x-shockwave-flash" data="http://blogs.universal.org/renatocardoso/assets/250x250.swf" width="230" height="230" style="margin-top:25px;">
					<param name="movie" value="http://blogs.universal.org/renatocardoso/assets/250x250.swf">
					<param name="quality" value="high">
					<param name="bgcolor" value="#ffffff">
					<param name="play" value="true">
					<param name="loop" value="true">
					<param name="wmode" value="window">
					<param name="scale" value="showall">
					<param name="menu" value="true">
					<param name="devicefont" value="false">
					<param name="salign" value="">
					<param name="allowScriptAccess" value="sameDomain">
					<param name="autoPlay" value="true"></param>
				<!--<![endif]-->
					<a href="http://www.adobe.com/go/getflash">
						<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player">
					</a>
				<!--[if !IE]>-->
				</object>
				<!--<![endif]-->
</object>

<blockquote class="instagram-media" data-instgrm-version="6" style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:658px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px); margin-top:10px;">
	<div style="padding:8px;">
		<div style=" background:#F8F8F8; line-height:0; margin-top:40px; padding:50.0% 0; text-align:center; width:100%;">
			<div style=" background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACwAAAAsCAMAAAApWqozAAAAGFBMVEUiIiI9PT0eHh4gIB4hIBkcHBwcHBwcHBydr+JQAAAACHRSTlMABA4YHyQsM5jtaMwAAADfSURBVDjL7ZVBEgMhCAQBAf//42xcNbpAqakcM0ftUmFAAIBE81IqBJdS3lS6zs3bIpB9WED3YYXFPmHRfT8sgyrCP1x8uEUxLMzNWElFOYCV6mHWWwMzdPEKHlhLw7NWJqkHc4uIZphavDzA2JPzUDsBZziNae2S6owH8xPmX8G7zzgKEOPUoYHvGz1TBCxMkd3kwNVbU0gKHkx+iZILf77IofhrY1nYFnB/lQPb79drWOyJVa/DAvg9B/rLB4cC+Nqgdz/TvBbBnr6GBReqn/nRmDgaQEej7WhonozjF+Y2I/fZou/qAAAAAElFTkSuQmCC); display:block; height:44px; margin:0 auto -44px; position:relative; top:-22px; width:44px;"></div>
		</div>
		<p style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; line-height:17px; margin-bottom:0; margin-top:8px; overflow:hidden; padding:8px 0 7px; text-align:center; text-overflow:ellipsis; white-space:nowrap;">
			<a href="https://www.instagram.com/p/BAdHxB0QGEV/" style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px; text-decoration:none;" target="_blank">Uma foto publicada por Edir Macedo (@bispomacedo)</a> em <time style=" font-family:Arial,sans-serif; font-size:14px; line-height:17px;" datetime="2016-01-12T22:02:05+00:00">Jan 12, 2016 às 2:02 PST</time>
		</p>
	</div>
</blockquote>
<script async defer src="//platform.instagram.com/en_US/embeds.js"></script>
<!-- FIM BANNER E ADS -->

	</div><!-- -->

	<span id="loading">
		<img src="img/ajax-loader.gif" class="ajax-loader" />
		<p>Carregando...</p>
	</span>
	
	<div id="conteudo">
		<?php
			if (isset($_GET['p'])){
				include_once('includes/' .$_GET['p']. '.php');
			} else {
				include_once('includes/' .$_SESSION['user_level']. '-principal.php');
			}
		?>
	</div>
	
</div>