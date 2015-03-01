<?php

namespace KI\UpontBundle\Services\Gracenote;

// A class to handle all external communication via HTTP(S)
class HTTP
{
    // Constants
    const GET  = 0;
    const POST = 1;

    // Members
    private $url;                  // URL to send the request to.
    private $timeout;              // Seconds before we give up.
    private $headers  = array();   // Any headers to send with the request.
    private $postData = null;      // The POST data.
    private $ch       = null;      // cURL handle
    private $type     = HTTP::GET; // Default is GET

    ////////////////////////////////////////////////////////////////////////////////////////////////

    // Ctor
    public function __construct($url, $proxyUrl = null, $proxyUser = null, $timeout = 10000)
    {
        $this->url     = $url;
        $this->timeout = $timeout;

        // Prepare the cURL handle.
        $this->ch = curl_init();

        // Set connection options.
        curl_setopt($this->ch, CURLOPT_URL,            $this->url);     // API URL
        curl_setopt($this->ch, CURLOPT_USERAGENT,      'php-gracenote'); // Set our user agent
        curl_setopt($this->ch, CURLOPT_FAILONERROR,    true);            // Fail on error response.
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);            // Follow any redirects
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);            // Put the response into a variable instead of printing.
        curl_setopt($this->ch, CURLOPT_TIMEOUT_MS,     $this->timeout); // Don't want to hang around forever.

        // Code custom !!!
        if($proxyUrl !== null){
            curl_setopt($this->ch, CURLOPT_PROXY, $proxyUrl);
            curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $proxyUser);
        }
    }

    // Dtor
    public function __destruct()
    {
        if ($this->ch !== null) { curl_close($this->ch); }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////

    // Prepare the cURL handle
    private function prepare()
    {
        // Set header data
        if ($this->headers !== null)
        {
            $hdrs = array();
            foreach ($this->headers as $header => $value)
            {
                // If specified properly (as string) use it. If name=>value, convert to name:value.
                $hdrs[] = ((strtolower(substr($value, 0, 1)) === 'x')
                          && (strpos($value, ':') !== false)) ? $value : $header.':'.$value;
            }
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $hdrs);
        }

        // Add POST data if it's a POST request
        if ($this->type == HTTP::POST)
        {
            curl_setopt($this->ch, CURLOPT_POST,       true);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->postData);
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
            if (GN_DEBUG) { echo('http: external request '.(($this->type == HTTP::GET) ? 'GET' : 'POST').' url=' . $this->url. ', timeout=' . $this->timeout . '\n'); }

            // Execute the request
            $response = curl_exec($this->ch);
        }
        catch (\Exception $e)
        {
            throw new GNException(GNError::HTTP_REQUEST_ERROR);
        }

        // Validate the response, or throw the proper exceptionS.
        $this->validateResponse();

        return $response;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////

    // This validates a cURL response and throws an exception if it's invalid in any way.
    public function validateResponse()
    {
        $curlError = curl_errno($this->ch);
        if ($curlError !== CURLE_OK)
        {
            switch ($curlError)
            {
                case CURLE_HTTP_NOT_FOUND:      throw new GNException(GNError::HTTP_RESPONSE_ERROR_CODE, $this->getResponseCode());
                case CURLE_OPERATION_TIMEOUTED: throw new GNException(GNError::HTTP_REQUEST_TIMEOUT);
            }

            throw new GNException(GNError::HTTP_RESPONSE_ERROR, $curlError);
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function getHandle()          { return $this->ch; }
    public function getResponseCode()    { return curl_getinfo($this->ch, CURLINFO_HTTP_CODE); }

    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function setPOST()            { $this->type = HTTP::POST; }
    public function setGET()             { $this->type = HTTP::GET; }
    public function setPOSTData($data)   { $this->postData = $data; }
    public function setHeaders($headers) { $this->headers = $headers; }
    public function addHeader($header)   { $this->headers[] = $header; }
    public function setCurlOpt($o, $v)   { curl_setopt($this->ch, $o, $v); }

    ////////////////////////////////////////////////////////////////////////////////////////////////

    // Wrappers
    public function get()
    {
        $this->setGET();
        return $this->execute();
    }

    public function post($data = null)
    {
        if ($data !== null) { $this->postData = $data; }
        $this->setPOST();
        return $this->execute();
    }
};
