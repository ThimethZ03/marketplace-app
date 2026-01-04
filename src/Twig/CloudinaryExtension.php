<?php

namespace App\Twig;

use App\Service\CloudinaryUploader;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CloudinaryExtension extends AbstractExtension
{
    public function __construct(private CloudinaryUploader $cloudinaryUploader)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('cloudinary_url', [$this, 'getCloudinaryUrl']),
        ];
    }

    public function getCloudinaryUrl(?string $publicId): string
    {
        if (!$publicId) {
            return '';
        }
        return $this->cloudinaryUploader->getUrl($publicId);
    }
}
