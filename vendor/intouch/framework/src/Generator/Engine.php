<?php

namespace Intouch\Framework\Generator;

use Aws\Api\Validator;
use Intouch\Framework\Configuration\ConnectionConfig;
use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Dao\DataConfig;
use PDO;

class Engine {

    private $Dominio = null;

    public function __construct(
        public string $entityDomain,
        public string $entityNamespace,
        public string $entityTable
    )
    {
        $dominios = ConnectionConfig::Instance();

        if (isset($dominios[$entityDomain])) {
            $this->Dominio = $dominios[$entityDomain];
        }
    }

    public function Generate($overwrite = false) {

        $source = __DIR__;
        $dest   = __DIR__ . '/../../../../../';

        $error = false;

        error_reporting(0);

        echo "\n";
        echo "Generación de código iniciada\n";
        echo "-----------------------------\n";

        // Obtener la lista de tablas
        $tablas = explode(',', $this->entityTable);
        
 
        // Validación de conexión
        $stmt = $this->ValidateConnection();

 

        if (!isset($stmt) || $stmt == null) {
            return false;
        }

        // Procesar las tablas
        foreach($tablas as $tabla) {

            $table = trim($tabla);

            $schema = $this->GetSchema($table);
        
            $validated = $this->Validate(Table: $table, Overwrite: $overwrite, Stmt: $stmt, Dest: $dest);

            if (!$validated)
                continue;

            // Obtener los campos de la tabla
            //
            $fields = $this->GetTableFields(Table: $table, Conn: $stmt);

            // Convertir los campos
            //
            $fieldContent = $this->ConvertFields($fields);
            
            // Generar los archivos
            //
            $files = [
                "Dto" => [
                    "TemplatePath"      => $source . '/Templates/Dto.php',
                    "DestinationFolder" => $dest . 'Application/BLL/DataTransferObjects/' . $this->entityNamespace,
                    "Destination"       => $dest . 'Application/BLL/DataTransferObjects/' . $this->entityNamespace . '/' . $schema->Table . 'Dto.php',
                    "Fields"            => $fieldContent,
                    "Preserve"          => false
                ],
                "DtoT" => [
                    "TemplatePath"      => $source . '/Templates/DtoT.php',
                    "DestinationFolder" => $dest . 'Application/BLL/DataTransferObjects/' . $this->entityNamespace,
                    "Destination"       => $dest . 'Application/BLL/DataTransferObjects/' . $this->entityNamespace . '/' . $schema->Table . 'DtoT.php',
                    "Fields"            => $fieldContent,
                    "Preserve"          => true
                ],
                "Svc" => [
                    "TemplatePath"      => $source . '/Templates/Svc.php',
                    "DestinationFolder" => $dest . 'Application/BLL/Services/' . $this->entityNamespace,
                    "Destination"       => $dest . 'Application/BLL/Services/' . $this->entityNamespace . '/' . $schema->Table . 'Svc.php',
                    "Fields"            => $fieldContent,
                    "Preserve"          => false
                ],
                "SvcT" => [
                    "TemplatePath"      => $source . '/Templates/SvcT.php',
                    "DestinationFolder" => $dest . 'Application/BLL/Services/' . $this->entityNamespace,
                    "Destination"       => $dest . 'Application/BLL/Services/' . $this->entityNamespace . '/' . $schema->Table . 'SvcT.php',
                    "Fields"            => $fieldContent,
                    "Preserve"          => true
                ],
                "Ent" => [
                    "TemplatePath"      => $source . '/Templates/Ent.php',
                    "DestinationFolder" => $dest . 'Application/Dao/Entities/' . $this->entityNamespace,
                    "Destination"       => $dest . 'Application/Dao/Entities/' . $this->entityNamespace . '/' . $schema->Table . '.php',
                    "Fields"            => $fieldContent,
                    "Preserve"          => false
                ],
                "EntT" => [
                    "TemplatePath"      => $source . '/Templates/EntT.php',
                    "DestinationFolder" => $dest . 'Application/Dao/Entities/' . $this->entityNamespace,
                    "Destination"       => $dest . 'Application/Dao/Entities/' . $this->entityNamespace . '/' . $schema->Table . 'T.php',
                    "Fields"            => $fieldContent,
                    "Preserve"          => true
                ],
                "Dao" => [
                    "TemplatePath"      => $source . '/Templates/Dao.php',
                    "DestinationFolder" => $dest . 'Application/Dao/Services/' . $this->entityNamespace,
                    "Destination"       => $dest . 'Application/Dao/Services/' . $this->entityNamespace . '/' . $schema->Table . 'Dao.php',
                    "Fields"            => $fieldContent,
                    "Preserve"          => false
                ],
                "DaoT" => [
                    "TemplatePath"      => $source . '/Templates/DaoT.php',
                    "DestinationFolder" => $dest . 'Application/Dao/Services/' . $this->entityNamespace,
                    "Destination"       => $dest . 'Application/Dao/Services/' . $this->entityNamespace . '/' . $schema->Table . 'DaoT.php',
                    "Fields"            => $fieldContent,
                    "Preserve"          => true
                ]
            ];

            // Generar los archivos
            //
            foreach($files as $file) {
                $this->GenerateFile(File: $file, Table: $schema);
            }
        }

        echo "\n";
        echo "-----------------------------\n";

        if ($error) {
            echo "Generación de código finalizada con error\n";
        }
        else {
            echo "Generación de código finalizada OK\n";
        }

        echo "\n";
    }

