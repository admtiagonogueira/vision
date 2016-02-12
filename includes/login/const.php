<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: acesso.php'); exit();} ?>
<?php
/** ERROS DE LOGIN */
const ERR_USU_BLOQ = 'Usuário bloqueado!';
const ERR_IDENT_USU = 'Erro na identificação do usuário!';
const ERR_IDENT_LEVEL = 'Erro na identificação no nível do usuário!';
const ERR_VERIFY_DIG = 'Verifique os campos digitados!';
const ERR_DUO_LOGIN = 'Usuário já logado';
const ERR_PERM_USU = 'Usuário sem permissão para acessar esta página!';
?>
