<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/21
 * Time: 16:34
 */

namespace App\Adapters\ExtendFileStorage;


use Carbon\Carbon;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\Adapter\Polyfill\StreamedReadingTrait;
use League\Flysystem\Config;
use Log;
use Qiniu\Auth;
use Qiniu\Cdn\CdnManager;
use Qiniu\Http\Error;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

class QiniuAdapter extends AbstractAdapter
{

    use NotSupportingVisibilityTrait, StreamedReadingTrait;

    /**
     * @var string
     */
    protected $accessKey;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var string
     */
    protected $bucket;

    /**
     * @var string
     */
    protected $cdnDomain;

    /**
     * @var bool
     */
    protected $ssl;

    /**
     * @var \Qiniu\Auth
     */
    protected $authManager;

    /**
     * @var \Qiniu\Storage\UploadManager
     */
    protected $uploadManager;

    /**
     * @var \Qiniu\Storage\BucketManager
     */
    protected $bucketManager;

    /**
     * @var \Qiniu\Cdn\CdnManager
     */
    protected $cdnManager;

    public function __construct($authManager, $bucket, $ssl = false, $cdnDomain)
    {
        $this->authManager = $authManager;
        $this->bucket      = $bucket;
        $this->ssl         = $ssl;
        $this->cdnDomain   = $cdnDomain;
        $this->setBucketManager($authManager);
        $this->setUploadManager();
        $this->setCdnManager($authManager);
    }

    /**
     * @param Auth $auth
     *
     * @return $this
     */
    public function setBucketManager(Auth $auth)
    {
        $this->bucketManager = new BucketManager($auth);
        return $this;
    }

    /**
     * @return BucketManager
     */
    public function getBucketManager()
    {
        return $this->bucketManager ?: $this->bucketManager = new BucketManager($this->authManager);
    }

    /**
     * @return $this
     */
    public function setUploadManager()
    {
        $this->uploadManager = new UploadManager();
        return $this;
    }

    /**
     * @return UploadManager
     */
    public function getUploadManager()
    {
        return $this->uploadManager ?: $this->uploadManager = new UploadManager();
    }

    /**
     * @param Auth $auth
     *
     * @return $this
     */
    public function setCdnManager(Auth $auth)
    {
        $this->cdnManager = new CdnManager($auth);
        return $this;
    }

    /**
     * @return CdnManager
     */
    public function getCdnManager()
    {
        return $this->cdnManager ?: $this->cdnManager = new CdnManager($this->authManager);
    }

    /**
     * bucket
     *
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * Get the upload token.
     *
     * @param string|null $key
     * @param int         $expires
     * @param string|null $policy
     * @param string|null $strictPolice
     *
     * @return string
     */
    public function getUploadToken($key = null, $expires = 3600, $policy = null, $strictPolice = null)
    {
        return $this->authManager->uploadToken($this->bucket, $key, $expires, $policy, $strictPolice);
    }

    /**
     * Write a new file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function write($path, $contents, Config $config)
    {
        $params = $config->get('params', null);
        $mime   = $config->get('mime', 'application/octet-stream');

        $uploadToken = $this->getUploadToken($path);
        list($response, $error) = $this->getUploadManager()->put($uploadToken, $path, $contents, $params, $mime, true);

        if ($error) {
            $this->logError(__FUNCTION__, $error);
            return false;
        }
        return $response;
    }

    /**
     * Write a new file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream($path, $resource, Config $config)
    {

        $contents = stream_get_contents($resource);
        return $this->write($path, $contents, $config);
    }

    /**
     * Update a file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function update($path, $contents, Config $config)
    {
        $this->delete($path);
        return $this->write($path, $contents, $config);
    }

    /**
     * Update a file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function updateStream($path, $resource, Config $config)
    {
        $this->delete($path);
        return $this->writeStream($path, $resource, $config);
    }

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $new_path
     *
     * @return bool
     */
    public function rename($path, $new_path)
    {
        $error = $this->getBucketManager()->move($this->bucket, $path, $this->bucket, $new_path);
        if ($error !== null) {
            $this->logError(__FUNCTION__, $error);
            return false;
        } else {
            return true;
        }
    }

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $new_path
     *
     * @return bool
     */
    public function copy($path, $new_path)
    {
        $error = $this->getBucketManager()->copy($this->bucket, $path, $this->bucket, $new_path);
        if ($error !== null) {
            $this->logError(__FUNCTION__, $error);
            return false;
        } else {
            return true;
        }
    }

    /**
     * Delete a file or multiple files.
     *
     * @param string|array $path
     *
     * @return bool
     */
    public function delete($path)
    {

        $error = $this->getBucketManager()->delete($this->bucket, $path);
        if ($error !== null) {
            $this->logError(__FUNCTION__, $error);
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {
        $files = $this->listContents($dirname);
        foreach ($files as $file) {
            $this->delete($file['path']);
        }

        return true;
    }

    /**
     * Create a directory.
     *
     * @param string $dirname directory name
     * @param Config $config
     *
     * @return array|false
     */
    public function createDir($dirname, Config $config)
    {
        return ['path' => $dirname, 'type' => 'dir'];
    }

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return array|bool|null
     */
    public function has($path)
    {
        $meta = $this->getMetadata($path);
        if ($meta) {
            return true;
        }

        return false;
    }

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read($path)
    {
        $location = $this->applyPathPrefix($path);

        return ['contents' => file_get_contents($location)];
    }

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {
        list($ret, $error) = $this->getBucketManager()->listFiles($this->bucket, $directory);
        if ($error !== null) {
            $this->logError(__FUNCTION__, $error);
            return [];
        }

        $contents = [];
        foreach (isset($ret[0]['items']) ? $ret[0]['items'] : [] as $item) {
            $contents[] = [
                'type'      => 'file',
                'path'      => $item['key'],
                'timestamp' => $item['putTime'],
                'size'      => $item['fsize']
            ];
        }

        return $contents;
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path)
    {
        list($ret, $error) = $this->getBucketManager()->stat($this->bucket, $path);
        if ($error !== null) {
            return false;
        } else {
            return $ret;
        }
    }

    /**
     * Get the size of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getSize($path)
    {
        if ($object = $this->getMetadata($path)) {
            return ['size' => $object['fsize']];
        }

        return false;
    }

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMimetype($path)
    {
        if ($object = $this->getMetadata($path)) {
            return ['size' => $object['mimeType']];
        }

        return false;
    }

    /**
     * Get the last modified time of a file as a timestamp.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getTimestamp($path)
    {
        if ($object = $this->getMetadata($path)) {
            return ['timestamp' => $object['putTime']];
        }

        return false;
    }

    /**
     * @DriverFunction
     *
     * @param mixed $path
     *
     * @return string
     */
    public function getUrl($path)
    {
        return ($this->ssl ? 'https://' : 'http://') . $this->cdnDomain . '/' . ltrim($path, '/');
    }

    /**
     * @DriverFunction
     *
     * @param     $path
     * @param int $expiration
     *
     * @return string
     */
    public function getTemporaryUrl($path, $expiration = 3600)
    {
        $url = $this->getUrl($path);

        if ($expiration instanceof Carbon) {
            $expiration = $expiration->diffInSeconds(Carbon::now());
        }

        $expiration = intval($expiration);
        return $this->authManager->privateDownloadUrl($url, $expiration);
    }

    /**
     * @param       $fun string function name : __FUNCTION__
     * @param Error $error
     */
    protected function logError($fun, Error $error)
    {
        Log::error($fun . ": FAILED => " . $error->code() . ' ' . $error->message());
    }
}
