<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use MonkeryTestCase\BrainMonkeyWpTestCase;

class FilterTest extends BrainMonkeyWpTestCase
{

    private static $filterValueData = [];
    private static $validateValueData = [];

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
        if (! self::$filterValueData) {
            self::$filterValueData = require __DIR__.'/../data/data-FilterTest::testFilterValue.php';
        }

        return self::$filterValueData;
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
        if (! self::$validateValueData) {
            self::$filterValueData = require __DIR__.'/../data/data-FilterTest::testValidateValue.php';
        }

        return self::$validateValueData;
    }
}
