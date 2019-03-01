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

namespace Niko9911\Serializable\Tests\Stubs;

final class Flag
{
    /** @var string */
    private $mainColor;

    /** @var int */
    private $height;

    /** @var int */
    private $width;

    /** @var bool */
    private $registered;

    /** @var array */
    private $options;

    public function __construct(
        string $mainColor,
        int $height,
        int $width,
        bool $registered,
        array $options = []
    ) {
        $this->mainColor = $mainColor;
        $this->height = $height;
        $this->width = $width;
        $this->registered = $registered;
        $this->options = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getMainColor(): string
    {
        return $this->mainColor;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getRegistered(): bool
    {
        return $this->registered;
    }
}
