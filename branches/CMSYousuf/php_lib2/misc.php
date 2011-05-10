<?php
   $FORM = array_merge( $_GET, $_POST );
  
   function validate_email ( $email_address ) {
      if ( !eregi( "^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,6}$", $email_address ) ) {
         # simple test - ^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$
   	   # Alternative taken from James Socol's checkemail.php
   	   # if ( !ereg("^[a-zA-Z0-9][a-zA-Z0-9_\.\-]*[@][a-zA-Z0-9\.\-]*[\.][a-zA-Z]{2,4}$", $email_address ) ) {
         return FALSE;
      }
      return TRUE;
      # max domain extension is 6 (.museum, .travel) - http://en.wikipedia.org/wiki/GTLD
      # country domain extions - http://www.webopedia.com/quick_ref/topleveldomains/countrycodeA-E.asp
   }
   
   function file_print_replace ( $file_name, $replace_entries, $print_output = "" ) {
      $file_name_contents = file_get_contents ( $file_name );
      
      $total_entries = count( $replace_entries ) / 2;

      for ( $entry_number = 0; $entry_number < $total_entries; $entry_number++ ) {
         $to_find = $replace_entries[ ( $entry_number * 2 ) ];
         $to_replace = $replace_entries[ ( $entry_number * 2 ) + 1 ];
      
         $file_name_contents = str_replace( $to_find, $to_replace, $file_name_contents );
      }
      
      if ( $print_output != "" ) {
         return ( $file_name_contents );
      } else {
         print $file_name_contents;
      }
   }
   
   function current_file_name () {
      preg_match( "/([^\/]+)$/i", $_SERVER['PHP_SELF'], $results );
      return $results[1];
   }

   function associate_array_to_html_options ( $associate_array, $selected_item = array() ) {
      if ( $selected_item == NULL ) {
         $selected_item = array( );
      } else if ( !is_array($selected_item) ) {
         $selected_item = array( $selected_item );
      }

      if ( $associate_array ) {
         foreach ( $associate_array as $value => $text ) {
            $html_output .= "<option";
            if ( preg_match( "/^\[-NO-VALUE-(\w+-)?\]$/i", $value ) ) {
               $html_output .= " value=''";
            } else if ( strval($value) == strval($text) OR $text == "" ) {
               $text = $value;
            } else {
               $html_output .= " value=\"$value\"";
            }

            if ( in_array( $value, $selected_item ) ) {
               $html_output .= " selected";
            }

            $html_output .= ">$text</option> ";
         }
      }
      
      return $html_output;
   }

   function array_flip2 ( $array, $value_to_assign = "" ) {
      # exchanges the values for their keys and leaves their values blank
		foreach ( $array as $key => $value ) {
			$associate_array[$value] = $value_to_assign;
		}
		return $associate_array;
   }

	function fix_array_for_html_options ( $array ) {
	   return array_flip2( $array );
	}

   function convert_date ( $date, $new_format ) {
	   $new_time = strtotime( $date );
		$new_date = date( $new_format, $new_time );
		return $new_date;
	}

   if(!function_exists('array_to_php')) {
   # precausion incase it conflicts with subs_new.php
   function array_to_php ( $array ) {
      $php_string = "array( ";
      if ( count($array) ) {
         foreach ( $array as $key => $value ) {
            #if ( intval($key) == $key ) {
            #   print "$key => \"value\",
            #} else {
            if ( is_array($value) ) {
               #$php_string .= "array( ";
               #foreach ( $value as $key1 => $value1 ) {
               #   $php_string .= "'$key1' => \"$value1\", ";
               #}
               #$php_string = preg_replace( "/, $/", "", $php_string );
               #$php_string .= " ), ";
               # above wont work with multi-dimensional arrays, so the below does the recursion
               $php_string .= "'".mysql_real_escape_string($key)."' => " . preg_replace( "/;$/", ", ", array_to_php( $value ) );
            } else {
               $php_string .= "'".mysql_real_escape_string($key)."' => \"" . mysql_real_escape_string( $value ) . "\", ";
               #$php_string .= "'$key' => \"" . addcslashes( $value, '"' ) . "\", ";
            }
            #}
         }
         $php_string = preg_replace( "/, $/", "", $php_string );
      }
      $php_string .= " );";
      
      return $php_string;
   }
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

   function print_r2 ( $variable, $exit = 0, $no_htmlentities = 0 ) {
      # if php5, headers_list() can be used to determine if text or html format has been defined as the output
      print "<pre>";
      print ( $no_htmlentities ) ? print_r( $variable, 1 ) : htmlentities( print_r( $variable, 1 ) );
      print "</pre>";
      if ( $exit ) {
         exit;
      }
   }

   function use_cache_file ( $cached_file, $cache_period ) {
      if ( file_exists($cached_file) ) {
         if ( (filemtime($cached_file) + $cache_period) > time() ) {
            return true;
         }
      }
      return false;
   }

   function var_cache ( $variable ) {
      # shorten version of cache_variable
      if ( is_string($variable) ) {
         return "\"" . addcslashes( $variable, '\\"' ) . "\";";
      } else if ( is_numeric($variable) ) {
         return "$variable;";
      } else if ( is_array($variable) ) {
         return array_to_php($variable);
      }
   }

?>
