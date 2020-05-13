<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/21
 * Time: 16:34
 */

namespace App\Adapters\ExtendFileStorage;


use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\Util;
use OSS\Core\OssException;
use OSS\OssClient;

class AliOssAdapter extends AbstractAdapter
{

    /**
     * oss 返回数据格式
     *
     * @var array
     */
    protected static $resultMap = [
        'Body'           => 'raw_contents',
        'Content-Length' => 'size',
        'ContentType'    => 'mimetype',
        'Size'           => 'size',
        'StorageClass'   => 'storage_class',
    ];

    /**
     * oss 请求meta
     *
     * @var array
     */
    protected static $metaOptions = [
        'CacheControl',
        'Expires',
        'ServerSideEncryption',
        'Metadata',
        'ACL',
        'ContentType',
        'ContentDisposition',
        'ContentLanguage',
        'ContentEncoding',
    ];

    protected static $metaMap = [
        'CacheControl'         => 'Cache-Control',
        'Expires'              => 'Expires',
        'ServerSideEncryption' => 'x-oss-server-side-encryption',
        'Metadata'             => 'x-oss-metadata-directive',
        'ACL'                  => 'x-oss-object-acl',
        'ContentType'          => 'Content-Type',
        'ContentDisposition'   => 'Content-Disposition',
        'ContentLanguage'      => 'response-content-language',
        'ContentEncoding'      => 'Content-Encoding',
    ];

    protected $client;

    protected $bucket;

    protected $endPoint;

    protected $ssl;

    protected $isCName;

    protected $cdnDomain;

    //配置
    protected $options = [
        'Multipart' => 128
    ];

    /**
     * AliOssAdapter constructor.
     *
     * @param OssClient $ossClient
     * @param           $bucket
     * @param           $endPoint
     * @param           $ssl
     * @param bool      $isCName
     * @param           $cdnDomain
     * @param null      $prefix
     * @param array     $options
     */
    public function __construct(
        OssClient $ossClient,
        $bucket,
        $endPoint,
        $ssl,
        $isCName = false,
        $cdnDomain,
        $prefix = null,
        array $options = []
    ) {
        $this->client    = $ossClient;
        $this->bucket    = $bucket;
        $this->endPoint  = $endPoint;
        $this->ssl       = $ssl;
        $this->isCName   = $isCName;
        $this->cdnDomain = $cdnDomain;
        $this->options   = array_merge($this->options, $options);
        $this->setPathPrefix(null);
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
     * client
     *
     * @return OssClient
     */
    public function getClient()
    {
        return $this->client;
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
        $object  = $this->applyPathPrefix($path);
        $options = $this->getOptions($this->options, $config);

        if (!isset($options[OssClient::OSS_LENGTH])) {
            $options[OssClient::OSS_LENGTH] = Util::contentSize($contents);
        }
        if (!isset($options[OssClient::OSS_CONTENT_TYPE])) {
            $options[OssClient::OSS_CONTENT_TYPE] = Util::guessMimeType($path, $contents);
        }
        $this->client->putObject($this->bucket, $object, $contents, $options);
        return $this->normalizeResponse($options, $path);
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
     * Write a new file using a file.
     *
     * @param string $path
     * @param string $filePath
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeFile($path, $filePath, Config $config)
    {
        $object                            = $this->applyPathPrefix($path);
        $options                           = $this->getOptions($this->options, $config);
        $options[OssClient::OSS_CHECK_MD5] = true;
        if (!isset($options[OssClient::OSS_CONTENT_TYPE])) {
            $options[OssClient::OSS_CONTENT_TYPE] = Util::guessMimeType($path, '');
        }
        try {
            $this->client->uploadFile($this->bucket, $object, $filePath, $options);
        } catch (OssException $e) {
            $this->logError(__FUNCTION__, $e);
            return false;
        }
        return $this->normalizeResponse($options, $path);
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
        if (!$config->has('visibility') && !$config->has('ACL')) {
            $config->set(static::$metaMap['ACL'], $this->getObjectACL($path));
        }

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
        $contents = stream_get_contents($resource);
        return $this->update($path, $contents, $config);
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
        if (!$this->copy($path, $new_path)) {
            return false;
        }

        return $this->delete($path);
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
        $object    = $this->applyPathPrefix($path);
        $newObject = $this->applyPathPrefix($new_path);
        try {
            $this->client->copyObject($this->bucket, $object, $this->bucket, $newObject);
        } catch (OssException $e) {
            $this->logError(__FUNCTION__, $e);
            return false;
        }

        return true;
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
        if (is_string($path)) {
            $object = $this->applyPathPrefix($path);

            $this->client->deleteObject($this->bucket, $object);

        } elseif (is_array($path) && $path) {
            $this->client->deleteObjects($this->bucket, $path);
        }

        return true;
    }

    /**
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {
        $dirname = rtrim($this->applyPathPrefix($dirname), '/') . '/';
        try {
            $dirObjects = $this->listDirObjects($dirname, true);
        } catch (OssException $e) {
            $this->logError(__FUNCTION__, $e);
            return false;
        }

        if (count($dirObjects['objects']) > 0) {
            $objects = [];
            foreach ($dirObjects['objects'] as $object) {
                $objects[] = $object['Key'];
            }

            $this->client->deleteObjects($this->bucket, $objects);

        }

        $this->client->deleteObject($this->bucket, $dirname);

        return true;
    }

    /**
     * Create a directory.
     *
     * @param string $dirName directory name
     * @param Config $config
     *
     * @return array|false
     */
    public function createDir($dirName, Config $config)
    {
        $object  = $this->applyPathPrefix($dirName);
        $options = $this->getOptionsFromConfig($config);

        $this->client->createObjectDir($this->bucket, $object, $options);

        return ['path' => $dirName, 'type' => 'dir'];
    }

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false file meta data
     */
    public function setVisibility($path, $visibility)
    {
        $object = $this->applyPathPrefix($path);
        $acl    = ($visibility === AdapterInterface::VISIBILITY_PUBLIC) ? OssClient::OSS_ACL_TYPE_PUBLIC_READ : OssClient::OSS_ACL_TYPE_PRIVATE;
        try {
            $this->client->putObjectAcl($this->bucket, $object, $acl);
        } catch (OssException $e) {
            $this->logError(__FUNCTION__, $e);
            return false;
        }

        return compact('visibility');
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
        $object = $this->applyPathPrefix($path);
        return $this->client->doesObjectExist($this->bucket, $object);
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
        $result             = $this->readObject($path);
        $result['contents'] = (string)$result['raw_contents'];
        unset($result['raw_contents']);
        return $result;
    }

    /**
     * Read a file as a stream.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function readStream($path)
    {
        $result           = $this->readObject($path);
        $result['stream'] = $result['raw_contents'];
        rewind($result['stream']);
        // Ensure the EntityBody object destruction doesn't close the stream
        $result['raw_contents']->detachStream();
        unset($result['raw_contents']);
        return $result;
    }

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     * @throws OssException
     */
    public function listContents($directory = '', $recursive = false)
    {
        $dirObjects = $this->listDirObjects($directory, true);

        $contents = $dirObjects["objects"];
        $result   = array_map([$this, 'normalizeResponse'], $contents);
        $result   = array_filter($result, function ($value) {
            return $value['path'] !== false;
        });
        return Util::emulateDirectories($result);
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
        $object     = $this->applyPathPrefix($path);
        $objectMeta = $this->client->getObjectMeta($this->bucket, $object);
        return $objectMeta;
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
        $object         = $this->getMetadata($path);
        $object['size'] = $object['content-length'];
        return $object;
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
            $object['mimetype'] = $object['content-type'];
        }
        return $object;
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
            $object['timestamp'] = strtotime($object['last-modified']);
        }
        return $object;
    }

