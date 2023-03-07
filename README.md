# EXT:logwriter_pushover
[![TYPO3 badge](https://shields.io/endpoint?url=https://typo3-badges.dev/badge/logwriter_pushover/extension/shields)](https://extensions.typo3.org/extension/logwriter_pushover)
[![Latest Stable Version](http://poser.pugx.org/jpmschuler/logwriter-pushover/v)](https://packagist.org/packages/jpmschuler/showpageeditors)
[![Total Downloads](http://poser.pugx.org/jpmschuler/logwriter-pushover/downloads)](https://packagist.org/packages/jpmschuler/showpageeditors)
[![License](http://poser.pugx.org/jpmschuler/logwriter-pushover/license)](https://packagist.org/packages/jpmschuler/showpageeditors)
[![Build Status](https://github.com/jpmschuler/typo3-logwriter-pushover/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/jpmschuler/typo3-logwriter-pushover/actions/workflows/ci.yml)

[![PHP Version Require](http://poser.pugx.org/jpmschuler/showpageeditors/require/php)](https://packagist.org/packages/jpmschuler/logwriter-pushover)
[![TYPO3 V11](https://img.shields.io/badge/TYPO3-11-orange.svg)](https://get.typo3.org/version/11)


This TYPO3 extension provides LogWriter to send notifications to Pushover.net API.

Configure in e.g. `AdditionalConfiguration.php`
```php
$GLOBALS['TYPO3_CONF_VARS']['LOG']['T3docs']['Examples']['Controller']['writerConfiguration'] = [
    LogLevel::ERROR => [
        PushoverWriter::class => [
            'apiEndpoint' => 'https://api.pushover.net/1/messages.json',
            'apiToken' => 'azGDORePK8gMaC0QOYAMyEEuzJnyUi',
            'userKey' => 'uQiRzpo4DXghDmr9QzzfQu27cmVRsG',
        ],
    ],
];
```
or configure via ENV vars (if you use both, LogWriter config overrides ENV vars).
```sh
PUSHOVER_API_ENDPOINT="https://api.pushover.net/1/messages.json"
PUSHOVER_API_TOKEN="azGDORePK8gMaC0QOYAMyEEuzJnyUi"
PUSHOVER_USER_KEY="uQiRzpo4DXghDmr9QzzfQu27cmVRsG"
```

Defining the endpoint is optional, but allows e.g. an API forwarding proxy behind rigorous firewalls.

User keys and group keys are of course both valid.

see Pushover API docs at https://pushover.net/api

|                 | URL                                                        |
|-----------------|------------------------------------------------------------|
| **Repository:** | https://github.com/jpmschuler/typo3-logwriter-pushover     |
| **TER:**        | https://extensions.typo3.org/extension/logwriter_pushover |
| **Packagist:**  | https://packagist.org/packages/jpmschuler/logwriter-pushover  |
