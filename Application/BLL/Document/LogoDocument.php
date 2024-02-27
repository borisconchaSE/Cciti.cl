<?php

namespace Application\BLL\Document;

use Application\BLL\DataTransferObjects\Core\ClienteDto;
use Application\BLL\Document\Definitions\LogoDocumentDefinition;
use Application\BLL\Document\Definitions\DocumentTypeEnum;
use Intouch\Framework\Document\GenericDocument;

class LogoDocument extends GenericDocument {

    public function __construct(LogoDocumentDefinition $definition, $base64Image = null)
    {
        parent::__construct($definition, $base64Image);
    }

    /**
     * Obtiene un LogoDocument desde la información del cliente
     * 
     * @param ClienteDto $cliente
     * 
     * @return null|LogoDocument
     */
    public static function NewFromCliente(ClienteDto $cliente) {

        if (!isset($cliente))
            return null;

        $definition = new LogoDocumentDefinition([
            "IdCliente"      => $cliente->IdCliente,
            "DocumentType"   => DocumentTypeEnum::LOGO,
            "Filename"       => isset($cliente->UriLogo) ? $cliente->UriLogo : ''
        ]);

        return new LogoDocument($definition);
    }

    public function GetFolder() {

        if ($this->DocumentDefinition instanceof LogoDocumentDefinition)
            return __DIR__ . '/../../FileData/logo/clientes/' . $this->DocumentDefinition->IdCliente;
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }

    }

    public function GetFilename()
    {
        if ($this->DocumentDefinition instanceof LogoDocumentDefinition)
            return $this->DocumentDefinition->Filename;
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }        
    }

    public function GetUrl() {

        // Cambiamos a URL directa
        return '/assets/logo/clientes/' 
            . $this->DocumentDefinition->IdCliente . '/'
            . $this->DocumentDefinition->Filename;

        if ($this->DocumentDefinition instanceof LogoDocumentDefinition)
            return '/assets/documentos?linkid=' . $this->GetEncodedLink();
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }

    }

    public function GetHttpUrl() {

        // Cambiamos a URL directa
        return '/assets/logo/clientes/' 
            . $this->DocumentDefinition->IdCliente . '/'
            . $this->DocumentDefinition->Filename;

        if ($this->DocumentDefinition instanceof LogoDocumentDefinition)
            return SITE_URL_HTTP . '/assets/documentos?linkid=' . $this->GetEncodedLink();
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }

    }
    
}