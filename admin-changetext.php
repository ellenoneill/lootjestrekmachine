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
        if($_SERVER['REQUEST_METHOD'] != "POST")
            {
                 $sql = "SELECT tekst FROM groepen WHERE id = ".$_SESSION['gid']."";
                 $res = mysql_query($sql) or echo_mysql_error($sql);
                 $row = mysql_fetch_assoc($res);
                echo '
					<form method="post" action="'.$_SERVER['PHP_SELF'].'">
						<fieldset>
							<legend>Wijzig de tekst voor jouw groep</legend>
					    	<p><textarea name="text">Welkom op de lootjestrekpagina van de groep '.$_SESSION['gname'].'.'.stripslashes($row['tekst']).'</textarea></p>
					    	<p><input type="submit" value="Opslaan" /></p>
					    </fieldset>
					</form>
				';
            }
        else
            {
                $text = mysql_real_escape_string($_POST['text']);
                $sql = "UPDATE groepen SET tekst = '".$text."' WHERE id = ".$_SESSION['gid']." LIMIT 1";
                 $res = mysql_query($sql) or echo_mysql_error($sql);
                 echo '
                 	<p class="message success">De tekst is opgeslagen.</p>
					<p><a href="login.php">&laquo; Terug naar het overzicht</a></p>';
            }
    }
else
    {
        echo '<p class="message warning">Je moet ingelogd zijn (als beheerder) om deze pagina te kunnen bekijken.</p>';
    }
einde_pagina();
?> 