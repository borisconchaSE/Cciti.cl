<?php

namespace Intouch\Framework\Dates;

use Intouch\Framework\Math\Modulo;

class Interval {

    public function __construct(
        public int $Years = 0, 
        public int $Months = 0, 
        public int $Days = 0, 
        public int $Hours = 0, 
        public int $Minutes = 0, 
        public int $Seconds = 0) {        
    }
}