    private function GenerateFile($File, object $Table) {
        
        $template       = $File['TemplatePath'];
        $folder         = $File['DestinationFolder'];
        $destination    = $File['Destination'];
        $fields         = $File['Fields'];
        $preserve       = $File['Preserve'];

        // Revisar si se debe preservar el archivo, y si este existe
        //
        if ($preserve) {
            if (file_exists($destination)) {
                return;
            }
        }

        // Obtener el contenido de la plantilla
        //
        $content = file_get_contents($template);

        // Reemplazar la metadata
        //
        // NAMESPACE
        $content = str_replace('<<NAMESPACE>>', $this->entityNamespace, $content);
        $content = str_replace('<<DOMAIN>>', $this->entityDomain, $content);
        $content = str_replace('<<ENTITY>>', $Table->Table, $content);
        $content = str_replace('<<FIELDS>>', $fields[0], $content);
        $content = str_replace('<<FULLFIELDS>>', $fields[1], $content);
        $content = str_replace('<<SCHEMA>>', $Table->Schema, $content);
        
        // Verificar si existe la carpeta de destino
        //
        if (!file_exists($folder)) {
            mkdir(
                $folder,
                0775,
                true
            );
        }

        // Escribir el archivo (preservar los traits)
        //
        file_put_contents($destination, $content);
    }

    private function ValidateConnection() {

        $error = false;

   

        echo $this->fillRight("Verificando conexión a la BD...", ' ', 55);
  
        // Intentar una conexión a la DB
        //
        $databaseOK = true;
        $stmt = null;
        
        try {
            echo "error";
            $stmt = DataConfig::GetPDOConnection($this->entityDomain);
       
            print_r($stmt);

            if (!isset($stmt))
                $databaseOK = false;
            else {
                echo "[OK]\n";
            }
        }
        catch (\Exception $e) {
            echo "error";
            print_r($e);
            $databaseOK = false;
        }
        
        if (!$databaseOK) {
            echo "[ERROR]\n";
            echo "La conexión a la BD ha fallado\n";
            $error = true;
        }

        echo "\n";

        if (!$error) {
            return $stmt;
        }
        else
            return null;
    }

