<?php
include('functions.php');
begin_pagina();
if($_SESSION['login'] == true)
    {
        unset(
            $_SESSION['login'],
            $_SESSION['uid'],
            $_SESSION['uname'],
            $_SESSION['gid'],
            $_SESSION['gname'],
            $_SESSION['uisadmin'],
            $_SESSION['ugetrokkenid'],
            $_SESSION['ugetrokkennaam']
        );
        session_destroy();
    }
header('Location: index.php');
einde_pagina();
?> 