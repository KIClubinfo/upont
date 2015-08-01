<?php

namespace KI\PonthubBundle\Service\Gracenote;

// A simple class to encapsulate errors that can be returned by the API.
class GNError
{
    const UNABLE_TO_PARSE_RESPONSE = 1; // The response couldn't be parsed. Maybe an error, or maybe the API changed.

    const API_RESPONSE_ERROR       = 1000; // There was a GN error code returned in the response.
    const API_NO_MATCH             = 1001; // The API returned a NO_MATCH (i.e. there were no results).
    const API_NON_OK_RESPONSE      = 1002; // There was some unanticipated non-'OK' response from the API.

    const HTTP_REQUEST_ERROR       = 2000; // An uncaught exception was raised while doing a cURL request.
    const HTTP_REQUEST_TIMEOUT     = 2001; // The external request timed out.
    const HTTP_RESPONSE_ERROR_CODE = 2002; // There was a HTTP400 error code returned.
    const HTTP_RESPONSE_ERROR      = 2003; // A cURL error that wasn't a timeout or HTTP400 response.

    const INVALID_INPUT_SPECIFIED  = 3000; // Some input the user gave wasn't valid.

    // The human readable error messages
    static public $_MESSAGES = array(
        // Generic Errors
        1    => 'Unable to parse response from Gracenote WebAPI.'

        // Specific API Errors
        ,1000 => 'The API returned an error code.'
        ,1001 => 'The API returned no results.'
        ,1002 => 'The API returned an unacceptable response.'

        // HTTP Errors
        ,2000 => 'There was an error while performing an external request.'
        ,2001 => 'Request to a Gracenote WebAPI timed out.'
        ,2002 => 'WebAPI response had a HTTP error code.'
        ,2003 => 'cURL returned an error when trying to make the request.'

        // Input Errors
        ,3000 => 'Invalid input.'
    );

    public static function getMessage($code) { return self::$_MESSAGES[$code]; }
}
