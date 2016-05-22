--
-- Databank: `autodiscover`
--
CREATE DATABASE IF NOT EXISTS `autodiscover` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `autodiscover`;
-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `what` varchar(25) NOT NULL,
  `value` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
INSERT INTO `config` (`id`, `what`, `value`) VALUES
(1, 'community', 'public');
-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `FoundHosts`
--

CREATE TABLE IF NOT EXISTS `FoundHosts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(25) NOT NULL,
  `hostname` varchar(50) NOT NULL,
  `ignored` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP ON autodiscover.* TO autodiscover@localhost IDENTIFIED by 'autodiscover';
GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP ON db_nagiosql_v2.* TO autodiscover@localhost IDENTIFIED by 'autodiscover';
UPDATE mysql.user SET Password = OLD_PASSWORD('autodiscover') WHERE Host = 'localhost' AND User = 'autodiscover';
FLUSH PRIVILEGES;

