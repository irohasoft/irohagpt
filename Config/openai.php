<?php

// Replace your API key
$config['api_key']	= '(your API Key)';
//$config['model']	= 'gpt-3.5-turbo';
//$config['model']	= 'gpt-4';
$config['model']	= 'gpt-4o';


// API request parameters
// https://platform.openai.com/docs/api-reference/chat
$config['temperature']			= 1.0;
$config['max_tokens']			= 1024;
$config['top_p']				= 1.0;
$config['frequency_penalty']	= 0;
$config['presence_penalty']		= 0;

