CREATE TABLE IF NOT EXISTS address (
	addressID BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY (addressID),
    streetName VARCHAR(60) NOT NULL,
    houseNumber INT NOT NULL,
    city VARCHAR(60) NOT NULL,
    county VARCHAR(60) NOT NULL,
    postcode VARCHAR(8) NOT NULL
);

CREATE TABLE IF NOT EXISTS client (
    clientID BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY (clientID),
    addressID BIGINT NOT NULL, FOREIGN KEY (addressID) REFERENCES address(addressID),
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL, -- password email and phone number constraints will be validated on input level
    email VARCHAR(50) NOT NULL,
    phoneNumber VARCHAR(11) NOT NULL,
    isBanned BOOL NOT NULL DEFAULT false
);

CREATE TABLE IF NOT EXISTS vendor (
    vendorID BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY (vendorID),
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL, -- password email and phone number constraints will be validated on input level
    email VARCHAR(50) NOT NULL,
    phoneNumber VARCHAR(11) NOT NULL,
    vendorOrginisationRole VARCHAR(25) NOT NULL, 
    vendorOrginisationName VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS service (
    serviceID BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY(serviceID),
    serviceName VARCHAR(50) NOT NULL, 
    serviceDecription VARCHAR(500) NOT NULL,
    servicePrice DECIMAL(10,2) NOT NULL -- Checks will be done on input level
);

CREATE TABLE IF NOT EXISTS vendorServiceAllocation (
    vendorServiceID BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY(vendorServiceID),
    vendorID BIGINT NOT NULL, FOREIGN KEY (vendorID) REFERENCES vendor(vendorID),
    serviceID BIGINT NOT NULL, FOREIGN KEY (serviceID) REFERENCES service(serviceID)
);

CREATE TABLE IF NOT EXISTS clientServiceAllocation (
    clientServiceID BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY(clientServiceID),
    clientID BIGINT NOT NULL, FOREIGN KEY (clientID) REFERENCES client(clientID),
    serviceID BIGINT NOT NULL, FOREIGN KEY (serviceID) REFERENCES service(serviceID)
);