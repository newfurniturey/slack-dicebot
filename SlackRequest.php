<?php

namespace Slackbot;

class SlackRequest {
    const REQUEST_TOKEN         = 'token';
    const REQUEST_TEAM_ID       = 'team_id';
    const REQUEST_TEAM          = 'team_domain';
    const REQUEST_CHANNEL_ID    = 'channel_id';
    const REQUEST_CHANNEL       = 'channel_name';
    const REQUEST_TIMESTAMP     = 'timestamp';
    const REQUEST_USER_ID       = 'user_id';
    const REQUEST_USER          = 'user_name';
    const REQUEST_TEXT          = 'text';
    const REQUEST_TRIGGER       = 'trigger_word';
    
    /**
     * Single instance of the SlackRequest object.
     * @type SlackRequest
     */
    private static $instance = null;
    
    /**
     * Holds all of the $_POST parameters
     * @type Array
     */
    private $requestParams = array();
    
    /**
     * Singleton constructor to initialize the SlackRequest object.
     */
    private function __construct() {
        $this->initRequestParams();
    }
    
    /**
     * Initializes and returns an instance of the SlackRequest object.
     *
     * @return SlackRequest
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new SlackRequest();
        }
        
        return self::$instance;
    }
    
    /**
     * Returns the value for the requested parameter, if it exists.
     *
     * @param string $param
     * @return string|array
     */
    public function getParam($param) {
        return isset($this->requestParams[$param]) ? $this->requestParams[$param] : null;
    }
    
    /**
     * Checks if the current request has the required parameters.
     *
     * @return boolean
     */
    public function isValidRequest() {
        static $requiredFields = array(
            self::REQUEST_TOKEN, self::REQUEST_TEAM, self::REQUEST_CHANNEL,
            self::REQUEST_USER, self::REQUEST_TEXT, self::REQUEST_TRIGGER
        );
        
        if (empty($this->requestParams)) {
            return false;
        }
        
        foreach ($requiredFields as $field) {
            if (empty($this->requestParams[$field])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Populates a local container with data from $_POST.
     */
    private function initRequestParams() {
        $this->requestParams = $_POST;
        $_POST = array();
    }
}

// auto-initialize this class upon being included =]
SlackRequest::getInstance();
