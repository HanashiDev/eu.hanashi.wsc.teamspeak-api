CREATE TABLE wcf1_teamspeak (
    teamspeakID INT(10) NOT NULL AUTO_INCREMENT,
    connectionName VARCHAR(20),
    hostname VARCHAR(50) NOT NULL,
    queryType ENUM('raw','ssh') NOT NULL,
    queryPort SMALLINT(5) UNSIGNED NOT NULL,
    virtualServerPort SMALLINT(5) UNSIGNED NOT NULL,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(30) NOT NULL,
    displayName VARCHAR(50),
    creationDate INT(10) NOT NULL,
    PRIMARY KEY (teamspeakID)
);