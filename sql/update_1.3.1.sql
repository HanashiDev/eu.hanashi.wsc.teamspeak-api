ALTER TABLE wcf1_teamspeak CHANGE queryType queryType enum('raw','ssh') NOT NULL;
ALTER TABLE wcf1_teamspeak CHANGE queryPort queryPort SMALLINT(5) NOT NULL AFTER queryType;
ALTER TABLE wcf1_teamspeak CHANGE virtualServerPort virtualServerPort SMALLINT(5) NOT NULL AFTER queryPort;