<?php
namespace Intouch\Framework\Widget\Definitions\AmChart5;

use Intouch\Framework\Widget\GenericWidget;

class Basic extends GenericWidget {

    public string $RootKey = "";
    public string $Key = "";

    public function __construct(
        string      $Key,
        array       $Replace
    ) {
        $this->Key = $Key;
        parent::__construct(Replace: $Replace);
    }

    public function BuildOptionList(array $Options, int $Tabs = 0, bool $UseBraces = false) {

        $resultTabs = "";
        if ($UseBraces) {
            $optionTabs = str_repeat("\t", $Tabs + 1);
            $resultTabs = str_repeat("\t", $Tabs);
        }
        else {
            $optionTabs = str_repeat("\t", $Tabs);
        }

        $result = "";
        foreach($Options as $option) {
            if ($option != '') {
                $option .= ",\n";
            }
            $result .= $optionTabs . $option;
        }

        if ($UseBraces) {
            $result .= $resultTabs . "{\n" . $result . "\n" . $resultTabs . "}\n";
        }

        return $result;
    }

    public function BuildOption(string $Option, $Value) : string {

        $optionValue = "";

        if (is_bool($Value))
            $optionValue = ($Value) ? "true" : "false";
        elseif (is_numeric($Value))
            $optionValue = $Value;
        else
            $optionValue = '"' . $Value . '"';

        return $Option . ": " . $optionValue;        
    }

    public function AddOption(array $OptionSet, string $Option, $Value) {

        if (isset($Value)) {
            $OptionSet[] = $this->BuildOption(Option: $Option, Value: $Value);
        }        
        
        return $OptionSet;
    }

    public function Draw($echoResult = true): string {

        $this->AddReplace(Search: 'ROOTKEY', Replacement: $this->RootKey);
        return parent::Draw($echoResult);

    }
}