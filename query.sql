-- 
-- Tabelstructuur voor tabel 'groepen'
-- 

CREATE TABLE groepen (
  id int(10) NOT NULL auto_increment,
  naam varchar(100) collate latin1_general_ci NOT NULL,
  beheer_id int(10) NOT NULL,
  getrokken tinyint(1) NOT NULL default '0',
  tekst longtext collate latin1_general_ci,
  PRIMARY KEY  (id),
  UNIQUE KEY beheer_id (beheer_id),
  UNIQUE KEY naam (naam)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

-- 
-- Tabelstructuur voor tabel 'mensen'
-- 

CREATE TABLE mensen (
  id int(10) NOT NULL auto_increment,
  naam varchar(50) collate latin1_general_ci NOT NULL,
  mail varchar(100) collate latin1_general_ci NOT NULL,
  verlang longtext collate latin1_general_ci NOT NULL,
  groep_id int(10) NOT NULL,
  `code` varchar(10) collate latin1_general_ci NOT NULL,
  getrokken int(10) NOT NULL default '0',
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci; 