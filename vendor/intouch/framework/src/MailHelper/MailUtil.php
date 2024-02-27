<?php

namespace Intouch\Framework\MailHelper;

class MailUtil {

    public static function Saludo(\Datetime $horaDelDia = null) {

        if (isset($horaDelDia)) {
            $hora = $horaDelDia->format('H') * 1;
        }
        else {
            $hora = (new \DateTime())->format('H') * 1;
        }
        
        if ($hora >= 0 && $hora < 12) {
            return "muy buenos d&iacute;as";
        }
        else if ($hora >= 12 && $hora < 20) {
            return "muy buenas tardes";
        }
        else {
            return "muy buenas noches";
        }
    }

}