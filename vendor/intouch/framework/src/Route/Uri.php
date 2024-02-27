<?php

namespace Intouch\Framework\Route;

use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Controllers\ControllerDefinition;
use Karriere\JsonDecoder\JsonDecoder;

class Uri {

    // Las variables por posicion, el controller y el action,
    // se definiran una vez que se haya asociado un controlador
    // a este request
    public $Controller = '';
    public $Action = '';
    public $PositionVariables = array();

    // El RequestURI, una vez eliminado el query string, queda limpio
    public $CleanRequest = '';

    public $Variables = array();
    public $BodyContent =  '';
    public $Body = null;

    protected function __construct(
        public string $ServerScheme,
        public string $ServerRequestUri,        
        public string $ServerMethod,
        public string $ServerContentType,
        public int $ServerContentLength,
        public string $ServerQueryString,
    ) {
        $requestParts = explode('?', $ServerRequestUri);

        $request = $requestParts[0];

        if (substr($request, -1, 1) == '/') {
            $request = substr($request, 0, strlen($request)-1);
        }

        $this->CleanRequest = $request;
    }

    /**
     * Parse: obtiene toda la información de la petición HTTP actual y devuelve
     *        un objeto que contiene el QueryString como un arreglo asociativo, el Body del 
     *        post como un objeto, y el nombre del controlador y de la accion respectiva
     *        relativo al URI (no los nombre de clases ni los namespaces internos)
     * 
     * @return null|Uri
     */
    public static function Parse() : ?Uri {

        $uri = null;

        // Obtener el URI original
        if (isset($_SERVER['REQUEST_URI'])) {

            // Scheme (http, https)
            $scheme = _nularr($_SERVER, 'REQUEST_SCHEME', '');

            // Request
            $requestUri = _nularr($_SERVER, 'REQUEST_URI', '');

            // Method
            $method = _nularr($_SERVER, 'REQUEST_METHOD', '');

            // Content Type
            $contentType = _nularr($_SERVER, 'CONTENT_TYPE', '');

            // Content Lenght
            $contentLength = _nularr($_SERVER, 'CONTENT_LENGTH', 0);

            // Querystring
            $queryString = _nularr($_SERVER, 'QUERY_STRING', '');

            // Instanciar el resultado
            $uri = new Uri(
                ServerScheme: $scheme,
                ServerRequestUri: $requestUri,
                ServerMethod: $method,
                ServerContentType: $contentType,
                ServerContentLength: $contentLength,
                ServerQueryString: $queryString
            );

            // Obtener la información de controller, action y variables por posicion (controller/action/V1/V2/V3/.../Vn)
            // if ($requestUri != '') {
            //     $uri->ParseRequestUri();
            // }

            // Obtener las variables del query string
            if ($queryString != '') {
                $uri->ParseQueryString();
            }

            if (isset($contentType) && isset($contentLength) && $contentLength > 0) {
                $uri->ParseBody();
            }

        }

        return $uri;
    }

    // protected function ParseRequestUri() {

    //     $secciones = explode('?', $this->ServerRequestUri);

    //     if (isset($secciones[0])) {
    //         $request = $secciones[0];

    //         // eliminar el / inicial
    //         if (substr($request, 0, 1) == '/') {
    //             $request = substr($request, 1);
    //         }

    //         // Particionar la llamada
    //         $parts = explode('/', $request);

    //         // Ver si existe controlador
    //         $this->Controller = _nularr($parts, 0, '');

    //         // Ver si existe action
    //         $this->Action = _nularr($parts, 1, '');

    //         // Buscar variables por posicion
    //         $count = count($parts);
    //         if ($count > 2) {
    //             $vars = array();
    //             for ($idx = 2; $idx < $count; $idx++) {
    //                 array_push($vars, $parts[$idx]);
    //             }

    //             $this->PositionVariables = $vars;                
    //         }
    //     }
    // }

    protected function ParseQueryString() {
        $variables = explode('&', $this->ServerQueryString);

        $values = array();
        foreach($variables as $value) {

            $par = explode('=', $value);

            if (isset($par) && is_array($par) && count($par) == 2) {
                $values[$par[0]] = $par[1];
            }
        }

        $this->Variables = $values;
    }

