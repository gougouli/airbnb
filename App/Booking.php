<?php


namespace App;


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
        $stmt = $db->prepare("SELECT * FROM booking WHERE id_accomodation = :id_acco AND 
        (start_date > :enddate OR end_date < :startdate)");
        $stmt->execute([
            "id_acco" => $this->acco_id,
            "startdate" => $this->start,
            "enddate" => $this->end
        ]);
        return $stmt->rowCount();
    }

    public function getAccoPrice()
    {
        $db = Mysql::getInstance();
        $stmt = $db->prepare("SELECT price FROM accomodation WHERE id = ?");
        $stmt->execute([$this->acco_id]);
        return $stmt->fetch();
    }

    public function calculPrice()
    {
        $price = $this->person * $this->end - $this->start * $this->getAccoPrice();
    }
}