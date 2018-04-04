USE omts;

CREATE TABLE app_user(
    user_id INT NOT NULL AUTO_INCREMENT,
    email VARCHAR(256) NOT NULL UNIQUE,
    phone_number VARCHAR(15) NOT NULL,
    first_name VARCHAR(25) NOT NULL,
    middle_name VARCHAR(25),
    last_name VARCHAR(25) NOT NULL,
    street_number INT NOT NULL,
    street_name VARCHAR(50) NOT NULL,
    city VARCHAR(20) NOT NULL,
    province CHAR(2) NOT NULL,
    postalcode CHAR(6) NOT NULL,
    password VARCHAR(25) NOT NULL,
    credit_card_number VARCHAR(16),
    credit_card_expiry DATE,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    PRIMARY KEY(user_id)
);

CREATE TABLE theatre(
    theatre_id INT NOT NULL AUTO_INCREMENT,
    theatre_name VARCHAR(50) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    street_number INT NOT NULL,
    street_name VARCHAR(50) NOT NULL,
    city VARCHAR(20) NOT NULL,
    province CHAR(2) NOT NULL,
    postalcode CHAR(6) NOT NULL,
    PRIMARY KEY(theatre_id)
);

CREATE TABLE auditorium(
    theatre_id INT NOT NULL,
    auditorium_number INT NOT NULL,
    max_seats INT NOT NULL,
    screen_size ENUM('Small', 'Medium', 'Large') NOT NULL,
    FOREIGN KEY(theatre_id) REFERENCES theatre(theatre_id) ON DELETE CASCADE,
    PRIMARY KEY(theatre_id, auditorium_number)
);

CREATE TABLE supplier_contact(
    supplier_contact_id INT NOT NULL AUTO_INCREMENT,
    phone_number VARCHAR(50) NOT NULL,
    first_name VARCHAR(25) NOT NULL,
    middle_name VARCHAR(25),
    last_name VARCHAR(25) NOT NULL,
    PRIMARY KEY(supplier_contact_id)
);

CREATE TABLE movie_supplier(
    movie_supplier_id INT NOT NULL AUTO_INCREMENT,
    company_name VARCHAR(50) NOT NULL,
    street_number INT NOT NULL,
    street_name VARCHAR(50) NOT NULL,
    city VARCHAR(20) NOT NULL,
    province CHAR(2) NOT NULL,
    postalcode CHAR(6) NOT NULL,
    supplier_contact_id INT NOT NULL,
    FOREIGN KEY(supplier_contact_id) REFERENCES supplier_contact(supplier_contact_id) ON DELETE CASCADE,
    PRIMARY KEY(movie_supplier_id)
);

CREATE TABLE movie(
    movie_id INT NOT NULL AUTO_INCREMENT,
    poster_url VARCHAR(1024) NOT NULL,
    title VARCHAR(50) NOT NULL,
    duration_minutes INT NOT NULL,
    age_rating ENUM('G', 'PG', 'PG-13', 'R', 'NC-17') NOT NULL,
    plot VARCHAR(1024) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    movie_supplier_id INT NOT NULL,
    FOREIGN KEY(movie_supplier_id) REFERENCES movie_supplier(movie_supplier_id) ON DELETE CASCADE,
    PRIMARY KEY(movie_id)
);

CREATE TABLE actor(
    actor_id INT NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(25) NOT NULL,
    middle_name VARCHAR(25),
    last_name VARCHAR(25) NOT NULL,
    PRIMARY KEY(actor_id)
);

CREATE TABLE director(
    director_id INT NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(25) NOT NULL,
    middle_name VARCHAR(25),
    last_name VARCHAR(25) NOT NULL,
    PRIMARY KEY(director_id)
);

CREATE TABLE production_company(
    production_company_id INT NOT NULL AUTO_INCREMENT,
    production_company_name VARCHAR(25) NOT NULL,
    PRIMARY KEY(production_company_id)
);

CREATE TABLE plays(
    actor_id INT NOT NULL,
    movie_id INT NOT NULL,
    FOREIGN KEY(actor_id) REFERENCES actor(actor_id) ON DELETE CASCADE,
    FOREIGN KEY(movie_id) REFERENCES movie(movie_id) ON DELETE CASCADE,
    PRIMARY KEY(actor_id, movie_id)
);

CREATE TABLE directs(
    director_id INT NOT NULL,
    movie_id INT NOT NULL,
    FOREIGN KEY(director_id) REFERENCES director(director_id) ON DELETE CASCADE,
    FOREIGN KEY(movie_id) REFERENCES movie(movie_id) ON DELETE CASCADE,
    PRIMARY KEY(director_id, movie_id)
);

CREATE TABLE makes(
    production_company_id INT NOT NULL,
    movie_id INT NOT NULL,
    FOREIGN KEY(production_company_id) REFERENCES production_company(production_company_id) ON DELETE CASCADE,
    FOREIGN KEY(movie_id) REFERENCES movie(movie_id) ON DELETE CASCADE,
    PRIMARY KEY(production_company_id, movie_id)
);

