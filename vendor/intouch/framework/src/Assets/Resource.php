<?php

namespace Intouch\Framework\Assets;

class Resource {  

    protected $__content = null;
    protected string $__uri = '';

    function __get($name) {

        switch($name) {
            case "content": return $this->__content;
            case "uri": return $this->__uri;
            default:
                user_error("Invalid property: " . __CLASS__ . "->$name");
                return null;
        }

    }

    function __set($name, $value) {

        switch($name) {
            case "content": 
                $this->__content = $value;
                break;
            case "uri": 
                $this->__uri = $value;
                break;
            default:
                user_error("Invalid property: " . __CLASS__ . "->$name");
                return null;
        }

    }
    public function __construct(
        public string $id,
        public string $name,
        public string $localpath = '',
        public string $location = ''
    ) {}

}