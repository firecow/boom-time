<?
declare(strict_types=1);

/**
 * @param int $errtype
 * @param string $errstr
 * @param string $errfile
 * @param int $errlin
 * @throws ErrorException
 */
function errorHandler(int $errtype, string $errstr, string $errfile, int $errlin)
{
    throw new ErrorException($errstr, $errtype, 1, $errfile, $errlin);
}

function exceptionHandler(Throwable $exception)
{
    error_log("$exception");
}

function shutdownFunction() {
    $error = error_get_last();
    if ($error !== null) {
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr  = $error["message"];
        $errtype  = $error["type"];

        $exception = new ErrorException($errstr, $errtype, 1, $errfile, $errline);
        error_log("$exception");
    }
}

set_exception_handler('exceptionHandler');
set_error_handler('errorHandler');
register_shutdown_function('shutdownFunction');