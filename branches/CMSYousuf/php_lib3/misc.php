<?php
//   error_reporting( E_ALL );
//   error_reporting( E_ALL ^ E_NOTICE ) - Report all errors except E_NOTICE;
//   ini_set('display_errors','On');

//   ob_start();
//   set_time_limit(0);
//   ------
//   print "";
//   ob_flush();
//   flush();

// phpinfo(8) - php modules | phpinfo(16) - $_ENV | phpinfo(32) - $_SERVER

   $FORM = array_merge( $_GET, $_POST );

   # using parse_str to parse the $_SERVER['QUERY_STRING'] wont work well, so the choice is to use the fuctions
   #  proper_parse_str or parse_str_ext (this needs a bit of modification so that non array values arent put into array form)

   # need to changed magic_quotes_gpc to Off in php.ini
   #if ( get_magic_quotes_gpc() ) {
   #   $FORM = stripslashes_deep( $FORM );
   #}
   # taken from php manual "stripslashes" lukas.skowronski at gmail dot com
   function stripslashes_deep ( $array ) {
      foreach ( $array as $key => $value ) {
         if( is_array($value) ){
            $value = stripslashes_deep( $value );
            $array_temp[$key] = $value;
         } else {
            $array_temp[$key] = stripslashes( $value );
         }
      } 
      return $array_temp; 
   }

	$full_dmy_date_time_wkday_zone = "l, jS F Y, H:i:s O"; # Sunday, 17th February 2008, 02:14 +0200
	$full_mdy_date_time_wkday_zone = "l, F jS Y, H:i:s O"; # Sunday, February 17th 2008, 02:14 +0200
	$abbr_dmy_date_time_wkday_zone = "D, j M Y, H:i:s O"; # Sun, 17th Feb 2008, 02:14 +0200
	$abbr_mdy_date_time_wkday_zone = "D, M j Y, H:i:s O"; # Sun, Feb 17th 2008, 02:14 +0200

	$full_dmy_date_timesm_wkday_zone = "l, jS F Y, h:i:sA O"; # Sunday, 17th February 2008, 02:14AM +0200
	$full_mdy_date_timesm_wkday_zone = "l, F jS Y, h:i:sA O"; # Sunday, February 17th 2008, 02:14AM +0200
	$abbr_dmy_date_timesm_wkday_zone = "D, j M Y, h:i:sA O"; # Sun, 17th Feb 2008, 02:14AM +0200
	$abbr_mdy_date_timesm_wkday_zone = "D, M j Y, h:i:sA O"; # Sun, Feb 17th 2008, 02:14AM +0200

	$full_dmy_date_timem_wkday_zone = "l, jS F Y, h:iA O"; # Sunday, 17th February 2008, 02:14AM +0200
	$full_mdy_date_timem_wkday_zone = "l, F jS Y, h:iA O"; # Sunday, February 17th 2008, 02:14AM +0200
	$abbr_dmy_date_timem_wkday_zone = "D, j M Y, h:iA O"; # Sun, 17th Feb 2008, 02:14AM +0200
	$abbr_mdy_date_timem_wkday_zone = "D, M j Y, h:iA O"; # Sun, Feb 17th 2008, 02:14AM +0200

	$full_dmy_date_time_wkday = "l, jS F Y, H:i A"; # Sunday, 17th February 2008, 02:14 AM
	$full_mdy_date_time_wkday = "l, F jS Y, H:i A"; # Sunday, February 17th 2008, 02:14 AM
	$full_dmy_date_wkday = "l, jS F Y"; # Sunday, 17th February 2008
	$full_mdy_date_wkday = "l, F jS Y"; # Sunday, February 17th 2008

	$full_date_with_weekday = "l, F jS, Y";
	$full_date = "F jS, Y"; # June 13th, 2007
	$full_date_time_gmt = "F jS, Y g:iA"; # June 13th, 2007 1:00AM
	$full_date_dmy = "jS F, Y"; # 13th June, 2007
	$full_date_dmy_short_month = "jS M, Y"; # 13th Jun, 2007
	$full_date_mdy_short_month = "M jS, Y"; # Jun 13th, 2007
	$short_date = "d-M-y"; # 13-Jun-07

   $mysql_datetime_format = "Y-m-d H:i:s";

   if(!function_exists('validate_email')) {
   # http://www.regular-expressions.info/email.html
   function validate_email ( $email_address ) {
      if ( !eregi( "^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$", $email_address ) ) {
         #^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$
         return "0";
      }
      return 1;
   }
   }

   function goto_url ( $url_to_got_to, $type = "", $delay = 0, $message = "" ) {
      if ( $type == "javascript" OR headers_sent() ) {
         if ( $delay ) {
            print "<html><head><title>Page with Redirect in $delay Seconds</title>";
            print "<meta http-equiv=\"refresh\" content=\"$delay;url=$url_to_got_to\"/>";
            print "</head><body>$message</body></html>";
         } else {
            # performs a javascript url redirect
            print "<script language=\"javascript\">\r\n<!-- \r\n if (window.location.replace)\r\n  location.replace(\"$url_to_got_to\");\r\n else\r\n  window.location.href = \"$url_to_got_to\";\r\n//-->\r\n</script>";
         }
      } else {
         header( "location: $url_to_got_to" );
      }
      exit;
   }

   function close_page () {
      print "<html><body onLoad='window.close();'></body></html>";
      exit;
   }

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

   function print_r2 ( $variable, $exit = 0, $no_htmlentities = 0 ) {
      # if php5, headers_list() can be used to determine if text or html format has been defined as the output
      print "<pre>";
      print ( $no_htmlentities ) ? print_r( $variable, 1 ) : htmlentities( print_r( $variable, 1 ) );
      print "</pre>";
      if ( $exit ) {
         exit;
      }
   }

   function current_file_name ( $use_request_uri = 0 ) {
      if ( $use_request_uri ) {
         $current_url = preg_replace( "/\?.+/", "", $_SERVER['REQUEST_URI'] );
         preg_match( "/([^\/]+)$/i", $current_url, $results );
      } else {
         preg_match( "/([^\/]+)$/i", $_SERVER['PHP_SELF'], $results );
      }
      return $results[1];
   }

   function current_file_url ( $use_request_uri = 0 ) {
      if ( $_SERVER['SERVER_NAME'] != $_SERVER['HTTP_HOST'] OR $use_request_uri ) {
         # incase the http requested server is different then the registered server name
         $current_url = "http://" . $_SERVER['HTTP_HOST'];
      } else {
         $current_url = "http://" . $_SERVER['SERVER_NAME'];
      }
      if ( $use_request_uri ) {
         $current_url .= preg_replace( "/\?.*$/", "", $_SERVER['REQUEST_URI'] );
      } else {
         $current_url .= $_SERVER['PHP_SELF'];
      }
      return $current_url;
   }

   function current_dir_url ( $use_request_uri = 0 ) {
      return preg_replace( "/[^\/]+$/", "", current_file_url($use_request_uri) );
   }

   function server_ip () {
      return gethostbyname( $_SERVER['SERVER_NAME'] );
   }

   # ------------------------- Array Functions -------------------------

   # it seems PHP has its own function for this called var_export($variable,true)
   function array_to_php ( $array, $variable_name = "", $format = 0, $key_array = array() ) {
      if ( !$format ) {
         if ( !count($array) ) return "array();";
         $php_string = "array( ";
         foreach ( $array as $key => $value ) {
            $key = addcslashes( $key , "'" );
            if ( is_array($value) ) {
               $php_string .= "'$key' => " . preg_replace( "/;$/", ", ", array_to_php( $value ) );
            } else if ( is_float($value) OR is_int($value) ) {
               $php_string .= "'$key' => $value, ";
            } else if ( is_null($value) ) {
               $php_string .= "'$key' => NULL, ";
            } else if ( is_bool($value) ) {
               $php_string .= "'$key' => " . ($value) ? "true" : "false" . ", ";
            } else {
               $php_string .= "'$key' => \"" . my_escape_string( $value ) . "\", ";
            }
         }
         $php_string = preg_replace( "/, $/", "", $php_string );
         $php_string .= " );";
      } else if ( $format == 1 ) {
         if ( !count($array) ) return "$variable_name = array();";
         foreach ( $array as $key => $value ) {
            if ( is_array($value) ) {
               array_push( $key_array, addcslashes( $key , "'" ) );
               $php_string .= array_to_php( $value, $variable_name, $format, $key_array );
               array_pop( $key_array );
            } else {
               if ( is_array($key_array) AND count($key_array) ) {
                  $associate_key = "['" . join( "']['", $key_array ) . "']['" . addcslashes( $key , "\\'" ) . "']";
               } else {
                  $associate_key = "['" . addcslashes( $key , "'" ) . "']";
               }
               if ( is_float($value) OR is_int($value) ) {
                  $php_string .= $variable_name . $associate_key . " = $value;\r\n";
               } else {
//                  $new_value = addcslashes( $value, '\\"' );
//                  $new_value = str_replace( "\r", '\r', $new_value );
//                  $new_value = str_replace( "\n", '\n', $new_value );
                  $php_string .= $variable_name . $associate_key . " = \"" . my_escape_string( $value ) . "\";\r\n";
               }
            }
         }
      } else if ( $format == 2 ) {
         if ( !count($array) ) return "$variable_name = array();";
         foreach ( $array as $key => $value ) {
            $key = addcslashes( $key , "'" );
            if ( is_array($value) ) {
               $php_string .= $variable_name . "['$key'] = " . array_to_php( $value ) . ";\r\n";
            } else {
               $php_string .= $variable_name . "['$key'] = \"" . my_escape_string( $value ) . "\";\r\n";
            }
         }
      }
      
      return $php_string;
   }

   function my_escape_string ( $value, $charlist = '\\"' ) {
      # could use mysql_real_escape_string, but it doesnt translate tabs
      # '\\"' is for text surrounded by "", while "\\'" for single quotes
      $new_value = addcslashes( $value, $charlist );
      $new_value = str_replace( "\r", '\r', $new_value );
      $new_value = str_replace( "\n", '\n', $new_value );
      $new_value = str_replace( "\t", '\t', $new_value );
      return $new_value;
   }

   # joins non-numeric key arrays and preserves their keys (primarily)
   function array_merge2 ( $array1, $array2, $ignore_keys = 0 ) {
      # created this function because PHP 4.3.0 screws up '$result = $array1 + $array2;'
      #  as it preserves the key values when merging non-numeric key arrays (stated in PHP Manual - array_merge)
      $new_array = array();
      if ( is_array($array1) ) {
         foreach ( $array1 as $key => $value ) {
            if ( !$ignore_keys ) {
               $new_array[$key] = $value;
            } else {
               $new_array[] = $value;
            }
         }
      }
      if ( is_array($array2) ) {
         foreach ( $array2 as $key => $value ) {
            if ( !$ignore_keys ) {
               $new_array[$key] = $value;
            } else {
               $new_array[] = $value;
            }
         }
      }

      return $new_array;
   }

   # appends numeric key arrays (primarily) - array_merge can be used instead of this if the renumbering of the numeric keys isnt important
   #  array_merge_recursive can possibly be used instead of this when keeping keys
   function array_append ( &$array1, $array2, $keep_keys = 0 ) {
      if ( is_array($array2) ) {
         foreach ( $array2 as $key => $value ) {
            if ( $keep_keys ) {
               $array1[$key] = $value;
            } else {
               $array1[] = $value;
            }
         }
      }
   }

   function associate_array_to_html_options ( $associate_array, $selected_item = array() ) {
      return array_to_html_options ( $associate_array, $selected_item );
   }

   function array_to_html_options ( $associate_array, $selected_item = array(), $use_only_keys_or_values = "", $case_sensitive = 1 ) {
      if ( !array_count($associate_array) ) return "";
      if ( preg_match( "/keys/i", $use_only_keys_or_values ) ) {
         # will only show the keys in the output
         foreach ( $associate_array as $key => $value ) {
            $associate_array1[$key] = '';
         }
         $associate_array = $associate_array1;
      } else if ( preg_match( "/values/i", $use_only_keys_or_values ) ) {
         # will only show the values in the output
         foreach ( $associate_array as $key => $value ) {
            $associate_array1[$value] = '';
         }
         $associate_array = $associate_array1;
      }

      if ( $selected_item == NULL ) {
         $selected_item = array();
      } else if ( !is_array($selected_item) ) {
         $selected_item = array( $selected_item );
      }

      if ( $associate_array ) {
         $case_insensitive_select_items = "||" . join( "||", $selected_item ) . "||";
         foreach ( $associate_array as $value => $text ) {
            if ( preg_match( "/^\[?-GROUP-START-(\w+-)?\]?$/i", $value ) ) {
               $html_output .= "<optgroup label=\"$text\"> ";
            } else if ( preg_match( "/^\[?-GROUP-END-(\w+-)?\]?$/i", $value ) ) {
               $html_output .= "</optgroup> ";
            } else {
               $html_output .= "<option";
               if ( preg_match( "/^\[?-NO-VALUE-(\w+-)?\]?$/i", $value ) ) {
                  $html_output .= " value=''";
               } else if ( strval($value) === strval($text) OR $text == "" ) {
                  # used === as ( "1" == "01" ) returns true
                  $text = $value;
               } else {
                  $html_output .= " value=\"$value\"";
               }
   
               if ( in_array( $value, $selected_item ) ) {
                  $html_output .= " selected";
               } else if ( !$case_sensitive AND preg_match( "/\|\|$value\|\|/i", $case_insensitive_select_items ) ) {
                  $html_output .= " selected";
               }
   
               $html_output .= ">$text</option> ";
            }
         }
      }
      
      return $html_output;
   }

   function in_array_case ( $string, $array ) {
      # case insensitive version of in_array but doesnt work on multi-dimensional arrays
      if ( preg_match( "/\x7f\x7f$string\x7f\x7f/i", "\x7f\x7f" . join( "\x7f\x7f", $array ) . "\x7f\x7f" ) ) {
         return true;
      }
      return false;
   }

   function array_count( $array ) {
      # built this because count() isnt a 100% array counting function
      if ( is_array($array) AND count($array) ) {
         return count( $array );
      } else {
         return 0;
      }
   }

   function array_flip2 ( $array, $value_to_assign = "" ) {
      # exchanges the values for their keys and leaves their values blank or to a given value
		foreach ( $array as $key => $value ) {
			$associate_array[$value] = $value_to_assign;
		}
		return $associate_array;
   }

	function fix_array_for_html_options ( $array ) {
	   return array_flip2( $array );
	}

