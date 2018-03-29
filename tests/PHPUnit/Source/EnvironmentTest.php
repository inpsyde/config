<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

use Inpsyde\Config\Filter;
use Inpsyde\Config\Schema;
use MonkeryTestCase\BrainMonkeyWpTestCase;

class EnvironmentTest extends BrainMonkeyWpTestCase
{

    private $environment = [];

    /**
     * @dataProvider hasData
     */
    public function testGet(string $key, array $schemaData, string $value, callable $filterCb, array $expected)
    {
        $filter = \Mockery::mock(Filter::class);
        $schema = \Mockery::mock(Schema::class);

        $schema->expects('getDefinition')
            ->times(4)
            ->with($key)
            ->andReturn($schemaData[$key]);

        $filter->expects('validateValue')
            ->once()
            ->with($value, $schemaData[$key])
            ->andReturnUsing(
                function ($value) use ($filterCb) {
                    return $filterCb($value);
                }
            );
        $filter->expects('filterValue')
            ->once()
            ->with($value, $schemaData[$key])
            ->andReturn($expected['get']);

        $this->putEnv($schemaData[$key]['source_name'], $value);

        self::assertSame(
            $expected['get'],
            (new Environment($schema, $filter))->get($key)
        );
    }

    /**
     * @dataProvider hasData
     */
    public function testHas(string $key, array $schemaData, string $value, callable $filterCb, array $expected)
    {
        $filter = \Mockery::mock(Filter::class);
        $schema = \Mockery::mock(Schema::class);

        $schema->expects('getDefinition')
            ->twice()
            ->with($key)
            ->andReturn($schemaData[$key]);

        $filter->expects('validateValue')
            ->once()
            ->with($value, $schemaData[$key])
            ->andReturnUsing(
                function ($value) use ($filterCb) {
                    return $filterCb($value);
                }
            );

        $this->putEnv($schemaData[$key]['source_name'], $value);

        self::assertSame(
            $expected['has'],
            (new Environment($schema, $filter))->has($key)
        );
    }

    /**
     * @see testHas
     * @see testGet
     */
    public function hasData(): array
    {
        return [
            'complete definition filter returns true' => [
                'key' => 'some.config.key',
                'schema' => [
                    'some.config.key' => [
                        'source' => Source::SOURCE_ENV,
                        'source_name' => 'INPSYDE_CONFIG_TEST',
                        'default_value' => 5.5,
                        'filter' => FILTER_VALIDATE_FLOAT,
                    ],
                ],
                'value' => '5.5',
                'filterCb' => function () {
                    return 5.5;
                },
                'expected' => [
                    'has' => true,
                    'get' => 5.5,
                ],
            ],
        ];
    }

    /**
     * @dataProvider hasHandlesDefaultValueData
     */
    public function testHasHandlesDefaultValue(
        string $key,
        array $schemaData,
        array $expected
    ) {
        $filter = \Mockery::mock(Filter::class);
        $schema = \Mockery::mock(Schema::class);

        $schema->expects('getDefinition')
            ->twice()
            ->with($key)
            ->andReturn($schemaData[$key]);

        $filter->expects('validateValue')
            ->never();

        self::assertSame(
            $expected['has'],
            (new Environment($schema, $filter))->has($key)
        );
    }

    /**
     * @dataProvider hasHandlesDefaultValueData
     */
    public function testGetHandlesDefaultValue(
        string $key,
        array $schemaData,
        array $expected
    ) {
        $filter = \Mockery::mock(Filter::class);
        $schema = \Mockery::mock(Schema::class);

        $schema->expects('getDefinition')
            ->times(6)
            ->with($key)
            ->andReturn($schemaData[$key]);

        $filter->expects('validateValue')
            ->never();

        self::assertSame(
            $expected['get'],
            (new Environment($schema, $filter))->get($key)
        );
    }

    /**
     * @see testHasHandlesDefaultValue
     * @see testGetHandlesDefaultValue
     */
    public function hasHandlesDefaultValueData(): array
    {
        return [
            [
                'key' => 'some.key',
                'schema' => [
                    'some.key' => [
                        'source' => Source::SOURCE_ENV,
                        'source_name' => 'INPSYDE_CONFIG',
                        'filter' => FILTER_VALIDATE_FLOAT,
                        'default_value' => 5.5,
                    ],
                ],
                'expected' => [
                    'has' => true,
                    'get' => 5.5,
                ],
            ],
        ];
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
