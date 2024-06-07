<?php

namespace App\Doctrine;

use Doctrine\ORM\Mapping\UnderscoreNamingStrategy as DoctrineUnderscoreNamingStrategy;

class UnderscoreNamingStrategy extends DoctrineUnderscoreNamingStrategy
{
    public function __construct()
    {
        parent::__construct(CASE_LOWER);
    }
}
