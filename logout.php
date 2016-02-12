<?php

session_start();
session_unset(); //Limpa Variáveis Globais de Sessão
session_destroy();

header('Location: acesso.php');
exit();

?>