<?php



    class page {

      public static function render($page,$arg = []) {
          ob_start();
          extract($arg);
          header('Content-type: text/html');
        include('pages/'.$page.'.php');
        return ob_end_flush();
      }

      public static  function renderBlade($page , $args = []) {

       global $blade; 
      echo $blade->make($page, $args)->render();

      }
    }


 ?>
