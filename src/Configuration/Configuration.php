<?php

namespace Thor\Common\Configuration;

use ArrayObject;

class Configuration extends ArrayObject
{
    protected static array $configurations = [];

    /**
     * @param array $configArray
     */
    public function __construct(array $configArray = [])
    {
        parent::__construct($configArray, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @param Configuration $configuration
     *
     * @return $this
     */
    public function merge(Configuration $configuration): static
    {
        foreach ($configuration as $key => $value) {
            $this[$key] = $value;
        }
        return $this;
    }

    /**
     * @param mixed ...$args
     *
     * @return static
     */
    public static function get(mixed ...$args): static
    {
        return static::$configurations[static::class] ??= new static(...$args);
    }

}

