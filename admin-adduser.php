<?php
include('functions.php');
begin_pagina();
if($_SESSION['login'] == true && $_SESSION['uisadmin'] == 1)
    {
		echo '<a href="logout.php" class="button logout">Uitloggen</a>';
        $sql = "SELECT naam, id, mail, verlang FROM mensen WHERE groep_id = ".$_SESSION['gid']." ORDER BY naam";
        $res = mysql_query($sql) or echo_mysql_error($sql);
        $sql2 = "SELECT getrokken, tekst FROM groepen WHERE id = ".$_SESSION['gid']." LIMIT 1";
        $res2 = mysql_query($sql2) or echo_mysql_error($sql2);
        $row2 = mysql_fetch_assoc($res2);
        $sql3 = "SELECT IF(g.naam = '','niemand',g.naam) AS getrokkennaam, m.getrokken AS getrokkenid, m.verlang FROM mensen m LEFT JOIN mensen g ON g.id = m.getrokken WHERE m.id = ".$_SESSION['uid']." LIMIT 1";
        $res3 = mysql_query($sql3) or echo_mysql_error($sql3);
        $row3 = mysql_fetch_assoc($res3);
        $_SESSION['ugetrokkennaam'] = $row3['getrokkennaam'];
        $_SESSION['ugetrokkenid'] = $row3['getrokkenid'];
        $getrokken = $row2['getrokken'] == 1?true:false;
		if($_SESSION['uisadmin'] == 1)
		    {
				if($getrokken)
				    {
				        echo '<p><a href="admin-draw.php" class="button draw">Trek de lootjes nogmaals</a></p>';
				    }
				else
				    {
				        echo '<p><a href="admin-draw.php" class="button draw">Trek de lootjes</a></p>';
				    }
		    }
        $sql = "SELECT getrokken FROM groepen WHERE id = ".$_SESSION['gid']." LIMIT 1";
        $res = mysql_query($sql) or echo_mysql_error($sql);
        $row = mysql_fetch_assoc($res);
         if($row['getrokken'] == 0)
             {
                if($_SERVER['REQUEST_METHOD'] != "POST")
                    {
                        echo '
							<form method="post" action="'.$_SERVER['PHP_SELF'].'">
								<input type="hidden" name="step" value="2" />
								<fieldset>
									<legend>Hoeveel gebruikers wil je toevoegen?</legend>
							    	<p><input type="text" name="aant" value="1" /> <input type="submit" value="Volgende" /></p>
							    </fieldset>
							</form>
						';
                    }
                elseif(intval($_POST['step']) == 2)
                    {
                         $aant = intval($_POST['aant']);
                         $aant = $aant > 99?99:$aant;
                         $aant = $aant < 1?1:$aant;
                         echo '
							<form method="post" action="'.$_SERVER['PHP_SELF'].'">
							    <input type="hidden" name="step" value="3" />
							    <fieldset>
							    	<legend>Gegevens van de personen die je wilt toevoegen:</legend>
								    <table class="invites">
								    	<thead>
									        <tr>
									            <th>Naam</th>
									            <th>E-mailadres</th>
									        </tr>
								        </thead>
								        <tbody>';
					                        for($i = 0; $i < $aant; $i++)
					                            {
					                                echo '
								        <tr>
								            <td><input type="text" name="namen[]" maxlength="50" /></td>
								            <td><input type="text" name="mails[]" maxlength="100" /></td>
								        </tr>';
							                            }
							                        echo '
								    	</tbody>
								    </table>
			                        <p><input type="submit" value="Opslaan" /></p>
		                        </fieldset>
							</form>
						';
                    }
                elseif(intval($_POST['step']) == 3)
                    {
                        $names = $_POST['namen'];
                        $mails = $_POST['mails'];
                        $already = array();
                        foreach($names as $key => $value)
                            {
                                $name = mysql_real_escape_string($value);
                                $mail = mysql_real_escape_string($mails[$key]);
                                $code = keygen(10);
                                if(strlen($name) > 0)
                                    {
                                        if(!is_unieke_naam_in_groep($name, $_SESSION['gid']))
                                            {
                                                $already[] = $name;
                                            }
                                        else
                                            {
                                                $sql = "INSERT INTO mensen SET
                                                naam = '".$name."',
                                                mail = '".$mail."',
                                                groep_id = ".$_SESSION['gid'].",
                                                code = '".$code."'";
                                                $res = mysql_query($sql) or echo_mysql_error($sql);
                                                mail($name.' <'.$mail.'>', 'Lootjes trekken', '
Hallo '.$name.',

Zojuist is er een account voor je aangemaakt voor de Lootjestrekmachine in de groep \''.$_SESSION['gname'].'\'.

Groepsnaam: '.$_SESSION['gname'].'
Naam: '.$name.'
Inlogcode: '.$code.'

Surf naar '.$config['website'].' om in te loggen en je verlanglijst in te vullen.
Als de trekking is geweest kun je daar zien wie je getrokken hebt.

De Lootjestrekmachine','From: De Lootjestrekmachine <'.$config['mail'].'>');
                                            }
                                    }
                            }
                        if(count($already) > 1)
                            {
                                echo '
                                	<div class="message error">
                                		<p>Deze namen zijn al in gebruik, er zijn geen accounts voor ze aangemaakt:</p>
                                		<ul>';
			                                foreach($already as $name)
			                                    {
			                                        echo '<li>'.$name.'</li>';
			                                    }
			                                echo '
		                                </ul>
		                            </div>
									<p class="message info">De overige accounts zijn wel aangemaakt, er is een mailtje verstuurd naar die mensen met instructies en een inlogcode.</p>
									<p><a href="login.php">&laquo; Terug naar het overzicht</a></p>';
                            }
                        else
                            {
                                echo '
                                	<p class="message success">De accounts zijn aangemaakt, er is een mailtje verstuurd naar de mensen met instructies en een inlogcode.</p>
									<p><a href="login.php">&laquo; Terug naar het overzicht</a></p>';
                            }
                    }
            }
        else
            {
                echo '
                	<p class="message warning">Je kunt geen gebruikers meer toevoegen als de trekking al geweest is.</p>
					<p><a href="login.php">&laquo; Terug naar het overzicht</a></p>';
            }
    }
else
    {
        echo '<p class="message warning">Je moet ingelogd zijn (als beheerder) om deze pagina te kunnen bekijken.</p>';
    }
einde_pagina();
?> 