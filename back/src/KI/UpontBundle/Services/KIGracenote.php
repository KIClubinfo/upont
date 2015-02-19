<?php

namespace KI\UpontBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;

// Échange des informations avec l'API Gracenote pour récupérer des informations
// sur la musique (utilisé par Ponthub)
// Testé par PonthubControllerTest
class KIGracenote extends ContainerAware
{
    public function searchAlbum($name, $artistHint = '')
    {
        $api = new GracenoteWebAPI(
            $this->container->getParameter('upont_gracenote_key1'),
            $this->container->getParameter('upont_gracenote_key2'),
            $this->container->getParameter('upont_gracenote_key3'),
            $this->container->getParameter('proxy_url'),
            $this->container->getParameter('proxy_user')
        );

        $response = $api->searchAlbum($artistHint, $name, GracenoteWebAPI::BEST_MATCH_ONLY);

        // On garde le premier résultat, c'est le plus pertinent
        if (count($response) > 0) {
            return array(
                'name' => $response[0]['album_title'],
                'artist' => $response[0]['album_artist_name'],
                'year' => $response[0]['album_year'],
                'image' => preg_replace('#\?.*$#Ui', '', $response[0]['album_art_url']),
            );
        }

        return null;
    }
}










//===== Gracenote PHP API: https://github.com/richadams/php-gracenote =====//
// Code modifié en tenant compte des paramètres proxy

// Defaults
if(!defined("GN_DEBUG")) { define("GN_DEBUG", false); }

class GracenoteWebAPI
{
    // Constants
    const BEST_MATCH_ONLY = 0; // Will put API into "SINGLE_BEST" mode.
    const ALL_RESULTS     = 1;

    // Members
    private $_clientID  = null;
    private $_clientTag = null;
    private $_userID    = null;
    private $_proxyUrl  = null;
    private $_proxyUser = null;
    private $_apiURL    = "https://[[CLID]].web.cddbp.net/webapi/xml/1.0/";

    // Constructor
    public function __construct($clientID, $clientTag, $userID = null, $proxyUrl, $proxyUser)
    {
        // Sanity checks
        if ($clientID === null || $clientID == "")   { throw new GNException(GNError::INVALID_INPUT_SPECIFIED, "clientID"); }
        if ($clientTag === null || $clientTag == "") { throw new GNException(GNError::INVALID_INPUT_SPECIFIED, "clientTag"); }

        $this->_clientID  = $clientID;
        $this->_clientTag = $clientTag;
        $this->_userID    = $userID;
        $this->_proxyUrl  = $proxyUrl;
        $this->_proxyUser = $proxyUser;
        $this->_apiURL    = str_replace("[[CLID]]", $this->_clientID, $this->_apiURL);
    }

    // Will register your clientID and Tag in order to get a userID. The userID should be stored
    // in a persistent form (filesystem, db, etc) otherwise you will hit your user limit.
    public function register($clientID = null)
    {
        // Use members from constructor if no input is specified.
        if ($clientID === null) { $clientID = $this->_clientID."-".$this->_clientTag; }

        // Make sure user doesn't try to register again if they already have a userID in the ctor.
        if ($this->_userID !== null)
        {
            echo "Warning: You already have a userID, no need to register another. Using current ID.\n";
            return $this->_userID;
        }

        // Do the register request
        $request = "<QUERIES>
                       <QUERY CMD=\"REGISTER\">
                          <CLIENT>".$clientID."</CLIENT>
                       </QUERY>
                    </QUERIES>";
        $http = new HTTP($this->_apiURL, $this->_proxyUrl, $this->_proxyUser);
        $response = $http->post($request);
        $response = $this->_checkResponse($response);

        // Cache it locally then return to user.
        $this->_userID = (string)$response->RESPONSE->USER;
        return $this->_userID;
    }

    // Queries the Gracenote service for a track
    public function searchTrack($artistName, $albumTitle, $trackTitle, $matchMode = self::ALL_RESULTS)
    {
        // Sanity checks
        if ($this->_userID === null) { $this->register(); }

        $body = $this->_constructQueryBody($artistName, $albumTitle, $trackTitle, "", "ALBUM_SEARCH", $matchMode);
        $data = $this->_constructQueryRequest($body);
        return $this->_execute($data);
    }

    // Queries the Gracenote service for an artist.
    public function searchArtist($artistName, $matchMode = self::ALL_RESULTS)
    {
        return $this->searchTrack($artistName, "", "", $matchMode);
    }

