<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

use Brain\Monkey\Functions;
use Inpsyde\Config\Exception\UnknownKey;
use Inpsyde\Config\Filter;
use Inpsyde\Config\Helper\SchemaReader;
use Inpsyde\Config\Schema;
use MonkeryTestCase\BrainMonkeyWpTestCase;

class WpOptionTest extends BrainMonkeyWpTestCase
{

    /**
     * @dataProvider hasData
     * @group unit
     */
    public function testHas(string $key, array $definitionForKey, array $mockData, bool $expected)
    {
        $filter = $this->buildFilterMock($mockData, $definitionForKey);
        $schema = $this->buildSchemaMock($key, $definitionForKey);
        $schemaReader = $this->buildSchemaReaderMock($mockData, $key, $schema);

        $optionLoader = function ($optionName, $default) use ($mockData) {
            self::assertSame(
                $optionName,
                $mockData['schemaReader']['sourceName']['return']
            );
            if ($mockData['schemaReader']['hasDefault']['return']) {
                self::assertSame(
                    $mockData['schemaReader']['defaultValue']['return'],
                    $default
                );
            } else {
                self::assertFalse($default);
            }

            // wp_option returns 'false' if there is no value
            return null === $mockData['rawValue']
                ? false
                : $mockData['rawValue'];
        };

        $testee = new WpOption($optionLoader, $schema, $filter, $schemaReader);
        self::assertSame(
            $expected,
            $testee->has($key)
        );
    }

