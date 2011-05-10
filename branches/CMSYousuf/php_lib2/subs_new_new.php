<?php
   function print_r_html ( $variable, $data_type = 0 ) {
      print "<font face='Courier New' size='2'>";
      if ( $data_type ) {
         if ( is_array($variable) ) {
            print "Array<br>\r\n(<br>\r\n";
            foreach ( $variable as $key => $value ) {
               print "&nbsp; &nbsp;['" . $key . "'] (" . gettype($value) . ") => $value<br>\r\n";
               #print "&nbsp; &nbsp;['" . $key . "'] => $value<br>";
            }
            print ")";
         }
      } else {
         print str_replace( "  ", " &nbsp;", nl2br( print_r( $variable, 1 ) ) );
      }
      print "</font>";
   }

   function user_ip () {
      return ( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
// http://www.php.net/variables.predefined - 01-May-2003 09:06
//if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
//   if ($_SERVER["HTTP_CLIENT_IP"]) {
//    $proxy = $_SERVER["HTTP_CLIENT_IP"];
//  } else {
//    $proxy = $_SERVER["REMOTE_ADDR"];
//  }
//  $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
//} else {
//  if ($_SERVER["HTTP_CLIENT_IP"]) {
//    $ip = $_SERVER["HTTP_CLIENT_IP"];
//  } else {
//    $ip = $_SERVER["REMOTE_ADDR"];
//  }
//}
   }

   function two_decimals_new ( $number, $show_2_decimals_if_fraction = 0, $leave_as_is = 0 ) {
      $number = number_format( $number, 2, ".", "," );
      if ( $leave_as_is ) {
         # doesnt remove the fraction
      } else if ( $show_2_decimals_if_fraction ) {
         # eliminates only trailing '.00'
         $number = preg_replace( "/\.0+$/", "", $number );
      } else {
         # elimiates trailing 0s and the decimal if necessary
         $number = preg_replace( "/\.?0+$/", "", $number );
      }
      return $number;
   }

	function shorten_string ( $string, $size_to_shorten_to, $crop = 0 ) {
	   if ( strlen($string) > $size_to_shorten_to ) {
	      if ( $crop ) {
		      #$string = substr( $string, 0, $size_to_shorten_to-2 ) . "...";
		      $string = preg_replace( "/[-\,\(\s]*$/", "", substr( $string, 0, $size_to_shorten_to-2 ) ) . "...";
	      } else {
		      #$string = preg_replace( "/\s*[,-]?\s+\S*$/s", "", substr( $string, 0, $size_to_shorten_to-2 ) ) . "...";
		      $string = preg_replace( "/\s*[,-]?\s+[\w\(]*$/", "", substr( $string, 0, $size_to_shorten_to-2 ) ) . "...";
		   }
		}
		return $string;
	}

   function random_password( $n=8, $chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890' ) {  
      srand( (double)microtime() * 1000000 );  
      $m = strlen( $chars );
      while( $n-- ) { 
         $x .= substr( $chars, rand()%$m, 1 ); 
      }  

      return $x;  
   }

   # http://www.php.net/manual/en/function.ucfirst.php
   function my_ucwords($str, $is_name=false, $capitalize_no_vowel_words = false) {
      // exceptions to standard case conversion
      if ($is_name) {
          $all_uppercase = '';
          $all_lowercase = 'De La|De Las|Der|Van De|Van Der|Vit De|Von|Or|And';
      } else {
          // addresses, essay titles ... and anything else
          $all_uppercase = 'Po|Rr|Se|Sw|Ne|Nw';
          $all_lowercase = 'A|And|As|By|In|Of|Or|To';
          // Philipz Inc. addon
          $all_uppercase .= '|Ii|Iii|Iv|Vi|Vii|Viii|Ix|Xi';
          $all_lowercase .= '|At|The|On|For';
      }
      $prefixes = 'Mc';
      // Philipz changed "'s"
      $suffixes = "'\w";
   
      // captialize all first letters
      $str = preg_replace('/\\b(\\w)/e', 'strtoupper("$1")', strtolower(trim($str)));
   
      if ($all_uppercase) {
          // capitalize acronymns and initialisms e.g. PHP
          $str = preg_replace("/\\b($all_uppercase)\\b/e", 'strtoupper("$1")', $str);
      }
      if ($all_lowercase) {
          // decapitalize short words e.g. and
          if ($is_name) {
              // all occurences will be changed to lowercase
              $str = preg_replace("/\\b($all_lowercase)\\b/e", 'strtolower("$1")', $str);
          } else {
              // first and last word will not be changed to lower case (i.e. titles)
              $str = preg_replace("/(?<=\\W)($all_lowercase)(?=\\W)/e", 'strtolower("$1")', $str);
          }
      }
      if ($prefixes) {
          // capitalize letter after certain name prefixes e.g 'Mc'
          $str = preg_replace("/\\b($prefixes)(\\w)/e", '"$1".strtoupper("$2")', $str);
      }
      if ($suffixes) {
          // decapitalize certain word suffixes e.g. 's # Philipz fixed below line
          #$str = preg_replace("/(\\w)($suffixes)\\b/e", '"$1".strtolower("$2")', $str);
          $str = preg_replace("/(\\w)($suffixes)\\b/e", "'$1'.strtolower('$2')", $str);
      }

      if ( $capitalize_no_vowel_words ) {
         # Philipz Inc. - Capitilizing words that have no vowels
         #$str = preg_replace("/\\b([^aeiouy]{1,4})\\b/ie", "strtoupper('$1')", $str);
         $str = preg_replace("/\\b([bcdfghjklmnpqrstvwxz]{1,4})\\b/ie", "strtoupper('$1')", $str);
      }

      return $str;
   }

   function sentenceCase($s){
      # http://php.net/strings
      # Author : James Baker
      $str = strtolower($s);
      $cap = true;
     
      for($x = 0; $x < strlen($str); $x++){
          $letter = substr($str, $x, 1);
          if($letter == "." || $letter == "!" || $letter == "?" || $letter == "\n" || $letter == "\r"){
              $cap = true;
          }elseif($letter != " " && $cap == true){
              $letter = strtoupper($letter);
              $cap = false;
          }
         
          $ret .= $letter;
      }
     
      return $ret;
   }

//   function SentenceCase($str) {
//      # http://www.php.net/manual/en/function.ucfirst.php
//      # for backward compatibility as i used the code of < Northie > 05-Sep-2006 04:39 earlier
//   }

	function array_unshift_assoc(&$arr, $key, $val) { 
		$arr = array_reverse($arr, true); 
		$arr[$key] = $val; 
		$arr = array_reverse($arr, true); 
		return count($arr); 
	}

   function get_users_country ( $connect_id, $ip_address = "", $country_db = "", $country_table = "" ) {
      $country_db = ( $country_db ) ? $country_db : "theemira_count";
      $country_table = ( $country_table ) ? $country_table : "ip_to_country";
      $ip_address = ( $ip_address ) ? $ip_address : $_SERVER['REMOTE_ADDR'];

      $current_db = mysql_current_databse( $connect_id );

      #preg_match( "/(\d*)\.(\d*)\.(\d*)\.(\d*)/", $_SERVER['REMOTE_ADDR'], $ip_parts );
      #$ip_2_long = ($ip_parts[1] * 16777216) + ($ip_parts[2] * 65536) + ($ip_parts[3] * 256) + $ip_parts[4];
      $ip_2_long = sprintf( "%u", ip2long( $ip_address ) ); 

      if ( $current_db != $country_db ) {
         mysql_change_db( $connect_id, $country_db );
      }

      #$country_record = mysql_extract_records_where( $connect_id, $country_table, array( "ip_from <= $ip_2_long" => '', "ip_to >= $ip_2_long" => '' ) );
      $country_record = mysql_extract_records_where( $connect_id, $country_table, "ip_from <= $ip_2_long AND ip_to >= $ip_2_long" );

      if ( !$country_record[1]['country_code2'] ) {
         $user_country_code = "UN";
         $user_country_name = "Unknown";
      } else {
         $user_country_code = $country_record[1]['country_code2'];
         $user_country_name = $country_record[1]['country_name'];
      }

      if ( $current_db != $country_db ) {
         mysql_change_db( $connect_id, $current_db );
      }

      return array( $user_country_code, $user_country_name );
   }

   function create_drop_down_categories ( $data_array, $name_field_name = "", $id_name_field = "", $parent_id_field_name = "", $category_separator = "-" ) {
      if ( is_array(current($data_array)) ) {
         # its a two dimensional array and needs to be processed first
         if ( !$parent_id_field_name ) {
            # handles the following scenarios
            #  [001] => array( [name] => 'Accounting' )
            #  [1] => array( [id] => '001', [name] => 'Accounting' )
            foreach ( $data_array as $key => $value_array ) {
               $key = ( $id_name_field ) ? $value_array[$id_name_field] : $key;
               $new_data_array[$key] = $value_array[$name_field_name];
            }
         } else {
            # handles when the parent_id field needs to be added to the beginning of the category_id
            #  [1] => array( [parent_id] => 0, [name] => 'Accounting' )
            $process_key_paths = 1;
            foreach ( $data_array as $key => $value_array ) {
               $key = ( $id_name_field ) ? $id_name_field : $key;
               if ( $value_array[$parent_id_field_name] ) {
                  $parent_id_array[$key] = $value_array[$parent_id_field_name];
               }
               #if ( $key_paths[$value_array[$parent_id_field_name]] ) {
               #   $key_paths[$key] = $key_paths[$value_array[$parent_id_field_name]] . $category_separator . $key;
               #} else if ( $value_array[$parent_id_field_name] ) {
               #   $key_paths[$key] = $value_array[$parent_id_field_name] . $category_separator . $key;
               #}
               $new_data_array[$key] = $value_array[$name_field_name];
            }
            $data_array = $new_data_array;

            # creates a reference table between id and path id
            foreach ( $data_array as $key => $value ) {
               $key_paths[$key] = $key;
               $categorised_data_array[$key] = $value;
               if ( $parent_id_array[$key] ) {
                  $current_key = $parent_id_array[$key];
                  $key_path = $key;
                  do {
                     $key_path = $current_key . $category_separator . $key_path;
                     $current_key = $parent_id_array[$current_key];
                  } while ( $current_key );
                  $key_paths[$key] = $key_path;
                  $categorised_data_array[$key_path] = $value;
               }
            }
         }

         $data_array = $new_data_array;
      }

      # create the full category path for each entry
      foreach ( $data_array as $code => $name ) {
         if ( $process_key_paths ) {
            $number_of_indents = substr_count( $key_paths[$code], $category_separator );
            $parent_id = preg_replace( "/-\w+$/", "", $key_paths[$code] );
         } else {
            $number_of_indents = substr_count( $code, $category_separator );
            $parent_id = preg_replace( "/-\w+$/", "", $code );
         }

         $categorised_data_array[$code] = $name;
         if ( $number_of_indents ) {
            $region_names = array();
            do {
               #$categorised_data_array[$parent_id] = ( $categorised_data_array[$parent_id] ) ? $categorised_data_array[$parent_id] : $data_array[$parent_id];
               $region_names[] = str_replace( " ", "_", $categorised_data_array[$parent_id] );
               $parent_id = preg_replace( "/" . preg_quote($category_separator, '/') . "?\w+$/", "", $parent_id );
            } while( $parent_id );
            $region_names = array_reverse( $region_names );

            $categorised_data_array[$key_paths[$code]] = $name;
            $category_list[$code] = join( " ", $region_names ) . " " . str_replace( " ", "_", $name );
         } else {
            $category_list[$code] = str_replace( " ", "_", $name );
         }
      }

      # sort the array according to parent category names
      asort( $category_list );
      foreach ( $category_list as $key => $value ) {
         #if ( $process_key_paths ) {
         #   $number_of_indents = substr_count( $key_paths[$key], "-" );
         #} else {
         #   $number_of_indents = substr_count( $key, "-" );
         #}
         $number_of_indents = substr_count( preg_replace("/\s+/", " ", $value), " " );

         $drop_down_list[$key] = $data_array[$key];
         if ( $number_of_indents > 0 ) {
            $drop_down_list[$key] = str_repeat( "&#8212;", $number_of_indents ) . " " . $data_array[$key];
         }
      }

      return $drop_down_list;
   }

# http://www.php.net/manual/en/function.http-build-query.php - < mqchen at gmail dot com > 03-Feb-2007 01:27
# added $question_mark and $comma variables and its related modifications
#if(!function_exists('http_build_query')) {
   function http_build_query_new($data,$prefix=null,$sep='',$key='',$question_mark=0,$comma=0,$current_url=0) {
      $ret = array();
      foreach((array)$data as $k => $v) {
         $k = urlencode($k);
         if(is_int($k) && $prefix != null) {
            $k = $prefix.$k;
         }
         if(!empty($key)) {
            #$k = $key."[".$k."]";
            # making it more like the original php
            $k = $key."[]";
         }
         
         if(is_array($v) || is_object($v)) {
           array_push($ret,http_build_query_new($v,"",$sep,$k));
         } else {
            # added the if so that blank entries dont show up
            if ( urlencode($v) ) {
               array_push($ret,$k."=".urlencode($v));
            }
         }
      }
      if(empty($sep)) {
         $sep = ini_get("arg_separator.output");
      }
      $return_data = implode($sep, $ret);
      if ( $comma ) {
         if ( $return_data ) {
            $return_data .= "&";
         }
         if ( $question_mark ) {
            $return_data = "?" . $return_data;
         }
      } else if ( $question_mark AND $return_data ) {
         $return_data = "?" . $return_data;
      }
      if ( $current_url ) {
         if ( $current_url == 1 ) {
            preg_match( "/([^\/]+)$/i", $_SERVER['PHP_SELF'], $results );
            $return_data = $results[1] . $return_data;
         } else {
            $return_data = $current_url . $return_data;
         }
      }
      return $return_data;
   }
#}

   function mysql_select_total_records ( $connect_id ) {
      # requires mysql 4+
      # http://dev.mysql.com/doc/refman/4.1/en/information-functions.html#function_found-rows
      $sql_result = mysql_query( "SELECT FOUND_ROWS()", $connect_id );
      print "Yes";
      print "<pre>";
      print_r( $sql_result );
      print "</pre>";
      exit;

      if ( $sql_result ) {
         $sql_record = mysql_fetch_assoc( $sql_result );
         return $sql_record['COUNT(*)'];
      }
      return 0;
   }
?>
