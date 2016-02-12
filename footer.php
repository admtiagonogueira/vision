<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: acesso.php'); exit();} ?>
<?php if ($_SESSION['logado'] != true){ $conx = null; header('Location: acesso.php'); exit(); } ?>

<div class="clearfix"></div>

</div><?php /* Fim Corpo */ ?>
			
<div id="rodape">
	<img src="img/linha-color.png" />
	<div class="footer-cols">
		<?php getTitulo('Links Indicados'); ?>
		<ul>
			<li><a href="#">Bispo Macedo</a></li>
			<li><a href="#">Força Jovem Universal</a></li>
			<li><a href="#">Universal.org</a></li>
			<li><a href="#">Facebook</a></li>
			<li><a href="#">Instagram</a></li>
		</ul>
    </div>
	<div class="footer-cols">
		<?php getTitulo('Links Indicados'); ?>
		<ul>
			<li><a href="#">Bispo Macedo</a></li>
			<li><a href="#">Força Jovem Universal</a></li>
			<li><a href="#">Universal.org</a></li>
			<li><a href="#">Facebook</a></li>
			<li><a href="#">Instagram</a></li>
		</ul>
	</div>
	<div class="footer-cols">
		<?php getTitulo('Links Indicados'); ?>
		<ul>
			<li><a href="#">Bispo Macedo</a></li>
			<li><a href="#">Força Jovem Universal</a></li>
			<li><a href="#">Universal.org</a></li>
			<li><a href="#">Facebook</a></li>
			<li><a href="#">Instagram</a></li>
		</ul>
	</div>
	<div class="footer-cols">
		<?php getTitulo('Teste'); ?>
		0
	</div>
</div>

</div><?php /* Fim do All */ ?>

<div id="newdialog-mask"></div><?php /* Esta TAG mostra uma máscara preta atrás de uma janela modal, que é aplicada quando se clica em itens com a classe '.abrir-dialog-ui' - Verificar CSS e JS */ ?>

</body>
</html>