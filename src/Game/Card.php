<?php

namespace Challe_P\Game\Card;

class Card
{
    protected string $value;
    protected string $suit;
    
    public function __construct(string $value = "ace", string $suit = "spades")
    {
        $this->value = $value;
        $this->suit = $suit;
    }

    public function getValue(): string 
    {
        return $this->value . " of " . $this->suit;
    }

    public function getImagePath(): string 
    {
        return 'img/cards/fronts/' . $this->suit . "_" . $this->value . ".svg";
    }
}