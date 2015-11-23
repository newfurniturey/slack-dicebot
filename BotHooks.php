<?php

namespace Slackbot;

class BotHooks {
    const HOOK_DIR = 'hooks';
    
    /**
     * Single instance of the BotHooks object.
     * @type BotHooks
     */
    private static $instance = null;
    
    /**
     * List of all loaded hooks.
     * @type Array
     */
    private $hooks = array();
    
    /**
     * Mapping of all found triggers and their respective hooks.
     * @type Array
     */
    private $triggers = array();
    
    /**
     * Singleton constructor to initialize the BotHooks object.
     */
    private function __construct() {
        $this->initHooks();
    }
    
    /**
     * Initializes and returns an instance of the BotHooks object.
     *
     * @return BotHooks
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new BotHooks();
        }
        
        return self::$instance;
    }
    
    /**
     * Returns a list of all hooks that accept the given token.
     *
     * @param String $token
     * @return Array
     */
    public function getHooksByToken($token) {
        $hooks = array();
        foreach ($this->hooks as $hook) {
            if ($hook->isValidToken($token)) {
                $hooks[] = $hook;
            }
        }
        
        return $hooks;
    }
    
    /**
     * Returns a list of all hooks that support the given trigger.
     *
     * @param String $trigger
     * @return Array;
     */
    public function getHooksByTrigger($trigger) {
        return isset($this->triggers[$trigger]) ? $this->triggers[$trigger] : array();
    }
    
    /**
     * Initializes all of the hooks found in the configured hook directory.
     */
    private function initHooks() {
        if (!is_dir(BotHooks::HOOK_DIR)) {
            throw new Slackbot\Exception\ConfigException('Cannot locate hook directory "' . BotHooks::HOOK_DIR . '"');
        }
        
        $hookFiles = glob(BotHooks::HOOK_DIR . '/[A-Z][a-z]*Hook.php');
        foreach ($hookFiles as $hookFile) {
            // include the file
            require_once $hookFile;
            
            // load a class that has the same name as the file
            $fileName = substr($hookFile, strrpos($hookFile, '/') + 1);
            $className = 'Slackbot\\Hooks\\' . substr($fileName, 0, strlen($fileName) - 4);
            $hookInstance = new $className();
            if (!class_exists($className) && is_subclass_of($hookInstance, 'Slackbot\\Hooks\\Hook')) {
                unset($hookInstance);
                continue;
            }
            
            // store the hook
            $this->hooks[] = $hookInstance;
            
            // process the triggers supported by the hook
            $this->loadHookTriggers($hookInstance);
        }
    }
    
    /**
     * Generate a trigger-to-hook map for the given hook and each of it's supported triggers.
     *
     * @param \Slackbot\Hooks\Hook $hook
     */
    private function loadHookTriggers(\Slackbot\Hooks\Hook $hook) {
        $triggers = $hook->getTriggers();
        foreach ($triggers as $trigger) {
            if (!isset($this->triggers[$trigger])) {
                $this->triggers[$trigger] = array();
            }
            
            $this->triggers[$trigger][] = $hook;
        }
    }
}

// auto-initialize this class upon being included =]
BotHooks::getInstance();
