-- mysql
CREATE TABLE urls (
	uid INTEGER NOT NULL AUTO_INCREMENT,
  longurl text(256) NOT NULL,
	PRIMARY KEY (uid),
	UNIQUE KEY u_long (longurl(256))
) ENGINE=MyISAM, CHARSET=utf8;


-- postgres
CREATE TABLE urls (
  uid SERIAL NOT NULL,
  longurl character varying (256) NOT NULL,
  PRIMARY KEY (uid),
  UNIQUE (longurl)
);