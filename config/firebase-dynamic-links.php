<?php

return [
    /*
    | Supported: "google_firebase", "manual",
    */
    'generation_method' => env('FIREBASE_DYNAMIC_GENERATION_METHOD', 'google_firebase'),

    'domain_uri_prefix' => env('FIREBASE_DYNAMIC_URI', 'https://example.page.link'),

    'link' => env('FIREBASE_DYNAMIC_LINK', 'https://your-site.com'),

    'android_info' => [
        'android_package_name' => env('FIREBASE_DYNAMIC_ANDROID_PACKAGE_NAME', 'com.application.android'),
    ],

    'ios_info' => [
        'iosBundleId' => env('FIREBASE_DYNAMIC_IOS_BUNDLE_ID', 'com.application.ios'),
        'iosAppStoreId' => env('FIREBASE_DYNAMIC_APP_STORE_ID', '0123456789'),
        'iosIpadBundleId' => env('FIREBASE_DYNAMIC_IOS_IPAD_BUNDLE_ID', 'com.application.ipad'),
    ],

    // Here is configuration for "google_firebase" builder
    /*
    |
    | 1. Sign in to the Google Cloud console: https://console.cloud.google.com/
    | 2. Create a project if it has not been created yet. To do this, click on the "Create Project"
    | button in the upper right corner of the page and enter the project name.
    | 3. Select the created project from the list on the "Dashboard" page.
    | 4. Open the side menu and select "APIs & Services".
    | 5. On the "APIs & Services" page, click on the "Enable APIs and Services" button.
    | 6. Select the required service, API that you want to use, and click "Enable".
    | 7. Go to the "Credentials" page and click "Create Credentials".
    | 8. Select "API Key" from the list of options.
    | 9. Configure the API key restrictions if necessary and click the "Create" button.
    | 10. The API key will be created. Copy the key and use it to access the relevant Google services.
    |
    */
    'google_api_key' => env('FIREBASE_DYNAMIC_GOOGLE_API_KEY', null),

    'suffix' => [
        /*
        |
        | There are two options: SHORT & UNGUESSABLE
        |
        | By default, or if you set the parameter to "UNGUESSABLE",
        | the path component will be a 17-character string.
        | If you set the parameter to "SHORT", the path component will be
        | a string that is only as long as needed to be unique, with
        | a minimum length of 4 characters.
        |
        */
        'option' => env('FIREBASE_DYNAMIC_LINK_TYPE', 'SHORT'),
    ],
];
