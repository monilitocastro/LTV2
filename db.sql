CREATE TABLE Account (
  AccountID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  TimestampRecent TIMESTAMP,
  Delinquency INT,
  Balance DECIMAL(16, 2)
);

CREATE TABLE User (
  UserID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  UserType ENUM('Patient', 'Nurse', 'Nurse Practitioner', 'Physician', 'Admin', 'EMT', 'Specialist', 'Technician'),
  Name VARCHAR(255),
  Username VARCHAR(255),
  Password VARCHAR(255),
  Address VARCHAR(255),
  AccountID INT,
  UNIQUE (Username),
  FOREIGN KEY (AccountID) REFERENCES Account(AccountID)
);

CREATE TABLE MedicalRecord (
  MR_ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  EmplID INT,
  PatientID INT,
  SymptID INT,
  Trtmt_ID INT,
  Folder_path VARCHAR(255),
  Timestamp TIMESTAMP,
  DescriptionID INT,
  RxNumber INT,
  FOREIGN KEY (RxNumber) REFERENCES Prescription(RxNumber),
  FOREIGN KEY (DescriptionID) REFERENCES Description(DescriptionID),
  FOREIGN KEY (Trtmt_ID) REFERENCES Treatment(TreatmentID),
  FOREIGN KEY (SymptID) REFERENCES Symptom(SymptomID),
  FOREIGN KEY (PatientID) REFERENCES User(UserID),
  FOREIGN KEY (EmplID) REFERENCES User(UserID)
); 

CREATE TABLE Transactions (
  TransactionID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  PatientID INT,
  Timestamp TIMESTAMP,
  TransactionType BOOLEAN,
  Amount DECIMAL(16, 2),
  FOREIGN KEY (PatientID) REFERENCES User(UserID)
);

CREATE TABLE Appointment (
  AppointmentID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  PhysicianID INT,
  PatientID INT,
  Time DATETIME,
  DescriptionID INT,
  FOREIGN KEY (DescriptionID) REFERENCES Description(DescriptionID),
  FOREIGN KEY (PhysicianID) REFERENCES User(UserID),
  FOREIGN KEY (PatientID) REFERENCES User(UserID)
);

CREATE TABLE Symptom (
  SymptomID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  Name VARCHAR(255)
);

CREATE TABLE Treatment (
  TreatmentID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  Name VARCHAR(255),
  Cost DECIMAL(16, 2)
);

CREATE TABLE Prescription (
  RxNumber INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  Name VARCHAR(255),
  Quantity INT,
  Refills INT
);

CREATE TABLE Description (
  DescriptionID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  FreeFormText TEXT
);

DELIMITER //

CREATE PROCEDURE sign_up_patient(IN name VARCHAR(255), IN username VARCHAR(255), IN password VARCHAR(255), IN address VARCHAR(255))
BEGIN
  INSERT INTO Account (Delinquency, Balance) VALUES (0, 0);
  INSERT INTO User (UserType, Name, Username, Password, Address, AccountID) VALUES ('Patient', name, username, password, address, LAST_INSERT_ID());
END//

CREATE PROCEDURE sign_up_caregiver(IN usertype VARCHAR(50), IN name VARCHAR(255), IN username VARCHAR(255), IN password VARCHAR(255), IN address VARCHAR(255))
BEGIN
  INSERT INTO Account (Delinquency, Balance) VALUES (0, 0);
  INSERT INTO User (UserType, Name, Username, Password, Address, AccountID) VALUES (usertype, name, username, password, address, LAST_INSERT_ID());
END//

CREATE PROCEDURE view_appointments(IN id INT)
BEGIN
  SELECT * FROM Appointment WHERE PatientID = id ORDER BY Time;
END//

CREATE PROCEDURE schedule_appointment(IN patient_id INT, IN physician_id INT, IN time DATETIME, IN description TEXT)
BEGIN
  IF description IS NOT NULL THEN
    INSERT INTO Description (FreeFormText) VALUES (description);
    INSERT INTO Appointment (PhysicianID, PatientID, Time, DescriptionID) VALUES (physician_id, patient_id, time, LAST_INSERT_ID());
  ELSE
    INSERT INTO Appointment (PhysicianID, PatientID, Time) VALUES (physician_id, patient_id, time);
  END IF;
END//

CREATE PROCEDURE cancel_appointment(IN appointment_id INT)
BEGIN
  SELECT DescriptionID INTO @desc_id FROM Appointment WHERE AppointmentID = appointment_id;
  DELETE FROM Description WHERE DescriptionID = @desc_id;
  DELETE FROM Appointment WHERE AppointmentID = appointment_id;
END//

CREATE PROCEDURE write_exam_note(IN note TEXT, IN empl_id INT, IN patient_id INT)
BEGIN
  INSERT INTO Description (FreeFormText) VALUES (note);
  INSERT INTO MedicalRecord (EmplID, PatientID, DescriptionID) VALUES (empl_id, patient_id, LAST_INSERT_ID());
END//

CREATE PROCEDURE create_disease_thread(IN patient_id INT, IN empl_id INT, IN symptom_id INT, IN treatment_id INT, IN description TEXT)
BEGIN
  IF description IS NOT NULL THEN
    INSERT INTO Description (FreeFormText) VALUES (description);
    INSERT INTO MedicalRecord (EmplID, PatientID, SymptID, Trtmnt_ID, DescriptionID) VALUES (empl_id, patient_id, symptom_id, treatment_id, LAST_INSERT_ID());
  ELSE
    INSERT INTO MedicalRecord (EmplID, PatientID, SymptID, Trtmnt_ID) VALUES (empl_id, patient_id, symptom_id, treatment_id);
  END IF;
END//

CREATE PROCEDURE view_medical_history(IN patient_id INT)
BEGIN
  SELECT * FROM MedicalRecord WHERE PatientID = patient_id ORDER BY Timestamp;
END//

CREATE PROCEDURE view_prescription_history(IN patient_id INT)
BEGIN
  SELECT * FROM MedicalRecord WHERE PatientID = patient_id AND RxNumber IS NOT NULL ORDER BY Timestamp;
END//

CREATE PROCEDURE view_account_balance(IN patient_id INT)
BEGIN
  SELECT Balance FROM Account NATURAL JOIN User WHERE UserID = patient_id;
END//

CREATE PROCEDURE make_payment(IN amount DECIMAL(16, 2), IN patient_id INT)
BEGIN
  SELECT AccountID INTO @account_id FROM User WHERE UserID = patient_id;
  INSERT INTO Transactions (PatientID, TransactionType, Amount) VALUES (patient_id, 0, amount);
  UPDATE Account SET Balance = Balance - amount WHERE AccountID = @account_id;
END//

CREATE PROCEDURE schedule_lab_test(IN patient_id INT, IN physician_id INT, IN time DATETIME, IN description TEXT)
BEGIN
  IF description IS NOT NULL THEN
    INSERT INTO Description (FreeFormText) VALUES (description);
    INSERT INTO Appointment (PhysicianID, PatientID, Time, DescriptionID) VALUES (physician_id, patient_id, time, LAST_INSERT_ID());
  ELSE
    INSERT INTO Appointment (PhysicianID, PatientID, Time) VALUES (physician_id, patient_id, time);
  END IF;
END//

CREATE PROCEDURE change_password(IN user_id INT, IN password VARCHAR(255))
BEGIN
  UPDATE User SET Password = password WHERE UserID = user_id;
END//

DELIMITER ;
