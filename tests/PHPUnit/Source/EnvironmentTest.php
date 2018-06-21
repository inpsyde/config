<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

use Inpsyde\Config\Exception\MissingConfig;
use Inpsyde\Config\Filter;
use Inpsyde\Config\Helper\SchemaReader;
use Inpsyde\Config\Schema;
use MonkeryTestCase\BrainMonkeyWpTestCase;

class EnvironmentTest extends BrainMonkeyWpTestCase
{

    private $environment = [];

    /**
     * @dataProvider getData
     */
    public function testGet(string $key, array $definitionForKey, array $mockData, $expected)
    {
        $schema = $this->buildSchemaMock($key, $definitionForKey);
        $filter = $this->buildFilterMock($mockData, $definitionForKey);
        $schemaReader = $this->buildSchemaReaderMock($mockData, $key, $schema);

        if (null !== $mockData['envName']) {
            $this->putEnv($mockData['envName'], $mockData['rawValue']);
        }

        self::assertSame(
            $expected,
            (new Environment($schema, $filter, $schemaReader))->get($key)
        );
    }

    /**
     * @see testGet
     */
    public function getData(): array
    {
        return [
            'existing value' => [
                'key' => 'some.config.key',
                'definitionForKey' => [
                    /*
                     * It doesn't matter how this array looks like as it is only passed through
                     *  and is expected as parameter for mocks
                     */
                    'Im Just Random Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => '5.5',
                    'envName' => 'INPSYDE_CONFIG_TEST',
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
                    'schemaReader' => [
                        'sourceName' => [
                            'return' => 'INPSYDE_CONFIG_TEST',
                        ],
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
                'definitionForKey' => [
                    'Im Just Random Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => null,
                    'envName' => null,
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
                            'return' => 'INPSYDE_CONFIG_TEST',
                        ],
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
     * @dataProvider hasData
     */
    public function testHas(string $key, array $definitionForKey, array $mockData, bool $expected)
    {
        $schema = $this->buildSchemaMock($key, $definitionForKey);
        $filter = $this->buildFilterMock($mockData, $definitionForKey);
        $schemaReader = $this->buildSchemaReaderMock($mockData, $key, $schema);

        if (null !== $mockData['rawValue']) {
            $this->putEnv($mockData['envName'], $mockData['rawValue']);
        }

        self::assertSame(
            $expected,
            (new Environment($schema, $filter, $schemaReader))->has($key)
        );
    }

    /**
     * @see testHas
     */
    public function hasData(): array
    {
        return [
            'existing value' => [
                'key' => 'some.config.key',
                'definitionForKey' => [
                    /*
                    * It doesn't matter how this array looks like as it is only passed through
                    *  and is expected as parameter for mocks
                    */
                    'Im Just Arbitrary Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => '5.5',
                    'envName' => 'INPSYDE_CONFIG_TEST_A',
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
                            'return' => 'INPSYDE_CONFIG_TEST_A',
                        ],
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
            'not existing value' => [
                'key' => 'some.config.key',
                'definitionForKey' => [
                    'Im Just Arbitrary Data That Gets Passed Around',
                ],
                'mockData' => [
                    'rawValue' => null,
                    'envName' => 'INPSYDE_CONFIG_TEST_B',
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
                            'return' => 'INPSYDE_CONFIG_TEST_B',
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
            'not existing value fall back to default' => [
                'key' => 'some.config.key',
                'definitionForKey' => [
                    'Im Just Arbitrary Data That Gets Passed Around'
                ],
                'mockData' => [
                    'rawValue' => null,
                    'envName' => 'INPSYDE_CONFIG_TEST_C',
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
                            'return' => 'INPSYDE_CONFIG_TEST_C',
                        ],
                        'hasDefault' => [
                            'return' => true,
                        ],
                        'defaultValue' => [
                            'return' => 10.1,
                        ],
                    ],
                ],
                'expected' => 10.5,
            ],
        ];
    }

    public function testGetThrowsMissingConfigException()
    {
        $key = 'what.the.config';

        $schema = \Mockery::mock(Schema::class);
        $filter = \Mockery::mock(Filter::class);
        $schemaReader = \Mockery::mock(
            SchemaReader::class,
            [
                'sourceName' => '',
            ]
        );

        self::expectException(MissingConfig::class);

        (new Environment($schema, $filter, $schemaReader))
            ->get($key);
    }

    private function putEnv(string $name, string $value)
    {
        $this->environment[$name] = $value;
        putenv("{$name}={$value}");
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

    protected function tearDown()
    {
        parent::tearDown();

        foreach (array_keys($this->environment) as $env) {
            putenv($env);
        }

        $this->environment = [];
    }
}
