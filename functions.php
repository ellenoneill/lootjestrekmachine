<?php session_start();
ob_start();
$sql_username = "";
$sql_password = "";
$sql_host = "";
$sql_dbname = "";
$config['mail'] = ""; //Jouw e-mailadres
$config['website'] = ""; //De url naar je website
mysql_connect($sql_host, $sql_username, $sql_password);
mysql_select_db($sql_dbname);
function keygen($i)
    {
        $alfabet = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'), range('0', '9'));
        $actkey = "";
        while(strlen($actkey) < $i)
            {
                $actkey .= $alfabet[array_rand($alfabet)];
            }
        return($actkey);
    }
function mysql_is_unique($value, $table, $field)
    {
        $sql = "SELECT ".$field." FROM ".$table." WHERE ".$field." LIKE '".$value."' LIMIT 1";
        $res = mysql_query($sql) or echo_mysql_error($sql);
        if(mysql_num_rows($res) > 0)
            {
                return false;
            }
        else
            {
                return true;
            }
    }
function is_unieke_naam_in_groep($naam, $gid)
    {
        $sql = "SELECT id FROM mensen WHERE naam LIKE '".$naam."' AND groep_id = ".$gid." LIMIT 1";
        $res = mysql_query($sql) or echo_mysql_error($sql);
        if(mysql_num_rows($res) > 0)
            {
                return false;
            }
        else
            {
                return true;
            }
    }
function echo_mysql_error($sql)
    {
        echo '
        <h2>MySQL error</h2>: '.mysql_error().'
		<h3>Query:</h3>
		<pre>'.$sql.'</pre>';
        exit();
    }
function form_login($username = null, $gid = null, $code = null)
    {
        $sql = "SELECT id, naam FROM groepen ORDER BY naam";
        $res = mysql_query($sql) or echo_mysql_error($sql);
        echo '
			<form method="post" action="login.php">
				<fieldset>
					<legend>Inloggen</legend>
					<p><label for="groepsnaam">Groepsnaam</label><br />
					<select name="groepsnaam">
	                    <option value="">Maak je keuze...</option>';
	        while($row = mysql_fetch_assoc($res))
	            {
	                 if($gid == $row['id'])
	                     {
	                        echo '
	                    <option value="'.$row['id'].'" selected="selected">'.$row['naam'].'</option>';
	                    }
	                else
	                    {
	                        echo '
	                    <option value="'.$row['id'].'">'.$row['naam'].'</option>';
	                    }
	            }
	        echo '
	                </select><br />
	                <a href="register.php">Maak een nieuwe groep aan</a></p>
					<p><label for="gebruikersnaam">Naam</label><br />
					<input type="text" name="gebruikersnaam" value="'.$username.'" /></p>
					<p><label for="inlogcode">Inlogcode</label><br />
					<input type="password" name="inlogcode" value="'.$code.'" /></p>
					<p><input type="submit" value="Log in" /></p>
				</fieldset>
			</form>';
    }
function begin_pagina()
    {
        echo '
			<!DOCTYPE html>
			<meta charset="utf-8">
			<title>Lootjes trekken</title>
			<link rel="stylesheet" type="text/css" href="/static/css/universal.css" media="all">
			<link rel="stylesheet" type="text/css" href="/static/css/default.css" media="all">
			<link rel="stylesheet" type="text/css" href="/static/css/custom.css" media="all">
			<h1 class="page-heading"><a href="login.php">De Lootjestrekmachine</a></h1>
		';
    }
function einde_pagina()
    {
         echo '
			<p class="legal">&copy; 2006-'.date("Y").', powered by de <a href="http://github.com/eliun/lootjestrekmachine">Lootjestrekmachine</a>, <a href="http://phphulp.nl/php/scripts/3/806/">scripting</a> door Herjan Treurniet, revisie door <a href="http://ellenoneill.nl">Ellen O\'Neill</a>, <a href="http://code.google.com/p/universal-ie6-css/">Universal CSS</a> van Andy Clarke. Aan deze website kunnen geen rechten ontleend worden. De makers van deze website zijn niet aansprakelijk voor de gevolgen van het gebruik van deze website. De diensten aangeboden op deze site zijn volkomen gratis, verwacht dan ook niet al te veel service.</p>
		';
        ob_end_flush();
    }
function _make_url_clickable_cb($matches) {
	$ret = '';
	$url = $matches[2];
 
	if ( empty($url) )
		return $matches[0];
	// removed trailing [.,;:] from URL
	if ( in_array(substr($url, -1), array('.', ',', ';', ':')) === true ) {
		$ret = substr($url, -1);
		$url = substr($url, 0, strlen($url)-1);
	}
	return $matches[1] . "<a href=\"$url\" rel=\"nofollow\">$url</a>" . $ret;
}
 
function _make_web_ftp_clickable_cb($matches) {
	$ret = '';
	$dest = $matches[2];
	$dest = 'http://' . $dest;
 
	if ( empty($dest) )
		return $matches[0];
	// removed trailing [,;:] from URL
	if ( in_array(substr($dest, -1), array('.', ',', ';', ':')) === true ) {
		$ret = substr($dest, -1);
		$dest = substr($dest, 0, strlen($dest)-1);
	}
	return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\">$dest</a>" . $ret;
}
 
function _make_email_clickable_cb($matches) {
	$email = $matches[2] . '@' . $matches[3];
	return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
}
 
function make_clickable($ret) {
	$ret = ' ' . $ret;
	// in testing, using arrays here was found to be faster
	$ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $ret);
	$ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_web_ftp_clickable_cb', $ret);
	$ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);
 
	// this one is not in an array because we need it to run last, for cleanup of accidental links within links
	$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
	$ret = trim($ret);
	return $ret;
}
?> 