<?php

namespace Slackbot\Hooks;

abstract class Hook {
    
    /**
     * The declared name of this hook.
	 * @type String
     */
    protected $name = '';
	
    /**
     * List of supported tokens.
	 * This is typically populated through the config.ini file.
     * @type Array
     */
    protected $tokens = array();
    
    /**
     * List of supported triggers. (to be populated by subclasses)
     * @type Array
     */
    protected $triggers = array();
	
	/**
	 * Adds the specified token to the supported-token list for
	 * the current hook.
	 *
	 * @param String $token
	 */
	public function addToken($token) {
		if (!in_array($token, $this->tokens)) {
			$this->tokens[] = $token;
		}
	}
	
	/**
	 * Adds a list of tokens to the supported-token list for
	 * the current hook.
	 *
	 * @param Array $tokens
	 */
	public function addTokens(array $tokens) {
		foreach ($tokens as $token) {
			$this->addToken($token);
		}
	}
	
	/**
	 * Returns the name of the current hook.
	 *
	 * @return String
	 */
	public function getName() {
		return $this->name;
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
     * Processes the input text and returns the response for Slack.
     *
     * @param String $trigger
     * @param String $text
     * @return String
     */
    abstract public function process($trigger, $text);
}
