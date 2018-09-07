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
     * @group unit
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
        return require __DIR__ . '/../../data/Source/data-ConstantTest::testHas.php';
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
        return require __DIR__ .'/../../data/Source/data-ConstantTest::testGet.php';
    }

    /**
     * @group unit
     */
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
