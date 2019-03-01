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

trait SerializableTrait
{
    /**
     * @return array
     *
     * @throws Exception\ConversionException
     */
    public function toArray(): array
    {
        return EntityToArray::convert($this);
    }

    /**
     * @return string Returns current object as JSON
     *
     * @throws Exception\ConversionException
     */
    public function toJson(): string
    {
        return \json_encode($this->toArray());
    }
}
