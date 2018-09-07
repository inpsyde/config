<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

use Inpsyde\Config\Exception\UnknownKey;
use Inpsyde\Config\Exception\MissingValue;
use Inpsyde\Config\Filter;
use Inpsyde\Config\Helper\SchemaReader;
use Inpsyde\Config\Schema;
use MonkeryTestCase\BrainMonkeyWpTestCase;

class VariableTest extends BrainMonkeyWpTestCase
{

    /**
     * @dataProvider hasData
     * @group unit
     */
    public function testHas(string $key, array $mockData, bool $expected)
    {
        /**
         * It doesn't matter how this array looks like as it is only passed through
         *  and is expected as parameter for mocks
         */
        $definitionForKey = ['just arbitrary data', 'really'];
        $filter = $this->buildFilterMock($mockData, $definitionForKey);
        $schema = $this->buildSchemaMock($key, $definitionForKey, $mockData);
        $schemaReader = $this->buildSchemaReaderMock($mockData, $key, $schema);

        self::assertSame(
            $expected,
            (new Variable($schema, $mockData['config'], $filter, $schemaReader))->has($key)
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
                'mockData' => [
                    'rawValue' => '5.5',
                    'config' => [
                        'some.config.key' => '5.5',
                    ],
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
                    'schema' => [
                        'getKeys' => [
                            'return' => ['some.config.key'],
                        ],
                    ],
                    'schemaReader' => [
                        'hasDefault' => [
                            'return' => true,
                        ],
                        'defaultValue' => [
                            'return' => 10.1,
                        ],
                    ],
                ],
                'expected' => true,
            ],
            'existing value without default' => [
                'key' => 'some.config.key',
                'mockData' => [
                    'rawValue' => '5.5',
                    'config' => [
                        'some.config.key' => '5.5',
                    ],
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
                    'schema' => [
                        'getKeys' => [
                            'return' => ['some.config.key'],
                        ],
                    ],
                    'schemaReader' => [
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
                'mockData' => [
                    'rawValue' => 'foo',
                    'config' => [
                        'some.config.key' => 'foo',
                    ],
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
                    'schema' => [
                        'getKeys' => [
                            'return' => ['some.config.key'],
                        ],
                    ],
                    'schemaReader' => [
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
                'mockData' => [
                    'rawValue' => 'foo',
                    'config' => [
                        'some.config.key' => 'foo',
                    ],
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
                    'schema' => [
                        'getKeys' => [
                            'return' => ['some.config.key'],
                        ],
                    ],
                    'schemaReader' => [
                        'hasDefault' => [
                            'return' => true,
                        ],
                        'defaultValue' => [
                            'return' => 10.01,
                        ],
                    ],
                ],
                'expected' => false,
            ],
            'not existing value fall back to default value' => [
                'key' => 'some.config.key',
                'mockData' => [
                    'rawValue' => null,
                    'config' => [],
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
                    'schema' => [
                        'getKeys' => [
                            'return' => [],
                        ],
                    ],
                    'schemaReader' => [
                        'hasDefault' => [
                            'return' => true,
                        ],
                        'defaultValue' => [
                            'return' => 10.1,
                        ],
                    ],
                ],
                'expected' => true,
            ],
            'not existing value without default' => [
                'key' => 'some.config.key',
                'mockData' => [
                    'rawValue' => null,
                    'config' => [],
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
                    'schema' => [
                        'getKeys' => [
                            'return' => [],
                        ],
                    ],
                    'schemaReader' => [
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
    public function testGet(string $key, array $mockData, $expected)
    {
        /**
         * It doesn't matter how this array looks like as it is only passed through
         *  and is expected as parameter for mocks
         */
        $definitionForKey = ['just arbitrary data', 'really'];
        $filter = $this->buildFilterMock($mockData, $definitionForKey);
        $schema = $this->buildSchemaMock($key, $definitionForKey, $mockData);
        $schemaReader = $this->buildSchemaReaderMock($mockData, $key, $schema);

        self::assertSame(
            $expected,
            (new Variable($schema, $mockData['config'], $filter, $schemaReader))->get($key)
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
                'mockData' => [
                    'rawValue' => '5.5',
                    'config' => [
                        'some.config.key' => '5.5',
                    ],
                    'filter' => [
                        'filterValue' => [
                            'expect' => 'once',
                            'return' => 5.5,
                        ],
                        'validateValue' => [
                            'expect' => 'once',
                            'return' => true,
                        ],
                    ],
                    'schema' => [
                        'getKeys' => [
                            'return' => ['some.config.key'],
                        ],
                    ],
                    'schemaReader' => [
                        'hasDefault' => [
                            'return' => true,
                        ],
                        'defaultValue' => [
                            'return' => 10.01,
                        ],
                    ],
                ],
                'expected' => 5.5,
            ],
            'existing value without default' => [
                'key' => 'some.config.key',
                'mockData' => [
                    'rawValue' => '5.5',
                    'config' => [
                        'some.config.key' => '5.5',
                    ],
                    'filter' => [
                        'filterValue' => [
                            'expect' => 'once',
                            'return' => 5.5,
                        ],
                        'validateValue' => [
                            'expect' => 'once',
                            'return' => true,
                        ],
                    ],
                    'schema' => [
                        'getKeys' => [
                            'return' => ['some.config.key'],
                        ],
                    ],
                    'schemaReader' => [
                        'hasDefault' => [
                            'return' => false,
                        ],
                        'defaultValue' => [
                            'return' => null,
                        ],
                    ],
                ],
                'expected' => 5.5,
            ],
            'not existing value returns default' => [
                'key' => 'some.config.key',
                'mockData' => [
                    'rawValue' => null,
                    'config' => [],
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
                    'schema' => [
                        'getKeys' => [
                            'return' => ['some.config.key'],
                        ],
                    ],
                    'schemaReader' => [
                        'hasDefault' => [
                            'return' => true,
                        ],
                        'defaultValue' => [
                            'return' => 10.1,
                        ],
                    ],
                ],
                'expected' => 10.1,
            ],
        ];
    }

    /**
     * @group unit
     */
    public function testGetThrowsException()
    {
        $validKey = 'some.config.key';
        $invalidKey = 'does.not.exist.here';
        $filter = \Mockery::mock(Filter::class);

        $schema = \Mockery::mock(Schema::class);
        $schema->shouldReceive('getKeys')
            ->with(Source::SOURCE_VARIABLE)
            ->andReturn([$validKey]);

        $schemaReader = \Mockery::mock(SchemaReader::class);
        $schemaReader->shouldReceive('hasDefault')
            ->atLeast()->once()
            ->with($invalidKey, $schema)
            ->andReturn(false);

        self::expectException(UnknownKey::class);

        (new Variable($schema, [$validKey => true], $filter, $schemaReader))
            ->get($invalidKey);
    }

    /**
     * @group unit
     */
    public function testConstructor()
    {
        $keys = ['key.with.default', 'key.without.default'];
        $config = [
            'key.without.default' => 'foo',
        ];
        $schema = \Mockery::mock(Schema::class);
        $schema->shouldReceive('getKeys')
            ->atLeast()->once()
            ->with(Source::SOURCE_VARIABLE)
            ->andReturn($keys);

        $schemaReader = \Mockery::mock(SchemaReader::class);
        $schemaReader->shouldReceive('hasDefault')
            ->with($keys[0], $schema)
            ->andReturn(true);
        $schemaReader->shouldReceive('hasDefault')
            ->with($keys[1], $schema)
            ->andReturn(false);

        self::assertInstanceOf(
            Variable::class,
            new Variable($schema, $config, null, $schemaReader)
        );
    }

    /**
     * @group unit
     */
    public function testConstructorThrowsExceptionOnMissingValue()
    {
        $key = 'some.config.key';
        $schema = \Mockery::mock(Schema::class);
        $schema->shouldReceive('getKeys')
            ->atLeast()->once()
            ->with(Source::SOURCE_VARIABLE)
            ->andReturn([$key]);

        $schemaReader = \Mockery::mock(SchemaReader::class);
        $schemaReader->shouldReceive('hasDefault')
            ->with($key, $schema)
            ->andReturn(false);

        self::expectException(MissingValue::class);

        (new Variable($schema, [], null, $schemaReader));
    }

    private function buildSchemaMock(string $key, array $definitionForKey, array $mockData): Schema
    {
        $schema = \Mockery::mock(Schema::class);
        $schema->shouldReceive('getDefinition')
            ->with($key)
            ->andReturn($definitionForKey);
        $schema->shouldReceive('getKeys')
            ->with(Source::SOURCE_VARIABLE)
            ->andReturn($mockData['schema']['getKeys']['return']);

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
