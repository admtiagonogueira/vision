<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php
/* functions.php inclui este arquivo */

//Usado no Menu Principal e Login
const URL_HOME = 'http://visionrj.fjupara.com.br';

//Usados nas páginas
//const USER_COMMIT_ERR = 'Erro na inserção do usuário!';
const PAG_COMMIT_OK = 'Registro inserido com sucesso!';
const PAG_COMMIT_ERR = 'Erro na inserção do registro!';
const PAG_QUERY_ERR = 'Erro na consulta ao Banco de Dados!';
const PAG_EMPTY_ERR = 'Existem campos obrigatórios em branco!';
const PAG_DUPLIC_ERR = 'Registro já existe!';
const PAG_UPDATE_OK = 'Registro atualizado com sucesso!';
const PAG_UPDATE_ERR = 'Registro não atualizado!';
const USER_DUPLIC_ERR = 'Usuário já existe!';
const PASS_UPDATE_OK = 'A senha foi atualizada!';
const PASS_UPDATE_WAR = 'A senha não foi atualizada!';
const PASS_UPDATE_ERR = 'Erro na atualizção da senha!';
const PASS_VERIFY_ERR = 'A senha digitada não confere com a atual!';
const PAG_DEL_OK = 'Registro deletado com sucesso!';
const PAG_DEL_ERR = 'Erro na exclusão do registro!';
const FILE_UP_OK = 'Upload do arquivo realizado com sucesso!';
const FILE_UP_ERR = 'Erro no upload do arquivo!';
const FILE_QUERY_ERR = 'Arquivo não encontrado!';
const FILE_DEL_OK = 'Arquivo deletado com sucesso!';
const FILE_DEL_ERR = 'Erro na exclusão do arquivo!';
const EMAIL_SEND_OK = 'E-mail enviado com sucesso!';
const EMAIL_SEND_ERR = 'Erro no envio do e-mail!';
const SMS_SEND_OK = 'SMS enviado com sucesso!';
const SMS_SEND_ERR = 'Erro no envio do SMS!';

//Diretório padrão de Uploads, em relação ao index.php
const DIR_UPLOADS = 'uploads';

//Define a cor da DIV que apresenta mensagem na página
const DIV_OK = 1;
const DIV_WAR = 2;
const DIV_ERR = 3;
?>