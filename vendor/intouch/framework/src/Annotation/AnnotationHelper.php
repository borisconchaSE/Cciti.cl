<?php

namespace Intouch\Framework\Annotation;

use Intouch\Framework\Collection\GenericCollection;
use ReflectionParameter;

class AnnotationHelper {

    private ?AttributeContainer $__ClassAttributes;
    private array $__MethodAttributes = [];
    private array $__PropertyAttributes = [];

    private $__Reflected;
    
    function __get($name) {

        switch($name) {
            case "ClassAttributes": return $this->__ClassAttributes;
            case "MethodAttributes": return $this->__MethodAttributes;
            case "PropertyAttributes": return $this->__PropertyAttributes;
            case "Reflected": return $this->__Reflected;
            default:
                user_error("Invalid property: " . __CLASS__ . "->$name");
                return null;
        }

    }

    private function __construct(public string $fullClassname) {

        $this->__ClassAttributes = null;
        $this->__MethodAttributes = [];
        $this->__PropertyAttributes = [];
        
        $this->__Reflected = new \ReflectionClass($fullClassname);

        $this->LoadAttributes();
    }

    public static function FromClass($fullClassname) {
        return new AnnotationHelper(fullClassname: $fullClassname);
    }

    public static function FromObject($object) {
        $classname = $object::class;

        return new AnnotationHelper(fullClassname: $classname);
    }

    private function LoadAttributes() {

        $this->__ClassAttributes = null;
        $this->__MethodAttributes = [];
        $this->__PropertyAttributes = [];
        
        $reflector = $this->__Reflected; // new \ReflectionClass($this->fullClassname);

        // Atributos de clase
        //
        $classContainer = new AttributeContainer(type: ContainerTypeEnum::TYPE_CLASS, name: $reflector->getShortName());
        $classAttributes = $reflector->getAttributes();

        if (isset($classAttributes) && $classAttributes !== false && is_array($classAttributes) && count($classAttributes) > 0) {
            
            // Instanciar cada atributo de clase encontrado para agregarlo a la coleccion
            //
            foreach($classAttributes as $classAttribute) {
                $instancia = $classAttribute->newInstance();
                if (isset($instancia)) {
                    $classContainer->AddAttribute(name: $classAttribute->getName(), attribute: $instancia);
                }
            }
        }

        // Actualizar los atributos de clase actuales
        if (isset($classContainer))
            $this->__ClassAttributes = $classContainer;


        // Atributos de metodo
        //
        foreach ($reflector->getMethods() as $method) {

            if (isset($method) && isset($method->class) && $method->class == 'Framework\Widget\Definitions\AmChart5\Data\DataCollection' ) {
                $stop = 1;
            }

            $methodType = $method->getReturnType();

            $methodContainer = new AttributeContainer(
                type: ContainerTypeEnum::TYPE_METHOD, name: $method->getName(),
                returnType: isset($methodType) ? $methodType->getName() : ''
            );
            $methodAttributes = $method->getAttributes();

            if (isset($methodAttributes) && $methodAttributes !== false && is_array($methodAttributes) && count($methodAttributes) > 0) {
            
                // Instanciar cada atributo de clase encontrado para agregarlo a la coleccion
                //
                foreach($methodAttributes as $methodAttribute) {
                    $instancia = $methodAttribute->newInstance();
                    if (isset($instancia)) {
                        $methodContainer->AddAttribute(name: $methodAttribute->getName(), attribute: $instancia);
                    }
                }
            }

            // Agregar los parametros del metodo al contenedor
            //
            $methodParameters = $method->getParameters();

            foreach($methodParameters as $methodParameter) {
                $methodContainer->AddParameter(
                    $methodParameter->getName(), 
                    new MethodParameter(
                        name: $methodParameter->getName(),
                        type: ($methodParameter->hasType()) ? $methodParameter->getType()->getName() : "",
                        allowsNull: $methodParameter->allowsNull(),
                        defaultValue: ($methodParameter->isDefaultValueAvailable()) ? $methodParameter->getDefaultValue() : null,
                        position: $methodParameter->getPosition(),
                        isOptional: $methodParameter->isOptional()
                    )
                );
            }

            // agregar el contenedor al listado, con el nombre del metodo
            if (isset($methodContainer))
                $this->__MethodAttributes[$method->getName()] = $methodContainer;
        }

        // Atributos de propiedades
        //
        foreach ($reflector->getProperties() as $property) {

            $propertyType = $property->getType();

            $propertyContainer = new AttributeContainer(
                type: ContainerTypeEnum::TYPE_PROPERTY, name: $property->getName(),
                dataType: isset($propertyType) ? $propertyType->getName() : ''
            );
            $propertyAttributes = $property->getAttributes();

            if (isset($propertyAttributes) && $propertyAttributes !== false && is_array($propertyAttributes) && count($propertyAttributes) > 0) {
            
                // Instanciar cada atributo de clase encontrado para agregarlo a la coleccion
                //
                foreach($propertyAttributes as $propertyAttribute) {
                    $instancia = $propertyAttribute->newInstance();
                    if (isset($instancia)) {
                        $propertyContainer->AddAttribute(name: $propertyAttribute->getName(), attribute: $instancia);
                    }
                }
            }

            // agregar el contenedor al listado, con el nombre del metodo
            if (isset($propertyContainer))
                $this->__PropertyAttributes[$property->getName()] = $propertyContainer;
        }
    }

