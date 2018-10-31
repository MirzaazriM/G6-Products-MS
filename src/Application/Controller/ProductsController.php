<?php

namespace Application\Controller;

use Model\Entity\ResponseBootstrap;
use Model\Service\Facade\CachingHandler;
use Model\Service\ProductsService;
use Symfony\Component\HttpFoundation\Request;

class ProductsController
{
    private $productsService;

    public function __construct(ProductsService $productsService)
    {
        $this->productsService = $productsService;
    }


    /**
     * Get single product
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function get(Request $request):ResponseBootstrap
    {
        // get id
        $id = $request->get('id');

        // create response object
        $response = new ResponseBootstrap();

        // check if parameters are present
        if (isset($id)){
            return $this->productsService->getProduct($id);
        } else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }

        return $response;
    }


    /**
     * @param Request $request
     * @return ResponseBootstrap
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getAll(Request $request):ResponseBootstrap
    {
        // call service for data
        return $this->productsService->getProducts();
    }


    /**
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function getMostsold(Request $request):ResponseBootstrap
    {
        // call service for data
        return $this->productsService->getMostsoldProducts();
    }


    /**
     * Get active products
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function getActive(Request $request):ResponseBootstrap
    {
        // call service for data
        return $this->productsService->getActiveProducts();
    }


    /**
     * Get last 2 added products
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function getLast(Request $request):ResponseBootstrap
    {
        // call service for data
        return $this->productsService->getLastProducts();
    }


    /**
     * Edit product
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function put(Request $request):ResponseBootstrap
    {
        // get data
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];
        $skuId = $data['sku_id'];
        $productId = $data['product_id'];
        $name = $data['name'];
        $price = $data['price'];
        $discount = $data['discount'];
        $description = $data['description'];
        $images = $data['images'];
        $tags = $data['tags'];
        $supplements = $data['supplements'];
        $outOfStock = $data['out_of_stock'];
        $dimensions = $data['dimensions'];

        // create response object
        $response = new ResponseBootstrap();

        // check if data is present
        if (isset($id) && isset($skuId) && isset($productId) && isset($name) && isset($price) && isset($description) && isset($images)  && isset($discount)  && isset($tags) && isset($supplements) && isset($outOfStock) && isset($dimensions)){
            return $this->productsService->editProduct($id, $skuId, $productId, $name, $description, $price, $discount, $images, $tags, $supplements, $outOfStock, $dimensions);
        } else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }

        // return response
        return $response;
    }


    /**
     * Add product
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function post(Request $request):ResponseBootstrap {
        // get data
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $price = $data['price'];
        $discount = $data['discount'];
        $description = $data['description'];
        $images = $data['images'];
        $tags = $data['tags'];
        $supplements = $data['supplements'];
        $dimensions = $data['dimensions'];

        // create response object
        $response = new ResponseBootstrap();

        // check if data is present
        if (isset($name) && isset($price) && isset($description) && isset($images)  && isset($discount)  && isset($tags)  && isset($supplements) && isset($dimensions)){
            return $this->productsService->addProduct($name, $description, $price, $discount, $images, $tags, $supplements, $dimensions);
        } else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }

        // return response
        return $response;
    }


    /**
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function delete(Request $request):ResponseBootstrap {
        // get ids
        $id = $request->get('id');
        $productId = $request->get('product_id');
        $skuId = $request->get('sku_id');

        // create response object
        $response = new ResponseBootstrap();

        // check if parameters are present
        if (isset($id) && isset($productId)){
            return $this->productsService->deleteProduct($id, $productId, $skuId);
        } else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }

        return $response;
    }


    /**
     * Delete cache - no service
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function deleteCache(Request $request):ResponseBootstrap {
        // create caching handler object
        $cacheHandler = new CachingHandler();
        // call delete cache function
        $cacheHandler->deleteCache();

        // create response object
        $response = new ResponseBootstrap();

        // set response
        $response->setStatus(200);
        $response->setMessage('Success');

        // return response
        return $response;
    }

}