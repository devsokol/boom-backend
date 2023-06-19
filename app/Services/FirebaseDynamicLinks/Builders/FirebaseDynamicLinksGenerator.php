<?php

namespace App\Services\FirebaseDynamicLinks\Builders;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Services\FirebaseDynamicLinks\DynamicLinkResponse;
use App\Services\FirebaseDynamicLinks\Exceptions\BadRequestException;
use App\Services\FirebaseDynamicLinks\Contracts\SocialMetaTagsDynamicLink;

final class FirebaseDynamicLinksGenerator extends AbstractDynamicLinkGenerator
{
    public function generate(): DynamicLinkResponse
    {
        $data = $this->prepareConfigurationData();

        $response = $this->execute($data);

        $dynamicLinkResponse = new DynamicLinkResponse;

        if ($response->ok()) {
            $responseData = $response->json();

            $dynamicLinkResponse
                ->setLink($responseData['shortLink'])
                ->setPreviewLink($responseData['previewLink'])
                ->setWarnings($responseData['warning']);
        } else {
            $responseError = $response->json();

            $textError = sprintf(
                'An error occurred while trying to get Firebase Dynamic Link: %s',
                $responseError['error']['message']
            );

            throw new BadRequestException($textError);
        }

        return $dynamicLinkResponse;
    }

    private function prepareConfigurationData(): array
    {
        $configs = $this->configurations();

        $data = [
            'dynamicLinkInfo' => [
                'domainUriPrefix' => $configs->getDomainUrlPrefix(),
                'link' => $this->getUrl(),
                'androidInfo' => [
                    'androidPackageName' => $configs->getAndroidPackageName(),
                ],
                'iosInfo' => [
                    'iosBundleId' => $configs->getIosBundleId(),
                    'iosAppStoreId' => $configs->getIosAppStoreId(),
                    'iosIpadBundleId' => $configs->getIosIpadBundleId(),
                ],
            ],
            'suffix' => [
                'option' => $configs->getLinkType(),
            ],
        ];

        if ($this instanceof SocialMetaTagsDynamicLink) {
            $data['dynamicLinkInfo']['socialMetaTagInfo']['socialTitle'] = $this->metaTitle;

            if ($this->metaDescription) {
                $data['dynamicLinkInfo']['socialMetaTagInfo']['socialDescription'] = $this->metaDescription;
            }

            if ($this->metaImageUrl) {
                $data['dynamicLinkInfo']['socialMetaTagInfo']['socialImageLink'] = $this->metaImageUrl;
            }
        }

        return $data;
    }

    private function execute(array $data): Response
    {
        $params   = http_build_query([
            'key' => $this->configurations()->getApiKey(),
        ]);

        $endpoint = sprintf('https://firebasedynamiclinks.googleapis.com/v1/shortLinks?%s', $params);

        return Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBody(json_encode($data), 'application/json')
            ->post($endpoint);
    }
}
