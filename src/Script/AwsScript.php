<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Lyrasoft\Unidev\Script;

use Aws\Credentials\Credentials;
use Aws\S3\PostObjectV4;
use Lyrasoft\Unidev\S3\S3Service;
use Lyrasoft\Unidev\UnidevPackage;
use Windwalker\Legacy\Core\Asset\AbstractScript;
use Windwalker\Legacy\Ioc;

/**
 * The AwsScript class.
 *
 * @since  1.5.6
 */
class AwsScript extends AbstractScript
{
    /**
     * Property packageClass.
     *
     * @var  string
     */
    protected static $packageClass = UnidevPackage::class;

    /**
     * s3BrowserUploader
     *
     * @param string $name
     * @param string $acl
     * @param array  $options
     *
     * @return  void
     *
     * @since  1.5.6
     */
    public static function s3BrowserUploader(
        string $name,
        string $acl = S3Service::ACL_PUBLIC_READ,
        array $options = []
    ) {
        if (!static::inited(__METHOD__)) {
            static::addJS(static::packageName() . '/js/aws/s3-uploader.min.js');
        }

        if (!static::inited(__METHOD__, get_defined_vars())) {
            $options = static::mergeOptions([
                'starts_with' => [
                    'key' => '',
                    'Content-Type' => '',
                    'Content-Disposition' => ''
                ]
            ], $options);

            $s3 = Ioc::make(S3Service::class);

            $bucket    = $s3->getBucketName();
            $subfolder = $s3->getSubfolder();
            $endpoint  = $s3->getHost(false)->__toString();

            $conditions = [
                ['bucket' => $s3->getBucketName()],
                ['acl' => $acl],
            ];

            $defaultInputs = [
                'bucket' => $s3->getBucketName(),
                'acl' => $acl
            ];

            foreach ($options['starts_with'] as $key => $value) {
                $conditions[] = ['starts-with', '$' . $key, $value];
                $defaultInputs[$key] = '';
            }

            $postObject = new PostObjectV4(
                $s3->getClient(),
                $bucket,
                $defaultInputs,
                $conditions,
                '+2hours'
            );

            $formInputs = $postObject->getFormInputs();

            $optionString = static::getJSObject(
                $options,
                compact(
                    'endpoint',
                    'subfolder',
                    'formInputs'
                )
            );

            $js = <<<JS
S3Uploader.getInstance('$name', $optionString);
JS;

            static::internalJS($js);
        }
    }
}
