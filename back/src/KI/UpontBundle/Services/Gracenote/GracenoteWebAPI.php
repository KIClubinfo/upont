<?php

namespace KI\UpontBundle\Services\Gracenote;

//===== Gracenote PHP API: https://github.com/richadams/php-gracenote =====//
// Code modifié en tenant compte des paramètres proxy

// Defaults
if(!defined('GN_DEBUG')) { define('GN_DEBUG', false); }

class GracenoteWebAPI
{
    // Constants
    const BEST_MATCH_ONLY = 0; // Will put API into 'SINGLE_BEST' mode.
    const ALL_RESULTS     = 1;

    // Members
    private $clientID  = null;
    private $clientTag = null;
    private $userID    = null;
    private $proxyUrl  = null;
    private $proxyUser = null;
    private $apiURL    = 'https://[[CLID]].web.cddbp.net/webapi/xml/1.0/';

    // Constructor
    public function __construct($clientID, $clientTag, $userID = null, $proxyUrl, $proxyUser)
    {
        // Sanity checks
        if ($clientID === null || $clientID == '')   { throw new GNException(GNError::INVALID_INPUT_SPECIFIED, 'clientID'); }
        if ($clientTag === null || $clientTag == '') { throw new GNException(GNError::INVALID_INPUT_SPECIFIED, 'clientTag'); }

        $this->clientID  = $clientID;
        $this->clientTag = $clientTag;
        $this->userID    = $userID;
        $this->proxyUrl  = $proxyUrl;
        $this->proxyUser = $proxyUser;
        $this->apiURL    = str_replace('[[CLID]]', $this->clientID, $this->apiURL);
    }

    // Will register your clientID and Tag in order to get a userID. The userID should be stored
    // in a persistent form (filesystem, db, etc) otherwise you will hit your user limit.
    public function register($clientID = null)
    {
        // Use members from constructor if no input is specified.
        if ($clientID === null) { $clientID = $this->clientID.'-'.$this->clientTag; }

        // Make sure user doesn't try to register again if they already have a userID in the ctor.
        if ($this->userID !== null)
        {
            echo 'Warning: You already have a userID, no need to register another. Using current ID.\n';
            return $this->userID;
        }

        // Do the register request
        $request = '<QUERIES>
                       <QUERY CMD=\'REGISTER\'>
                          <CLIENT>'.$clientID.'</CLIENT>
                       </QUERY>
                    </QUERIES>';
        $http = new HTTP($this->apiURL, $this->proxyUrl, $this->proxyUser);
        $response = $http->post($request);
        $response = $this->checkResponse($response);

        // Cache it locally then return to user.
        $this->userID = (string)$response->RESPONSE->USER;
        return $this->userID;
    }

    // Queries the Gracenote service for a track
    public function searchTrack($artistName, $albumTitle, $trackTitle, $matchMode = self::ALL_RESULTS)
    {
        // Sanity checks
        if ($this->userID === null) { $this->register(); }

        $body = $this->constructQueryBody($artistName, $albumTitle, $trackTitle, '', 'ALBUM_SEARCH', $matchMode);
        $data = $this->constructQueryRequest($body);
        return $this->execute($data);
    }

    // Queries the Gracenote service for an artist.
    public function searchArtist($artistName, $matchMode = self::ALL_RESULTS)
    {
        return $this->searchTrack($artistName, '', '', $matchMode);
    }

    // Queries the Gracenote service for an album.
    public function searchAlbum($artistName, $albumTitle, $matchMode = self::ALL_RESULTS)
    {
        return $this->searchTrack($artistName, $albumTitle, '', $matchMode);
    }

    // This looks up an album directly using it's Gracenote identifier. Will return all the
    // additional GOET data.
    public function fetchAlbum($gnid)
    {
        // Sanity checks
        if ($this->userID === null) { $this->register(); }

        $body = $this->constructQueryBody('', '', '', $gnid, 'ALBUM_FETCH');
        $data = $this->constructQueryRequest($body, 'ALBUM_FETCH');
        return $this->execute($data);
    }

