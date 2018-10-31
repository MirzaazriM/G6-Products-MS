<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 8/31/18
 * Time: 11:09 AM
 */

namespace Model\Service\Facade;


use Model\Entity\ProductsCollection;

class CollectionToArrayCovertor
{

    private $collection;

    public function __construct(ProductsCollection $collection){
        $this->collection = $collection;
    }


    /**
     * Convert data to array
     *
     * @return array
     */
    public function convertData():array  {
        // create new array
        $data = [];

        // loop through data
        for($i = 0; $i < count($this->collection); $i++){
            $data[$i]['id'] = $this->collection[$i]->getId();
            $data[$i]['stripe_product_id'] = $this->collection[$i]->getStripeProductId();
            $data[$i]['stripe_sku_id'] = $this->collection[$i]->getStripeSkuId();
            $data[$i]['name'] = $this->collection[$i]->getName();
            //$data[$i]['description'] = $this->collection[$i]->getDescription();
            $data[$i]['price'] = $this->collection[$i]->getPrice();
            //$data[$i]['date_added'] = $this->collection[$i]->getDate();
            //$data[$i]['out_of_stock'] = $this->collection[$i]->getOutOfStock();
            $data[$i]['price_with_discount'] = $this->collection[$i]->getDiscount();
            $data[$i]['images'] = $this->collection[$i]->getImages();
            $data[$i]['description'] = $this->collection[$i]->getSupplements();
            $data[$i]['tags'] = explode(',', $this->collection[$i]->getTags());

        }

        // return converted data
        return $data;
    }




}