    /**
     * Get the visibility of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getVisibility($path)
    {
        $object = $this->applyPathPrefix($path);
        try {
            $acl = $this->client->getObjectAcl($this->bucket, $object);
        } catch (OssException $e) {
            $this->logError(__FUNCTION__, $e);
            return false;
        }
        if ($acl == OssClient::OSS_ACL_TYPE_PUBLIC_READ) {
            $res['visibility'] = AdapterInterface::VISIBILITY_PUBLIC;
        } else {
            $res['visibility'] = AdapterInterface::VISIBILITY_PRIVATE;
        }
        return $res;
    }

    /**
     * @param $path
     *
     * @return string
     */
    public function getUrl($path)
    {
//        if (!$this->has($path)) throw new FileNotFoundException($path.' not found');
        return ($this->ssl ? 'https://' : 'http://') . ($this->isCName ? ($this->cdnDomain == '' ? $this->endPoint : $this->cdnDomain) : $this->bucket . '.' . $this->endPoint) . '/' . ltrim($path,
                '/');
    }

    /**
     * 生成临时链接
     *
     * @DriverFunction
     *
     * @param       $path
     * @param       $expiration
     * @param array $options
     *
     * @return string
     * @throws \OSS\Core\OssException
     * @throws FileNotFoundException
     */
    public function getTemporaryUrl($path, $expiration = 600, array $options = [])
    {

        if (!$this->has($path)) {
            throw new FileNotFoundException($path . ' not found');
        }

        if ($expiration instanceof Carbon) {
            $expiration = $expiration->diffInSeconds(Carbon::now());
        }

        $expiration = intval($expiration);
        return $this->client->signUrl($this->bucket, $path, $expiration, OssClient::OSS_HTTP_GET, $options);
    }

    /**
     * oss Header 参数
     *
     * @param array       $options
     * @param Config|null $config
     *
     * @return array
     */
    protected function getOptions(array $options = [], Config $config = null)
    {
        $options = array_merge($this->options, $options);

        if ($config) {
            $options = array_merge($options, $this->getOptionsFromConfig($config));
        }

        return [OssClient::OSS_HEADERS => $options];
    }

