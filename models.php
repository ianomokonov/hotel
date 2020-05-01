<?php
    class User {
        public $id;
        public $name;
        public $surname;
        public $middlename;
        public $phone;
        public $email;
    }

    class Room {
        public $id;
        public $img;
        public $name;
        public $description;
        public $price;
    }

    class Place {
        public $id;
        public $name;
    }

    class Order {
        public $id;
        public $userId;
        public $carId;
        public $placeId;
        public $dateFrom;
        public $dateTo;
        public $orderSum;
        public $time;

        public $user;
        public $car;
        public $place;
    }

    class DateRange {
        public $dateFrom;
        public $dateTo;
    }
?>