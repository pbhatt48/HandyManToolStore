USE handymantool;

CREATE	TABLE	`Clerk`	(
		`ClerkLogin`	VARCHAR(50)	NOT	NULL	,
		`Password`	VARCHAR(50)	NOT	NULL	,
		`FirstName`	VARCHAR(50)	NULL	,
		`LastName`	VARCHAR(50)	NULL	,
		PRIMARY	KEY	(`ClerkLogin`));
		
CREATE	TABLE	`Customer`	(
		`CustomerLogin`	VARCHAR(50)	NOT	NULL	,
		`Password`	VARCHAR(50)	NOT	NULL	,
		`FirstName`	VARCHAR(50)	NULL	,
		`LastName`	VARCHAR(50)	NULL	,
		`Address`	VARCHAR(256)	NULL	,
		`WorkPhoneCountryCode`	VARCHAR(10)	NULL,
		`WorkPhoneLocalNumber`	VARCHAR(20)	NULL,
		`HomePhoneCountryCode`	VARCHAR(10)	NULL,
		`HomePhoneLocalNumber`	VARCHAR(20)	NULL,
		PRIMARY	KEY	(`CustomerLogin`));

CREATE	TABLE	`Reservation`	(
		`ReservationId`	INT	NOT	NULL	AUTO_INCREMENT,
		`CustomerLogin`	VARCHAR(50)	NOT	NULL,
		`StartDate`	DATETIME	NOT	NULL	,
		`EndDate`	DATETIME	NOT	NULL	,
		`CreditCard`	VARCHAR(16)	NULL	,
		`CreditCardExpirationDate`	DATETIME	NULL	,
		`PickUpClerk`	VARCHAR(50)	NULL,
		`DropOffClerk`	VARCHAR(50)	NULL,
		PRIMARY	KEY	(`ReservationId`),
		FOREIGN	KEY	(`CustomerLogin`)	REFERENCES	`Customer`(`CustomerLogin`),
		FOREIGN	KEY	(`PickUpClerk`)	REFERENCES	`Clerk`(`ClerkLogin`),
		FOREIGN	KEY	(`DropOffClerk`)	REFERENCES	`Clerk`(`ClerkLogin`));

CREATE	TABLE	`ToolType`	(
		`ToolTypeId`	INT	NOT	NULL	AUTO_INCREMENT,
		`Name`	VARCHAR(128)	NOT	NULL,
		PRIMARY	KEY	(`ToolTypeId`));

CREATE	TABLE	`Tools`	(
		`ToolId`	INT	NOT	NULL	AUTO_INCREMENT,
		`ToolTypeId`	INT	NOT	NULL,
		`AbbDescription`	VARCHAR(512)	NULL,
		`FullDescription`	VARCHAR(1024)	NULL,
		`PurchasePrice`	DECIMAL(10,2)	NOT	NULL,
		`RentalPricePerDay`	DECIMAL(10,2)	NOT	NULL,
		`DepositAmount`	DECIMAL(10,2)	NOT	NULL,
		`AvailableForSale`	INT	NULL,
		`ToolSold`	INT	NULL,
			PRIMARY	KEY	(`ToolId`),
			FOREIGN	KEY	(`ToolTypeId`)	REFERENCES	`ToolType`(`ToolTypeId`));
			
CREATE	TABLE	`Reserved`	(
		`ReservationId`	INT	NOT	NULL,
		`ToolId`	INT	NOT	NULL	,
		UNIQUE	(`ToolId`,`ReservationId`),
		FOREIGN	KEY	(`ReservationId`)	REFERENCES	`Reservation`(`ReservationId`),
		FOREIGN	KEY	(`ToolId`)	REFERENCES	`Tools`(`ToolId`));

CREATE	TABLE	`PowerTool_Accesories`	(
		`ToolId`	INT	NOT	NULL	,
		`Accesories`	VARCHAR(200)	NOT	NULL,
			UNIQUE	(`ToolId`,`Accesories`),
			FOREIGN	KEY	(`ToolId`)	REFERENCES	`Tools`(`ToolId`));

CREATE	TABLE	`ServiceTool`	(
		`ToolId`	INT	NOT	NULL	,
		`ClerkLogin`	VARCHAR(50)	NOT	NULL,
		`StartDate`	DATETIME	NOT	NULL	,
		`EndDate`	DATETIME	NOT	NULL	,
		`EstRepairCost`	DECIMAL(10,2)	NOT	NULL,
		FOREIGN	KEY	(`ToolId`)	REFERENCES	`Tools`(`ToolId`),
		FOREIGN	KEY	(`ClerkLogin`)	REFERENCES	`Clerk`(`ClerkLogin`));