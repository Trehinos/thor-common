<?php

namespace Thor\Common\Configuration;

use Symfony\Component\Yaml\Yaml;

class ConfigurationFromFile extends Configuration
{

    private static array $configurations = [];

    /**
     * @param mixed ...$args
     *
     * @return static
     */
    final public static function get(mixed ...$args): static
    {
        return static::$configurations[static::class] ??= new static(...$args);
    }

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        parent::__construct(self::loadYml($path));
    }

    /**
     * Gets the configuration from a file in the config folder.
     *
     * @param string $path
     *
     * @return static
     */
    public static function fromFile(string $path): static
    {
        return new static($path);
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public static function loadYml(string $path): array
    {
        return Yaml::parseFile("$path.yml");
    }

    /**
     * @param Configuration $configuration
     * @param string        $path
     *
     * @return bool
     */
    public static function writeTo(Configuration $configuration, string $path): bool
    {
        $str = Yaml::dump(
            $configuration->getArrayCopy(),
            6,
            flags: Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK | Yaml::DUMP_NULL_AS_TILDE
        );
        return file_put_contents($path, $str) !== false;
    }

    public function write(string $path): bool
    {
        return self::writeTo($this, $path);
    }

}
