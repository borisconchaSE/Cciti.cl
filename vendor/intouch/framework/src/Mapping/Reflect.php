<?php

namespace Intouch\Framework\Mapping;

class Reflect {

    public static function ToArray(object $Element) {

        // Instanciar el objeto de destino
        $result = [];

        foreach(get_object_vars($Element) as $var => $value) {

            $result[] = (object)[
                'PropertyName'      => $var,
                'Value'             => $value,
            ];
        }

        return $result;
    }
}