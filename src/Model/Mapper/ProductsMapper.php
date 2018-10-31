<?php

namespace Model\Mapper;

use Model\Entity\Product;
use Model\Entity\ProductsCollection;
use Model\Entity\Shared;
use PDO;
use PDOException;
use Component\DataMapper;
use Symfony\Component\Config\Definition\Exception\Exception;

class ProductsMapper extends DataMapper
{

    public function getConfiguration()
    {
        return $this->configuration;
    }


    /**
     * Get product by id
     *
     * @param Product $product
     * @return ProductsCollection
     */
    public function getProduct(Product $product):ProductsCollection
    {
        // create response object
        $response = new ProductsCollection();

        try {
            // set database instructions
            $sql = "SELECT 
                        p.id,
                        p.stripe_sku_id,
                        p.stripe_product_id,
                        p.name,
                        p.description,
                        p.price,
                        p.discount,
                        p.date,
                        p.out_of_stock,
                        p.sku_weight,
                        p.sku_height,
                        p.sku_length,
                        p.sku_width,
                        GROUP_CONCAT(DISTINCT pi.image_name) AS images,
                        GROUP_CONCAT(DISTINCT pt.tag_id) AS tags,
                        GROUP_CONCAT(DISTINCT ps.supplement_id) AS supplements
                    FROM products AS p 
                    LEFT JOIN product_images AS pi ON p.id = pi.product_parent
                    LEFT JOIN product_tags AS pt ON p.id = pt.product_parent
                    LEFT JOIN product_supplements AS ps ON  p.id = ps.product_parent
                    WHERE p.id = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $product->getId()
            ]);

            // set data to variable if any
            $data = $statement->fetch(PDO::FETCH_ASSOC);

