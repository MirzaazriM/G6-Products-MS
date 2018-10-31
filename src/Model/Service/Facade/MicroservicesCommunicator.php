<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 9/4/18
 * Time: 11:27 AM
 */

namespace Model\Service\Facade;


class MicroservicesCommunicator
{

    private $data;
    private $configuration;

    public function __construct(array $data, array $configuration)
    {
       $this->data = $data;
       $this->configuration = $configuration;
    }


    /**
     * Integrate MSs data
     */
    public function integrateMicroservicesData(){
        // loop through data and call neccessary MSs for each array item
        for($i = 0; $i < count($this->data); $i++){
            // get ids to send
            $supplementIds = $this->data[$i]['description'];
            $tagIds = $this->data[$i]['tags'];

            // set new data to supplements index
            $this->data[$i]['description'] = json_decode($this->callSupplementsMS($supplementIds));

            // get only supplement names and their descriptions
            $this->data[$i]['description'] = $this->getSupplementNamesAndDescriptions($this->data[$i]['description']);

            // set new data to tags index
            // TODO
            // $this->data[$i]['tags'] = json_decode($this->callTagsMS(implode(',', $tagIds)));
        }

        // return full data
        return $this->data;
    }


    public function getSupplementNamesAndDescriptions($data){

        //die(print_r($data));

        $formattedData = [];

        if(!empty($data)){
            for($i = 0; $i < count($data); $i++){
                $formattedData[$i]['title'] = $data[$i]->name;
                $formattedData[$i]['description'] = $data[$i]->description;
            }
        }

        return $formattedData;
    }


    /**
     * Call supplements MS
     *
     * @param string $ids
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function callSupplementsMS(string $ids){
        // call Supplements MS for supplements data
        $client = new \GuzzleHttp\Client();
        $result = $client->request('GET', $this->configuration['supplements_url'] . '/supplements/ids?ids=' . $ids, []);
        $supplements = $result->getBody()->getContents();

        // return data
        return $supplements;
    }


    /**
     * Call tags MS
     *
     * @param string $ids
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function callTagsMS(string $ids){
        // call Tags MS for supplements data
        $client = new \GuzzleHttp\Client();
        $result = $client->request('GET', $this->configuration['tags_url'] . '/tags/ids?ids=' . $ids, []);
        $tags = $result->getBody()->getContents();

        // return data
        return $tags;
    }

}