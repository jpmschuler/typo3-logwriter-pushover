<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Jpmschuler\LogwriterPushover\Log\Writer;

use RuntimeException;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Log\Writer\AbstractWriter;
use TYPO3\CMS\Core\Log\Writer\WriterInterface;

/**
 * Log writer that writes the log records into a file.
 */
class PushoverWriter extends AbstractWriter
{
    /**
     * Log file path, relative to TYPO3's base project folder
     */
    protected string $apiEndpoint = '';

    protected const DEFAULT_API_ENDPOINT = 'https://api.pushover.net/1/messages.json';

    protected string $apiToken = '';

    protected string $userKey = '';

    /**
     * Constructor, opens the log file handle
     * @param array<string, string> $options
     */
    public function __construct(array $options = [])
    {
        // the parent constructor reads $options and sets them
        parent::__construct($options);
        if (empty($options['apiEndpoint'])) {
            if (getenv('PUSHOVER_API_ENDPOINT')) {
                $this->setApiEndpoint(getenv('PUSHOVER_API_ENDPOINT'));
            } else {
                $this->setApiEndpoint(self::DEFAULT_API_ENDPOINT);
            }
        }
        if (empty($options['apiToken']) && getenv('PUSHOVER_API_TOKEN')) {
            $this->setApiToken(getenv('PUSHOVER_API_TOKEN'));
        }
        if (empty($options['userKey']) && getenv('PUSHOVER_USER_KEY')) {
            $this->setApiToken(getenv('PUSHOVER_USER_KEY'));
        }
        $this->checkConfig();
    }

    public function checkConfig(): void
    {
        $configErrors = [];
        if (!$this->getApiEndpoint()) {
            $configErrors[] = 'apiEndpoint URL not set';
        }
        if (!$this->getApiToken()) {
            $configErrors[] = 'apiToken not set';
        }
        if (!$this->getUserKey()) {
            $configErrors[] = 'userKey not set';
        }
        if (count($configErrors)) {
            throw new RuntimeException(
                'LogWriter config for Pushover is incomplete: ' . implode(', ', $configErrors),
                1678192349240
            );
        }
    }

    protected function perpareMessageFromLogRecord(LogRecord $record): string
    {
        $data = '';
        $context = $record->getData();
        $message = $record->getMessage();
        if (!empty($context)) {
            // Fold an exception into the message, and string-ify it into context so it can be jsonified.
            if (isset($context['exception']) && $context['exception'] instanceof \Throwable) {
                $message .= $this->formatException($context['exception']);
                $context['exception'] = (string)$context['exception'];
            }
            $data = '- ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $message = sprintf(
            '%s [%s] request="%s" component="%s": %s %s',
            date('r', (int)$record->getCreated()),
            strtoupper($record->getLevel()),
            $record->getRequestId(),
            $record->getComponent(),
            $this->interpolate($message, $context),
            $data
        );
        return $message;
    }

    protected function perpareTitleFromLogRecord(LogRecord $record): string
    {
        return strtoupper($record->getLevel()) . ' at ' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'];
    }

    /**
     * Writes the log record
     *
     * @param LogRecord $record Log record
     * @return WriterInterface $this
     * @throws RuntimeException
     */
    public function writeLog(LogRecord $record)
    {
        $curlErrorMessage = null;
        $postParameter = [
            'token' => $this->getApiToken(),
            'user' => $this->getUserKey(),
            'message' => $this->perpareMessageFromLogRecord($record),
            'title' => $this->perpareTitleFromLogRecord($record)
        ];
        $curlHandle = curl_init($this->getApiEndpoint());
        if ($curlHandle === false) {
            $curlErrorMessage = 'Could not send log entry to Pushover, cURL could not init API endpoint.';
            throw new RuntimeException(
                $curlErrorMessage,
                1678196258319
            );
        }
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $postParameter);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);

        $curlResponse = curl_exec($curlHandle);
        if (curl_errno($curlHandle)) {
            $curlErrorMessage = 'Could not send log entry to Pushover, cURL error: ' . curl_error($curlHandle);
        }
        curl_close($curlHandle);

        if ($curlErrorMessage) {
            throw new RuntimeException(
                $curlErrorMessage,
                1678195758284
            );
        }
        return $this;
    }

    protected function getApiEndpoint(): string
    {
        return $this->apiEndpoint;
    }

    protected function getUserKey(): string
    {
        return $this->userKey;
    }

    protected function getApiToken(): string
    {
        return $this->apiToken;
    }

    protected function setApiEndpoint(string $value): void
    {
        $this->apiEndpoint = $value;
    }

    protected function setUserKey(string $value): void
    {
        $this->userKey = $value;
    }

    protected function setApiToken(string $value): void
    {
        $this->apiToken = $value;
    }
}
