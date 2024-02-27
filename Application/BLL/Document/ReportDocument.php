<?php

namespace Application\BLL\Document;

use Application\BLL\Document\Definitions\DocumentTypeEnum;
use Application\BLL\Document\Definitions\ReportDocumentDefinition;
use Application\BLL\Document\Definitions\ReportTypeEnum;
use Intouch\Framework\Document\GenericDocument;
use Intouch\Framework\Document\GenericDocumentDefinition;

class ReportDocument extends GenericDocument {

    public function __construct(ReportDocumentDefinition $definition)
    {
        parent::__construct($definition);
    }    

    public function GetFolder() {

        if ($this->DocumentDefinition instanceof ReportDocumentDefinition)
            return __DIR__ . '/../../FileData/reportes/clientes/' . $this->DocumentDefinition->IdCliente;
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }

    }

    public function GetFilename()
    {

        if ($this->DocumentDefinition instanceof ReportDocumentDefinition)
            return $this->DocumentDefinition->Filename;
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }
        
    }

    public function GetUrl() {

        if ($this->DocumentDefinition instanceof ReportDocumentDefinition)
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

        if ($this->DocumentDefinition instanceof ReportDocumentDefinition)
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