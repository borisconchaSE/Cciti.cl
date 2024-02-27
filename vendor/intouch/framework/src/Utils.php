<?php

namespace Intouch\Framework;

class Utils
{
    public static $VEINTEMB = 20971520;

    public static function debugArray($array)
    {
        echo "<pre style='padding: 82px 230px !important'>";
        // echo "<pre>";
        print_r($array);
        echo "</pre>";
        die();
    }

    public static function formatRut($rut)
    {
        return number_format(substr($rut, 0, -1), 0, "", ".") . '-' . substr($rut, strlen($rut) - 1, 1);
    }

    public static function limpiarRut($rut)
    {
        $rut = str_replace('.', '', $rut);
        $rut = str_replace('-', '', $rut);
        return $rut;
    }

    public static function getTipoDocumento($dataApplicationStringBase64)
    {
        $array = explode('/', $dataApplicationStringBase64);
        $array = explode(';', $array[1]);
        return $array[0];
    }

    public static function validateEmpty($key)
    {
        return isset($key) && empty($key) === false ? trim($key) : '';
    }

    public static function validatePetitionGet($key)
    {
        return isset($_GET[$key]) && empty($_GET[$key]) === false ? strtolower(trim($_GET[$key])) : '';
    }

    public static function validatePetitionPost($key)
    {
        return isset($_POST[$key]) && empty($_POST[$key]) === false ? strtolower(trim($_POST[$key])) : '';
    }

    public static function formatDateTime($datetime)
    {
        $array = explode(" ", $datetime);
        return self::formatDate($array[0]);
    }

    public static function formatDate($date)
    {
        $array = explode("-", $date);
        $array = array_reverse($array);
        return join("-", $array);
    }

    public static function sanearVariables($var, $nosanear = false)
    {
        
        /* TIPOS DE VARIABLES DEVUELTOS POR gettype()
            "boolean"
            "integer"
            "double"
            "string"
            "array"
            "object"
            "resource"
            "NULL"
            "unknown type"
        */
        switch(gettype($var)) {
            case "string":

                //$var = strtr($var, "<>(){}", "      ");

                if (!$nosanear) {
                    $var = str_replace('<', '', $var);
                    $var = str_replace('>', '', $var);
                    $var = str_replace('(', '', $var);
                    $var = str_replace(')', '', $var);
                    $var = str_replace('{', '', $var);
                    $var = str_replace('}', '', $var);
                }
                break;
            case "NULL":
            case "unknown type":
                $var = null;
                break;
        }

        return $var;
    }

    public static function createDocument($base64)
    {
        $nombreArchivo = uniqid();
        $base_to_php = explode(',', $base64);
        $data = base64_decode($base_to_php[1]);
        $path = "/public/documentos/" . $nombreArchivo . '.' . Utils::getTipoDocumento($base_to_php[0]);
        $filepath = ".." . $path;
        if (file_put_contents($filepath, $data)) {
            return $path;
        }

        return false;
    }
}