    // This retrieves ONLY the OET data from a fetch, and nothing else. Will return an array of that data.
    public function fetchOETData($gnid)
    {
        // Sanity checks
        if ($this->userID === null) { $this->register(); }

        $body = '<GN_ID>'.$gnid.'</GN_ID>
                 <OPTION>
                     <PARAMETER>SELECT_EXTENDED</PARAMETER>
                     <VALUE>ARTIST_OET</VALUE>
                 </OPTION>
                 <OPTION>
                     <PARAMETER>SELECT_DETAIL</PARAMETER>
                     <VALUE>ARTIST_ORIGIN:4LEVEL,ARTIST_ERA:2LEVEL,ARTIST_TYPE:2LEVEL</VALUE>
                 </OPTION>';

        $data = $this->constructQueryRequest($body, 'ALBUM_FETCH');
        $request = new HTTP($this->apiURL, $this->proxyUrl, $this->proxyUser);
        $response = $request->post($data);
        $xml = $this->checkResponse($response);

        $output = array();
        $output['artist_origin'] = ($xml->RESPONSE->ALBUM->ARTIST_ORIGIN) ? $this->getOETElem($xml->RESPONSE->ALBUM->ARTIST_ORIGIN) : '';
        $output['artist_era']    = ($xml->RESPONSE->ALBUM->ARTIST_ERA)    ? $this->getOETElem($xml->RESPONSE->ALBUM->ARTIST_ERA)    : '';
        $output['artist_type']   = ($xml->RESPONSE->ALBUM->ARTIST_TYPE)    ? $this->getOETElem($xml->RESPONSE->ALBUM->ARTIST_TYPE)  : '';
        return $output;
    }

