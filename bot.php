<?php

namespace Slackbot;

/**
 * @todo Remove this after initial testing.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'SlackRequest.php';
require_once 'SlackResponse.php';
require_once 'BotHooks.php';

// initialize and validate the incoming request
$request = SlackRequest::getInstance();
if (!$request->isValidRequest()) {
    SlackResponse::getInstance()->invalidRequest();
}

// get our beloved token, trigger and text
$token = $request->getParam(SlackRequest::REQUEST_TOKEN);
$trigger = $request->getParam(SlackRequest::REQUEST_TRIGGER);
$text = $request->getParam(SlackRequest::REQUEST_TEXT);

// load any registered hooks for this trigger
$tmpHooks = BotHooks::getInstance()->getHooksByTrigger($trigger);
if (empty($tmpHooks)) {
    SlackResponse::getInstance()->invalidRequest();
}

// find which of the loaded hooks are valid for the request token
$hooks = array();
foreach ($tmpHooks as $hook) {
    if ($hook->isValidToken($token)) {
        $hooks[] = $hook;
    }
}
unset($tmpHooks);

// no need to proceed if we don't have any hooks
if (empty($hooks)) {
    SlackResponse::getInstance()->unauthorizedRequest();
}

// try all found hooks for a suitable response
$response = null;
foreach ($hooks as $hook) {
    $response = $hook->process($trigger, $text);
    
    if ($response !== null) {
        // stop processing hooks after we have a single match
        break;
    }
}

if ($response === null) {
    // well boo; no hook matched the given input
    $response = 'Request format not recognized.';
}

// annnnd we're done =]
SlackResponse::getInstance()->respond($response);
