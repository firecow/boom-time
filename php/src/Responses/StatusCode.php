<?
declare(strict_types=1);

namespace App\Responses;

class StatusCode
{
    // Script executed as intended, and everything was ok.
    const OK = "200 Ok";

    // Input was malformed or missing.
    const BAD_REQUEST = "400 Bad Request";

    // Resource was not found. User, game or challenge wasn't found, or file was not found.
    const NOT_FOUND = "404 Not Found";

    // Username, id or other resource was already taken.
    const CONFLICT = "409 Conflict";

    // Trying to access without specifying access token.
    const UNAUTHORIZED = "401 Unauthorized";

    // Input was wellformed and present, but input data could not be used to fullfill the use case.
    const UNPROCESSABLE_ENTITY = "422 Unprocessable Entity";

    // An unhandled exception occured.
    const INTERNAL_SERVER_ERROR = "500 Internal Server Error";
}