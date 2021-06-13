<?php
/**
 * Part of unidev project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Image\Storage;

use Imgur\Client;
use Windwalker\Legacy\Filesystem\File;
use Windwalker\Legacy\Structure\Structure;

/**
 * The ImgurImageStorage class.
 *
 * @since  1.0
 *
 * @link   https://github.com/Adyg/php-imgur-api-client
 */
class ImgurImageStorage implements ImageStorageInterface
{
    /**
     * Property imgur.
     *
     * @var  Client
     */
    protected $imgur;

    /**
     * Property config.
     *
     * @var  Structure
     */
    protected $config;

    /**
     * ImgurImageStorage constructor.
     *
     * @param Client    $imgur
     * @param Structure $config
     */
    public function __construct(Client $imgur, Structure $config)
    {
        $this->imgur  = $imgur;
        $this->config = $config;
    }

    /**
     * uploadRaw
     *
     * @param   string $image
     * @param   string $path
     * @param   string $type
     *
     * @return string
     */
    public function uploadRaw($image, $path, $type = null)
    {
        $data = [
            'image' => base64_encode($image),
            'type' => 'base64',
        ];

        if ($this->config['album']) {
            $data['alumb'] = $this->config['album'];
        }

        $basic = $this->imgur->api('image')->upload($data)->getData();

        return $basic['link'];
    }

    /**
     * upload
     *
     * @param   string $file
     * @param   string $path
     *
     * @return  string
     */
    public function upload($file, $path)
    {
        $data = [
            'image' => $file,
            'type' => 'file',
        ];

        if ($this->config['album']) {
            $data['alumb'] = $this->config['album'];
        }

        $basic = $this->imgur->api('image')->upload($data)->getData();

        return $basic['link'];
    }

    /**
     * delete
     *
     * @param   string $path
     *
     * @return  boolean
     */
    public function delete($path)
    {
        $path = File::stripExtension($path);

        $basic = $this->imgur->api('image')->deleteImage($path)->getData();

        return $basic['success'];
    }

    /**
     * getHost
     *
     * @return  string
     */
    public function getHost()
    {
        return 'https://i.imgur.com';
    }

    /**
     * getRemoteUrl
     *
     * @param   string $path
     *
     * @return  string
     */
    public function getRemoteUrl($path)
    {
        if (!File::getExtension($path)) {
            $img = $this->imgur->image($path);

            return $img->link;
        }

        return static::getHost() . '/' . $path;
    }

    /**
     * Method to get property Imgur
     *
     * @return  Client
     */
    public function getImgur()
    {
        return $this->imgur;
    }

    /**
     * Method to set property imgur
     *
     * @param   Client $imgur
     *
     * @return  static  Return self to support chaining.
     */
    public function setImgur($imgur)
    {
        $this->imgur = $imgur;

        return $this;
    }
}
