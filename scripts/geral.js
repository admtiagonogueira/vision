$(function(){
/*
	$(".item").hide();
	$(".cat-h1").on("click", function(){
		var statusSeta = $(this).children("#seta").attr("src");
		if(statusSeta == "img/seta-cima-menu.png"){
			$(this).next().slideDown("slow");
			$(this).children("#seta").attr("src", "img/seta-baixo-menu.png");
			$(this).parent().siblings().find("ul").slideUp("slow");
			$(this).parent().siblings().find("#seta").attr("src", "img/seta-cima-menu.png");
		} else if(statusSeta == "img/seta-baixo-menu.png") {
			$(this).next().slideUp("slow");
			$(this).children("#seta").attr("src", "img/seta-cima-menu.png");
		}
	});//Fim Accordion
*/
	//Ajax Menu
	$(".sub-item").on("click", function(e){
		//Aplica background no item clicado e tira dos outros
		$(this).attr("style", "background-color: #ccc");
		$(this).parent().siblings().find("li").removeAttr("style");
		
		e.preventDefault();
		var link = $(this).parent().attr("href");
		
		$.ajax({
			url: "ajax.php",
			type: "POST",
			data: {p: link},
			beforeSend: function(){
				$("#conteudo").empty();
				$("#loading").css("display", "block");
			},
			error: function(){
				alert("Erro na requisição! Tente novamente.");
			},
			success: function(result){
					$("#loading").css("display", "none");
					$("#conteudo").html(result);
					//Datatables Plugin p/ funcionar c/ Ajax
					$(".tab_pagina").dataTable({
					"language": {"url": "../DataTables/Portuguese.json"},
					"iDisplayLength": 25,
					"bSort" : false
					});
					//InputMask Plugin p/ funcionar c/ Ajax
					$(".form-data").inputmask("99/99/9999");
					$(".form-celular").inputmask("(99) [9]9999-9999");
					$(".form-fone-fixo").inputmask("(99) 9999-9999");
					$(".form-cep").inputmask("99999-999");
					//Atribui popup de confirmação para botões de deletar registros C/ Ajax
					$("input.form-btn-del").on("click", function(){
						var result = window.confirm("Você deseja mesmo excluir este registro?");
						if (result){
							return true;
						} else {
							return false;
						}
					});
					//Abre modal para descrição de itens s/ Ajax
					$('.abrir-dialog-ui').click(function(e) {
						e.preventDefault();

						var el = '.newdialog'; //$(this).attr('href');

						var maskHeight = $(document).height();
						var maskWidth = $(window).width();

						$('#newdialog-mask').css({'width':maskWidth,'height':maskHeight});

						$('#newdialog-mask').fadeIn(1000);	
						$('#newdialog-mask').fadeTo("slow",0.8);	

						//Get the window height and width
						var winH = $(window).height();
						var winW = $(window).width();

						$(el).css('top',  winH/2-$(el).height()/2);
						$(el).css('left', winW/2-$(el).width()/2);


						$(this).next().children('.newdialog').fadeIn(2000);

					});

					$('.newdialog .close').click(function (e) {
						e.preventDefault();

						$('#newdialog-mask').hide();
						$('.newdialog').hide();
					});		

					$('#newdialog-mask').click(function () {
						$(this).hide();
						$('.newdialog').hide();
					});
					/* Fim Modal */
			},
			dataType: "html"
		});
		
	});

//Datatables Plugin p/ funcionar s/ Ajax
$(".tab_pagina").dataTable({
	"language": {"url": "../DataTables/Portuguese.json"},
	"iDisplayLength": 25,
	"bSort" : false
	});

//InputMask Plugin p/ funcionar s/ Ajax
$(".form-data").inputmask("99/99/9999");
$(".form-celular").inputmask("(99) [9]9999-9999");
$(".form-fone-fixo").inputmask("(99) 9999-9999");
$(".form-cep").inputmask("99999-999");
	
//Atribui popup de confirmação para botões de deletar registros s/ Ajax
$("input.form-btn-del").on("click", function(){
	var result = window.confirm("Você deseja mesmo excluir este registro?");
	if (result){
		return true;
	} else {
		return false;
	}
});

//Abre modal para descrição de itens s/ Ajax
$('.abrir-dialog-ui').click(function(e) {
	e.preventDefault();

	var el = '.newdialog'; //$(this).attr('href');

	var maskHeight = $(document).height();
	var maskWidth = $(window).width();

	$('#newdialog-mask').css({'width':maskWidth,'height':maskHeight});

	$('#newdialog-mask').fadeIn(1000);	
	$('#newdialog-mask').fadeTo("slow",0.8);	

	//Get the window height and width
	var winH = $(window).height();
	var winW = $(window).width();

	$(el).css('top',  winH/2-$(el).height()/2);
	$(el).css('left', winW/2-$(el).width()/2);


	$(this).next().children('.newdialog').fadeIn(2000);

});

$('.newdialog .close').click(function (e) {
	e.preventDefault();

	$('#newdialog-mask').hide();
	$('.newdialog').hide();
});		

$('#newdialog-mask').click(function () {
	$(this).hide();
	$('.newdialog').hide();
});
/* Fim Modal */
	
	
	//End All	
});