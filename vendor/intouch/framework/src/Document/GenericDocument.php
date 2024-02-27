<?php

namespace Intouch\Framework\Document;

use Intouch\Framework\Security\Encoder;

abstract class GenericDocument {

    private static $DocumentTypeDictionary = array();

    protected $DocumentDefinition = null;
    protected $Data = null;

    private $DocumentClass = null;
    private $DocumentDefinitionClass = null;

    public function __construct($documentDefinition, $data = null)
    {
        $this->DocumentDefinition = $documentDefinition;
        $this->DocumentDefinitionClass = get_class($documentDefinition);
        $this->DocumentClass = get_class($this);

        $this->Data = $data;
    }

    /**
     * Obtiene el enlace codificado que contiene la información de definicion de este documento
     */
    public function GetEncodedLink() {

        $params = get_object_vars($this->DocumentDefinition);

        // Recorrer las propiedades y los valores
        $link = '';
        foreach($params as $prop => $value) {
            if ($link != '')             {
                $link .= ',';
            }

            $link .= $prop . "=" . trim($value);
        }

        return Encoder::Instance()->Encode($link);
    }

    /**
     * Agrega una clase al diccionario de tipos
     * Esto permite generar el tipo correcto desde el link codificado
     * 
     * @param string $documentClassname
     * @param string $documentDefinitionclassName
     * @param string $className
     */
    public static function AddToDictionary($documentType, $documentClassname, $documentDefinitionclassName) {
        self::$DocumentTypeDictionary[$documentType] = [$documentClassname, $documentDefinitionclassName];
    }

    /**
     * Retorna un objeto anónimo con los valores encontrados en el link
     * 
     * @param string $encodedLink
     * 
     * @return GenericDocument
     */
    public static function NewFromEncodedLink($encodedLink) {

        $link = Encoder::Instance()->Decode($encodedLink);
        
        // Crear un arreglo con los parámetros
        $paramList = array();
        $params = explode(',', $link);

        foreach($params as $keypar) {
            $par = explode('=', $keypar);

            if (isset($par[0]) && isset($par[1])) {
                $paramList[$par[0]] = $par[1] == '' ? null : $par[1];
            }
        }

        // Crear un nuevo objeto para la definición del documento
        //
        $documentDefinition = null;

        if (isset($paramList['DocumentType'])) {
            
            // Buscar el objeto en el diccionario
            if (isset(self::$DocumentTypeDictionary[$paramList['DocumentType']])) {
                
                $documentDeclaration = self::$DocumentTypeDictionary[$paramList['DocumentType']];
                
                $documentClassName = $documentDeclaration[0];
                $definitionClassName = $documentDeclaration[1];

                $documentDefinition = new $definitionClassName($paramList);

                if (isset($paramList['Filename'])) {
                    $documentDefinition->Filename = $paramList['Filename'];
                }

                $document = new $documentClassName($documentDefinition);
            }
        }
        else {
            throw new \Exception('El tipo de documento no ha sido definido');
        }

        return $document;
    }

    public static function GenerateRandomFilename(string $extension = null, $prefix = '', $longFilename = false) {

        $filename = uniqid($prefix, $longFilename);

        if (isset($extension)) {
            $filename .= '.' . $extension;
        }

        return $filename;
    }

    public function SetContent($data) {
        $this->Data = $data;
    }

    public function GetDocumentDefinition() {

        return $this->DocumentDefinition;

    }

    /**
     * Obtiene la carpeta donde se almacenará el documento, según lógica basada en el DocumentDefinition
     */
    abstract function GetFolder();

    /**
     * Obtiene el nombre del archivo
     */
    abstract function GetFilename();

    /**
     * Obtiene la ruta del archivo en formato URL
     */
    abstract function GetUrl();

    abstract function GetHttpUrl();


    public function GetPath() {

        // Obtener la carpeta de almacenamiento del archivo
        //
        $folderpath = $this->GetFolder();

        // Obtener el nombre del archivo
        //
        $filename = $this->GetFilename();

        // Generar la ruta del archivo
        //
        $filepath = $folderpath . '/' . $filename;

        return $filepath;
    }



    /**
     * Abre el archivo correspondiente al documento y retorna el contenido
     * 
     */
    public function LoadDocument($binary = true) {

        // Chequear que el archivo exista
        //
        $filepath = $this->GetPath();

        if (file_exists($filepath)) {
            $file = null;

            if ($binary)            
                $file = fopen($filepath, 'rb');
            else
                $file = fopen($filepath, 'r');

            if ($file) {
                $contents = fread($file, filesize($filepath));
                fclose($file);

                return $contents;
            }

            else {
                return null;
            }
            
        }
        else {
            return null;
        }
    }

    /**
     * Guarda el archivo correspondiente al documento en la ruta definida
     * 
     * @param bool $sobreescribir Sobrescribir el archivo si existe
     * @param bool $base64Image Guardar el archivo como imagen desde un origen en base64
     * 
     */
    public function SaveDocument($sobreescribir = false, $base64Image = false, $createFolders = true) {

        // Chequear que la data exista
        //
        if (!isset($this->Data) || $this->Data == '') {
            throw new \Exception('El documento no contiene información para guardar');
        }

        // Chequear que la carpeta de destino existe
        //
        $folderPath = $this->GetFolder($this->DocumentDefinition);

        if (!file_exists($folderPath)) {
            if ($createFolders) {
                mkdir($folderPath, 0775, true);
            }
        }

        // Chequear que el archivo NO exista
        //
        $filepath = $this->GetPath();

        if (file_exists($filepath) && !$sobreescribir) {
            throw new \Exception('El archivo ya existe');
        }
        else {
            if ($base64Image) {
                // Escribir la imagen
                $imageData = base64_decode($this->Data);
                $source = imagecreatefromstring($imageData);
                $rotate = imagerotate($source, 0, 0); // if want to rotate the image
                $imageSave = imagejpeg($rotate, $filepath, 100);
                
                imagedestroy($source);
            }
            else {
                file_put_contents($filepath, $this->Data);
            }
        }
    }

}