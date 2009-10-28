<?php
include('functions.php');
begin_pagina();
if($_SESSION['login'] != true)
    {
        if($_SERVER['REQUEST_METHOD'] == "POST")
            {
                $username = mysql_real_escape_string($_POST['gebruikersnaam']);
                $code = mysql_real_escape_string($_POST['inlogcode']);
                $gid = intval($_POST['groepsnaam']);
                if($gid == 0)
                    {
                        echo '<p class="message error">Je hebt geen groep gekozen.</p>';
                        form_login($username, $gid, $code);
                    }
                else
                    {
                        $sql = "SELECT u.id, u.naam AS eigen_naam, u.groep_id, u.getrokken, p.naam AS naam_getrokken, g.naam AS groep_naam, IF(g.beheer_id = u.id, 1, 0) AS beheerder FROM mensen u LEFT JOIN groepen g ON g.id = u.groep_id LEFT JOIN mensen p ON p.id = u.getrokken WHERE u.naam LIKE '".$username."' AND u.code = '".$code."' AND u.groep_id = ".$gid." LIMIT 1";
                        $res = mysql_query($sql) or echo_mysql_error($sql);
                        
                        if(mysql_num_rows($res) > 0)
                            {
                                 $row = mysql_fetch_assoc($res);
                                $_SESSION['login'] = true;
                                $_SESSION['uid'] = $row['id'];
                                $_SESSION['uname'] = $row['eigen_naam'];
                                $_SESSION['gid'] = $row['groep_id'];
                                $_SESSION['gname'] = $row['groep_naam'];
                                $_SESSION['uisadmin'] = $row['beheerder'];
                                $_SESSION['ugetrokkenid'] = $row['getrokken'];
                                $_SESSION['ugetrokkennaam'] = $row['naam_getrokken'];
                                header('Refresh: 0;');
                            }
                        else
                            {
                                echo '<p class="message error">Deze gebruiker bestaat niet of de ingevoerde inloggegevens zijn onjuist.</p>';
                                form_login($username, $gid, $code);
                            }
                    }
            }
        else
            {
                echo '<p class="message error">Vul je naam en inlogcode in.</p>';
                form_login();
                echo '<p><a href="register.php">Maak een nieuwe groep aan.</a></p>';
            }
    }
else
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
		if(isset($_GET['action']))
             {
                if($_GET['action'] == 'verlang')
                    {
                        $verlang = mysql_real_escape_string($_POST['verlanglijst']);
                        $sql4 = "UPDATE mensen SET verlang = '".$verlang."' WHERE id = ".$_SESSION['uid']." LIMIT 1";
                        $res4 = mysql_query($sql4) or echo_mysql_error($sql4);
                        if($getrokken)
                            {
                                $sql5 = "SELECT naam, mail FROM mensen WHERE getrokken = ".$_SESSION['uid']." AND groep_id = ".$_SESSION['gid']." LIMIT 1";
                                $res5 = mysql_query($sql5) or echo_mysql_error($sql5);
                                $row5 = mysql_fetch_assoc($res5);
                                mail($row5['naam'].' <'.$row5['mail'].'>', $_SESSION['uname'].' heeft zijn/haar verlanglijst gewijzigd', '
Hallo '.$row5['naam'].',

Je krijgt dit mailtje omdat jij '.$_SESSION['uname'].' hebt getrokken met lootjes trekken. Hij/zij heeft zijn/haar verlanglijst gewijzigd. Hieronder zie je die verlanglijst:

'."".$verlang."".'

Groet,
De Lootjestrekmachine', 'From: De Lootjestrekmachine <'.$config['mail'].'>');
                            }
                        header('Location: '.$_SERVER['PHP_SELF']);
                        exit();
                    }
            }
        if(!empty($row2['tekst']))
            {
                echo '<p><strong>'.nl2br(stripslashes($row2['tekst'])).'</strong>';
                if($_SESSION['uisadmin'] == 1)
					    {
					        echo ' [<a href="admin-changetext.php" class="edit">Wijzig deze tekst</a>]';
					    }
            }
        else
            {
                echo '<p><strong>Welkom op de lootjestrekpagina van de groep '.$_SESSION['gname'].'.</strong>';
				if($_SESSION['uisadmin'] == 1)
					    {
					        echo ' [<a href="admin-changetext.php" class="edit">Wijzig deze tekst</a>]';
					    }
            }
        if($getrokken)
            {
                echo '<p class="message success">Je hebt <strong>'.$row3['getrokkennaam'].'</strong> getrokken!</p>';
            }
        else
            {
                echo '<p class="message info">Er is nog geen trekking geweest in deze groep.</p>';
            }
        echo '
        	<p>Hieronder zie je een lijst van alle leden in jouw groep en hun verlanglijst. Klik op een naam om een e-mail te sturen.</p>
			<table class="wishlists">
				<thead>
				    <tr>
				        <th>Naam</th>
				        <th colspan="2">Verlanglijst</th>
				    </tr>
			    </thead>
			    <tbody>';
			        while($row = mysql_fetch_assoc($res))
			            {
			                 if(nl2br(strip_tags(stripslashes($row['verlang']))) == '')
			                     {
			                        $row['verlang'] = '<p class="message warning">Deze persoon heeft nog geen verlanglijst opgegeven.</p>';
			                    }
			                echo '
				    <tr>
				        <td><a href="mailto:'.$row['mail'].'">'.$row['naam'].'</a></td>
				        <td>';
				        $string = ''.nl2br(strip_tags(stripslashes($row['verlang']))).'</td>';
						echo make_clickable($string);
				        if($_SESSION['uisadmin'] == 1)
				            {
				                echo '
				        <td><a href="admin-deleteuser.php?id='.$row['id'].'" class="button trash" title="Verwijderen"><span></span>Verwijderen</a></td>';
				            }
				        echo '
				    </tr>';
			            }
			        echo '
			    	<tr>
			    		<td colspan="3" class="last-child">';
			    			if($_SESSION['uisadmin'] == 1)
							    {
							        echo ' <a href="admin-adduser.php" class="button add">Voeg nog iemand toe</a></p>';
							    }
			    	echo '
			    		</td>
			    </tbody>
			</table>';
        echo '
			<form method="post" action="'.$_SERVER['PHP_SELF'].'?action=verlang">
				<fieldset>
					<legend>Jouw verlanglijstje</legend>
					<p>
						Hier kun je je verlanglijst invullen of wijzigen, bijvoorbeeld:<br />
						<em class="example">Chocoladeletter<br />
						&euro; 3,95<br />
						http://chocoladeletters.nl</em>
					</p>
					<p>Vergeet niet op \'Opslaan\' te klikken om je wijzigingen op te slaan.</p>
			    	<p><textarea name="verlanglijst">'.strip_tags(stripslashes($row3['verlang'])).'</textarea><p>
			    	<p><input type="submit" value="Opslaan" /></p>
			    </fieldset>
			</form>';
    }
einde_pagina();
?> 