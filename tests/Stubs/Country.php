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

final class Country extends \Niko9911\Serializable\Serializable
{
    use \Niko9911\Serializable\SerializableTrait;

    /** @var string */
    private $name;

    /** @var int */
    private $id;

    /** @var Flag */
    private $flag;

    public function __construct(string $name, int $id, Flag $flag)
    {
        $this->name = $name;
        $this->id = $id;
        $this->flag = $flag;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFlag(): Flag
    {
        return $this->flag;
    }
}
