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
        if(isset($_GET['id']) && isset($_GET['delete']))
            {
                $id = intval($_GET['id']);
                $sql = "SELECT groep_id, naam, getrokken FROM mensen WHERE id = ".$id." LIMIT 1";
                $res = mysql_query($sql) or echo_mysql_error($sql);
                if(mysql_num_rows($res) > 0)
                    {
                        $row = mysql_fetch_assoc($res);
                        if($row['groep_id'] != $_SESSION['gid'])
                            {
                                echo '
                                	<p class="message error">Deze gebruiker zit niet in jouw groep, je kunt hem dus niet verwijderen.</p>
									<p><a href="login.php">&laquo; Terug naar het overzicht</a></p>';
                                einde_pagina();
                                exit();
                            }
                        elseif($row['getrokken'] != 0)
                            {
                                echo '
                                	<p class="message error">Je kunt geen mensen verwijderen als de trekking al geweest is.</p>
									<p><a href="login.php">&laquo; Terug naar het overzicht</a></p>';
                                einde_pagina();
                                exit();
                            }
                        elseif($id == $_SESSION['uid'])
                            {
                                echo '
                                	<p class="message warning">Je kunt jezelf niet verwijderen.</p>
									<p><a href="login.php">&laquo; Terug naar het overzicht</a></p>';
                                einde_pagina();
                                exit();
                            }
                        else
                            {
                                $sql = "DELETE FROM mensen WHERE id = ".$id." LIMIT 1";
                                $res = mysql_query($sql) or echo_mysql_error($sql);
                                echo '
                                	<p class="message success">'.$row['naam'].' is verwijderd.</p>
									<p><a href="login.php">&laquo; Terug naar het overzicht</a></p>';
                            }
                    }
                else
                    {
                        echo '
                        	<p class="message error">Deze gebruiker bestaat niet.</p>
							<p><a href="login.php">&laquo; Terug naar het overzicht</a></p>';
                        einde_pagina();
                        exit();
                    }
            }
        elseif(isset($_GET['id']))
            {
                $id = intval($_GET['id']);
                $sql = "SELECT naam, getrokken FROM mensen WHERE id = ".$id." LIMIT 1";
                $res = mysql_query($sql) or echo_mysql_error($sql);
                if(mysql_num_rows($res) > 0)
                    {
                        $row = mysql_fetch_assoc($res);
                        if($row['getrokken'] != 0)
                            {
                                echo '
                                	<p class="message error">Je kunt geen mensen verwijderen als de trekking al geweest is.</p>
									<p><a href="login.php">&laquo; Terug naar het overzicht</a></p>';
                                einde_pagina();
                                exit();
                            }
                        elseif($id == $_SESSION['uid'])
                            {
                                echo '
                                	<p class="message warning">Je kunt jezelf niet verwijderen.</p>
									<p><a href="login.php">&laquo; Terug naar het overzicht</a></p>';
                                einde_pagina();
                                exit();
                            }
                        echo '
							<form method="post" action="'.$_SERVER['PHP_SELF'].'">
								<fieldset>
									<legend>Weet je zeker dat je '.$row['naam'].' wilt verwijderen?</legend>
							    	<p><input type="button" onclick="window.location = \''.$_SERVER['PHP_SELF'].'?id='.$id.'&delete\'" value="Ja" /> <input type="button" onclick="window.location = \'login.php\'" value="Nee" /></p>
							    </fieldset>
							</form>
                		';
                    }
                else
                    {
                        echo '
                        	<p class="message error">Deze gebruiker bestaat niet.</p>
							<p><a href="login.php">&laquo; Terug naar het overzicht</a></p>';
                    }
            }
        else
            {
                echo '
                	<p class="message warning">Je hebt geen gebruiker gekozen.</p>
					<p><a href="login.php">&laquo; Terug naar het overzicht</a></p>';
            }
    }
else
    {
        echo '<p class="message warning">Je moet ingelogd zijn (als beheerder) om deze pagina te kunnen bekijken.</p>';
    }
einde_pagina();
?> 