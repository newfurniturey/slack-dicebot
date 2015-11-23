<?php

namespace Slackbot\Hooks;
require_once 'Hook.php';
require_once 'exception/ConfigException.php';

class DicebotHook extends Hook {
    const CONFIG_FILE = 'config.ini';
    
    /**
     * List of supported tokens.
     * @type Array
     */
    protected $tokens = array();
    
    /**
     * List of supported triggers. (to be populated by subclasses)
     * @type Array
     */
    protected $triggers = array(
        '.roll'
    );
    
    /**
     * Initialize the bot
     */
    public function __construct() {
        $this->loadConfig();
    }
    
    /**
     * Processes the input text and returns the response for Slack.
     *
     * @param String $trigger
     * @param String $raw_text
     * @return String
     */
    public function process($trigger, $raw_text) {
        $text = trim(substr($raw_text, strlen($trigger) + 1));
        
        if (preg_match('/^(\d+)d(\d+)$/', $text, $matches)) {
            $rolls = $this->roll($matches[1], $matches[2]);
            return $this->formatResponse($text, $rolls);
        }
        
        return null;
    }
    
    /**
     * Rolls a die with the specified number of sides one or more times.
     *
     * @param Integer $multiplier How many times to roll the die.
     * @param Integer $sides      How many sides does the die have.
     * @return Array
     */
    protected function roll($multiplier, $sides) {
        $rolls = array();
        for ($i = 0; $i < $multiplier; $i++) {
            $rolls[] = mt_rand(1, $sides);
        }
        return $rolls;
    }
    
    /**
     * Formats the list of rolls into a single pretty line.
     *
     * @param String $text The user-specified roll(s) to make
     * @param Array $rolls The generated roll results
     * @return String
     */
    protected function formatResponse($text, array $rolls) {
        $response = $text . ': ';
        $total = 0;
        $numRolls = count($rolls);
        for ($i = 0; $i < $numRolls; $i++) {
            $total += $rolls[$i];
            $response .= (($i > 0) ? '+' : '') . $rolls[$i];
        }
        
        return $response . (($numRolls > 1) ? (' = ' . $total) : '');
    }
    
    /**
     * Dynamically load in the supported tokens for this bot.
     */
    private function loadConfig() {
        if (!is_readable(self::CONFIG_FILE)) {
            throw new \Slackbot\Exception\ConfigException('Missing config file ' . self::CONFIG_FILE);
        }
        
        $config = parse_ini_file(self::CONFIG_FILE, true);
        if (empty($config['dicebot']) || empty($config['dicebot']['token'])) {
            throw new \Slackbot\Exception\ConfigException('Missing dicebot tokens');
        }
        
        $this->tokens = is_array($config['dicebot']['token']) ? $config['dicebot']['token'] : array($config['dicebot']['token']);
    }
}
