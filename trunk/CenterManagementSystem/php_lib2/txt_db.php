<?php
   function extract_all_fields_in_column ( $db_file_name, $column_to_extract ) {
      $file_records = file( $db_file_name );

      $output_array = array();
      foreach ( $file_records as $file_record ) {
         $split_file_record = explode( "||", $file_record );
         array_push( $output_array, chop( $split_file_record[$column_to_extract-1] ) );
      }
      
      return $output_array;
   }

   function txt_extract_record_id ( $db_file_name, $record_id, $column_number = 0, $field_splitter = "||" ) {
      return extract_record_id ( $db_file_name, $record_id, $column_number, $field_splitter );
   }

   function extract_record_id ( $db_file_name, $record_id, $column_number = 0, $field_splitter = "||" ) {
      $db_records = file( $db_file_name );
   
      foreach ( $db_records as $db_record ) {
         $record_fields = explode( $field_splitter, chop( $db_record ) );
   
         if ( $record_fields[$column_number] == $record_id ) {
            return $record_fields;
         }
      }
   }

   function txt_extract_records_where ( $db_file_name, $field_values, $fields_to_extract = "", $field_splitter = "||" ) {
      return extract_all_records_with ( $db_file_name, $field_values, $fields_to_extract, $field_splitter );
   }

   function extract_all_records_with ( $db_file_name, $field_values, $fields_to_extract = "", $field_splitter = "||" ) {
      $recordz = array();
      if ( $db_records = @file( $db_file_name ) ) {
         foreach ( $db_records as $db_record ) {
            $record_fields = explode( $field_splitter, chop( $db_record ) );
            
            $pass = 1;
            foreach ( $field_values as $key => $value ) {
               if ( $record_fields[$key] != $value ) {
                  $pass = 0;
               }
            }
            if ( $pass == 1 ) {
               if ( $fields_to_extract ) {
                  $extracted_fields = array();
                  foreach( $fields_to_extract as $field_number ) {
                     $extracted_fields[] = $record_fields[$field_number];
                  }
                  $recordz[] = $extracted_fields;
               } else {
                  $recordz[] = $record_fields;
               }
            }
         }
      }
      return $recordz;
   }

   function txt_extract_records_where_new ( $db_file_name, $where_fields, $fields_to_extract = "", $order_by_field = "", $limit = "", $field_splitter = "||" ) {
      if ( $order_by_field ) {
         list( $order_field_number, $order_method ) = explode( " ", $order_by_field );
      }
      if ( $limit ) {
         list( $limit_start, $limit_end ) = preg_split( "/,\s?/", $limit );
         $limit_end += $limit_start;
      } else {
         $limit_start = 0;
         $limit_end = 1000000000;
      }

      $x = 0;
      if ( $db_records = @file( $db_file_name ) ) {
         foreach ( $db_records as $db_record ) {
            $record_fields = explode( $field_splitter, trim( $db_record ) );
            
            $pass = 1;
            foreach ( $where_fields as $key => $value ) {
               list( $column_number, $comparison ) = explode( " ", $key );
               
               if ( $comparison == "=" OR !$comparison ) {
                  if ( $record_fields[$column_number] != $value ) {
                     $pass = 0;
                     break;
                  }
               } else if ( $comparison == ">" ) {
                  if ( $record_fields[$column_number] <= $value ) {
                     $pass = 0;
                     break;
                  }
               } else if ( $comparison == "<" ) {
                  if ( $record_fields[$column_number] >= $value ) {
                     $pass = 0;
                     break;
                  }
               } else if ( $comparison == ">=" ) {
                  if ( $record_fields[$column_number] < $value ) {
                     $pass = 0;
                     break;
                  }
               } else if ( $comparison == "<=" ) {
                  if ( $record_fields[$column_number] > $value ) {
                     $pass = 0;
                     break;
                  }
               } else if ( $comparison == "<>" OR $comparison == "!=" ) {
                  if ( $record_fields[$column_number] != $value ) {
                     $pass = 0;
                     break;
                  }
               }
            }
            if ( $pass == 1 ) {
               if ( $x >= $limit_start and $x < $limit_end ) {
                  if ( $fields_to_extract ) {
                     $extracted_fields = array();
                     foreach( $fields_to_extract as $field_number ) {
                        $extracted_fields[$field_number] = $record_fields[$field_number];
                     }
                     $recordz[$record_fields[$order_field_number] . "_" . $x] = $extracted_fields;
                  } else {
                     $recordz[$x] = $record_fields;
                  }
               } else if ( $x >= $limit_end ) {
                  break;
               }
               $x++;
            }
         }
      }

      if ( $order_field_number != "" and $recordz ) {
         if ( $order_method == "DESC" ) {
            krsort( $recordz );
         } else if ( $order_method == "DESC_N" ) {
            krsort( $recordz, SORT_NUMERIC );
         } else {
            ksort( $recordz );
         }
      }
      return $recordz;
   }

   function get_db_column ( $db_file_name, $field_number, $field_value ) {
      $db_records = file( $db_file_name );

      $recordz = array();
      for ( $x = 1; $x <= count($db_records); $x++ ) {
         $db_record = $db_records[$x-1];
         $record_fields = explode( "||", chop( $db_record ) );

         if ( $record_fields[$field_number-1] == $field_value ) {
            return $x;
         }
      }

      return 0;
   }

   function get_associate_array ( $db_file_name, $column_to_extracts, $text_separator = " - " ) {
      $file_records = file( $db_file_name );

      $first_record = array_shift( $column_to_extracts );
      $output_array = array();
      foreach ( $file_records as $file_record ) {
         $split_file_record = explode( "||", $file_record );
         $value_array = array();
         foreach ( $column_to_extracts as $column_extract ) {
            $value_array[] = $split_file_record[$column_extract];
         }
         
         $output_array[$split_file_record[$first_record]] = join( $text_separator, $value_array );
      }
      
      return $output_array;
   }
?>