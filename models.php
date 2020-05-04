<?php
    class User {
        public $id;
        public $name;
        public $surname;
        public $middlename;
        public $email;
    }

    class Course {
        public $id;
        public $name;
        public $description;
        public $points;
        public $questions;
    }

    class Question {
        public $id;
        public $title;
        public $courseId;
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