    // Queries the Gracenote service for an album.
    public function searchAlbum($artistName, $albumTitle, $matchMode = self::ALL_RESULTS)
    {
        return $this->searchTrack($artistName, $albumTitle, "", $matchMode);
    }

    // This looks up an album directly using it's Gracenote identifier. Will return all the
    // additional GOET data.
    public function fetchAlbum($gn_id)
    {
        // Sanity checks
        if ($this->_userID === null) { $this->register(); }

        $body = $this->_constructQueryBody("", "", "", $gn_id, "ALBUM_FETCH");
        $data = $this->_constructQueryRequest($body, "ALBUM_FETCH");
        return $this->_execute($data);
    }

    // This retrieves ONLY the OET data from a fetch, and nothing else. Will return an array of that data.
    public function fetchOETData($gn_id)
    {
        // Sanity checks
        if ($this->_userID === null) { $this->register(); }

        $body = "<GN_ID>".$gn_id."</GN_ID>
                 <OPTION>
                     <PARAMETER>SELECT_EXTENDED</PARAMETER>
                     <VALUE>ARTIST_OET</VALUE>
                 </OPTION>
                 <OPTION>
                     <PARAMETER>SELECT_DETAIL</PARAMETER>
                     <VALUE>ARTIST_ORIGIN:4LEVEL,ARTIST_ERA:2LEVEL,ARTIST_TYPE:2LEVEL</VALUE>
                 </OPTION>";

        $data = $this->_constructQueryRequest($body, "ALBUM_FETCH");
        $request = new HTTP($this->_apiURL, $this->_proxyUrl, $this->_proxyUser);
        $response = $request->post($data);
        $xml = $this->_checkResponse($response);

        $output = array();
        $output["artist_origin"] = ($xml->RESPONSE->ALBUM->ARTIST_ORIGIN) ? $this->_getOETElem($xml->RESPONSE->ALBUM->ARTIST_ORIGIN) : "";
        $output["artist_era"]    = ($xml->RESPONSE->ALBUM->ARTIST_ERA)    ? $this->_getOETElem($xml->RESPONSE->ALBUM->ARTIST_ERA)    : "";
        $output["artist_type"]   = ($xml->RESPONSE->ALBUM->ARTIST_TYPE)    ? $this->_getOETElem($xml->RESPONSE->ALBUM->ARTIST_TYPE)  : "";
        return $output;
    }