    private function GetSchema(string $Table) {
        $Schema = '';
        $split = explode('.', $Table);

        if (is_array($split) && count($split) > 1) {
            $Schema = $split[0];
            $TableName  = $split[1];
        }
        else {
            $TableName = $Table;
        }
        
        return (object) [
            'Schema'        => $Schema,
            'Table'         => $TableName,
            'FullName'      => $Table
        ];
    }

    private function Validate(string $Table, bool $Overwrite, $Stmt, $Dest) {

        $schema = $this->GetSchema($Table);

        $error = false;
        echo $this->fillRight("TABLA: " . $schema->Table, ' ', 55);
        echo "\n";
        echo $this->fillRight("-", '-', 25);
        echo "\n";

        // PASO 1
        // Comprobar la existencia de la tabla
        //
        if (!$error) {
            
            echo $this->fillRight("Verificando existencia de la tabla...", ' ', 55);

            if (!$this->TableExists($Stmt, $schema->FullName)) {
                echo "[ERROR]\n";
                echo "La tabla [" . $Table . "] no existe en la base de datos asignada al dominio [" . $this->entityDomain . "]\n";
                $error = true;
            }
            else {
                echo "[OK]\n";
            }
        }

        // PASO 2
        // Definir los nombres de carpetas generar y revisar si ya existen
        //
        if (!$error) {
            
            echo $this->fillRight("Verificando carpetas de destino...", ' ', 55);

            // Definición de carpetas
            //
            $folders = array();
            $folders['Application'] = $Dest . 'Application';
            $folders['Application/BLL'] = $folders['Application'] . '/BLL';
            $folders['Application/BLL/DataTransferObjects'] = $folders['Application/BLL'] . '/DataTransferObjects';
            $folders['Application/BLL/Services'] = $folders['Application/BLL'] . '/Services';
            $folders['Application/Dao'] = $folders['Application'] . '/Dao';
            $folders['Application/Dao/Entities'] = $folders['Application'] . '/Dao/Entities';

            $notfound = "";
            foreach($folders as $nombre => $ruta) {

                if (!file_exists($ruta)) {
                    if ($notfound != '')
                        $notfound .= ', ';

                    $notfound .= $nombre;
                }
            }

            if ($notfound != '') {
                echo "[ERROR]\n";
                echo "Las siguientes carpetas no han sido encontradas:\n" . $notfound . "\n";
                $error = true;
            }
            else {
                echo "[OK]\n";
            }

        }

        // PASO 4
        // Revisar si existen los archivos en el destino
        //
        if (!$error && !$Overwrite) {
            
            echo $this->fillRight("Verificando clases de destino...", ' ', 55);

            // Definición de clases
            //
            $files = array();
            $files['Dto'] = $Dest . 'Application/BLL/DataTransferObjects/' . $this->entityNamespace . '/' . $schema->Table . 'Dto.php';
            $files['Svc'] = $Dest . 'Application/BLL/Services/' . $this->entityNamespace . '/' . $schema->Table . 'Svc.php';
            $files['Ent'] = $Dest . 'Application/Dao/Entities/' . $this->entityNamespace . '/' . $schema->Table . '.php';
            $files['Dao'] = $Dest . 'Application/Dao/' . $this->entityNamespace . '/' . $schema->Table . 'Dao.php';

            $found = "";
            foreach($files as $nombre => $ruta) {

                if (file_exists($ruta)) {
                    if ($found != '')
                        $found .= ', ';

                    $found .= $ruta;
                }
            }

            if ($found != '') {
                echo "[ERROR]\n";
                echo "Los siguientes archivos ya existen:\n" . $found . "\n";
                echo "Debe eliminar los archivos o especificar el parámetro 'overwrite' en la llamada\n";
                $error = true;
            }
            else {
                echo "[OK]\n";
            }

        }

        echo "\n";

        return !$error;
    }

