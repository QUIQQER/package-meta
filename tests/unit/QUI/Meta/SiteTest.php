<?php

namespace QUITests\Meta;

use PHPUnit\Framework\TestCase;
use QUI;
use QUI\Interfaces\Projects\Site as SiteInterface;
use QUI\Meta\Site;
use QUI\Projects\Project;

class SiteTest extends TestCase
{
    public function testExplicitSiteMetadataIsApplied(): void
    {
        $attributes = $this->runOnInit(
            [
                'quiqqer.meta.site.title' => 'SEO title',
                'quiqqer.meta.site.robots' => 'noindex, nofollow',
                'quiqqer.meta.site.description' => 'SEO description',
                'quiqqer.meta.site.canonical' => 'https://example.test/canonical',
                'quiqqer.meta.site.publisher' => 'Site publisher'
            ],
            [
                'meta.project.copyright' => 'Example copyright',
                'meta.project.revisit' => '7 days'
            ]
        );

        self::assertSame(
            [
                'meta.revisit' => '7 days',
                'meta.itemscope' => 'https://schema.org/WebPage',
                'meta.seotitle' => 'SEO title',
                'meta.robots' => 'noindex, nofollow',
                'meta.description' => 'SEO description',
                'meta.copyright' => 'Example copyright',
                'meta.publisher' => 'Site publisher',
                'meta.canonical' => 'https://example.test/canonical'
            ],
            $attributes
        );
    }

    public function testProjectAndLegacyFallbacksAreApplied(): void
    {
        $attributes = $this->runOnInit(
            [
                'short' => 'Short description',
                'title' => 'Page title'
            ],
            [
                'meta.project.robots' => 'nofollow',
                'meta.project.publisher' => '',
                'publisher' => 'Legacy publisher'
            ]
        );

        self::assertSame('Page title', $attributes['meta.seotitle']);
        self::assertSame('nofollow', $attributes['meta.robots']);
        self::assertSame('Short description', $attributes['meta.description']);
        self::assertSame('Legacy publisher', $attributes['meta.publisher']);
        self::assertSame('', $attributes['meta.revisit']);
        self::assertSame('', $attributes['meta.copyright']);
        self::assertArrayNotHasKey('meta.canonical', $attributes);
    }

    public function testEmptyMetadataUsesLocaleAndHardCodedDefaults(): void
    {
        $localeDescription = QUI::getLocale()->getByLang(
            'en',
            'quiqqer/meta',
            'quiqqer.projects.description'
        );

        $attributes = $this->runOnInit([], []);

        self::assertSame($localeDescription ?: '', $attributes['meta.description']);
        self::assertSame('', $attributes['meta.seotitle']);
        self::assertSame('all', $attributes['meta.robots']);
        self::assertSame('', $attributes['meta.publisher']);
        self::assertArrayNotHasKey('meta.canonical', $attributes);
    }

    /**
     * @param array<string, mixed> $siteAttributes
     * @param array<string, mixed> $projectConfig
     * @return array<string, mixed>
     */
    private function runOnInit(array $siteAttributes, array $projectConfig): array
    {
        $Project = $this->createMock(Project::class);
        $Project->method('getLang')->willReturn('en');
        $Project->method('getConfig')->willReturnCallback(
            static fn(string|bool $name): mixed => $projectConfig[$name] ?? false
        );

        $Site = $this->createMock(SiteInterface::class);
        $Site->method('getProject')->willReturn($Project);
        $Site->method('getAttribute')->willReturnCallback(
            static fn(string $name): mixed => $siteAttributes[$name] ?? false
        );

        $writtenAttributes = [];
        $Site->method('setAttribute')->willReturnCallback(
            static function (string $name, mixed $value) use (&$writtenAttributes): void {
                $writtenAttributes[$name] = $value;
            }
        );

        Site::onInit($Site);

        return $writtenAttributes;
    }
}
