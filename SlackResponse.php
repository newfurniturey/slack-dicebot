<?php

namespace Slackbot;

class SlackResponse {
    
    /**
     * Single instance of the SlackResponse object.
     * @type SlackResponse
     */
    private static $instance = null;
    
    /**
     * Singleton constructor to initialize the SlackResponse object.
     */
    private function __construct() {
        
    }
    
    /**
     * Initializes and returns an instance of the SlackResponse object.
     *
     * @return SlackResponse
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new SlackResponse();
        }
        
        return self::$instance;
    }
    
    /**
     * Declares the current request to be invalid by issuing the 400 HTTP Status Code.
     */
    public function invalidRequest($message = null) {
        header('HTTP/1.0 400 Bad Request');
        $this->respond(
            ($message !== null) ? $message : '400 Bad Request'
        );
    }
    
    /**
     * Declares the current request to be unauthorized by issuing the 403 HTTP Status Code.
     */
    public function unauthorizedRequest() {
        header('HTTP/1.0 403 Forbidden');
        $this->respond(
            ($message !== null) ? $message : '403 Forbidden'
        );
    }
    
    /**
     * Sends the given response text to the browser and ends the request.
     *
     * @param string|array $response
     */
    public function respond($response) {
        $expectedResponse = array('text' => '');
        
        if (is_string($response)) {
            $expectedResponse['text'] = $response;
        } else if (is_array($response)) {
            if (!isset($response['text'])) {
                throw new \InvalidArgumentException('Missing "text" response index');
            }
            
            $expectedResponse['text'] = $response['text'];
        } else {
            throw new \InvalidArgumentException('Unknown $response data type');
        }
        
        echo json_encode($expectedResponse);
        exit();
    }
}