    /**
    * Check if a table exists in the current database.
    *
    * @param PDO $pdo PDO instance connected to a database.
    * @param string $table Table to search for.
    * @return bool TRUE if table exists, FALSE if no table found.
    */
    private function TableExists($pdo, $table) {

        $qry = '';
        switch($this->Dominio->Type) {
            case 'mysql':
                $qry = "SELECT 1 FROM $table LIMIT 1";
                break;
            case 'sqlserver':
                $qry = "SELECT TOP 1 * FROM $table";
                break;
            default:
                $qry = "SELECT 1 FROM $table LIMIT 1";
                break;
        }
    
        // Try a select statement against the table
        // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
        try {
            $result = $pdo->query($qry);
        } catch (\Exception $e) {
            // We got an exception == table not found
            return false;
        }
    
        // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
        return $result !== FALSE;
    }

    private function fillRight($origin, $fillChar, $totalLength) {

            $currentLength = strlen($origin);
            $difference    = $totalLength - $currentLength;

            if ($difference == 0) {
                return $origin;
            }
            else if ($difference < 0) {
                return substr($origin, 0, $totalLength);
            }
            else {
                return $origin . str_repeat($fillChar, $difference);
            }
    }

    private function GetTableFields(string $Table, $Conn) {
        
        $schema = $this->GetSchema($Table);
        $fields = null;

        switch ($this->Dominio->Type) {
            case 'mysql':
                $fields = $this->GetMySqlFields(Table: $Table, Conn: $Conn);
                break;
            case 'sqlserver':
                $fields = $this->GetSqlServerFields(Table: $schema->FullName, Conn: $Conn);
                break;
            default:
                return [];
        }

        return $fields;
    }

    private function GetMySqlFields(string $Table, $Conn) {

        $qry = "
        DESCRIBE $Table
        ";

        $stmt = $Conn->prepare($qry);

        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute();
        $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $Fields = [];

        foreach($columns as $col) {

            // metadata
            $name = $col['Field'];
            $dbtype = $col['Type'];
            $nullable = ($col['Null'] == 'YES');
            $primaryKey = ($col['Key'] == 'PRI');
            $autoincrement = ($col['Extra'] == 'auto_increment');
            $uniqueKey  = ($col['Key'] == 'UNI');

            // tipo estandar
            // quitar todo lo que esta despues del parentesis
            $parentesis = strpos($dbtype, '(');

            if ($parentesis !== false) {
                $stdtype = substr($dbtype, 0, $parentesis);
            }
            else {
                $stdtype = $dbtype;
            }

            switch($stdtype) {
                case 'decimal':
                    $phptype = 'float';
                    $type = 'decimal';
                    break;                    
                case 'tinyint':
                case 'int':
                case 'bigint':
                    $phptype = 'int';
                    $type = 'int';
                    break;
                case 'varchar':
                case 'char':
                    $phptype = 'string';
                    $type = 'string';
                    break;
                case 'date':
                case 'datetime':
                    $phptype = 'string';
                    $type = 'datetime';
                    break;
                default:
                $phptype = 'string';
                    $type = 'string';
                    break;
            }

            $Fields[$name] = (object) [
                'Name'          => $name,
                'DataType'      => $type,
                'PhpDataType'   => $phptype,
                'PrimaryKey'    => $primaryKey,
                'UniqueKey'     => $uniqueKey,
                'Nullable'      => $nullable,
                'AutoIncrement' => $autoincrement,
            ];

        }

        return $Fields;

    }

