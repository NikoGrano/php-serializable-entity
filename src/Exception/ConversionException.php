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

namespace Niko9911\Serializable\Exception;

use Throwable;

class ConversionException extends Exception
{
    /**
     * @var object
     */
    private $originalObject;
    /**
     * @var string
     */
    private $objectName;
    /**
     * @var int
     */
    private $currentRecursion;

    public function __construct(
        object $originalObject,
        string $objectName,
        int $currentRecursion,
        string $message,
        int $code,
        Throwable $previous = null
    ) {
        $this->originalObject = $originalObject;
        $this->objectName = $objectName;
        $this->currentRecursion = $currentRecursion;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return object
     */
    public function getOriginalObject(): object
    {
        return $this->originalObject;
    }

    /**
     * @return string
     */
    public function getObjectName(): string
    {
        return $this->objectName;
    }

    /**
     * @return int
     */
    public function getCurrentRecursion(): int
    {
        return $this->currentRecursion;
    }
}
