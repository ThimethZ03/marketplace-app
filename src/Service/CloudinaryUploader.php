<?php

namespace App\Service;

use Cloudinary\Cloudinary;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CloudinaryUploader
{
    private Cloudinary $cloudinary;

    public function __construct(
        string $cloudName,
        string $apiKey,
        string $apiSecret
    ) {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key'    => $apiKey,
                'api_secret' => $apiSecret,
            ],
        ]);
    }

    public function upload(UploadedFile $file, string $folder = 'marketplace/adverts'): string
    {
        try {
            $result = $this->cloudinary->uploadApi()->upload(
                $file->getPathname(),
                [
                    'folder' => $folder,
                    'resource_type' => 'image',
                    'transformation' => [
                        'width' => 800,
                        'height' => 800,
                        'crop' => 'limit',
                        'quality' => 'auto',
                    ]
                ]
            );

            return $result['public_id'];
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to upload image: ' . $e->getMessage());
        }
    }

    public function getUrl(string $publicId): string
    {
        return $this->cloudinary->image($publicId)->toUrl();
    }

    public function delete(string $publicId): bool
    {
        try {
            $this->cloudinary->uploadApi()->destroy($publicId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
