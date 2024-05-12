<?php

// innehåller 10 rows, sjukt nog!

namespace App\Project;

use App\Project\Row;
use App\Game\Card;

class Mat
{
    /**
     * @var array<Row> An array holding five horizontal rows.
     */
    private array $horizontalRows = [];
    /**
     * @var array<Row> An array holding five vertical rows.
     */
    private array $verticalRows = [];

    public function __construct()
    {
        $this->horizontalRows = [new Row(), new Row(), new Row(), new Row(), new Row()];
        $this->verticalRows = [new Row(), new Row(), new Row(), new Row(), new Row()];
    }

    // logiken skickar in två kordinater, den här klassen översätter och ploppar in?
    public function setCard(int $horizontalPosition, int $verticalPosition, Card $card): void
    {
        $this->horizontalRows[$horizontalPosition]->setCard($verticalPosition, $card);
        $this->verticalRows[$verticalPosition]->setCard($horizontalPosition, $card);
    }

    public function __toString()
    {
        $output = "";
        foreach ($this->horizontalRows as $row) {
            $output .= $row . "\n";
        }
        $output = rtrim($output);
        return $output;
    }

    /**
     * @return array<Row> An array holding five horizontal rows.
     */
    public function getHorizontalRows(): array
    {
        return $this->horizontalRows;
    }

    /**
     * @return array<Row> An array holding five vertical rows.
     */
    public function getVerticalRows(): array
    {
        return $this->verticalRows;
    }
}
