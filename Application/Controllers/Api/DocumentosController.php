<?php

// CONTROLLER OBSOLETO - NO USAR

namespace Application\Controllers\Api;

use Application\BLL\Document\Definitions\DocumentTypeEnum;
use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Controllers\BaseController;
use Intouch\Framework\Document\GenericDocument;

class DocumentosController extends BaseController {

    public function anyIndex(String $linkid) {

        // Buscar el documento
        $document = GenericDocument::NewFromEncodedLink($linkid);

        switch($document->GetDocumentDefinition()->DocumentType) {

            case DocumentTypeEnum::FOTO:

                // Verificar si se ha especificado el id de cliente
                //
                $idCliente = $document->GetDocumentDefinition()->IdCliente;

                if (!isset($idCliente) || $idCliente != $document->GetDocumentDefinition()->IdCliente) {
                    exit;
                }

                // Obtener el archivo
                //
                $image = $document->LoadDocument();
                $this->DisplayImage($image);

                exit;
                break;

            default:
                exit;
                break;
        }

        if (isset($document)) {
        }
        else {
            return null;
        }
    }
}