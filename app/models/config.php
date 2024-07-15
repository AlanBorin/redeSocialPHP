<?php

$db = new SQLite3(__DIR__ . '/../../database/redeSocial.sqlite');
if (!function_exists('autenticar')) {

    function autenticar()
    {
        session_start();
        if (!$_SESSION['auth']) {
            header('Location: ../views/login.php?msg=Faça login');
            exit;
        }
    }
}
