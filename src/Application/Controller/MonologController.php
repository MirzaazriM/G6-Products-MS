<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 9/3/18
 * Time: 11:39 AM
 */

namespace Application\Controller;


use Model\Entity\ResponseBootstrap;
use Model\Service\MonologService;
use Symfony\Component\HttpFoundation\Request;

class MonologController
{

    private $monologService;

    public function __construct(MonologService $monologService)
    {
        $this->monologService = $monologService;
    }


    /**
     * Get logs
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function getLogs(Request $request):ResponseBootstrap {
        // call service for data
        return $this->monologService->getLogs();
    }


    /**
     * Delete log
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function deleteLog(Request $request):ResponseBootstrap {
        // get date from url
        $date = $request->get('date');

        // create response object
        $response = new ResponseBootstrap();

        // check if date is set
        if(isset($date)){
            return $this->monologService->deleteLog($date);
        }else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }

        // return response
        return $response;
    }

}