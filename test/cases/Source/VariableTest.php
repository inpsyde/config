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
        return require __DIR__.'/../../data/Source/data-VariableTest::testHas.php';
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
        return require __DIR__ . '/../../data/Source/data-VariableTest::testGet.php';
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
