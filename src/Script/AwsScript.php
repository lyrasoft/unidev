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
use Phoenix\Script\CoreScript;
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

    public static function s3BrowserUploader(string $name, array $options = [])
    {
        if (!static::inited(__METHOD__)) {
            static::addJS(static::packageName() . '/js/aws/s3-uploader.min.js');
        }

        if (!static::inited(__METHOD__, get_defined_vars())) {
            $options = static::mergeOptions([
                'acl' => S3Service::ACL_PUBLIC_READ,
                'starts_with' => [
                    'key' => '',
                    'Content-Type' => '',
                    'Content-Disposition' => ''
                ]
            ], $options);


            $s3 = Ioc::make(S3Service::class);
            /** @var Credentials $credentials */
            $credentials = $s3->getClient()->getCredentials()->wait();

            $accessKey = $credentials->getAccessKeyId();
            $bucket    = $s3->getBucketName();
            $subfolder = $s3->getSubfolder();
            $endpoint  = $s3->getHost(false)->__toString();
            $region    = $s3->getClient()->getRegion();
            $acl       = $options['acl'];

            $policy = [
                'expiration' => '2030-12-01T12:00:00.000Z',
                'conditions' => [
                    ['bucket' => $s3->getBucketName()],
                    ['acl' => $acl],
                ]
            ];

            foreach ($options['starts_with'] as $key => $value) {
                $policy['conditions'][] = ['starts-with', '$' . $key, $value];
            }

            $policy    = base64_encode(json_encode($policy));
            $signature = base64_encode(hash_hmac('sha1', $policy, $credentials->getSecretKey(), true));

            $optionString = static::getJSObject(
                $options,
                compact(
                    'policy',
                    'signature',
                    'bucket',
                    'endpoint',
                    'subfolder',
                    'region',
                    'accessKey'
                )
            );

            $js = <<<JS
S3Uploader.getInstance('$name', $optionString);
JS;

            static::internalJS($js);
        }
    }
}
