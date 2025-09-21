CREATE DATABASE incidentPortal;

USE incidentPortal;

CREATE TABLE users (
    userID INT NOT NULL AUTO_INCREMENT,
    firstName VARCHAR(255),
    lastName VARCHAR(255),
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    role ENUM(
        'admin', 
        'agent', 
        'viewer'
    ) NOT NULL DEFAULT 'agent',
    PRIMARY KEY (userID)
);

CREATE TABLE clients (
    clientID INT NOT NULL AUTO_INCREMENT,
    firstName VARCHAR(255) NOT NULL,
    lastName VARCHAR(255) NOT NULL,
    location VARCHAR(255),
    email VARCHAR(255) NOT NULL UNIQUE,
    status TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (clientID)
);

CREATE TABLE incidents (
    incidentID INT NOT NULL AUTO_INCREMENT,
    clientID INT NOT NULL,
    userID INT NOT NULL,
    impact ENUM(
        'low', 
        'medium', 
        'high'
    ) NOT NULL DEFAULT 'low',
    urgency ENUM(
        'low',
        'medium', 
        'high'
    ) NOT NULL DEFAULT 'low',
    severity ENUM(
        'low',
        'medium',
        'high',
        'critical'
    ) NOT NULL DEFAULT 'low',
    title VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL,
    category ENUM(
        'network',
        'hardware',
        'software',
        'security',
        'database',
        'service',
        'access',
        'other'
    ) NOT NULL DEFAULT 'other',
    datecreated DATETIME NOT NULL,
    dateresolved DATETIME,
    status ENUM(
        'new',
        'inprogress',
        'resolved'
    ) NOT NULL DEFAULT 'new',
    PRIMARY KEY (incidentID),
    FOREIGN KEY (clientID) REFERENCES clients (clientID),
    FOREIGN KEY (userID) REFERENCES users (userID)
)

CREATE TABLE notes (
    noteID INT NOT NULL AUTO_INCREMENT,
    incidentID INT NOT NULL,
    userID INT NOT NULL,
    content VARCHAR(255) NOT NULL,
    dateadded DATETIME NOT NULL,
    PRIMARY KEY (noteID),
    FOREIGN KEY (incidentID) REFERENCES incidents(incidentID),
    FOREIGN KEY (userID) REFERENCES users (userID)
)