<?php

if ( function_exists ( 'stream_resolve_include_path' ) == false ) {
    function stream_resolve_include_path ( $filename ) {
        $paths = explode ( PATH_SEPARATOR, get_include_path () );
        foreach ( $paths as $path ) {
            $path = realpath ( $path . PATH_SEPARATOR . $filename );
            if ( $path ) {
                return $path;
            }
        }
        return false;
    }
}

spl_autoload_register ( function ( $className, $fileExtensions = null ) {
    $className = str_replace ( '_', '/', $className );
    $className = str_replace ( '\\', '/', $className );
    $file = stream_resolve_include_path ( SITE_ROOT . '/' . $className . '.php' );
    if ( $file === false ) {
        $file = stream_resolve_include_path ( strtolower ( $className . '.php' ) );
    }
    if ( $file !== false ) {
        include $file;
        return true;
    }
    return false;
});
?>