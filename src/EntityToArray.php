<?php

declare(strict_types=1);

/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is released under MIT license by Niko Granö.
 *
 * @copyright Niko Granö <niko9911@ironlions.fi> (https://granö.fi)
 *
 */

namespace Niko9911\Serializable;

use Niko9911\Serializable\Exception\ConversionException;
use Niko9911\Serializable\Exception\RecursionLimitException;

class EntityToArray
{
    /** @var array */
    protected $result = [];

    /** @var int */
    protected $recursionDepth;

    /** @var bool */
    protected $throwExceptionOnRecursionLimit;

    /** @var bool */
    private $replaceValuesOnRecursionLimit;

    /**
     * @param object $entity                         entity implementing getters to convert to array
     * @param int    $recursionDepth                 Maximum depth to parse. Everything deeper than this will be
     *                                               replaced or exception thrown.
     * @param bool   $throwExceptionOnRecursionLimit will define if we should throw exception in case recursion limit is passed
     * @param bool   $replaceValuesOnRecursionLimit  In case if RecursionLimit is passed and throwing exception is false,
     *                                               should the values of array be replaced? Will replace with `***`.
     *                                               Otherwise value will be original value type of object.
     *
     * @return array
     *
     * @throws ConversionException
     */
    public static function convert(
        object $entity,
        int $recursionDepth = 4,
        bool $throwExceptionOnRecursionLimit = true,
        bool $replaceValuesOnRecursionLimit = true
    ): array {
        try {
            return (
            new self(
                $recursionDepth,
                $throwExceptionOnRecursionLimit,
                $replaceValuesOnRecursionLimit
            )
            )->run($entity);
        } catch (\ReflectionException $e) {
            // @codeCoverageIgnoreStart
            // Have no idea how to trigger this, but pretty sure somebody will.
            throw new ConversionException(
                $entity,
                \get_class($entity),
                $recursionDepth,
                $e->getMessage(),
                $e->getCode(),
                $e
            );
            // @codeCoverageIgnoreStart
        }
    }

    /**
     * EntityToArray constructor.
     *
     * @param int  $recursionDepth
     * @param bool $throwExceptionOnRecursionLimit
     * @param bool $replaceValuesOnRecursionLimit
     */
    protected function __construct(
        int $recursionDepth,
        bool $throwExceptionOnRecursionLimit,
        bool $replaceValuesOnRecursionLimit
    ) {
        $this->recursionDepth = $recursionDepth;
        $this->throwExceptionOnRecursionLimit = $throwExceptionOnRecursionLimit;
        $this->replaceValuesOnRecursionLimit = $replaceValuesOnRecursionLimit;
    }

    /**
     * @param object $ent
     *
     * @return array
     *
     * @throws \ReflectionException
     * @throws ConversionException
     */
    protected function run(object $ent): array
    {
        if (self::isSpecialClass($ent)) {
            return $this->handleSpecial($ent);
        }

        foreach (self::getMethods($ent) as $method) {
            if (0 === \mb_strpos($method->name, 'get')) {
                $name = \lcfirst(\mb_substr($method->name, 3));
                $value = $method->invoke($ent);

                if ($this->recursionDepth > 0) {
                    $this->result[$name] = $this->handleRecursive($value);
                } elseif ($this->throwExceptionOnRecursionLimit) {
                    throw new RecursionLimitException(
                        $ent,
                        \get_class($ent),
                        $this->recursionDepth,
                        'Recursion limit reached!',
                        0
                    );
                } elseif ($this->replaceValuesOnRecursionLimit) {
                    $this->result[$name] = '***';
                } else {
                    $this->result[$name] = $value;
                }
            }
        }

        return $this->result;
    }

    /**
     * @param $value
     *
     * @return mixed
     *
     * @throws ConversionException
     */
    protected function handleRecursive($value)
    {
        if (\is_array($value)) {
            $var = $this->handleRecursiveArray($value);

            return 1 === \count($var) && isset($var[0]) ? $var[0] : $var;
        }

        if (\is_object($value)) {
            $var = $this->handleRecursiveObject($value);

            return 1 === \count($var) && isset($var[0]) ? $var[0] : $var;
        }

        return $value;
    }

    /**
     * @param array $arr
     *
     * @return array
     *
     * @throws ConversionException
     */
    protected function handleRecursiveArray(array $arr): array
    {
        $return = [];
        foreach ($arr as $key => $item) {
            $return[$key] = $this->handleRecursive($item);
        }

        return $return;
    }

    /**
     * @param object $obj
     *
     * @return array
     *
     * @throws ConversionException
     */
    protected function handleRecursiveObject(object $obj): array
    {
        return self::convert(
            $obj,
            --$this->recursionDepth,
            $this->throwExceptionOnRecursionLimit,
            $this->replaceValuesOnRecursionLimit
        );
    }

    /**
     * @param object $ent
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function handleSpecial(object $ent)
    {
        switch (\get_class($ent)) {
            case \DateTimeImmutable::class:
                /* @var \DateTimeImmutable $ent */
                return [$ent->format(DATE_ATOM)];
                break;
            case \DateTimeZone::class:
                /* @var \DateTimeZone $ent */
                /* @noinspection PhpUnhandledExceptionInspection */
                return ['timezone' => $ent->getName(), 'offset' => $ent->getOffset(new \DateTime('now', new \DateTimeZone('UTC')))];
                break;
            default:
                // @codeCoverageIgnoreStart
                // Should not be reached, but good practice to have default.
                return [];
            // @codeCoverageIgnoreSEnd
        }
    }

    /**
     * @param object $e
     *
     * @return \ReflectionMethod[]
     *
     * @throws \ReflectionException
     */
    protected static function getMethods(object $e): array
    {
        return (new \ReflectionClass(\get_class($e)))->getMethods(\ReflectionMethod::IS_PUBLIC);
    }

    /**
     * @param object $e
     *
     * @return bool
     */
    protected static function isSpecialClass(object $e): bool
    {
        switch (\get_class($e)) {
            case \DateTimeZone::class:
            case \DateTimeImmutable::class:
                return true;
            default: return false;
        }
    }
}
