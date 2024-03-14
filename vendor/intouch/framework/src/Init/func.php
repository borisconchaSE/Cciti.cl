<?php

// Funciones de uso general en la aplicacion que no requieren de una clase de control
// ****************************

use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Configuration\RestrictionConfig;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\Mensajes\Mensaje;
use Intouch\Framework\Restrictions\Restriction;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;

function _first($element){
    if(isset($element) && is_array($element) && !empty($element) ){
        return $element[0];
    }else{
        return null;
    }
}

function __ValidarAccesoRestriccion($idRestriction) {    

    if (isset(Session::Instance()->usuario->Perfil)) {
        return RestrictionConfig::CalcularAccion($idRestriction, Session::Instance()->usuario->Perfil);
    }
    else {
        return "";
    }

}

/**
 * Agrega un nuevo string a un string existente, con un separador.
 */
function _att($original, $attach, $separator) {

    if ($original != '') {
        $original .= $separator;
    }

    $original .= $attach;

    return $original;
}

function _nxt(string $letra = '') {
    $colLetras = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

    // ubicar la letra actual
    $idxLetra = 0;
    if ($letra != '') {
        foreach($colLetras as $idx => $item) {
            if (strtoupper($letra) == $item) {
                $idxLetra = $idx + 1;
                break;
            }
        }
    }

    return $colLetras[$idxLetra];
}

function _mon($ammount, $decimals = 0) {
    return '$ ' . number_format(
        round($ammount, $decimals), 
        $decimals, ",", "."
    );
}

function _nulcol(GenericCollection|null $collection, $dtoName, $key) {

    if (!isset($collection)) {
        return new GenericCollection(
            DtoName: $dtoName,
            Key: $key,
            Values: []
        );
    }
    else {
        return $collection;
    }
}

function _num($number) {
    return _dec($number, 0);
}

function _dec($number, $decimals = 0) {
    return number_format(
        round($number, $decimals), 
        $decimals, ",", "."
    );
}

function _perc($number, $decimals = 0) {
    return number_format(
        round($number, $decimals), 
        $decimals, ",", "."
    ) . '%';
}

function _perm($idRestriction) {

    if (isset(Session::Instance()->usuario->Perfil)) {
        return RestrictionConfig::CalcularAccion($idRestriction, Session::Instance()->usuario->Perfil);
    }
    else {
        return "";
    }
}


function _msg($messageID, $idioma = null) {
    return Mensaje::ObtenerMensaje($messageID, $idioma);
}

function _opt($messageID, $optionValue, $selected=true) {
    return "<option value='$optionValue'" . (($selected) ? " selected " : "") . " class='update-option-onlanguagechange' data-messageid='$messageID'>" . _msg($messageID) . "</option>";
}

function _inp($id, $messageID, $type = 'text', $name = '', $value = '', $required = true, $class = 'form-control') {

    if ($name == '')
        $name = $id;

    return "<input type='$type' name='$name' id='$id' class='$class update-placeholder-onlanguagechange' title='Nombre de la detención' placeholder='" . _msg($messageID) . "' data-messageid='$messageID' ". ($required ? " required='' " : "")  . " value='$value'>";
}

function _nul($input = null, $default = '') {
    return isset($input) ? $input : $default;
}

function _in(string $input, string $searchItems) {
    $parts = explode(',', $searchItems);

    foreach($parts as $part) {
        if ( trim($part) == $input)
            return true;
    }

    return false;
}

function _nularr($array = null, $index = '', $default = '') {

    if (isset($array)) {
        if (isset($array[$index])) {
            return $array[$index];
        }
        else {
            return $default;
        }
    }
    else {
        return $default;
    }

}

function _hasAttr($classname, $attribute) {
    $reflector = new \ReflectionClass($classname);
    $hasAttr = count($reflector->getAttributes($attribute)) > 0;

    return $hasAttr;
}

function _getAttrArgs($classname, $attribute) {

    $reflector = new \ReflectionClass($classname);    
    $attributes = $reflector->getAttributes($attribute);

    if (count($attributes) > 0) {
        $args = $attributes[0]->getArguments();
        return $args;
    }
    else {
        return null;
    }
}

function _getClassAttrs($classname, $attribute) {
    $reflector = new \ReflectionClass($classname);    
    return $reflector->getAttributes($attribute);
}

function _guid() {
    return vsprintf('%s%s-%s-4000-8%.3s-%s%s%s0',str_split(dechex( microtime(true) * 1000 ) . bin2hex( random_bytes(8) ),4));
}
function JoinObjects(
    string $ParentKey               = '',
    string $ChildrenKey             = '',
    $ParentObj    = null,
    $ChildrenObj  = null,
    bool $Count                     = false,
)
{
    $response = null;
    $cc = 0;
    
    // Se Verifica que el padre y el hijo no vengan vacios
    if(is_object($ParentObj) && is_object($ChildrenObj)):
       
        //Si vienen todos los datos se inicia el cruce de información
        foreach($ChildrenObj as $CObject){

            if($ParentObj->{$ParentKey} == $CObject->{$ChildrenKey}):
                if($Count == true){
                    $cc++;
                }else{
                    $response = $CObject;
                }
               

            endif;
            
        }        
    endif;

    if($Count == true){
        return $cc;
    }else{
        return $response;
    }

    
  

    

}

function removeEspecialCharacters(string $txt){

    return trim(str_replace([
        "'",
        '"',
        "/",
        "*",
        "-",
        "+",
        "`"
    ],"",$txt));

}


function __NumberMonthToName(int $N){

    switch($N){
        default:
        case 1:
            return "Enero";
        case 2:
            return "Febrero";
        case 3:
            return "Marzo";
        case 4:
            return "Abril";
        case 5:
            return "Mayo";
        case 6:
            return "Junio";
        case 7:
            return "Julio";
        case 8:
            return "Agosto";
        case 9:
            return "Septiembre";
        case 10:
            return "Octubre";
        case 11:
            return "Noviembre";
        case 12:
            return "Diciembre";
        
    }

}


 