<?php

declare(strict_types=1);

namespace App\Message;

readonly class YoutubeDownloadMessage
{
    public function __construct(
        private string $url,
        private string $quality,
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getQuality(): string
    {
        return $this->quality;
    }
}