CREATE TABLE screening(
    screening_id INT NOT NULL AUTO_INCREMENT,
    theatre_id INT NOT NULL,
    auditorium_number INT NOT NULL,
    movie_id INT NOT NULL,
    date_time DATETIME NOT NULL,
    FOREIGN KEY(theatre_id, auditorium_number) REFERENCES auditorium(theatre_id, auditorium_number) ON DELETE CASCADE,
    FOREIGN KEY(movie_id) REFERENCES movie(movie_id) ON DELETE CASCADE,
    UNIQUE(
        theatre_id,
        auditorium_number,
        date_time
    ),
    PRIMARY KEY(screening_id)
);

CREATE TABLE ticket(
    ticket_id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    theatre_id INT NOT NULL,
    auditorium_number INT NOT NULL,
    date_time DATETIME NOT NULL,
    number_of_tickets INT NOT NULL,
    purchase_date_time DATETIME NOT NULL,
    FOREIGN KEY(user_id) REFERENCES app_user(user_id) ON DELETE CASCADE,
    FOREIGN KEY(theatre_id, auditorium_number, date_time) REFERENCES screening(theatre_id, auditorium_number, date_time) ON DELETE CASCADE,
    PRIMARY KEY(
        ticket_id,
        user_id,
        theatre_id,
        auditorium_number,
        date_time
    )
);

CREATE TABLE movie_review(
    movie_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL,
    comments VARCHAR(512),
    FOREIGN KEY(movie_id) REFERENCES movie(movie_id) ON DELETE CASCADE,
    FOREIGN KEY(user_id) REFERENCES app_user(user_id) ON DELETE CASCADE,
    PRIMARY KEY(movie_id, user_id)
);

INSERT INTO app_user
(email, phone_number, first_name, middle_name, last_name, street_number, street_name, city, province, postalcode, password, credit_card_number, credit_card_expiry, role)
VALUES
('test@test.com', '9171972175', 'Alex', NULL, 'Chin', '204', 'College', 'Toronto', 'Ontario', '85427', '12345678', '1235647895123456', '2019-04-05', 'admin')
;

INSERT INTO app_user
(email, phone_number, first_name, middle_name, last_name, street_number, street_name, city, province, postalcode, password, credit_card_number, credit_card_expiry)
VALUES
('gfruen0@va.gov', '3033802287', 'Gay', NULL, 'Fruen', '95', 'Sauthoff', 'Littleton', 'CO', '80127', 'GciwG3L', '3557551877481443', '2020-11-16'),
('glemoir1@vistaprint.com', '5128737578', 'Gratia', NULL, 'Lemoir', '4515', 'Glendale', 'Austin', 'TX', '78769', 'Tdpn8qqm', '374283352643019', '2021-05-11'),
('lwalak2@digg.com', '3601393003', 'Lamar', 'Ninnoli', 'Walak', '31618', 'Vidon', 'Seattle', 'WA', '98166', '8NRLaoX', '5010120991364517', '2021-08-04'),
('dsallnow3@house.gov', '9171972172', 'Dale', NULL, 'Sallnow', '40', 'Dwight', 'New York City', 'NY', '10045', 'leVqKSUN3ah', '348349904636877', '2023-02-21'),
('jdundendale4@vimeo.com', '7868402090', 'Juanita', NULL, 'Dundendale', '2139', 'Homewood', 'Miami', 'FL', '33158', 'PESaSAC', '5602210596645495', '2019-07-25'),
('gtreharne5@yelp.com', '9794984331', 'Guss', NULL, 'Treharne', '1', 'Sundown', 'College Station', 'TX', '77844', 'Tz5fP81', '3576410203224018', '2020-02-16'),
('etiffney6@free.fr', '8307495550', 'Elsbeth', 'Swindon', 'Tiffney', '94', 'Atwood', 'San Antonio', 'TX', '78245', 'ysN1Us8OBxh', '50383595814270923', '2021-02-04'),
('hdcosta7@tumblr.com', '3105435654', 'Humfried', 'Huscroft', 'D''Costa', '12', 'Schurz', 'Los Angeles', 'CA', '90071', 'jTdQqdiaJq', '4936761858284077600', '2020-07-10'),
('bmackeeg8@themeforest.net', '2141154479', 'Betta', NULL, 'MacKeeg', '2', 'Muir', 'Dallas', 'TX', '75231', '86sJDD', '3545289870681694', '2020-10-31'),
('soreilly9@kickstarter.com', '2067457909', 'Sauncho', NULL, 'O''Reilly', '65', 'Memorial', 'Seattle', 'WA', '98195', 'aTmhCoEWx', '5510749600849057', '2021-11-26'),
('dvatchera@bloglines.com', '5131192344', 'Danna', 'Berget', 'Vatcher', '791', 'Brickson Park', 'Cincinnati', 'OH', '45238', 'fZYQzFowfn', '6387836786483369', '2020-06-26'),
('fgodrichb@mlb.com', '7573005473', 'Freemon', NULL, 'Godrich', '3', 'Jenna', 'Norfolk', 'VA', '23509', 'PzpzThvs', '3568547208028082', '2020-11-04'),
('dscanderetc@trellian.com', '2819708676', 'Dex', 'Wrench', 'Scanderet', '81', 'Lunder', 'Houston', 'TX', '77060', 'ZlvxQCEIYlD', '5002350917161719', '2021-06-05'),
('rblasid@mediafire.com', '4054276636', 'Rosemarie', NULL, 'Blasi', '60', 'Prentice', 'Oklahoma City', 'OK', '73167', 'jAJtbHgpVG04', '67066220940412756', '2021-05-13'),
('bluckcocke@sakura.ne.jp', '9093850834', 'Bobby', NULL, 'Luckcock', '574', 'Upham', 'San Bernardino', 'CA', '92424', 'qRTlaakmLF', '3577348240444042', '2020-06-26'),
('bnazairf@friendfeed.com', '3049678790', 'Babette', 'Masterman', 'Nazair', '748', 'Farmco', 'Charleston', 'WV', '25362', 'kfHYXdUFo5A', '3580360296099666', '2019-12-10'),
('msamwayeg@berkeley.edu', '8329604228', 'Mollie', NULL, 'Samwaye', '185', 'Oriole', 'Spring', 'TX', '77388', '6Tfld8wRmgw2', '6767448418373709494', '2020-12-10'),
('rklischh@i2i.jp', '5032480194', 'Ronda', 'Postins', 'Klisch', '0316', 'Moulton', 'Salem', 'OR', '97306', '9YUeDQHHGvB', '3577002439786073', '2022-06-12'),
('sboyni@dyndns.org', '5041393876', 'Shari', NULL, 'Boyn', '41', 'Fair Oaks', 'Metairie', 'LA', '70033', '1n1wAkPH', '3576277128526102', '2020-05-16'),
('slyenyngj@ehow.com', '5169530058', 'Sara', 'McLernon', 'Lyenyng', '5339', 'Luster', 'Jamaica', 'NY', '11407', 'cc77eus4KV', '374283116891490', '2020-12-24')
;