    /**
     * Retrieve options from a Config instance. done
     *
     * @param Config $config
     *
     * @return array
     */
    protected function getOptionsFromConfig(Config $config)
    {
        $options = [];

        foreach (static::$metaOptions as $option) {
            if (!$config->has($option)) {
                continue;
            }
            $options[static::$metaMap[$option]] = $config->get($option);
        }

        if ($visibility = $config->get('visibility')) {
            $options['x-oss-object-acl'] = $visibility === AdapterInterface::VISIBILITY_PUBLIC ? OssClient::OSS_ACL_TYPE_PUBLIC_READ : OssClient::OSS_ACL_TYPE_PRIVATE;
        }

        if ($mimetype = $config->get('mimetype')) {
            $options['Content-Type'] = $mimetype;
        }

        return $options;
    }

    /**
     * OSS 返回值解析
     *
     * @param array  $object
     * @param string $path
     *
     * @return array file metadata
     */
    protected function normalizeResponse(array $object, $path = null)
    {
        $result            = ['path' => $path ?: $this->removePathPrefix(isset($object['Key']) ? $object['Key'] : $object['Prefix'])];
        $result['dirname'] = Util::dirname($result['path']);
        if (isset($object['LastModified'])) {
            $result['timestamp'] = strtotime($object['LastModified']);
        }
        if (substr($result['path'], -1) === '/') {
            $result['type'] = 'dir';
            $result['path'] = rtrim($result['path'], '/');
            return $result;
        }
        $result = array_merge($result, Util::map($object, static::$resultMap), ['type' => 'file']);
        return $result;
    }

    /**
     * The the ACL visibility.
     *
     * @param string $path
     *
     * @return string
     */
    protected function getObjectACL($path)
    {
        $metadata = $this->getVisibility($path);
        return $metadata['visibility'] === AdapterInterface::VISIBILITY_PUBLIC ? OssClient::OSS_ACL_TYPE_PUBLIC_READ : OssClient::OSS_ACL_TYPE_PRIVATE;
    }

    /**
     * 列举文件夹内文件列表，可递归获取子文件夹
     *
     * @param string $dirname   目录
     * @param bool   $recursive 是否递归
     *
     * @return mixed
     * @throws OssException
     */
    protected function listDirObjects($dirname = '', $recursive = false)
    {
        $delimiter  = '/';
        $nextMarker = '';
        $maxKeys    = 1000;
        //存储结果
        $result = [];
        while (true) {
            $options = [
                'delimiter' => $delimiter,
                'prefix'    => $dirname,
                'max-keys'  => $maxKeys,
                'marker'    => $nextMarker,
            ];
            try {
                $listObjectInfo = $this->client->listObjects($this->bucket, $options);
            } catch (OssException $e) {
                $this->logError(__FUNCTION__, $e);
                throw $e;
            }
            $nextMarker = $listObjectInfo->getNextMarker(); // 得到nextMarker，从上一次listObjects读到的最后一个文件的下一个文件开始继续获取文件列表
            $objectList = $listObjectInfo->getObjectList(); // 文件列表
            $prefixList = $listObjectInfo->getPrefixList(); // 目录列表
            if (!empty($objectList)) {
                foreach ($objectList as $objectInfo) {
                    $object['Prefix']       = $dirname;
                    $object['Key']          = $objectInfo->getKey();
                    $object['LastModified'] = $objectInfo->getLastModified();
                    $object['eTag']         = $objectInfo->getETag();
                    $object['Type']         = $objectInfo->getType();
                    $object['Size']         = $objectInfo->getSize();
                    $object['StorageClass'] = $objectInfo->getStorageClass();
                    $result['objects'][]    = $object;
                }
            } else {
                $result["objects"] = [];
            }
            if (!empty($prefixList)) {
                foreach ($prefixList as $prefixInfo) {
                    $result['prefix'][] = $prefixInfo->getPrefix();
                }
            } else {
                $result['prefix'] = [];
            }
            //递归查询子目录所有文件
            if ($recursive) {
                foreach ($result['prefix'] as $prefix_dirname) {
                    $next              = $this->listDirObjects($prefix_dirname, $recursive);
                    $result["objects"] = array_merge($result['objects'], $next["objects"]);
                }
            }
            //没有更多结果了
            if ($nextMarker === '') {
                break;
            }
        }
        return $result;
    }

    /**
     * Read an object from the OssClient.
     *
     * @param string $path
     *
     * @return array
     */
    protected function readObject($path)
    {
        $object         = $this->applyPathPrefix($path);
        $result['Body'] = $this->client->getObject($this->bucket, $object);
        $result         = array_merge($result, ['type' => 'file']);
        return $this->normalizeResponse($result, $path);
    }

    /**
     * @param            $fun string function name : __FUNCTION__
     * @param \Exception $e
     */
    protected function logError($fun, \Exception $e)
    {
        Log::error($fun . ": FAILED => " . $e->getMessage());
    }
}
