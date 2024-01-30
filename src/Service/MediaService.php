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
        if (!$url) {
            return [];
        }

        $options = !empty($options) ? $options : $this->getDefaultOptions();

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

    public function handleMedia(?array $mediaInput): array
    {
        if (!isset($mediaInput)) {
            return [];
        }

        $transformedItems = [];

        foreach ($mediaInput as $key => $media) {
            if (in_array($media['soort'], ['HOOFDFOTO', 'FOTO', 'PLATTEGROND', 'CONNECTED_PARTNER'], true) && isset($media['link'])) {
                $isDocument = $media['soort'] === 'DOCUMENT';
                $isConnectedPartner = $media['soort'] === 'CONNECTED_PARTNER';
                $transformedItems[] = $this->transformItem($media, $isDocument, $isConnectedPartner);

                unset($mediaInput[$key]);
            }
        }

        return array_merge($transformedItems, array_values($mediaInput));
    }

    public function transformItem(array $media, ?bool $isDocument = false, ?bool $isConnectedPartner = false): array
    {
        $options = [
            'sizes' => [
                '480w' => '580x387',
                '768w' => '870x580',
                '1280w' => '1450x967',
            ],
        ];

        $transformedMedia = [
            'soort' => $media['soort'],
            'title' => $media['title'] ?? '',
            'omschrijving' => $media['omschrijving'] ?? '',
        ];

        $url = parse_url($media['link']);
        if (isset($url['query'])) {
            $media['link'] .= '&resize=4';
        } else {
            $media['link'] .= '?resize=4';
        }

        if ($isDocument) {
            $transformedMedia['link'] = $media['link'];
            $transformedMedia['mimetype'] = $media['mimetype'];
        } elseif ($isConnectedPartner) {
            $transformedMedia['link'] = $this->downloadAndSaveOriginalImage($media['link']);
            $transformedMedia['mimetype'] = $media['mimetype'];
        } else {
            $transformedMedia['sizes'] = $this->buildObject($media['link'], $options);
            $transformedMedia['mimetype'] = 'image/webp';
        }

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

    private function downloadAndSaveOriginalImage(string $url): string
    {
        $imageContent = $this->downloadFile($url);
        if ($imageContent) {
            $path = parse_url($url, PHP_URL_PATH);
            $filename = basename($path);
            $this->assetsStorage->write($filename, $imageContent);

            return $this->assetsStorage->publicUrl($filename);
        }

        return '';
    }

    private function downloadFile(?string $url): ?string
    {
        if (!$url || !$this->isValidUrl($url)) {
            return null;
        }

        try {
            $this->logger->info('Downloading file');
            return file_get_contents($url);
        } catch (FilesystemException $e) {
            $this->logger->error('Filesystem error:' . $e->getMessage());
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

        $imagePath = "{$path}-{$width}x{$height}.webp";
        $this->assetsStorage->write($imagePath, $resizedImage->get('webp'));

        return $this->assetsStorage->publicUrl($imagePath);
    }

    private function isValidUrl(string $url): bool
    {
        $url = parse_url($url);
        return isset($url['scheme']) && in_array($url['scheme'], ['http', 'https'], true);
    }

    private function getDefaultOptions(): array
    {
        return [
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
    }
}