    // Fetches album metadata based on a table of contents.
    public function albumToc($toc)
    {
        // Sanity checks
        if ($this->userID === null) { $this->register(); }

        $body = '<TOC><OFFSETS>'.$toc.'</OFFSETS></TOC>';

        $data = $this->constructQueryRequest($body, 'ALBUM_TOC');
        return $this->execute($data);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////

    // Simply executes the query to Gracenote WebAPI
    protected function execute($data)
    {
        $request = new HTTP($this->apiURL, $this->proxyUrl, $this->proxyUser);
        $response = $request->post($data);
        return $this->parseResponse($response);
    }

    // This will construct the gracenote query, adding in the authentication header, etc.
    protected function constructQueryRequest($body, $command = 'ALBUM_SEARCH')
    {
        return
            '<QUERIES>
                <AUTH>
                    <CLIENT>'.$this->clientID.'-'.$this->clientTag.'</CLIENT>
                    <USER>'.$this->userID.'</USER>
                </AUTH>
                <QUERY CMD=\''.$command.'\'>
                    '.$body.'
                </QUERY>
            </QUERIES>';
    }

    // Constructs the main request body, including some default options for metadata, etc.
    protected function constructQueryBody($artist, $album = '', $track = '', $gnid = '', $command = 'ALBUM_SEARCH', $matchMode = self::ALL_RESULTS)
    {
        $body = '';

        // If a fetch scenario, user the Gracenote ID.
        if ($command == 'ALBUM_FETCH')
        {
            $body .= '<GN_ID>'.$gnid.'</GN_ID>';
        }
        // Otherwise, just do a search.
        else
        {
            // Only get the single best match if that's what the user wants.
            if ($matchMode == self::BEST_MATCH_ONLY) { $body .= '<MODE>SINGLE_BEST_COVER</MODE>'; }

            // If a search scenario, then need the text input
            if ($artist != '') { $body .= '<TEXT TYPE=\'ARTIST\'>'.$artist.'</TEXT>'; }
            if ($track != '')  { $body .= '<TEXT TYPE=\'TRACK_TITLE\'>'.$track.'</TEXT>'; }
            if ($album != '')  { $body .= '<TEXT TYPE=\'ALBUM_TITLE\'>'.$album.'</TEXT>'; }
        }

        // Include extended data.
        $body .= '<OPTION>
                      <PARAMETER>SELECT_EXTENDED</PARAMETER>
                      <VALUE>COVER,REVIEW,ARTIST_BIOGRAPHY,ARTIST_IMAGE,ARTIST_OET,MOOD,TEMPO</VALUE>
                  </OPTION>';

        // Include more detailed responses.
        $body .= '<OPTION>
                      <PARAMETER>SELECT_DETAIL</PARAMETER>
                      <VALUE>GENRE:3LEVEL,MOOD:2LEVEL,TEMPO:3LEVEL,ARTIST_ORIGIN:4LEVEL,ARTIST_ERA:2LEVEL,ARTIST_TYPE:2LEVEL</VALUE>
                  </OPTION>';

        // Only want the thumbnail cover art for now (LARGE,XLARGE,SMALL,MEDIUM,THUMBNAIL)
        $body .= '<OPTION>
                      <PARAMETER>COVER_SIZE</PARAMETER>
                      <VALUE>MEDIUM</VALUE>
                  </OPTION>';

        return $body;
    }

    // Check the response for any Gracenote API errors.
    protected function checkResponse($response = null)
    {
        // Response is in XML, so attempt to load into a SimpleXMLElement.
        $xml = null;
        try
        {
            $xml = new \SimpleXMLElement($response);
        }
        catch (\Exception $e)
        {
            throw new GNException(GNError::UNABLE_TO_PARSE_RESPONSE);
        }

        // Get response status code.
        $status = (string) $xml->RESPONSE->attributes()->STATUS;

        // Check for any error codes and handle accordingly.
        switch ($status)
        {
            case 'ERROR':    throw new GNException(GNError::API_RESPONSE_ERROR, (string) $xml->MESSAGE);
            case 'NO_MATCH': throw new GNException(GNError::API_NO_MATCH);
            default:
                if ($status != 'OK') { throw new GNException(GNError::API_NON_OK_RESPONSE, $status); }
        }

        return $xml;
    }

    // This parses the API response into a PHP Array object.
    protected function parseResponse($response)
    {
        // Parse the response from Gracenote, check for errors, etc.
        try
        {
            $xml = $this->checkResponse($response);
        }
        catch (\SAPIException $e)
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
            $obj['album_gnid']        = (string)($a->GN_ID);
            $obj['album_artist_name'] = (string)($a->ARTIST);
            $obj['album_title']       = (string)($a->TITLE);
            $obj['album_year']        = (string)($a->DATE);
            $obj['genre']             = $this->getOETElem($a->GENRE);
            $obj['album_art_url']     = (string)($this->getAttribElem($a->URL, 'TYPE', 'COVERART'));

            // Artist metadata
            $obj['artist_image_url']  = (string)($this->getAttribElem($a->URL, 'TYPE', 'ARTIST_IMAGE'));
            $obj['artist_bio_url']    = (string)($this->getAttribElem($a->URL, 'TYPE', 'ARTIST_BIOGRAPHY'));
            $obj['review_url']        = (string)($this->getAttribElem($a->URL, 'TYPE', 'REVIEW'));

            // If we have artist OET info, use it.
            if ($a->ARTIST_ORIGIN)
            {
                $obj['artist_era']    = $this->getOETElem($a->ARTIST_ERA);
                $obj['artist_type']   = $this->getOETElem($a->ARTIST_TYPE);
                $obj['artist_origin'] = $this->getOETElem($a->ARTIST_ORIGIN);
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

                $track['track_number']      = (int)($t->TRACK_NUM);
                $track['track_gnid']        = (string)($t->GN_ID);
                $track['track_title']       = (string)($t->TITLE);
                $track['track_artist_name'] = (string)($t->ARTIST);

                // If no specific track artist, use the album one.
                if (!$t->ARTIST) { $track['track_artist_name'] = $obj['album_artist_name']; }

                $track['mood']              = $this->getOETElem($t->MOOD);
                $track['tempo']             = $this->getOETElem($t->TEMPO);

                // If track level GOET data exists, overwrite metadata from album.
                if (isset($t->GENRE))         { $obj['genre']         = $this->getOETElem($t->GENRE); }
                if (isset($t->ARTIST_ERA))    { $obj['artist_era']    = $this->getOETElem($t->ARTIST_ERA); }
                if (isset($t->ARTIST_TYPE))   { $obj['artist_type']   = $this->getOETElem($t->ARTIST_TYPE); }
                if (isset($t->ARTIST_ORIGIN)) { $obj['artist_origin'] = $this->getOETElem($t->ARTIST_ORIGIN); }

                $obj['tracks'][] = $track;
            }

            $output[] = $obj;
        }
        return $output;
    }

    // A helper function to return the child node which has a certain attribute value.
    private function getAttribElem($root, $attribute, $value)
    {
        foreach ($root as $r)
        {
            $attrib = $r->attributes();
            if ($attrib[$attribute] == $value) { return $r; }
        }
    }

    // A helper function to parse OET data into an array
    private function getOETElem($root)
    {
        $array = array();
        foreach($root as $data)
        {
            $array[] = array(
                'id'   => (int)($data['ID']),
                'text' => (string)($data)
            );
        }
        return $array;
    }
}
