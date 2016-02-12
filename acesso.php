<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <!-- This file has been downloaded from Bootsnipp.com. Enjoy! -->
    <title>Vision - .:Login:.</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/gif" href="favicon.gif" >
    <link href="css/login/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        body{padding-top:20px;}
		.panel-default { margin-top: 230px; }
		.btn { background-color: #003556; border-color: #003556; }
		.btn:active { background-color: #003556; border-color: #003556; }
		.btn:hover { background-color: #003556; border-color: #000000; }
		.logo-login { position: absolute; top: 60px; left: 50%; margin-left: -125px; }
		.erro-login { display: block; color: #EE0000; text-align: center; }
    </style>
    <script src="scripts/login/jquery-1.11.1.min.js"></script>
    <script src="scripts/login/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
<img src="img/logo-login.png" alt="Vision" class="logo-login" />
    <div class="row">
		<div class="col-md-4 col-md-offset-4">
			<!-- 1 -->
    		<div class="panel panel-default" id="painel">
			  	<div class="panel-heading">
			    	<h3 class="panel-title">Logar-se no sistema</h3>
			 	</div>
			  	<div class="panel-body">
			    	<form accept-charset="UTF-8" role="form" action="login.php" method="POST">
                    <fieldset>
			    	  	<div class="form-group">
			    		    <input class="form-control" placeholder="Usuário" name="user" type="text">
			    		</div>
			    		<div class="form-group">
			    			<input class="form-control" placeholder="Senha" name="pass" type="password" value="">
			    		</div>
			    		<div class="checkbox">
			    	    	<label>
			    	    		<input name="remember" type="checkbox" value="Remember Me"> Lembrar-me
			    	    	</label>
			    	    </div>
			    		<input class="btn btn-lg btn-success btn-block" type="submit" value="Entrar">
			    	</fieldset>
			      	</form>
			    </div>
				<?php if(isset($_SESSION['msgLogin'])) echo $_SESSION['msgLogin']; session_destroy();?>
			</div>
			<!-- 0 -->
		</div>
	</div>
</div>
<script type="text/javascript">

</script>
</body>
</html>
