<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;

class MediaService
{
    public function __construct(protected FilesystemOperator $publicUploadsStorage, private readonly LoggerInterface $logger)
    {
    }

    // public function buildObject(string $url): array
    // {
    //     $relativeUrl = parse_url($url);
    //     $path = $relativeUrl['path'];

    //     $images = [];

    //     if(!$this->fileExist("{$path}-400x266.webp")) {
    //         $imageContent = $this->downloadFile($url);

    //         if ($imageContent) {
    //             $images = [
    //                 '1x' => $this->resizeAndSaveImage($imageContent, '400x266', $path),
    //                 '2x' => $this->resizeAndSaveImage($imageContent, '800x532', $path),
    //                 'mainImage' => [
    //                     '1x' => $this->resizeAndSaveImage($imageContent, '500x333', $path),
    //                     '2x' => $this->resizeAndSaveImage($imageContent, '1000x666', $path),
    //                     '3x' => $this->resizeAndSaveImage($imageContent, '2000x1333', $path),
    //                 ]
    //             ];
    //         }
    //     } else {
    //         $images = [
    //             '1x' => $this->publicUploadsStorage->publicUrl("{$path}-400x266.webp"),
    //             '2x' => $this->publicUploadsStorage->publicUrl("{$path}-800x532.webp"),
    //             'mainImage' => [
    //                 '1x' => $this->publicUploadsStorage->publicUrl("{$path}-500x333.webp"),
    //                 '2x' => $this->publicUploadsStorage->publicUrl("{$path}-1000x666.webp"),
    //                 '3x' => $this->publicUploadsStorage->publicUrl("{$path}-2000x1333.webp"),
    //             ]
    //         ];
    //     }

    //     return $images;
    // }
    public function buildObject(string $url, array $options = []): array
    {
        $defaultOptions = [
            'sizes' => [
                '1x' => '400x266',
                '2x' => '800x532',
                'mainImage' => [
                    '480w' => '580x387',
                    '768w' => '870x580',
                    '1280w' => '1450x967',
                    '1536w' => '1740x1160',
                ],
            ],
        ];

        $options = array_merge($defaultOptions, $options);

        $relativeUrl = parse_url($url);
        $path = $relativeUrl['path'];

        $images = [];
        foreach ($options['sizes'] as $key => $size) {
            if (is_array($size)) {
                foreach ($size as $subKey => $subSize) {
                    $images['mainImage'][$subKey] = $this->processImage($subSize, $path, $url);
                }
            } else {
                $images[$key] = $this->processImage($size, $path, $url);
            }
        }

        return $images;
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
            return $this->publicUploadsStorage->publicUrl($imagePath);
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
        return $this->publicUploadsStorage->fileExists($path);
    }

    private function resizeAndSaveImage(string $imageContent, string $size, string $path): string
    {
        [$width, $height] = explode('x', $size);
        $imagine = new Imagine();
        $image = $imagine->load($imageContent);
        $resizedImage = $image->resize(new Box($width, $height));

        $this->publicUploadsStorage->write("{$path}-{$width}x{$height}.webp", $resizedImage->get('webp'));

        return $this->publicUploadsStorage->publicUrl("{$path}-{$width}x{$height}.webp");
    }

    private function isValidUrl(string $url): bool
    {
        $url = parse_url($url);

        return isset($url['scheme']) && in_array($url['scheme'], ['http', 'https'], true);
    }
}
