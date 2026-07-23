<?php

namespace QUITests\Meta;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class PackageConfigurationTest extends TestCase
{
    public function testProjectRobotsDefaultIsSelectable(): void
    {
        $Settings = simplexml_load_file(dirname(__DIR__, 4) . '/settings.xml');

        self::assertInstanceOf(SimpleXMLElement::class, $Settings);

        $defaultValues = $Settings->xpath(
            '/quiqqer/project/settings/config/section/conf[@name="robots"]/defaultvalue'
        );
        $selectOptions = $Settings->xpath(
            '/quiqqer/project/settings/window/categories/category/settings'
            . '/select[@conf="meta.project.robots"]/option/@value'
        );

        self::assertIsArray($defaultValues);
        self::assertCount(1, $defaultValues);
        self::assertIsArray($selectOptions);

        $defaultValue = (string)$defaultValues[0];
        $optionValues = array_map(
            static fn(SimpleXMLElement $value): string => (string)$value,
            $selectOptions
        );

        self::assertSame('all', $defaultValue);
        self::assertContains($defaultValue, $optionValues);
    }
}
