<?php
/*
 * Class cache
 * @author Repetskiy Maksim
 */


class CachePrintFile implements CacheInterface{
   /**
     * @var string
     */
   protected $cacheDir;

   /**
     * @param string $cacheDir
     * @param int $cachetime
     */
   public function __construct($cacheDir = '.') {
      $this->cacheDir = $cacheDir;
   }

   protected function createFile($key) {
      return $this->cacheDir . '/' . $key . '.dat';
   }

   /**
     * Store a mixed type value in cache for a certain amount of seconds.
     * Allowed values are primitives and arrays.
     *
     * @param string $key
     * @param mixed $value
     * @param int $duration Duration in seconds
     * @return mixed
     */
   public function set(string $key, $value, int $duration){

      $filename = $this->createFile($key);

      if (!file_exists($this->cacheDir))
         if(!mkdir($this->cacheDir, 0700))
            throw new Exception('Could not create folder cache. Insufficient rights.');

      if(!$f = fopen($filename, 'w')){
         throw new Exception('Could not write cache:');
      }

      fwrite($f, json_encode(['timeDuration' => time() + $duration, 'data' => $value]));
      //fwrite($f, serialize([ 'timeDuration' => time() + $duration, 'data' => $value]));

      fclose($f);

      return true;
   }
   
   /**
     * Retrieve stored item.
     * Returns the same type as it was stored in.
     * Returns null if entry has expired.
     *
     * @param string $key
     * @return mixed|null
     */
   public function get(string $key){
      $filename = $this->createFile($key);

      if(!file_exists($filename)  || !is_readable($filename))
         return null;

      $data = json_decode(file_get_contents($filename));
      //$data = unserialize(file_get_contents($filename));

      if($data->timeDuration < time())
         return false;

      return $data->data;
   }
}
