<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 9/5/18
 * Time: 11:06 AM
 */

namespace Model\Service\Facade;



use Symfony\Component\Cache\Simple\FilesystemCache;

class CachingHandler
{
    private $cache;

    public function __construct()
    {
        $this->cache = new FilesystemCache();
    }


    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function checkIfResponseIsCached(){
        // set data variable
        $data = [];

        // check if response is cached
        if ($this->cache->has('products')) {
            $data = $this->cache->get('products');
        }

        // return data
        return $data;
    }


    /**
     * @param $data
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function cacheNewResponse($data){
        $this->cache->set('products', $data);
    }


    public function deleteCache(){
        $this->cache->clear();
    }
}