INSERT INTO theatre
(theatre_name, phone_number, street_number, street_name, city, province, postalcode)
VALUES
('Muller-Batz', '8582154910', '73954', 'Marquette', 'San Diego', 'CA', '92132'),
('Tromp LLC', '9079477932', '354', 'Corben', 'Anchorage', 'AK', '99599'),
('Nitzsche and Sons', '9092746143', '56076', 'Corscot', 'San Bernardino', 'CA', '92424'),
('Davis-Ondricka', '5174313616', '13', 'Kennedy', 'Lansing', 'MI', '48919'),
('Howe-Klocko', '2155451402', '58', 'Claremont', 'Philadelphia', 'PA', '19184'),
('Russel, Shanahan and Koss', '7637055882', '97791', 'Gulseth', 'Loretto', 'MN', '55598'),
('Ward and Sons', '8063344414', '7', 'Twin Pines', 'Lubbock', 'TX', '79405'),
('Bauch and Sons', '2125140187', '6', 'Surrey', 'Jamaica', 'NY', '11431'),
('Lebsack Inc', '9202290481', '814', 'Jay', 'Appleton', 'WI', '54915'),
('Krajcik-Medhurst', '3146777882', '78', 'Dorton', 'Saint Louis', 'MO', '63180')
;

INSERT INTO auditorium
(theatre_id, auditorium_number, max_seats, screen_size)
VALUES
(1, 1, 281, 'Large'),
(1, 2, 166, 'Large'),
(1, 3, 199, 'Medium'),
(1, 4, 112, 'Small'),
(1, 5, 141, 'Large'),
(1, 6, 125, 'Small'),
(1, 7, 273, 'Small'),
(1, 8, 263, 'Medium'),
(1, 9, 298, 'Medium'),
(1, 10, 150, 'Small'),
(2, 1, 132, 'Large'),
(2, 2, 221, 'Large'),
(2, 3, 300, 'Medium'),
(2, 4, 155, 'Large'),
(2, 5, 210, 'Large'),
(2, 6, 191, 'Large'),
(2, 7, 254, 'Medium'),
(2, 8, 162, 'Large'),
(2, 9, 129, 'Medium'),
(2, 10, 191, 'Small'),
(3, 1, 217, 'Large'),
(3, 2, 188, 'Large'),
(3, 3, 225, 'Medium'),
(3, 4, 157, 'Medium'),
(3, 5, 141, 'Medium'),
(3, 6, 216, 'Small'),
(3, 7, 245, 'Small'),
(3, 8, 190, 'Medium'),
(3, 9, 299, 'Large'),
(3, 10, 115, 'Large'),
(4, 1, 212, 'Small'),
(4, 2, 208, 'Small'),
(4, 3, 121, 'Medium'),
(4, 4, 172, 'Large'),
(4, 5, 163, 'Small'),
(4, 6, 222, 'Medium'),
(4, 7, 217, 'Medium'),
(4, 8, 108, 'Medium'),
(4, 9, 189, 'Medium'),
(4, 10, 295, 'Medium'),
(5, 1, 247, 'Medium'),
(5, 2, 282, 'Large'),
(5, 3, 164, 'Small'),
(5, 4, 101, 'Large'),
(5, 5, 132, 'Small'),
(5, 6, 113, 'Large'),
(5, 7, 165, 'Large'),
(5, 8, 155, 'Large'),
(5, 9, 299, 'Small'),
(5, 10, 191, 'Medium'),
(6, 1, 179, 'Medium'),
(6, 2, 294, 'Large'),
(6, 3, 101, 'Small'),
(6, 4, 118, 'Large'),
(6, 5, 261, 'Medium'),
(6, 6, 138, 'Small'),
(6, 7, 230, 'Small'),
(6, 8, 188, 'Small'),
(6, 9, 257, 'Small'),
(6, 10, 277, 'Small'),
(7, 1, 218, 'Small'),
(7, 2, 107, 'Small'),
(7, 3, 285, 'Medium'),
(7, 4, 260, 'Medium'),
(7, 5, 128, 'Medium'),
(7, 6, 267, 'Medium'),
(7, 7, 280, 'Medium'),
(7, 8, 102, 'Large'),
(7, 9, 237, 'Small'),
(7, 10, 198, 'Small'),
(8, 1, 101, 'Small'),
(8, 2, 235, 'Medium'),
(8, 3, 158, 'Medium'),
(8, 4, 133, 'Small'),
(8, 5, 116, 'Large'),
(8, 6, 204, 'Large'),
(8, 7, 176, 'Large'),
(8, 8, 103, 'Medium'),
(8, 9, 177, 'Medium'),
(8, 10, 197, 'Medium'),
(9, 1, 177, 'Small'),
(9, 2, 156, 'Small'),
(9, 3, 242, 'Small'),
(9, 4, 266, 'Medium'),
(9, 5, 230, 'Large'),
(9, 6, 128, 'Medium'),
(9, 7, 241, 'Large'),
(9, 8, 244, 'Large'),
(9, 9, 111, 'Large'),
(9, 10, 231, 'Large'),
(10, 1, 148, 'Medium'),
(10, 2, 144, 'Medium'),
(10, 3, 143, 'Medium'),
(10, 4, 186, 'Medium'),
(10, 5, 271, 'Large'),
(10, 6, 196, 'Small'),
(10, 7, 248, 'Small'),
(10, 8, 193, 'Small'),
(10, 9, 183, 'Large'),
(10, 10, 200, 'Large')
;

