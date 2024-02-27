<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Mapper\Mapper;

#[Widget(Template: 'InputSelect')]
class InputSelectMultiple extends Input {    

    public function __construct(
        public string $Key,        
        public string $ValueField,
        public string $DescriptionField,
        public array  $Values,
        public        $DescriptionFunction = null,
        public string $SelectedValue = '',
        public ?bool  $Required = false,
        public ?bool  $Disabled = false,
        public array  $Classes = [],
        public array  $Styles = [],
        public array  $Attributes = [],
        public array  $Properties = [],
        public array  $OptionAttributeNames = [],
    )
    {
        // Classes
        $this->AddClasses($Classes);
        $this->AddClass('js-source-states');

        // Styles
        $this->AddStyles($Styles);
        $this->AddStyle('width', '100%');
        
        // Attributes
        $this->AddAttributes($Attributes);
        $this->AddAttribute('id', $Key);

        // Properties
        $this->AddProperties($Properties);
        $this->AddProperty('required', $Required);
        $this->AddProperty('disabled', $Disabled);

        $IdSpan         =   $Key ."-span";
        $AttributeSpan  =   'id="'.$IdSpan.'"';

        

        // Generar las opciones
        $options = '';
        foreach($Values as $row) {

            // atributos del option
            $attr = [];

            foreach($OptionAttributeNames as $optionattr) {
                $attrValue = Mapper::EvaluateKeyMultilevel($row, $optionattr);
                //array_push($attr, ['data-' . $optionattr, $row->$optionattr]);
                array_push($attr, ['data-' . str_replace('->', '-', $optionattr), $attrValue]);
            }

            $PropertyNameValue = ( is_string($ValueField) && strpos($ValueField, "[]") !== false ) ? str_replace('[]','',$ValueField) : $ValueField ;

            $value =$row->$PropertyNameValue;

            if (isset($DescriptionFunction) && is_callable($DescriptionFunction)) {
                $description = $DescriptionFunction($row->$ValueField, $row->$DescriptionField, $row);
            }
            else {
                $description = $row->$DescriptionField;
            }
            
            $selected = ($value == $SelectedValue);

            $option = new InputSelectOption(
                Value: $value,
                Description: $description,
                Selected: $selected,
                Attributes: $attr
            );

            if ($options == '') {
                $options .= "\n";
            }
            $options .= $option->Draw(false);            
        }

        parent::__construct(Replace: [
            'OPTIONS'           => $options,
            'CLASSES'           => $this->DrawClasses(),
            'STYLES'            => $this->DrawStyles(),
            'ATTRIBUTES'        => $this->DrawAttributes(),
            'PROPERTIES'        => $this->DrawProperties(),
            'ATTRIBUTES_SPAN'   => $AttributeSpan
        ]);
    }
}