//   # PHP Manual is_array - < elanthis at awesomeplay dot com > [24-May-2007 11:25] with !== correction
//   function is_vector( &$array ) { 
//      if ( !is_array($array) || empty($array) ) { 
//         return -1; 
//      } 
//      $next = 0; 
//      foreach ( $array as $k => $v ) { 
//         if ( $k !== $next ) return false; 
//         $next++; 
//      } 
//      return true; 
//   }

   # PHP Manual is_array - < elanthis at awesomeplay dot com > [24-May-2007 11:25] with !== correction
   #  alex frase [16-Jul-2008 08:05] was good also but slightly slower
   #  < angelo [at] mandato <dot> com > - uses array_merge
   function is_assoc( &$array, $ignore_data_type = 0, $next = 0 ) {
      # $next lets you start the sequential array check from a number other than 0
      #  but this is more suitable for the is_vector function
      if ( !is_array($array) OR empty($array) ) {
         return -1;
      }
      if ( $ignore_data_type ) {
         foreach ( $array as $k => $v ) {
            if ( strval($k) != strval($next) ) return true;
            $next++;
         }
      } else {
         foreach ( $array as $k => $v ) {
            if ( $k !== $next ) return true;
            $next++;
         }
      }
      return false;
   }

   # sorts multi-dimentsional associate array
   #  alternative method (usort) - http://www.the-art-of-web.com/php/sortarray/
   function sort_multi_array ( $array, $fields, $sort = SORT_REGULAR, $append_array_key = 0 ) {
      # need to add sort by field value (zero pad the field or use natsort()) or field length (zero pad this also)
      if ( !is_array($array) OR count($array) == 0 ) return $array;

      $field_array = explode( ",", $fields );
      foreach ( $array as $key => $array1 ) {
         $key_name = "";
         foreach ( $field_array as $field_name ) {
            if ( $key_name ) {
               $key_name .= "_" . $array1[$field_name];
            } else {
               $key_name .= $array1[$field_name];
            }
         }
         if ( $append_array_key ) {
            $key_name .= $array1[$key];
         }
         $array2[$key_name] = $array1;
         $array2a[$key_name] = $key;
      }

      ksort( $array2, $sort );
      if ( is_assoc($array) ) {
         # if the multi-dimensional array was a root associated array
         foreach ( $array2 as $key => $array3 ) {
            $array4[$array2a[$key_name]] = $array3;
         }
      } else {
         foreach ( $array2 as $array3 ) {
            $array4[] = $array3;
         }
      }

      return $array4;
   }

   # added key_column to make it similar to http://snippets.bigtoach.com/snippet/array_pivot/
   # - possibly do some error checking to make sure that column_name and key_column are actual keys
   function array_column ( $array, $column_name, $key_column = "" ) {
      if ( !is_array($array) ) return false;

      $return_array = array();
      if ( $key_column ) {
         foreach ( $array as $record ) {
            $return_array[$record[$key_column]] = $record[$column_name];
         }
      } else {
         foreach ( $array as $record ) {
            $return_array[] = $record[$column_name];
         }
      }

      return $return_array;
   }

   # ------------------------- Numeric Functions -------------------------

   # http://www.weberdev.com/get_example-1509.html
   #  convert numbers to roman numerals

   # ------------------------- Currency Functions -------------------------

   function currency_format ( $number, $display_format = 0, $decimals = 2, $dec_point = ".", $thousand_sep = "," ) {
      $number = number_format( $number, $decimals, $dec_point, $thousand_sep );

      if ( $display_format == 1 ) {
         # elimiates trailing 0s and the decimal if necessary
         $number = preg_replace( "/\.?0+$/", "", $number );
      } else if ( $display_format == 2 ) {
         # eliminates only trailing decimal point and trailing zeros
         $number = preg_replace( "/\.0+$/", "", $number );
      } else {
         # doesnt remove the fraction
      }

      return $number;
   }

   # fixed up version of shorten_numbers2() found in misc_subs2.php
   function currency_short ( $number, $show_title = 0, $decimals = 2, $thresh_hold = 9 ) {
      # $thresh_hold makes it possible to bump numbers to its next thousand level. ex - 900 turns to 0.9K
      if ( $number >= ($thresh_hold * 100000000) ) {
         $number /= 1000000000;
         $decimal_place = "B";
         $title_tip = "Billion";
      } else if ( $number >= ($thresh_hold * 100000) ) {
         $number /= 1000000;
         $decimal_place = "M";
         $title_tip = "Million";
      } else if ( $number >= ($thresh_hold * 100) ) {
         $number /= 1000;
         $decimal_place = "K";
         $title_tip = "Thousand";
      }
      $number = currency_format( $number, 1, $decimals );

      if ( $show_title ) {
         $decimal_place = "<a title='$title_tip'>$decimal_place</a>";
         # abbr not supported by IE 6
         #$decimal_place = "<abbr title='$title_tip'>$decimal_place</a>";
      }

      return $number . $decimal_place;
   }

   function two_decimals_new ( $number, $show_2_decimals_if_fraction = 0, $leave_as_is = 0 ) {
      if ( $show_2_decimals_if_fraction ) {
         return currency_format( $number, 2 );
      } else if ( $leave_as_is ) {
         return currency_format( $number );
      } else {
         return currency_format( $number, 1 );
      }
//      $number = number_format( $number, 2, ".", "," );
//      if ( $leave_as_is ) {
//         # doesnt remove the fraction
//      } else if ( $show_2_decimals_if_fraction ) {
//         # eliminates only trailing '.00'
//         $number = preg_replace( "/\.0+$/", "", $number );
//      } else {
//         # elimiates trailing 0s and the decimal if necessary
//         $number = preg_replace( "/\.?0+$/", "", $number );
//      }
//      return $number;
   }

   # ------------------------- String Functions -------------------------

   // add the chopping in the middle of a string like in
   //  http://aidanlister.com/repos/v/function.str_chop.php
	function shorten_string ( $string, $size_to_shorten_to, $crop_length = 0, $add_dots = 1 ) {
	   if ( strlen($string) > $size_to_shorten_to ) {
	      if ( !$crop_length ) {
   	      if ( $add_dots ) {
   		      $string = preg_replace( "/,?\s+[\w\(]*$/", "", substr( $string, 0, $size_to_shorten_to-2 ) ) . "...";
   		   } else {
   		      $string = preg_replace( "/,?\s+[\w\(]*$/", "", substr( $string, 0, $size_to_shorten_to ) );
   		   }
	      } else {
	         # crops the string at the given length and only removes punctuations and empty space at the end
   	      if ( $add_dots ) {
   		      $string = preg_replace( "/[\,\(\s]*$/", "", substr( $string, 0, $size_to_shorten_to-2 ) ) . "...";
   		   } else {
   		      $string = preg_replace( "/[\,\(\s]*$/", "", substr( $string, 0, $size_to_shorten_to ) );
   		   }
	      }
		}
		return $string;
	}

   // this could come in handy http://aidanlister.com/repos/v/function.str_rand.php
   function random_password( $n=8, $chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890' ) {
      # http://www.phpfreaks.com/quickcode/Random_Password_Generator/56.php
      srand( (double)microtime() * 1000000 );
      $m = strlen( $chars );
      while( $n-- ) { 
         $x .= substr( $chars, rand()%$m, 1 ); 
      }  

      return $x;  
   }

   function scramble_word($word) {
      # http://www.php.net/str_shuffle - jojersztajner at OXYGEN dot POLAND - 16-Jun-2007 03:27
      if (strlen($word) < 2)
         return $word;
      else
         return $word{0} . str_shuffle(substr($word, 1, -1)) . $word{strlen($word) - 1};
   }

   function my_ucwords ( $str, $is_name=false, $capitalize_no_vowel_words = false ) {
      # http://www.php.net/manual/en/function.ucwords.php - 24-Dec-2005 07:34
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
          $all_lowercase .= '|At|The|On|For|An|With';
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
         $str = preg_replace("/\\b([bcdfghjklmnpqrstvwxz1234567890]{1,4})\\b/ie", "strtoupper('$1')", $str);
      }

      # Philipz Inc. - Capitalize commonly abbreviated words
      $common_upcase_short_words = array( "CEO", "PA", "IP", "ID", "IDs", "URL", "UID" );
      foreach ( $common_upcase_short_words as $value ) {
         $str = preg_replace( "/\b" . $value . "\b/i", $value, $str );
      }

      # Philipz Inc. - Capitalize abbreviations with dots
      $str = preg_replace( "/\b(([a-z]\.)+)\b/i", "strtoupper('$1')", $str );

      return $str;
   }

   # Consider using sentence_cap from < http://www.php.net/manual/en/function.ucfirst.php > by adefoor at gmail dot com
   # Need to expand this function so that it understands words that need to be capitalized like days of the week, month
   #  of the year, 'I', and proper nouns as suggested by Uwe at < http://www.php.net/manual/en/function.ucfirst.php >
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

   function file_print_replace ( $file_name, $replace_entries, $print_output = "" ) {
      $file_name_contents = file_get_contents ( $file_name );
      
      $total_entries = count( $replace_entries ) / 2;

      for ( $entry_number = 0; $entry_number < $total_entries; $entry_number++ ) {
         $to_find = $replace_entries[ ( $entry_number * 2 ) ];
         $to_replace = $replace_entries[ ( $entry_number * 2 ) + 1 ];
      
         $file_name_contents = str_replace( $to_find, $to_replace, $file_name_contents );
      }
      
      if ( $print_output ) {
         print $file_name_contents;
      } else {
         return $file_name_contents;
      }
   }

   # http://www.php.net/strtr - anonymous at hotmail dot com
   #  an updated version of this can be found at http://phpsnippets.wordpress.com/page/2/
   function remove_accents($string) {
      return strtr($string, "ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ", "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");
   }

   # http://www.php.net/strtr - < troelskn at gmail dot com > [23-Jan-2008 03:39]
   #  I modified some of it
   function transcribe_cp1252_to_latin1( $cp1252 ) {
      $conversion_array = array(
      "\x80" => "[euro]",  "\x81" => " ",    "\x82" => "'", "\x83" => 'f',
      "\x84" => '"',  "\x85" => "...",  "\x86" => "+", "\x87" => "#",
      "\x88" => "^",  "\x89" => "0/00", "\x8A" => "S", "\x8B" => "<",
      "\x8C" => "OE", "\x8D" => " ",    "\x8E" => "Z", "\x8F" => " ",
      "\x90" => " ",  "\x91" => "`",    "\x92" => "'", "\x93" => '"',
      "\x94" => '"',  "\x95" => "*",    "\x96" => "-", "\x97" => "--",
      "\x98" => "~",  "\x99" => "[tm]", "\x9A" => "s", "\x9B" => ">",
      "\x9C" => "oe", "\x9D" => " ",    "\x9E" => "z", "\x9F" => "Y" );
      
      return strtr( $cp1252, $conversion_array );
   }

   # zerofill strings to a particular length
   function zeros_add ( $string, $number_of_zeros ) {
      return substr( str_repeat("0",$number_of_zeros) . $string, -$number_of_zeros );
   }

   # remove zerofill 0's from strings
   function zeros_del ( $string ) {
      return preg_replace( "/^0+/", "", $string );
   }

   # had to add these as i hated having to go back to the syntax to check how these are to be done
   function substr_left ( $string, $chars ) {
      return substr( $string, 0, $chars );
   }
   function substr_right ( $string, $chars ) {
      return substr( $string, -$chars );
   }

   function variables_export( $variable_array, $format = 1, $separator = "=" ) {
      if ( $format == 1 ) {
         # outputs like this key=value
         foreach ( $variable_array as $key => $value ) {
            if ( strval($value) != "" ) {
               #$new_value = my_escape_string( $value );
               $new_value = str_replace( array("\r", "\n", "\t"), array('\r', '\n', '\t'), $value );
               $output[] = "$key$separator$new_value";
            }
         }
      }

      return join( "\r\n", $output );
   }

   function variables_import( $variables, $return_array = array(), $separator = "=" ) {
      $variable_lines = preg_split( "/\r\n/", $variables );
      foreach ( $variable_lines as $variable_line ) {
         preg_match( "/^([^=]+?)(\s?$separator\s?)(.+)/is", $variable_line, $preg_results );
         # need to add routines for handing variable imports like whats in the map db using eval()
         $return_array[$preg_results[1]] = $preg_results[3];
      }

      return $return_array;
   }

   function trim_full ( $string, $execute_type = 0 ) {
      # removes multiple white space in the middle of a string in addition to triming it from the ends
      if ( $execute_type ) {
         # removes only spaces and tabs
         return preg_replace( "/[ \t]+/", " ", trim($string) );
      } else {
         # removes all white space including \r, \n, etc.
         return preg_replace( "/\s+/", " ", trim($string) );
      }
   }

   # PHP base64_encode - Tom (06-Dec-2006 06:20)
   function base64_url_encode ( $plainText ) {
       $base64 = base64_encode($plainText);
       $base64url = strtr($base64, '+/', '-_');
       return $base64url;
   }

   # ------------------------- Date / Time Functions -------------------------

   function convert_date ( $date, $new_format, $gmt = 0 ) {
      # strtotime uses GNU formats from http://www.gnu.org/software/tar/manual/html_node/tar_113.html
      # acceptable dates are 1972-09-24; 24 September 1972; Sep 24, 1972; 09 Jan 2008
      # acceptable times are 20:02, 8:02pm, 20:02-0500
      # best formats include - 2008-01-17 08:09:39; 17 Jan 2008 11:09:39 +0300
      # accepted date-times - 2/22/2008 3:16:26 PM; 
      global $debug;

      if ( preg_match( "/^(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2}:\d{2})(?:\.\d+\-\+)?(.*)$/", $date, $preg_results ) ) {
         # Handling the RFC 3339 format - ex. 2003-12-13T18:30:02-05:00, 2003-12-13T18:30:02Z
         $preg_results[3] = str_replace( ":", "", $preg_results[3] );
         $date = $preg_results[1] . " " . $preg_results[2] . " " . $preg_results[3];
      }
      # 06/22/08, 

      if ( $debug ) print $date . "<br>\r\n";
      if ( $debug ) print "Format - " . $new_format . "<br>\r\n";
	   $new_time = @strtotime( $date );
	   if ( $new_time == FALSE ) {
	      print "Error Handling Date - $date<br>\r\n";
	   } else if ( !$new_format ) {
	      print "Error With Format - Format is blank<br>\r\n";
	   }
	   if ( $gmt ) {
   		$new_date = gmdate( $new_format, $new_time );
	   } else {
		   $new_date = date( $new_format, $new_time );
		}
		return $new_date;
	}

   # taken from php manual microtime help page
   #  < alreece45 at yahoo dot com > (03-Jun-2007 12:36) has benchmark
   function microtime_float () {
      list($usec, $sec) = explode( " ", microtime() );
      return ((float)$usec + (float)$sec);
   }

   function timer_start () {
      global $timer_start_time;
      $timer_start_time = microtime_float();
      return $timer_start_time;
   }

   function timer_stop ( $timer_time_start = 0, $decimals = 4, $output = "seconds" ) {
      global $timer_start_time, $timer_end_time, $timer_time;
      $timer_end_time = microtime_float();
      if ( $timer_time_start ) {
         $timer_time = $timer_end_time - $timer_time_start;
      } else {
         $timer_time = $timer_end_time - $timer_start_time;
      }
      if ( $output == "milli" ) {
         $timer_time *= 1000;
      } else if ( $output == "micro" ) {
         $timer_time *= 1000000;
      }
      $timer_time = number_format( $timer_time, $decimals );
      return $timer_time;
   }

   function time_convert_24_to_12_hours ( $string_of_24_hours ) {
      $string_segments = preg_split( "/\s+/", $string_of_24_hours );
      foreach ( $string_segments as $x => $hours_24 ) {
         preg_match( "/(\d{1,2})[:\.](\d{1,2})/", $hours_24, $preg_results );
         if ( $preg_results ) {
            $am_or_pm = "AM";
            if ( $preg_results[1] < 12 ) {
               $preg_results[1] = ( $preg_results[1] == 0 ) ? "12" : $preg_results[1]+0;
            } else {
               $preg_results[1] = ( $preg_results[1] == "12" ) ? "12" : $preg_results[1]-12;
               $am_or_pm = "PM";
            }
            $string_segments[$x] = $preg_results[1] . ":" . $preg_results[2] . $am_or_pm;
         }
      }

      return join( " ", $string_segments );
   }

   function time_convert_12_to_24_hours ( $string_of_24_hours ) {
      $string_of_24_hours = preg_replace( "/(\d)\s+([ap]m)/i", "$1$2", $string_of_24_hours );
      $string_segments = preg_split( "/\s+/", $string_of_24_hours );
      foreach ( $string_segments as $x => $hours_24 ) {
         preg_match( "/(\d{1,2})[:\.](\d{1,2})((?:[:\.]\d{1,2})?)([ap]m)?(\*)?/i", $hours_24, $preg_results );
         if ( $preg_results ) {
            if ( strlen($preg_results[1]) == 1 ) $preg_results[1] = "0" . $preg_results[1];
            if ( strlen($preg_results[2]) == 1 ) $preg_results[2] = "0" . $preg_results[2];
            if ( strlen($preg_results[3]) == 2 ) $preg_results[3] = substr_replace( $preg_results[3], "0", 1, 0 );
            if ( preg_match( "/am/i", $preg_results[4] ) ) {
               if ( $preg_results[1] == "12" ) $preg_results[1] = "00";
               $string_segments[$x] = $preg_results[1] . ":" . $preg_results[2] . $preg_results[3] . $preg_results[5];
            } else {
               if ( $preg_results[1] == "12" ) $preg_results[1] = "00";
               $string_segments[$x] = ($preg_results[1] + 12) . ":" . $preg_results[2] . $preg_results[3] . $preg_results[5];
            }
         }
      }

      return join( " ", $string_segments );
   }

   # ------------------------- Captcha Functions -------------------------

   # check http://www.php.net/gd
   #  links
   #   http://captchas.net/sample/php/
   #   http://freshmeat.net/p/captchaphp
   #   http://www.phpcaptcha.org/ (seems to be quite good)
   #   http://www.white-hat-web-design.co.uk/articles/php-captcha.php
   #   http://recaptcha.net/plugins/php/ (have to signup)

   # ------------------------- Others Functions -------------------------

   // Alt. - http://www.midorijs.com/php2json.php.txt
   // PHP Manual - json_encode < php at koterov dot ru > (16-Feb-2007 10:43)
   function php2js($a)
   {
       if (is_null($a)) return 'null';
       if ($a === false) return 'false';
       if ($a === true) return 'true';
       if (is_scalar($a)) {
           $a = addslashes($a);
           $a = str_replace("\n", '\n', $a);
           $a = str_replace("\r", '\r', $a);
           $a = preg_replace('{(</)(script)}i', "$1'+'$2", $a);
           return "'$a'";
       }
       $isList = true;
       for ($i=0, reset($a); $i<count($a); $i++, next($a))
           if (key($a) !== $i) { $isList = false; break; }
       $result = array();
       if ($isList) {
           foreach ($a as $v) $result[] = php2js($v);
           return '[ ' . join(', ', $result) . ' ]';
       } else {
           foreach ($a as $k=>$v) 
               $result[] = php2js($k) . ': ' . php2js($v);
           return '{ ' . join(', ', $result) . ' }';
       }
   }

   # PHP extension, PHP-JSON - json_decode() OR PEAR library, JSON-PHP require_once('JSON.php');
   # http://gggeek.altervista.org/sw/article_20061113.html
   function parse_ajax ( $text_string, $id_name = 0, $id_name_alternatives = array() ) {
      # tested on
      #  http://www.apple.com/trailers/home/feeds/most_pop.json
      #  
      preg_match( "/\"?(\w*)\"?\[(.+)\]/is", $text_string, $fields_and_values );
      $fields_and_values[2] = preg_replace( "/\"(\w+)\":(\w+)/", "'$1' => \"$2\"", $fields_and_values[2] );
      $fields_and_values[2] = preg_replace( "/\"(\w+)\":\"([^\"]+)\"/", "'$1' => \"$2\"", $fields_and_values[2] );
      $fields_and_values[2] = preg_replace( "/\{([^\}\{]+?)\}/", "array( $1 )", $fields_and_values[2] );
      $fields_and_values[2] = preg_replace( "/\"(\w+)\":\[([^\]\[].+?)\]/", "'$1' => array( $2 )", $fields_and_values[2] );
      $fields_and_values[2] = preg_replace( "/array\( (array\( .+? \)) \)/", "$1", $fields_and_values[2] );
      $fields_and_values[2] = preg_replace( "/\{([^\}\{]+?)\}/", "array( $1 )", $fields_and_values[2] );
      $fields_and_values[2] = preg_replace( "/:\[(array\( .+? \))\]/s", "=> $1", $fields_and_values[2] );
      $fields_and_values[2] = preg_replace( "/:array/", "=> array", $fields_and_values[2] );
      #$fields_and_values[1] = preg_replace( "/\"(\w+)\":\[(.+?)\]/", "'$1' => array( $2 )", $fields_and_values[1] );
      #$fields_and_values[1] = preg_replace( "/\"(\w+)\":\{(.+?)\}/", "'$1' => array( $2 )", $fields_and_values[1] );
      #$fields_and_values[1] = preg_replace( "/\"(\w+)\":\{(.+?)\}/", "'$1' => array( $2 )", $fields_and_values[1] );
      #$fields_and_values[1] = preg_replace( "/\"(\w+)\":\{(.+?)\}/", "'$1' => array( $2 )", $fields_and_values[1] );
      eval( "\$output_array = array( " . $fields_and_values[2] . " );" );
      
//      $fields_and_values_array = explode( "},{", preg_replace( "/^{(.+)}$/is", "$1", $fields_and_values[1] ) );
//      foreach ( $fields_and_values_array as $value ) {
//         $assoc_array = array();
//         while ( preg_match( "/\"(\w+)\":\"(.+?)\"|\"(\w+)\":(\d+)|\"(\w+)\":\[(.+?)\]/is", $value, $preg_results ) ) {
//            $value = str_replace( $preg_results[0], "", $value );
//            if ( $preg_results[1] ) {
//               # a string
//               $assoc_array[$preg_results[1]] = $preg_results[2];
//            } else if ( $preg_results[3] ) {
//               # a number
//               $assoc_array[$preg_results[3]] = $preg_results[4];
//            } else {
//               # an array
//               $assoc_array[$preg_results[5]] = explode( '","', preg_replace( "/^\"(.+)\"$/is", "$1", $preg_results[6] ) );
//            }
//         }
//         if ( $id_name ) {
//            if ( count($id_name_alternatives) ) {
//               $output_array[$assoc_array[$id_name_alternatives[0]]] = $assoc_array[$id_name_alternatives[1]];
//            } else {
//               $output_array[$assoc_array['id']] = $assoc_array['name'];
//            }
//         } else {
//            $output_array[] = $assoc_array;
//         }
//      }

      return $output_array;
   }

