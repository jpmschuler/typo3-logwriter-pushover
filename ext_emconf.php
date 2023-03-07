<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'LogWriter Pushover.net',
    'description' => 'Logwriter for sending notifications via pushover.net API',
    'category' => 'plugin',
    'author' => 'Schuler, J. Peter M.',
    'author_email' => 'j.peter.m.schuler@uni-due.de',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '11.5.4',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99'
        ],
    ]
];
