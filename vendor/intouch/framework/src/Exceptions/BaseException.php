<?php

namespace Intouch\Framework\Exceptions;

class BaseException extends \Exception {

    public function __construct(        
        int $code,
        string $message,
        public string $debugMessage
    ) {
        parent::__construct(message: $message, code: $code);
    }

    public function getDebugMessage() {
        return $this->debugMessage;
    }
}