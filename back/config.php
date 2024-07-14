<?php

$db = new SQLite3('../banco/redeSocial.sqlite');
if (!function_exists('autenticar')) {

    function autenticar()
    {
        session_start();
        if (!$_SESSION['auth']) {
            header('Location: ../front/login.php?msg=Faça login');
            exit;
        }
    }
}
