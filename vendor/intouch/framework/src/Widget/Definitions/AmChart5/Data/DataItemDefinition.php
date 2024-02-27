<?php

namespace Intouch\Framework\Widget\Definitions\AmChart5\Data;

class DataItemDefinition {

    public function __construct(
        public string   $FieldName,
        public string   $Value,
        public          $StrokeSettingsFunction = null, // Debe devolver una instancia de StrokeDefinition o NULL y se evalúa por cada elemento de la colección de $Data (entrega $element como parametro)
        public          $ColumnSettingsFunction = null, // Debe devolver una instancia de StrokeDefinition o NULL y se evalúa por cada elemento de la colección de $Data (entrega $element como parametro)
    ) {}

}