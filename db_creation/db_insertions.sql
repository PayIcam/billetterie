INSERT INTO `ticketing`.`promos`(promo_name, still_student, has_payicam) VALUES
(115,0, 1), (116,0, 1), (117,0, 1), (2013,0, 1), (2014,0, 1), (2015,0, 1), (2016,0, 1), (2017,0, 1), (118,1, 1), (119,1, 1), (120,1, 1), (121,1, 1), (122,1, 1), (2018,1, 1), (2019,1, 1), (2020,1, 1), (2021,1, 1), (2022,1, 1), ('Permanents', 2, 1), ('Invités', 2, 1), ('Ingénieurs', 0, 0), ('Artistes', 2, 0), ('Autre', 2, 0);

INSERT INTO `ticketing`.`sites`(site_name) VALUES ('Lille'), ('Toulouse');

INSERT INTO `ticketing`.`config` VALUES ('ticketing', 1), ('event_administration', 1), ('inscriptions', 1), ('participant_administration', 1);