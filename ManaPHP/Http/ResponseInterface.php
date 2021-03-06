<?php

namespace ManaPHP\Http {

    /**
     * ManaPHP\Http\ResponseInterface initializer
     */
    interface ResponseInterface
    {

        /**
         * Sets the HTTP response code
         *
         * @param int    $code
         * @param string $message
         *
         * @return static
         */
        public function setStatusCode($code, $message);

        /**
         * Overwrites a header in the response
         *
         * @param string $name
         * @param string $value
         *
         * @return static
         */
        public function setHeader($name, $value);

        /**
         * Send a raw header to the response
         *
         * @param string $header
         *
         * @return static
         */
        public function setRawHeader($header);

        /**
         * Sets output expire time header
         *
         * @param int|\DateTime $datetime
         *
         * @return static
         */
        public function setExpires($datetime);

        /**
         * Sends a Not-Modified response
         *
         * @return static
         */
        public function setNotModified();

        /**
         * Sets the response content-type mime, optionally the charset
         *
         * @param string $contentType
         * @param string $charset
         *
         * @return static
         */
        public function setContentType($contentType, $charset = null);

        /**
         * Redirect by HTTP to another action or URL
         *
         * @param string $location
         * @param int    $statusCode
         *
         * @return static
         */
        public function redirect($location, $statusCode = 302);

        /**
         * Sets HTTP response body
         *
         * @param string $content
         *
         * @return static
         */
        public function setContent($content);

        /**
         * Sets HTTP response body. The parameter is automatically converted to JSON
         *
         *<code>
         *    $response->setJsonContent(array("status" => "OK"));
         *</code>
         *
         * @param mixed $content
         * @param int   $jsonOptions
         *
         * @return static
         */
        public function setJsonContent($content, $jsonOptions = null);

        /**
         * Appends a string to the HTTP response body
         *
         * @param string $content
         *
         * @return static
         */
        public function appendContent($content);

        /**
         * Gets the HTTP response body
         *
         * @return string
         */
        public function getContent();

        /**
         * Sends headers to the client
         *
         * @return static
         */
        public function sendHeaders();

        /**
         * Prints out HTTP response to the client
         *
         * @return static
         */
        public function send();

        /**
         * Sets an attached file to be sent at the end of the request
         *
         * @param string $file
         * @param string $attachmentName
         *
         * @return static
         */
        public function setFileToSend($file, $attachmentName = null);

        /**
         * @param string $attachmentName
         *
         * @return static
         */
        public function setAttachment($attachmentName);

        /**
         * @param array        $rows
         * @param string       $attachmentName
         * @param array|string $header
         *
         * @return mixed
         */
        public function setCsvContent($rows, $attachmentName, $header = null);
    }
}
