CREATE TABLE [base] (
[vid] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
[title] VARCHAR(100)  UNIQUE NOT NULL,
[time] INTEGER  NOT NULL,
[memo] TEXT  NULL,
[mulit] INTEGER  NOT NULL
);

CREATE TABLE [options] (
[oid] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
[vid] INTEGER  NOT NULL,
[value] VARCHAR(255)  NOT NULL,
[rate] INTEGER  NOT NULL
);