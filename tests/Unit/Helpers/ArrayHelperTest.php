<?php

declare(strict_types=1);

namespace App\Tests\Unit\Helpers;

use App\Helpers\ArrayHelper;
use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase
{
    public function testRemapMediaLinksRenamesLinkToUrl(): void
    {
        $media = [
            ['link' => 'https://example.com/image1.jpg', 'soort' => 'HOOFDFOTO'],
            ['link' => 'https://example.com/image2.jpg', 'soort' => 'FOTO'],
        ];

        $result = ArrayHelper::remapMediaLinks($media);

        $this->assertArrayHasKey('url', $result[0]);
        $this->assertArrayNotHasKey('link', $result[0]);
        $this->assertSame('https://example.com/image1.jpg', $result[0]['url']);
        $this->assertSame('https://example.com/image2.jpg', $result[1]['url']);
    }

    public function testRemapMediaLinksDoesNotModifyItemsWithExistingUrl(): void
    {
        $media = [
            ['url' => 'https://example.com/image1.jpg', 'link' => 'https://example.com/other.jpg', 'soort' => 'FOTO'],
        ];

        $result = ArrayHelper::remapMediaLinks($media);

        $this->assertSame('https://example.com/image1.jpg', $result[0]['url']);
        $this->assertArrayHasKey('link', $result[0]);
    }

    public function testRemapMediaLinksHandlesEmptyArray(): void
    {
        $this->assertSame([], ArrayHelper::remapMediaLinks([]));
    }

    public function testRemapMediaLinksHandlesNonArrayItems(): void
    {
        $media = [null, 'string', 42];

        $result = ArrayHelper::remapMediaLinks($media);

        $this->assertSame([null, 'string', 42], $result);
    }
}
