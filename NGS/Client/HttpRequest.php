<?php
namespace NGS\Client;

require_once(__DIR__.'/../Logger/Logger.php');

/**
 * Request object used by {@see NGS\Client\RestHttp}
 */
class HttpRequest
{
    private $uri;
    private $curl;
    private $method;
    private $options;
    private $responseInfo;
    private $responseHeaders;
    private $responseBody;

    public function __construct($uri, $method = null, $body = null, $headers = null, $options = null)
    {
        $this->uri = $uri;
        $this->curl = curl_init($uri);

        $this->options = array(
            CURLOPT_RETURNTRANSFER  => true,
            CURLINFO_HEADER_OUT     => true,
            CURLOPT_HEADER          => true
        );

        if (is_array($options)) {
            $this->options += $options;
        }

        $this->method = $method;

        if($method !== null)
            $this->method($method);
        if($body !== null)
            $this->body($body);
        if($headers !== null)
            $this->headers($headers);
    }

    public function headers($headers)
    {
        if(!isset($this->options[CURLOPT_HTTPHEADER]))
            $this->options[CURLOPT_HTTPHEADER] = array();

        if(is_array($headers)) {
            foreach($headers as $key => $value)
                $this->options[CURLOPT_HTTPHEADER][] = $value;
        }
        else if(is_string($headers)) {
            $this->options[CURLOPT_HTTPHEADER][] = $headers;
        }
        return $this;
    }

    public function method($method)
    {
        $method = strtoupper($method);
        if ($method === 'POST') {
            $this->options[CURLOPT_POST] = true;
        } else {
            $this->options[CURLOPT_CUSTOMREQUEST] = $method;
        }
        return $this;
    }

    public function body($body)
    {
        $this->options[CURLOPT_POSTFIELDS] = $body;
    }

    public function send()
    {
        curl_setopt_array($this->curl, $this->options);

        $response = curl_exec($this->curl);
        $this->responseInfo = curl_getinfo($this->curl);

        $headerSize = $this->responseInfo['header_size'];

        $this->responseHeaders = explode("\r\n", substr($response, 0, $headerSize));
        array_splice($this->responseHeaders, -2);

        $this->responseBody = substr($response, $headerSize);

        return $this->responseBody;
    }

    public function getResponseInfo()
    {
        return $this->responseInfo;
    }

    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    public function getResponseCode()
    {
        return isset($this->responseInfo['http_code']) ? $this->responseInfo['http_code'] : null;
    }

    public function getResponseContentType()
    {
        return isset($this->responseInfo['content_type']) ? $this->responseInfo['content_type'] : null;
    }

    public function getError()
    {
        $error = curl_error($this->curl);
        return $error ? $error : null;
    }

    public function __toString()
    {
        $headers = isset($this->options[CURLOPT_HTTPHEADER]) ? $this->options[CURLOPT_HTTPHEADER] : array();
        $body = isset($this->options[CURLOPT_POSTFIELDS]) ? $this->options[CURLOPT_POSTFIELDS] : '';
        return strtoupper($this->method).' '.$this->uri."\n"
            .implode("\n", $headers)."\n"
            .$body;
    }

    public function logRequest()
    {
        $headers = $this->options[CURLOPT_HTTPHEADER];
        $body = $this->options[CURLOPT_POSTFIELDS];

        $logger = \NGS\Logger::instance();
        $logger->info(array(
            'Sending HTTP Request:',
            $this->method.' '.$this->uri,
            implode(PHP_EOL, $headers),
            $body
        ));
    }

    public function logResponse()
    {
        $headers = $this->getResponseHeaders();
        $body = $this->responseBody;

        $logger = \NGS\Logger::instance();
        $logger->info(array('Received HTTP Response:',
            implode(PHP_EOL, $headers),
            $body
        ));
    }
}
