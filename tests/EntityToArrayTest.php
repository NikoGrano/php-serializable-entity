<?php

/** @noinspection UnusedFunctionResultInspection */

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

namespace Niko9911\Serializable\Tests;

use Niko9911\Serializable\EntityToArray;
use Niko9911\Serializable\Exception\RecursionLimitException;
use Niko9911\Serializable\Tests\Stubs\Country;
use Niko9911\Serializable\Tests\Stubs\Flag;
use PHPUnit\Framework\TestCase;

final class EntityToArrayTest extends TestCase
{
    private const NAME = 'Finland';
    private const CODE = 358;
    private const MAIN = 'Blue';
    private const SIZE = [150, 245];
    private const REGI = true;

    private const EXPECT_ARR =
        [
            'name' => self::NAME,
            'id'   => self::CODE,
            'flag' => [
                    'mainColor'  => self::MAIN,
                    'height'     => self::SIZE[0],
                    'width'      => self::SIZE[1],
                    'registered' => self::REGI,
                    'options'    => [],
                ],
        ];

    private const EXPECT_REC_1_ARR =
        [
            'name' => self::NAME,
            'id'   => self::CODE,
            'flag' => [
                'options'    => '***',
                'mainColor'  => '***',
                'height'     => '***',
                'width'      => '***',
                'registered' => '***',
            ],
        ];

    private const EXPECT_JSO = /* @lang JSON */
    '{"name":"Finland","id":358,"flag":{"options":[],"mainColor":"Blue","height":150,"width":245,"registered":true}}';

    private static function getFlag(): Flag
    {
        return new Flag(self::MAIN, self::SIZE[0], self::SIZE[1], self::REGI);
    }

    private static function buildEntity(): Country
    {
        return new Country(self::NAME, self::CODE, self::getFlag());
    }

    /**
     * @throws \Niko9911\Serializable\Exception\ConversionException
     */
    public function testStatic(): void
    {
        $this->assertEquals(self::EXPECT_ARR, EntityToArray::convert(self::buildEntity()));
    }

    /**
     * @throws \Niko9911\Serializable\Exception\ConversionException
     */
    public function testMethodToArray(): void
    {
        $this->assertEquals(self::EXPECT_ARR, self::buildEntity()->toArray());
    }

    /**
     * @throws \Niko9911\Serializable\Exception\ConversionException
     */
    public function testMethodToJson(): void
    {
        $this->assertEquals(self::EXPECT_JSO, self::buildEntity()->toJson());
    }

    public function testJsonSerializableExtend(): void
    {
        $this->assertEquals(self::EXPECT_JSO, \json_encode(self::buildEntity()));
    }

    /**
     * @throws \Niko9911\Serializable\Exception\ConversionException
     */
    public function testMethodRecursionLimitReachedException(): void
    {
        $this->expectException(RecursionLimitException::class);

        EntityToArray::convert(
            self::buildEntity(),
            0,
            true,
            true
        );
    }

    /**
     * @throws \Niko9911\Serializable\Exception\ConversionException
     */
    public function testMethodRecursionLimitReached(): void
    {
        $array = EntityToArray::convert(
            self::buildEntity(),
            1,
            false,
            true
        );

        $this->assertEquals(self::EXPECT_REC_1_ARR, $array);
    }

    /**
     * @throws \Niko9911\Serializable\Exception\ConversionException
     */
    public function testMethodRecursionLimitReachedNoReplace(): void
    {
        $expect =
            [
                'name' => self::NAME,
                'id'   => self::CODE,
                'flag' => [
                    'options' => [
                            'secondary' => [
                                    'options' => [
                                            'sub' => new Flag(
                                                self::MAIN,
                                                self::SIZE[0],
                                                self::SIZE[1],
                                                self::REGI
                                            ),
                                        ],
                                    'mainColor'  => self::MAIN,
                                    'height'     => self::SIZE[0],
                                    'width'      => self::SIZE[1],
                                    'registered' => true,
                                ],
                        ],
                    'mainColor'  => self::MAIN,
                    'height'     => self::SIZE[0],
                    'width'      => self::SIZE[1],
                    'registered' => true,
                ],
            ];

        $entity = new Country(self::NAME, self::CODE, new Flag(
            self::MAIN,
            self::SIZE[0],
            self::SIZE[1],
            self::REGI,
            ['secondary' => new Flag(
                self::MAIN,
                self::SIZE[0],
                self::SIZE[1],
                self::REGI,
                ['sub' => new Flag(
                    self::MAIN,
                    self::SIZE[0],
                    self::SIZE[1],
                    self::REGI
                ),
                ]
            ),
            ]
        ));

        $array = EntityToArray::convert(
            $entity,
            2,
            false,
            false
        );

        $this->assertEquals($expect, $array);
    }

    /**
     * @throws \Niko9911\Serializable\Exception\ConversionException
     * @throws \Exception
     */
    public function testSpecialCaseDateTimeImmutable(): void
    {
        $expected = [
            'name'    => self::NAME,
            'id'      => self::CODE,
            'flag'    => [
                'options'    => [
                        'released' => [
                                'timestamp' => 946684800,
                                'timezone'  => [
                                    'timezone' => 'UTC',
                                    'offset'   => 0,
                                ],
                            ],
                    ],
                'mainColor'  => self::MAIN,
                'height'     => self::SIZE[0],
                'width'      => self::SIZE[1],
                'registered' => true,
            ],
        ];

        $entity = new Country(self::NAME, self::CODE, new Flag(
            self::MAIN,
            self::SIZE[0],
            self::SIZE[1],
            true,
            [
                'released' => new \DateTimeImmutable('2000-01-01', new \DateTimeZone('UTC')),
            ]
        ));

        $this->assertEquals(EntityToArray::convert($entity, 10), $expected);
    }
}
