<?php

trait DB {

    private $host = '127.0.0.1';
    private $dbname = 'DB_NAME';
    private $username = 'DB_USER';
    private $password = 'DB_PASS';


    public function db() {
        
          @$mysql = new mysqli($this->host,$this->username,$this->password,$this->dbname);
          if ($mysql->connect_error) {
               echo 'db Error';
               die();
          }
          $mysql->set_charset("utf8");
          return $mysql;
      }
      private function   toJSON($res) {
        $result = array();
        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
          array_push($result,$row);
        }
        return json_encode($result);
      }


}

