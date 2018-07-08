<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2018 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Lyrasoft\Unidev\S3;

use Aws\S3\S3Client;
use Windwalker\Core\Config\Config;
use Windwalker\Filesystem\Path;
use Windwalker\Http\Stream\Stream;

/**
 * The S3Service class.
 *
 * @since  1.4
 */
class S3Service
{
    const ACL_PRIVATE = 'private';
    const ACL_PUBLIC_READ = 'public-read';
    const ACL_PUBLIC_READ_WRITE = 'public-read-write';
    const ACL_AUTHENTICATED_READ = 'authenticated-read';

    const STORAGE_CLASS_STANDARD = 'STANDARD';
    const STORAGE_CLASS_RRS = 'REDUCED_REDUNDANCY';

    const SSE_NONE = '';
    const SSE_AES256 = 'AES256';

    /**
     * Property s3.
     *
     * @var  S3Client
     */
    protected $client;

    /**
     * Property config.
     *
     * @var  Config
     */
    protected $config;

    /**
     * S3Service constructor.
     *
     * @param S3Client $s3
     * @param Config   $config
     */
    public function __construct(S3Client $s3, Config $config)
    {
        $this->client = $s3;
        $this->config = $config;
    }

    /**
     * putObject
     *
     * @param array $args
     *
     * @return  \Aws\Result
     *
     * @since  1.4
     */
    public function putObject(array $args)
    {
        $args['Bucket'] = $this->getBucketName();

        $args['Key'] = Path::clean($this->getSubfolder() . '/' . $args['Key'], '/');

        return $this->client->putObject($args);
    }

    /**
     * uploadFile
     *
     * @param string $file
     * @param string $path
     * @param array  $args
     *
     * @return  \Aws\Result
     *
     * @since  1.4
     */
    public function uploadFile($file, $path, array $args = [])
    {
        $args['Key'] = $path;
        $args['Body'] = $stream = new Stream($file, Stream::MODE_READ_ONLY_FROM_BEGIN);

        $result = $this->putObject($args);

        $stream->close();
        unset($stream);

        return $result;
    }

    /**
     * uploadFile
     *
     * @param string|Stream $data
     * @param string        $path
     * @param array         $args
     *
     * @return  \Aws\Result
     *
     * @since  1.4
     */
    public function uploadFileData($data, $path, array $args = [])
    {
        $args['Key'] = $path;
        $args['Body'] = $data;

        return $this->putObject($args);
    }

    /**
     * deleteObject
     *
     * @param string $path
     * @param array  $args
     *
     * @return  \Aws\Result
     *
     * @since  1.4
     */
    public function deleteObject($path, array $args = [])
    {
        $args['Bucket'] = $this->getBucketName();

        $args['Key'] = Path::clean($this->getSubfolder() . '/' . $path, '/');

        return $this->client->deleteObject($args);
    }

    /**
     * getKey
     *
     * @return  string
     *
     * @since  1.4
     */
    public function getKey()
    {
        return $this->config->get('unidev.amazon.key');
    }

    /**
     * getBucket
     *
     * @return  string
     */
    public function getBucketName()
    {
        $bucket = $this->config->get('unidev.amazon.bucket');

        if (!$bucket) {
            throw new \UnexpectedValueException('Please enter bucket first.');
        }

        return $bucket;
    }

    /**
     * getSubfolder
     *
     * @return  string
     *
     * @since  1.4
     */
    public function getSubfolder()
    {
        return $this->config->get('unidev.amazon.subfolder');
    }

    /**
     * getHost
     *
     * @param bool $subfolder
     *
     * @return string
     */
    public function getHost($subfolder = true)
    {
        $host = $this->client->getEndpoint();

        $subfolder = $subfolder ? '/' . $this->getSubfolder() : null;

        return rtrim($host . $subfolder, '/');
    }

    /**
     * Method to get property S3Client
     *
     * @return  S3Client
     *
     * @since  1.4
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Method to set property s3Client
     *
     * @param   S3Client $client
     *
     * @return  static  Return self to support chaining.
     *
     * @since  1.4
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }
}