            // if data is not empty set values to the entity
            if(isset($data['id'])){
                // create product entity and set its values
                $productContainer = new Product();
                $productContainer->setId($data['id']);
                $productContainer->setStripeSkuId($data['stripe_sku_id']);
                $productContainer->setStripeProductId($data['stripe_product_id']);
                $productContainer->setName($data['name']);
                $productContainer->setDescription($data['description']);
                $productContainer->setDate($data['date']);
                $productContainer->setOutOfStock($data['out_of_stock']);

                // add prefixes to image names to create link
                $images = explode(',', $data['images']);
                for($i = 0; $i < count($images); $i++){
                    $images[$i] = $this->configuration['asset_link'] . $images[$i];
                }

                $productContainer->setImages($images);
                $productContainer->setTags($data['tags']);
                $productContainer->setSupplements($data['supplements']);
                $productContainer->setPrice($data['price']);
                $productContainer->setDiscount($data['discount']);
                $productContainer->setSkuWeight($data['sku_weight']);
                $productContainer->setSkuHeight($data['sku_height']);
                $productContainer->setSkuLength($data['sku_length']);
                $productContainer->setSkuWidth($data['sku_width']);

                // add entity to the collection
                $response->addEntity($productContainer);
            }

        }catch (PDOException $e){
            // get error code
            $code = $e->errorInfo[1];

            // set appropriate monolog entry dependeng on error code value
            if((int)$code >= 1000 && (int)$code <= 1749){
                $this->monolog->addError('Get product mapper: ' . $e);
            }else {
                $this->monolog->addWarning('Get product mapper: ' . $e);
            }
        }

        // return response
        return $response;
    }


    /**
     * Get active products
     *
     * @return ProductsCollection
     */
    public function getNumberOfActiveProducts()
    {
        try {
            // set database instructions
            $sql = "SELECT COUNT(*) as number FROM products WHERE out_of_stock = 'false'";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            // set number of active products as null
            $data = null;

            // set number of active products
            if($statement->rowCount() > 0){
                $data = $statement->fetch(PDO::FETCH_ASSOC)['number'];
            }

        }catch (PDOException $e){
            // get error code
            $code = $e->errorInfo[1];

            // set appropriate monolog entry dependeng on error code value
            if((int)$code >= 1000 && (int)$code <= 1749){
                $this->monolog->addError('Get number of active products mapper: ' . $e);
            }else {
                $this->monolog->addWarning('Get number of active products mapper: ' . $e);
            }
        }

        // return response
        return $data;
    }


    /**
     * Get all products
     *
     * @return ProductsCollection
     */
    public function getProducts():ProductsCollection
    {
        // create response object
        $response = new ProductsCollection();

        try {
            // set database instructions
            $sql = "SELECT 
                        p.id,
                        p.stripe_product_id,
                        p.stripe_sku_id,
                        p.name,
                        p.description,
                        p.price,
                        p.discount,
                        p.date,
                        p.out_of_stock,
                        GROUP_CONCAT(DISTINCT pi.image_name) AS images,
                        GROUP_CONCAT(DISTINCT pt.tag_id) AS tags,
                        GROUP_CONCAT(DISTINCT ps.supplement_id) AS supplements
                    FROM products AS p 
                    LEFT JOIN product_images AS pi ON p.id = pi.product_parent
                    LEFT JOIN product_tags AS pt ON p.id = pt.product_parent
                    LEFT JOIN product_supplements AS ps ON  p.id = ps.product_parent
                    GROUP BY p.id";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            while($row = $statement->fetch(PDO::FETCH_ASSOC)){
                // create product entity and set its values
                $productContainer = new Product();
                $productContainer->setId($row['id']);
                $productContainer->setName($row['name']);
                $productContainer->setDescription($row['description']);
                $productContainer->setDate($row['date']);
                $productContainer->setStripeProductId($row['stripe_product_id']);
                $productContainer->setStripeSkuId($row['stripe_sku_id']);
                $productContainer->setOutOfStock($row['out_of_stock']);

                // add prefixes to image names to create link
                $images = explode(',', $row['images']);
                for($i = 0; $i < count($images); $i++){
                    $images[$i] = $this->configuration['asset_link'] . $images[$i];
                }

                $productContainer->setImages($images);
                $productContainer->setTags($row['tags']);
                $productContainer->setSupplements($row['supplements']);
                $productContainer->setPrice($row['price']);
                $productContainer->setDiscount($row['discount']);

                // add entity to the collection
                $response->addEntity($productContainer);
            }

        }catch (PDOException $e){
            // get error code
            $code = $e->errorInfo[1];

            // set appropriate monolog entry dependeng on error code value
            if((int)$code >= 1000 && (int)$code <= 1749){
                $this->monolog->addError('Get products mapper: ' . $e);
            }else {
                $this->monolog->addWarning('Get products mapper: ' . $e);
            }
        }

        // return response
        return $response;
    }



    public function getMostsoldProducts(){

        try {

            // set database instructions
            $sql = "SELECT name, COUNT(name) AS total_sales FROM order_items GROUP BY name ORDER BY total_sales DESC LIMIT 0,3";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $data = $statement->fetchAll(PDO::FETCH_ASSOC);


            $sqlProduct = "SELECT id FROM products WHERE name = ?";
            $statementProduct = $this->connection->prepare($sqlProduct);

            $wholeData = [];
            $counter = 0;

            foreach ($data as $product){
                $statementProduct->execute([
                    $product['name']
                ]);

                $id = $statementProduct->fetch();
                $wholeData[$counter]['id'] = $id['id'];
                $wholeData[$counter]['name'] = $product['name'];
                $wholeData[$counter]['total_sales'] = $product['total_sales'];

                $counter++;
            }

            //die(print_r($wholeData));

        }catch (PDOException $e){
            // get error code
            $code = $e->errorInfo[1];

            // set appropriate monolog entry dependeng on error code value
            if((int)$code >= 1000 && (int)$code <= 1749){
                $this->monolog->addError('Get most sold products mapper: ' . $e);
            }else {
                $this->monolog->addWarning('Get most sold products mapper: ' . $e);
            }

            return [];
        }

        return $wholeData;
    }


    /**
     * Get all products
     *
     * @return ProductsCollection
     */
    public function getLastAddedProducts():ProductsCollection
    {
        // create response object
        $response = new ProductsCollection();

        try {
            // set database instructions
            $sql = "SELECT 
                        p.id,
                        p.name,
                        p.description,
                        p.price,
                        p.discount,
                        p.date,
                        p.out_of_stock,
                        GROUP_CONCAT(DISTINCT pi.image_name) AS images,
                        GROUP_CONCAT(DISTINCT pt.tag_id) AS tags,
                        GROUP_CONCAT(DISTINCT ps.supplement_id) AS supplements
                    FROM products AS p 
                    LEFT JOIN product_images AS pi ON p.id = pi.product_parent
                    LEFT JOIN product_tags AS pt ON p.id = pt.product_parent
                    LEFT JOIN product_supplements AS ps ON  p.id = ps.product_parent
                    GROUP BY p.id
                    ORDER BY p.date DESC
                    LIMIT 0,2";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            while($row = $statement->fetch(PDO::FETCH_ASSOC)){
                // create product entity and set its values
                $productContainer = new Product();
                $productContainer->setId($row['id']);
                $productContainer->setName($row['name']);
                $productContainer->setDescription($row['description']);
                $productContainer->setDate($row['date']);
                $productContainer->setOutOfStock($row['out_of_stock']);

                // add prefixes to image names to create link
                $images = explode(',', $row['images']);
                for($i = 0; $i < count($images); $i++){
                    $images[$i] = $this->configuration['asset_link'] . $images[$i];
                }

                $productContainer->setImages($images);
                $productContainer->setTags($row['tags']);
                $productContainer->setSupplements($row['supplements']);
                $productContainer->setPrice($row['price']);
                $productContainer->setDiscount($row['discount']);

                // add entity to the collection
                $response->addEntity($productContainer);
            }

        }catch (PDOException $e){
            // get error code
            $code = $e->errorInfo[1];

            // set appropriate monolog entry dependeng on error code value
            if((int)$code >= 1000 && (int)$code <= 1749){
                $this->monolog->addError('Get last two added products mapper: ' . $e);
            }else {
                $this->monolog->addWarning('Get last two added products mapper: ' . $e);
            }
        }

        // return response
        return $response;
    }


    /**
     * Edit product
     *
     * @param Product $product
     * @return Shared
     */
    public function editProduct(Product $product, Shared $shared):Shared
    {

        try{
            // begin transaction
            $this->connection->beginTransaction();

            // set database instructions for updating products table
            $sql = "UPDATE 
                      products 
                      SET 
                      name = ?,
                      description = ?,
                      price = ?,
                      discount = ?,
                      out_of_stock = ?,
                      sku_weight = ?,
                      sku_height = ?,
                      sku_length = ?,
                      sku_width = ?
                    WHERE id = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $product->getName(),
                $product->getDescription(),
                $product->getPrice(),
                $product->getDiscount(),
                $product->getOutOfStock(),
                $product->getSkuWeight(),
                $product->getSkuHeight(),
                $product->getSkuLength(),
                $product->getSkuWidth(),
                $product->getId()
            ]);

            // delete all data for this product in child tables
            $sqlDelete = "DELETE 
                              pi.*,
                              ps.*,
                              pt.*
                          FROM product_images AS pi
                          LEFT JOIN product_supplements AS ps ON pi.product_parent = ps.product_parent
                          LEFT JOIN product_tags AS pt ON pi.product_parent = pt.product_parent
                          WHERE pi.product_parent = ?";
            $statementDelete = $this->connection->prepare($sqlDelete);
            $statementDelete->execute([
                $product->getId()
            ]);

            // if anything has been deleted insert new values
            if($statementDelete->rowCount() > 0){
                // UPDATE IMAGES
                $sqlInsert = "INSERT INTO product_images (image_name, product_parent) VALUES (?, ?)";
                $statementInsert = $this->connection->prepare($sqlInsert);
                $images = $product->getImages();
                foreach ($images as $image){
                    $statementInsert->execute([
                        $image,
                        $product->getId()
                    ]);
                }

                // UPDATE SUPPLEMENTS
                $sqlInsert = "INSERT INTO product_supplements (supplement_id, product_parent) VALUES (?, ?)";
                $statementInsert = $this->connection->prepare($sqlInsert);
                $supplements = $product->getSupplements();
                foreach ($supplements as $supplement){
                    $statementInsert->execute([
                        $supplement,
                        $product->getId()
                    ]);
                }

                // UPDATE TAGS
                $sqlInsert = "INSERT INTO product_tags (tag_id, product_parent) VALUES (?, ?)";
                $statementInsert = $this->connection->prepare($sqlInsert);
                $tags = $product->getTags();
                foreach ($tags as $tag){
                    $statementInsert->execute([
                        $tag,
                        $product->getId()
                    ]);
                }
            }

            // commit transaction
            $this->connection->commit();

            // set status
            if($statement->rowCount() > 0 or $statementDelete->rowCount() > 0){
                $shared->setState(200);
            }else {
                $shared->setState(304);
            }


        } catch(PDOException $e){
            // rollback everything in case of failure
            $this->connection->rollBack();

            // set status
            $shared->setState(304);

            // get error code
            $code = $e->errorInfo[1];

            // set appropriate monolog entry depending on error code value
            if((int)$code >= 1000 && (int)$code <= 1749){
                $this->monolog->addError('Edit product mapper: ' . $e);
            }else {
                $this->monolog->addWarning('Edit product mapper: ' . $e);
            }
        }

        // return response
        return $shared;
    }


    /**
     * Delete product
     *
     * @param Product $product
     * @return Shared
     */
    public function deleteProduct(Product $product, Shared $shared):Shared {

        try{
            // begin transaction
            $this->connection->beginTransaction();

            // set database instructions
            $sql = "DELETE 
                      p.*,
                      pi.*,
                      ps.*,
                      pt.* 
                    FROM products AS p
                    LEFT JOIN product_images AS pi ON p.id = pi.product_parent
                    LEFT JOIN product_supplements AS ps ON p.id = ps.product_parent
                    LEFT JOIN product_tags AS pt ON p.id = pt.product_parent
                    WHERE p.id = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $product->getId()
            ]);

            // check if anything is deleted and set response
            if($statement->rowCount() > 0){
                $shared->setState(200);
            }else{
                $shared->setState(304);
            }

            // commit transaction
            $this->connection->commit();

        }catch (PDOException $e){
            // rollback everything in case of failure
            $this->connection->rollBack();

            // set state
            $shared->setState(304);

            // get error code
            $code = $e->errorInfo[1];

            // set appropriate monolog entry dependeng on error code value
            if((int)$code >= 1000 && (int)$code <= 1749){
                $this->monolog->addError('Delete product mapper: ' . $e);
            }else {
                $this->monolog->addWarning('Delete product mapper: ' . $e);
            }
        }

        // return response
        return $shared;
    }


    /**
     * Add product
     *
     * @param Product $product
     * @return Shared
     */
    public function addProduct(Product $product, Shared $shared):Shared
    {
        try {

            // beginn transaction
            $this->connection->beginTransaction();

            // set database instructions for inserting product info
            $sql = "INSERT INTO 
                      products 
                      (stripe_product_id, stripe_sku_id, name, description, price, discount, sku_weight, sku_height, sku_length, sku_width)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $product->getStripeProductId(),
                $product->getStripeSkuId(),
                $product->getName(),
                $product->getDescription(),
                $product->getPrice(),
                $product->getDiscount(),
                $product->getSkuWeight(),
                $product->getSkuHeight(),
                $product->getSkuLength(),
                $product->getSkuWidth()
            ]);

            // check if anything is inserted in database, procede with rest of actions and set shared state
            if($statement->rowCount() > 0){

                // set product parent id
                $productParent = $this->connection->lastInsertId();

                // insert product images
                $images = $product->getImages();
                $sqlImages = "INSERT INTO 
                                  product_images
                                  (image_name, product_parent)
                                  VALUES (?, ?)";
                $statementImages = $this->connection->prepare($sqlImages);
                foreach ($images as $image){
                    $statementImages->execute([
                        $image,
                        $productParent
                    ]);
                }

                // insert product supplements
                $supplements = $product->getSupplements();
                $sqlSupplements = "INSERT INTO 
                                      product_supplements
                                      (supplement_id, product_parent)
                                      VALUES (?, ?)";
                $statementSupplements = $this->connection->prepare($sqlSupplements);
                foreach ($supplements as $supplement){
                    $statementSupplements->execute([
                        $supplement,
                        $productParent
                    ]);
                }

                // insert product tags
                $tags = $product->getTags();
                $sqlTags = "INSERT INTO 
                              product_tags
                              (tag_id, product_parent)
                              VALUES (?, ?)";
                $statementTags = $this->connection->prepare($sqlTags);
                foreach ($tags as $tag){
                    $statementTags->execute([
                        $tag,
                        $productParent
                    ]);
                }

                // if everything pass good set appropriate state
                $shared->setState(200);

            }else {
                $shared->setState(304);
            }

            // commit transaction
            $this->connection->commit();

        }catch (PDOException $e){
            // rollback everything in case of failure
            $this->connection->rollBack();

            // set state
            $shared->setState(304);

            // get error code
            $code = $e->errorInfo[1];

            // set appropriate monolog entry dependeng on error code value
            if((int)$code >= 1000 && (int)$code <= 1749){
                $this->monolog->addError('Add product mapper: ' . $e);
            }else {
                $this->monolog->addWarning('Add product mapper: ' . $e);
            }
        }

        // return response
        return $shared;
    }
}