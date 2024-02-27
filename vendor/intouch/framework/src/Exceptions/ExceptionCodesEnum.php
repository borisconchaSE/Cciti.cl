<?php

namespace Intouch\Framework\Exceptions;

abstract class ExceptionCodesEnum {

    const ERR_NO_SESSION = -100;

    const ERR_DEFAULT = 1;
    const ERR_WITHVIEW = 2;

    const ERR_INVALID_PARAMETER = 200;
    const ERR_MISSING_PARAMETER = 201;
    const ERR_MISSING_FILES = 202;

    const ERR_DATA_INSERT = 300;
    const ERR_DATA_UPDATE = 301;
    const ERR_DATA_DELETE = 302;
    const ERR_DATA_READ = 303;

    const ERR_UNAUTHORIZED = 401;
    const ERR_PAYMENT_REQUIRED = 402;
    const ERR_FORBIDDEN = 403;
    const ERR_ROUTE_NOTFOUND = 404;
    const ERR_METHOD_NOTALLOWED = 405;

    const ERR_INTERNAL_SERVER = 500;
    
}