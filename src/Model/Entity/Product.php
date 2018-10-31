<?php

namespace Model\Entity;

use Model\Contract\HasId;

class Product implements HasId
{
    private $id;
    private $stripeProductId;
    private $stripeSkuId;
    private $skuWidth;
    private $skuHeight;
    private $skuWeight;
    private $skuLength;
    private $skuQuantity;
    private $name;
    private $description;
    private $price;
    private $discount;
    private $date;
    private $images;
    private $tags;
    private $supplements;
    private $outOfStock;


    /**
     * @return mixed
     */
    public function getSkuQuantity()
    {
        return $this->skuQuantity;
    }

    /**
     * @param mixed $skuQuantity
     */
    public function setSkuQuantity($skuQuantity)
    {
        $this->skuQuantity = $skuQuantity;
    }

    /**
     * @return mixed
     */
    public function getSkuWidth()
    {
        return $this->skuWidth;
    }

    /**
     * @param mixed $skuWidth
     */
    public function setSkuWidth($skuWidth): void
    {
        $this->skuWidth = $skuWidth;
    }

    /**
     * @return mixed
     */
    public function getSkuHeight()
    {
        return $this->skuHeight;
    }

    /**
     * @param mixed $skuHeight
     */
    public function setSkuHeight($skuHeight): void
    {
        $this->skuHeight = $skuHeight;
    }

    /**
     * @return mixed
     */
    public function getSkuWeight()
    {
        return $this->skuWeight;
    }

    /**
     * @param mixed $skuWeight
     */
    public function setSkuWeight($skuWeight): void
    {
        $this->skuWeight = $skuWeight;
    }

    /**
     * @return mixed
     */
    public function getSkuLength()
    {
        return $this->skuLength;
    }

    /**
     * @param mixed $skuLength
     */
    public function setSkuLength($skuLength): void
    {
        $this->skuLength = $skuLength;
    }

    /**
     * @return mixed
     */
    public function getStripeSkuId()
    {
        return $this->stripeSkuId;
    }

    /**
     * @param mixed $stripeSkuId
     */
    public function setStripeSkuId($stripeSkuId): void
    {
        $this->stripeSkuId = $stripeSkuId;
    }

    /**
     * @return mixed
     */
    public function getStripeProductId()
    {
        return $this->stripeProductId;
    }

    /**
     * @param mixed $stripeProductId
     */
    public function setStripeProductId($stripeProductId): void
    {
        $this->stripeProductId = $stripeProductId;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param mixed $discount
     */
    public function setDiscount($discount): void
    {
        $this->discount = $discount;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param mixed $images
     */
    public function setImages($images): void
    {
        $this->images = $images;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getSupplements()
    {
        return $this->supplements;
    }

    /**
     * @param mixed $supplements
     */
    public function setSupplements($supplements): void
    {
        $this->supplements = $supplements;
    }

    /**
     * @return mixed
     */
    public function getOutOfStock()
    {
        return $this->outOfStock;
    }

    /**
     * @param mixed $outOfStock
     */
    public function setOutOfStock($outOfStock): void
    {
        $this->outOfStock = $outOfStock;
    }

}