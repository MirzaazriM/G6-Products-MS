<?php

namespace Model\Service;

use Model\Entity\Product;
use Model\Entity\ResponseBootstrap;
use Model\Entity\Shared;
use Model\Mapper\ProductsMapper;
use Model\Service\Facade\CachingHandler;
use Model\Service\Facade\CollectionToArrayCovertor;
use Model\Service\Facade\MicroservicesCommunicator;
use Monolog\Logger;
use Symfony\Component\Config\Definition\Exception\Exception;

class ProductsService
{
    private $productsMapper;
    private $configuration;
    private $monolog;
    private $cacheHandler;

    public function __construct(ProductsMapper $productsMapper)
    {
        $this->productsMapper = $productsMapper;
        $this->configuration = $productsMapper->getConfiguration();
        $this->monolog = new Logger('monolog');
        $this->cacheHandler = new CachingHandler();

        \Stripe\Stripe::setApiKey("sk_test_8BxTOeXo8UnKVVqmtt0IG6sf");
    }


    /**
     * Get product by id
     *
     * @param int $id
     * @return ResponseBootstrap
     */
    public function getProduct(int $id):ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // create entity and set its values
            $entity = new Product();
            $entity->setId($id);

            // call mapper for data
            $data = $this->productsMapper->getProduct($entity);

            // convert collection data to an array in a facade object
            //$facade = new CollectionToArrayCovertor($data);
            //$convertedData = $facade->convertData();

            $convertedData = [];

            $convertedData['id'] = $data[0]->getId();
            $convertedData['name'] = $data[0]->getName();
            $convertedData['stripe_product_id'] = $data[0]->getStripeProductId();
            $convertedData['stripe_sku_id'] = $data[0]->getStripeSkuId();
            $convertedData['price'] = $data[0]->getPrice();
            $convertedData['discount'] = $data[0]->getDiscount();
            $convertedData['images'] = $data[0]->getImages();
            $convertedData['description'] = $data[0]->getSupplements();
            $convertedData['dimensions']['sku_weight'] = $data[0]->getSkuWeight();
            $convertedData['dimensions']['sku_height'] = $data[0]->getSkuHeight();
            $convertedData['dimensions']['sku_length'] = $data[0]->getSkuLength();
            $convertedData['dimensions']['sku_width'] = $data[0]->getSkuWidth();
            $convertedData['tags'] = explode(',', $data[0]->getTags());

            // get current state of stock from stripe
            $quantity = \Stripe\SKU::retrieve($convertedData['stripe_sku_id']);
            $quantity = $quantity['inventory']['quantity'];
            $convertedData['dimensions']['sku_quantity'] = $quantity;

            // call Tags MS for supplements data
            $client = new \GuzzleHttp\Client();
            $result = $client->request('GET', $this->configuration['tags_url'] . '/tags/ids?ids=' . implode(',', $convertedData['tags']), []);
            $convertedData['tags'] = json_decode($result->getBody()->getContents());

            // check data and set appropriate response
            if(!empty($convertedData)){
                $response->setStatus(200);
                $response->setMessage('Success');
                $response->setData($convertedData);
            }else {
                $response->setStatus(204);
                $response->setMessage('No content');
            }

