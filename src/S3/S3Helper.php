<?php
/**
 * Part of eng4tw project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\S3;

use Windwalker\Core\Facade\AbstractProxyFacade;

/**
 * The S3Helper class.
 *
 * @see \S3
 *
 * @method  static  void         setEndpoint($host)
 * @method  static  void         setAuth($accessKey, $secretKey)
 * @method  static  boolean      hasAuth()
 * @method  static  void         setSSL($enabled, $validate = true)
 * @method  static  void         setSSLAuth($sslCert = null, $sslKey = null, $sslCACert = null)
 * @method  static  void         setProxy($host, $user = null, $pass = null, $type = CURLPROXY_SOCKS5)
 * @method  static  void         setExceptions($enabled = true)
 * @method  static  void         setTimeCorrectionOffset($offset = 0)
 * @method  static  void         setSigningKey($keyPairId, $signingKey, $isFile = true)
 * @method  static  boolean      freeSigningKey()
 * @method  static  array|false  listBuckets($detailed = false)
 * @method  static  array|false  getBucket($bucket, $prefix = null, $marker = null, $maxKeys = null, $delimiter = null, $returnCommonPrefixes = false)
 * @method  static  boolean      putBucket($bucket, $acl = \S3::ACL_PRIVATE, $location = false)
 * @method  static  boolean      deleteBucket($bucket)
 * @method  static  array|false  inputFile($file, $md5sum = true)
 * @method  static  array|false  inputResource(&$resource, $bufferSize = false, $md5sum = '')
 * @method  static  boolean      putObject($input, $bucket, $uri, $acl = \S3::ACL_PRIVATE, $metaHeaders = array(), $requestHeaders = array(), $storageClass = \S3::STORAGE_CLASS_STANDARD, $serverSideEncryption = \S3::SSE_NONE)
 * @method  static  boolean      putObjectFile($file, $bucket, $uri, $acl = \S3::ACL_PRIVATE, $metaHeaders = array(), $contentType = null)
 * @method  static  boolean      putObjectString($string, $bucket, $uri, $acl = \S3::ACL_PRIVATE, $metaHeaders = array(), $contentType = 'text/plain')
 * @method  static  mixed        getObject($bucket, $uri, $saveTo = false)
 * @method  static  mixed|false  getObjectInfo($bucket, $uri, $returnInfo = true)
 * @method  static  mixed|false  copyObject($srcBucket, $srcUri, $bucket, $uri, $acl = \S3::ACL_PRIVATE, $metaHeaders = array(), $requestHeaders = array(), $storageClass = \S3::STORAGE_CLASS_STANDARD)
 * @method  static  boolean      setBucketRedirect($bucket = NULL, $location = NULL)
 * @method  static  boolean      setBucketLogging($bucket, $targetBucket, $targetPrefix = null)
 * @method  static  array|false  getBucketLogging($bucket)
 * @method  static  boolean       disableBucketLogging($bucket)
 * @method  static  string|false  getBucketLocation($bucket)
 * @method  static  boolean       setAccessControlPolicy($bucket, $uri = '', $acp = array())
 * @method  static  mixed|false  getAccessControlPolicy($bucket, $uri = '')
 * @method  static  boolean      deleteObject($bucket, $uri)
 * @method  static  string       getAuthenticatedURL($bucket, $uri, $lifetime, $hostBucket = false, $https = false)
 * @method  static  string       getSignedPolicyURL($policy)
 * @method  static  string       getSignedCannedURL($url, $lifetime)
 * @method  static  \stdClass    getHttpUploadPostParams($bucket, $uriPrefix = '', $acl = \S3::ACL_PRIVATE, $lifetime = 3600, $maxFileSize = 5242880, $successRedirect = "201", $amzHeaders = array(), $headers = array(), $flashVars = false)
 * @method  static  array|false  createDistribution($bucket, $enabled = true, $cnames = array(), $comment = null, $defaultRootObject = null, $originAccessIdentity = null, $trustedSigners = array())
 * @method  static  array|false  getDistribution($distributionId)
 * @method  static  array|false  updateDistribution($dist)
 * @method  static  boolean      deleteDistribution($dist)
 * @method  static  array        listDistributions()
 * @method  static  array        listOriginAccessIdentities()
 * @method  static  boolean      invalidateDistribution($distributionId, $paths)
 * @method  static  array        getDistributionInvalidationList($distributionId)
 * @method  static  integer      __getTime()
 * @method  static  string       __getSignature($string)
 *
 * @since  {DEPLOY_VERSION}
 */
class S3Helper extends AbstractProxyFacade
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
	 * Property _key.
	 *
	 * @var  string
	 */
	protected static $_key = 'unidev.s3';

	/**
	 * Put an object from a file.
	 *
	 * @param string $file        Input data
	 * @param string $uri         Object URI
	 * @param string $acl         ACL constant
	 * @param array  $metaHeaders Array of x-amz-meta-* headers
	 * @param string $contentType Content type
	 *
	 * @return  boolean
	 */
	public static function upload($file, $uri, $acl = \S3::ACL_PUBLIC_READ, $metaHeaders = array())
	{
		return static::putObject(\S3::inputFile($file, false), static::getBucketName(), $uri, $acl, $metaHeaders);
	}

	/**
	 * Delete an object.
	 *
	 * @param string $uri Object URI
	 *
	 * @return  boolean
	 */
	public static function delete($uri)
	{
		return static::deleteObject(static::getBucketName(), $uri);
	}

	/**
	 * getEndpoint
	 *
	 * @return  string
	 */
	public static function getEndpoint()
	{
		return \S3::$endpoint;
	}

	/**
	 * getBucket
	 *
	 * @return  string
	 */
	public static function getBucketName()
	{
		return static::getContainer()->get('system.config')->get('amazon.bucket');
	}

	/**
	 * getHost
	 *
	 * @return  string
	 */
	public static function getHost()
	{
		return 'https://' . static::getBucketName() . '.' . static::getEndpoint();
	}
}
