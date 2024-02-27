<?php

namespace Application\BLL\Helpers;

class FileHelper {

    public function GetTipoDocumento($dataApplicationStringBase64)
    {
        $array = explode('/', $dataApplicationStringBase64);
        $array = explode(';', $array[1]);
        return $array[0];
    }

    public function SaveFile($path, $filenameNoExt, $base64) 
    {
        $rootDir = $_SERVER["DOCUMENT_ROOT"];
        $archivoSinExtension = ($filenameNoExt == "") ? uniqid() : $filenameNoExt;

        // Obtener la información desde el base64
        //$bases = explode(',', $base64);
        // Obtener los datos de la imagen
        //$datosImagen = base64_decode($bases[1]);
        // Obtener la extension del archivo
        //$extension = self::GetTipoDocumento($bases[0]);

        // Obtener la ruta final del archivo        
        $rutaFinal = "documentos/" . (($path != "") ? $path . "/" : "") . $archivoSinExtension . ".jpg"; // . $extension;        
        $rutaEscritura = $rootDir . "/" . $rutaFinal;

        // Preparar la carpeta si esta no existe
        if (!file_exists("documentos/" . (($path != "") ? $path . "/" : ""))) {
            mkdir("documentos/" . (($path != "") ? $path . "/" : ""), 0777, true);
        }

        // Escribir la imagen
        $imageData = base64_decode($base64);
        $source = imagecreatefromstring($imageData);
        $rotate = imagerotate($source, 0, 0); // if want to rotate the image
        $imageSave = imagejpeg($rotate, $rutaEscritura, 100);
        
        imagedestroy($source);

        return $rutaFinal;
/*
        if  (file_put_contents($rutaEscritura, $datosImagen) !== false ) {
            return $rutaFinal;
        }
        else {
            return "";
        }
        */
    }
}