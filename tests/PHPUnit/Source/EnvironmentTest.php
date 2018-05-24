<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

use Inpsyde\Config\Exception\MissingConfig;
use Inpsyde\Config\Filter;
use Inpsyde\Config\Schema;
use MonkeryTestCase\BrainMonkeyWpTestCase;

class EnvironmentTest extends BrainMonkeyWpTestCase
{

    private $environment = [];

    /**
     * @dataProvider getData
     */
    public function testGet(string $key, array $schemaData, $value, callable $filterCb, $expected)
    {
        $filter = \Mockery::mock(Filter::class);
        $schema = \Mockery::mock(Schema::class);

        $schema->shouldReceive('getDefinition')
            ->with($key)
            ->andReturn($schemaData[$key]);

        $filter->shouldReceive('validateValue')
            ->with($value, $schemaData[$key])
            ->andReturnUsing($filterCb);
        $filter->shouldReceive('filterValue')
            ->with($value, $schemaData[$key])
            ->andReturn($expected);

        if (null !== $value) {
            $this->putEnv($schemaData[$key]['source_name'], $value);
        }

        self::assertSame(
            $expected,
            (new Environment($schema, $filter))->get($key)
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
                'schema' => [
                    'some.config.key' => [
                        'source' => Source::SOURCE_ENV,
                        'source_name' => 'INPSYDE_CONFIG_TEST',
                        'filter' => FILTER_VALIDATE_FLOAT,
                    ],
                ],
                'value' => '5.5',
                'filterCb' => function () {
                    return 5.5;
                },
                'expected' =>  5.5,
            ],
            'not existing value returns default' => [
                'key' => 'some.config.key',
                'schema' => [
                    'some.config.key' => [
                        'source' => Source::SOURCE_ENV,
                        'source_name' => 'INPSYDE_CONFIG_TEST',
                        'default_value' => 10.1,
                        'filter' => FILTER_VALIDATE_FLOAT,
                    ],
                ],
                'value' => null,
                'filterCb' => function () {
                    return false;
                },
                'expected' =>  10.1,
            ],
        ];
    }

    /**
     * @dataProvider hasData
     */
    public function testHas(string $key, array $schemaData, $value, callable $filterCb, bool $expected)
    {
        $filter = \Mockery::mock(Filter::class);
        $schema = \Mockery::mock(Schema::class);

        $schema->shouldReceive('getDefinition')
            ->atLeast()
            ->once()
            ->with($key)
            ->andReturn($schemaData[$key]);

        $filter->shouldReceive('validateValue')
            ->with($value, $schemaData[$key])
            ->andReturnUsing($filterCb);

        if (null !== $value) {
            $this->putEnv($schemaData[$key]['source_name'], $value);
        }

        self::assertSame(
            $expected,
            (new Environment($schema, $filter))->has($key)
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
                'schema' => [
                    'some.config.key' => [
                        'source' => Source::SOURCE_ENV,
                        'source_name' => 'INPSYDE_CONFIG_TEST_A',
                        'default_value' => 5.5,
                        'filter' => FILTER_VALIDATE_FLOAT,
                    ],
                ],
                'value' => '5.5',
                'filterCb' => function () {
                    return 5.5;
                },
                'expected' =>  true,
            ],
            'not existing value' => [
                'key' => 'some.config.key',
                'schema' => [
                    'some.config.key' => [
                        'source' => Source::SOURCE_ENV,
                        'source_name' => 'INPSYDE_CONFIG_TEST_B',
                    ],
                ],
                'value' => null,
                'filterCb' => function () {
                    return false;
                },
                'expected' => false,
            ],
            'not existing value fall back to default' => [
                'key' => 'some.config.key',
                'schema' => [
                    'some.config.key' => [
                        'source' => Source::SOURCE_ENV,
                        'source_name' => 'INPSYDE_CONFIG_TEST_C',
                        'default_value' => 10.5,
                        'filter' => FILTER_VALIDATE_FLOAT
                    ],
                ],
                'value' => null,
                'filterCb' => function () {
                    return false;
                },
                'expected' => 10.5,
            ],
        ];
    }

    public function testGetThrowsMissingConfigException() {

        $schema = \Mockery::mock(Schema::class);
        $filter = \Mockery::mock(Filter::class);
        $key = 'what.the.config';

        $schema->shouldReceive('getDefinition')
            ->with($key)
            ->andReturn([]);

        self::expectException(MissingConfig::class);

        (new Environment($schema,$filter))
            ->get($key);
    }

    private function putEnv(string $name, string $value)
    {
        $this->environment[$name] = $value;
        putenv("{$name}={$value}");
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
