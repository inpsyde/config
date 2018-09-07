<?php
declare(strict_types=1);

namespace Inpsyde\Config\Helper;

use Inpsyde\Config\Exception\MissingDefaultValue;
use Inpsyde\Config\Schema;
use Inpsyde\Config\Source\Source;
use MonkeryTestCase\BrainMonkeyWpTestCase;

class SchemaReaderTest extends BrainMonkeyWpTestCase
{

    /**
     * @group unit
     */
    public function testSourceName()
    {
        $key = 'some.config.key';
        $sourceName = 'SOME_ENV_VAR';

        $schema = \Mockery::mock(Schema::class);
        $schema->shouldReceive('getDefinition')
            ->with($key)
            ->andReturn(
                [
                    'source' => 'doesntMatter',
                    'source_name' => $sourceName,
                ]
            );

        self::assertSame(
            $sourceName,
            (new SchemaReader())->sourceName($key, $schema)
        );
    }

    /**
     * @dataProvider hasDefaultData
     * @group unit
     */
    public function testHasDefault(string $key, array $definition, bool $expected)
    {

        $schema = \Mockery::mock(Schema::class);
        $schema->shouldReceive('getDefinition')
            ->with($key)
            ->andReturn($definition);

        self::assertSame(
            $expected,
            (new SchemaReader())->hasDefault($key, $schema)
        );
    }

    /**
     * @see testHasDefault
     */
    public function hasDefaultData(): array
    {
        return [
            'expect true' => [
                'key' => 'what.ever',
                'definition' => [
                    'source' => 'irrelevant',
                    'source_name' => 'irrelevant',
                    'default_value' => 'Hello World'
                ],
                'expected' => true,
            ],
            'expect false' => [
                'key' => 'what.ever',
                'definition' => [
                    'source' => 'irrelevant',
                    'source_name' => 'irrelevant',
                ],
                'expected' => false,
            ],
        ];
    }

    /**
     * @group unit
     */
    public function testDefaultValue()
    {
        $key = 'some.key';
        $defaultValue = 10.01;
        $schema = \Mockery::mock(Schema::class);
        $schema->shouldReceive('getDefinition')
            ->with($key)
            ->andReturn(
                [
                    'default_value' => $defaultValue
                ]
            );

        self::assertSame(
            $defaultValue,
            (new SchemaReader())->defaultValue($key, $schema)
        );
    }

    /**
     * @group unit
     */
    public function testDefaultValueThrowsException()
    {
        $key = 'some.key';
        $schema = \Mockery::mock(Schema::class);
        $schema->shouldReceive('getDefinition')
            ->with($key)
            ->andReturn(
                [
                    'source' => Source::SOURCE_ENV,
                    'source_name' => 'SOME_ENV_VAR',
                ]
            );

        self::expectException(MissingDefaultValue::class);

        (new SchemaReader())->defaultValue($key, $schema);
    }
}
