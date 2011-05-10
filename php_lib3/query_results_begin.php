<?php
//      $records_per_page = 10;
//      $select_addons = array( "WHERE" => $where_clause, "ORDER BY" => "NAME", "GROUP" => "name,city,boxno,phone" );
//      $fields_to_extract = "CODE ID, name, groupno, classname, city, boxno as address, phone, fax, classcode, email, website, location";
//      $mysql_connection_id = ""; $table_to_query = $mysql_hawk_biz_db_companies_tb;
      # need to set the variables $table_to_query and $select_addons before including this file

		$maximum_result_pages = ( !$maximum_result_pages ) ? 10 : $maximum_result_pages;
      $records_per_page = ( !$records_per_page ) ? 10 : $records_per_page;
      $FORM['start'] = ( $FORM['start'] == "" OR $FORM['start'] < 0 ) ? "0" : $FORM['start'];
      if ( $FORM['start'] == "all" ) {
         $FORM['start'] = 0;
         $records_per_page = 100000000;
         $select_addons['LIMIT'] = "$FORM[start], $records_per_page";
      } else {
         $select_addons['LIMIT'] = "$FORM[start], $records_per_page";
      }

      #print mysql_create_select_statement( $table_to_query, $fields_to_extract, $select_addons );
      #exit;

      $mysql_version = intval( mysql_version() );
      if ( $mysql_version >= 4 ) {
         $time_start = microtime_float();
         $select_addons['FOUND_ROWS'] = '';
         $search_results = mysql_extract_records_where( $mysql_connection_id, $table_to_query, $select_addons, $fields_to_extract );
         $total_records = mysql_select_total_records( $mysql_connection_id );
         $time_end = microtime_float();
      } else {
         $time_start = microtime_float();
         $search_results = mysql_extract_records_where( $mysql_connection_id, $table_to_query, $select_addons, $fields_to_extract );
         if ( $select_addons['LIMIT'] ) {
//            $field_list = "";
//            if ( $select_addons['LIMIT'] ) {
//               $field_list = 
//            }
            $total_records = mysql_count_query_records( $mysql_connection_id, $table_to_query, $select_addons );
         } else {
            $total_records = count( $search_results );
         }
         $time_end = microtime_float();
      }
      $mysql_execute_time = $time_end - $time_start;
   	$total_records = ( $total_records == "" ) ? 0 : $total_records;
   
      $starting = ( !$total_records ) ? 0 : $FORM['start'] + 1;
      $ending = $FORM['start'] + $records_per_page;
      $ending = ( $ending > $total_records ) ? $total_records : $ending;
      $displaying = $ending - $starting + 1;
   	$total_pages = ceil( $total_records / $records_per_page );
      $current_page = ceil( $starting / $records_per_page );
   	$total_pages = ( $total_pages ) ? $total_pages : 1;
   	$current_page = ( $current_page ) ? $current_page : 1;
   	if ( !$total_records ) {
      	$total_pages = 0;
      	$current_page = 0;
   	}   

      /* Showing <?php print $starting ?> -<?php print $ending ?> of <?php print $total_records ?> */
      /* [Page <?php print $current_page ?> of <?php print $total_pages ?>] */

      # clear up trash entries in the query string
//      $query_string = preg_replace( "/&start=\d+/", "", "&" . $_SERVER['QUERY_STRING'] . "&" );
//      $query_string = preg_replace( "/\w+=&/", "&", $query_string . "&" );
//      $query_string = preg_replace( "/^&+/", "", $query_string );
//      $query_string = preg_replace( "/&+$/", "", $query_string );
//      $query_string = preg_replace( "/&+/", "&", $query_string );

      if ( $form_query_variables ) {
         $FORM1 = $FORM;
         foreach ( $FORM1 as $key => $value ) {
            if ( is_array($value) ) {
               $value = join( ",", $value );
            }
            $paginate_query_string_variables[$key] = $value;
         }
      } else if ( !$paginate_query_string_variables ) {
         if ( $server_query_string ) {
            parse_str( $_SERVER['QUERY_STRING'], $paginate_query_string_variables );
         } else {
            $url_parse = parse_url( $_SERVER['REQUEST_URI'] );
            parse_str( $url_parse['query'], $paginate_query_string_variables );
         }
      }
      unset( $paginate_query_string_variables['start'] );
      if ( $paginate_query_string_variables_to_remove ) {
         foreach ( $paginate_query_string_variables_to_remove as $key ) {
            unset( $paginate_query_string_variables[$key] );
         }
      }
      $query_string = http_build_query_new( $paginate_query_string_variables, "", "&" );

?>