INSERT INTO supplier_contact
(phone_number, first_name, middle_name, last_name)
VALUES
('4394121093', 'Dario', NULL, 'Meineking'),
('2364826756', 'Janna', NULL, 'Peacey'),
('9996324231', 'Petronille', NULL, 'Nornable'),
('5964372198', 'Marijn', NULL, 'Abarough'),
('7539330051', 'Wang', NULL, 'Coade'),
('3792190471', 'Farica', NULL, 'Frankton'),
('5741582774', 'Finley', NULL, 'Gulley'),
('2983561515', 'Prissie', NULL, 'Bonallick'),
('4168421033', 'Lannie', NULL, 'Surphliss'),
('7484532584', 'Kennan', NULL, 'Mickelwright')
;

INSERT INTO movie_supplier
(company_name, street_number, street_name, city, province, postalcode, supplier_contact_id)
VALUES
('Hoeger, Mayer and Erdman', '60', 'Meadow Valley', 'Miami', 'FL', '33124', 1),
('Stracke and Sons', '13', 'Golf', 'Buffalo', 'NY', '14225', 2),
('Kilback-Veum', '53', 'Di Loreto', 'Springfield', 'MO', '65805', 3),
('Sawayn, Pagac and Schmidt', '970', '2nd', 'Boulder', 'CO', '80310', 4),
('Wuckert, Schaden and Kirlin', '895', 'Evergreen', 'Oceanside', 'CA', '92056', 5),
('Fritsch and Sons', '5', 'Dorton', 'Harrisburg', 'PA', '17126', 6),
('Heidenreich Inc', '1', 'Caliangt', 'Denver', 'CO', '80217', 7),
('Carroll, Hahn and Kessler', '5', 'Cherokee', 'Riverside', 'CA', '92513', 8),
('Gislason LLC', '8412', 'Forest', 'Miami', 'FL', '33158', 9),
('Prosacco, Conroy and Howell', '3759', 'Weeping Birch', 'Alexandria', 'VA', '22333', 10)
;

