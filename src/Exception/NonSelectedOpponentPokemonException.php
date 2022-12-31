<?php

namespace App\Exception;

class NonSelectedOpponentPokemonException extends \Exception
{
    public function __construct()
    {
        parent::__construct('You cannot attack your opponent because he/she did not select his/her Pokemon yet.');
    }
}