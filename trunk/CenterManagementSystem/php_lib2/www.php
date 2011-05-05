<?php
   function socket_get_http ( $file_url, $request_features = array() ) {
      if ( $request_features['Server'] ) {
         # the submitted request fields contains http responce fields
         return array( "", "" );
      }
      $url_parse = parse_url( $file_url );

      $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
      if ($socket < 0) {
          echo "socket_create() failed: reason: " . socket_strerror($socket) . "\n";
          return;
      }

      $address = gethostbyname($url_parse['host']);
      $service_port = getservbyname('www', 'tcp');
      $result = socket_connect($socket, $address, $service_port);

      if ($result < 0) {
          echo "socket_connect() failed.\nReason: ($result) " . socket_strerror($result) . "\n";
          exit;
      }

      if ( $request_features['method'] == "POST" ) {
         $requests[] = "POST $url_parse[path] HTTP/1.1";
         push( @request, "Content-type: application/x-www-form-urlencoded" );
         push( @request, "Content-length: " . length($url_parse['query']) );
         push( @request, $url_parse['query'] );
      } else {
         if ( !$request_features['method'] ) {
            $request_features['method'] = "GET";
         }
         if ( $url_parse['query'] ) {
            $requests[] = "$request_features[method] $url_parse[path]?$url_parse[query] HTTP/1.1";
         } else {
            $requests[] = "$request_features[method] $url_parse[path] HTTP/1.1";
         }
      }
      $requests[] = "Host: $url_parse[host]";

      foreach( $request_features as $key => $value ) {
         if ( $value and $key != "method" ) {
            $requests[] = "$key: $value";
         }
      }
      #$requests[] = "Referer: $request_features[refferal_url]";
      #$requests[] = "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.10)";

      if ( !$request_features['Connection'] ) {
         # The following line is a must if using HTTP/1.1
         $requests[] = "Connection: Close";
      }

      $http_request = join( "\r\n", $requests ) . "\r\n\r\n";
      socket_write( $socket, $http_request, strlen($http_request) );

      $out = '';
      while ($out = socket_read($socket, 2048)) {
         $output_text .= $out;
      }

      socket_close( $socket );
      #print "<pre>";
      #print_r( $http_request );
      #print "</pre>";
      #exit;

      $responce_lines = explode( "\n", $output_text );

      #$http_responce_code = trim( array_shift( $http_responces ) );
      #$http_responce = trim( array_shift( $responce_lines ) );
      while ( $http_responce = trim(array_shift($responce_lines)) ) {
         list( $http_responce_name, $http_responce_value ) = explode( ": ", $http_responce, 2 );
         if ( !$http_responce_value ) {
            $http_responce_value = $http_responce_name;
            $http_responce_name = "0";
         }
         $http_responces[$http_responce_name] = $http_responce_value;
      }

      if ( $responce_lines ) {
         $responce_file = join( "\n", $responce_lines );
      }

      return array( $responce_file, $http_responces );
   }
?>
