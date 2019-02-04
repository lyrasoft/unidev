<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Lyrasoft\Unidev\Script;

use Aws\Credentials\Credentials;
use Lyrasoft\Unidev\S3\S3Service;
use Lyrasoft\Unidev\UnidevPackage;
use Phoenix\Script\PhoenixScript;
use Windwalker\Core\Asset\AbstractScript;
use Windwalker\Ioc;

/**
 * The AwsScript class.
 *
 * @since  __DEPLOY_VERSION__
 */
class AwsScript extends AbstractScript
{
    /**
     * Property packageClass.
     *
     * @var  string
     */
    protected static $packageClass = UnidevPackage::class;

    public static function s3BrowserUploader(string $name, string $acl = S3Service::ACL_PUBLIC_READ)
    {
        if (!static::inited(__METHOD__)) {
            static::addJS(static::packageName() . '/js/aws/s3-uploader.min.js');
        }

        if (!static::inited(__METHOD__, get_defined_vars())) {
            $s3 = Ioc::make(S3Service::class);
            /** @var Credentials $credentials */
            $credentials = $s3->getClient()->getCredentials()->wait();

            $policy = [
                'expiration' => '2030-12-01T12:00:00.000Z',
                'conditions' => [
                    ['bucket' => $s3->getBucketName()],
                    ['acl' => $acl],
                    ['starts-with', '$key', ''],
                    ['starts-with', '$Content-Type', ''],
                    ['starts-with', '$Content-Disposition', ''],
                ]
            ];

            $accessKey = $credentials->getAccessKeyId();
            $bucket    = $s3->getBucketName();
            $subfolder = $s3->getSubfolder();
            $endpoint  = $s3->getHost(false)->__toString();
            $region    = $s3->getClient()->getRegion();
            $policy    = base64_encode(json_encode($policy));
            $signature = base64_encode(hash_hmac('sha1', $policy, $credentials->getSecretKey(), true));

            $options = static::getJSObject(
                compact(
                    'policy',
                    'signature',
                    'bucket',
                    'endpoint',
                    'subfolder',
                    'region',
                    'accessKey',
                    'acl'
                )
            );

            $js = <<<JS
S3Uploader.getInstance('$name', $options);
JS;

            PhoenixScript::domready($js);
        }
    }
}
