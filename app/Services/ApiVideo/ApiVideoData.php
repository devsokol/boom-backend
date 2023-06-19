<?php

namespace App\Services\ApiVideo;

class ApiVideoData
{
    public function __construct(private string $videoId)
    {
    }

    public function __toString()
    {
        return json_encode($this->getAssets(), JSON_UNESCAPED_SLASHES);
    }

    public function getAssets(): array
    {
        return [
            'assets' => [
                'hls' => $this->getHsl(),
                'iframe' => $this->getIframe(),
                'player' => $this->getPlayer(),
                'thumbnail' => $this->getThumbnail(),
                'mp4' => $this->getMp4(),
            ],
        ];
    }

    public function getVideoId(): ?string
    {
        return $this->videoId;
    }

    public function setVideoId(string $videoId): self
    {
        $this->videoId = $videoId;

        return $this;
    }

    public function getHsl(): string
    {
        return sprintf('https://cdn.api.video/vod/%s/hls/manifest.m3u8', $this->getVideoId());
    }

    public function getIframe(): string
    {
        return '<iframe '
            . 'src="https://embed.api.video/vod/' . $this->getVideoId() . '" '
            . 'width="100%" '
            . 'height="100%" '
            . 'frameborder="0" '
            . 'scrolling="no" '
            . 'allowfullscreen="true">'
            . '</iframe>';
    }

    public function getPlayer(): string
    {
        return sprintf('https://embed.api.video/vod/%s', $this->getVideoId());
    }

    public function getThumbnail(): string
    {
        return sprintf('https://cdn.api.video/vod/%s/thumbnail.jpg', $this->getVideoId());
    }

    public function getMp4(): string
    {
        return sprintf('https://cdn.api.video/vod/%s/mp4/source.mp4', $this->getVideoId());
    }
}
