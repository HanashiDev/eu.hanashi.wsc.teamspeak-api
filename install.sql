CREATE TABLE wcf1_teamspeak (
    teamspeakID INT(10) NOT NULL AUTO_INCREMENT,
    connectionName VARCHAR(20),
    hostname VARCHAR(50) NOT NULL,
    queryType VARCHAR(3) NOT NULL,
    queryPort INT(10) NOT NULL,
    virtualServerPort INT(10) NOT NULL,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(30) NOT NULL,
    displayName VARCHAR(50),
    creationDate INT(10) NOT NULL,
    PRIMARY KEY (teamspeakID)
);