    /**
     * @see testHas
     */
    public function hasData(): array
    {
        return [
            'existing value with default' => [
                'key' => 'some.config.key',
                'definitionForKey' => [
                    /*
                     * It doesn't matter how this array looks like as it is only passed through
                     *  and is expected as parameter for mocks
                     */
                    'Im Just Arbitrary Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => '10',
                    'filter' => [
                        'filterValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                        'validateValue' => [
                            'expect' => 'once',
                            'return' => true,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => '_option_name',
                        ],
                        'hasDefault' => [
                            'return' => true,
                        ],
                        'defaultValue' => [
                            'return' => 11,
                        ],
                    ],
                ],
                'expected' => true,
            ],
            'existing value without default' => [
                'key' => 'some.config.key',
                'definitionForKey' => [
                    'Im Just Arbitrary Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => '10',
                    'filter' => [
                        'filterValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                        'validateValue' => [
                            'expect' => 'once',
                            'return' => true,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => '_option_name',
                        ],
                        'hasDefault' => [
                            'return' => false,
                        ],
                        'defaultValue' => [
                            'return' => null,
                        ],
                    ],
                ],
                'expected' => true,
            ],
            'existing invalid value without default' => [
                'key' => 'some.config.key',
                'definitionForKey' => [
                    'Im Just Arbitrary Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => 'foo',
                    'filter' => [
                        'filterValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                        'validateValue' => [
                            'expect' => 'once',
                            'return' => false,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => '_option_name',
                        ],
                        'hasDefault' => [
                            'return' => false,
                        ],
                        'defaultValue' => [
                            'return' => null,
                        ],
                    ],
                ],
                'expected' => false,
            ],
            'invalid value does not fall back to default value' => [
                'key' => 'some.config.key',
                'definitionForKey' => [
                    'Im Just Arbitrary Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => 'foo',
                    'filter' => [
                        'filterValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                        'validateValue' => [
                            'expect' => 'once',
                            'return' => false,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => '_option_name',
                        ],
                        'hasDefault' => [
                            'return' => true,
                        ],
                        'defaultValue' => [
                            'return' => 10,
                        ],
                    ],
                ],
                'expected' => false,
            ],
            'not existing value falls back to default value' => [
                'key' => 'some.config.key',
                'definitionForKey' => [
                    'Im Just Arbitrary Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => null,
                    'filter' => [
                        'filterValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                        'validateValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => '_option_name',
                        ],
                        'hasDefault' => [
                            'return' => true,
                        ],
                        'defaultValue' => [
                            'return' => 10,
                        ],
                    ],
                ],
                'expected' => true,
            ],
            'not existing value' => [
                'key' => 'some.config.key',
                'definitionForKey' => [
                    'Im Just Arbitrary Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => null,
                    'filter' => [
                        'filterValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                        'validateValue' => [
                            'expect' => 'never',
                            'return' => true,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => '_option_name',
                        ],
                        'hasDefault' => [
                            'return' => false,
                        ],
                        'defaultValue' => [
                            'return' => null,
                        ],
                    ],
                ],
                'expected' => false,
            ],
        ];
    }

    /**
     * @dataProvider getData
     * @group unit
     */
    public function testGet(string $key, array $definitionForKey, array $mockData, $expected)
    {
        $filter = $this->buildFilterMock($mockData, $definitionForKey);
        $schema = $this->buildSchemaMock($key, $definitionForKey);
        $schemaReader = $this->buildSchemaReaderMock($mockData, $key, $schema);

        $optionLoader = function ($optionName) use ($mockData) {
            self::assertSame(
                $optionName,
                $mockData['schemaReader']['sourceName']['return']
            );

            // wp_option returns 'false' if there is no value
            return null === $mockData['rawValue']
                ? false
                : $mockData['rawValue'];
        };

        $testee = new WpOption($optionLoader, $schema, $filter, $schemaReader);
        self::assertSame(
            $expected,
            $testee->get($key)
        );
    }

    /**
     * @see testGet
     */
    public function getData(): array
    {
        return [
            'existing value with default' => [
                'key' => 'some.config.key',
                'definitionForKey' => [
                    /*
                     * It doesn't matter how this array looks like as it is only passed through
                     *  and is expected as parameter for mocks
                     */
                    'Im Just Arbitrary Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => '10',
                    'filter' => [
                        'filterValue' => [
                            'expect' => 'once',
                            'return' => 10,
                        ],
                        'validateValue' => [
                            'expect' => 'once',
                            'return' => true,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => '_option_name',
                        ],
                        'hasDefault' => [
                            'return' => true,
                        ],
                        'defaultValue' => [
                            'return' => 9,
                        ],
                    ],
                ],
                'expected' => 10,
            ],
            'existing value without default' => [
                'key' => 'some.config.key',
                'definitionForKey' => [
                    'Im Just Arbitrary Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => '10.01',
                    'filter' => [
                        'filterValue' => [
                            'expect' => 'once',
                            'return' => 10.01,
                        ],
                        'validateValue' => [
                            'expect' => 'once',
                            'return' => true,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => '_option_name',
                        ],
                        'hasDefault' => [
                            'return' => false,
                        ],
                        'defaultValue' => [
                            'return' => null,
                        ],
                    ],
                ],
                'expected' => 10.01,
            ],
            'not existing value returns default' => [
                'key' => 'some.config.key',
                'definitionForKey' => [
                    'Im Just Arbitrary Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => null,
                    'filter' => [
                        'filterValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                        'validateValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => '_option_name',
                        ],
                        'hasDefault' => [
                            'return' => true,
                        ],
                        'defaultValue' => [
                            'return' => 9.0,
                        ],
                    ],
                ],
                'expected' => 9.0,
            ],
        ];
    }

    /**
     * @group unit
     */
    public function testGetThrowsMissingConfigException()
    {
        $schema = \Mockery::mock(Schema::class);
        $filter = \Mockery::mock(Filter::class);
        $key = 'not.a.config';

        $schema->shouldReceive('getDefinition')
            ->with($key)
            ->andReturn([]);

        self::expectException(UnknownKey::class);

        (new WpOption(
            function () {
            }, $schema, $filter
        ))
            ->get($key);
    }

    /**
     * @group unit
     */
    public function testAsWpOption()
    {
        $key = 'site.home_url';
        $sourceName = 'home';
        $defaultValue = 'http://localhost';
        $expected = 'http://my.site';
        $definition = [
            'source' => Source::SOURCE_WP_OPTION,
            'source_name' => 'home',
            'default_value' => $defaultValue,
        ];

        $schema = \Mockery::mock(Schema::class);
        $filter = \Mockery::mock(Filter::class);

        $schema->shouldReceive('getDefinition')
            ->with($key)
            ->andReturn($definition);

        $filter->shouldReceive(
            [
                'validateValue' => true,
                'filterValue' => $expected,
            ]
        );

        Functions\expect('get_option')
            ->once()
            ->with($sourceName, $defaultValue)
            ->andReturn($expected);

        self::assertSame(
            $expected,
            (WpOption::asWpOption($schema, $filter))->get($key)
        );
    }

    /**
     * @group unit
     */
    public function testAsWpSiteOption()
    {
        $key = 'site.home_url';
        $sourceName = 'home';
        $defaultValue = 'http://localhost';
        $expected = 'http://my.site';
        $definition = [
            'source' => Source::SOURCE_WP_OPTION,
            'source_name' => 'home',
            'default_value' => $defaultValue,
        ];

        $schema = \Mockery::mock(Schema::class);
        $filter = \Mockery::mock(Filter::class);

        $schema->shouldReceive('getDefinition')
            ->with($key)
            ->andReturn($definition);

        $filter->shouldReceive(
            [
                'validateValue' => true,
                'filterValue' => $expected,
            ]
        );

        Functions\expect('get_site_option')
            ->once()
            ->with($sourceName, $defaultValue)
            ->andReturn($expected);

        self::assertSame(
            $expected,
            (WpOption::asWpSiteOption($schema, $filter))->get($key)
        );
    }

    private function buildSchemaMock(string $key, array $definitionForKey): Schema
    {
        $schema = \Mockery::mock(Schema::class);
        $schema->shouldReceive('getDefinition')
            ->with($key)
            ->andReturn($definitionForKey);

        /* @var Schema $schema */
        return $schema;
    }

    private function buildFilterMock(array $mockData, array $definitionForKey): Filter
    {
        $filter = \Mockery::mock(Filter::class);
        $filter->shouldReceive('validateValue')
            ->{$mockData['filter']['validateValue']['expect']}()
            ->with($mockData['rawValue'], $definitionForKey)
            ->andReturn($mockData['filter']['validateValue']['return']);
        $filter->shouldReceive('filterValue')
            ->{$mockData['filter']['filterValue']['expect']}()
            ->with($mockData['rawValue'], $definitionForKey)
            ->andReturn($mockData['filter']['filterValue']['return']);

        /* @var Filter $filter */
        return $filter;
    }

    private function buildSchemaReaderMock(array $mockData, string $key, Schema $schema): SchemaReader
    {
        $reader = \Mockery::mock(SchemaReader::class);
        $reader->shouldReceive('sourceName')
            ->with($key, $schema)
            ->andReturn($mockData['schemaReader']['sourceName']['return']);
        $reader->shouldReceive('hasDefault')
            ->with($key, $schema)
            ->andReturn($mockData['schemaReader']['hasDefault']['return']);
        $reader->shouldReceive('defaultValue')
            ->with($key, $schema)
            ->andReturn($mockData['schemaReader']['defaultValue']['return']);

        /* @var SchemaReader $reader */
        return $reader;
    }
}
