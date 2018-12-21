INSERT INTO `user` (`idUser`, `username`, `name`, `password`, `mail`, `avatar`, `dateSign`) VALUES
(1, 'Bedox', 'Tom', 'mdp', 'tomtom@gmail.com', '', '2015-02-24 11:23:12'),
(2, 'Crevette', 'Olivier', 'password', 'olivier@olivier.com', '', '2018-06-05 04:11:23');

INSERT INTO `tweet` (`idTweet`, `idUser`, `idTweet_to`, `content`, `dateTweet`) VALUES
(1, 1, 1, 'hqhfhqf @Bedox qjflsqdfj #cool', '2018-11-14 01:26:05'),
(2, 2, NULL, 'dashkibikou #GILETSJAUNES', '2017-01-22 01:00:08'),
(3, 2, 1, 'ladllalllalalal @Bedox', '2020-10-11 01:07:09');

INSERT INTO `hashtag` (`idHashtag`, `nameHashtag`) VALUES
(1, 'cool'),
(2, 'GILETSJAUNES');

INSERT INTO `likeTweet` (`idUser`, `idTweet`) VALUES
(1, 3);

INSERT INTO `follow` (`idUser_to`, `idUser_from`) VALUES
(1, 3),
(2, 1);

INSERT INTO `mention` (`idUser`, `idTweet`) VALUES
(1, 1),
(1, 3);

INSERT INTO `useHashtag` (`idHashtag`, `idTweet`) VALUES
(1, 1),
(2, 2);
