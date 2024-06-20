<?php

// innehåller 10 rows, sjukt nog!

namespace App\Project;

use App\Project\Exceptions\PositionFilledException;
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

    /**
     * @var array<int> An array containing the scores.
     */
    public array $score = [0, 0];

    public function __construct(string $matString = "")
    {
        $this->horizontalRows = [new Row(), new Row(), new Row(), new Row(), new Row()];
        $this->verticalRows = [new Row(), new Row(), new Row(), new Row(), new Row()];
        if ($matString == "") {
            return;
        }
        $matString = explode("\n", $matString);
        $lineNo = 0;
        foreach ($matString as $line) {
            $cards = explode(", ", $line);
            $column = 0;
            foreach ($cards as $card) {
                if ($card != "null") {
                    $card = explode(" ", $card);
                    $this->setCard($lineNo, $column, new Card($card[0], $card[2]));
                }
                $column++;
            }
            $lineNo++;
        }
    }

    /**
     * Takes two coordinates, then sets the card at the position.
     */
    public function setCard(int $horizontalPosition, int $verticalPosition, Card $card): void
    {
        try {
            $this->horizontalRows[$horizontalPosition]->setCard($verticalPosition, $card);
            $this->verticalRows[$verticalPosition]->setCard($horizontalPosition, $card);
        } catch (PositionFilledException $e) {
            // Do nothing
        }
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

    /**
     * @param array<int> $score
     */
    public function setScore(array $score): void
    {
        $this->score = $score;
    }

    /**
     * @return array<int>
     */
    public function getScore(): array
    {
        return $this->score;
    }
}