    /**
     * Obtiene una colección con todas las propiedades que tienen el atributo especificado
     * 
     * @param string $fullAtrributeClassname El nombre de clase del atributo buscado
     * 
     * @return GenericCollection
     */
    public function GetAttributeProperties(string $fullAtrributeClassname) {

        $results = array();

        // Verificar si existen propiedades
        //
        if (isset($this->__PropertyAttributes) || count($this->__PropertyAttributes) > 0) {
            
            // Recorrer las propiedades y recopilar aquellas que tengan el atributo buscado
            //
            foreach($this->__PropertyAttributes as $propertyName => $attributeContainer) {            
                foreach($attributeContainer->Attributes as $attributeName => $attribute) {
                    if ($attributeName == $fullAtrributeClassname) {
                        array_push($results, new PropertyAttribute(propertyName: $propertyName, attribute: $attribute));
                        break;
                    }
                }
            }

        }

        if (count($results) > 0)
            return new GenericCollection(
                DtoName: PropertyAttribute::class,
                Key: 'propertyName',
                Values: $results
            );
        else
            return null;
    }

    /**
     * Obtiene la propiedad que contenga el atributo especificado. Si hay más de una, devuelve la primera encontrada
     * 
     * @param string $fullAtrributeClassname El nombre de clase del atributo buscado
     * 
     * @return Collection
     */
    public function FindAttributeProperty(string $fullAttributeClassname) {

        $attributes = $this->GetAttributeProperties($fullAttributeClassname);

        if (isset($attributes) && $attributes->Count() > 0) {
            return $attributes->First();
        }

        return null;
    }

     /**
     * Obtiene una colección con todos los metodos que tienen el atributo especificado
     * 
     * @param string $fullAtrributeClassname El nombre de clase del atributo buscado
     * 
     * @return Collection
     */
    public function GetAttributeMethods(string $fullAttributeClassname) {

        $results = array();

        // Verificar si existen metodos
        //
        if (isset($this->__MethodAttributes) || count($this->__MethodAttributes) > 0) {
            
            // Recorrer los metodos y recopilar aquellos que tengan el atributo buscado
            //
            foreach($this->__MethodAttributes as $methodName => $attributeContainer) {            
                foreach($attributeContainer->Attributes as $attributeName => $attribute) {
                    if ($attributeName == $fullAttributeClassname) {
                        array_push($results, new MethodAttribute(methodName: $methodName, methodParameters: $attributeContainer->Parameters, attribute: $attribute));
                        break;
                    }
                }
            }

        }

        if (count($results) > 0)
            return new GenericCollection(
                Key: 'methodName',
                DtoName: MethodAttribute::class,
                Values: $results
            );
        else
            return null;
    }


    public function GetMethodAttributes(MethodAttribute $method) : AttributeContainer {

        if (isset($this->__MethodAttributes[$method->methodName])) {
            return $this->__MethodAttributes[$method->methodName];
        }
        else {
            return null;
        }
    }

     /**
     * Obtiene el metodo que contenga el atributo especificado. Si hay más de uno, devuelve el primero encontrado
     * 
     * @param string $fullAtrributeClassname El nombre de clase del atributo buscado
     * 
     * @return Collection
     */
    public function FindAttributeMethod(string $fullAttributeClassname) {

        $attributes = $this->GetAttributeMethods($fullAttributeClassname);

        if (isset($attributes) && $attributes->Count() > 0) {
            return $attributes->First();
        }

        return null;
    }

    public function MethodHasAttribute(string $method, string $fullAttributeClassname): bool {

        $results = array();

        // Verificar si existen metodos
        //
        if (isset($this->__MethodAttributes) || count($this->__MethodAttributes) > 0) {
            
            // Recorrer los metodos y encontrar el metodo buscado, y verificar si tiene el atributo
            //
            foreach($this->__MethodAttributes as $methodName => $attributeContainer) {

                if (strtolower($methodName) == strtolower($method)) {
                    foreach($attributeContainer->Attributes as $attributeName => $attribute) {
                        if ($attributeName == $fullAttributeClassname) {
                            return true;
                        }
                    }
                }
            }

        }

        return false;
        
    }

    public function FindAttributeClass($fullAttributeClassname) {

        $results = array();

        // Verificar si existen attributos de clase
        //
        if (isset($this->__ClassAttributes->Attributes) || count($this->__ClassAttributes->Attributes) > 0) {
            
            // Recorrer los atributos de la clase y verificar si existe el atributo buscado
            //
            foreach($this->__ClassAttributes->Attributes as $attributeName => $attribute) {                            
                if ($attributeName == $fullAttributeClassname) {
                    array_push($results, new ClassAttribute(className: $this->fullClassname, attribute: $attribute));
                    break;
                }
            }

        }

        if (count($results) > 0)
            return $results[0];
        else
            return null;
    }

    public function Exists(string $property, string $attribute) {

        if (isset($this->__PropertyAttributes[$property]) && $this->__PropertyAttributes[$property]->HasAttribute($attribute)) {
            return true;
        }
        else {
            return false;
        }
    }

}