<?php
   $sql_debug = 0;

   function goto_url ( $url_to_got_to ) {
      print "<script language=\"javascript\">
<!-- 
 location.replace(\"$url_to_got_to\");
//-->
</script>";
      exit;
   }

   if(!function_exists('array_to_php')) {
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
               $php_string .= "'$key' => " . preg_replace( "/;$/", ", ", array_to_php( $value ) );
            } else {
               $php_string .= "'$key' => \"" . addcslashes( $value, '"' ) . "\", ";
            }
            #}
         }
         $php_string = preg_replace( "/, $/", "", $php_string );
      }
      $php_string .= " );";
      
      return $php_string;
   }
   }

   function array_merge2 ( $array1, $array2 ) {
      # created this function because PHP 4.3.0 screws up '$result = $array1 + $array2;'
      $new_array = array();
      if ( is_array($array1) ) {
         foreach ( $array1 as $key => $value ) {
            $new_array[$key] = $value;
         }
      }
      if ( is_array($array2) ) {
         foreach ( $array2 as $key => $value ) {
            $new_array[$key] = $value;
         }
      }

      return $new_array;
   }

   function array_to_html_options ( $associate_array, $selected_item = array(), $use_only_keys_or_values = "" ) {
      if ( preg_match( "/keys/i", $use_only_keys_or_values ) ) {
         # will only show the keys in the output
         foreach ( $associate_array as $key => $value ) {
            $associate_array1[$key] = '';
         }
         $associate_array = $associate_array1;
      } else if ( preg_match( "/values/i", $use_only_keys_or_values ) ) {
         # will only show the valuses in the output
         foreach ( $associate_array as $key => $value ) {
            $associate_array1[$value] = '';
         }
         $associate_array = $associate_array1;
      }

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

            $html_output .= ">$text</option>";
         }
      }
      
      return $html_output;
   }

   function mysql_create_select_statement ( $table, $fields = "*", $select_addons = array() ) {
      if ( is_array($fields) ) {
         $sql_statement = "SELECT " . join( ", ", $fields ) . " FROM $table";
      } else {
         $sql_statement = "SELECT $fields FROM $table";
      }

		if ( !is_array($select_addons) ) {
		   # incase this is sent as a text string SQL statement addon
         $sql_statement .= " " . $select_addons;
		} else {
         if ( is_array($select_addons['where']) ) {
            $sql_statement .= mysql_build_where_clause( $select_addons['where'] );
         } else if ( $select_addons['where'] ) {
            $sql_statement .= " WHERE " . $select_addons['where'];
         }
         if ( $select_addons['order'] ) {
            $sql_statement .= " ORDER BY " . $select_addons['order'];
         } else if ( $select_addons['sort'] ) {
            $sql_statement .= " ORDER BY " . $select_addons['sort'];
         }
         if ( $select_addons['limit'] ) {
            $sql_statement .= " LIMIT " . $select_addons['limit'];
         }
      }

      return $sql_statement;
   }

   function mysql_create_associate_array ( $connect_id, $table, $fields, $select_addons = array(), $format = "", $multi_dimension = 0, $text_separator = " - " ) {
      global $sql_debug;

      if ( !$connect_id OR !$fields ) return 0;

	   if ( !is_array($fields) ) {
			$fields = preg_split( "/,\s*/", $fields );
		}
		if ( count($fields) < 2 ) return 0;

      $sql_statement = mysql_create_select_statement( $table, $fields, $select_addons );

      $sql_result = mysql_query( $sql_statement, $connect_id );
      if ( $sql_debug ) {
         mysql_show_sql_statement( $sql_statement, $sql_result );
      }

      if ( $sql_result ) {
         if ( $sql_result == 1 ) {
            return 1;
         } else if ( !mysql_num_rows($sql_result) ) {
            # Confirms that the SQL was executed correctly
            return 1;
         }

         while( $sql_record = mysql_fetch_assoc($sql_result) ) {
            $variable_name = array_shift( $sql_record );
            if ( $multi_dimension ) {
               # the outputted associated array will have be multi-dimensional
               # [1231] => array( [name] => Spiderman [timing] 1:24 )
               $return_array[$variable_name] = $sql_record;
            } else {
         		if ( !$format ) {
                  $return_array[$variable_name] = join( $text_separator, $sql_record );
         		} else {
                  # format the string by replacing the [field_name] entries
         		   $formatted_value = $format;
         		   foreach ( $sql_record as $key => $value ) {
         		      $formatted_value = str_replace( "[" . $key . "]", $value, $formatted_value );
         		   }
                  $return_array[$variable_name] = $formatted_value;
         		}
            }
         }
         return $return_array;
      }
      return 0;
   }
   
   function mysql_show_sql_statement ( $sql_statement, $sql_result = "" ) {
      print "SQL - $sql_statement<br>\r\nRESULT - $sql_result<br>\r\n";
   }

   function mysql_build_where ( $where_fields, $and_or = "AND" ) {
      # builds a where string surrounded by brackets with the keys and values in an array
      if ( is_array($where_fields) ) {
         foreach ( $where_fields as $field_name_and_expression => $field_value ) {
            preg_match( "/^([^\s]+)\s*(.+)?\s*$/", $field_name_and_expression, $matches );
            if ( count($matches) == 2 ) {
               $where_clause[] = "$matches[1] = '" . mysql_escape_string($field_value) . "'";
            } else if ( $field_value ) {
               $where_clause[] = "$matches[1] $matches[2] '" . mysql_escape_string($field_value) . "'";
            } else {
               $where_clause[] = $field_name_and_expression;
            }
         }

         return "( " . join( " " . $and_or . " ", $where_clause ) . " )";
      }

      return "";
   }

   function mysql_build_where_field_array ( $field_name, $field_values, $and_or = "OR", $reg_exp = array() ) {
      # builds a where string surrounded by brackets with a field name and an array of values
      if ( is_array($field_values) ) {
         if ( !$and_or ) $and_or = "OR";
         foreach ( $field_values as $field_value ) {
            preg_match( "/^([^\s]+)\s*(.+)?\s*$/", $field_name, $matches );
            if ( count($matches) == 2 ) {
               $where_clause[] = "$matches[1] = '" . mysql_real_escape_string($field_value) . "'";
            } else if ( $field_value ) {
               $where_clause[] = "$matches[1] $matches[2] '$reg_exp[start]" . mysql_escape_string($field_value) . "$reg_exp[end]'";
            }
         }

         return "( " . join( " " . $and_or . " ", $where_clause ) . " )";
      }
   }

   function build_category_paths ( $category_records, $category_separator = "||", $add_indents = 0, $additional_options = array() ) {
      if ( !count($category_records) ) return;
      $id_name_field = "id";
      $parent_id_field_name = "parent_id";
      $name_field_name = "name";
      $sort_field_name = "sort";
      if ( !$category_separator OR $add_indents ) $category_separator = "||";
      $sort_with_field = 1;
      $sort_characters = 6;

      if ( $additional_options['use_codes'] ) {
         $id_name_field = "code";
         $parent_id_field_name = "parent_code";
      }

      if ( is_array($additional_options['alternative_field_names']) ) {
         # incase the records array has different field names
         if ( $additional_options['alternative_field_names']['id'] ) $id_name_field = $additional_options['alternative_field_names']['id'];
         if ( $additional_options['alternative_field_names']['parent_id'] ) $parent_id_field_name = $additional_options['alternative_field_names']['parent_id'];
         if ( $additional_options['alternative_field_names']['name'] ) $name_field_name = $additional_options['alternative_field_names']['name'];
      }

      if ( $additional_options['improper_record_format'] ) {
         if ( $additional_options['improper_record_format'] == "with_parent_id" ) {
            # incase the records are formatted as [] => array( [id] = "", [parent_id] = "", [name] = "" )
            foreach ( $category_records as $record ) {
               $new_category_records[$record[$id_name_field]] = array( $parent_id_field_name => $record[$parent_id_field_name], $name_field_name => $record[$name_field_name] );
            }
            $category_records = $new_category_records;
         } else if ( $additional_options['improper_record_format'] == "single_dimension" ) {
            # incase the records are formatted as [id] => "name"
            foreach ( $category_records as $key => $value ) {
               $new_category_records[$key] = array( $name_field_name => $value );
            }
            $category_records = $new_category_records;
         }
      }

      if ( is_array($additional_options['build_id_with_parent']) ) {
         # incase the outputted path ids should be created using parent_id+category_id

         $id_separator = $additional_options['build_id_with_parent']['id_separator'];
         foreach ( $category_records as $key => $value_array ) {
            $parent_id_array[$key] = $value_array[$parent_id_field_name];
         }

         foreach ( $category_records as $key => $record ) {
            if ( $parent_id_array[$key] != "" ) {
               if ( $parent_id_array2[$parent_id_array[$key]] ) {
                  $new_category_records[$parent_id_array2[$parent_id_array[$key]] . $id_separator . $key] = $record;
               } else {
                  $key_path = $parent_id_array[$key];
                  $current_key = $parent_id_array[$key_path];
                  if ( is_array($category_records[$key_path]) ) {
                     while ( $current_key != "" AND $category_records[$current_key] ) {
                        $key_path = $current_key . $id_separator . $key_path;
                        $current_key = $parent_id_array[$current_key];
                     }
                     $record[$parent_id_field_name] = $key_path;
                     $new_category_records[$key_path . $id_separator . $key] = $record;
                     $parent_id_array2[$parent_id_array[$key]] = $key_path;
                  } else {
                     $new_category_records[$key] = $record;
                     $parent_id_array2[$current_key] = "";
                  }
               }
//               $current_key = $parent_id_array[$key];
//               $key_path = $key;
//               do {
//                  $key_path = $current_key . $id_separator . $key_path;
//                  $current_key = $parent_id_array[$current_key];
//               } while ( $current_key != "" );
//               $new_category_records[$key_path] = $record;
            }
         }
         $category_records = $new_category_records;
      }

      if ( $additional_options['build_parent_from_id'] ) $additional_options['build_parent_id'] = $additional_options['build_parent_from_id'];
      if ( $additional_options['build_parent_id'] ) {
         # incase the parent_id is not provided and can be build from the category_id
         if ( !is_array( $additional_options['build_parent_id']) ) {
            # removes the ending characters separated by a dash
            $preg_pattern = "/-?\w+$/";
            $preg_replace = "";
         } else {
            $preg_pattern = $additional_options['build_parent_id']['pattern'];
            $preg_replace = $additional_options['build_parent_id']['replace'];
         }

         foreach ( $category_records as $key => $record ) {
            if ( $additional_options['overwrite_parent_id'] ) $category_records[$key][$parent_id_field_name] = "";

            if ( $category_records[$key][$parent_id_field_name] AND $category_records[$category_records[$key][$parent_id_field_name]] ) {
               # use the already set parent id
            } else {
               # looks for the parent record id in the array by removing the preg_pattern by loops
               $loop_x = 0; $key1 = $key;
               do {
                  $key1 = preg_replace( $preg_pattern, $preg_replace, $key1 );
                  $loop_x++;
               } while ( !$category_records[$key1] AND $loop_x < 2 );
               if ( $category_records[$key1] ) {
                  $category_records[$key][$parent_id_field_name] = $key1;
               }
            }
         }
      }

      # builds category paths from an multi-dimensional array
      # [id] => array( [parent_id] => '', [name] => '' )
      foreach ( $category_records as $id => $value ) {
         if ( !$paths[$value[$parent_id_field_name]] ) {
            $original_parent_id = $value[$parent_id_field_name];
            $path = array();
            do {
               if ( $sort_with_field ) {
                  $value[$sort_field_name] = substr( "000000" . $value[$sort_field_name], -$sort_characters );
                  $value[$name_field_name] = "[" . $value[$sort_field_name] . "]" . $value[$name_field_name];
               }

               array_unshift( $path, $value[$name_field_name] );
               $parent_id = $value[$parent_id_field_name];
               $value = $category_records[$parent_id];
            } while ( $parent_id != NULL AND is_array($value) );
            # will stop if a category doesnt have a parent category record or else when it reaches the root
   
            $paths[$id] = str_replace( " ", "\xff", join( $category_separator, $path ) );

            if ( $original_parent_id AND !$paths[$original_parent_id] ) {
               # Adds the current IDs parent ID path to the array which helps dramatically if the categories are not sorted by parent_id
               array_pop( $path );
               if ( count($path) ) {
                  $paths[$original_parent_id] = join( $category_separator, $path );
               }
            }
         } else {
            if ( $sort_with_field ) {
               $value[$sort_field_name] = substr( "000000" . $value[$sort_field_name], -$sort_characters );
               $value[$name_field_name] = "[" . $value[$sort_field_name] . "]" . $value[$name_field_name];
            }
            $paths[$id] = str_replace( " ", "\xff", $paths[$value[$parent_id_field_name]] . $category_separator . $value[$name_field_name] );
         }
      }

      if ( $additional_options['alpha_sort'] == "N" ) {
         foreach ( $paths as $key => $value ) {
            if ( !preg_match( "/".preg_quote($category_separator,'/')."/", $value ) ) {
               unset( $paths[$key] );
               $new_paths[$key] = $value;
               foreach ( $paths as $key1 => $value1 ) {
                  if ( preg_match( "/^".preg_quote($value.$category_separator,'/')."/", $value1 ) ) {
                     $new_paths[$key1] = $value1;
                     unset( $paths[$key1] );
                  }
               }
            }
         }
         $new_paths = $paths;
      } else {
         asort( $paths );
      }

      foreach ( $paths as $key => $value ) {
         $value = preg_replace( "/\[\d{" . $sort_characters . "}\]/", "", $value );
         $value = str_replace( "\xff", " ", $value );
         if ( $add_indents ) {
            $number_of_indents = explode( $category_separator, $value );
   
            $value = end($number_of_indents);
            if ( (count($number_of_indents)-1) > 0 ) {
               $value = str_repeat( "&#8212;", (count($number_of_indents)-1) ) . " " . $value;
            }
         }
         $paths[$key] = $value;
      }
      
      return $paths;
   }

?>
