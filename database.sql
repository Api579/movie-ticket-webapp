create database lume;
use lume;

create table movies
(   moviesid int unsigned not null auto_increment primary key,
    movie_name char(100) not null,
    synopsis text,
    main_cast text,
    language char(100),
    release_date text,
    genre char(100),
    runtime char(100),
    rating char(100),
    poster_path varchar(255)
);

create table halls
(   hallsid int unsigned not null auto_increment primary key,
    hall_name varchar(50) not null,
    total_rows int not null,
    seats_per_row int not null
);

create table showtimes
(   showtimeid int unsigned not null auto_increment primary key,
    movie_id int unsigned not null,
    show_date date not null,
    show_time time not null,
    hall_id int unsigned not null,
    foreign key (movie_id) references movies(moviesid) on delete cascade,
    foreign key (hall_id) references halls(hallsid) on delete cascade
);

create table seats
(   seatsid int unsigned not null auto_increment primary key,
    hall_id int unsigned not null,
    row_label char(1) not null,
    seat_number int not null,
    foreign key (hall_id) references halls(hallsid)
);

create table showtime_seats 
(   showtime_seatsid int unsigned not null auto_increment primary key,
    showtime_id int unsigned not null,
    seat_id int unsigned not null,
    is_booked boolean default 0,
    foreign key (showtime_id) references showtimes(showtimeid),
    foreign key (seat_id) references seats(seatsid)
);

create table members
(   membersid int unsigned not null auto_increment primary key,
    username varchar(50) not null unique,
    password varchar(255) not null,
    email varchar(100)
);

create table bookings
(   bookingid int unsigned not null auto_increment primary key,
    members_id int unsigned not null,
    showtime_id int unsigned not null,
    seat_id int unsigned not null,
    booking_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    foreign key (members_id) references members(membersid),
    foreign key (showtime_id) references showtimes(showtimeid),
    foreign key (seat_id) references seats(seatsid)
);