    private function GetSqlServerFields(string $Table, $Conn) {

        // Verificar sio la tabla contiene el nombre del esquema
        $tableName = $Table;
        $schema    = '';

        if (strpos($Table, '.') !== false) {
            $parts = explode('.', $Table);
            $schema    = $parts[0];
            $tableName = $parts[1];
        }

        $qry = "EXEC sp_columns @table_name = N'" . $tableName . "'";

        if ($schema != '') {
            $qry .= ", @table_owner = N'" . $schema . "'";
        }

        $stmt = $Conn->prepare($qry);

        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute();
        $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $pkqry = "
        exec sp_primary_keys_rowset $tableName
        ";

        $stmt = $Conn->prepare($pkqry);

        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute();
        $primary = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (isset($primary)) {
            $fieldPK = $primary[0]['COLUMN_NAME'];
        }
        else {
            $fieldPK = '';
        }

        $Fields = [];

        foreach($columns as $col) {

            // metadata
            $name = $col['COLUMN_NAME'];
            $dbtype = trim(str_replace('identity', '', $col['TYPE_NAME']));
            $nullable = ($col['IS_NULLABLE'] == 'YES');
            $primaryKey = ($col['COLUMN_NAME'] == $fieldPK);
            $autoincrement = (strpos($col['TYPE_NAME'], 'identity') !== false);
            $uniqueKey  = false;

            // tipo estandar
            // quitar todo lo que esta despues del parentesis
            $parentesis = strpos($dbtype, '(');

            if ($parentesis !== false) {
                $stdtype = substr($dbtype, 0, $parentesis);
            }
            else {
                $stdtype = $dbtype;
            }

            switch($stdtype) {
                case 'decimal':
                    $phptype = 'float';
                    $type = 'decimal'; 
                    break;                    
                case 'tinyint':
                case 'int':
                case 'bigint':
                    $phptype = 'int';
                    $type = 'int';
                    break;
                case 'varchar':
                case 'nvarchar':
                case 'char':
                    $phptype = 'string';
                    $type = 'string';
                    break;
                case 'date':
                case 'datetime':
                    $phptype = 'string';
                    $type = 'datetime';
                    break;
                default:
                    $phptype = 'string';
                    $type = 'string';
                    break;
            }

            $Fields[$name] = (object) [
                'Name'          => $name,
                'DataType'      => $type,
                'PhpDataType'   => $phptype,
                'PhpDataType'   => $phptype,
                'PrimaryKey'    => $primaryKey,
                'UniqueKey'     => $uniqueKey,
                'Nullable'      => $nullable,
                'AutoIncrement' => $autoincrement,
            ];

        }

        return $Fields;
    }

    private function ConvertFields($fields) {

        $fieldContent = "";
        $fieldFullContent = "";

        foreach($fields as $field) {

            if ($fieldContent != "") {
                $fieldContent .= "\n";
            }
            if ($fieldFullContent != "") {
                $fieldFullContent .= "\n";
            }


            $annotation = "";
            if ($field->DataType == 'datetime') {
                
                $field->Nullable = true;

                if ($annotation != '')
                    $annotation .= ', ';
                $annotation .= "DataType: 'datetime'";
            } // PrimaryKey: true

            if ($field->PrimaryKey) {
                if ($annotation != '')
                    $annotation .= ', ';
                $annotation .= "PrimaryKey: true";
            }

            if ($annotation != "") {
                $fieldFullContent .= "\t#[EntityField(" . $annotation . ")]\n";
            }

            $dataType = $field->PhpDataType;

            if ($field->Nullable) {
                $dataType = '?' . $dataType;
            }

            $fieldContent .= "\t\tpublic $dataType $" . $field->Name;
            $fieldFullContent .= "\tpublic $dataType $" . $field->Name;

            if ($field->DataType == 'datetime' || $field->Nullable) {
                $fieldContent .= " = null,";
                $fieldFullContent .= " = null;";
            }
            else if ($field->DataType == 'string') {
                $fieldContent .= " = '',";
                $fieldFullContent .= " = '';";
            }
            else {
                $fieldContent .= " = 0,";
                $fieldFullContent .= " = 0;";
            }
        }

        $fieldContentLen = strlen($fieldContent);
        if (substr($fieldContent, $fieldContentLen-1, 1) == ',') {
            $fieldContent = substr($fieldContent, 0, $fieldContentLen-1);
        }

        return [$fieldContent, $fieldFullContent];

    }
}