INSERT INTO movie
(poster_url, title, duration_minutes, age_rating, plot, start_date, movie_supplier_id, end_date)
VALUES
('http://image.tmdb.org/t/p/w500/v5HlmJK9bdeHxN2QhaFP1ivjX3U.jpg', 'Pacific Rim: Uprising', 104, 'NC-17', 'phasellus sit amet erat nulla tempus vivamus in felis eu sapien cursus vestibulum proin eu mi nulla ac', '2019-03-17', 1, '2019-01-15'),
('http://image.tmdb.org/t/p/w500/c0nUX6Q1ZB0P2t1Jo6EeFSVnOGQ.jpg', 'Isle of Dogs', 66, 'R', 'tempor turpis nec euismod scelerisque quam turpis adipiscing lorem vitae mattis', '2018-05-21', 3, '2018-05-08'),
('http://image.tmdb.org/t/p/w500/jvDBfavZASdKsJunu9VCAtXjLS2.jpg', 'Unsane', 77, 'G', 'sed tristique in tempus sit amet sem fusce consequat nulla nisl nunc nisl duis bibendum felis sed interdum venenatis turpis', '2018-12-09', 4, '2018-04-16'),
('http://image.tmdb.org/t/p/w500/xHdf2wRgCSp9MrZRryikiZIH6jB.jpg', 'Sherlock Gnomes', 107, 'G', 'facilisi cras non velit nec nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus vel nulla', '2018-04-12', 5, '2018-06-22'),
('http://image.tmdb.org/t/p/w500/7ZrIkdRTbPNCB6LCk71CxWLVJMc.jpg', 'Paul, Apostle of Christ', 118, 'PG', 'orci luctus et ultrices posuere cubilia curae donec pharetra magna vestibulum aliquet ultrices erat tortor sollicitudin mi sit amet', '2019-03-06', 6, '2019-01-04'),
('http://image.tmdb.org/t/p/w500/b2Z9B4tWFYLJspBOVIYpZI4ACVC.jpg', 'Midnight Sun', 65, 'PG', 'et magnis dis parturient montes nascetur ridiculus mus etiam vel augue vestibulum rutrum rutrum neque', '2018-05-08', 7, '2018-07-10'),
('http://image.tmdb.org/t/p/w500/w8tf90g8mvlsa11Bg3s81H6lOLg.jpg', 'Hichki', 89, 'NC-17', 'ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae donec pharetra magna vestibulum aliquet', '2018-06-04', 8, '2018-08-03'),
('http://image.tmdb.org/t/p/w500/8hgqfshiwHcIoAk7uKckKcDVpv4.jpg', 'Final Portrait', 82, 'R', 'quis odio consequat varius integer ac leo pellentesque ultrices mattis odio donec vitae nisi nam ultrices libero non mattis pulvinar', '2018-07-07', 9, '2018-11-29'),
('http://image.tmdb.org/t/p/w500/yAcb58vipewa1BfNit2RjE6boXA.jpg', 'A Wrinkle in Time', 94, 'PG', 'quisque erat eros viverra eget congue eget semper rutrum nulla nunc purus phasellus in felis donec semper', '2018-11-23', 10, '2018-03-24'),
('http://image.tmdb.org/t/p/w500/wo3gu56JKRwZ2TpKt8mPjXLQK23.jpg', 'The Strangers: Prey at Night', 62, 'PG', 'tempus vivamus in felis eu sapien cursus vestibulum proin eu mi nulla ac enim in tempor turpis nec', '2018-11-05', 1, '2018-11-26'),
('http://image.tmdb.org/t/p/w500/fyufzd2S1lLxMxkTHTnmr6VJfe5.jpg', 'Red Sparrow', 109, 'G', 'sit amet diam in magna bibendum imperdiet nullam orci pede venenatis non sodales sed tincidunt eu', '2018-04-01', 3, '2018-07-17'),
('http://image.tmdb.org/t/p/w500/1wS89vns6cseCn4UHSqj97xKEKW.jpg', 'Game Night', 62, 'PG-13', 'turpis enim blandit mi in porttitor pede justo eu massa donec', '2018-05-29', 4, '2019-01-24'),
('http://image.tmdb.org/t/p/w500/2yjSvEDuM3rLDng40erLsWkQRfn.jpg', 'Peter Rabbit', 91, 'G', 'interdum mauris ullamcorper purus sit amet nulla quisque arcu libero rutrum', '2018-11-03', 5, '2019-01-07'),
('http://image.tmdb.org/t/p/w500/wamM5AyPAeNPNPfjWRI9WD8dweQ.jpg', 'Death Wish', 91, 'G', 'mi pede malesuada in imperdiet et commodo vulputate justo in blandit ultrices enim lorem ipsum dolor sit amet consectetuer', '2018-09-12', 6, '2018-08-10'),
('http://image.tmdb.org/t/p/w500/wh1f7peigW0qUXXwynwVAt7axZd.jpg', 'The Hurricane Heist', 105, 'G', 'sit amet lobortis sapien sapien non mi integer ac neque duis bibendum morbi non quam nec dui luctus', '2018-06-12', 7, '2018-08-11'),
('http://image.tmdb.org/t/p/w500/d3qcpfNwbAMCNqWDHzPQsUYiUgS.jpg', 'Annihilation', 107, 'PG-13', 'sed tincidunt eu felis fusce posuere felis sed lacus morbi sem mauris laoreet', '2018-07-28', 8, '2019-01-23'),
('http://image.tmdb.org/t/p/w500/bXrZ5iHBEjH7WMidbUDQ0U2xbmr.jpg', 'Jumanji: Welcome to the Jungle', 120, 'NC-17', 'elit ac nulla sed vel enim sit amet nunc viverra dapibus', '2018-05-11', 9, '2019-02-07'),
('http://image.tmdb.org/t/p/w500/pU1ULUq8D3iRxl1fdX2lZIzdHuI.jpg', 'Ready Player One', 64, 'PG', 'elit ac nulla sed vel enim sit amet nunc viverra dapibus nulla suscipit', '2018-03-21', 10, '2018-12-11'),
('http://image.tmdb.org/t/p/w500/iilu7fMIXPTuirk4mCN7eNifBhn.jpg', 'Finding Your Feet', 70, 'PG-13', 'et magnis dis parturient montes nascetur ridiculus mus etiam vel augue vestibulum rutrum rutrum neque aenean auctor gravida sem', '2018-04-03', 1, '2018-05-02'),
('http://image.tmdb.org/t/p/w500/o8mGEiiEmR7yyoDqk9iSAeokYVZ.jpg', 'Outside In', 76, 'G', 'consectetuer adipiscing elit proin interdum mauris non ligula pellentesque ultrices phasellus id', '2018-04-30', 3, '2018-07-12')
;