    // Fetches album metadata based on a table of contents.
    public function albumToc($toc)
    {
        // Sanity checks
        if ($this->_userID === null) { $this->register(); }

        $body = "<TOC><OFFSETS>".$toc."</OFFSETS></TOC>";

        $data = $this->_constructQueryRequest($body, "ALBUM_TOC");
        return $this->_execute($data);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////

    // Simply executes the query to Gracenote WebAPI
    protected function _execute($data)
    {
        $request = new HTTP($this->_apiURL, $this->_proxyUrl, $this->_proxyUser);
        $response = $request->post($data);
        return $this->_parseResponse($response);
    }

    // This will construct the gracenote query, adding in the authentication header, etc.
    protected function _constructQueryRequest($body, $command = "ALBUM_SEARCH")
    {
        return
            "<QUERIES>
                <AUTH>
                    <CLIENT>".$this->_clientID."-".$this->_clientTag."</CLIENT>
                    <USER>".$this->_userID."</USER>
                </AUTH>
                <QUERY CMD=\"".$command."\">
                    ".$body."
                </QUERY>
            </QUERIES>";
    }

    // Constructs the main request body, including some default options for metadata, etc.
    protected function _constructQueryBody($artist, $album = "", $track = "", $gn_id = "", $command = "ALBUM_SEARCH", $matchMode = self::ALL_RESULTS)
    {
        $body = "";

        // If a fetch scenario, user the Gracenote ID.
        if ($command == "ALBUM_FETCH")
        {
            $body .= "<GN_ID>".$gn_id."</GN_ID>";
        }
        // Otherwise, just do a search.
        else
        {
            // Only get the single best match if that's what the user wants.
            if ($matchMode == self::BEST_MATCH_ONLY) { $body .= "<MODE>SINGLE_BEST_COVER</MODE>"; }

            // If a search scenario, then need the text input
            if ($artist != "") { $body .= "<TEXT TYPE=\"ARTIST\">".$artist."</TEXT>"; }
            if ($track != "")  { $body .= "<TEXT TYPE=\"TRACK_TITLE\">".$track."</TEXT>"; }
            if ($album != "")  { $body .= "<TEXT TYPE=\"ALBUM_TITLE\">".$album."</TEXT>"; }
        }

        // Include extended data.
        $body .= "<OPTION>
                      <PARAMETER>SELECT_EXTENDED</PARAMETER>
                      <VALUE>COVER,REVIEW,ARTIST_BIOGRAPHY,ARTIST_IMAGE,ARTIST_OET,MOOD,TEMPO</VALUE>
                  </OPTION>";

        // Include more detailed responses.
        $body .= "<OPTION>
                      <PARAMETER>SELECT_DETAIL</PARAMETER>
                      <VALUE>GENRE:3LEVEL,MOOD:2LEVEL,TEMPO:3LEVEL,ARTIST_ORIGIN:4LEVEL,ARTIST_ERA:2LEVEL,ARTIST_TYPE:2LEVEL</VALUE>
                  </OPTION>";

        // Only want the thumbnail cover art for now (LARGE,XLARGE,SMALL,MEDIUM,THUMBNAIL)
        $body .= "<OPTION>
                      <PARAMETER>COVER_SIZE</PARAMETER>
                      <VALUE>MEDIUM</VALUE>
                  </OPTION>";

        return $body;
    }

    // Check the response for any Gracenote API errors.
    protected function _checkResponse($response = null)
    {
        // Response is in XML, so attempt to load into a SimpleXMLElement.
        $xml = null;
        try
        {
            $xml = new \SimpleXMLElement($response);
        }
        catch (Exception $e)
        {
            throw new GNException(GNError::UNABLE_TO_PARSE_RESPONSE);
        }

        // Get response status code.
        $status = (string) $xml->RESPONSE->attributes()->STATUS;

        // Check for any error codes and handle accordingly.
        switch ($status)
        {
            case "ERROR":    throw new GNException(GNError::API_RESPONSE_ERROR, (string) $xml->MESSAGE);
            case "NO_MATCH": throw new GNException(GNError::API_NO_MATCH);
            default:
                if ($status != "OK") { throw new GNException(GNError::API_NON_OK_RESPONSE, $status); }
        }

        return $xml;
    }

    // This parses the API response into a PHP Array object.
    protected function _parseResponse($response)
    {
        // Parse the response from Gracenote, check for errors, etc.
        try
        {
            $xml = $this->_checkResponse($response);
        }
        catch (SAPIException $e)
        {
            // If it was a no match, just give empty array back
            if ($e->getCode() == SAPIError::GRACENOTE_NO_MATCH)
            {
                return array();
            }

            // Otherwise, re-throw the exception
            throw $e;
        }

        // If we get to here, there were no errors, so continue to parse the response.
        $output = array();
        foreach ($xml->RESPONSE->ALBUM as $a)
        {
            $obj = array();

            // Album metadata
            $obj["album_gnid"]        = (string)($a->GN_ID);
            $obj["album_artist_name"] = (string)($a->ARTIST);
            $obj["album_title"]       = (string)($a->TITLE);
            $obj["album_year"]        = (string)($a->DATE);
            $obj["genre"]             = $this->_getOETElem($a->GENRE);
            $obj["album_art_url"]     = (string)($this->_getAttribElem($a->URL, "TYPE", "COVERART"));

            // Artist metadata
            $obj["artist_image_url"]  = (string)($this->_getAttribElem($a->URL, "TYPE", "ARTIST_IMAGE"));
            $obj["artist_bio_url"]    = (string)($this->_getAttribElem($a->URL, "TYPE", "ARTIST_BIOGRAPHY"));
            $obj["review_url"]        = (string)($this->_getAttribElem($a->URL, "TYPE", "REVIEW"));

            // If we have artist OET info, use it.
            if ($a->ARTIST_ORIGIN)
            {
                $obj["artist_era"]    = $this->_getOETElem($a->ARTIST_ERA);
                $obj["artist_type"]   = $this->_getOETElem($a->ARTIST_TYPE);
                $obj["artist_origin"] = $this->_getOETElem($a->ARTIST_ORIGIN);
            }
            // If not available, do a fetch to try and get it instead.
            else
            {
                $obj = array_merge($obj, $this->fetchOETData((string)($a->GN_ID)));
            }

            // Parse track metadata if there is any.
            foreach($a->TRACK as $t)
            {
                $track = array();

                $track["track_number"]      = (int)($t->TRACK_NUM);
                $track["track_gnid"]        = (string)($t->GN_ID);
                $track["track_title"]       = (string)($t->TITLE);
                $track["track_artist_name"] = (string)($t->ARTIST);

                // If no specific track artist, use the album one.
                if (!$t->ARTIST) { $track["track_artist_name"] = $obj["album_artist_name"]; }

                $track["mood"]              = $this->_getOETElem($t->MOOD);
                $track["tempo"]             = $this->_getOETElem($t->TEMPO);

                // If track level GOET data exists, overwrite metadata from album.
                if (isset($t->GENRE))         { $obj["genre"]         = $this->_getOETElem($t->GENRE); }
                if (isset($t->ARTIST_ERA))    { $obj["artist_era"]    = $this->_getOETElem($t->ARTIST_ERA); }
                if (isset($t->ARTIST_TYPE))   { $obj["artist_type"]   = $this->_getOETElem($t->ARTIST_TYPE); }
                if (isset($t->ARTIST_ORIGIN)) { $obj["artist_origin"] = $this->_getOETElem($t->ARTIST_ORIGIN); }

                $obj["tracks"][] = $track;
            }

            $output[] = $obj;
        }
        return $output;
    }

    // A helper function to return the child node which has a certain attribute value.
    private function _getAttribElem($root, $attribute, $value)
    {
        foreach ($root as $r)
        {
            $attrib = $r->attributes();
            if ($attrib[$attribute] == $value) { return $r; }
        }
    }

    // A helper function to parse OET data into an array
    private function _getOETElem($root)
    {
        $array = array();
        foreach($root as $data)
        {
            $array[] = array(
                "id"   => (int)($data["ID"]),
                "text" => (string)($data)
            );
        }
        return $array;
    }
};

// Extend normal PHP exceptions by includes an additional information field we can utilize.
class GNException extends \Exception
{
    private $_extInfo; // Additional information on the exception.

