<?php

namespace App\Services\ApiVideo;

use ApiVideo\Client\Client;
use ApiVideo\Client\Model\VideoCreationPayload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use SplFileObject;
use Symfony\Component\HttpClient\Psr18Client;

class ApiVideoService
{
    private string $baseUrl;

    private string $apiKey;

    private Client $client;

    public function __construct(
        private bool $isPublic = true,
        private bool $mp4Support = true
    ) {
        $this->baseUrl = config('api-video.baseUrl');
        $this->apiKey = config('api-video.apiKey');

        $this->initVideoClient();
    }

    public function isPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function mp4Support(bool $mp4Support): self
    {
        $this->mp4Support = $mp4Support;

        return $this;
    }

    public function store(UploadedFile $file, string $description = null): ?string
    {
        $payload = new VideoCreationPayload();

        $payload->setTitle(Str::random(40));
        $payload->setDescription($description);
        $payload->setPublic($this->isPublic);
        $payload->setMp4support($this->mp4Support);

        $video = $this->client->videos()->create($payload);

        $filePath = $file->getRealPath();

        $response = $this->client->videos()->upload(
            $video->getVideoId(),
            new SplFileObject($filePath)
        );

        return $response->valid() ? $video->getVideoId() : null;
    }

    public function delete(string $videoId): void
    {
        $this->client->videos()->delete($videoId);
    }

    private function initVideoClient(): void
    {
        $httpClient = new Psr18Client();

        $this->client = new Client($this->baseUrl, $this->apiKey, $httpClient);
    }
}
