<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 9/3/18
 * Time: 11:39 AM
 */

namespace Model\Service;

use Dubture\Monolog\Reader\LogReader;
use Model\Entity\ResponseBootstrap;
use Model\Mapper\MonologMapper;
use Monolog\Logger;

class MonologService
{

    private $monologMapper;
    private $monolog;

    public function __construct(MonologMapper $monologMapper)
    {
        $this->monologMapper = $monologMapper;
        $this->monolog = new Logger('monolog');
    }


    /**
     * Get logs service
     *
     * @return ResponseBootstrap
     */
    public function getLogs():ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // set responses variable and read file
            $responses = [];
            $reader = new LogReader('../resources/loggs/monolog.txt');

            // Get logs
            foreach ($reader as $key=>$log) {
                if(!empty($log['date'])){
                    array_push($responses,
                        [
                            'id' => $key,
                            'date' => $log['date']->format('Y-m-d H:i:s'),
                            'logger'=> $log['logger'],
                            'level' => $log['level'],
                            'message' => $log['message']
                        ]
                    );
                }
            }

            // set response
            if(!empty($responses)){
                $response->setStatus(200);
                $response->setMessage('Success');
                $response->setData(
                    $responses
                );
            }else {
                $response->setStatus(204);
                $response->setMessage('No content');
            }

            // return data
            return $response;

        }catch (\Exception $e){
            // write monolog entry
            $this->monolog->addError('Get logs service: ' . $e);

            // set response on failure
            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }
    }


    /**
     * Delete log from monolog.txt
     *
     * @param string $date
     * @return ResponseBootstrap
     */
    public function deleteLog(string $date):ResponseBootstrap {

        try{
            // create response object
            $response = new ResponseBootstrap();

            // set variable for tracking if anything has been deleted
            $deleted = false;

            // open file
            $file_out = file("../resources/loggs/monolog.txt");

            // loop through file lines and find logg to delete
            foreach($file_out as $key=>$line){
                if(strpos($line, $date) !== false){
                    //Delete the recorded line
                    unset($file_out[$key]);

                    // change tracker value
                    $deleted = true;
                }
            }

            // write new data into file
            file_put_contents("../resources/loggs/monolog.txt", $file_out);

            // set response
            if($deleted === true){
                $response->setStatus(200);
                $response->setMessage('Success');
            }else {
                $response->setStatus(304);
                $response->setMessage('Not modified');
            }

            // return response
            return $response;

        }catch(\Exception $e){
            // write monolog entry
            $this->monolog->addError('Delete log service: ' . $e);

            // set response on failure
            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }
    }

}