    public function __construct($code = 0, $extInfo = "")
    {
        parent::__construct(GNError::getMessage($code), $code);
        $this->_extInfo = $extInfo;
        echo("exception: code=" . $code . ", message=" . GNError::getMessage($code) . ", ext=" . $extInfo . "\n");
    }

    public function getExtraInfo() { return $this->_extInfo; }
};

// A simple class to encapsulate errors that can be returned by the API.
class GNError
{
    const UNABLE_TO_PARSE_RESPONSE = 1;    // The response couldn't be parsed. Maybe an error, or maybe the API changed.

    const API_RESPONSE_ERROR       = 1000; // There was a GN error code returned in the response.
    const API_NO_MATCH             = 1001; // The API returned a NO_MATCH (i.e. there were no results).
    const API_NON_OK_RESPONSE      = 1002; // There was some unanticipated non-"OK" response from the API.

    const HTTP_REQUEST_ERROR       = 2000; // An uncaught exception was raised while doing a cURL request.
    const HTTP_REQUEST_TIMEOUT     = 2001; // The external request timed out.
    const HTTP_RESPONSE_ERROR_CODE = 2002; // There was a HTTP400 error code returned.
    const HTTP_RESPONSE_ERROR      = 2003; // A cURL error that wasn't a timeout or HTTP400 response.

    const INVALID_INPUT_SPECIFIED  = 3000; // Some input the user gave wasn't valid.

    // The human readable error messages
    static public $_MESSAGES = array
    (
        // Generic Errors
        1    => "Unable to parse response from Gracenote WebAPI."

        // Specific API Errors
        ,1000 => "The API returned an error code."
        ,1001 => "The API returned no results."
        ,1002 => "The API returned an unacceptable response."

        // HTTP Errors
        ,2000 => "There was an error while performing an external request."
        ,2001 => "Request to a Gracenote WebAPI timed out."
        ,2002 => "WebAPI response had a HTTP error code."
        ,2003 => "cURL returned an error when trying to make the request."

        // Input Errors
        ,3000 => "Invalid input."
    );

    public static function getMessage($code) { return self::$_MESSAGES[$code]; }
};

// A class to handle all external communication via HTTP(S)
class HTTP
{
    // Constants
    const GET  = 0;
    const POST = 1;

