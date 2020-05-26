<?php


namespace App;

use PDO;
use \Datetime;

class Booking
{
    private $price;

    public function __construct($user_id, $acco_id, $start, $end, $person)
    {
        $this->user_id = $user_id;
        $this->acco_id = $acco_id;
        $this->start = $start;
        $this->end = $end;
        $this->person = $person;
    }

    public function nmbrPerson()
    {
        if($this->person > 0){
            return 1;
        }
        return 0;
    }

    public function reserve()
    {
        $db = Mysql::getInstance();
        $stmt = $db->prepare("INSERT INTO booking (id_user, id_accomodation, start_date, end_date, price, person) VALUES (:id_user, :acco_id, :start_date, :end_date, :price, :person)");
        $stmt->execute([
            "id_user" => $this->user_id,
            "acco_id" => $this->acco_id,
            "start_date" => $this->start,
            "end_date" => $this->end,
            "price" => $this->calculPrice(),
            "person" => $this->person,
        ]);
    }

    public function correctDate()
    {
        if($this->start < $this->end){
            return true;
        }
        return false;
    }

    public function available()
    {
        $db = Mysql::getInstance();
        $stmt = $db->prepare("SELECT * FROM booking WHERE id_accomodation = :acco_id AND (
        (start_date <= :startdate AND end_date >= :startdate) OR
        (start_date <= :enddate AND end_date >= :enddate) OR
        (start_date >= :startdate AND end_date <= :enddate))");
        $stmt->execute([
            "acco_id" => $this->acco_id,
            "startdate" => $this->start,
            "enddate" => $this->end
        ]);
        if($stmt->rowCount() == 0){
            return 1;
        }
        return 0;
    }

    public function getAccoPrice()
    {
        $db = Mysql::getInstance();
        $stmt = $db->prepare("SELECT price FROM accomodation WHERE id = ?");
        $stmt->execute([$this->acco_id]);
        $price = $stmt->fetch(PDO::FETCH_ASSOC)['price'];
        return $price;
    }

    public function calculPrice()
    {
        return $this->person * $this->diffDay() * $this->getAccoPrice();
    }

    public function diffDay()
    {
        $date1 = new DateTime($this->start);
        $date2 = new DateTime($this->end);
        return date_diff($date1, $date2)->days;
    }
}