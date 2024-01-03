CREATE TABLE tx_ffpifirmwarelist_domain_model_firmwareversiondetail
(
	uid                 int(11)                         NOT NULL auto_increment,
	pid                 int(11)             DEFAULT '0' NOT NULL,

	version             varchar(255)        DEFAULT ''  NOT NULL,
	gluon_release       varchar(255)        DEFAULT ''  NOT NULL,
	openwrt_release     varchar(255)        DEFAULT ''  NOT NULL,
	has_security_issues tinyint(1)          DEFAULT '0' NOT NULL,
	additional_notes    text                DEFAULT ''  NOT NULL,
	git                 varchar(255)        DEFAULT ''  NOT NULL,

	tstamp              int(11) unsigned    DEFAULT '0' NOT NULL,
	crdate              int(11) unsigned    DEFAULT '0' NOT NULL,
	cruser_id           int(11) unsigned    DEFAULT '0' NOT NULL,
	deleted             tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden              tinyint(4) unsigned DEFAULT '0' NOT NULL,
	sorting             int(11) unsigned    DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY version (version)
);
