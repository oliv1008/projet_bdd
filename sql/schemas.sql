# ----- TABLE : user

CREATE TABLE IF NOT EXISTS user
(
   idUser INTEGER NOT NULL AUTO_INCREMENT,
   username VARCHAR(255) NULL,
   name VARCHAR(255) NULL,
   mail VARCHAR(255) NULL,
   password VARCHAR(255) NULL,
   avatar VARCHAR(255) NULL,
   dateSign DATETIME NULL,
   PRIMARY KEY (idUser)
);

# ----- TABLE : tweet

CREATE TABLE IF NOT EXISTS tweet
(
   idTweet INTEGER NOT NULL AUTO_INCREMENT,
   idUser INTEGER NOT NULL,
   idTweet_to INTEGER NULL,
   content VARCHAR(1000) NULL,
   dateTweet DATETIME NULL,
   PRIMARY KEY (idTweet)
);

# ----- TABLE : hastag

CREATE TABLE IF NOT EXISTS hashtag
(
   idHashtag INTEGER NOT NULL AUTO_INCREMENT,
   nameHashtag VARCHAR(255) NULL,
   PRIMARY KEY (idHashtag)
);

# ----- TABLE : use

CREATE TABLE IF NOT EXISTS useHashtag
(
   idHashtag INTEGER NOT NULL,
   idTweet INTEGER NOT NULL,
   PRIMARY KEY (idHashtag, idTweet)
);

# ----- TABLE : mention

CREATE TABLE IF NOT EXISTS mention
(
   idUser INTEGER NOT NULL,
   idTweet INTEGER NOT NULL,
   PRIMARY KEY (idUser, idTweet)
);

# ----- TABLE : like

CREATE TABLE IF NOT EXISTS likeTweet
(
   idUser INTEGER NOT NULL,
   idTweet INTEGER NOT NULL,
   PRIMARY KEY (idUser, idTweet)
);

# ----- TABLE : follow

CREATE TABLE IF NOT EXISTS follow
(
   idUser_to INTEGER NOT NULL,
   idUser_from INTEGER NOT NULL,
   PRIMARY KEY (idUser_to, idUser_from)
);