# taken from http://www.prolifique.com/entities.php.txt
// Much simpler UTF-8-ness checker using a regular expression created by the W3C:
// Returns true if $string is valid UTF-8 and false otherwise.
// From http://w3.org/International/questions/qa-forms-utf-8.html
function isUTF8($str) {
   return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]           # ASCII
       | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
       | \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
       | \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       | \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
       | \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
   )*$%xs', $str);
  }

   function http_no_cache () {
      # No Caching
      // Date in the past
      header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
      // always modified
      header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
      // HTTP/1.1
      header("Cache-Control: no-store, no-cache, must-revalidate");
      header("Cache-Control: post-check=0, pre-check=0", false);
      // HTTP/1.0
      header("Pragma: no-cache");
   }

   # http://www.whatsmyip.org/mod_gzip_test/
   # http://www.php.net/ob_gzhandler
   # http://httpd.apache.org/docs/2.0/mod/mod_deflate.html
   # http://www.addedbytes.com/php/php-gzip-and-htaccess/
   # http://www.tellinya.com/read/2007/09/09/106.html
   function gzip_webpage () {
      if ( substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') ) {
         ob_start( "ob_gzhandler" );
      }
   }

   function gzip_output ( $output ) {
      if ( substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') ) {
         if ( !headers_sent() ) {
            header('Content-Encoding: gzip');
         }

         // http://www.php.net/gzcompress < @boas.anthro.mnsu.edu > 26-Sep-2000 11:30 - great explaination
         // http://www.php.net/ob_gzhandler < daijoubuNOSP at Mvideotron dot ca > 27-Oct-2003 02:05
         //  which in turn was taken from phpBB's page_tail.php
         $gzip_size        = ob_get_length(); 
         $gzip_contents    = ob_get_clean(); // PHP < 4.3 use ob_get_contents() + ob_end_clean() 

         echo "\x1f\x8b\x08\x00\x00\x00\x00\x00", 
            substr(gzcompress($gzip_contents, 3), 0, - 4), // substr -4 isn't needed 
            pack('V', crc32($gzip_contents)),    // crc32 and 
            pack('V', $gzip_size);               // size are ignored by all the browsers i have tested 
      }
   }

   # zip functionality
   #  http://zziplib.sourceforge.net/zip-php.html
   #  http://pecl.php.net/package/zip 
   #  http://pear.php.net/package/Archive_Zip / http://articles.techrepublic.com.com/5100-10878_11-6125204.html
   #  http://www.weberdev.com/get_example-4066.html / http://www.phpclasses.org/browse/file/3631.html
   #  http://blog.jimmyr.com/Zip_a_File_with_PHP_14_2008.php
   #  http://news.softpedia.com/news/A-Simple-PHP-Script-to-Create-ZIP-Archives-on-Windows-80871.shtml
   #  http://www.phpclasses.org/browse/file/9524.html
   #  On Unix - $cmd = `zip -r $zipname *`;

   # http://www.php.net/manual/en/function.http-build-query.php - < mqchen at gmail dot com > 03-Feb-2007 01:27
   # added $question_mark and $append_separator variables and its related modifications
   # added the 
   # if(!function_exists('http_build_query')) {
   function http_build_query_new($data,$prefix=null,$sep='',$key='',$question_mark=0,$append_separator=0,$current_url=0) {
      $ret = array();
      foreach((array)$data as $k => $v) {
         $k = urlencode($k);
         if(is_int($k) && $prefix != null) {
            # changed this line to be equivalent to < flyingmeteor at gmail dot com >
            $k = urlencode($prefix).$k;
         }
         if(!empty($key)) {
            # added the below line to fix 'array[0]=' to 'array[]=' if array is a numeric array
            if ( !is_assoc($v) ) $k = "";
            $k = $key."%5B".$k."%5D"; # "[".$k."]"
         }
         
         if(is_array($v) || is_object($v)) {
           array_push($ret,http_build_query_new($v,"",$sep,$k));
         } else {
            # added the if so that blank entries dont show up
            $urlencode_v = urlencode($v);
            if ( $urlencode_v ) array_push($ret,$k."=".$urlencode_v);
         }
      }
      if(empty($sep)) {
         $sep = ini_get("arg_separator.output");
      }
      $return_data = implode($sep, $ret);
      if ( $append_separator AND $return_data ) $return_data .= $sep;
      if ( ($question_mark OR $current_url) AND $return_data ) $return_data = "?" . $return_data;
      if ( $current_url ) {
         if ( $current_url == 1 ) {
            preg_match( "/([^\/]+)$/i", $_SERVER['PHP_SELF'], $results );
            $return_data = $results[1] . $return_data;
         } else if ( $current_url == 2 ) {
            preg_match( "/([^\/]+)$/i", $_SERVER['REQUEST_URI'], $results );
            $return_data = $results[1] . $return_data;
         } else {
            $return_data = $current_url . $return_data;
         }
      }
      return $return_data;
   }

   # parse_url - Michael Muryn (27-Aug-2007 04:51)
   #  opposite to parse_url
   function glue_url ( $parsed ) {
      if (!is_array($parsed)) return false;
      $uri = isset($parsed['scheme']) ? $parsed['scheme'].':'.((strtolower($parsed['scheme']) == 'mailto') ? '' : '//') : '';
      $uri .= isset($parsed['user']) ? $parsed['user'].(isset($parsed['pass']) ? ':'.$parsed['pass'] : '').'@' : '';
      $uri .= isset($parsed['host']) ? $parsed['host'] : '';
      $uri .= isset($parsed['port']) ? ':'.$parsed['port'] : '';
      if(isset($parsed['path']))
      {
          $uri .= (substr($parsed['path'], 0, 1) == '/') ? $parsed['path'] : ('/'.$parsed['path']);
      }
      $uri .= isset($parsed['query']) ? '?'.$parsed['query'] : '';
      $uri .= isset($parsed['fragment']) ? '#'.$parsed['fragment'] : '';
      return $uri;
   }

   function http_build_rewrite_url ( $query_data, $rewrite_keys = "", $rewrite_base_url = "", $query_data_rewrite_values = "" ) {
      foreach ( $rewrite_keys as $key ) {
         if ( is_array($query_data_rewrite_values[$key]) ) {
            $rewrite_string_parts[$key] = $query_data_rewrite_values[$key][$query_data[$key]];
         } else {
            $rewrite_string_parts[$key] = $query_data[$key];
         }
         if ( !$rewrite_string_parts[$key] ) {
            $rewrite_string_parts[$key] = "-";
         }
      }
      
      $rewrite_string = $rewrite_base_url . join( "/", $rewrite_string_parts ) . "/";
      $rewrite_string = preg_replace( "/\/(-\/)+$/", "/", $rewrite_string );

      return $rewrite_string;
   }

   function rewrite_link ( $title, $id = "", $rewrite_style = "", $separator_char = "_", $replace_regexp = "/\W+/" ) {
      if ( $separator_char ) $preg_replace_separator_char = preg_quote( $separator_char, "/" ) . "*";
      if ( !$rewrite_style ) {
         $rewrite_string = preg_replace( "/^$preg_replace_separator_char(.+?)$preg_replace_separator_char$/", "$1", preg_replace( $replace_regexp, $separator_char, $title ) );
         if ( $id ) {
            $rewrite_string .= "$separator_char$id.html";
         }
      } else if ( $rewrite_style == "id_first" ) {
         $rewrite_string = preg_replace( "/^" . preg_quote( $separator_char, "/" ) . "*(.+?)" . preg_quote( $separator_char, "/" ) . "*$/", "$1", preg_replace( $replace_regexp, $separator_char, $title ) );
         if ( $id ) {
            $rewrite_string = "$id$separator_char$rewrite_string.html";
         }
      } else if ( $rewrite_style == "category_path" ) {
         $replace_regexp = "/[^\w\/\\\]+/";
         $rewrite_string = preg_replace( "/^" . preg_quote( $separator_char, "/" ) . "*(.+?)" . preg_quote( $separator_char, "/" ) . "*$/", "$1", preg_replace( $replace_regexp, $separator_char, $title ) );
         $rewrite_string = str_replace( "/", "|", $rewrite_string );
         $rewrite_string = str_replace( "\\", "/", $rewrite_string );
      } else if ( $rewrite_style == "category_path_new" ) {
         $replace_regexp = "/[^\w\/\\\]+/";
         $rewrite_string = preg_replace( "/^" . preg_quote( $separator_char, "/" ) . "*(.+?)" . preg_quote( $separator_char, "/" ) . "*$/", "$1", preg_replace( $replace_regexp, $separator_char, $title ) );
         $rewrite_string = str_replace( "/", "|", $rewrite_string );
         $rewrite_string = str_replace( "\\", "-", $rewrite_string );
      }
      return $rewrite_string;
   }

   # Browser and OS Detect - $_SERVER['HTTP_USER_AGENT']
   # http://apptools.com/phptools/browser/source.php
   # http://lab.amanwithapencil.com/user_agent/
   # http://snipplr.com/view/1018/browser-detection/
   # http://drupal.org/node/282546
   # http://www.phpclasses.org/browse/package/2669.html
   # http://www.robert-gonzalez.com/2007/04/09/php-browseros-tests/
?>