            // return response
            return $response;

        }catch (\Exception $e){
            // write monolog entry
            $this->monolog->addError('Get product service: ' . $e);

            // set response on failure
            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }

    }


    /**
     * Get active products
     *
     * @return ResponseBootstrap
     */
    public function getActiveProducts():ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // call mapper for data
            $data = $this->productsMapper->getNumberOfActiveProducts();

            // check data and set appropriate response
            if($data !== null){
                $response->setStatus(200);
                $response->setMessage('Success');
                $response->setData([
                    'number' => $data
                ]);
            }else {
                $response->setStatus(204);
                $response->setMessage('No content');
            }

            // return response
            return $response;

        }catch (\Exception $e){
            // write monolog entry
            $this->monolog->addError('Get number of active products service: ' . $e);

            // set response on failure
            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }
    }


    /**
     * Get last 2 added products
     *
     * @return ResponseBootstrap
     */
    public function getLastProducts():ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // call mapper for data
            $data = $this->productsMapper->getLastAddedProducts();

            // convert collection data to an array in a facade object
            $facade = new CollectionToArrayCovertor($data);
            $convertedData = $facade->convertData();

            // get microservices data using MicroservicesCommunicator object
            $communicator = new MicroservicesCommunicator($convertedData, $this->configuration);
            $convertedData = $communicator->integrateMicroservicesData();

            // check data and set appropriate response
            if(!empty($convertedData)){
                $response->setStatus(200);
                $response->setMessage('Success');
                $response->setData($convertedData);
            }else {
                $response->setStatus(204);
                $response->setMessage('No content');
            }
            // return response
            return $response;

        }catch (\Exception $e){
            // write monolog entry
            $this->monolog->addError('Get last two added products service: ' . $e);

            // set response on failure
            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }
    }


    /**
     * @return ResponseBootstrap
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getProducts():ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // check if response is already cached
           // $convertedData = $this->cacheHandler->checkIfResponseIsCached();

            $convertedData = [];

            // set is data is empty
            if(empty($convertedData)){
                // call mapper for data
                $data = $this->productsMapper->getProducts();

                // convert collection data to an array in a facade object
                $facade = new CollectionToArrayCovertor($data);
                $convertedData = $facade->convertData();

                // get microservices data using MicroservicesCommunicator object
                $communicator = new MicroservicesCommunicator($convertedData, $this->configuration);
                $convertedData = $communicator->integrateMicroservicesData();

                // cache response
                // $this->cacheHandler->cacheNewResponse($convertedData);
            }

            // check data and set appropriate response
            if(!empty($convertedData)){
                $response->setStatus(200);
                $response->setMessage('Success');
                $response->setData($convertedData);
            }else {
                $response->setStatus(204);
                $response->setMessage('No content');
            }

            // return response
            return $response;

        }catch (\Exception $e){
            // write monolog entry
            $this->monolog->addError('Get products service: ' . $e);

            // set response on failure
            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }

    }


    /**
     * Get most sold products
     *
     * @return ResponseBootstrap
     */
    public function getMostsoldProducts():ResponseBootstrap {

        try {

            // create response object
            $response = new ResponseBootstrap();

            $data = $this->productsMapper->getMostsoldProducts();

            // convert collection data to an array in a facade object
//            $facade = new CollectionToArrayCovertor($data);
//            $convertedData = $facade->convertData();
//
//            // get microservices data using MicroservicesCommunicator object
//            $communicator = new MicroservicesCommunicator($convertedData, $this->configuration);
//            $convertedData = $communicator->integrateMicroservicesData();

            // check data and set appropriate response
            if(!empty($data)){
                $response->setStatus(200);
                $response->setMessage('Success');
                $response->setData($data);
            }else {
                $response->setStatus(204);
                $response->setMessage('No content');
            }

            // return response
            return $response;

        }catch (Exception $e){
            // write monolog entry
            $this->monolog->addError('Get most sold products service: ' . $e);

            // set response on failure
            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }

    }




    /**
     * Edit product
     *
     * @param int $id
     * @param string $name
     * @param string $description
     * @param string $price
     * @param string $discount
     * @param array $images
     * @param array $tags
     * @param array $supplements
     * @param string $outOfStock
     * @return ResponseBootstrap
     */
    public function editProduct(int $id, string $skuId, string $productId, string $name, string $description, string $price, string $discount, array $images, array $tags, array $supplements, string $outOfStock, array $dimensions):ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            \Stripe\Stripe::setApiKey("sk_test_8BxTOeXo8UnKVVqmtt0IG6sf");

            // update product name
            $product = \Stripe\Product::retrieve($productId);
            $product->name = $name;
            $product->save();

            $sku = \Stripe\SKU::retrieve($skuId);
            $sku->package_dimensions = array(
                    "weight" => $dimensions[0],
                    "height" => $dimensions[1],
                    "length" => $dimensions[2],
                    "width" => $dimensions[3]
            );
            $sku->inventory = array(
                "quantity" => $dimensions[4]
            );
            $sku->price = $discount * 100;


            // $sku->price = $discount;
            $sku->save();

            // create entity and set its values
            $entity = new Product();
            $entity->setId($id);
            $entity->setName($name);
            $entity->setDescription($description);
            $entity->setPrice($price);
            $entity->setDiscount($discount);
            $entity->setImages($images);
            $entity->setTags($tags);
            $entity->setSupplements($supplements);
            $entity->setSkuWeight($dimensions[0]);
            $entity->setSkuHeight($dimensions[1]);
            $entity->setSkuLength($dimensions[2]);
            $entity->setSkuWidth($dimensions[3]);
            $entity->setOutOfStock($outOfStock);

            // create shared entity
            $shared = new Shared();

            // get response
            $result = $this->productsMapper->editProduct($entity, $shared);

            // check result and set response
            if ($result->getState() == 200){
                // delete caching
                $this->cacheHandler->deleteCache();

                // set response
                $response->setStatus(200);
                $response->setMessage('Success');
            } else {
                $response->setStatus(304);
                $response->setMessage('Not modified');
            }

            // return response
            return $response;

        }catch (\Exception $e){
            // write monolog entry
            $this->monolog->addError('Edit product service: ' . $e);

            // set response on failure
            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }

    }


    /**
     *  Delete product by id
     *
     * @param int $id
     * @param string $productId
     * @return ResponseBootstrap
     */
    public function deleteProduct(int $id, string $productId, string $skuId):ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            \Stripe\Stripe::setApiKey("sk_test_8BxTOeXo8UnKVVqmtt0IG6sf");

            // create entity and set its values
            $entity = new Product();
            $entity->setId($id);

            // create shared entity
            $shared = new Shared();

            // get response
            $result = $this->productsMapper->deleteProduct($entity, $shared);

            $sku = \Stripe\SKU::retrieve($skuId);
            $sku->delete();

            $product = \Stripe\Product::retrieve($productId);
            $product->delete();

            // check data and set response
            if ($result->getState() == 200){
                // delete caching
                $this->cacheHandler->deleteCache();

                $response->setStatus(200);
                $response->setMessage('Success');
            } else {
                $response->setStatus(304);
                $response->setMessage('Not modified');
            }

            // return response
            return $response;

        }catch (\Exception $e){
            // write monolog entry
            $this->monolog->addError('Delete product service: ' . $e);

            // set response on failure
            $response->setStatus(400);
            $response->setMessage('Unable to delete');
            return $response;
        }
    }


    /**
     * Add a product
     *
     * @param string $name
     * @param string $description
     * @param string $price
     * @param string $discount
     * @param array $images
     * @param array $tags
     * @param array $supplements
     * @return ResponseBootstrap
     */
    public function addProduct(string $name, string $description, string $price, string $discount, array $images, array $tags, array $supplements, array $dimensions):ResponseBootstrap {


        try {
            // create response object
            $response = new ResponseBootstrap();

            \Stripe\Stripe::setApiKey("sk_test_8BxTOeXo8UnKVVqmtt0IG6sf");

            $pr = \Stripe\Product::create(array(
                "name" => $name,
                "type" => "good",
            ));

            $productId = $pr['id'];

            $sku = \Stripe\SKU::create(array(
                "product" => $productId,
                "price" => ($discount * 100),
                "currency" => "usd",
                "inventory" => array(
                    "type" => "finite",
                    "quantity" => $dimensions[4]
                ),
                "package_dimensions" => array(
                    "weight" => $dimensions[0],
                    "height" => $dimensions[1],
                    "length" => $dimensions[2],
                    "width" => $dimensions[3]
                )
            ));

            $skuId = $sku['id'];

            //die(print_r($sku));

            // create entity and set its values
            $entity = new Product();
            $entity->setName($name);
            $entity->setDescription($description);
            $entity->setPrice($price);
            $entity->setDiscount($discount);
            $entity->setImages($images);
            $entity->setTags($tags);
            $entity->setSupplements($supplements);
            $entity->setStripeSkuId($skuId);
            $entity->setSkuWeight($dimensions[0]);
            $entity->setSkuHeight($dimensions[1]);
            $entity->setSkuLength($dimensions[2]);
            $entity->setSkuWidth($dimensions[3]);
            $entity->setStripeProductId($productId);

            // create shared entity
            $shared = new Shared();

            // get response
            $result = $this->productsMapper->addProduct($entity, $shared);

            // check result and set response
            if ($result->getState() == 200){
                // delete caching
                $this->cacheHandler->deleteCache();

                // set response
                $response->setStatus(200);
                $response->setMessage('Success');
            } else {
                $response->setStatus(304);
                $response->setMessage('Not modified');
            }

            // return response
            return $response;

        }catch (\Exception $e){
            // write monolog entry
            $this->monolog->addError('Add product service: ' . $e);

            // set response on failure
            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }
    }

}
