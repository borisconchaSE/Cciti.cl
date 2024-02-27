<?php

namespace Intouch\Framework\Math;

class Modulo {

    private $cociente = 0;
    private $resto    = 0;

    public function __get($name) {

        switch($name) {
            case "Cociente": return $this->cociente;
            case "Resto"   : return $this->resto;
            default        : return null;
        }
    }

    private function __construct(int $cociente, int $resto)
    {
        $this->cociente = $cociente;
        $this->resto    = $resto;
    }

    public static function Calc(int $dividendo = 0, int $divisor = 1) {

        if ($divisor == 0) return null;

        $_resto    = $dividendo % $divisor;
        $_cociente = ($dividendo - $_resto) / $divisor;

        return new Modulo(
            cociente: $_cociente, resto: $_resto
        );
    }
}