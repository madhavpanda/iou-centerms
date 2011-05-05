<?php
   function mysql_start ( $server = "localhost", $database, $username = "root", $password = "" ) {
      $connect_id = @mysql_connect( $server, $username, $password );
      if ( $connect_id ) {
         #mysql_select_db( $database, $connect_id ) or die('Query failed: ' . mysql_error());
         if ( mysql_select_db( $database, $connect_id ) ) {
            return $connect_id;
         }
         mysql_stop( $connect_id );
      }
      return 0;
   }

   function mysql_change_db ( $connect_id, $database ) {
      if ( !$connect_id ) return;
      if ( mysql_select_db( $database, $connect_id ) ) {
         return $connect_id;
      }
      return 0;
   }

   function mysql_stop ( $connect_id ) {
      if ( !$connect_id ) return;
      return mysql_close( $connect_id );
   }
   
   function mysql_execute_sql ( $connect_id, $sql_statement ) {
      if ( !$connect_id ) return;
      $sql_result = mysql_query( $sql_statement, $connect_id );

      if ( $sql_result ) {
         if ( $sql_result == 1 ) {
            return 1;
         } else if ( !mysql_num_rows($sql_result) ) {
            # Confirms that the SQL was executed correctly
            return 1;
         }
         $x = 1;
         while( $sql_record = mysql_fetch_assoc($sql_result) ) {
            $return_array[$x] = $sql_record;
            $x++;
         }
         # Old method which should be slower
         #for( $x = 1; $x <= mysql_num_rows($sql_result); $x++ ) {
         #   $sql_record = mysql_fetch_assoc( $sql_result );
         #   foreach ( $sql_record as $field_name => $field_value ) {
         #      $return_array[$x][$field_name] = $field_value;
         #   }
         #}
         
         return $return_array;
      }
      return 0;
   }

   function mysql_add_record ( $connect_id, $table, $fields, $quick_insert = 0, $duplicate_key_update = "" ) {
      if ( !$connect_id ) return;
      $sql_statement = "INSERT INTO $table ";
      $field_names = array_keys( $fields );

      if ( $field_names[0] != "0" AND !$quick_insert ) {
         #user has set the field names of the fields to be inserted
         $sql_statement .= "( `" . join( "`, `", $field_names ) . "` ) ";
      }
      foreach ( $fields as $key => $value ) {
         $field_value = "'" . mysql_escape_string( $value ) . "'";
         if ( $field_value == "''" ) {
            $field_value = "NULL";
         }
         $fields[$key] = $field_value;
      }
      $sql_statement .= "VALUES ( " . join( ", ", $fields ) . " ) ";
      
      # Available in MySQL 4.1.0 and up
      if ( $duplicate_key_update ) {
         $sql_statement .= "ON DUPLICATE KEY UPDATE ";
         if ( is_array($duplicate_key_update) ) {
            foreach( $duplicate_key_update as $key => $value ) {
               $field_and_value = "$key='" . mysql_escape_string($value) . "'";
               if ( $field_and_value == "$key='' " ) {
                  $field_and_value = "$key=NULL";
               }
               $field_values[] = $field_and_value;
            }
            $sql_statement .= join( ", ", $field_values );
         } else {
            $sql_statement .= $duplicate_key_update;
         }
      }

      if ( $GLOBALS['sql_debug'] ) print $sql_statement . "<br>\r\n";
      $requery_result = mysql_query( $sql_statement, $connect_id );
      if ( !$requery_result ) print mysql_error() . "<br>\r\n";
      return $requery_result;
   }

   function mysql_upd_record ( $connect_id, $table, $fields, $where_fields ) {
      if ( !$connect_id ) return;
      $sql_statement = "UPDATE $table SET ";
      foreach ( $fields as $field_name => $field_value ) {
         $field_and_value = "`$field_name` = '" . mysql_escape_string($field_value) . "'";
         if ( $field_and_value != "`$field_name` = ''" ) {
            $update_fields[] = $field_and_value;
         } else {
            $update_fields[] = "`$field_name` = NULL";
         }
      }
      $sql_statement .= join( ", ", $update_fields );
      $sql_statement .= mysql_build_where_clause( $where_fields );
      if ( $GLOBALS['sql_debug'] ) print $sql_statement . "<br>\r\n";
      $requery_result = mysql_query( $sql_statement, $connect_id );
      if ( !$requery_result ) print mysql_error() . "<br>\r\n";
      return $requery_result;
   }

   function mysql_del_record ( $connect_id, $table, $where_fields ) {
      if ( !$connect_id ) return;
      $sql_statement = "DELETE FROM $table";
      if ( is_array($where_fields) ) {
         $sql_statement .= mysql_build_where_clause( $where_fields );
      } else if ( $where_fields ) {
         $sql_statement .= " WHERE " . $where_fields;
      }
      if ( $GLOBALS['sql_debug'] ) print $sql_statement . "<br>\r\n";
      $requery_result = mysql_query( $sql_statement, $connect_id );
      if ( !$requery_result ) print mysql_error() . "<br>\r\n";
      return $requery_result;
   }

   function mysql_extract_record_id ( $connect_id, $table, $where_fields, $fields = "*" ) {
      if ( !$connect_id ) return;
      if ( !$fields ) $fields = "*";
      $sql_statement = "SELECT $fields FROM $table";
      if ( is_array($where_fields) ) {
         $sql_statement .= mysql_build_where_clause( $where_fields );
      } else if ( $where_fields ) {
         $sql_statement .= " WHERE " . $where_fields;
      }

      if ( $GLOBALS['sql_debug'] ) print $sql_statement . "<br>\r\n";
      $sql_result = mysql_query( $sql_statement, $connect_id );

      if ( $sql_result ) {
         return mysql_fetch_assoc($sql_result);
      }
      return 0;
   }

   function mysql_extract_records_where ( $connect_id, $table, $where_fields = "", $fields = "*", $order_by_field = "", $limit = "" ) {
      if ( !$connect_id ) return;
      if ( !$fields ) $fields = "*";
      $sql_statement = "SELECT $fields FROM $table";
      if ( is_array($where_fields) ) {
         $sql_statement .= mysql_build_where_clause( $where_fields );
      } else if ( $where_fields ) {
         $sql_statement .= " WHERE " . $where_fields;
      }
      if ( $order_by_field ) {
         $sql_statement .= " ORDER BY " . $order_by_field;
      }
      if ( $limit ) {
         $sql_statement .= " LIMIT " . $limit;
      }
      if ( $GLOBALS['sql_debug'] ) print $sql_statement . "<br>\r\n";
      $sql_result = mysql_query( $sql_statement, $connect_id );
      #if ( !$sql_result ) print mysql_error() . "<br>\r\n";

      if ( $sql_result ) {
         $x = 1;
         while( $sql_record = mysql_fetch_assoc($sql_result) ) {
            $return_array[$x] = $sql_record;
            $x++;
         }
         #for( $x = 1; $x <= mysql_num_rows($sql_result); $x++ ) {
         #   $sql_record = mysql_fetch_assoc( $sql_result );
         #   foreach ( $sql_record as $field_name => $field_value ) {
         #      $return_array[$x][$field_name] = $field_value;
         #   }
         #}
         return $return_array;
      }
      return 0;
   }

   function mysql_empty_table ( $connect_id, $table ) {
      # Truncate is better than 'DELETE FORM $table' as truncate will reset auto_increment to 1
      mysql_query( "TRUNCATE TABLE $table", $connect_id );
   }

   function mysql_extract_column ( $connect_id, $table, $field_name, $where_fields = "", $order_by_field = "", $unique = 0 ) {
      if ( !$connect_id ) return;
      $sql_statement = "SELECT $field_name FROM $table";
      if ( is_array($where_fields) ) {
         $sql_statement .= mysql_build_where_clause( $where_fields );
      } else if ( $where_fields ) {
         $sql_statement .= " WHERE " . $where_fields;
      }
      if ( $unique ) {
         $sql_statement .= " GROUP BY " . $field_name;
      }
      if ( $order_by_field ) {
         $sql_statement .= " ORDER BY " . $order_by_field;
      }
      if ( $GLOBALS['sql_debug'] ) print $sql_statement . "<br>\r\n";
      $sql_result = mysql_query( $sql_statement, $connect_id );

      if ( $sql_result ) {
         while( $sql_record = mysql_fetch_row($sql_result) ) {
            $column_result[] = $sql_record[0];
         }
         return $column_result;
      }
      return 0;
   }

   function mysql_make_associate_array ( $connect_id, $table, $fields, $text_separator = " - " ) {
      $sql_statement = "SELECT " . join( ", ", $fields ) . " FROM $table";
      $sql_statement .= mysql_build_where_clause( $where_fields );

      if ( $GLOBALS['sql_debug'] ) print $sql_statement . "<br>\r\n";
      $sql_result = mysql_query( $sql_statement, $connect_id );

      if ( $sql_result ) {
         while( $sql_record = mysql_fetch_row($sql_result) ) {
            $variable_name = array_shift( $sql_record );
            $value = join( $text_separator, $sql_record );
            $return_array[$variable_name] = $value;
         }
         return $return_array;
      }
      return 0;
   }

   function mysql_make_associate_array2 ( $connect_id, $table, $fields, $where_fields = "", $order_by_field = "", $limit = "", $text_separator = " - " ) {
      if ( !$connect_id ) return;
      if ( is_array($fields) ) {
         $sql_statement = "SELECT " . join( ", ", $fields ) . " FROM $table";
      } else {
         $sql_statement = "SELECT $fields FROM $table";
      }
      if ( is_array($where_fields) ) {
         $sql_statement .= mysql_build_where_clause( $where_fields );
      } else if ( $where_fields ) {
         $sql_statement .= " WHERE " . $where_fields;
      }
      if ( $order_by_field ) {
         $sql_statement .= " ORDER BY " . $order_by_field;
      }
      if ( $limit ) {
         $sql_statement .= " LIMIT " . $limit;
      }

      if ( $GLOBALS['sql_debug'] ) print $sql_statement . "<br>\r\n";
      $sql_result = mysql_query( $sql_statement, $connect_id );

      if ( $sql_result ) {
         while( $sql_record = mysql_fetch_row($sql_result) ) {
            $variable_name = array_shift( $sql_record );
            $value = join( $text_separator, $sql_record );
            $return_array[$variable_name] = $value;
         }
         return $return_array;
      }
      return 0;
   }

   function mysql_make_assoc_array ( $connect_id, $table, $fields, $where_fields = "", $order_by_field = "", $limit = "", $text_separator = "" ) {
      # Returns a two-dimentional associated array
      if ( !$connect_id ) return;
      if ( is_array($fields) ) {
         $sql_statement = "SELECT " . join( ", ", $fields ) . " FROM $table";
      } else {
         $sql_statement = "SELECT $fields FROM $table";
      }
      if ( is_array($where_fields) ) {
         $sql_statement .= mysql_build_where_clause( $where_fields );
      } else if ( $where_fields ) {
         $sql_statement .= " WHERE " . $where_fields;
      }
      if ( $order_by_field ) {
         $sql_statement .= " ORDER BY " . $order_by_field;
      }
      if ( $limit ) {
         $sql_statement .= " LIMIT " . $limit;
      }

      if ( $GLOBALS['sql_debug'] ) print $sql_statement . "<br>\r\n";
      $sql_result = mysql_query( $sql_statement, $connect_id );

      if ( $sql_result ) {
         while( $sql_record = mysql_fetch_assoc($sql_result) ) {
            $variable_name = array_shift( $sql_record );
            $return_array[$variable_name] = $sql_record;
         }
         return $return_array;
      }
      return 0;
   }

   function mysql_count_query_records ( $connect_id, $table, $where_fields = "" ) {
      if ( !$connect_id ) return;
      $sql_statement = "SELECT COUNT(*) FROM $table";
      if ( is_array($where_fields) ) {
         $sql_statement .= mysql_build_where_clause( $where_fields );
      } else if ( $where_fields ) {
         $sql_statement .= " WHERE " . $where_fields;
      }
      if ( $GLOBALS['sql_debug'] ) print $sql_statement . "<br>\r\n";
      $sql_result = mysql_query( $sql_statement, $connect_id );

      if ( $sql_result ) {
         $sql_record = mysql_fetch_assoc( $sql_result );
         return $sql_record['COUNT(*)'];
      }
      return 0;
   }

   function mysql_count_record_groups ( $connect_id, $table, $fields, $where_fields = "" ) {
      if ( !$connect_id ) return;
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
      if ( $GLOBALS['sql_debug'] ) print $sql_statement . "<br>\r\n";

      $sql_result = mysql_query( $sql_statement, $connect_id );

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
               $result[$x] = $sql_record;
               $x++;
            }
         }

         return $result;
      }

      return 0;
   }

   function mysql_count_table_records ( $connect_id, $database, $table ) {
      if ( !$connect_id ) return;
      $sql_statement = "SHOW TABLE STATUS FROM $database";
      $sql_result = mysql_query( $sql_statement, $connect_id );

      if ( $sql_result ) {
         for( $x = 1; $x <= mysql_num_rows($sql_result); $x++ ) {
            $sql_record = mysql_fetch_assoc( $sql_result );
            if ( strtolower($sql_record['Name']) == strtolower($table) ) {
               return $sql_record['Rows'];
            }
         }
      }

      return 0;
   }

   function mysql_increment_field ( $connect_id, $table, $field, $where_fields, $by = 1 ) {
      if ( !$connect_id ) return;
      $sql_statement = "UPDATE $table SET $field=$field+$by";
      if ( is_array($where_fields) ) {
         $sql_statement .= mysql_build_where_clause( $where_fields );
      } else if ( $where_fields ) {
         $sql_statement .= " WHERE " . $where_fields;
      }

      if ( $GLOBALS['sql_debug'] ) print $sql_statement . "<br>\r\n";
      $sql_result = mysql_query( $sql_statement, $connect_id );
   }

   function mysql_get_foreign_key_list( $connect_id, $database, $table, $key_field, $display_fields, $where_clause = "", $order_by_field = "" ) {
      if ( !$connect_id ) return;
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
      if ( !$connect_id ) return;
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

   function mysql_table_structure ( $connect_id, $table ) {
      if ( !$connect_id ) return;
      #Also possible with 'SHOW FIELDS FROM '
      $sql_statement = "SHOW COLUMNS FROM " . $table;
      $sql_result = mysql_query( $sql_statement, $connect_id );

      if ( $sql_result ) {
         while( $sql_record = mysql_fetch_assoc($sql_result) ) {
            $field_name = $sql_record['Field'];
            list( $field_type, $field_size, $field_others ) = mysql_field_type_extract( $sql_record['Type'] );
            $table_structure[$field_name]['type'] = $field_type;
            $table_structure[$field_name]['size'] = $field_size;
            $table_structure[$field_name]['others'] = $field_others;
            $table_structure[$field_name]['null'] = $sql_record['Null'];
            $table_structure[$field_name]['keys'] = $sql_record['Key'];
            $table_structure[$field_name]['default'] = $sql_record['Default'];
            $table_structure[$field_name]['extra'] = $sql_record['Extra'];
         }
         return $table_structure;
      }
      return 0;
   }

   function mysql_field_structure ( $connect_id, $table, $field ) {
      if ( !$connect_id ) return;
      $sql_statement = "SHOW COLUMNS FROM " . $table;
      $sql_result = mysql_query( $sql_statement, $connect_id );

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
         #$field_parts[2] = str_replace( "''", "'", $field_parts[2] );
         #$field_parts[2] = str_replace( "','", ",", $field_parts[2] );
         #$field_parts[2] = preg_replace( "/^'/", "", $field_parts[2] );
         #$field_parts[2] = preg_replace( "/'$/", "", $field_parts[2] );
         # New ----
         $field_parts[2] = preg_replace( "/^\'(.+)\'$/", "$1", $field_parts[2] );
         # Handles "\'" which shows as '' after query
         $field_parts[2] = str_replace( "''", "'", $field_parts[2] );
         $field_parts[2] = explode( "','", "','" . $field_parts[2] );
         unset( $field_parts[2][0] );
         # ----
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

   function mysql_enum_field_values ( $mysql_connect_id, $table, $field, $add_numbers = 0 ) {
      $extracted_field = mysql_field_structure( $mysql_connect_id, $table, $field );
      #$enum_values = explode( ",", $extracted_field['size'] );
      # New ----
      $enum_values = $extracted_field['size'];

      if ( $add_numbers ) {
         foreach ( $enum_values as $values ) {
            $x++;
            $enum_values1[$x] = $values;
         }
         $enum_values = $enum_values1;
      }
      # ----

      return $enum_values;
   }

   function mysql_enum_field_to_html_options ( $connect_id, $table, $field, $selected = array(), $add_numbers = 0 ) {
      if ( !$connect_id ) return;
      $extracted_field = mysql_field_structure( $connect_id, $table, $field );
      $enum_values = $extracted_field['size'];

      if ( is_array($selected) ) {
         $selected_array = $selected;
      } else {
         $selected_array = explode( ",", $selected );
      }

      foreach ( $enum_values as $text ) {
         if ( $add_numbers ) {
            $x++;
            if ( in_array( $text, $selected_array ) ) {
               $html_output .= "<option value=\"$x\" selected>$text";
            } else {
               $html_output .= "<option value=\"$x\">$text";
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
      #Formatted as $array['username ='] = "yousuf";
      if ( $where_fields ) {
         if ( !is_array($where_fields) ) {
            $where_clause_text = $where_fields;
         } else {
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
            $where_clause_text = join( " AND ", $where_clause );
         }

         return " WHERE " . $where_clause_text;
      }
      #return 0;
   }

   function mysql_current_databse ( $connect_id ) {
      if ( !$connect_id ) return;
      $sql_result = mysql_query( "SELECT DATABASE()", $connect_id );

      if ( $sql_result ) {
         $sql_record = mysql_fetch_assoc( $sql_result );
         return $sql_record['DATABASE()'];
      }

      return 0;
   }

   function mysql_primary_key_field ( $table_structure ) {
      foreach ( $table_structure as $key => $value ) {
         if ( $value['keys'] == "PRI" ) {
            return $key;
         }
      }

      return "";
   }

   #SQL Queries
   #List Tables in DB - show table status from `mysql`
   #Give Table Structure Code - show create table `theemira_general`.`user_temp`
   # Get current DB - SELECT DATABASE();
   # Get tables in current DB - SHOW TABLES;
   # Get table structure - DESCRIBE pet;
?>
