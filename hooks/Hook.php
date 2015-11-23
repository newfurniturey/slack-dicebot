<?php

namespace Slackbot\Hooks;

abstract class Hook {
    
    /**
     * List of supported tokens. (to be populated by subclasses)
     * @type Array
     */
    protected $tokens = array();
    
    /**
     * List of supported triggers. (to be populated by subclasses)
     * @type Array
     */
    protected $triggers = array();
    
    /**
     * Determines if the given token is accepted by the current class.
     *
     * @param String $token
     * @return Boolean
     */
    public function isValidToken($token) {
        foreach ($this->tokens as $supported_token) {
            if ($token === $supported_token) {
                return true;
            }
        }
        
        // no matching token was found
        return false;
    }
    
    /**
     * Returns the set of triggers the current class accepts.
     *
     * @return Array
     */
    public function getTriggers() {
        return $this->triggers;
    }
    
    /**
     * Processes the input text and returns the response for Slack.
     *
     * @param String $trigger
     * @param String $text
     * @return String
     */
    abstract public function process($trigger, $text);
}
