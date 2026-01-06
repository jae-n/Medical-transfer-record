CREATE DATABASE IF NOT EXISTS `MedDB`;
USE `MedDB`;


CREATE TABLE Patient (
    pid INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    gender VARCHAR(10),
    email VARCHAR(100),
    phone VARCHAR(20)
);


CREATE TABLE Employee (
    empID INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    startDate DATE NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    shift ENUM('day','night'),
    role ENUM('Immediate Responder','Administrative Staff','Scheduled Staff') NOT NULL
);


CREATE TABLE Doctor (
    docID INT PRIMARY KEY,
    empID INT NOT NULL,
    specialization VARCHAR(100),
    FOREIGN KEY (empID) REFERENCES Employee(empID) ON DELETE RESTRICT ON UPDATE CASCADE
);


CREATE TABLE HeadDoctor (
    docID INT,
    depID INT,
    empID INT,
    PRIMARY KEY (docID, depID),
    FOREIGN KEY (docID) REFERENCES Doctor(docID) ON DELETE RESTRICT ON UPDATE CASCADE
);


CREATE TABLE Nurse (
    nurseID INT PRIMARY KEY,
    empID INT NOT NULL,
    specialization VARCHAR(100),
    FOREIGN KEY (empID) REFERENCES Employee(empID) ON DELETE RESTRICT ON UPDATE CASCADE
);


CREATE TABLE HR (
    empID INT PRIMARY KEY,
    email VARCHAR(100),
    phone VARCHAR(20),
    accessLevel ENUM('Standard','Manager','Admin'),
    FOREIGN KEY (empID) REFERENCES Employee(empID) ON DELETE RESTRICT ON UPDATE CASCADE
);


CREATE TABLE DeptType (
    deptTypeID INT PRIMARY KEY,
    deptType VARCHAR(50),
    operatingHours VARCHAR(50)
);


CREATE TABLE Department (
    depID INT PRIMARY KEY,
    depName VARCHAR(100),
    deptTypeID INT,
    location VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    FOREIGN KEY (deptTypeID) REFERENCES DeptType(deptTypeID) ON DELETE SET NULL ON UPDATE CASCADE
);


CREATE TABLE Record (
    rid INT PRIMARY KEY,
    pid INT,
    docID INT,
    diagnosis VARCHAR(255),
    treatment VARCHAR(255),
    hospitalName VARCHAR(100),
    FOREIGN KEY (pid) REFERENCES Patient(pid) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (docID) REFERENCES Doctor(docID) ON DELETE RESTRICT ON UPDATE CASCADE
);


CREATE TABLE Appointment (
    appID INT PRIMARY KEY,
    appDate DATE,
    pid INT,
    docID INT,
    status ENUM('Requested','Approved','Cancelled'),
    FOREIGN KEY (pid) REFERENCES Patient(pid) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (docID) REFERENCES Doctor(docID) ON DELETE SET NULL ON UPDATE CASCADE
);