INSERT INTO actor
(first_name, middle_name, last_name)
VALUES
('Jocelin', NULL, 'Wride'),
('Gabrielle', 'Ledbetter', 'Moisey'),
('Maurits', NULL, 'Robke'),
('Maddi', 'MacNeely', 'Churchward'),
('Delcina', NULL, 'Finessy'),
('Sandy', 'Kik', 'Dulieu'),
('Amata', NULL, 'Maasze'),
('Raymond', 'Iskowicz', 'Bertolin'),
('Monte', 'Woolveridge', 'Fiennes'),
('Alex', NULL, 'Fargher'),
('Christiana', 'Temperley', 'Grabert'),
('Grantham', NULL, 'Water'),
('Serge', 'Oxberry', 'Santi'),
('Tuck', 'Hamshere', 'Crann'),
('Rosemonde', 'Tregust', 'Trainor'),
('Jacquenette', 'Jenken', 'Gehrts'),
('Buffy', 'Cominotti', 'Bickerdicke'),
('Gris', 'Galland', 'Duchant'),
('Celene', NULL, 'Cullinan'),
('Merrie', 'Shalloe', 'Whittlesey'),
('Elmer', NULL, 'Bostick'),
('Chester', 'Georgiades', 'Parman'),
('Raven', NULL, 'Essame'),
('Liuka', 'Brunel', 'Goody'),
('Michaeline', NULL, 'Clouter'),
('Denney', 'Burden', 'Saxelby'),
('Paolina', NULL, 'Olin'),
('Yehudit', 'Beecraft', 'Rickhuss'),
('Clary', 'Strettle', 'McMurdo'),
('Isis', 'Taverner', 'Crush'),
('Nessa', 'Botten', 'Newey'),
('Tarah', NULL, 'D''Emanuele'),
('Allan', NULL, 'Seaton'),
('Beckie', NULL, 'Loades'),
('Ammamaria', NULL, 'Frude'),
('Boone', NULL, 'Fould'),
('Adham', 'Bullene', 'Whitnell'),
('Katherina', 'Fishe', 'Sansam'),
('Curry', 'McCrillis', 'Tattam'),
('Millisent', NULL, 'Ruslin'),
('Isador', 'Dayce', 'Winsor'),
('Patricio', NULL, 'Warrack'),
('Misha', NULL, 'Mandry'),
('Shannah', 'Spofforth', 'Griston'),
('Dotty', 'Scarrott', 'Milius'),
('Flossie', NULL, 'Leads'),
('Fan', NULL, 'Bourley'),
('Woodrow', NULL, 'Sclater'),
('Herta', NULL, 'Bresner'),
('Melicent', NULL, 'Thieme'),
('Stafani', 'Goodered', 'Linsay'),
('Cindy', NULL, 'Brookes'),
('Nahum', 'Fleming', 'Sutherby'),
('Walt', 'Plumridege', 'Otton'),
('Genovera', 'Bussons', 'Boame'),
('Jeffrey', NULL, 'Fishley'),
('Clo', NULL, 'Paumier'),
('Nikolos', NULL, 'Cortese'),
('Silvia', 'Marquese', 'Dallin'),
('Petronille', 'Willcott', 'Shorey'),
('Xena', 'Newart', 'Huggon'),
('Cal', 'Pointin', 'Swatton'),
('Roxane', 'Imlock', 'Learoid'),
('Willie', 'Bretherick', 'Klesse'),
('Nial', 'Youdell', 'Mitchelhill'),
('Zsazsa', 'Shuttell', 'Cisec'),
('Malissia', 'Werrilow', 'Claxson'),
('Wendy', 'Rutigliano', 'Antusch'),
('Terri', 'Reeves', 'Lanfare'),
('Lisa', NULL, 'Tindle'),
('Redford', NULL, 'Kerne'),
('Minny', 'Ratie', 'Saffen'),
('Zebulen', 'Gilcriest', 'Duffree'),
('Jacki', NULL, 'Boxall'),
('Huey', 'Clewlowe', 'Aseef'),
('Frederik', NULL, 'Jervis'),
('Rivi', 'Keighley', 'Inglesent'),
('Christophe', NULL, 'Collet'),
('Alister', NULL, 'Burvill'),
('Minny', NULL, 'Jiras'),
('Jehu', 'Rew', 'McDarmid'),
('Denver', 'Gatherer', 'Shalliker'),
('Cooper', 'Stanbury', 'Hartigan'),
('Berte', 'Rapa', 'Loftin'),
('Krista', NULL, 'Caulfield'),
('Laverna', NULL, 'Woehler'),
('Joannes', 'Keesman', 'Younger'),
('Berton', 'Laughrey', 'O''Sirin'),
('Torrie', NULL, 'Casini'),
('Sigismund', NULL, 'Portch'),
('Sidney', 'Chaikovski', 'Mc Combe'),
('Wilden', NULL, 'McCaffrey'),
('Ulrike', 'Woodwind', 'Bawles'),
('Alison', 'Nason', 'Masterton'),
('Cass', 'Grimsdell', 'Devitt'),
('Sadye', NULL, 'Loughnan'),
('Kile', 'Cars', 'Ivanchikov'),
('Lorne', NULL, 'Egentan'),
('Walker', 'Pitchford', 'Mion'),
('Trevar', NULL, 'Ballinger')
;