    // Members
    private $_url;                  // URL to send the request to.
    private $_timeout;              // Seconds before we give up.
    private $_headers  = array();   // Any headers to send with the request.
    private $_postData = null;      // The POST data.
    private $_ch       = null;      // cURL handle
    private $_type     = HTTP::GET; // Default is GET

    ////////////////////////////////////////////////////////////////////////////////////////////////

    // Ctor
    public function __construct($url, $proxyUrl = null, $proxyUser = null, $timeout = 10000)
    {
        global $_CONFIG;
        $this->_url     = $url;
        $this->_timeout = $timeout;

        // Prepare the cURL handle.
        $this->_ch = curl_init();

        // Set connection options.
        curl_setopt($this->_ch, CURLOPT_URL,            $this->_url);     // API URL
        curl_setopt($this->_ch, CURLOPT_USERAGENT,      "php-gracenote"); // Set our user agent
        curl_setopt($this->_ch, CURLOPT_FAILONERROR,    true);            // Fail on error response.
        curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, true);            // Follow any redirects
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);            // Put the response into a variable instead of printing.
        curl_setopt($this->_ch, CURLOPT_TIMEOUT_MS,     $this->_timeout); // Don't want to hang around forever.

        // Code custom !!!
        if($proxyUrl !== null){
            curl_setopt($this->_ch, CURLOPT_PROXY, $proxyUrl);
            curl_setopt($this->_ch, CURLOPT_PROXYUSERPWD, $proxyUser);
        }
    }

    // Dtor
    public function __destruct()
    {
        if ($this->_ch != null) { curl_close($this->_ch); }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////

    // Prepare the cURL handle
    private function prepare()
    {
        // Set header data
        if ($this->_headers != null)
        {
            $hdrs = array();
            foreach ($this->_headers as $header => $value)
            {
                // If specified properly (as string) use it. If name=>value, convert to name:value.
                $hdrs[] = ((strtolower(substr($value, 0, 1)) === "x")
                          && (strpos($value, ":") !== false)) ? $value : $header.":".$value;
            }
            curl_setopt($this->_ch, CURLOPT_HTTPHEADER, $hdrs);
        }

        // Add POST data if it's a POST request
        if ($this->_type == HTTP::POST)
        {
            curl_setopt($this->_ch, CURLOPT_POST,       true);
            curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $this->_postData);
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function execute()
    {
        // Prepare the request
        $this->prepare();

        // Now try to make the call.
        $response = null;
        try
        {
            if (GN_DEBUG) { echo("http: external request ".(($this->_type == HTTP::GET) ? "GET" : "POST")." url=" . $this->_url. ", timeout=" . $this->_timeout . "\n"); }

            // Execute the request
            $response = curl_exec($this->_ch);
        }
        catch (Exception $e)
        {
            throw new GNException(GNError::HTTP_REQUEST_ERROR);
        }

        // Validate the response, or throw the proper exceptionS.
        $this->validateResponse($response);

        return $response;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////

    // This validates a cURL response and throws an exception if it's invalid in any way.
    public function validateResponse($response, $errno = null)
    {
        $curl_error = ($errno === null) ? curl_errno($this->_ch) : $errno;
        if ($curl_error !== CURLE_OK)
        {
            switch ($curl_error)
            {
                case CURLE_HTTP_NOT_FOUND:      throw new GNException(GNError::HTTP_RESPONSE_ERROR_CODE, $this->getResponseCode());
                case CURLE_OPERATION_TIMEOUTED: throw new GNException(GNError::HTTP_REQUEST_TIMEOUT);
            }

            throw new GNException(GNError::HTTP_RESPONSE_ERROR, $curl_error);
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function getHandle()          { return $this->_ch; }
    public function getResponseCode()    { return curl_getinfo($this->_ch, CURLINFO_HTTP_CODE); }

    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function setPOST()            { $this->_type = HTTP::POST; }
    public function setGET()             { $this->_type = HTTP::GET; }
    public function setPOSTData($data)   { $this->_postData = $data; }
    public function setHeaders($headers) { $this->_headers = $headers; }
    public function addHeader($header)   { $this->_headers[] = $header; }
    public function setCurlOpt($o, $v)   { curl_setopt($this->_ch, $o, $v); }

    ////////////////////////////////////////////////////////////////////////////////////////////////

    // Wrappers
    public function get()
    {
        $this->setGET();
        return $this->execute();
    }

    public function post($data = null)
    {
        if ($data != null) { $this->_postData = $data; }
        $this->setPOST();
        return $this->execute();
    }
};
