<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use MonkeryTestCase\BrainMonkeyWpTestCase;

class FilterTest extends BrainMonkeyWpTestCase
{

    private static $testData = [];

    /**
     * @dataProvider filterValueData
     * @group unit
     */
    public function testFilterValue($value, array $schema, $expected)
    {
        self::assertSame(
            $expected,
            (new Filter())->filterValue($value, $schema)
        );
    }

    /**
     * @see testFilterValue
     */
    public function filterValueData(): array
    {
        if (! self::$testData) {
            self::$testData = require __DIR__.'/../data/filter.php';
        }

        return self::$testData['testFilterValue'];
    }

    /**
     * @dataProvider validateValueData
     * @group unit
     */
    public function testValidateValue($value, array $schema, bool $expected)
    {
        self::assertSame(
            $expected,
            (new Filter())->validateValue($value, $schema)
        );
    }

    /**
     * @see testValidateValue
     */
    public function validateValueData(): array
    {
        if (! self::$testData) {
            self::$testData = require __DIR__.'/../data/filter.php';
        }

        return self::$testData['testValidateValue'];
    }
}