INSERT INTO director
(first_name, middle_name, last_name)
VALUES
('Ragnar', NULL, 'Parmenter'),
('Dom', NULL, 'Bengtson'),
('Ardella', 'Penketh', 'Bissill'),
('Wald', 'Accomb', 'Boddy'),
('Kory', NULL, 'Olivo'),
('Mohandas', 'Dunton', 'Davenhall'),
('Staci', NULL, 'Juett'),
('Cassie', 'Dorcey', 'Rosettini'),
('Preston', 'De Gregoli', 'Pepperell'),
('Zia', NULL, 'Chomiszewski')
;

INSERT INTO production_company
(production_company_name)
VALUES
('Simonis, Aufderhar and Okuneva'),
('Miller-Swaniawski'),
('Sauer, Miller and Hodkiewicz'),
('Walker and Sons'),
('Gleason-Williamson'),
('Mitchell Group'),
('Kohler and Sons'),
('Dickinson LLC'),
('Stanton Inc'),
('Greenfelder Inc')
;

INSERT INTO plays
(actor_id, movie_id)
VALUES
(28, 1),
(19, 2),
(25, 3),
(45, 4),
(76, 5),
(32, 6),
(68, 7),
(52, 8),
(38, 9),
(18, 10),
(90, 11),
(80, 12),
(92, 13),
(100, 14),
(7, 15),
(96, 16),
(25, 17),
(28, 18),
(26, 19),
(87, 20),
(18, 1),
(10, 2),
(69, 3),
(76, 4),
(89, 5),
(35, 6),
(32, 7),
(70, 8),
(11, 9),
(80, 10),
(81, 11),
(79, 12),
(28, 13),
(30, 14),
(4, 15),
(52, 16),
(47, 17),
(44, 18),
(71, 19),
(7, 20)
;

INSERT INTO directs
(director_id, movie_id)
VALUES
(1, 1),
(3, 2),
(9, 3),
(9, 4),
(8, 5),
(3, 6),
(9, 7),
(4, 8),
(6, 9),
(5, 10),
(2, 11),
(4, 12),
(7, 13),
(8, 14),
(10, 15),
(8, 16),
(7, 17),
(3, 18),
(8, 19),
(8, 20)
;

INSERT INTO makes
(production_company_id, movie_id)
VALUES
(9, 1),
(1, 2),
(10, 3),
(8, 4),
(2, 5),
(10, 6),
(4, 7),
(5, 8),
(6, 9),
(4, 10),
(3, 11),
(10, 12),
(3, 13),
(2, 14),
(9, 15),
(8, 16),
(5, 17),
(9, 18),
(5, 19),
(7, 20)
;

