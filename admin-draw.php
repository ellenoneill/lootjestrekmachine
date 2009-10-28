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
        if($_GET['action'] != "now")
            {
                echo '
					<form method="post" action="'.$_SERVER['PHP_SELF'].'">
						<fieldset>
							<legend>Weet je zeker dat je de trekking nu wilt uitvoeren?</legend>
					    	<p><input type="button" onclick="window.location = \''.$_SERVER['PHP_SELF'].'?action=now\'" value="Ja" /> <input type="button" onclick="window.location = \'login.php\'" value="Nee" /></p>
						</fieldset>
					</form>
                ';
            }
        else
            {
                $sql = "SELECT id, naam, mail, verlang, code FROM mensen WHERE groep_id = ".$_SESSION['gid']." ORDER BY naam";
                $res = mysql_query($sql) or echo_mysql_error($sql);
                $ids = array();
                $info = array();
                if(mysql_num_rows($res) < 2)
                    {
                         echo '
                         	<p class="message warning">Er moeten minstens twee mensen in een groep zitten voor je lootjes kunt trekken.</p>
							<p><a href="login.php">&laquo; Terug naar het overzicht</a></p>';
                         einde_pagina();
                         exit();
                    }
                else
                    {
                        while($row = mysql_fetch_assoc($res))
                            {
                                $ids[] = $row['id'];
                                $info[$row['id']] = $row;
                            }
                        $names = $got = $ids;
                        $himself = true;
                        $deze = true;
                        while($himself == true)
                            {
                                shuffle($got);
                                foreach($names as $key => $value)
                                    {
                                        if($value == $got[$key])
                                            {
                                                $deze = true;
                                            }
                                    }
                                if($deze == true)
                                    {
                                        $himself = true;
                                    }
                                else
                                    {
                                        $himself = false;
                                    }
                                $deze = false;
                            }
                        foreach($names as $key => $value)
                            {
                                if($value == $got[$key])
                                    {
                                        echo '<p class="message warning">Iemand heeft zichzelf getrokken!</p>';
                                        exit();
                                    }
                                if($value == $_SESSION['uid'])
                                    {
                                        $self = $got[$key];
                                    }
                                $sql2 = "UPDATE mensen SET getrokken = ".$got[$key]." WHERE id = ".$value." LIMIT 1";
                                $res2 = mysql_query($sql2) or echo_mysql_error($sql2);
                                if(empty($info[$got[$key]]['verlang']))
                                    {
                                        $info[$got[$key]]['verlang'] = 'Deze persoon heeft nog geen verlanglijst opgegeven.';
                                    }
                                mail($info[$value]['naam'].' <'.$info[$value]['mail'].'>', 'Lootje getrokken!', '
Hallo '.$info[$value]['naam'].',

Je hebt de volgende persoon getrokken:
'.$info[$got[$key]]['naam'].'

Hieronder vind je zijn/haar verlanglijst. Deze kan nog aangepast worden, mocht dat gebeuren, dan krijg je een mailtje. De verlanglijst is ook te bekijken op '.$config['website'].'.

'."".$info[$got[$key]]['verlang']."".'

Hieronder vind je jouw inloggegevens ter herinnering:
Groepsnaam: '.$_SESSION['gname'].'
Naam: '.$info[$value]['naam'].'
Inlogcode: '.$info[$value]['code'].'

Met vriendelijke groeten,
De Lootjestrekmachine', 'From: De Lootjestrekmachine <'.$config['mail'].'>');
                            }
                        $sql3 = "UPDATE groepen SET getrokken = 1 WHERE id = ".$_SESSION['gid']." LIMIT 1";
                        $res3 = mysql_query($sql3) or echo_mysql_error($sql3);
                        if($info[$self]['verlang'] == '')
                            {
                                $info[$self]['verlang'] = 'Deze persoon heeft nog geen verlanglijst opgegeven.';
                            }
                        echo '
                        	<div class="message success">
                        		<p>De trekking is gedaan! Je hebt zelf <strong>'.$info[$self]['naam'].'</strong> getrokken. Hieronder staat zijn/haar verlanglijst, als hij/zij die heeft ingevuld:</p>
								'.nl2br(strip_tags($info[$self]['verlang'])).'
							</div>
							<p><a href="login.php">&laquo; Terug naar het overzicht</a></p>';
                    }
            }
    }
else
    {
        echo '<p class="message warning">Je moet ingelogd zijn (als beheerder) om deze pagina te kunnen bekijken.</p>';
    }
einde_pagina();
?> 