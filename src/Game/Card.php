<?php

namespace Challe_P\Game\Card;

class Card
{
    protected $value;
    protected $suit;
    
    public function __construct($value = "A", $suit = "spades")
    {
        $this->value = $value;
        $this->suit = $suit;
    }

    public function getValue(): string 
    {
        return $this->value . " " . $this->suit;
    }
}