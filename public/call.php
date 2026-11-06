<?php
ini_set("allow_url_fopen", TRUE);
$ref = empty($_SERVER["HTTP_REFERER"]) ? $_REQUEST['host'] : $_SERVER["HTTP_REFERER"];

switch(TRUE) {
  case ini_get("allow_url_fopen"):
   // set up our headers
   $hdrs = array( 'http' => array(
       'method' => "GET",
       'header'=> "Host: www.example.net\r\n" .
        "User-Agent: Audio Broadcast\r\n" .
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" .
        "Accept-Language: en-us,en;q=0.5\r\n" .
        "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n" . 
        "Keep-Alive: 115\r\n" .
        "Proxy-Connection: keep-alive\r\n" .
        "Referer: ".$ref,     // Setting the http-referer
       'request_fulluri' => true
       )
   );
   $context = stream_context_create ($hdrs);  // set up the context
   $fp = fopen('https://'.$_REQUEST['host'].':8901/cgi/web/receive.pl?Account=12392061647&Password=jonas1444&First_Phone_Number='.$_REQUEST['First_Phone_Number'].'&Second_Phone_Number='.$_REQUEST['Second_Phone_Number'], 'r', false, $context);
    break;
  default:
    error_log('Enable allow_url_fopen');
}
?>