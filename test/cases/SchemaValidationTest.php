<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use MonkeryTestCase\BrainMonkeyWpTestCase;

class SchemaValidationTest extends BrainMonkeyWpTestCase
{

    private static $testData = [];

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
        if (! self::$testData) {
            self::$testData = require __DIR__.'/../data/schema-validation.php';
        }

        return self::$testData['testValidateSchema'];
    }

    /**
     * @dataProvider validateSchemaThrowsExceptionData
     * @group unit
     */
    public function testValidateSchemaThrowsException(array $schema, string $expectedException)
    {
        self::expectException($expectedException);
        (new SchemaValidation())->validateSchema($schema);
    }

    /**
     * @see testValidateSchemaThrowsException
     */
    public function validateSchemaThrowsExceptionData(): array
    {

        if (! self::$testData) {
            self::$testData = require __DIR__.'/../data/schema-validation.php';
        }

        return self::$testData['testValidateSchemaThrowsException'];
    }
}
