<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

use Inpsyde\Config\Exception\MissingConfig;
use Inpsyde\Config\Filter;
use Inpsyde\Config\Schema;
use MonkeryTestCase\BrainMonkeyWpTestCase;

/**
 * @runTestsInSeparateProcesses
 */
class ConstantTest extends BrainMonkeyWpTestCase
{

    /**
     * @dataProvider hasData
     */
    public function testHas(array $definition, string $key, array $mockData, bool $expected)
    {
        $schema = \Mockery::mock(Schema::class);
        $schema->shouldReceive('getDefinition')
            ->with($key)
            ->andReturn($definition[$key]);

        $filter = \Mockery::mock(Filter::class);
        $filter->shouldReceive('validateValue')
            ->with($mockData['value'], $definition[$key])
            ->andReturn($mockData['filter']['return']);

        if ($mockData['constant']['define']) {
            define($definition[$key]['source_name'], $mockData['value']);
        }

        self::assertSame(
            $expected,
            (new Constant($schema, $filter))->has($key)
        );
    }

    /**
     * @see testHas
     */
    public function hasData(): array
    {
        return [
            'defined expects true' => [
                'definition' => [
                    'some.constant.config' => [
                        'source_name' => 'CONSTANT_A',
                    ],
                ],
                'key' => 'some.constant.config',
                'mockData' => [
                    'value' => 10,
                    'filter' => [
                        'return' => true,
                    ],
                    'constant' => [
                        'define' => true,
                    ],
                ],
                'expected' => true,
            ],
            'not defined expects false' => [
                'definition' => [
                    'some.constant.config' => [
                        'source_name' => 'CONSTANT_A',
                    ],
                ],
                'key' => 'some.constant.config',
                'mockData' => [
                    'value' => null,
                    'filter' => [
                        'return' => null,
                    ],
                    'constant' => [
                        'define' => false,
                    ],
                ],
                'expected' => false,
            ],
            'not defined default value expects true' => [
                'definition' => [
                    'some.constant.config' => [
                        'source_name' => 'CONSTANT_A',
                        'default_value' => 10,
                    ],
                ],
                'key' => 'some.constant.config',
                'mockData' => [
                    'value' => null,
                    'filter' => [
                        'return' => null,
                    ],
                    'constant' => [
                        'define' => false,
                    ],
                ],
                'expected' => true,
            ],
            'defined invalid expects false' => [
                'definition' => [
                    'some.constant.config' => [
                        'source_name' => 'CONSTANT_A',
                        'default_value' => 10,
                    ],
                ],
                'key' => 'some.constant.config',
                'mockData' => [
                    'value' => 0,
                    'filter' => [
                        'return' => false,
                    ],
                    'constant' => [
                        'define' => true,
                    ],
                ],
                'expected' => false,
            ],
        ];
    }

    /**
     * @dataProvider getData
     */
    public function testGet(array $definition, string $key, array $mockData, $expected)
    {
        $schema = \Mockery::mock(Schema::class);
        $schema->shouldReceive('getDefinition')
            ->with($key)
            ->andReturn($definition[$key]);

        $filter = \Mockery::mock(Filter::class);
        $filter->shouldReceive('validateValue')
            ->{$mockData['filter']['validate']['expected']}()
            ->with($mockData['value'], $definition[$key])
            ->andReturn($mockData['filter']['validate']['return']);
        $filter->shouldReceive('filterValue')
            ->{$mockData['filter']['filter']['expected']}()
            ->with($mockData['value'], $definition[$key])
            ->andReturn($mockData['filter']['filter']['return']);

        if ($mockData['constant']['define']) {
            define($definition[$key]['source_name'], $mockData['value']);
        }

        self::assertSame(
            $expected,
            (new Constant($schema, $filter))->get($key)
        );
    }

    /**
     * @see testGet
     */
    public function getData(): array
    {
        return [
            'defined expects value' => [
                'definition' => [
                    'some.constant.config' => [
                        'source_name' => 'CONSTANT_A',
                    ],
                ],
                'key' => 'some.constant.config',
                'mockData' => [
                    'value' => 10,
                    'filter' => [
                        'validate' => [
                            'expected' => 'once',
                            'return' => true,
                        ],
                        'filter' => [
                            'expected' => 'once',
                            'return' => 10,
                        ],
                    ],
                    'constant' => [
                        'define' => true,
                    ],
                ],
                'expected' => 10,
            ],
            'not defined expects default' => [
                'definition' => [
                    'some.constant.config' => [
                        'source_name' => 'CONSTANT_A',
                        'default_value' => "http://some.url",
                    ],
                ],
                'key' => 'some.constant.config',
                'mockData' => [
                    'value' => null,
                    'filter' => [
                        'validate' => [
                            'expected' => 'never',
                            'return' => null,
                        ],
                        'filter' => [
                            'expected' => 'never',
                            'return' => null,
                        ],
                    ],
                    'constant' => [
                        'define' => false,
                    ],
                ],
                'expected' => "http://some.url",
            ],
        ];
    }

    public function testGetThrowsException()
    {
        $schema = \Mockery::mock(
            Schema::class,
            [
                'getDefinition' => []
            ]
        );
        self::expectException(MissingConfig::class);

        (new Constant($schema))
            ->get('not.me');
    }
}
