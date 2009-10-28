<?php
include('functions.php');
begin_pagina();
if($_SESSION['login'] == true)
    {
        header('Location: login.php');
    }
else
    {
		form_login();
    }
einde_pagina();
?> 