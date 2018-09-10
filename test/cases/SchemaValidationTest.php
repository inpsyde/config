<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use MonkeryTestCase\BrainMonkeyWpTestCase;

class SchemaValidationTest extends BrainMonkeyWpTestCase
{

    private static $validateSchemaThrowsExceptionData = [];

    private static $validateSchemaData = [];

    /**
     * @dataProvider validateSchemaData
     * @group unit
     */
    public function testValidateSchema(array $schema, array $expected)
    {
        $testee = new SchemaValidation();
        $schema = $testee->validateSchema($schema);

        self::assertInstanceOf(
            Schema::class,
            $schema
        );

        foreach ($expected as $key => $definition) {
            self::assertEquals(
                $definition,
                $schema->getDefinition($key),
                "Failed for key {$key}",
                0.0,
                10,
                true
            );
        }
    }

    /**
     * @see testValidateSchema
     */
    public function validateSchemaData(): array
    {
        if (! self::$validateSchemaData) {
            self::$validateSchemaData = require __DIR__.'/../data/data-SchemaValidationTest::testValidateSchema.php';
        }

        return self::$validateSchemaData;
    }

    /**
     * @dataProvider validateSchemaThrowsExceptionData
     * @group unit
     */
    public function testValidateSchemaThrowsException(
        array $schema,
        string $expectedException,
        string $expectedExceptionMessage
    ) {
        self::expectException($expectedException);
        self::expectExceptionMessage($expectedExceptionMessage);
        (new SchemaValidation())->validateSchema($schema);
    }

    /**
     * @see testValidateSchemaThrowsException
     */
    public function validateSchemaThrowsExceptionData(): array
    {
        if (! self::$validateSchemaThrowsExceptionData) {
            self::$validateSchemaThrowsExceptionData = require __DIR__
                .'/../data/data-SchemaValidationTest::testValidateSchemaThrowsException.php';
        }

        return self::$validateSchemaThrowsExceptionData;
    }
}
