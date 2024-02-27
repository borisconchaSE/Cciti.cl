<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\AnnotationHelper;
use Intouch\Framework\Annotation\Attributes\Template;
use Intouch\Framework\Annotation\Attributes\Widget;
use ReflectionClass;

class GenericWidget implements IDrawable {

    private string $__Content = '';
    private array  $__Contents = [];
    private string $__TemplatePath = '';
    private array $__Classes = [];
    private array $__Styles = [];
    private array $__Attributes = [];

    protected function __construct(
        public ?array $Replace = null,
        public ?array $Replaces = [],
    ) {

        // Obtener el template
        //
        $annotations = AnnotationHelper::FromObject($this);

        // Buscar en los atributos de la clase
        $widgetDef = $annotations->FindAttributeClass(Widget::class);
        
        if (!isset($widgetDef) || !isset($widgetDef->attribute)) {
            return;
        }
        
        $widget = $widgetDef->attribute;

        $tieneTemplates = isset($widget->Templates) && is_array($widget->Templates) && count($widget->Templates) > 0;
        $tieneTemplate  = isset($widget->Template) || $widget->Template != '';

        if (!$tieneTemplate && !$tieneTemplates) {
            return;
        }

        // Obtener la ruta donde se encuentra la clase "derivada" de este widget
        //
        $rc = new ReflectionClass(get_class($this));
        $ruta = dirname($rc->getFileName());

        // Componer la ruta hacia los archivos de template
        //
        if ($tieneTemplates) {
            foreach($widget->Templates as $template) {
                $this->__Contents[$template->Template] = $this->LeerTemplate($ruta, $template);
            }
        }
        else if ($tieneTemplate) {

            $this->__TemplatePath = $ruta . '/' . $widget->Path . '/' . $widget->Template . $widget->Extension;

            // Leer el template
            //
            $content = $this->LeerTemplate($ruta, new Template(
                Path: $widget->Path, Template: $widget->Template, Extension: $widget->Extension
            ));

            $this->__Content = $content;
        }

    }

    private function LeerTemplate (string $ClassPath, Template $template) {

        $filePath = $ClassPath . '/' . $template->Path . '/' . $template->Template . $template->Extension;

        if (!file_exists($filePath)) {
            throw new \Exception('No se encuentra el contenido del template: ' . $filePath);
        }

        return file_get_contents($filePath);
    }

    function SetReplace(array $Replace) {
        $this->Replace = $Replace;
    }

    function AddReplace(string $Search, string $Replacement) {
        $this->Replace = array_merge($this->Replace, [
            $Search  => $Replacement
        ]);
    }

    public function OnBeforeDraw() {

    }

    public function Draw($echoResult = true) {

        $this->OnBeforeDraw();

        if (!isset($this->__Content) || $this->__Content == '') {
            return '';
        }

        if (!isset($this->Replace) || count($this->Replace) <= 0) {
            return $this->__Content;
        }

        // Reemplazar los contenidos
        //
        $builder = $this->__Content;

        foreach($this->Replace as $key => $replace) {

            // Replace es una funcion??
            if (is_callable($replace)) {
                $callResult = $replace();

                if ($callResult instanceof IDrawable) {
                    $result = $callResult->Draw(false);
                }
                elseif (is_array($callResult)) {
                    $result = "";
                    foreach($callResult as $drawable) {
                        if ($drawable instanceof IDrawable) {
                            $result .= $drawable->Draw(false) . "\n\n";
                        }
                    }
                }
                else {
                    $result = $callResult;
                }
            }
            else if ($replace instanceof IDrawable) {
                $result = $replace->Draw(false);
            }
            else if (is_array($replace)) {
                $result = "";
                foreach($replace as $drawable) {                    
                    if ($drawable instanceof IDrawable) {
                        $result .= $drawable->Draw(false) . "\n\n";
                        }
                }
            }
            else {
                $result = $replace;
            }

            $builder = str_replace('[[' . strtoupper($key) . ']]', $result, $builder);
        }

        if ($echoResult) {
            echo $builder;
        }

        return $builder;
    }

