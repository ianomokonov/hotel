<?php
    class User {
        public $id;
        public $name;
        public $surname;
        public $middlename;
        public $phone;
        public $email;
    }

    class Car {
        public $id;
        public $img;
        public $name;
        public $description;
        public $price;
        public $fuelType;
        public $engineVolume;
        public $enginePower;
        public $speed;
        public $time;
        public $volumePerHundred;
        public $kpp;
        public $driveUnit;
        public $places;
        public $backVolume;
        public $license;
        public $createYear;
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