INSERT INTO screening
(theatre_id, auditorium_number, movie_id, date_time)
VALUES
(1, 1, 13, '2018-07-19 13:21:40'),
(1, 2, 18, '2018-08-16 19:40:53'),
(1, 3, 17, '2018-09-24 16:21:34'),
(1, 4, 19, '2018-03-26 14:21:16'),
(1, 5, 14, '2018-07-23 03:13:51'),
(1, 6, 11, '2018-10-10 01:15:22'),
(1, 7, 3, '2018-09-01 08:52:33'),
(1, 8, 15, '2018-08-16 01:40:52'),
(1, 9, 11, '2018-07-04 01:17:16'),
(1, 10, 6, '2018-03-26 17:19:17'),
(2, 1, 3, '2018-12-17 10:06:08'),
(2, 2, 19, '2018-08-14 07:36:48'),
(2, 3, 1, '2018-12-05 22:20:17'),
(2, 4, 15, '2018-07-14 11:00:09'),
(2, 5, 18, '2018-11-18 14:01:03'),
(2, 6, 19, '2018-11-08 21:50:50'),
(2, 7, 15, '2018-09-20 22:24:13'),
(2, 8, 12, '2018-06-07 11:50:56'),
(2, 9, 1, '2018-10-17 15:18:38'),
(2, 10, 15, '2018-03-27 12:26:07'),
(3, 1, 18, '2018-06-16 03:30:02'),
(3, 2, 10, '2018-11-26 19:26:15'),
(3, 3, 19, '2018-04-05 18:14:18'),
(3, 4, 9, '2018-07-16 10:54:48'),
(3, 5, 10, '2018-05-26 20:56:15'),
(3, 6, 18, '2018-06-04 19:04:17'),
(3, 7, 15, '2018-11-25 21:36:57'),
(3, 8, 6, '2018-09-20 01:00:05'),
(3, 9, 9, '2018-04-11 13:46:55'),
(3, 10, 1, '2018-09-30 23:32:49'),
(4, 1, 12, '2018-11-29 08:07:26'),
(4, 2, 19, '2018-07-25 08:38:18'),
(4, 3, 19, '2018-09-10 23:33:00'),
(4, 4, 9, '2018-04-09 21:55:29'),
(4, 5, 16, '2018-10-19 21:24:16'),
(4, 6, 17, '2018-05-09 02:52:35'),
(4, 7, 12, '2018-08-24 00:00:06'),
(4, 8, 10, '2018-05-18 06:39:27'),
(4, 9, 16, '2018-06-24 05:31:34'),
(4, 10, 16, '2018-11-11 10:41:03'),
(5, 1, 16, '2018-07-02 12:17:24'),
(5, 2, 11, '2018-03-21 20:56:39'),
(5, 3, 18, '2018-07-01 11:34:23'),
(5, 4, 9, '2018-09-15 23:21:43'),
(5, 5, 13, '2018-04-15 01:59:06'),
(5, 6, 7, '2018-05-17 08:38:34'),
(5, 7, 10, '2018-12-13 17:49:06'),
(5, 8, 15, '2018-05-09 09:08:00'),
(5, 9, 13, '2018-11-07 07:39:22'),
(5, 10, 7, '2018-07-15 12:40:40'),
(6, 1, 1, '2018-09-30 20:03:29'),
(6, 2, 13, '2018-08-13 05:37:42'),
(6, 3, 13, '2018-10-23 00:07:06'),
(6, 4, 4, '2018-06-14 18:34:14'),
(6, 5, 9, '2018-11-30 20:56:00'),
(6, 6, 4, '2018-11-25 11:41:23'),
(6, 7, 6, '2018-07-08 02:00:13'),
(6, 8, 15, '2018-06-07 00:43:05'),
(6, 9, 10, '2018-07-27 03:39:59'),
(6, 10, 13, '2018-07-12 23:39:16'),
(7, 1, 17, '2018-10-17 17:01:34'),
(7, 2, 2, '2018-06-08 18:40:36'),
(7, 3, 12, '2018-12-01 10:00:41'),
(7, 4, 10, '2018-11-16 21:16:34'),
(7, 5, 18, '2018-09-24 06:09:44'),
(7, 6, 1, '2018-08-07 04:55:53'),
(7, 7, 12, '2018-04-27 16:58:49'),
(7, 8, 13, '2018-09-23 03:14:52'),
(7, 9, 3, '2018-08-09 07:59:35'),
(7, 10, 20, '2018-10-08 00:13:08'),
(8, 1, 12, '2018-11-24 05:53:34'),
(8, 2, 1, '2018-11-02 05:30:53'),
(8, 3, 14, '2018-04-09 23:35:50'),
(8, 4, 11, '2018-06-29 22:52:18'),
(8, 5, 13, '2018-06-08 03:24:46'),
(8, 6, 19, '2018-08-04 11:52:39'),
(8, 7, 13, '2018-09-26 12:04:46'),
(8, 8, 13, '2018-08-16 00:20:02'),
(8, 9, 5, '2018-03-31 08:10:08'),
(8, 10, 19, '2018-08-12 07:34:16'),
(9, 1, 17, '2018-12-13 17:38:06'),
(9, 2, 7, '2018-04-30 20:09:46'),
(9, 3, 16, '2018-06-11 09:19:04'),
(9, 4, 1, '2018-04-12 02:48:01'),
(9, 5, 16, '2018-07-16 01:10:16'),
(9, 6, 1, '2018-03-22 20:11:50'),
(9, 7, 4, '2018-12-19 12:08:28'),
(9, 8, 14, '2018-09-02 09:25:15'),
(9, 9, 2, '2018-11-02 12:42:46'),
(9, 10, 8, '2018-06-19 14:20:57'),
(10, 1, 7, '2018-08-30 00:31:06'),
(10, 2, 11, '2018-09-11 08:57:01'),
(10, 3, 19, '2018-10-04 04:53:43'),
(10, 4, 8, '2018-09-29 04:09:29'),
(10, 5, 12, '2018-08-17 10:53:18'),
(10, 6, 16, '2018-05-25 15:21:25'),
(10, 7, 4, '2018-12-14 08:41:17'),
(10, 8, 12, '2018-03-22 18:41:44'),
(10, 9, 13, '2018-06-11 03:35:30'),
(10, 10, 5, '2018-10-07 04:34:13')
;

INSERT INTO ticket
(user_id, theatre_id, auditorium_number, date_time, number_of_tickets, purchase_date_time)
VALUES
(1, 10, 5, '2018-08-17 10:53:18', 5, '2018-08-05 17:10:47')
;

INSERT INTO movie_review
(movie_id, user_id, rating, comments)
VALUES
(1, 1, 3, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 2, 2, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 3, 3, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 4, 4, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 5, 5, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 6, 2, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 7, 3, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 8, 5, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 9, 1, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 10, 3, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 11, 3, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 12, 2, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 13, 1, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 14, 5, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 15, 3, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 17, 5, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 18, 3, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 19, 2, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 20, 3, 'luctus nec molestie sed justo pellentesque viverra pede'),
(1, 16, 4, 'luctus nec molestie sed justo pellentesque viverra pede'),
(2, 14, 5, 'vestibulum aliquet ultrices erat tortor sollicitudin mi sit amet lobortis'),
(3, 1, 1, 'nonummy maecenas tincidunt lacus at velit vivamus vel nulla eget'),
(5, 21, 2, 'lacinia erat vestibulum sed magna at nunc commodo'),
(7, 20, 5, 'interdum venenatis turpis enim blandit mi in porttitor pede justo'),
(9, 15, 3, 'etiam vel augue vestibulum rutrum rutrum'),
(18, 3, 5, 'nullam porttitor lacus at turpis donec posuere metus'),
(20, 20, 5, 'elit ac nulla sed vel enim sit'),
(15, 13, 2, 'maecenas rhoncus aliquam lacus morbi'),
(14, 8, 2, 'imperdiet nullam orci pede venenatis non sodales sed')
;