    protected function AddStyle(string $style, string $value, string $group = 'default') {
        
        if (!isset($this->__Styles[$group])) {
            $this->__Styles[$group] = '';
        }

        if ($this->__Styles[$group] != '') {
            $this->__Styles[$group] .= ' ';
        }

        $this->__Styles[$group] .= $style . ': ' . $value . '; ';
    }

    protected function AddStyles(array $styles, string $group = 'default') {

        foreach($styles as $style) {
            if (is_array($style) && count($style) == 2) {
                $this->AddStyle($style[0], $style[1], $group);
            }
        }

    }

    protected function DrawStyles(string $group = 'default') {

        if (isset($this->__Styles[$group]) && $this->__Styles[$group] != '') {
            return 'style="' . $this->__Styles[$group] . '"';
        }
        else {
            return '';
        }
    }

    protected function AddClass(string $class, string $group = 'default') {

        if (!isset($this->__Classes[$group])) {
            $this->__Classes[$group] = '';
        }

        if ($this->__Classes[$group] != '') {
            $this->__Classes[$group] .= ' ';
        }

        $this->__Classes[$group] .= $class;
    }

    protected function AddClasses(array $classes, string $group = 'default') {
        foreach($classes as $class) {
            $this->AddClass($class, $group);
        }
    }

    protected function DrawClasses(string $group = 'default') {

        if (isset($this->__Classes[$group]) && $this->__Classes[$group] != '') {
            return 'class="' . $this->__Classes[$group] . '"';
        }
        else {
            return '';
        }
    }

    /**
     * AddAttribute
     * 
     * Agrega una propiedad del tipo <propiedad>
     * 
     * @param string $property El nombre de la propiedad (ej: disabled, required, selected, etc)
     * @param string $value    El valor del atributo, si es falso, el atributo NO SERA AGREGADO
     */
    protected function AddProperty(string $property, bool $value, string $group = 'default') {

        if ($value) {
            if (!isset($this->__Properties[$group])) {
                $this->__Properties[$group] = [];
            }
    
            $this->__Properties[$group][$property] = $value;
        }

    }

    /**
     * AddAttribute
     * 
     * Agrega un atributo del tipo <nombre>='<valor>', no se admiten valores NULL
     * 
     * @param string $attribute El nombre del atributo
     * @param string $value     El valor del atributo, si es NULL el atributo NO SERA AGREGADO
     */
    protected function AddAttribute(string $attribute, $value = null, string $group = 'default') {
        if (isset($value)) {
            if (!isset($this->__Attributes[$group])) {
                $this->__Attributes[$group] = [];
            }
    
            $this->__Attributes[$group][$attribute] = $value;
        }
    }

    protected function AddAttributes(array $attributes, string $group = 'default') {
        foreach($attributes as $key => $attribute) {
            if (is_array($attribute) && count($attribute) == 2) {
                $this->AddAttribute($attribute[0], $attribute[1], $group);
            }
            else if ($key != '' && !is_numeric($key)) {
                $this->AddAttribute($key, $attribute, $group);
            }
        }
    }

    protected function AddProperties(array $properties, string $group = 'default') {
        foreach($properties as $key => $property) {
            if (is_array($property) && count($property) == 2) {
                $this->AddProperty($property[0], $property[1], $group);
            }
            else if ($key != '' && !is_numeric($key)) {
                $this->AddProperty($key, $property, $group);
            }            
        }
    }

    protected function DrawAttributes(string $group = 'default') {

        $attributes = '';

        if (isset($this->__Attributes[$group])) {

            foreach($this->__Attributes[$group] as $attribute => $value) {
                if ($attributes != '') {
                    $attributes .= ' ';
                }

                $attributes .= $attribute . '="' . $value . '"';
            }
        }

        return $attributes;
    }

    protected function DrawProperties(string $group = 'default') {

        $properties = '';
        if (isset($this->__Properties[$group])) {

            foreach($this->__Properties[$group] as $properties => $value) {
                if ($properties != '') {
                    $properties .= ' ';
                }

                $properties .= $properties . ' ';
            }
        }

        return $properties;
    }
}