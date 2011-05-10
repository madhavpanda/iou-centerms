<?php
   #print mysql_error();
   $sql_errors = 0;
   $sql_debug = 0;
   $sql_test = 0;
   $sql_last_query_time = 0;

   function mysql_start ( $server = "localhost", $database = "", $username = "root", $password = "" ) {
      global $sql_errors, $sql_debug, $mysql_version;

      $connect_id = @mysql_connect( $server, $username, $password );
      if ( $connect_id ) {
         $mysql_version = floatval( mysql_version() );
         if ( $database ) {
            if ( mysql_select_db( $database, $connect_id ) ) {
               return $connect_id;
            } else {
               # Wasnt able to select the database
               if ( $sql_errors OR $sql_debug ) print "<b>Error:</b> Unable to connect to [$database] database.<br>\r\n";
               return $connect_id;
            }
         } else {
            return $connect_id;
         }
      }
      if ( $sql_errors OR $sql_debug ) print "<b>Error:</b> Unable to connect to [$server] database server.<br>\r\n";
      return 0;
   }

   function mysql_stop ( $connect_id = 0 ) {
      if ( $connect_id ) {
         return mysql_close( $connect_id );
      } else {
         return mysql_close();
      }
   }

   function mysql_change_db ( $connect_id, $database ) {
      global $sql_errors, $sql_debug;

      if ( !$database ) return;
      if ( $connect_id ) {
         $successful = mysql_select_db( $database, $connect_id );
      } else {
         $successful = mysql_select_db( $database );
      }
      if ( $successful ) {
         return 1;
      } else {
         if ( $sql_errors ) print "<b>Error:</b> Unable to connect to [$database] database.<br>\r\n";
         return 0;
      }
   }

   function mysql_change_db_if_diff ( $connect_id, $db_name ) {
      $current_db = mysql_current_databse( $connect_id );
      if ( $current_db != $db_name ) {
         mysql_change_db( $connect_id, $db_name );
         return $current_db;
      }
      return "";
   }

   function mysql_execute_sql ( $connect_id, $sql_statement, $return_single_record_or_field = 0, $return_sql_result = 0 ) {
      global $sql_errors, $sql_debug, $sql_test, $sql_last_query_time;
//echo $sql_statement;
      if ( !$sql_statement ) return;
      #$sql_statement = preg_replace( "/\r|\n/", " ", $sql_statement );
      if ( !$sql_test ) {
         if ( $connect_id ) {
            $time_start = microtime_float();
            $sql_result = mysql_query( $sql_statement, $connect_id );
            $time_end = microtime_float();
         } else {
            $time_start = microtime_float();
            $sql_result = mysql_query( $sql_statement );
            $time_end = microtime_float();
         }
      }
      $sql_last_query_time = number_format( $time_end - $time_start, 4 );
      if ( $sql_debug OR $sql_test ) mysql_show_sql_statement( $sql_statement, $sql_result, mysql_error() );

      if ( $return_sql_result ) return $sql_result;

      if ( $sql_result ) {
         if ( $sql_result == 1 ) {
            # SQL statements that dont return results return true or false
            return 1;
         } else if ( !mysql_num_rows($sql_result) ) {
            # Confirms that the SQL was executed correctly but no results were returned
            #return 1;
            return array();
         }
         $x = 0;
         while( $sql_record = mysql_fetch_assoc($sql_result) ) {
            $x++;
            $return_array[$x] = $sql_record;
         }
         if ( $x == 1 AND $return_single_record_or_field ) {
            if ( count($return_array[1]) == 1 ) {
               $return_array = current( $return_array[1] );
            } else {
               $return_array = $return_array[1];
            }
         }
         
         return $return_array;
      }
      return 0;
   }

   function mysql_execute_sql_quick ( $connect_id, $sql_statement ) {
      # executed from within other functions in order to eliminate unnecessary error checking
      return mysql_execute_sql( $connect_id, $sql_statement, 0, 1 );
//      global $sql_errors, $sql_debug;
//
//      if ( $connect_id ) {
//         $sql_result = mysql_query( $sql_statement, $connect_id );
//      } else {
//         $sql_result = mysql_query( $sql_statement );
//      }
//      if ( $sql_debug ) mysql_show_sql_statement( $sql_statement, $sql_result, mysql_error() );
//
//      return $sql_result;
   }

   function mysql_add_record ( $connect_id, $table, $fields, $quick_insert = 0 ) {
      $sql_statement = "INSERT INTO $table ";
      $field_names = array_keys( $fields );

      if ( $field_names[0] != "0" AND !$quick_insert ) {
         #user has set the field names of the fields to be inserted
         $sql_statement .= "( `" . join( "`, `", $field_names ) . "` ) ";
      }

      foreach ( $fields as $key => $value ) {
         if ( is_null($value) ) {
            $field_value = "NULL";
         } else if ( is_integer($value) OR is_float($value) ) {
            $field_value = "$value";
         } else {
            $field_value = "'" . mysql_real_escape_string( $value ) . "'";
            if ( $field_value == "''" ) {
               $field_value = "NULL";
            }
         }
//         if ( $value != NULL AND $value != "" ) {
//            $field_value = "'" . mysql_real_escape_string( $value ) . "'";
//         } else {
//            $field_value = "NULL";
//         }
         $fields[$key] = $field_value;
      }
      $sql_statement .= "VALUES ( " . join( ", ", $fields ) . " )";

      return mysql_execute_sql_quick( $connect_id, $sql_statement );
   }

   function mysql_upd_record ( $connect_id, $table, $fields, $where_fields = "" ) {
      $sql_statement = "UPDATE $table SET ";
      if ( is_array($fields) ) {
         foreach ( $fields as $field_name => $field_value ) {
            $field_and_value = "`$field_name` = '" . mysql_real_escape_string($field_value) . "'";
            if ( $field_and_value != "`$field_name` = ''" ) {
               $update_fields[] = $field_and_value;
            } else {
               $update_fields[] = "`$field_name` = NULL";
            }
         }
         $sql_statement .= join( ", ", $update_fields );
      } else {
         $sql_statement .= $fields;
      }
      if ( is_array($where_fields) ) {
         $sql_statement .= mysql_build_where_clause( $where_fields );
      } else if ( $where_fields ) {
         $sql_statement .= " WHERE " . $where_fields;
      }

      return mysql_execute_sql_quick( $connect_id, $sql_statement );
   }

   function mysql_del_record ( $connect_id, $table, $where_fields = "" ) {
      $sql_statement = "DELETE FROM $table";
      if ( is_array($where_fields) ) {
         $sql_statement .= mysql_build_where_clause( $where_fields );
      } else if ( $where_fields ) {
         $sql_statement .= " WHERE " . $where_fields;
      }

      return mysql_execute_sql_quick( $connect_id, $sql_statement );
   }

   function determine_if_new_system ( $where_array ) {
      if ( !is_array($where_array) ) return 0;
      # determine if the mysql function was called using the new or old system
      $new_system_variables = array( "WHERE", "ORDER", "ORDER BY", "SORT", "SORT BY", "LIMIT", "GROUP", "GROUP BY" );
      foreach ( $new_system_variables as $variable ) {
         if ( isset($where_array[$variable]) ) {
            return 1;
         }
      }
      return 0;
   }

   function mysql_extract_record_id ( $connect_id, $table, $where_fields, $fields = "" ) {
      if ( determine_if_new_system($where_fields) ) {
         $sql_statement = mysql_create_select_statement( $table, $fields, $where_fields );
      } else {
         $sql_statement = mysql_create_select_statement( $table, $fields, array( 'WHERE' => $where_fields ) );
      }

      $sql_result = mysql_execute_sql_quick( $connect_id, $sql_statement );

      if ( !$sql_result ) {
         # likely error in SQL statements
         return false;
      } else if ( !mysql_num_rows($sql_result) ) {
         # Confirms that the SQL was executed correctly but no results were returned
         return 0;
      } else {
         return mysql_fetch_assoc( $sql_result );
      }
   }

   function mysql_extract_records_where ( $connect_id, $table, $where_fields = "", $fields = "", $order_by_field = "", $limit = "" ) {
      if ( determine_if_new_system($where_fields) AND !$order_by_field AND !$limit ) {
         $sql_statement = mysql_create_select_statement( $table, $fields, $where_fields );
      } else {
         $sql_statement = mysql_create_select_statement( $table, $fields, array( 'WHERE' => $where_fields, 'ORDER' => $order_by_field, 'LIMIT' => $limit ) );
      }

      $sql_result = mysql_execute_sql_quick( $connect_id, $sql_statement );

      if ( $sql_result ) {
         if ( !mysql_num_rows($sql_result) ) {
            # Confirms that the SQL was executed correctly but no results were returned
            #return 1;
            return array();
         } else {
            $x = 0;
            while( $sql_record = mysql_fetch_assoc($sql_result) ) {
               $x++;
               $return_array[$x] = $sql_record;
            }
			
            mysql_free_result( $sql_result );
            return $return_array;
         }
      }
      return 0;
   }

   function mysql_extract_column ( $connect_id, $table, $field_name, $where_fields = "", $order_by_field = "", $unique = 0 ) {
      if ( determine_if_new_system($where_fields) AND !$order_by_field AND !$unique ) {
         $sql_statement = mysql_create_select_statement( $table, $field_name, $where_fields );
      } else {
         $select_addons = array( 'WHERE' => $where_fields, 'ORDER BY' => $order_by_field );
         if ( $unique ) $select_addons['DISTINCT'] = '';
         $sql_statement = mysql_create_select_statement( $table, $field_name, $select_addons );
      }

      $sql_result = mysql_execute_sql_quick( $connect_id, $sql_statement );

      if ( $sql_result ) {
         if ( !mysql_num_rows($sql_result) ) {
            # Confirms that the SQL was executed correctly but no results were returned
            return array();
         } else {
            while( $sql_record = mysql_fetch_row($sql_result) ) {
               $column_result[] = $sql_record[0];
            }
            mysql_free_result( $sql_result );
            return $column_result;
         }
      }
      return 0;
   }

   function mysql_make_associate_array ( $connect_id, $table, $fields, $text_separator = " - " ) {
      return mysql_create_associate_array( $connect_id, $table, $fields, "", "", 0, $text_separator );
   }

   function mysql_make_associate_array2 ( $connect_id, $table, $fields, $where_fields = "", $order_by_field = "", $limit = "", $text_separator = " - " ) {
      return mysql_create_associate_array( $connect_id, $table, $fields, array( "WHERE" => $where_fields, "ORDER BY" => $order_by_field, "LIMIT" => $limit ), "", 0, $text_separator );
   }

   function mysql_make_assoc_array ( $connect_id, $table, $fields, $where_fields = "", $order_by_field = "", $limit = "", $text_separator = "" ) {
      # Returns a two-dimentional associated array
      return mysql_create_associate_array( $connect_id, $table, $fields, array( "WHERE" => $where_fields, "ORDER BY" => $order_by_field, "LIMIT" => $limit ), "", 1, $text_separator );
   }

   function mysql_count_query_records ( $connect_id, $table, $where_fields = "", $field_list = "" ) {
      if ( $field_list ) $field_list = ",".$field_list;
      if ( determine_if_new_system($where_fields) ) {
         # the LIMIT clause is useless for counting the total number of records
         unset( $where_fields['LIMIT'], $where_fields['ORDER BY'], $where_fields['ORDER'], $where_fields['SORT BY'], $where_fields['SORT'] );
         $sql_statement = mysql_create_select_statement( $table, "COUNT(*)" . $field_list, $where_fields );
      } else {
         $sql_statement = "SELECT COUNT(*)" . $field_list . " FROM $table";
         if ( is_array($where_fields) ) {
            $sql_statement .= mysql_build_where_clause( $where_fields );
         } else if ( $where_fields ) {
            $sql_statement .= " WHERE " . $where_fields;
         }
      }

      $sql_result = mysql_execute_sql_quick( $connect_id, $sql_statement );

      if ( $sql_result ) {
         if ( mysql_num_rows($sql_result) == 1 ) {
            $sql_record = mysql_fetch_assoc( $sql_result );
            return $sql_record['COUNT(*)'];
         } else {
            # if the sql result is not 1 row, that means GROUP was used in the SQL statement, so return the number of resulting rows
            return mysql_num_rows($sql_result);
         }
      }
      return 0;
   }

   function mysql_count_record_groups ( $connect_id, $table, $fields, $where_fields = "" ) {
      if ( is_array($fields) ) {
         $fields = join( ", ", $fields );
      }
      $sql_statement = "SELECT $fields, COUNT(*) as count_all_these_records FROM $table";
      if ( is_array($where_fields) ) {
         $sql_statement .= mysql_build_where_clause( $where_fields );
      } else if ( $where_fields ) {
         $sql_statement .= " WHERE " . $where_fields;
      }
      $sql_statement .= " GROUP BY $fields";
      $sql_statement .= " ORDER BY count_all_these_records";

      $sql_result = mysql_execute_sql_quick( $connect_id, $sql_statement );

      if ( $sql_result ) {
         if ( !preg_match( "/,/", $fields ) ) {
            # If there is only one field being counted then make the result an associated array
            while( $sql_record = mysql_fetch_assoc($sql_result) ) {
               $count_value = array_pop( $sql_record );
               $key = array_shift( $sql_record );
               $result[$key] = $count_value;
            }
            return $result;
         } else {
            $x = 0;
            while( $sql_record = mysql_fetch_assoc($sql_result) ) {
               $x++;
               $result[$x] = $sql_record;
            }
         }
         return $result;
      }

      return 0;
   }

   function mysql_count_table_records ( $connect_id, $database, $table ) {
      $sql_statement = "SHOW TABLE STATUS FROM $database";
      $sql_result = mysql_execute_sql_quick( $connect_id, $sql_statement );

      if ( $sql_result ) {
         while( $sql_record = mysql_fetch_assoc( $sql_result ) ) {
            if ( strtolower($sql_record['Name']) == strtolower($table) ) {
               return $sql_record['Rows'];
            }
         }
      }

      return 0;
   }

   function mysql_increment_field ( $connect_id, $table, $fields, $where_fields, $by = 1 ) {
      $sql_statement = "UPDATE $table SET ";
      # $field=$field+$by";
      if ( is_array($fields) ) {
         foreach ( $fields as $key => $value ) {
            if ( !$value ) $value = $by;
            $fieldz[$key] = "$key=$key+$value";
         }
      } else {
         $fieldz = preg_split( "/\s*,\s*/", $fields );
         foreach ( $fieldz as $key => $value ) {
            $fieldz[$key] = "$value=$value+$by";
         }
      }
      $sql_statement .= join( ", ", $fieldz );
      if ( is_array($where_fields) ) {
         $sql_statement .= mysql_build_where_clause( $where_fields );
      } else if ( $where_fields ) {
         $sql_statement .= " WHERE " . $where_fields;
      }

      return mysql_execute_sql_quick( $connect_id, $sql_statement );
   }

   function mysql_empty_table ( $connect_id, $table, $rest_auto_increment = 1 ) {
      global $mysql_version;
      if ( $reset_auto_increment ) {
         # Truncate is better than 'DELETE FORM $table' as truncate will reset auto_increment to 1
         mysql_query( "TRUNCATE TABLE $table", $connect_id );
      } else {
         if ( $mysql_version < 4.1 ) {
            # need to find a not null field in the table and delete using it
            # delete from keyword_search where `timestamp` is not null
         } else {
            # DELETE with leave auto increminate as is from MYSQL 4.1+
            mysql_query( "DELETE TABLE $table", $connect_id );
         }
      }
   }

   function mysql_add_records ( $connect_id, $table, $fields_array, $records_per_query = 100, $extra = array() ) {
      if ( !array_count($fields_array) ) return false;

      if ( $extra['insert_old'] ) return mysql_add_records_old( $connect_id, $table, $fields_array );

      $base_sql_statement = "INSERT INTO $table ";
      if ( $extra['insert_options'] ) {
         $extra['insert_options'] = strtoupper( $extra['insert_options'] );
         if ( $extra['insert_options'] == "IGNORE" ) {
            $base_sql_statement = "INSERT IGNORE INTO $table ";
         } else if ( $extra['insert_options'] == "REPLACE" ) {
            $base_sql_statement = "REPLACE INTO $table ";
         }
      }

      reset( $fields_array );
      $first_record = current( $fields_array );
      if ( is_array($first_record) ) {
         $field_names = array_keys( $first_record );
      } else {
         $field_names = array_keys( $table );
         $fields_array = array( 0 => $fields_array );
      }

      # check if each record in the array has all the same fields and in the same order
      foreach ( $fields_array as $key => $array ) {
         if ( $field_names != array_keys( $array ) ) {
            $record_fields_error++;
            $record_fields_error_records[$key] = $array;
         }
      }

      # add the non-bulk records one by one (as its possibly they all have different lengths) and then add the bulk ones
      if ( $record_fields_error ) {
         mysql_add_records_old( $connect_id, $table, $record_fields_error_records );
         foreach ( $record_fields_error_records as $key => $array ) {
            unset( $fields_array[$key] );
         }
      }

      $base_sql_statement .= "( `" . join( "`, `", $field_names ) . "` ) VALUES ";

      foreach ( $fields_array as $fields ) {
         $x++;
         foreach ( $fields as $key => $value ) {
            if ( is_null($value) ) {
               $field_value = "NULL";
            } else if ( is_integer($value) OR is_float($value) ) {
               $field_value = "$value";
            } else {
               $field_value = "'" . mysql_real_escape_string( $value ) . "'";
               if ( $field_value == "''" ) {
                  $field_value = "NULL";
               }
            }
            $fields[$key] = $field_value;
         }
         $add_records[] = "( " . join( ", ", $fields ) . " )";
         if ( $x == $records_per_query ) {
            $sql_statement = $base_sql_statement . join( ", ", $add_records );
            $return_value = mysql_execute_sql_quick( $connect_id, $sql_statement );
            $x = 0; $add_records = array();
         }
      }

      if ( $add_records ) {
         $sql_statement = $base_sql_statement . join( ", ", $add_records );
         $return_value = mysql_execute_sql_quick( $connect_id, $sql_statement );
      }

      return $return_value;
   }

   function mysql_add_records_old ( $connect_id, $table, $record_array, $quick_insert = 0 ) {
      foreach ( $record_array as $record ) {
         $return_value = mysql_add_record( $connect_id, $table, $record, $quick_insert );
      }

      return $return_value;
   }




   # Foreign Key Functions

   function mysql_get_foreign_key_list( $connect_id, $database, $table, $key_field, $display_fields, $where_clause = "", $order_by_field = "" ) {
      if ( $database ) {
         $current_db = mysql_current_databse( $connect_id );
         if ( $current_db != $database ) {
            mysql_change_db( $connect_id, $database );
         } else {
            $database = "";
         }
      }

      $key_field = strtolower( $key_field );
      if ( preg_match( "/field\[([^\]]+)\]/i", $display_fields ) ) {
         $display_fields_new = $display_fields;
         while ( preg_match( "/field\[([^\]]+)\]/i", $display_fields_new, $preg_results ) ) {
            $display_fields_new = str_replace( $preg_results[0], "", $display_fields_new );
            $field_list[] = strtolower( $preg_results[1] );
         }

         $fields_to_extract = join( ",", $field_list );
      } else {
         $field_list = preg_split( "/\s*,\s*/", $display_fields );
         $fields_to_extract = $display_fields;
         $text_separator = " - ";
      }
      if ( array_search($key_field,$field_list) === FALSE ) {
         $extract_keys = "$key_field,$fields_to_extract";
      } else {
         $extract_keys = $fields_to_extract;
         $disable_array_shift = 1;
      }

      $fkey_records = mysql_extract_records_where( $connect_id, $table, $where_clause, $extract_keys, $order_by_field );
      if ( $fkey_records ) {
         foreach ( $fkey_records as $value ) {
            if ( !$disable_array_shift ) {
               $record_value = array_shift( $value );
            } else {
               $record_value = $value[$key_field];
            }
            if ( $text_separator ) {
               $record_title = join( $text_separator, $value );
            } else {
               # Allows formatting of the $display_fields string
               $display_fields_new = $display_fields;
               while ( preg_match( "/field\[([^\]>]+)\]|field<([^>]+)>/i", $display_fields_new, $preg_results ) ) {
                  $preg_value = strtolower( $preg_results[1] );
                  $display_fields_new = str_replace( $preg_results[0], $value[$preg_value], $display_fields_new );
               }

               $bracket_types = array( "[" => "]", "<" => ">" );
               foreach ( $bracket_types as $open_bracket => $close_bracket ) {
                  $open_bracket = preg_quote( $open_bracket );
                  $close_bracket = preg_quote( $close_bracket );
                  $preg_query = "/(?:fk|fkey)" . $open_bracket . "([^\.]+)\.([^\.]+)\.([^=]+)=([^,]+), ([^" . $close_bracket . "]+)" . $close_bracket . "/i";
                  while ( preg_match( $preg_query, $display_fields_new, $preg_results ) ) {
                     # Usage   - fkey[database.table.field=value, fieldstodisplay]
                     # Example - fkey[general.city.code=DXB, 'field<name>-field<country_code>']
                     $preg_results[5] = preg_replace( "/^'/", "", $preg_results[5] );
                     $preg_results[5] = preg_replace( "/'$/", "", $preg_results[5] );
                     
                     $text_replace = mysql_get_foreign_key_text( $connect_id, $preg_results[1], $preg_results[2], array( $preg_results[3] => $preg_results[4] ), $preg_results[5] );
                     $display_fields_new = str_replace( $preg_results[0], $text_replace, $display_fields_new );
                  }
               }
               $record_title = $display_fields_new;
            }
            #$values_and_titles[] = $record_value . "=" . str_replace( ",", "&#44;", $record_title );
            $values_and_titles[$record_value] = $record_title;
         }
      } else {
         #print "#Error# No Results in FK List<br>\r\n";
      }

      if ( $database ) {
         mysql_change_db( $connect_id, $current_db );
      }

      if ( $values_and_titles ) {
         #return join( ",", $values_and_titles );
         return $values_and_titles;
      } else {
         return 0;
      }
   }

   function mysql_get_foreign_key_text( $connect_id, $database, $table, $key_field, $display_fields ) {
      if ( $database ) {
         $current_db = mysql_current_databse( $connect_id );
         if ( $current_db != $database ) {
            mysql_change_db( $connect_id, $database );
         } else {
            $database = "";
         }
      }

      if ( !is_array($key_field) ) {
         print "#Error# $key_field should be an array [Example - array( 'id' => 'snoop' )]";
      } else if ( current($key_field) ) {
         if ( preg_match( "/field\[([^\]]+)\]/i", $display_fields ) ) {
            $display_fields_new = $display_fields;
            while ( preg_match( "/field\[([^\]]+)\]/i", $display_fields_new, $preg_results ) ) {
               $display_fields_new = str_replace( $preg_results[0], "", $display_fields_new );
               $field_list[] = strtolower( $preg_results[1] );
            }
            $fields_to_extract = join( ",", $field_list );
         } else {
            $fields_to_extract = $display_fields;
            $text_separator = " - ";
         }

         $fkey_record = mysql_extract_record_id( $connect_id, $table, $key_field, $fields_to_extract );
         if ( $fkey_record ) {
            #$fkey_text = join( $text_separator, $fkey_record );
            if ( $text_separator ) {
               $fkey_text = join( $text_separator, $fkey_record );
            } else {
               # Allows formatting of the $display_fields string
               $display_fields_new = $display_fields;
               while ( preg_match( "/field\[([^\]]+)\]/i", $display_fields_new, $preg_results ) ) {
                  $preg_value = strtolower( $preg_results[1] );
                  $display_fields_new = str_replace( $preg_results[0], $fkey_record[$preg_value], $display_fields_new );
               }

               $bracket_types = array( "[" => "]", "<" => ">" );
               foreach ( $bracket_types as $open_bracket => $close_bracket ) {
                  $open_bracket = preg_quote( $open_bracket );
                  $close_bracket = preg_quote( $close_bracket );
                  $preg_query = "/(?:fk|fkey)" . $open_bracket . "([^\.]+)\.([^\.]+)\.([^=]+)=([^,]+), ([^" . $close_bracket . "]+)" . $close_bracket . "/i";
                  while ( preg_match( $preg_query, $display_fields_new, $preg_results ) ) {
                     # Usage   - fkey[database.table.field=value, fieldstodisplay]
                     # Example - fkey[general.city.code=DXB, 'field<name>-field<country_code>']
                     $preg_results[5] = preg_replace( "/^'/", "", $preg_results[5] );
                     $preg_results[5] = preg_replace( "/'$/", "", $preg_results[5] );
                  
                     $text_replace = mysql_get_foreign_key_text( $connect_id, $preg_results[1], $preg_results[2], array( $preg_results[3] => $preg_results[4] ), $preg_results[5] );
                     $display_fields_new = str_replace( $preg_results[0], $text_replace, $display_fields_new );
                  }
               }
               $fkey_text = str_replace( ",", "&#44;", $display_fields_new );
            }
         } else {
            $fkey_text = "FKey [ERROR]";
         }
      } else {
         $fkey_text = "[NULL]";
      }
      if ( $database ) {
         mysql_change_db( $connect_id, $current_db );
      }
      
      return $fkey_text;
   }








   # Extra Functions

   function mysql_table_structure ( $connect_id = "", $table, $lowercase_fields = 0, $only_field_names = 0 ) {
      if ( !$table ) return;
      #Also possible with 'SHOW FIELDS FROM '
      $sql_statement = "SHOW COLUMNS FROM " . $table;
      $sql_statement1 = "SHOW CREATE TABLE " . $table;
      if ( $connect_id ) {
         #mysql_execute_sql( $connect_id, $sql_statement, 0, 1 )
         $sql_result = mysql_query( $sql_statement, $connect_id );
         $sql_result1 = mysql_query( $sql_statement1, $connect_id );
      } else {
         $sql_result = mysql_query( $sql_statement );
         $sql_result1 = mysql_query( $sql_statement1 );
      }

      if ( $sql_result ) {
         while( $sql_record = mysql_fetch_assoc($sql_result) ) {
            $field_name = $sql_record['Field'];
            if ( $lowercase_fields ) $field_name = strtolower( $field_name );
            if ( $only_field_names ) {
               $table_structure[] = $field_name;
            } else {
               list( $field_type, $field_size, $field_others ) = mysql_field_type_extract( $sql_record['Type'] );
               $table_structure[$field_name]['type'] = $field_type;
               $table_structure[$field_name]['size'] = $field_size;
               $table_structure[$field_name]['others'] = $field_others;
               $table_structure[$field_name]['null'] = $sql_record['Null'];
               $table_structure[$field_name]['keys'] = $sql_record['Key'];
               $table_structure[$field_name]['default'] = $sql_record['Default'];
               $table_structure[$field_name]['extra'] = $sql_record['Extra'];
            }
         }
         if ( $only_field_names ) return $table_structure;

         # added this segment to identify which fields are labeled as UNIQUE KEY as they turn up as 'MUL' in the table 'keys' entry
         $table_sql = mysql_fetch_assoc( $sql_result1 );
         $table_fields = explode( "\n", $table_sql['Create Table'] );
         array_pop( $table_fields );
         array_shift( $table_fields );
         foreach ( $table_fields as $fields ) {
            if ( !preg_match( "/^`/", trim($fields) ) ) {
               # lines that dont have a field name start it
               if ( preg_match( "/^unique key `[^`]+` \(`([^`]+)`\)/i", trim($fields), $preg_results ) ) {
                  if ( $lowercase_fields ) $preg_results[1] = strtolower( $preg_results[1] );
                  $table_structure[$preg_results[1]]['keys'] = "UNI";
               }
            } else if ( preg_match( "/^`(\w+)` (.*?)[,|\)]?$/", trim($fields), $preg_results ) ) {
               $table_structure[$preg_results[1]]['sql'] = $preg_results[2];
            }
         }

         return $table_structure;
      }

      return false;
   }

   function mysql_field_structure ( $connect_id = 0, $table, $field ) {
      if ( !$table OR !$field ) return;
      $sql_statement = "SHOW COLUMNS FROM " . $table;
      if ( $connect_id ) {
         $sql_result = mysql_query( $sql_statement, $connect_id );
      } else {
         $sql_result = mysql_query( $sql_statement );
      }

      if ( $sql_result ) {
         while( $sql_record = mysql_fetch_assoc($sql_result) ) {
            if ( $sql_record["Field"] == $field ) {
               list( $field_type, $field_size, $field_others ) = mysql_field_type_extract( $sql_record['Type'] );
               $field_structure['type'] = $field_type;
               $field_structure['size'] = $field_size;
               $field_structure['others'] = $field_others;
               $field_structure['null'] = $sql_record['Null'];
               $field_structure['keys'] = $sql_record['Keys'];
               $field_structure['default'] = $sql_record['Default'];
               $field_structure['extra'] = $sql_record['Extra'];
               return $field_structure;
            }
         }
      }
      return 0;
   }

   function mysql_field_type_extract ( $field_type ) {
      if ( preg_match( "/^\w+\(/", $field_type ) ) {
         preg_match( "/^(\w+)\((.+)\) ?(.*)/", $field_type, $field_parts );
      } else {         
         preg_match( "/^(\w+)( )?(.*)/", $field_type, $field_parts );
         $field_parts[2] = "";
      }
      if ( preg_match( "/^enum|set$/i", $field_parts[1] ) ) {
         $field_parts[2] = preg_replace( "/^\'(.+)\'$/", "$1", $field_parts[2] );
         # Handles "\'" which shows as '' after query
         $field_parts[2] = str_replace( "''", "'", $field_parts[2] );
         # below makes an number associated associate array of the various enum or set values
         $field_parts[2] = explode( "','", "','" . $field_parts[2] );
         unset( $field_parts[2][0] );
      }
      if ( !$field_parts[2] ) {
         # Setting field size when its not defined
         if ( preg_match( "/^date$/i", $field_parts[1] ) ) {
            $field_parts[2] = "10";
         } else if ( preg_match( "/^tinyint$/i", $field_parts[1] ) ) {
            if ( preg_match( "/unsigned/i", $field_parts[3] ) ) {
               $field_parts[2] = "3";
            } else {
               $field_parts[2] = "4";
            }
         }
      }
      return array( $field_parts[1], $field_parts[2], $field_parts[3] );
   }

   function mysql_table_keys ( $connect_id = "", $table, $lowercase_fields = 0 ) {
      if ( !$table ) return;
      #Also possible with 'SHOW CREATE TABLE ' by extracting lines starting with (\w*\s*)KEY
      $sql_statement = "SHOW KEYS FROM " . $table;
      if ( $connect_id ) {
         $sql_result = mysql_query( $sql_statement, $connect_id );
      } else {
         $sql_result = mysql_query( $sql_statement );
      }

      if ( $sql_result ) {
         while( $sql_record = mysql_fetch_assoc($sql_result) ) {
            # name, type[pri,uni,ful], field_list[] = array( 'fieldname' => length );
            if ( !$table_keys[$sql_record['Key_name']] ) $table_keys[$sql_record['Key_name']] = array();
            if ( $sql_record['Key_name'] == "PRIMARY" ) {
               $table_keys[$sql_record['Key_name']]['type'] = "PRI";
            } else if ( $sql_record['Non_unique'] == "0" ) {
               $table_keys[$sql_record['Key_name']]['type'] = "UNI";
            } else if ( $sql_record['Comment'] == "FULLTEXT" ) {
               $table_keys[$sql_record['Key_name']]['type'] = "FUL";
            } else {
               $table_keys[$sql_record['Key_name']]['type'] = "KEY";
            }

            $column_name = $sql_record['Column_name'];
            if ( $sql_record['Sub_part'] AND !$sql_record['Comment'] != "FULLTEXT" ) {
               $column_name .= "[" . $sql_record['Sub_part'] . "]";
            }
            $table_keys[$sql_record['Key_name']]['fields'][$sql_record['Seq_in_index']] = $column_name;
         }

         return $table_keys;
      }

      return false;
   }

   function mysql_enum_field_values ( $connect_id = 0, $table, $field, $add_numbers = 0 ) {
      if ( !$table OR !$field ) return "";
      $extracted_field = mysql_field_structure( $connect_id, $table, $field );
      $enum_values = $extracted_field['size'];
      return $enum_values;
      #if ( $add_numbers ) {
      #   return $enum_values;
      #} else {
      #   return array_values( $enum_values );
      #}
   }

   function mysql_enum_field_to_html_options ( $connect_id, $table, $field, $selected = array(), $add_numbers = 0, $add_null = 0 ) {
      #if ( !$connect_id ) return;
      $extracted_field = mysql_field_structure( $connect_id, $table, $field, $add_numbers );
      $enum_values = $extracted_field['size'];

      if ( is_array($selected) ) {
         $selected_array = $selected;
      } else {
         # in order to be backward compatible with old scripts, but its best not to use this manner incase a value had a comma in it
         $selected_array = explode( ",", $selected );
      }

      if ( $extracted_field['null'] == "YES" AND $add_null ) {
         $html_output = "<option value=''></option>";
      }
      foreach ( $enum_values as $key => $text ) {
         if ( $add_numbers ) {
            if ( in_array( $text, $selected_array ) ) {
               $html_output .= "<option value=\"$key\" selected>$text";
            } else {
               $html_output .= "<option value=\"$key\">$text";
            }
         } else {
            if ( in_array( $text, $selected_array ) ) {
               $html_output .= "<option selected>$text";
            } else {
               $html_output .= "<option>$text";
            }
         }
         $html_output .= "</option>";
      }

      return $html_output;
   }

   function mysql_build_where_clause ( $where_fields ) {
      if ( !is_array($where_fields) ) {
         $where_clause = trim( $where_fields );
      } else {
         if ( count($where_fields) ) {
            if ( is_assoc($where_fields) ) {
               # Formatted as $array['username ='] = "yousuf";
               $where_clause = mysql_build_where( $where_fields, "AND", 1 );
            } else {
               # Formatted as $array( "VISIBLE = 'Y'", "ID = '1'" );
               $where_clause = join( " AND ", $where_fields );
            }
         }
      }
      if ( $where_clause ) {
         return " WHERE " . $where_clause;
      }
   }

   function mysql_current_databse ( $connect_id = "" ) {
      #if ( !$connect_id ) return;
      if ( $connect_id ) {
        $sql_result = mysql_query( "SELECT DATABASE()", $connect_id );
      } else {
        $sql_result = mysql_query( "SELECT DATABASE()" );
      }

      if ( $sql_result ) {
         $sql_record = mysql_fetch_assoc( $sql_result );
         return $sql_record['DATABASE()'];
      }

      if ( $sql_errors ) print "<b>Error:</b> Unable to determine current database [mysql_current_database].<br>\r\n";
      return 0;
   }

   function mysql_primary_key_field ( $table_structure ) {
      # assumes $table_structure is return from the mysql_table_structure function
      foreach ( $table_structure as $key => $value ) {
         if ( $value['keys'] == "PRI" OR $value['keys'] == "UNI" ) {
            return $key;
         }
      }

      return "";
   }

   function mysql_show_sql_statement ( $sql_statement, $sql_result = "", $mysql_error_message = "" ) {
      global $sql_test;

      if ( !$sql_test ) {
         if ( $mysql_error_message ) {
            print "SQL - $sql_statement<br>\r\nERROR - $mysql_error_message<br>\r\n";
         } else {
            print "SQL - $sql_statement<br>\r\nRESULT - $sql_result<br>\r\n";
         }
      } else {
         if ( $mysql_error_message ) {
            print "SQL - $sql_statement<br>\r\n";
         } else {
            print "SQL - $sql_statement<br>\r\n";
         }
      }
   }

   #SQL Queries
   #List Tables in DB - show table status from `mysql`
   #Give Table Structure Code - show create table `theemira_general`.`user_temp`
   # Get current DB - SELECT DATABASE();
   # Get tables in current DB - SHOW TABLES;
   # Get table structure - DESCRIBE pet;











   # Latest Additions

   function mysql_create_select_statement ( $table, $fields = "*", $select_addons = array() ) {
      $fields = ( !$fields ) ? "*" : $fields;
      $sql_statement = "SELECT ";
      if ( is_array($select_addons) AND (isset($select_addons['DISTINCT']) OR isset($select_addons['UNIQUE'])) ) $sql_statement .= "DISTINCT ";
      if ( is_array($select_addons) AND (isset($select_addons['FOUND_ROWS']) OR isset($select_addons['SQL_CALC_FOUND_ROWS'])) ) $sql_statement .= "SQL_CALC_FOUND_ROWS ";
      if ( is_array($fields) ) {
         $sql_statement .= join( ", ", $fields ) . " FROM $table";
      } else {
         $sql_statement .= "$fields FROM $table";
      }

		if ( !is_array($select_addons) ) {
		   # incase this is sent as a text string SQL statement addon
         $sql_statement .= " " . trim( $select_addons );
		} else {
		   # select structure - SELECT FROM JOIN WHERE GROUP HAVING ORDER LIMIT
		   #  http://dev.mysql.com/doc/refman/5.0/en/select.html
         if ( is_array($select_addons['JOIN']) ) {
            if ( count($select_addons['JOIN']) ) {
               # the array should be formated as ['JOIN_TYPE'], ['TABLE'], ['ON'] OR ['USING']
               # http://dev.mysql.com/doc/refman/5.0/en/join.html
               # http://en.wikipedia.org/wiki/Join_(SQL)
               # http://www.tizag.com/sqlTutorial/sqljoin.php
               #  table JOIN table1 ON ( table.field = table1.field )
               # USING links columns of the name in the two tables instead of having table1.id = table2.id
               #  table JOIN table1 USING ( id )
               # NATURAL JOIN automatically links up corresponding field names

               # alternative join means - SELECT table1.id, table2.name from table1,table2 where table1.id = table2.id
               print "Need to make this [mysql_create_select_statement] JOIN";
               exit;
            }
         } else if ( $select_addons['JOIN'] ) {
            # LEFT JOIN t2 ON (t1.a = t2.a) / LEFT JOIN t2 USING (a)
            $sql_statement .= " " . $select_addons['JOIN'];
         }
         if ( is_array($select_addons['WHERE']) ) {
            $sql_statement .= mysql_build_where_clause( $select_addons['WHERE'] );
         } else if ( $select_addons['WHERE'] ) {
            $sql_statement .= " WHERE " . $select_addons['WHERE'];
         } else if ( is_array($select_addons['where']) ) {
            $sql_statement .= mysql_build_where_clause( $select_addons['where'] );
         } else if ( $select_addons['where'] ) {
            $sql_statement .= " WHERE " . $select_addons['where'];
         }
         if ( $select_addons['GROUP'] ) {
            $sql_statement .= " GROUP BY " . $select_addons['GROUP'];
         } else if ( $select_addons['GROUP BY'] ) {
            $sql_statement .= " GROUP BY " . $select_addons['GROUP BY'];
         }
         if ( $select_addons['HAVING'] ) {
            $sql_statement .= " HAVING " . $select_addons['HAVING'];
         }
         if ( $select_addons['ORDER'] ) {
            $sql_statement .= " ORDER BY " . $select_addons['ORDER'];
         } else if ( $select_addons['ORDER BY'] ) {
            $sql_statement .= " ORDER BY " . $select_addons['ORDER BY'];
         } else if ( $select_addons['SORT'] ) {
            $sql_statement .= " ORDER BY " . $select_addons['SORT'];
         } else if ( $select_addons['SORT BY'] ) {
            $sql_statement .= " ORDER BY " . $select_addons['SORT BY'];
         }
         if ( $select_addons['LIMIT'] ) {
            $sql_statement .= " LIMIT " . $select_addons['LIMIT'];
         }
      }

      return $sql_statement;
   }

   function mysql_create_associate_array ( $connect_id, $table, $fields, $select_addons = array(), $format = "", $multi_dimension = 0, $text_separator = " - " ) {
      global $sql_debug;

      if ( !$table OR !$fields ) return false;

	   if ( !is_array($fields) ) {
			#preg_match_all( "/(\w+\([^\)]+\)(\s+as\s+`?\w+`?)?|(`?\w+`?)(\s+as\s+`?\w+`?)?)/", $fields, $preg_results, PREG_SET_ORDER );
			preg_match_all( "/\s*((\w+\([^\)]+\)(\s+as\s+`?\w+`?)?|((?:`?\w+`?\.)?`?\w+`?)(\s+as\s+`?\w+`?)?)|\*)\s*,/", $fields . ",", $preg_results, PREG_SET_ORDER );
			#print_r2( $preg_results );
			$fields = array();
			foreach ( $preg_results as $array ) {
			   $fields[] = $array[1];
			}
			#$fields = preg_split( "/,\s*/", $fields ); -- The Old
			if ( end($fields) == "*" ) {
			   # incase i want all the fields to be used and set the first initial field name to use as the keys in the array
			   array_pop( $fields );
			   $table_structure = mysql_table_structure( $connect_id, $table, 1, 1 );
			   foreach ( $fields as $field ) {
			      unset( $table_structure[array_search( $field, $table_structure )] );
			   }
			   foreach ( $table_structure as $field ) {
			      $fields[] = $field;
			   }
			}
		}
		if ( count($fields) < 2 ) return false;

      $sql_statement = mysql_create_select_statement( $table, $fields, $select_addons );

      if ( $connect_id ) {
         $sql_result = mysql_query( $sql_statement, $connect_id );
      } else {
         $sql_result = mysql_query( $sql_statement );
      }
      if ( $sql_debug ) mysql_show_sql_statement( $sql_statement, $sql_result, mysql_error() );

      if ( $sql_result ) {
         if ( $sql_result == 1 ) {
            #return 1;
            return array();
         } else if ( !mysql_num_rows($sql_result) ) {
            # Confirms that the SQL was executed correctly
            #return 1;
            return array();
         }

         while( $sql_record = mysql_fetch_assoc($sql_result) ) {
            $original_record = $sql_record;
            $variable_name = array_shift( $sql_record );
            if ( $multi_dimension ) {
               # the outputted associated array will be multi-dimensional
               # [1231] => array( [name] => Spiderman, [timing] => 1:24 )
               $return_array[$variable_name] = $sql_record;
            } else {
         		if ( !$format ) {
                  $return_array[$variable_name] = join( $text_separator, $sql_record );
         		} else {
                  # format the string by replacing the [field_name] entries
         		   $formatted_value = $format;
         		   foreach ( $original_record as $key => $value ) {
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

   function mysql_build_where ( $where_fields, $and_or = "AND", $no_brackets = 0 ) {
      # builds a where string surrounded by brackets with the keys and values in an array
      if ( is_array($where_fields) ) {
         foreach ( $where_fields as $field_name_and_expression => $field_value ) {
            preg_match( "/^([^\s]+)\s*(.+)?\s*$/", $field_name_and_expression, $matches );
            if ( count($matches) == 2 ) {
               #Executed as ["username"] = "yousuf"
               if ( preg_match( "/^MATCH\(/i", trim($matches[1]) ) ) {
                  $where_clause[] = "$matches[1] AGAINST('" . mysql_real_escape_string($field_value) . "')";
               } else {
                  $where_clause[] = "`$matches[1]` = '" . mysql_real_escape_string($field_value) . "'";
               }
            } else if ( strval($field_value) OR preg_match( "/^[!=<>]+$/", $matches[2] ) ) {
               #Executed as ["username !="] = "yousuf"
               if ( is_array($field_value) ) {
                  # http://dev.mysql.com/doc/refman/5.0/en/comparison-operators.html
                  # http://www.webdevelopersnotes.com/tutorials/sql/tutorial_mysql_in_and_between.php3
                  if ( preg_match( "/\bBETWEEN$/i", $matches[2] ) ) {
                     $first_value = mysql_real_escape_string( array_shift( $field_value ) );
                     $second_value = mysql_real_escape_string( array_shift( $field_value ) );
                     $where_clause[] = "`$matches[1]` $matches[2] $first_value AND $second_value";
                  } else if ( preg_match( "/\bIN$/i", $matches[2] ) ) {
                     foreach ( $field_value as $key => $value ) {
                        $field_value[$key] = "'" . mysql_real_escape_string($value) . "'";
                     }
                     $where_clause[] = "`$matches[1]` $matches[2] (" . join( ",", $field_value ) . ")";
                  }
               } else {
                  $where_clause[] = "`$matches[1]` $matches[2] '" . mysql_real_escape_string($field_value) . "'";
               }
            } else {
               #Formatted as $array["username IS NOT 'yousuf'"] = "";
               $where_clause[] = $field_name_and_expression;
            }
         }

         if ( count($where_clause) > 1 ) {
            $where_clause_text = join( " " . $and_or . " ", $where_clause );
            if ( !$no_brackets ) {
               $where_clause_text = "( " . $where_clause_text . " )";
            }
         } else {
            $where_clause[] = "";
            $where_clause_text = join( "", $where_clause );
         }
      }

      return $where_clause_text;
   }

   function mysql_build_where_field_array ( $field_name, $field_values, $and_or = "OR", $reg_exp = array(), $no_brackets = 0 ) {
      # builds a where string surrounded by brackets with a field name and an array of values
      if ( is_array($field_values) ) {
         if ( !$and_or ) $and_or = "OR";
         foreach ( $field_values as $field_value ) {
            preg_match( "/^([^\s]+)\s*(.+)?\s*$/", $field_name, $matches );
            if ( count($matches) == 2 ) {
               #Executed as ( "username", array( "yousuf", "mohammed", "ibrahim" );
               $where_clause[] = "$matches[1] = '" . mysql_real_escape_string($field_value) . "'";
            } else if ( $field_value ) {
               #Executed as ( "username regexp", array( "yousuf", "mohammed", "ibrahim" );
               $where_clause[] = "$matches[1] $matches[2] '$reg_exp[start]" . mysql_real_escape_string($field_value) . "$reg_exp[end]'";
            }
         }

         if ( count($where_clause) == 1 ) {
            $where_clause[] = "";
            $where_clause_text = join( "", $where_clause );
         } else if ( count($where_clause) > 1 ) {
            $where_clause_text = join( " " . $and_or . " ", $where_clause );
            if ( !$no_brackets ) {
               $where_clause_text = "( " . $where_clause_text . " )";
            }
         }
         return $where_clause_text;
      }
   }

   function mysql_table_auto_increment ( $connect_id, $table ) {
      $sql_statement = "SHOW TABLE STATUS";
      if ( $connect_id ) {
         $sql_result = mysql_query( $sql_statement, $connect_id );
      } else {
         $sql_result = mysql_query( $sql_statement );
      }

      if ( $sql_result ) {
         while( $sql_record = mysql_fetch_assoc($sql_result) ) {
            if ( $sql_record["Name"] == $table ) {
               return $sql_record["Auto_increment"];
            }
         }
      }

      return FALSE;
   }

   function mysql_version ( $connect_id = "" ) {
      if ( $connect_id ) {
         $sql_result = mysql_query( "SELECT VERSION()", $connect_id );
      } else {
         $sql_result = mysql_query( "SELECT VERSION()" );
      }

      $sql_record = mysql_fetch_assoc( $sql_result );
      $version = preg_replace( "/(-\w+)*$/", "", $sql_record['VERSION()'] );
      return $version;
   }

   function mysql_select_total_records ( $connect_id = "" ) {
      # requires mysql 4+
      # http://dev.mysql.com/doc/refman/4.1/en/information-functions.html#function_found-rows
      if ( $connect_id ) {
         $sql_result = mysql_query( "SELECT FOUND_ROWS()", $connect_id );
      } else {
         $sql_result = mysql_query( "SELECT FOUND_ROWS()" );
      }

      if ( $sql_result ) {
         $sql_record = mysql_fetch_assoc( $sql_result );
         return $sql_record['FOUND_ROWS()'];
      }
      return false;
   }

   function mysql_variables ( $connect_id = "" ) {
      if ( $connect_id ) {
         $sql_result = mysql_query( "SHOW VARIABLES", $connect_id );
      } else {
         $sql_result = mysql_query( "SHOW VARIABLES" );
      }

      $sql_record = mysql_fetch_assoc( $sql_result );
//      if ( $sql_result ) {
//         $sql_record = mysql_fetch_assoc( $sql_result );
//         return $sql_record['COUNT(*)'];
//      }
      print "<pre>";
      print_r( $sql_record );
      print "</pre>";
      exit;
   }

?>
