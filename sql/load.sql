USE `MedDB`;

SET FOREIGN_KEY_CHECKS = 0;

LOAD DATA LOCAL INFILE 'C:/Users/arpan/DatabaseProjectPhaseThree/data/patients.csv'
INTO TABLE Patient
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE 'C:/Users/arpan/DatabaseProjectPhaseThree/data/employees.csv'
INTO TABLE Employee
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE 'C:/Users/arpan/DatabaseProjectPhaseThree/data/doctors.csv'
INTO TABLE Doctor
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE 'C:/Users/arpan/DatabaseProjectPhaseThree/data/head_doctors.csv'
INTO TABLE HeadDoctor
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE 'C:/Users/arpan/DatabaseProjectPhaseThree/data/nurses.csv'
INTO TABLE Nurse
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE 'C:/Users/arpan/DatabaseProjectPhaseThree/data/hr.csv'
INTO TABLE HR
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE 'C:/Users/arpan/DatabaseProjectPhaseThree/data/dept_types.csv'
INTO TABLE DeptType
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE 'C:/Users/arpan/DatabaseProjectPhaseThree/data/departments.csv'
INTO TABLE Department
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE 'C:/Users/arpan/DatabaseProjectPhaseThree/data/records.csv'
INTO TABLE Record
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE 'C:/Users/arpan/DatabaseProjectPhaseThree/data/appointments.csv'
INTO TABLE Appointment
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 LINES;

SET FOREIGN_KEY_CHECKS = 1;
