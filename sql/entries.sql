INSERT INTO 'user' ('idUser', 'username', 'name', 'password', 'mail', 'avatar', 'dateSign') VALUES
(1, 'SuchelTom', 'Tom', 'mdp', 'tomtom@gmail.com', '', '2015-02-24 11:23:12'),
(2, 'MillochauOlivier', 'Olivier', 'password', 'olivier@olivier.com', '', '2018-06-05 04:11:23');
(3, 'SCSamuel', 'Samuel', 'pwd', 'sam@hotmail.com', '', '2017-01-12 17:25:49');

INSERT INTO 'tweet' ('idTweet', 'idUser', 'idTweet_to', 'content', 'dateTweet') VALUES
(1, 1, 2, 'Coucou  @Olivier!', '2017-11-14 01:26:05'),
(2, 2, 1, 'Ca va @Tom?', '2017-11-14 01:27:08'),
(3, 3, NULL, 'Test de hashtag #test', '2020-10-11 01:07:09');

INSERT INTO 'hashtag' ('idHashtag', 'nameHashtag') VALUES
(1, 'test');

INSERT INTO 'likeTweet' ('idUser', 'idTweet') VALUES
(1, 2);
(2, 1);

INSERT INTO 'follow' ('idUser_to', 'idUser_from') VALUES
(1, 2),
(2, 1);

INSERT INTO 'mention' ('idUser', 'idTweet') VALUES
(2, 1),
(1, 2);

INSERT INTO 'useHashtag' ('idHashtag', 'idTweet') VALUES
(1, 3);
