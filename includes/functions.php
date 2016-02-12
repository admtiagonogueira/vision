<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php date_default_timezone_set('America/Belem'); ?>
<?php require_once('const.php'); ?>

<?php
// --------------------- Anti Injection -----------------------------
function anti_injection($sql){
	// remove palavras que contenham sintaxe sql
	$sql = preg_replace('/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/i', '', $sql);
	$sql = trim($sql);//limpa espaços vazio antes e depois do texto
	$sql = strip_tags($sql);//tira tags html e php
	$sql = addslashes($sql);//Adiciona barras invertidas a uma string
	
	//Retirar acentos
	$map = array(
		'á' => 'a',	'à' => 'a',	'ã' => 'a',	'â' => 'a',	'é' => 'e',	'ê' => 'e',	'í' => 'i',	'ó' => 'o',	'ô' => 'o',	'õ' => 'o',	'ú' => 'u',	'ü' => 'u',
		'ç' => 'c',	'Á' => 'A',	'À' => 'A',	'Ã' => 'A',	'Â' => 'A',	'É' => 'E',	'Ê' => 'E',	'Í' => 'I',	'Ó' => 'O',	'Ô' => 'O',	'Õ' => 'O',	'Ú' => 'U',
		'Ü' => 'U',	'Ç' => 'C'
	);
	
	$str = strtr($sql, $map);
	$str = strtoupper($str);
	
	return $str;
}

function anti_injection_noCase($sql){
	// remove palavras que contenham sintaxe sql
	$sql = preg_replace('/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/i', '', $sql);
	$sql = trim($sql);//limpa espaços vazio
	$sql = strip_tags($sql);//tira tags html e php
	$sql = addslashes($sql);//Adiciona barras invertidas a uma string
	
	return $sql;
}

//Retorna o IP co usuário
function get_client_ip() {
     $ipaddress = '';
     if (getenv('HTTP_CLIENT_IP'))
         $ipaddress = getenv('HTTP_CLIENT_IP');
     else if(getenv('HTTP_X_FORWARDED_FOR'))
         $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
     else if(getenv('HTTP_X_FORWARDED'))
         $ipaddress = getenv('HTTP_X_FORWARDED');
     else if(getenv('HTTP_FORWARDED_FOR'))
         $ipaddress = getenv('HTTP_FORWARDED_FOR');
     else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
     else if(getenv('REMOTE_ADDR'))
         $ipaddress = getenv('REMOTE_ADDR');
     else
         $ipaddress = 'UNKNOWN';

     return $ipaddress; 
}

function get_home(){
	return URL_HOME;
	//return $_SERVER['HTTP_HOST'];
}

//Msg de erro na tela de login, conforme constantes no arquivo includes/login/const.php
function msgLogin($arg){
	$_SESSION['msgLogin'] = '<span class="erro-login">'.$arg.'</span>';
	$conx = null;
	header('Location: acesso.php');
	exit();	
}

//Verifica o nível, caso não seja o especificado no argumento $level, exibe mensagem
function verifyLevel($level){
	if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] != $level) {
		echo 'Usuário sem permissão para acessar esta página!';
		exit();
	}
}

//Retorna o título da página inserido formatado
function getTitulo($arg) {
	echo '<span class="titulo-pag"><h2><img src="../img/titulo-pag.png" /> '.$arg.'</h2></span>';
}

//Retorna um sub-título inserido formatado
function getSubTitulo($arg) {
	echo '<span class="sub-titulo-pag"><h3><img src="../img/sub-titulo-pag.png" /> '.$arg.'&nbsp;</h3></span>';
}

//Exibe um DIV com sucesso ou erro de consulta ou inserção nas páginas
//Recebe uma constante com a mensagem e outra com o tipo de janela (verde/vermelha)
//As constantes encontram-se em "/includes/const.php"
function getDivResult($constMsg, $constTipo){
	if ($constTipo === 1){
		echo '<div class="pag-div-ok"><img src="../img/icon-sucesso.png" class="pag-div-img" />'.$constMsg.'</div>';	
	} else if ($constTipo === 2){
		echo '<div class="pag-div-aviso"><img src="../img/icon-aviso.png" class="pag-div-img" />'.$constMsg.'</div>';
	} else if ($constTipo === 3){
		echo '<div class="pag-div-erro"><img src="../img/icon-erro.png" class="pag-div-img" />'.$constMsg.'</div>';	
	}
}

//Calcula a idade a partir de uma data inserida, no formato aaaa-mm-dd
function calc_idade($data_nascimento) {
	$date = new DateTime($data_nascimento); // data de nascimento
	$interval = $date->diff(new DateTime(date('Y-m-d'))); // data definida (hoje)

	echo $interval->format( '%Y' ); // Idadel final
}

?>