<?php

namespace Application\BLL\Document;

use Application\BLL\DataTransferObjects\Mantenciones\PlanDto;
use Application\BLL\DataTransferObjects\Mantenciones\MantencionDto;
use Application\BLL\Document\Definitions\DocumentTypeEnum;
use Application\BLL\Document\Definitions\PlanDocumentDefinition;
use Intouch\Framework\Document\GenericDocument;
use Intouch\Framework\Document\GenericDocumentDefinition;

class PlanDocument extends GenericDocument {

    public function __construct(PlanDocumentDefinition $definition, $base64Image = null)
    {
        parent::__construct($definition, $base64Image);
    }

    public static function NewsFromDto(MantencionDto $mantencion) {

        if (!isset($mantencion))
            return null;

        // Buscar todos los planes de esta mantencion
        $planes = array();

        foreach(glob(__DIR__ . '/../../FileData/plan/clientes/' . $mantencion->IdCliente . '/mantencion/' . $mantencion->IdMantencion . '/*_mant_*xml') as $file) {

            $fileinfo = pathinfo($file);

            $filename = $fileinfo['basename'];

            $year = substr($filename, 0, 4);
            $month = substr($filename, 4, 2);
            $day = substr($filename, 6, 2);
            
            $hour = substr($filename, 9, 2);
            $minute = substr($filename, 11,2);

            $name = "$year-$month-$day $hour:$minute Plan.xml";
            
            $definition = new PlanDocumentDefinition([
                "IdCliente"         => $mantencion->IdCliente,
                "IdMantencion"      => $mantencion->IdMantencion,
                "DocumentType"      => DocumentTypeEnum::PLAN,
                "Filename"          => $filename
            ]);

            array_push($planes, new PlanDocument($definition));
        }

        return $planes;
    }

    public function GetFolder() {

        if ($this->DocumentDefinition instanceof PlanDocumentDefinition)
            return __DIR__ . '/../../FileData/plan/clientes/' . $this->DocumentDefinition->IdCliente . '/mantencion/' . $this->DocumentDefinition->IdMantencion;
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }

    }

    public function GetFilename()
    {

        if ($this->DocumentDefinition instanceof PlanDocumentDefinition)
            return $this->DocumentDefinition->Filename;
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }
        
    }

    public function GetUrl() {

        if ($this->DocumentDefinition instanceof PlanDocumentDefinition)
            return '/assets/documentos?linkid=' . $this->GetEncodedLink();
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }

    }

    public function GetHttpUrl() {

        if ($this->DocumentDefinition instanceof PlanDocumentDefinition)
            return SITE_URL_HTTP . '/assets/documentos?linkid=' . $this->GetEncodedLink();
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }

    }
    
}