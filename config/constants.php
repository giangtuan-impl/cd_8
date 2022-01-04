<?php

return [
    'DEFAULT_IMAGE' => 'assets/image/avatar.png',
    'DEFAULT_IMAGE_APP' => 'assets/image/img-icon.png',
    'DEFAULT_IMAGE_FOLDER' => 'image/',
    'DEFAULT_ICON_FOLDER' => 'icons/',
    'IOS_BUILD_NAME' => 'ios_name',
    'ANDROID_BUILD_NAME' => 'android_name',
    'JENKINS_BUILD_FOLDER_PREFIX' => '/build-jenkins-',
    'IOS_PARAMS' => [
        'IPA_FOLDER' => '/',
        'IPA_EXTENSION' => '.ipa',
        'PROVISIONED_DEVICES_TAG' => '<key>ProvisionedDevices</key>',
        'CF_BUNDLE_IDENTIFIER_TAG' => '<key>CFBundleIdentifier</key>',
        'CF_BUNDLE_SHORT_VERSION_STRING_TAG' => '<key>CFBundleShortVersionString</key>',
        'CF_BUNDLE_VERSION_TAG' => '<key>CFBundleVersion</key>',
        'CF_BUNDLE_NAME_TAG' => '<key>CFBundleName</key>',
        'START_ARRAY_TAG' => '<array>',
        'END_ARRAY_TAG' => '</array>',
        'START_STRING_TAG' => '<string>',
        'END_STRING_TAG' => '</string>',
        'MOBILE_PROVISION_FILE_NAME' => "embedded.mobileprovision",
        'INFO_LIST_FILE_NAME' => "Info.plist",
        'EXTRACT_FOLDER_NAME' => 'Payload/',
        'APP_EXTENSION' => '.app',
        'APP_ICON_FILE_NAME' => 'AppIcon60x60@2x.png',
        'PLIST_FILE_TEMPLATE_PATH' => 'layouts.plist_file_template',
        'JSON_EXTENSION' => '.json',
        'PLIST_EXTENSION' => '.plist',
        'PLIST_DOWNLOAD_LINK' => 'itms-services://?action=download-manifest&url='
    ],
    'ANDROID_PARAMS' => [
        'APK_EXTENSION' => 'apk',
        'MANIFEST_FILE_NAME' => 'AndroidManifest.xml',
        'RESOURCE_FOLDER_NAME' => 'res',
    ],
    'JENKINS_BUILD' => env('JENKINS_BUILD'),
    'RESET_PASSWORD_TOKEN' => Illuminate\Support\Str::random(60)
];
