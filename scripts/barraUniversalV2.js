// JavaScript Document
document.write('<link rel="stylesheet" type="text/css" media="all" href="http://www.universal.org/estiloV2.css" />')
document.write('<div id="universal_bar" class="menu">')
	document.write('<div class="alinhar">')
		document.write('<div class="lgoSite"><a href="http://www.universal.org/" target="_blank"><img src="http://www.universal.org/images/logo.png" /></a></div>')
		
		document.write("<form class=\"iphoneMenu\">");
			document.write("<select name=\"menu\" onchange=\"javascript: document.location = this.value;\">")
				document.write("<option value=\"http://www.universal.org/\">Home</option>");
				document.write("<option value=\"http://www.universal.org/quem-somos\">Quem Somos</option>");
				document.write("<option value=\"http://www.universal.org/noticias-da-universal/\">Editorias</option>");
				document.write("<option value=\"http://www.universal.org/servicos.html\">Servi&ccedil;os</option>");
				document.write("<option value=\"http://www.universal.org/agenda.html\">Agenda</option>");
				document.write("<option value=\"http://www.universal.org/tv/\">TV Universal</option>");
				document.write("<option value=\"http://www.universal.org/blogs\">Blogs</option>");
				document.write("<option value=\"http://www.universal.org/sos\">Pastor Online</option>");
				document.write("<option value=\"http://doacao.universal.org/\">Doa&ccedil;&otilde;es</option>");
			document.write("</select>");
		document.write("</form>");
		
		document.write('<ul class="menuSite">')
			document.write('<li class="item-1"><a href="http://www.universal.org/quem-somos"  target="_blank">Quem Somos</a></li>')
			document.write('<li class="item-2"><a href="http://www.universal.org/noticias-da-universal/"  target="_blank">Editorias</a></li>')
			document.write('<li class="item-3"><a href="http://www.universal.org/servicos.html" target="_blank">Servi&ccedil;os</a></li>')
			document.write('<li class="item-4"><a href="http://www.universal.org/agenda.html" target="_blank">Agenda</a></li>')
			document.write('<li class="item-5"><a href="http://www.universal.org/tv/" target="_blank" rel="nofollow">TV Universal</a></li>')
			document.write('<li class="item-6"><a href="http://www.universal.org/blogs" target="_blank">Blogs</a></li>')
			document.write('<li class="item-9"><a href="http://www.universal.org/sos" target="_blank">Pastor Online</a></li>')
			document.write('<li class="item-7"><a href="http://doacao.universal.org/" target="_blank" rel="nofollow">Doa&ccedil;&otilde;es</a></li>')


			document.write('<li class="item-10">')
			document.write('<ul id="plugins">')


				document.write('<li id="pBusca">')
					document.write('<div id="blc_form_busca">')
						document.write('<form id="form_busca" name="form_busca" action="http://www.universal.org/busca" method="get">')
							document.write('<input type="text" id="termo" name="termo" value="Busca">')
							document.write('<input type="hidden" id="busca_ativa" name="busca_ativa" value="busca_ativa">')
							document.write('<input type="submit" id="busca_submit" name="busca_submit">')
						document.write('</form>')
					document.write('</div>')
				document.write('</li>')


				document.write('<li id="rede_fb"><a href="https://www.facebook.com/IgrejaUniversal" target="_blank">Facebook</a></li>')
				document.write('<li id="rede_tw"><a href="https://twitter.com/IgrejaUniversal" target="_blank">Twitter</a></li>')
				document.write('<li id="rede_gp"><a href="https://plus.google.com/share?url=http%3A%2F%2Fwww.universal.org%2F" target="_blank">Google+</a></li>')


			document.write('</ul>')
			document.write('</li>')
		document.write('</ul>')
		
	document.write('</div>')
document.write('</div>')

