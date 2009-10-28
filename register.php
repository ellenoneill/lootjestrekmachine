<?php
include('functions.php');
begin_pagina();
if($_SESSION['login'] == true)
    {
        header('Location: login.php');
    }
elseif($_SERVER['REQUEST_METHOD'] != "POST")
    {
        echo '
		<form method="post" action="'.$_SERVER['PHP_SELF'].'">
			<input type="hidden" name="action" value="register" />
			<fieldset>
				<legend>Nieuwe groep aanmaken</legend>
				<p><label for="groepsnaam">Groepsnaam</label><br />
				<input type="text" name="groepsnaam" /></p>
				<p><label for="gebruikersmaam">Naam</label><br />
				<input type="text" name="gebruikersnaam" /></p>
				<p><label for="mail">E-mailadres</label><br />
				<input type="text" name="mail" /></p>
				<p><input type="submit" value="Registreren" /></p>
			</fieldset>
		</form>';
    }
elseif($_POST['action'] == 'register')
    {
        $gebruikersnaam = mysql_real_escape_string(htmlspecialchars($_POST['gebruikersnaam']));
        $mail = mysql_real_escape_string($_POST['mail']);
        $groepsnaam = mysql_real_escape_string(htmlspecialchars($_POST['groepsnaam']));
        $code = keygen(10);
        if(empty($gebruikersnaam) || empty($mail))
            {
                $error = '<p class="message error">Groepsnaam, naam en e-mailadres zijn verplicht.</p>';
            }
        elseif(!mysql_is_unique($groepsnaam, 'groepen', 'naam'))
            {
                $error = '<p class=" message error">Deze groepsnaam is al in gebruik, kies een andere naam.</p>';
            }
        if(isset($error))
            {
                echo $error.'
				<form method="post" action="'.$_SERVER['PHP_SELF'].'">
				    <input type="hidden" name="action" value="register" />
					<fieldset>
						<legend>Nieuwe groep aanmaken</legend>
						<p><label for="groepsnaam">Groepsnaam</label><br />
						<input type="text" name="groepsnaam" value="'.$groepsnaam.'" /></p>
						<p><label for="gebruikersmaam">Naam</label><br />
						<input type="text" name="gebruikersnaam" value="'.$gebruikersnaam.'" /></p>
						<p><label>E-mailadres</label><br />
						<input type="text" name="mail" value="'.$mail.'" /></p>
						<p><input type="submit" value="Registreren" /></p>
					</fieldset>
				</form>';
            }
        else
            {
                $sql = "INSERT INTO mensen SET naam = '".$gebruikersnaam."', mail = '".$mail."', code = '".$code."'";
                $res = mysql_query($sql) or echo_mysql_error($sql);
                $bid = mysql_insert_id();
                $sql2 = "INSERT INTO groepen SET naam = '".$groepsnaam."', beheer_id = ".$bid."";
                $res2 = mysql_query($sql2) or echo_mysql_error($sql2);
                $gid = mysql_insert_id();
                $sql3 = "UPDATE mensen SET groep_id = ".$gid." WHERE id = ".$bid." LIMIT 1";
                $res3 = mysql_query($sql3) or echo_mysql_error($sql3);
                mail($gebruikersnaam.' <'.$mail.'>', 'Je inlogcode', '
Hallo '.$gebruikersnaam.',
Dit zijn je inloggegevens voor de Lootjestrekmachine:

Groepsnaam: '.$groepsnaam.'
Naam: '.$gebruikersnaam.'
Inlogcode: '.$code.'

Groet,
De Lootjestrekmachine', 'From: De Lootjestrekmachine <'.$config['mail'].'>');
				echo '
					<p class="message success">De groep is aangemaakt. Er is een mailtje verstuurd met je inlogcode.</p>
					<p><a href="login.php">Klik hier om in te loggen</a></p>';
            }
    }
einde_pagina();
?> 