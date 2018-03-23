<?php
declare(strict_types = 1);

namespace Inpsyde\Config;

use MonkeryTestCase\BrainMonkeyWpTestCase;

class SchemaValidationTest extends BrainMonkeyWpTestCase
{

    private static $testData = [];

    /**
     * @dataProvider validateSchemaData
     * @throws \Throwable
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
            self::assertSame(
                $definition,
                $schema->getDefinition($key),
                "Failed for key {$key}"
            );
        }
    }

    /**
     * @see testValidateSchema
     */
    public function validateSchemaData(): array
    {

        if (!self::$testData) {
            self::$testData = require __DIR__.'/../data/validate-schema-test-data.php';
        }

        return self::$testData['testValidateSchema'];
    }
}
