<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

use Inpsyde\Config\Exception\UnknownKey;
use Inpsyde\Config\Filter;
use Inpsyde\Config\Helper\SchemaReader;
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
    public function testHas(string $key, array $definitionForKey, array $mockData, bool $expected)
    {
        $filter = $this->buildFilterMock($mockData, $definitionForKey);
        $schema = $this->buildSchemaMock($key, $definitionForKey);
        $schemaReader = $this->buildSchemaReaderMock($mockData, $key, $schema);

        if (null !== $mockData['rawValue']) {
            define($mockData['schemaReader']['sourceName']['return'], $mockData['rawValue']);
        }

        self::assertSame(
            $expected,
            (new Constant($schema, $filter, $schemaReader))->has($key)
        );
    }

    /**
     * @see testHas
     */
    public function hasData(): array
    {
        return [
            'existing value with default' => [
                'key' => 'some.constant.config',
                'definition' => [
                    /*
                     * It doesn't matter how this array looks like as it is only passed through
                     *  and is expected as parameter for mocks
                     */
                    'Im Just Random Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => 'https://some.url',
                    'filter' => [
                        'validateValue' => [
                            'expect' => 'once',
                            'return' => true,
                        ],
                        'filterValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => 'CONSTANT_A',
                        ],
                        'hasDefault' => [
                            'return' => true,
                        ],
                        'defaultValue' => [
                            'return' => 'https://default.url',
                        ],
                    ],
                ],
                'expected' => true,
            ],
            'existing value without default' => [
                'key' => 'some.constant.config',
                'definition' => [
                    /*
                     * It doesn't matter how this array looks like as it is only passed through
                     *  and is expected as parameter for mocks
                     */
                    'Im Just Random Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => 'https://some.url',
                    'filter' => [
                        'validateValue' => [
                            'expect' => 'once',
                            'return' => true,
                        ],
                        'filterValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => 'CONSTANT_A',
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
                'key' => 'some.constant.config',
                'definition' => [
                    /*
                     * It doesn't matter how this array looks like as it is only passed through
                     *  and is expected as parameter for mocks
                     */
                    'Im Just Random Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => 'not-a-valid-number',
                    'filter' => [
                        'validateValue' => [
                            'expect' => 'once',
                            'return' => false,
                        ],
                        'filterValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => 'CONSTANT_A',
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
            'invalid value does not fall back to default' => [
                'key' => 'some.constant.config',
                'definition' => [
                    /*
                     * It doesn't matter how this array looks like as it is only passed through
                     *  and is expected as parameter for mocks
                     */
                    'Im Just Random Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => 'not-a-valid-number',
                    'filter' => [
                        'validateValue' => [
                            'expect' => 'once',
                            'return' => false,
                        ],
                        'filterValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => 'CONSTANT_A',
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
            'not existing value falls back to default' => [
                'key' => 'some.constant.config',
                'definition' => [
                    /*
                     * It doesn't matter how this array looks like as it is only passed through
                     *  and is expected as parameter for mocks
                     */
                    'Im Just Random Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => null,
                    'filter' => [
                        'validateValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                        'filterValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => 'CONSTANT_A',
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
            'not existing value without default' => [
                'key' => 'some.constant.config',
                'definition' => [
                    /*
                     * It doesn't matter how this array looks like as it is only passed through
                     *  and is expected as parameter for mocks
                     */
                    'Im Just Random Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => null,
                    'filter' => [
                        'validateValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                        'filterValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => 'CONSTANT_A',
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
     */
    public function testGet(string $key, array $definitionForKey, array $mockData, $expected)
    {
        $filter = $this->buildFilterMock($mockData, $definitionForKey);
        $schema = $this->buildSchemaMock($key, $definitionForKey);
        $schemaReader = $this->buildSchemaReaderMock($mockData, $key, $schema);

        if (null !== $mockData['rawValue']) {
            define($mockData['schemaReader']['sourceName']['return'], $mockData['rawValue']);
        }

        self::assertSame(
            $expected,
            (new Constant($schema, $filter, $schemaReader))->get($key)
        );
    }

    /**
     * @see testGet
     */
    public function getData(): array
    {
        return [
            'existing value with default' => [
                'key' => 'some.constant.config',
                'definitionForKey' => [
                    /*
                    * It doesn't matter how this array looks like as it is only passed through
                    *  and is expected as parameter for mocks
                    */
                    'Im Just Random Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => '10',
                    'filter' => [
                        'validateValue' => [
                            'expect' => 'once',
                            'return' => true,
                        ],
                        'filterValue' => [
                            'expect' => 'once',
                            'return' => 10,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => 'CONSTANT_A',
                        ],
                        'hasDefault' => [
                            'return' => true,
                        ],
                        'defaultValue' => [
                            'return' => 5,
                        ],
                    ],
                ],
                'expected' => 10,
            ],
            'existing value without default' => [
                'key' => 'some.constant.config',
                'definition' => [
                    'Im Just Random Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => 'http://some.url',
                    'filter' => [
                        'validateValue' => [
                            'expect' => 'once',
                            'return' => true,
                        ],
                        'filterValue' => [
                            'expect' => 'once',
                            'return' => 'http://some.url',
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => 'CONSTANT_A',
                        ],
                        'hasDefault' => [
                            'return' => false,
                        ],
                        'defaultValue' => [
                            'return' => null,
                        ],
                    ],
                ],
                'expected' => "http://some.url",
            ],
            'not existing value with default' => [
                'key' => 'some.constant.config',
                'definition' => [
                    'Im Just Random Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => null,
                    'filter' => [
                        'validateValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                        'filterValue' => [
                            'expect' => 'never',
                            'return' => null,
                        ],
                    ],
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => 'CONSTANT_A',
                        ],
                        'hasDefault' => [
                            'return' => true,
                        ],
                        'defaultValue' => [
                            'return' => 10.01,
                        ],
                    ],
                ],
                'expected' => 10.01,
            ],
        ];
    }

    public function testGetThrowsException()
    {
        $schema = \Mockery::mock(
            Schema::class,
            [
                'getDefinition' => [],
            ]
        );
        self::expectException(UnknownKey::class);

        (new Constant($schema))
            ->get('not.me');
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
