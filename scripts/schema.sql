CONNECT eviation;

CREATE TABLE guestbook (
    id INT NOT NULL PRIMARY KEY AUTOINCREMENT,
    email VARCHAR(32) NOT NULL,
    comment TEXT NULL,
    created DATETIME NOT NULL
);
 
CREATE INDEX "id" ON "guestbook" ("id");

