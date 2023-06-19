<?php

namespace App\Services\FirebaseDynamicLinks;

class DynamicLinkConfig
{
    private string $apiKey;

    private string $domainUriPrefix;

    private string $link;

    private string $androidPackageName;

    private string $iosBundleId;

    private string $iosAppStoreId;

    private string $iosIpadBundleId;

    private string $linkType;

    public function __construct()
    {
        $this->apiKey               = config('firebase-dynamic-links.google_api_key');
        $this->domainUriPrefix      = config('firebase-dynamic-links.domain_uri_prefix');
        $this->link                 = config('firebase-dynamic-links.link');

        $this->androidPackageName   = config('firebase-dynamic-links.android_info.android_package_name');

        $this->iosBundleId          = config('firebase-dynamic-links.ios_info.iosBundleId');
        $this->iosAppStoreId        = (int) config('firebase-dynamic-links.ios_info.iosAppStoreId');
        $this->iosIpadBundleId      = config('firebase-dynamic-links.ios_info.iosIpadBundleId');

        $this->linkType             = config('firebase-dynamic-links.suffix.option');
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getDomainUrlPrefix(): string
    {
        return $this->domainUriPrefix;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getAndroidPackageName(): string
    {
        return $this->androidPackageName;
    }

    public function getIosBundleId(): string
    {
        return $this->iosBundleId;
    }

    public function getIosAppStoreId(): string
    {
        return (string) $this->iosAppStoreId;
    }

    public function getIosIpadBundleId(): string
    {
        return $this->iosIpadBundleId;
    }

    public function getLinkType(): string
    {
        return $this->linkType;
    }
}
