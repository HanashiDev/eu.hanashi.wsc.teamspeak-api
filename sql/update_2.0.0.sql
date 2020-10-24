ALTER TABLE wcf1_teamspeak CHANGE queryType queryType ENUM('raw','ssh', 'http','https') NOT NULL;
ALTER TABLE wcf1_teamspeak CHANGE password password VARCHAR(50) NOT NULL;
