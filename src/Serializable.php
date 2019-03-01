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

abstract class Serializable implements \JsonSerializable
{
    use SerializableTrait;

    /**
     * Specify data which should be serialized to JSON.
     *
     * @see   https://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource
     *
     * @since 5.4.0
     *
     * @throws Exception\ConversionException
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
