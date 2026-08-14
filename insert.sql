use lume;

insert into movies (movie_name, synopsis, main_cast, language, release_date, genre, runtime, rating, poster_path) 
values
(
    'The Conjuring: Last Rites',
    'Paranormal investigators Ed and Lorraine Warren take on one last terrifying case involving mysterious entities they must confront.',
    'Vera Farmiga, Patrick Wilson, Mia Tomlinson',
    'English',
    'Fri, 5 Sept 2025',
    'Horror/Thriller',
    '2 hr 15 mins',
    'NC-16',
    'media/conjuring_poster.jpg'
),
(
    'Superman',
    'Superman must reconcile his alien Kryptonian heritage with his human upbringing as reporter Clark Kent. As the embodiment of truth, justice and the human way he soon finds himself in a world that views these as old-fashioned.',
    'David Corenswet, Rachel Brosnahan, Nicholas Hoult',
    'English',
    'Thu, 10 Jul 2025',
    'Action/Adventure',
    '2 hr 9 mins',
    'PG-13',
    'media/superman_poster.jpg'
),
(
    'Weapons',
    'When all but one child from the same class mysteriously vanish on the same night at exactly the same time, a community is left questioning who or what is behind their disappearance.',
    'Josh Brolin, Julia Garner',
    'English',
    'Thu, 7 Aug 2025',
    'Horror/Thriller',
    '2 hr 8 mins',
    'M18',
    'media/weapons_poster.jpg'
),
(
    'The Fantastic Four: First Steps',
    'Forced to balance their roles as heroes with the strength of their family bond, the Fantastic Four must defend Earth from a ravenous space god called Galactus and his enigmatic herald, the Silver Surfer.',
    'Pedro Pascal, Vanessa Kirby, Joseph Quinn, Ebon Moss-Bachrach',
    'English',
    'Thu, 24 Jul 2025',
    'Action/Science Fiction',
    '1 hr 55 mins',
    'PG',
    'media/fantastic4_poster.jpg'
),
(
    'Good Boy',
    'A loyal dog moves to a rural family home with his owner, only to discover supernatural forces lurking in the shadows. As dark entities threaten his human companion, the brave pup must fight to protect the one he loves most.',
    'Indy, Shane Jensen, Arielle Friedman',
    'English',
    'Thu, 30 Oct 2025',
    'Horror/Thriller',
    '1 hr 14 mins',
    'PG-13',
    'media/goodboy_poster.jpg'
),
(
    'Tron: Ares',
    'A highly sophisticated program, Ares, is sent from the digital world into the real world on a dangerous mission.',
    'Greta Lee, Jeff Bridges, Jared Leto',
    'English',
    'Thu, 9 Oct 2025',
    'Action/Science Fiction',
    '1 hr 59 mins',
    'PG',
    'media/tronares_poster.jpg'
),
(
    'Zootopia 2',
    'Brave rabbit cop Judy Hopps and her friend, the fox Nick Wilde, team up again to crack a new case, the most perilous and intricate of their careers.',
    'Ke Huy Qian, Jason Bateman, Ginnifer Goodwin',
    'English',
    'Thu, 27 Nov 2025',
    'Family/Animation',
    '1 hr 30 mins',
    'PG',
    'media/zootopia_poster.jpg'
);

insert into halls (hall_name, total_rows, seats_per_row)
values
('Hall 1', 5, 5), -- 5 rows (A–E), 5 seats each
('Hall 2', 5, 5),
('Hall 3', 5, 5),
('Hall 4', 5, 5);

insert into showtimes (movie_id, show_date, show_time, hall_id)
values
(1, '2025-11-05', '13:30:00', 1),
(1, '2025-11-05', '16:30:00', 1),
(1, '2025-11-05', '19:30:00', 1),
(1, '2025-11-06', '15:00:00', 1),
(1, '2025-11-06', '18:00:00', 1),
(2, '2025-11-05', '10:30:00', 2),
(2, '2025-11-05', '13:15:00', 2),
(2, '2025-11-06', '16:50:00', 2),
(2, '2025-11-06', '19:00:00', 2),
(3, '2025-11-05', '11:30:00', 3),
(3, '2025-11-05', '12:00:00', 3),
(3, '2025-11-06', '14:00:00', 3),
(3, '2025-11-07', '15:30:00', 3),
(3, '2025-11-07', '20:45:00', 3),
(4, '2025-11-05', '13:30:00', 4),
(4, '2025-11-05', '15:50:00', 4),
(6, '2025-11-05', '09:30:00', 4),
(6, '2025-11-05', '12:00:00', 4),
(6, '2025-11-06', '10:15:00', 4),
(6, '2025-11-06', '15:00:00', 4),
(6, '2025-11-07', '18:45:00', 4);

insert into seats (hall_id, row_label, seat_number)
values
(1, 'A', 1), (1, 'A', 2), (1, 'A', 3), (1, 'A', 4), (1, 'A', 5),
(1, 'B', 1), (1, 'B', 2), (1, 'B', 3), (1, 'B', 4), (1, 'B', 5),
(1, 'C', 1), (1, 'C', 2), (1, 'C', 3), (1, 'C', 4), (1, 'C', 5),
(1, 'D', 1), (1, 'D', 2), (1, 'D', 3), (1, 'D', 4), (1, 'D', 5),
(1, 'E', 1), (1, 'E', 2), (1, 'E', 3), (1, 'E', 4), (1, 'E', 5),
(2, 'A', 1), (2, 'A', 2), (2, 'A', 3), (2, 'A', 4), (2, 'A', 5),
(2, 'B', 1), (2, 'B', 2), (2, 'B', 3), (2, 'B', 4), (2, 'B', 5),
(2, 'C', 1), (2, 'C', 2), (2, 'C', 3), (2, 'C', 4), (2, 'C', 5),
(2, 'D', 1), (2, 'D', 2), (2, 'D', 3), (2, 'D', 4), (2, 'D', 5),
(2, 'E', 1), (2, 'E', 2), (2, 'E', 3), (2, 'E', 4), (2, 'E', 5),
(3, 'A', 1), (3, 'A', 2), (3, 'A', 3), (3, 'A', 4), (3, 'A', 5),
(3, 'B', 1), (3, 'B', 2), (3, 'B', 3), (3, 'B', 4), (3, 'B', 5),
(3, 'C', 1), (3, 'C', 2), (3, 'C', 3), (3, 'C', 4), (3, 'C', 5),
(3, 'D', 1), (3, 'D', 2), (3, 'D', 3), (3, 'D', 4), (3, 'D', 5),
(3, 'E', 1), (3, 'E', 2), (3, 'E', 3), (3, 'E', 4), (3, 'E', 5),
(4, 'A', 1), (4, 'A', 2), (4, 'A', 3), (4, 'A', 4), (4, 'A', 5),
(4, 'B', 1), (4, 'B', 2), (4, 'B', 3), (4, 'B', 4), (4, 'B', 5),
(4, 'C', 1), (4, 'C', 2), (4, 'C', 3), (4, 'C', 4), (4, 'C', 5),
(4, 'D', 1), (4, 'D', 2), (4, 'D', 3), (4, 'D', 4), (4, 'D', 5),
(4, 'E', 1), (4, 'E', 2), (4, 'E', 3), (4, 'E', 4), (4, 'E', 5);