    protected function ParseBody() {

        if (isset($_POST) && count($_POST) > 0) {
                        
            $body = array();

            foreach($_POST as $name => $post) {

                $jsonAttempt = json_decode($post);

                if (json_last_error() != JSON_ERROR_NONE) {
                    $jsonAttempt = $post;
                }

                $body[$name] = $jsonAttempt;
            }
            $this->Body = $body;
        }
        else {
        // if (isset($_REQUEST) && count($_REQUEST) > 0) {
        //     $body = new \stdClass;          
        //     foreach ($_REQUEST as $name => $request) {
        //         $body->$name = json_decode($request);
        //     }
        //     $this->Body = $body;
        // }
        // else {
            $this->BodyContent = $this->file_get_contents_utf8('php://input');
            $this->Body = [0 => json_decode($this->BodyContent)];
        // }
        }

    }

    protected function file_get_contents_utf8($fn) {
        $content = file_get_contents($fn);
         return mb_convert_encoding($content, 'UTF-8',
             mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
   }

    /**
     * Encuentra la mejor ruta que haga match entre los controladores definidos
     * 
     * @param array $route La definicion de ruta analizada
     * 
     * @return null|Route
     */
    public function FindMatch(GenericCollection $controllers) : null|Route {

        $matchs = array();

        // Eliminar el query string del URI

        foreach($controllers as $controller) {
            if (isset($controller->ControllerMethodRoutes)) {
                foreach($controller->ControllerMethodRoutes as $path => $route) {
                    if ($this->Match($this->CleanRequest, $route->FullPath)) {
                        $status = 1;
                        array_push($matchs,[$controller, $route]);
                    }
                }
            }
        }

        if (count($matchs) > 1) {
            user_error("Se ha detectado mas de una ruta coincidente con el URI [$this->ServerRequestUri]: " . __CLASS__ . "->FindMatch");
        }
        else if (count($matchs) == 1) {

            $controller = $matchs[0][0];
            $route = $matchs[0][1];
            
            // Setear el controlador y el metodo
            $this->Controller = $controller;
            $this->Action = $route;

            // Setear variable por posicion
            // Ahora que se tiene identificado un controlador y método
            // se puede concluir cuáles son las variables por posicion
            //
            // Ej:  /site/core/login/obtenerusuarios/4/18/?id=50&nombre=hola
            //                                      |    |
            //                                        ^ éstas son
            $this->SetPositionVars($controller, $route);

            return $route;
        }
        else {
            return null;
        }
        
    }

    /**
     * Identifica si el path analizado coincide con el request
     * 
     * @param string $request El URI enviado en el request
     * @param string $path La ruta encontrada a analizar
     * 
     * @return bool
     */
    private function Match(string $request, string $path) : bool {

        // Caso especial, request del sitio principal
        $pathR = strtolower(str_replace('/', '', str_replace('//', '', $path)));
        if ($request == '' && ($pathR == 'index' || $pathR == '')) {
            return true;
        }

        if ($request == '')
            return false;

        // quitar el querystring
        $parts = explode('?', $request);

        if (isset($parts[0])) {
            $requestOnly = $parts[0];
        }
        else {
            $requestOnly = $request;
        }

        return str_starts_with( strtolower($request . '/'), strtolower($path . '/'));
        //return str_starts_with( strtolower($path), strtolower($requestOnly));
        
    }

    private function SetPositionVars(ControllerDefinition $controller, Route $route) {

        $positionVars = array();

        // Setear las variables por posicion
        $parts = explode('?', $this->ServerRequestUri);

        // Debemos ignorar todo el texto despues del "?" (el querystring)
        if (isset($parts[0])) {
            $request = $parts[0];

            $variables = str_replace($route->FullPath, '', $request);

            if (isset($variables) && $variables != '') {
                $vars = explode('/', $variables);

                foreach($vars as $var) {
                    if ($var != '') {
                        array_push($positionVars, $var);
                    }
                }
            }
        }

        if (isset($positionVars) && count($positionVars) > 0) {
            $this->PositionVariables = $positionVars;
        }

    }
}