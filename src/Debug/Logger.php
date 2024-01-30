<?php

namespace Thor\Common\Debug;

use DateTime;
use Exception;
use Thor\Common\Guid;
use Thor\Common\Types\Strings;
use Throwable;
use Stringable;
use JsonException;

/**
 * Logger class for Thor.
 * Provides a static context to have a unique global Logger object.
 *
 * @package          Thor/Debug
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Logger implements LoggerInterface
{

    public const string DEFAULT_BASE_PATH = __DIR__ . '/../../var/';
    public const string DEFAULT_DATE_FORMAT = 'Y-m-d H:i:s.v';

    private static ?self $logger = null;

    /**
     * @param LogLevel    $minimumLogLevel
     * @param string      $dateFormat
     * @param string|null $path
     *
     * @throws Exception
     */
    public function __construct(
        public readonly LogLevel $minimumLogLevel = LogLevel::DEBUG,
        public readonly string $dateFormat = Logger::DEFAULT_DATE_FORMAT,
        private ?string $path = null,
    ) {
        $this->path ??= self::DEFAULT_BASE_PATH . Guid::hex() . '.log';
    }

    /**
     * Sets the static Logger.
     *
     * @throws Exception
     */
    public static function set(
        LogLevel $level = LogLevel::DEBUG,
        string $basePath = Logger::DEFAULT_BASE_PATH,
        string $dateFormat = Logger::DEFAULT_DATE_FORMAT,
    ): self {
        return self::$logger = new self($level, $dateFormat, $basePath . Guid::hex() . '.log');
    }

    /**
     * Logs a Throwable : the message is written with an LogLevel::ERROR level.
     * The details are written with the LogLevel::DEBUG level.
     */
    public static function logThrowable(Throwable $e): string
    {
        $pad = str_repeat(' ', 38);
        $traceStr = '';

        foreach (array_reverse($e->getTrace()) as $trace) {
            $file = $trace['file'] ?? '';
            $line = $trace['line'] ?? '';
            $location = "$file:$line";
            if ($location === ':') {
                $location = 'unknown';
            }
            $traceStr .= "$pad • @$location\n";

            $traceClass = $trace['class'] ?? null;
            $traceObject = $trace['object'] ?? null;
            $traceFunction = $trace['function'] ?? '';
            $traceType = $trace['type'] ?? '';
            $traceArgs = $trace['args'] ?? [];
            if ($traceClass !== null) {
                $traceStr .= "$pad • $traceType $traceClass::$traceFunction";
            } else {
                $traceStr .= "$pad • $traceType $traceFunction";
            }
            if (!empty($traceArgs)) {
                $traceStr .= '(' . implode(', ', $traceArgs) . ')';
            }
            if ($traceObject !== null) {
                "$pad " . json_encode($traceObject) . "\n";
            }
            $traceStr .= "\n\n";
        }

        self::write(
            "ERROR THROWN IN FILE {$e->getFile()} :  {$e->getLine()}\n$pad{$e->getMessage()}",
            LogLevel::ERROR
        );
        $message = <<<EOT
            Chronological trace :
            $traceStr                 
            EOT;
        self::write($message, LogLevel::DEBUG);

        return $message;
    }

    /**
     * Writes a message with the static Logger.
     */
    public static function write(
        Stringable|string $message,
        LogLevel $level = LogLevel::NOTICE,
        array $context = [],
        bool $print = false
    ): void {
        self::get()->log($level, $message, $context);
        if ($print) {
            echo Strings::interpolate($message, $context) . "\n";
        }
    }

    /**
     * @inheritDoc
     */
    public function log(LogLevel $level, Stringable|string $message, array $context = []): void
    {
        if ($level->value >= $this->minimumLogLevel->value) {
            $strLevel = str_pad($level->name, 10);
            $now = new DateTime();
            $nowStr = $now->format($this->dateFormat);
            $message = "$nowStr $strLevel : " . Strings::interpolate($message, $context);

            try {
                Folder::createIfNotExists(dirname($this->path));
                file_put_contents($this->path, "$message\n", FILE_APPEND);
            } catch (Throwable $t) {
                echo "LOGGER ERROR : {$t->getMessage()}\n";
            }
        }
    }

    /**
     * Gets the current static Logger.
     *
     * @throws Exception
     */
    public static function get(
        LogLevel $level = LogLevel::INFO,
        string $basePath = __DIR__ . '/../',
        string $dateFormat = 'Y-m-d H:i:s.v'
    ): self {
        return Logger::$logger ??= Logger::set($level, $dateFormat, $basePath);
    }

    /**
     * Writes data encoded into JSON with the static Logger.
     */
    public static function writeDebug(string $label, mixed $data, LogLevel $level = LogLevel::DEBUG): void
    {
        try {
            $message = "DEBUG : $label= " . json_encode($data, JSON_THROW_ON_ERROR);
            Logger::get()->log($level, $message);
        } catch (JsonException $e) {
            echo "LOGGER ERROR : {$e->getMessage()}\n";
        }
    }

    /**
     * @inheritDoc
     */
    public function emergency(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function alert(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function critical(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function error(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function warning(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function notice(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function info(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function debug(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
}
