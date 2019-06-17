<?php

/*
 *  Printful's Shipping Rate API
 */

class ConnectPSR{
    
    /**
     * @var string
     */
    protected $base_uri ="";

    /**
     * @var string
     */
    protected $api_key = "";

     /**
     * @var object GuzzleHttp\Client
     */
    protected $client = null;

    /**
     * @var object 
     */
    protected $fileCache = null;

    /**
     * @var int
     */
    protected $timecache;

    /**
     * @var int
     */
    protected $statusCode = 0;

      /**
     * @var string
     */
    protected $reasonError = '';


    /**
     * @param object CacheInterface $cache
     * @param int
     */
    public function __construct(CacheInterface $cache, $timecache = 300){
        $this->fileCache = $cache;
        $this->timecache = $timecache;
    }

    /**
     * @param string
     * @param string
    */
    public function connect($base_uri, $api_key){
        $this->base_uri = $base_uri;
        $this->api_key = base64_encode($api_key);
        $this->client = new GuzzleHttp\Client([
            'base_uri' => $this->base_uri,
            'headers'=>[
                'Authorization' => 'Basic '. $this->api_key,
            ],
            'http_errors' => false
        ]);
    }

    /**
     * @param string
     * @param mixed
     */
    public function requestPost($request_uri, $request_body){

        $cacheId = md5($this->base_uri.$request_uri);

        if($this->fileCache->get($cacheId)){
            $this->statusCode = -200;
        }else{
            $response = $this->client->request( 'POST', $request_uri,['json' => $request_body] );
            if(200 !== $response->getstatusCode()){
                $this->statusCode = $response->getstatusCode();
                $this->reasonError = $response->getreasonPhrase();
                return false;
            }
            $data = json_decode($response->getBody()->getContents(), true);
            $this->fileCache->set($cacheId, $data, $this->timecache);
            $this->statusCode = $response->getstatusCode();
        }
    }

    /**
     * GET STATUS
     */
    public function statusRequest(){
        echo "Status code: ";
        if($this->statusCode == 200){
            echo $this->statusCode;
        }else if($this->statusCode == -200){
            echo $this->statusCode.' FROM CACHE';
        }else{
            echo $this->statusCode . ' Reason error: ' . $this->reasonError;
        }
    }

}