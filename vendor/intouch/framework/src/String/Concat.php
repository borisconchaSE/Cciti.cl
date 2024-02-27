<?php

namespace Intouch\Framework\String;

class Concat {

    public static function Add(string $old, string $new, string $separator) {

        if (!isset($new) || $new == '')
            return $old;

        if (isset($old) && $old != '') {
            return $old . $separator . $new;
        }
        else {
            return $new;
        }
    }

    public static function AddWithCommaLine($old, $new) {
        return self::Add($old, $new, ",\n");
    }

    public static function AddWithLine($old, $new) {
        return self::Add($old, $new, "\n");
    }

    public static function AddWithComma($old, $new) {
        return self::Add($old, $new, ", ");
    }
}