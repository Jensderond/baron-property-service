<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;

class MediaService
{
    public function __construct(protected FilesystemOperator $assetsStorage, private readonly LoggerInterface $logger)
    {
    }

    public function buildObject(?string $url, array $options = []): array
    {
        if(!$url) {
            return [];
        }

        $defaultOptions = [
            'sizes' => [
                '1x' => '400x266',
                '2x' => '800x532',
                'mainImage' => [
                    '480w' => '580x387',
                    '768w' => '870x580',
                    '1280w' => '1450x967',
                ],
            ],
        ];

        $options = !empty($options) ? $options : $defaultOptions;

        $relativeUrl = parse_url($url);
        $path = $relativeUrl['path'];

        $images = [];
        foreach ($options['sizes'] as $key => $size) {
            if (is_array($size)) {
                foreach ($size as $subKey => $subSize) {
                    $images[$key][$subKey] = $this->processImage($subSize, $path, $url);
                }
            } else {
                $images[$key] = $this->processImage($size, $path, $url);
            }
        }

        return $images;
    }

    public function transfromItem(array $media): array
    {
        $options = [
            'sizes' => [
                '480w' => '580x387',
                '768w' => '870x580',
                '1280w' => '1450x967',
            ],
        ];

        $transformedMedia['sizes'] = $this->buildObject($media['link'], $options);
        $transformedMedia['soort'] = $media['soort'];
        $transformedMedia['title'] = $media['title'] ?? '';
        $transformedMedia['omschrijving'] = $media['omschrijving'] ?? '';
        $transformedMedia['mimetype'] = 'image/webp';

        return $transformedMedia;
    }

    private function processImage(string $size, string $path, string $url): string
    {
        $imagePath = "{$path}-{$size}.webp";
        if (!$this->fileExist($imagePath)) {
            $imageContent = $this->downloadFile($url);
            if ($imageContent) {
                return $this->resizeAndSaveImage($imageContent, $size, $path);
            }
        } else {
            return $this->assetsStorage->publicUrl($imagePath);
        }

        return '';
    }

    private function downloadFile(?string $url): string|null
    {
        if (!$url && !$this->isValidUrl($url)) {
            return null;
        }

        try {
            $this->logger->info('Downloading file');

            return file_get_contents($url . '&resize=4');
        } catch (FilesystemException $e) {
            $this->logger->error('Filesystem error:'.$e);
        }

        return null;
    }

    private function fileExist(string $path): bool
    {
        return $this->assetsStorage->fileExists($path);
    }

    private function resizeAndSaveImage(string $imageContent, string $size, string $path): string
    {
        [$width, $height] = explode('x', $size);
        $imagine = new Imagine();
        $image = $imagine->load($imageContent);
        $resizedImage = $image->resize(new Box($width, $height));

        $this->assetsStorage->write("{$path}-{$width}x{$height}.webp", $resizedImage->get('webp'));

        return $this->assetsStorage->publicUrl("{$path}-{$width}x{$height}.webp");
    }

    private function isValidUrl(string $url): bool
    {
        $url = parse_url($url);

        return isset($url['scheme']) && in_array($url['scheme'], ['http', 'https'], true);
    }
}
