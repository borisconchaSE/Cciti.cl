<?php

namespace Intouch\Framework\Controllers;

class Result {

    public function __construct(
        public string $ResultType,
        public int $UserUniqueID, 
        public bool $IsSessionActive,
        public string $Request,
        public int $ErrorCode,
        public string $ErrorMessage,
        public string $DebugMessage) {}

}