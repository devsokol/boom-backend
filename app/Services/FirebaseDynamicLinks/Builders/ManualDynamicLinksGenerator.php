<?php

namespace App\Services\FirebaseDynamicLinks\Builders;

use App\Services\FirebaseDynamicLinks\DynamicLinkResponse;
use App\Services\FirebaseDynamicLinks\Contracts\SocialMetaTagsDynamicLink;

final class ManualDynamicLinksGenerator extends AbstractDynamicLinkGenerator
{
    public function generate(): DynamicLinkResponse
    {
        $configs = $this->configurations();

        $params = $this->prepareConfigurationData();

        return (new DynamicLinkResponse())->setLink($configs->getDomainUrlPrefix() . '?' . $params);
    }

    private function prepareConfigurationData(): string
    {
        $configs = $this->configurations();

        $params = [
            'link' => $this->getUrl(),
            'apn' => $configs->getAndroidPackageName(),
            'ibi' => $configs->getIosBundleId(),
            'ipbi' => $configs->getIosIpadBundleId(),
            'isi' => $configs->getIosAppStoreId(),
        ];

        if ($this instanceof SocialMetaTagsDynamicLink) {
            $params['st'] = $this->metaTitle;

            if ($this->metaDescription) {
                $params['sd'] = $this->metaDescription;
            }

            if ($this->metaImageUrl) {
                $params['si'] = $this->metaImageUrl;
            }
        }

        return http_build_query($params);
    }
}
