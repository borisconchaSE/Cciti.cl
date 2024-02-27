<?php

namespace Intouch\Framework\Mapping;

class BaseMapper {

    public function __construct(public $sourceObject) {}

    public function MapTo(string $targetClass) {

        // Instanciar el objeto de destino
        $new = new $targetClass();

        foreach(get_object_vars($new) as $var => $value) {

            if (isset($this->sourceObject->$var)) {
                $new->$var = $this->sourceObject->$var;
            }
        }

        return $new;
    }
}