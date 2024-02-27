<?php

namespace Application\BLL\Document;

use Application\BLL\DataTransferObjects\Montaje\MonEvidenciaDto;
use Application\BLL\Document\Definitions\DocumentTypeEnum;
use Application\BLL\Document\Definitions\FotoDocumentDefinition;
use Intouch\Framework\Document\GenericDocument;
use Intouch\Framework\Document\GenericDocumentDefinition;

class FotoDocument extends GenericDocument {

    public function __construct(FotoDocumentDefinition $definition, $base64Image = null)
    {
        parent::__construct($definition, $base64Image);
    }

    public static function NewFromDto(MonEvidenciaDto $foto) {

        if (!isset($foto))
            return null;

        $definition = new FotoDocumentDefinition([
            "IdCliente"         => 0,
            "IdMonMontaje"      => $foto->IdMonMontaje,
            "DocumentType"      => DocumentTypeEnum::FOTO,
            "Filename"          => $foto->RutaArchivo
        ]);

        return new FotoDocument($definition);
    }

    public function GetFolder() {

        if ($this->DocumentDefinition instanceof FotoDocumentDefinition)
            return __DIR__ . '/../../../FileData/fotos/clientes/' . $this->DocumentDefinition->IdCliente . '/mantencion/' . $this->DocumentDefinition->IdMonMontaje;
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }

    }

    public function GetFilename()
    {

        if ($this->DocumentDefinition instanceof FotoDocumentDefinition)
            return $this->DocumentDefinition->Filename;
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }
        
    }

    public function GetUrl() {

        // Cambiamos a URL directa
        return '/assets/fotos/clientes/' 
            . $this->DocumentDefinition->IdCliente . '/mantencion/' 
            . $this->DocumentDefinition->IdMonMontaje . '/' 
            . $this->DocumentDefinition->Filename;

        if ($this->DocumentDefinition instanceof FotoDocumentDefinition)
            if ($this->DocumentDefinition->Authorization == 1) {
                return '/api/documentos?linkid=' . $this->GetEncodedLink();
            }
            else {
                return '/assets/documentos?linkid=' . $this->GetEncodedLink();
            }
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }

    }

    public function GetHttpUrl() {

        // Cambiamos a URL directa
        return '/assets/fotos/clientes/' 
            . $this->DocumentDefinition->IdCliente . '/mantencion/' 
            . $this->DocumentDefinition->IdMantencion . '/' 
            . $this->DocumentDefinition->Filename;

        if ($this->DocumentDefinition instanceof FotoDocumentDefinition)
            if ($this->DocumentDefinition->Authorization == 1) {
                return SITE_URL_HTTP . '/api/documentos?linkid=' . $this->GetEncodedLink();
            }
            else {
                return SITE_URL_HTTP . '/assets/documentos?linkid=' . $this->GetEncodedLink();
            }
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }

    }
    
}