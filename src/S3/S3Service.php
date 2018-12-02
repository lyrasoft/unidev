<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2018 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Lyrasoft\Unidev\S3;

use Aws\CommandInterface;
use Aws\S3\S3Client;
use Windwalker\Core\Config\Config;
use Windwalker\Filesystem\File;
use Windwalker\Filesystem\Path;
use Windwalker\Http\Stream\Stream;

/**
 * The S3Service class.
 *
 * @see https://aws.amazon.com/tw/documentation/sdk-for-php/
 * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html
 * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.S3.S3Client.html
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
     * getObject
     *
     * @param array $args
     *
     * @return  \Aws\Result
     *
     * @since  1.5.1
     */
    public function getObject(array $args)
    {
        return $this->runCommand('GetObject', $args);
    }

    /**
     * getFile
     *
     * @param string $path
     * @param array  $args
     *
     * @return  \Aws\Result
     *
     * @since  1.5.1
     */
    public function getFileInfo($path, array $args = [])
    {
        $args['Key'] = $path;

        return $this->getObject($args);
    }

    /**
     * getPreSignedUrl
     *
     * @param string $path     The file path.
     * @param string $expires  Use DateTime syntax, example: `+300seconds`
     * @param array  $args     Arguments.
     *
     * @return  \Psr\Http\Message\UriInterface
     *
     * @since  1.5.1
     */
    public function getPreSignedUrl($path, $expires, array $args = [])
    {
        $args['Key'] = $path;

        $cmd = $this->getCommand('GetObject', $args);

        return $this->client->createPresignedRequest($cmd, $expires)
            ->getUri();
    }

    /**
     * getPreSignedUrlWithFilename
     *
     * @param string $path     The file path.
     * @param string $expires  Use DateTime syntax, example: `+300seconds`
     * @param string $filename File name to save to local.
     * @param array  $args     Arguments.
     *
     * @return  \Psr\Http\Message\UriInterface
     *
     * @since  1.5.2
     */
    public function getPreSignedUrlWithFilename($path, $expires, $filename, array $args = [])
    {
        $args['Key'] = $path;
        $args['ResponseContentDisposition'] = sprintf(
            "attachment; filename*=UTF-8''%s",
            rawurlencode(File::makeUtf8Safe($filename))
        );

        $cmd = $this->getCommand('GetObject', $args);

        return $this->client->createPresignedRequest($cmd, $expires)
            ->getUri();
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
        return $this->runCommand('PutObject', $args);
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
     * @param array $args
     *
     * @return  \Aws\Result
     *
     * @since  1.5.1
     */
    public function deleteObject(array $args)
    {
        return $this->runCommand('DeleteObject', $args);
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
    public function deleteFile($path, array $args = [])
    {
        $args['Key'] = $path;

        return $this->deleteObject($args);
    }

    /**
     * command
     *
     * @param string $name
     * @param array  $args
     *
     * @return  \Aws\Result
     *
     * @since  1.5.1
     */
    public function runCommand($name, array $args = [])
    {
        $cmd = $this->getCommand($name, $args);

        return $this->client->execute($cmd);
    }

    /**
     * getCommand
     *
     * @param string $name
     * @param array  $args
     *
     * @return  CommandInterface
     *
     * @since  1.5.1
     */
    public function getCommand($name, array $args = [])
    {
        if (!isset($args['Bucket'])) {
            $args['Bucket'] = $this->getBucketName();
        }

        if (isset($args['Key'])) {
            $args['Key'] = ltrim(Path::clean($this->getSubfolder() . '/' . $args['Key'], '/'), '/');
        }

        return $this->client->getCommand($name, $args);
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
        $uri = $this->client->getEndpoint();

        $host = $uri->getHost();

        if (strpos($host, 's3.amazonaws.com') === 0) {
            $host = $this->getBucketName() . '.' . $host;
            $uri = $uri->withHost($host);
        }

        $subfolder = $subfolder ? '/' . $this->getSubfolder() : null;

        return rtrim($uri . $subfolder, '/');
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
