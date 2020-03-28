<?php


namespace App;


class Token
{
    private $alphabet = "0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
    private $token = '';

    public function __construct($length)
    {
        $this->token = substr(str_shuffle(str_repeat($this->alphabet, $length)), 0, $length);
    }
    public function getToken(): string{
        return $this->token;
    }

    public function __toString()
    {
        return $this->getToken();
    }
}
