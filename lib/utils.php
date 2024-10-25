<?php

function set_token() {
    $token = hash_hmac('sha1', time(), time());
    $_SESSION['_token'] = $token;
  }
  
  function insert_token()  {
    echo '<input type="hidden" name="_token" id="_token" value="'.$_SESSION['_token'].'">';
  }

  function grabIpInfo($ip)
{

  $curl = curl_init();

  curl_setopt($curl, CURLOPT_URL, "https://api.ipgeolocationapi.com/geolocate/" . $ip);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

  $returnData = curl_exec($curl);

  curl_close($curl);

  return json_decode($returnData,true);

}

function get_ton_price() {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.binance.com/api/v3/ticker/price?symbol=TONUSDT",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response,true);
  } 

  function checkAddress($address) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "http://localhost:3000/api/v1/validateAddress/".$address,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($response,true);

    return $result['isAddress'];
  }


function getOS($user_agent) {
    $os_platform    =   "Unknown OS Platform";
    $os_array       =   array(
                            '/windows nt 11/i'    =>  'Windows 11',
                            '/windows nt 10.0/i'    =>  'Windows 10',
                            '/windows nt 10/i'     =>  'Windows 10',
                            '/windows nt 6.3/i'     =>  'Windows 8.1',
                            '/windows nt 6.2/i'     =>  'Windows 8',
                            '/windows nt 6.1/i'     =>  'Windows 7',
                            '/windows nt 6.0/i'     =>  'Windows Vista',
                            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                            '/windows nt 5.1/i'     =>  'Windows XP',
                            '/windows xp/i'         =>  'Windows XP',
                            '/windows nt 5.0/i'     =>  'Windows 2000',
                            '/windows me/i'         =>  'Windows ME',
                            '/win98/i'              =>  'Windows 98',
                            '/win95/i'              =>  'Windows 95',
                            '/win16/i'              =>  'Windows 3.11',
                            '/macintosh|mac os x/i' =>  'Mac OS X',
                            '/mac_powerpc/i'        =>  'Mac OS 9',
                            '/linux/i'              =>  'Linux',
                            '/ubuntu/i'             =>  'Ubuntu',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'iPod',
                            '/ipad/i'               =>  'iPad',
                            '/android/i'            =>  'Android',
                            '/blackberry/i'         =>  'BlackBerry',
                            '/webos/i'              =>  'Mobile'
                        );

    foreach ($os_array as $regex => $value) {

        if (preg_match($regex, $user_agent)) {
            $os_platform    =   $value;
        }

    }

    return $os_platform;

}

  
function hash_pass($pass){
    return hash_hmac('sha256',$pass,$pass.'ozone_pass');
  }

  function page($arr){
    @$pg = $_GET['page'];
    if (!isset($pg)){
      $pg=1;
    }
    $start = ($pg * 5) - 5;
  
    $res = array_slice($arr,$start,5);
    return $res;
  
  }

  function show_date() {
    $date=date_create();
    echo date_format($date,"l, F j");
  }

  function show_date_time() {
    $date=date_create();
    echo date_format($date,"M j,H:i");
  }

  function shorten_hash($hash){
    return substr($hash,0,8).'...'.substr($hash,-8);
  } 

  function Post($url,$fields) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $fields,
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded'
        
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response,true);
  }

//   function time_elapsed_string($datetime, $full = false) {
//     $now = new DateTime;
//     $ago = new DateTime($datetime);

//     if ($ago > $now) {
//       $postfix = "remain";
//     }else{
//       $postfix = "ago";
//     }
//     $diff = $now->diff($ago);

//     $diff->w = floor($diff->d / 7);
//     $diff->d -= $diff->w * 7;

//     $string = array(
//         'y' => 'year',
//         'm' => 'month',
//         'w' => 'week',
//         'd' => 'day',
//         'h' => 'hour',
//         'i' => 'minute',
//         's' => 'second',
//     );
//     foreach ($string as $k => &$v) {
//         if ($diff->$k) {
//             $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
//         } else {
//             unset($string[$k]);
//         }
//     }

//     if (!$full) $string = array_slice($string, 0, 1);
//     return $string ? implode(', ', $string) . ' '.$postfix : 'just now';
// }

function fetchWebsiteHTML($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  
  $html = curl_exec($ch);
  if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
  }
  curl_close($ch);
  
  return $html;
}

function fetchOpenGraphTags($url) {
  $html = fetchWebsiteHTML($url);
  $doc = new DOMDocument();
  @$doc->loadHTML($html);
  
  $ogTags = [];
  $metaTags = $doc->getElementsByTagName('meta');

  // return $metaTags that its property attribute is equal to og:image

  foreach ($metaTags as $metaTag) {
      
       /** @var  DOMElement $metaTag */
      
       if ($metaTag->hasAttribute('property') && strpos($metaTag->getAttribute('property'), 'og:') === 0) {

       $ogTags[$metaTag->getAttribute('property')] = $metaTag->getAttribute('content');
       }
  }
  

  
  return $ogTags;
}

function auth() {
  return new class {

    public $isAdmin = false;

    public $hasGuard = false;

    function guard($role = null) {
       if (!$role) {
        return $this;
       }

      $this->hasGuard = true; 
      $this->isAdmin = isset($_SESSION['user']['role']) ?  $_SESSION['user']['role'] == $role : false;
      return $this;
    }

    function check() {
      
      if ($this->hasGuard) {
        return $this->isAdmin;
      }

      return isset($_SESSION['user']);

    }




  };
}

function asset(string $path) {
  
  $host_url = parse_url($path, PHP_URL_HOST);

  if ($host_url === false) {
    return $path;
  }else{
    return $host_url .'/assets/'. ltrim($path,'/');
  }
}

 
