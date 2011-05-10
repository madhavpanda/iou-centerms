<?php
   # $main_menu_fields_to_display, $fields_to_display, $fields_not_to_display
   # $primary_key_alternative, $ignore_field_check, $default_sort_field, $default_sort_type, $records_per_page
   # $page_encoding, $default_sort_string, $mysql_debug

   # $enabled_field_name - will change the field to an image icon to turn off and on as well as coloring the row
   # $checkbox_column - if set then a checkbox will appear on each row for easy deletion
   # $default_alignment - default alignment of fields whose alignment isnt set

   # features - main_align, main_preg_replace( find, replace ), main_trim, main_trim_crop, main_trim_crop2, main_crop_right, main_set
   #  main_url, main_url_base, rotate_enum, print_prefix_value, title_main
   #  main_pad_left, main_pad_right, main_pad_lfrt, main_pad
   # $field_features[field_name][feature]

   # $main_menu_enum_swap
   # $main_menu_clickable_fields - array of fields that can be clicked to the view, edit links or another url
   # $main_menu_clickable_field - will make the field clickable to the edit or view page
   # $no_function_column, $no_edit, $no_edit_button, $no_add, $no_duplicate, $no_delete, $no_view, $no_view_button

   # -- in add and edit pages --
   # features - print_value, title, 
   #  fkey - array( "DB" => $general_database_name, "TB" => "language", "FIELD_KEY" => "code2", "FIELD_DISPLAY" => "name", 'WHERE_CLAUSE' => "enabled = 'Y' AND code2 IS NOT NULL", 'ORDER_BY' => 'name' );
   #  fkey - "db,table,key,value,where,order";
   #  fkey_view - does fkey in the add/edit pages
   #  build_cat - array( "DB" => $general_database_name, "TB" => "location", "FIELDS" => "code,parent_code,name", 'WHERE_CLAUSE' => "code regexp '^AE-' AND ( type = 'country' OR type = 'state' OR type = 'city' OR type = 'town' OR type = 'village' ) AND redirect IS NULL AND enabled = 'Y'", 'ORDER_BY' => 'name', 'SEPARATOR' => "||", 'INDENT' => 1, 'OPTIONS' => array( 'use_codes' => "Y", 'build_parent_from_id' => "Y" ) );
   #  ['build_cat']['fkey'] - execute a category build from fkey formatted array, it maybe necessary to set the 'FIELDS' and 'OPTIONS' keys if the result isnt corrected

   # -- new edit file --
//   $mysql_server = "localhost";
//   $mysql_username = "theemira_all";
//   $mysql_password = "uaeviruz";
//
//   $database_name = "theemira_classifieds";
//   $table_name = "categories";
//
//   $main_menu_fields_to_display = array( "id", "parent_id", "name", "ad_features", "visible" );
//   $fields_to_display = array( );
//   $fields_not_to_display = array( );
//   $database_manager_link_url = "./";
//
//   #$field_features['developer_id']['title'] = "Developer";
//
//   #$records_per_page = 15;

//   $php_library_path = "../../php_lib2/";
//   include( "../../php_lib2/subs_new.php" );
//   include( "../../php_lib2/db_view.php" );
//   exit;

//   function db_view_add_record ( $record_fields ) {
//      return $record_fields;
//   }
//
//   function db_view_update_record ( $record_fields ) {
//      return $record_fields;
//   }

   if ( $php_library_path ) {
      if ( !function_exists('validate_email')) include( $php_library_path . "misc.php" );
      if ( !function_exists('file_put')) include( $php_library_path . "file.php" );
      if ( !function_exists('mysql_start')) include( $php_library_path . "mysql.php" );
      if ( !function_exists('mysql_generate_form')) {
         if ( $mysql_form_location ) {
            include( $mysql_form_location );
         } else {
            include( $php_library_path . "mysql_forms.php" );
         }
      }
   }
   if ( $mysql_debug ) $sql_debug = 1;

   if (!function_exists('shorten_string')) {
   	function shorten_string ( $string, $size_to_shorten_to ) {
   	   if ( strlen($string) > $size_to_shorten_to ) {
   		   $string = preg_replace( "/,?\s+[\w\(]*$/", "", substr( $string, 0, $size_to_shorten_to-2 ) ) . "...";
   		}
   		return $string;
   	}
   }

   # php manual "stripslashes" < lukas.skowronski at gmail dot com > (20-Jun-2007 12:15)
   if (!function_exists('stripslashes_deep')) {
      function stripslashes_deep ( $array ) {
         foreach ( $array as $key => $value ) {
            # added this if statement as slashes are also added to keys
            $key1 = stripslashes( $key );
            if ( $key != $key1 ) {
               unset( $array[$key] );
               $key = $key1;
            }
            if ( is_array($value) ) {
               $value = stripslashes_deep( $value );
               $array_temp[$key] = $value;
            } else {
               if ( !is_null($value) ) {
                  # added this line due to the comment by < hauser dot j at gmail dot com > (21-Feb-2006 10:13)
                  $array_temp[$key] = stripslashes( $value );
               }
            }
         } 
         return $array_temp; 
      }
   }

   # PHP Manual - Magic Quotes : Disabling Magic Quotes
   if ( get_magic_quotes_gpc() AND !$magic_quotes_cleared ) {
      $FORM = stripslashes_deep( $FORM );
//      $_GET = undoMagicQuotes($_GET);
//      $_POST = undoMagicQuotes($_POST);
//      $_COOKIE = undoMagicQuotes($_COOKIE);
//      $_REQUEST = undoMagicQuotes($_REQUEST);
   }

   #$sql_debug = 1;
   $mysql_connect_id = mysql_start( $mysql_server, $database_name, $mysql_username, $mysql_password );
   if ( !$mysql_connect_id ) {
      print "Unable to Connect to DB<br>" . mysql_error();
      exit;
   }

   #$FORM['return_query'] = ( $FORM['return_query'] ) ? "?" . $FORM['return_query'] : "";
   $FORM['return_query_mark'] = ( $FORM['return_query'] ) ? "?" . preg_replace( "/^\\?+/", "", $FORM['return_query'] ) : "";

   if ( $FORM['submit'] ) {
      if ( $FORM['f'] == "add" AND !$no_add ) {
         $table_structure = mysql_table_structure( $mysql_connect_id, $table_name );
         $primary_key_field = mysql_primary_key_field( $table_structure );

         foreach ( $FORM['fieldz'] as $field_name => $value ) {
            if ( is_array( $value ) ) {
               $add_fields[$field_name] = join( ",", $value );
            } else {
               if ( $trim_fields ) {
                  $value = trim( $value );
               }
               $add_fields[$field_name] = $value;
            }
         }

         # verification goes here

         if ( $field_add_values ) {
            # used to format fields according to user's preferences
            foreach ( $field_add_values as $key => $value_array ) {
               $value = $value_array['value'];

               # replaces all text in the form of 'field[field_name]' into the value given from the submitted values
               while ( preg_match( "/field\[([^\]]+)\]/i", $value, $preg_results ) ) {
                  $preg_value = strtolower( $preg_results[1] );
                  $value = str_replace( $preg_results[0], $add_fields[$preg_value], $value );
               }
               while ( preg_match( "/field_substr\[([^\]]+)\]\[([^\]]+)\](?:\[([^\]]+)\])\]/i", $value, $preg_results ) ) {
                  $preg_value = strtolower( $preg_results[1] );
                  if ( $preg_results[3] ) {
                     $value = str_replace( $preg_results[0], substr($add_fields[$preg_value],$preg_results[2],$preg_results[3]), $value );
                  } else {
                     $value = str_replace( $preg_results[0], substr($add_fields[$preg_value],$preg_results[2]), $value );
                  }
               }
               while ( preg_match( "/field_preplace\[([^\]]+)\]\[\"([^\"]+)\"\]\[\"([^\"]+)\"\]/i", $value, $preg_results ) ) {
                  $preg_value = strtolower( $preg_results[1] );
                  $new_value = preg_replace( $preg_results[2], $preg_results[3], $add_fields[$preg_value] );
                  $value = str_replace( $preg_results[0], $new_value, $value );
               }

               if ( $value == "current_date" ) {
                  $value = gmdate( "Y-m-d" );
               } else if ( $value == "current_date_time" ) {
                  $value = gmdate( "Y-m-d H:i:s" );
               } else if ( preg_match( "/date\[([^\]]+)\]/i", $value, $preg_results ) ) {
                  # FORMAT - 'date[l dS of F Y h:i:s A]' - allowing the use of any of the special date format characters
                  $value = gmdate( $preg_results[1] );
               }

               $functions = explode( ",", $value_array['functions'] );
               if ( $functions ) {
                  foreach ( $functions as $function_name ) {
                     if ( $function_name == "trim" ) {
                        $value = trim( $value );
                     } else if ( $function_name == "up_case" OR $function_name == "ucase" ) {
                        $value = strtoupper( $value );
                     } else if ( $function_name == "low_case" OR $function_name == "lcase" ) {
                        $value = strtolower( $value );
                     } else if ( $function_name == "up_case_first_letter" OR $function_name == "ucasefl" ) {
                        $value = upcase_first_letter( $value );
                     }
                  }
               }

               $add_fields[$key] = $value;
            }
         }

         if ( function_exists('db_view_add_record') ) {
            $add_fields = db_view_add_record( $add_fields );
            if ( is_string($add_fields) ) {
               $update_error_message = $add_fields;
            }
         }

         if ( !$update_error_message ) {
            mysql_add_record( $mysql_connect_id, $table_name, $add_fields );
            $error_message = mysql_error( $mysql_connect_id );
            if ( $error_message ) {
               print "MyQSL Error: $error_message";
               exit;
            }
            if ( $add_fields[$primary_key_field] ) {
               $record_id = $add_fields[$primary_key_field];
            } else {
               $record_id = mysql_insert_id( $mysql_connect_id );
            }
   
            if ( $FORM['submit'] == "Add and Duplicate" ) {
               $FORM['return_query_mark'] = htmlentities(urlencode($FORM['return_query']));
               header( "Location: $_SERVER[PHP_SELF]?f=add&pri=$record_id&return_query=" . $FORM['return_query_mark'] );
            } else if ( $FORM['submit'] == "Add and New" ) {
               $FORM['return_query_mark'] = htmlentities(urlencode($FORM['return_query']));
               header( "Location: $_SERVER[PHP_SELF]?f=add&return_query=" . $FORM['return_query_mark'] );
            } else if ( $FORM['submit'] == "Add and Close" ) {
               if ( $FORM['p_execute'] ) {
                  print "<html><body onLoad='window.opener.$FORM[p_execute];window.opener.focus();window.close();'></body></html>";
               } else {
                  print "<html><body onLoad='window.close();'></body></html>";
               }
               exit;
            } else {
               header( "Location: $_SERVER[PHP_SELF]" . $FORM['return_query_mark'] );
            }
            exit;
         }
      } else if ( ($FORM['f'] == "edt" OR $FORM['f'] == "edit") AND !$no_edit ) {
         $table_structure = mysql_table_structure( $mysql_connect_id, $table_name );
         $primary_key_field = ( !$primary_key_alternative ) ? mysql_primary_key_field( $table_structure ) : $primary_key_alternative;

         foreach ( $FORM['fieldz'] as $field_name => $value ) {
            if ( is_array( $value ) ) {
               $update_fields[$field_name] = join( ",", $value );
            } else {
               if ( $trim_fields ) {
                  $value = trim( $value );
               }
               $update_fields[$field_name] = $value;
            }
         }

         if ( $field_edit_values ) {
            # used to format fields according to user's preferences
            foreach ( $field_edit_values as $key => $value_array ) {
               $value = $value_array['value'];
               $functions = explode( ",", $value_array['functions'] );
               # replaces all text in the form of 'field[field_name]' into the value given from the submitted values
               while ( preg_match( "/field\[([^\]]+)\]/i", $value, $preg_results ) ) {
                  $preg_value = strtolower( $preg_results[1] );
                  $value = str_replace( $preg_results[0], $update_fields[$preg_value], $value );
               }

               if ( $value == "current_date" ) {
                  $value = gmdate( "Y-m-d" );
               } else if ( $value == "current_date_time" ) {
                  $value = gmdate( "Y-m-d H:i:s" );
               } else if ( preg_match( "/date\[([^\]]+)\]/i", $value, $preg_results ) ) {
                  # FORMAT - 'date[l dS of F Y h:i:s A]' - allowing the use of any of the special date format characters
                  $value = gmdate( $preg_results[1] );
               }

               if ( $functions ) {
                  foreach ( $functions as $function_name ) {
                     if ( $function_name == "trim" ) {
                        $value = trim( $value );
                     } else if ( $function_name == "up_case" OR $function_name == "ucase" ) {
                        $value = strtoupper( $value );
                     } else if ( $function_name == "low_case" OR $function_name == "lcase" ) {
                        $value = strtolower( $value );
                     } else if ( $function_name == "up_case_first_letter" OR $function_name == "ucasefl" ) {
                        $value = upcase_first_letter( $value );
                     }
                  }
               }

               $update_fields[$key] = $value;
            }
         }

         if ( function_exists('db_view_update_record') ) {
            $update_fields = db_view_update_record( $update_fields );
            if ( is_string($add_fields) ) {
               $update_error_message = $add_fields;
            }
         }

         if ( !$update_error_message ) {
            mysql_upd_record( $mysql_connect_id, $table_name, $update_fields, array( $primary_key_field => $FORM['pri_value'] ) );
            $error_message = mysql_error( $mysql_connect_id );
            if ( $error_message ) {
               print "MyQSL Error: $error_message";
               exit;
            }
         }

         #$query_string_extra = "start=$FORM[start]&sort_field=$FORM[sort_field]&sort_type=$FORM[sort_type]";
         #$query_string_extra = preg_replace( "/[\w%]+=&+/", "", $query_string_extra . "&" );
         #$query_string_extra = preg_replace( "/&+$/", "", $query_string_extra );
         #$FORM['return_query'] = str_replace( "\n", "&", $FORM['return_query'] );

         if ( $FORM['submit'] == "Edit and Close" ) {
            if ( $FORM['p_execute'] ) {
               print "<html><body><script language=\"JavaScript\">\r\nwindow.opener.$FORM[p_execute];window.opener.focus();\r\nwindow.close();\r\n</script></body></html>";
            } else {
               print "<html><body onLoad='window.close();'></body></html>";
            }
            exit;
         } else {
            header( "Location: $_SERVER[PHP_SELF]" . $FORM['return_query_mark'] );
         }
         exit;
      } else if ( $FORM['f'] == "del" AND !$no_delete ) {
         $table_structure = mysql_table_structure( $mysql_connect_id, $table_name );
         $primary_key_field = ( !$primary_key_alternative ) ? mysql_primary_key_field( $table_structure ) : $primary_key_alternative;

         #$query_string_extra = "start=$FORM[start]&sort_field=$FORM[sort_field]&sort_type=$FORM[sort_type]";
         #$query_string_extra = preg_replace( "/[\w%]+=&+/", "", $query_string_extra . "&" );
         #$query_string_extra = preg_replace( "/&+$/", "", $query_string_extra );

         $FORM['return_query_mark'] = str_replace( "\n", "&", $FORM['return_query_mark'] );

         mysql_del_record( $mysql_connect_id, $table_name, array( $primary_key_field => $FORM['pri'] ) );
         header( "Location: $_SERVER[PHP_SELF]" . $FORM['return_query_mark'] );
         exit;
      } else if ( $FORM['f'] == "chg_status" ) {
         if ( $FORM['selected_records'] ) {
            $table_structure = mysql_table_structure( $mysql_connect_id, $table_name );
            $primary_key_field = ( !$primary_key_alternative ) ? mysql_primary_key_field( $table_structure ) : $primary_key_alternative;
            $enabled_field_name = ( $checkbox_column_enable_field ) ? $checkbox_column_enable_field : $enabled_field_name;
   
            foreach ( $FORM['selected_records'] as $record_id ) {
               $delete_where[] = "$primary_key_field = '" . mysql_real_escape_string( $record_id ) . "'";
            }

            if ( $FORM['submit'] == "Disable" ) {
               mysql_upd_record( $mysql_connect_id, $table_name, array( $enabled_field_name => 'N' ), join( " OR ", $delete_where ) );
            } else if ( $FORM['submit'] == "Enable" ) {
               mysql_upd_record( $mysql_connect_id, $table_name, array( $enabled_field_name => 'Y' ), join( " OR ", $delete_where ) );
            } else if ( $FORM['submit'] == "Delete" ) {
               mysql_del_record( $mysql_connect_id, $table_name, join( " OR ", $delete_where ) );
            }
         }

         header( "Location: $_SERVER[PHP_SELF]" . $FORM['return_query_mark'] );
         exit;
      } else if ( $FORM['f'] == "swap" ) {
         $table_structure = mysql_table_structure( $mysql_connect_id, $table_name );
         $primary_key_field = ( !$primary_key_alternative ) ? mysql_primary_key_field( $table_structure ) : $primary_key_alternative;

         mysql_upd_record( $mysql_connect_id, $table_name, array( $FORM['field'] => $FORM['chg'] ), array( $primary_key_field => $FORM['pri'] ) );

         $FORM['return_query_mark'] = str_replace( "\n", "&", $FORM['return_query_mark'] );
         header( "Location: $_SERVER[PHP_SELF]" . $FORM['return_query_mark'] );
         #header( "Location: $_SERVER[PHP_SELF]" . str_replace( "\n", "&", $FORM['return_query'] ) );
         exit;
      } else if ( $FORM['f'] == "view" ) {
         if ( $FORM['submit'] == "Next Record" ) {
            
         }
         print "<pre>";
         print_r( $FORM );
         print "</pre>";
         exit;

      } else if ( $FORM['f'] == "goto" ) {
         
      }
   }
   $page_encoding = ( !$page_encoding ) ? "iso-8859-1" : $page_encoding;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Database Manager</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php print $page_encoding ?>">
<script language="JavaScript" type="text/javascript">
<!--
   function goto_url( theURL, message ) {
      if ( message == "" ) {
         message = "Are you sure you wish continue with this action?";
      }
      confirmation = confirm( message );
      if ( confirmation == 1 ) {
         top.location.href = theURL;
      }
   }

function confirm_action() {
   confirmation = confirm( 'Please confirm that you wish to delete the selected records.' );
   if ( confirmation == 1 ) {
      return true;
	   // document.myform.submit();
   }
   return false;
}

// http://www.shawnolson.net/a/639/select-all-checkboxes-in-a-form-with-javascript.html
function checkUncheckAll(theElement) {
   var theForm = theElement.form, z = 0;
   for(z=0; z<theForm.length;z++){
      if(theForm[z].type == 'checkbox' && theForm[z].name != 'checkall'){
         theForm[z].checked = theElement.checked;
      }
   }
}
-->
</script>
<link href="../site.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.arial_11px {
	font-family: Arial;
	font-size: 11px;
	line-height: normal;
}
.arial_10pt {
	font-family: Arial;
	font-size: 10pt;
	line-height: normal;
}
.verdana_10px {
	font-family: Verdana;
	font-size: 10px;
}
-->
</style>
</head>

<body>
<table width="760" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#0089B7">
   <tr> 
      <td height="50" align="center" bgcolor="#0099CC"><font size="6"><a href="<?php print $database_manager_link_url ?>" style="color: white">Database 
         Manager</a></font> &nbsp; &nbsp; &nbsp; <font color="#66FFFF" size="4">Database [<?php print $database_name ?>] 
         Table [<?php print $table_name ?>]</font></td>
  </tr>
  <tr> 
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td>&nbsp;</td>
        </tr>
<?php
   if ( !$FORM['f'] ) {
      $filter_operators = array( '=' => '', '!=' => '', '<' => '', '<=' => '', '>' => '', '>=' => '', 'regexp' => '~' );
      $current_page_query_string = preg_replace( "/^%3F/", "", htmlentities(urlencode($_SERVER['QUERY_STRING'])) );
      $current_page_query_string_for_link = $current_page_query_string;
      #$current_page_query_string_for_link = str_replace( "%26", "%0A", $current_page_query_string );
      #$current_page_query_string_for_link = str_replace( "%26", sprintf( "%02X", $query_and_replacement ) , $current_page_query_string );

      $table_structure = mysql_table_structure( $mysql_connect_id, $table_name );
      if ( $table_structure ) {
         $primary_key_field = ( !$primary_key_alternative ) ? mysql_primary_key_field( $table_structure ) : $primary_key_alternative;
   
         if ( !$main_menu_fields_to_display ) {
            #$main_menu_fields_to_display = $table_structure;
            $main_menu_fields_to_display = array_keys( $table_structure );
         } else {
            if ( is_string($main_menu_fields_to_display) ) {
               $main_menu_fields_to_display = preg_split( "/,\s*/", $main_menu_fields_to_display );
            }
            foreach ( $main_menu_fields_to_display as $field_name ) {
               # checks if all listed main menu fields are actually database fields
               if ( preg_match( "/\s+as\s+`?(\w+)`?$/", trim($field_name), $preg_result ) ) {
                  $renamed_fields[$preg_result[1]] = "";
               }

               if ( $ignore_field_check ) {
                  $new_field_list[] = $field_name;
               } else if ( preg_match( "/^\w+\s*\(.+\)(\s+as\s+\w+)?/", trim($field_name) ) ) {
                  # will handle mysql functions
                  $new_field_list[] = $field_name;
               } else if ( preg_match( "/^\w+\s+(\s+as\s+\w+)?/", trim($field_name) ) ) {
                  # will handle renamed fields
                  $new_field_list[] = $field_name;
               } else if ( $table_structure[$field_name] ) {
                  $new_field_list[] = $field_name;
               }
            }
            if ( !in_array($primary_key_field,$new_field_list) AND $primary_key_field ) {
               # Fixes problem when the primary key isnt displayed on the main menu
               $get_primary = ",`" . $primary_key_field . "`";
            }
            $main_menu_fields_to_display = $new_field_list;
         }
   
         if ( !array_key_exists($default_sort_field,$table_structure) ) {
            # Fixes problem when the $default_sort_field field is not found in the table
            $default_sort_field = "";
         }
   
         if ( !$FORM['sort_field'] AND $default_sort_string ) {
         } else if ( !$FORM['sort_field'] AND $default_sort_field ) {
            $FORM['sort_field'] = $default_sort_field;
         } else if ( !$FORM['sort_field'] ) {
            $FORM['sort_field'] = $primary_key_field;
            if ( !$FORM['sort_field'] ) {
               foreach ( $main_menu_fields_to_display as $key => $value ) {
                  $FORM['sort_field'] = $key;
                  break;
               }
            }
         }

         if ( $default_sort_string ) {
         } else if ( !$FORM['sort_type'] ) {
            if ( $default_sort_type ) {
               $FORM['sort_type'] = $default_sort_type;
            } else {
               $FORM['sort_type'] = "ASC";
            }
         }
   
         if ( $FORM['filter_field'] ) {
            $FORM['filter_data'] = mysql_real_escape_string( $FORM['filter_data'] );
            $where_clause["`$FORM[filter_field]` $FORM[filter_operator] '$FORM[filter_data]'"] = '';
         } else if ( $FORM['filter_operator'] AND $FORM['filter_data'] ) {
            foreach ( $table_structure as $key => $record ) {
               if ( $FORM['filter_operator'] == "=" ) {
                  # change single slash to double in order for like to handle it properly
                  $new_value = str_replace( chr(92), chr(92).chr(92), $FORM['filter_data'] );
                  $where_clause_filter[] = "`$key` LIKE \"%" . mysql_real_escape_string( $new_value ) . "%\"";
               } else {
                  $where_clause_filter[] = "`$key` $FORM[filter_operator] '" . mysql_real_escape_string( $FORM['filter_data'] ) . "'";
               }
            }
            $where_clause["( " . join( " OR ", $where_clause_filter ) . " )"] = '';
         }
         if ( $FORM['where_data'] ) {
            $where_clause = $FORM['where_data'];
         }
         $records_per_page = ( !$records_per_page ) ? 18 : $records_per_page;
   		$maximum_result_pages = 10;
         $FORM['start'] = ( $FORM['start'] == "" OR $FORM['start'] < 0 ) ? 0 : $FORM['start'];
         if ( strval($FORM['start']) == "all" ) {
            $FORM['start'] = 0;
            $records_per_page = 1000000;
         }
         $total_records = mysql_count_query_records( $mysql_connect_id, $table_name, $where_clause );
   		$total_records = ( $total_records == "" ) ? 0 : $total_records;
   		if ( count($main_menu_fields_to_display) ) {
   		   foreach ( $main_menu_fields_to_display as $key => $value ) {
   		      if ( !preg_match( "/\(|\)|\s+as\s+/i", $value ) ) {
   		         $main_menu_fields_to_display1[$key] = "`$value`";
   		      } else {
   		         $main_menu_fields_to_display1[$key] = $value;
   		         if ( preg_match( "/ as `?([\w]+)`?/i", $value, $preg_results1 ) ) {
   		            $main_menu_fields_to_display[$key] = $preg_results1[1];
   		         }
   		      }
   		   }
      		$main_menu_fields_to_display_string = join( ", ", $main_menu_fields_to_display1 );
      		#$main_menu_fields_to_display_string = "`" . join( "`,`", $main_menu_fields_to_display ) . "`";
   		}

         if ( !$FORM['sort_field'] AND !$FORM['sort_type'] ) {
            $sort_string = $default_sort_string;
         } else {
            $sort_string = "{$FORM['sort_field']} {$FORM['sort_type']}";
         }

         $records = mysql_extract_records_where( $mysql_connect_id, $table_name, $where_clause, $main_menu_fields_to_display_string . $get_primary, $sort_string, "$FORM[start], $records_per_page" );
   
   	   #$FORM['starting'] = ( !$total_records ) ? 0 : $FORM['start'] + 1;
   	   #$FORM['ending'] = $FORM['start'] + $records_per_page;
   	   #$FORM['ending'] = ( $FORM['ending'] > $total_records ) ? $total_records : $FORM['ending'];
   		#$total_pages = ceil( $total_records / $records_per_page );
   	   #$current_page = ceil( $FORM['starting'] / $records_per_page );
   		$starting = ( !$total_records ) ? 0 : $FORM['start'] + 1;
   		$ending = $FORM['start'] + $records_per_page;
   		$ending = ( $ending > $total_records ) ? $total_records : $ending;
   		$displaying = $ending - $starting + 1;
   		$total_pages = ceil( $total_records / $records_per_page );
   		$current_page = ceil( $starting / $records_per_page );
   		$total_pages = ( $total_pages ) ? $total_pages : 0;
   
         $main_menu_total_fields = count( $main_menu_fields_to_display );
   
         /* Showing <?php print $starting ?>-<?php print $ending ?> of <?php print $total_records ?> */
         /* [Page <?php print $current_page ?> of <?php print $total_pages ?>] */
         $query_string_extra = "sort_field=$FORM[sort_field]&sort_type=$FORM[sort_type]";
         $query_string_extra = preg_replace( "/[\w%]+=&+/", "", $query_string_extra . "&" );
         $query_string_extra = preg_replace( "/&+$/", "", $query_string_extra );
   
         $query_sorting_stripped_string = preg_replace( "/sort_(?:field|type)=[^&]*/", "", $_SERVER['QUERY_STRING'] );
         $query_sorting_stripped_string = preg_replace( "/&+/", "&", $query_sorting_stripped_string );
         $query_sorting_stripped_string = ( $query_sorting_stripped_string AND !preg_match("/^&/",$query_sorting_stripped_string) ) ? "&" . $query_sorting_stripped_string : $query_sorting_stripped_string;
      } else {
         $main_menu_fields_to_display = array();
         #$main_menu_total_fields = ;
      }
?>
        <tr> 
          <td><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
                     <tr bgcolor="#A8EAFF"> 
                        <td width="150" class="arial_11px">&nbsp;&nbsp;<?php if ( !$no_add ) { ?><a href="?f=add&return_query=<?php print $current_page_query_string ?>">Add Record</a><?php } ?></td>
                        <td align="center"><b>Main Menu <?php print $extra_menu_items ?></b></td>
                        <td width="150"></td>
                     </tr>
                     <tr> 
                        <td colspan="3"><img src="../images/non.gif" width="1" height="10"></td>
                     </tr>
                     <tr> 
                        <td colspan="3" align="center"><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" class="arial_10pt">
                              <form method="post" name="myform">
                              <tr height="20" bgcolor="#CEF3FF"> 
<?php
   if ( !$checkbox_column ) {
?>
                                 <td width="5" bgcolor="#CEF3FF"></td>
<?php
   } else {
?>
                                 <td width="25" align="center"><input name="checkall" type="checkbox" value="checkbox" onClick="checkUncheckAll(this)"></td>
<?php
   }
?>
<?php
   foreach ( $main_menu_fields_to_display as $field_name ) {
      $features = $field_features[$field_name];
      if ( $features['title_main'] OR $features['main_title'] ) {
         $display_field_name = ( $features['title_main'] ) ? $features['title_main'] : $features['main_title'];
      } else if ( $features['title'] ) {
         $display_field_name = $features['title'];
      } else if ( ($features['fkey'] OR $features['build_cat']) AND preg_match( "/(.*)_id$/i", $field_name, $preg_results ) ) {
         $display_field_name = convert_field_name( $preg_results[1] );
      } else {
         $display_field_name = convert_field_name( $field_name );
      }
//      if ( !$features['main_align'] ) {
//         $align = ( !$default_alignment ) ? ' align="center"' : ' align="' . $default_menu_alignment . '"';
      if ( $features['main_align'] ) {
         $align = " align=\"$features[main_align]\"";
      } else if ( $default_menu_alignment ) {
         $align = ' align="' . $default_menu_alignment . '"';
      } else if ( $default_alignment ) {
         $align = ' align="' . $default_alignment . '"';
      } else if ( $table_structure[$field_name]['type'] == "enum" ) {
         $align = " align=\"center\"";
      } else if ( preg_match( "/((tiny|small|medium|big)?int(eger)?|decimal|float|double|real)/", $table_structure[$field_name]['type'] ) ) {
         $align = " align=\"center\"";
      }

      $width = "";
      if ( $features['main_width'] ) {
         $width = " width=\"$features[main_width]\"";
      }
      $style = "";
      if ( $features['main_pad_left'] ) {
         $style = " style=\"padding-left: $features[main_pad_left]\"";
      } else if ( $features['main_pad_right'] ) {
         $style = " style=\"padding-right: $features[main_pad_right]\"";
      } else if ( $features['main_pad_lfrt'] ) {
         $style = " style=\"padding-left: $features[main_pad_lfrt]; padding-right: $features[main_pad_lfrt]\"";
      } else if ( $features['main_pad'] ) {
         $style = " style=\"padding: $features[main_pad]\"";
      }
?>
                                 <td<?php print $align ?><?php print $width ?><?php print $style ?>><b><a href="?sort_field=<?php print $field_name ?>&sort_type=ASC<?php print $query_sorting_stripped_string ?>">&#9650;</a><?php print $display_field_name ?><a href="?sort_field=<?php print $field_name ?>&sort_type=DESC<?php print $query_sorting_stripped_string ?>">&#9660;</a></b></td>
                                 <?php
   }
   if ( $table_structure ) {
      if ( !$no_function_column ) {
?>
                                 <td align="center" style="padding: 0 5px"><b>Functions</b></td>
<?php
      }
   } else {
?>
                                 <td align="center"><font color="red">
                                    <h1>Table Not Found</h1>
                                    </font></td>
                                 <?php
   }
?>
                                 <td width="5"></td>
                              </tr>
                              <tr> 
                                 <td colspan="<?php print $main_menu_total_fields+3 ?>" bgcolor="#0099CC"><img src="../images/non.gif" width="1" height="1"></td>
                              </tr>
                              <?php
      if ( $records ) {
         $view_icon = "[V]";
         $edit_icon = "[E]";
         $duplicate_icon = "[C]";
         $delete_icon = "[X]";
         if ( file_exists( "icons/view.gif" ) ) {
            $view_icon = "<img src='icons/view.gif' border='0' align='absmiddle'>";
         }
         if ( file_exists( "icons/edit.gif" ) ) {
            $edit_icon = "<img src='icons/edit.gif' border='0' align='absmiddle'>";
         }
         if ( file_exists( "icons/copy.gif" ) ) {
            $duplicate_icon = "<img src='icons/copy.gif' border='0' align='absmiddle'>";
         }
         if ( file_exists( "icons/delete.gif" ) ) {
            $delete_icon = "<img src='icons/delete.gif' border='0' align='absmiddle'>";
         }
         foreach ( $records as $row_number => $record ) {
            $enable_or_not = ""; $original_record = $record;
            if ( !preg_match( "/y/i", $record[$checkbox_column_enable_field] ) AND $checkbox_column_enable_field ) {
               $enable_or_not = "#DDDDDD";
            } else if ( !preg_match( "/y/i", $record[$enabled_field_name] ) AND $enabled_field_name ) {
               $enable_or_not = "#DDDDDD";
            }
?>
                              <tr bgcolor="<?php print $enable_or_not ?>" height="25"> 
<?php
   if ( !$checkbox_column ) {
?>
                                 <td></td>
<?php
   } else {
?>
                                 <td align="center"><input name="selected_records[]" type="checkbox" id="checkbox<?php print $row_number ?>" value="<?php print $record[$primary_key_field] ?>"></td>
<?php
   }
?>
<?php
            foreach ( $main_menu_fields_to_display as $field_name ) {
               $align = "";
               $features = $field_features[$field_name];

//               if ( !$features['main_align'] ) {
//                  $align = ( !$default_alignment ) ? ' align="center"' : ' align="' . $default_menu_alignment . '"';
               if ( $features['main_align'] ) {
                  $align = " align=\"$features[main_align]\"";
               } else if ( $default_alignment ) {
                  $align = ' align="' . $default_alignment . '"';
               } else if ( preg_match("/zerofill/",$table_structure[$field_name]['others']) ) {
                  $align = " align=\"center\"";
               #} else if ( $table_structure[$field_name]['type'] == "enum" AND join("",$table_structure[$field_name]['size']) == "YN" ) {
               } else if ( $table_structure[$field_name]['type'] == "enum" OR $table_structure[$field_name]['type'] == "set" ) {
                  $align = " align=\"center\"";
               } else if ( preg_match( "/((tiny|small|medium|big)?int(eger)?|decimal|float|double|real)/", $table_structure[$field_name]['type'] ) ) {
                  $align = " align=\"right\" style=\"padding-right: 10px\"";
               }
               #print_r2( $table_structure, 1 );

               if ( $features['main_edit'] ) {
                  $features['form_field_group_name'] = "fieldz";
?>
                                    <form method="post" name="myform">
                                     <td<?php print $align ?><?php print $style ?>><?php print mysql_generate_form_field( $mysql_connect_id, $table_name, $field_name, $features, $record[$field_name], $table_structure, 1 ) ?></td>
                                     <input name="f" type="hidden" value="edit"><input name="submit" type="hidden" value="1">
                                     <input name="pri_value" type="hidden" value="<?php print $record[$primary_key_field] ?>"><input name="return_query" type="hidden" id="return_query" value="<?php print $_SERVER['QUERY_STRING'] ?>">
                                    </form>
<?php
               } else {
               
               if ( $features['fkey'] ) {
//                  if ( is_string($features['fkey']) ) {
//                     $fkey_fields = explode( ",", $features['fkey'] );
//                     $record[$field_name] = mysql_get_foreign_key_text( $mysql_connect_id, $fkey_fields[0], $fkey_fields[1], array( $fkey_fields[2] => $record[$field_name] ), $fkey_fields[3] );
//                  } else {
//                     $record[$field_name] = mysql_get_foreign_key_text( $mysql_connect_id, $features['fkey']['DB'], $features['fkey']['TB'], array( $features['fkey']['FIELD_KEY'] => $record[$field_name] ), $features['fkey']['FIELD_DISPLAY'] );
//                  }
                  if ( !$reference_array[$field_name] ) {
                     if ( is_string($features['fkey']) ) {
                        $fkey_fields = explode( ",", $features['fkey'] );
                        if ( !$fkey_fields[3] ) $fkey_fields[3] = $fkey_fields[2];
                        $reference_array[$field_name] = mysql_get_foreign_key_list( $mysql_connect_id, $fkey_fields[0], $fkey_fields[1], $fkey_fields[2], $fkey_fields[3], $fkey_fields[4], $fkey_fields[5] );
                     } else {
                        if ( !$features['fkey']['FIELD_DISPLAY'] ) $features['fkey']['FIELD_DISPLAY'] = $features['fkey']['FIELD_KEY'];
                        $reference_array[$field_name] = mysql_get_foreign_key_list( $mysql_connect_id, $features['fkey']['DB'], $features['fkey']['TB'], $features['fkey']['FIELD_KEY'], $features['fkey']['FIELD_DISPLAY'], $features['fkey']['WHERE_CLAUSE'], $features['fkey']['ORDER_BY'] );
                     }
                  }
                  $multiple_values = explode( ",", $record[$field_name] );
                  $multiple_results = array();
                  foreach ( $multiple_values as $value ) {
                     $multiple_results[] = $reference_array[$field_name][$value];
                  }
                  $record[$field_name] = join( ", ", $multiple_results );
               } else if ( $features['build_cat'] ) {
                  if ( !$reference_array[$field_name] ) {
                     $field_properties = $features;
                     if ( $field_properties['build_cat']['fkey'] ) {
                        $field_properties['fkey'] = $field_properties['build_cat']['fkey'];
                        if ( is_string($field_properties['fkey']) ) {
                           $fkey_fields = explode( ",", $field_properties['fkey'] );
                           if ( !$fkey_fields[3] ) $fkey_fields[3] = $fkey_fields[2];
                           $field_properties['fkey'] = array( 'DB' => $fkey_fields[0], 'TB' => $fkey_fields[1], 'FIELD_KEY' => $fkey_fields[2], 'FIELD_DISPLAY' => $fkey_fields[3], 'WHERE_CLAUSE' => $fkey_fields[4], 'ORDER_BY' => $fkey_fields[5] );
                        }
                        if ( $field_properties['fkey']['FIELDS'] ) {
                           $fields = $field_properties['fkey']['FIELDS'];
                        } else {
                           if ( !$field_properties['fkey']['FIELD_DISPLAY'] ) $field_properties['fkey']['FIELD_DISPLAY'] = $field_properties['fkey']['FIELD_KEY'];
                           $fields = $field_properties['fkey']['FIELD_KEY'] . "," . $field_properties['fkey']['FIELD_DISPLAY'];
                           if ( $fields == "id,name" ) {
                              $fields = "id,parent_id,name";
                           } else if ( $fields == "code,name" ) {
                              $fields = "code,parent_code,name";
                           }
                        }
         
                        if ( $field_properties['fkey']['OPTIONS'] ) {
                           $options = $field_properties['fkey']['OPTIONS'];
                        } else if ( $field_properties['fkey']['FIELD_KEY'] == "code" ) {
                           $options = array( 'use_codes' => "Y" );
                        }
         
                        $field_properties['build_cat'] = array( 'DB' => $field_properties['fkey']['DB'], 'TB' => $field_properties['fkey']['TB'], 'FIELDS' => $fields, 'WHERE_CLAUSE' => $field_properties['fkey']['WHERE_CLAUSE'], 'ORDER_BY' => $field_properties['fkey']['ORDER_BY'], 'SEPARATOR' => "||", 'INDENT' => 1, 'OPTIONS' => $options );
                        #print_r2( $field_properties['build_cat'], 1 );
                     }
   
                     $database = $field_properties['build_cat']['DB'];
                     if ( $database ) {
                        $current_db = mysql_current_databse( $mysql_connect_id );
                        if ( $current_db != $database ) {
                           mysql_change_db( $mysql_connect_id, $database );
                        } else {
                           $database = "";
                        }
                     }

                     $category_records = mysql_make_assoc_array( $mysql_connect_id, $field_properties['build_cat']['TB'], $field_properties['build_cat']['FIELDS'], $field_properties['build_cat']['WHERE_CLAUSE'], $field_properties['build_cat']['ORDER_BY'] );
                     if ( $database ) {
                        mysql_change_db( $mysql_connect_id, $current_db );
                     }
                     $name_field = ( $field_properties['build_cat']['OPTIONS']['alternative_field_names']['name'] ) ? $field_properties['build_cat']['OPTIONS']['alternative_field_names']['name'] : "name";
                     foreach ( $category_records as $key => $array ) {
                        $category_records_new[$key] = $array[$name_field];
                     }
                     #$category_records_new = build_category_paths( $category_records, "\\", 0, $field_properties['build_cat']['OPTIONS'] );
                     $reference_array[$field_name] = $category_records_new;
                  }
                  $multiple_values = explode( ",", $record[$field_name] );
                  $multiple_results = array();
                  foreach ( $multiple_values as $value ) {
                     $multiple_results[] = $reference_array[$field_name][$value];
                  }
                  $record[$field_name] = join( ", ", $multiple_results );
               } else if ( isset($features['enum_main']) ) {
                  if ( !$features['enum_main'] ) {
                  } else {
                     $record[$field_name] = $features['enum_main'][$record[$field_name]];
                  }
               } else if ( $features['enum'] ) {
                  $record[$field_name] = $features['enum'][$record[$field_name]];
               }

               if ( $features['main_preg_replace'] ) {
                  $record[$field_name] = preg_replace( $features['main_preg_replace'][0], $features['main_preg_replace'][1], $record[$field_name] );
               }
               if ( $features['main_trim'] ) {
                  $record[$field_name] = shorten_string( $record[$field_name], $features['main_trim'] );
               }
               if ( $features['main_trim_crop'] ) {
                  if ( strlen($record[$field_name]) > $features['main_trim_crop'] ) {
                     $record[$field_name] = substr( $record[$field_name], 0, $features['main_trim_crop'] );
                  }
               }
               if ( $features['main_trim_crop2'] ) {
                  if ( strlen($record[$field_name]) > $features['main_trim_crop2'] ) {
                     $record[$field_name] = trim( substr( $record[$field_name], 0, $features['main_trim_crop2'] ) ) . "...";
                  }
               }
               if ( $features['main_crop_right'] ) {
                  $record[$field_name] = substr( $record[$field_name], -$features['main_crop_right'] );
               }
               if ( $features['main_set'] ) {
                  if ( gettype($record[$field_name]) == "NULL" ) {
                     $record[$field_name] = "[NULL]";
                  } else if ( $record[$field_name] == "" ) {
                     $record[$field_name] = "[EMPTY]";
                  } else {
                     $record[$field_name] = "[SET]";
                  }
               }

               if ( $features['main_url'] AND $record[$field_name] ) {
                  $record[$field_name] = $features['main_url_base'] . $record[$field_name];
                  $record[$field_name] = "<a href='$record[$field_name]' target='_blank'>URL</a>";
               }

               if ( $enabled_field_name == $field_name ) {
                  if ( !preg_match( "/y/i", $record[$field_name] ) ) {
                     $record[$field_name] = "<a href='?f=swap&pri=" . $record[$primary_key_field] . "&field=$field_name&chg=Y&return_query=$current_page_query_string_for_link&submit=y'><img src=\"icons/yellow.png\" border=\"0\" align='absmiddle'></a>";
                  } else {
                     $record[$field_name] = "<a href='?f=swap&pri=" . $record[$primary_key_field] . "&field=$field_name&chg=N&return_query=$current_page_query_string_for_link&submit=y'><img src=\"icons/green.png\" border=\"0\" align='absmiddle'></a>";
                  }
               } else if ( $table_structure[$field_name]['type'] == "enum" AND $main_menu_enum_swap == $field_name ) {
                  if ( preg_match( "/^[yn]{2}$/i", join( "", $table_structure[$field_name]['size'] ) ) ) {
                     reset( $table_structure[$field_name]['size'] );
                     $first_value = current( $table_structure[$field_name]['size'] );
                     $second_value = end( $table_structure[$field_name]['size'] );
                     $new_value = ( $record[$field_name] == $first_value ) ? $second_value : $first_value;
                     $record[$field_name] .= " <a href='?f=swap&pri=" . $record[$primary_key_field] . "&field=$field_name&chg=$new_value&return_query=$current_page_query_string_for_link&submit=y'><img src=\"icons/swap.png\" border=\"0\" align='absmiddle'></a>";
                  }
               } else if ( $table_structure[$field_name]['type'] == "enum" AND $features['rotate_enum'] ) {
                  $enum_values = $table_structure[$field_name]['size'];
                  if ( !intval($features['rotate_enum']) ) $features['rotate_enum'] = 1;
                  $enum_value = array_search( $record[$field_name], $enum_values ) + $features['rotate_enum'];
                  if ( $enum_value == 0 ) $enum_value = count($enum_values);
                  if ( !$enum_values[$enum_value] ) $enum_value = 1;
                  $new_value = $enum_values[$enum_value];
                  $record[$field_name] .= " <a href='?f=swap&pri=" . $record[$primary_key_field] . "&field=$field_name&chg=$new_value&return_query=$current_page_query_string_for_link&submit=y'><img src=\"icons/swap.png\" border=\"0\" align='absmiddle'></a>";
                  #$record[$field_name] = "<a href='?f=swap&pri=" . $record[$primary_key_field] . "&field=$field_name&chg=$new_value&return_query=$current_page_query_string_for_link&submit=y'>{$record[$field_name]}</a>";
               }
               $display_value = $record[$field_name];
               if ( $features['print_prefix_value'] ) {
                  $display_value = $features['print_prefix_value'] . $display_value;
               }
               $style = "";
               if ( $features['main_pad_left'] ) {
                  $style = " style=\"padding-left: $features[main_pad_left]\"";
               } else if ( $features['main_pad_right'] ) {
                  $style = " style=\"padding-right: $features[main_pad_right]\"";
               } else if ( $features['main_pad_lfrt'] ) {
                  $style = " style=\"padding-left: $features[main_pad_lfrt]; padding-right: $features[main_pad_lfrt]\"";
               } else if ( $features['main_pad'] ) {
                  $style = " style=\"padding: $features[main_pad]\"";
               }
               if ( $main_menu_clickable_fields[$field_name] ) {
                  if ( preg_match( "/^view|edi?t$/i", $main_menu_clickable_fields[$field_name] ) ) {
                     $click_url = $_SERVER['PHP_SELF'] . "?f=" . strtolower($main_menu_clickable_fields[$field_name]) . "&pri=$record[$primary_key_field]&return_query=$current_page_query_string_for_link";
                  } else {
                     $click_url = str_replace( "[value]", $record[$field_name], $main_menu_clickable_fields[$field_name] );
                     preg_match_all( "/field\[(.*?)\]/", $main_menu_clickable_fields[$field_name], $preg_results2, PREG_SET_ORDER );
                     foreach ( $preg_results2 as $array2 ) {
                        $click_url = str_replace( $array2[0], $original_record[$array2[1]], $click_url );
                     }
                  }
?>
                                 <td<?php print $align ?><?php print $style ?>><a href='<?php print $click_url ?>'><?php print $display_value ?></a></td>
<?php
               } else if ( $field_name == $primary_key_field OR $field_name == $main_menu_clickable_field ) {
                  $function_type = ( $no_edit ) ? "view" : "edit";
?>
                                 <td<?php print $align ?><?php print $style ?>><a href='<?php print $_SERVER['PHP_SELF'] ?>?f=<?php print $function_type ?>&pri=<?php print $record[$primary_key_field] ?>&return_query=<?php print $current_page_query_string_for_link ?>'><?php print $display_value ?></a></td>
<?php
               } else {
?>
                                 <td<?php print $align ?><?php print $style ?>><?php print $display_value ?></td>
                                 <?php
               }
               }
            }
?>
<?php
   if ( !$no_function_column ) {
?>
                                 <td align="center" nowrap>
                                    <?php
   if ( !$no_view AND !$no_view_button ) {
?>
                                    <a href="<?php print $_SERVER['PHP_SELF'] ?>?f=view&pri=<?php print $record[$primary_key_field] ?>&return_query=<?php print $current_page_query_string_for_link ?>" title="View"><?php print $view_icon ?></a> 
                                    <?php
   }
   if ( !$no_edit AND !$no_edit_button ) {
?>
                                    <a href="<?php print $_SERVER['PHP_SELF'] ?>?f=edt&pri=<?php print $record[$primary_key_field] ?>&return_query=<?php print $current_page_query_string_for_link ?>" title="Edit"><?php print $edit_icon ?></a> 
                                    <?php
   }
   if ( !$no_add AND !$no_duplicate ) {
?>
                                    <a href="<?php print $_SERVER['PHP_SELF'] ?>?f=add&pri=<?php print $record[$primary_key_field] ?>&return_query=<?php print $current_page_query_string_for_link ?>" title="Duplicate"><?php print $duplicate_icon ?></a> 
                                    <?php
   }
   if ( !$no_delete ) {
?>
                                    <a href="javascript:goto_url('<?php print $_SERVER['PHP_SELF'] ?>?f=del&submit=submit&pri=<?php print $record[$primary_key_field] ?>&return_query=<?php print urlencode(str_replace( "%26", "%0A", $current_page_query_string_for_link)) ?>','Are you sure you wish to delete this record?')" title="Delete"><?php print $delete_icon ?></a> 
                                    <?php
   }
?>
                                 </td>
<?php
   }
?>
                                 <td></td>
                              </tr>
                              <tr> 
                                 <td colspan="<?php print $main_menu_total_fields+3 ?>" bgcolor="#0099CC"><img src="../images/non.gif" width="1" height="1"></td>
                              </tr>
<?php
         }

         if ( $checkbox_column ) {
?>
                              <tr> 
                                 <td colspan="<?php print $main_menu_total_fields+3 ?>" height="25">
											<input name="submit" type="submit" class="arial_11px" value="Delete" onClick="return confirm_action()">
<?php
            if ( $enabled_field_name OR $checkbox_column_enable_field ) {
?>

											<input name="submit" type="submit" class="arial_11px" value="Enable">
											<input name="submit" type="submit" class="arial_11px" value="Disable">
<?php
            }
?>
                                 <input name="f" type="hidden" id="f" value="chg_status">
                                 <input name="return_query" type="hidden" value="<?php print $_SERVER['QUERY_STRING'] ?>"></td>
                              </tr>
<?php
         }
      } else {
?>
                              <tr> 
                                 <td height="25" align="center" colspan="<?php print $main_menu_total_fields+3 ?>">No Records Found</td>
                              </tr>
                              <tr> 
                                 <td colspan="<?php print $main_menu_total_fields+3 ?>" bgcolor="#0099CC"><img src="../images/non.gif" width="1" height="1"></td>
                              </tr>
<?php
      }

      if ( $table_structure ) {
         foreach ( $table_structure as $key => $value ) {
            $field_names[$key] = '';
         }
      }
?>
                              </form>
                           </table>
                           <table width="95%" border="0" cellpadding="0" cellspacing="0" class="arial_10pt">
                              <tr> 
                                 <td colspan="2"><img src="../images/non.gif" width="1" height="10"></td>
                              </tr>
                              <tr> 
                                 <form>
                                    <td>Quick Filter 
                                       <?php if ( $FORM['filter_operator'] ) { ?>
                                       <span class="arial_11px">[<a href="<?php print $_SERVER['PHP_SELF']; ?>?filter_data=<?php print $FORM['filter_data'] ?>">Clear Filter</a>]</span>
                                       <?php } ?>
                                       <br> <select name="filter_field">
                                          <option value="">Field Name</option>
                                          <?php print associate_array_to_html_options( $field_names, $FORM['filter_field'] ) ?> </select> <select name="filter_operator" id="filter_operator">
                                          <?php print associate_array_to_html_options( $filter_operators, $FORM['filter_operator'] ) ?> </select> <input name="filter_data" type="text" id="filter_data" size="15" value="<?php print $FORM['filter_data'] ?>"> <input type="submit" value="Go"></td>
                                 </form>
                                 <form>
                                    <td align="right">Where Statement<br> <input name="where_data" type="text" id="where_data" value="<?php print htmlspecialchars( $FORM['where_data'] ) ?>"> <input type="submit" value="Go"></td>
                                 </form>
                              </tr>
                           </table></td>
                     </tr>
                     <tr> 
                        <td colspan="3"><img src="../images/non.gif" width="1" height="10"></td>
                     </tr>
                  </table>
                  <table width="95%" height="20" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#E0E0E0" class="arial_11px">
                     <tr> 
                        <td width="150">&nbsp;&nbsp;<?php if ( !$no_add ) { ?><a href="?f=add&return_query=<?php print $current_page_query_string ?>">Add Record</a><?php } ?></td>
                        <td align="center" class="verdana_10px">[Page <?php print $current_page ?> of <?php print $total_pages ?>]<br>
<?php
         $_SERVER['QUERY_STRING'] = preg_replace( "/start=\d*/i", "", $_SERVER['QUERY_STRING'] );
         $_SERVER['QUERY_STRING'] = preg_replace( "/&+/i", "&", $_SERVER['QUERY_STRING'] );
         $_SERVER['QUERY_STRING'] = ( $_SERVER['QUERY_STRING'] AND !preg_match("/^&/",$_SERVER['QUERY_STRING']) ) ? "&" . $_SERVER['QUERY_STRING'] : $_SERVER['QUERY_STRING'];

         /* if ( $total_pages != 1 ) {
      		if ( !$FORM['start'] ) {
      			print "[<b>Previous</b>]";
      		} else {
      			print "[<a href=\"?$_SERVER[QUERY_STRING]&start=" . ( $FORM['start'] - $records_per_page ) . "\">Previous</a>]";
   	   	}
      	   $y = 0;
      	   for ( $x = 0; $x < $total_records; $x += $records_per_page ) {
      	      $y++;
         	   if ( $x == $FORM['start'] ) {
            	   print " [<b>$y</b>] ";
               	$current_page = $y;
      	      } else {
         	      print " [<a href=\"?$_SERVER[QUERY_STRING]&start=$x\">$y</a>] ";
            	}
      	   }

      	   if ( $current_page == $y ) {
         	   print "[<b>Next</b>]";
      	   } else {
      	      print "[<a href=\"?$_SERVER[QUERY_STRING]&start=" . ( $FORM['start'] + $records_per_page ) . "\">Next</a>]";
         	}
         } */

      $add_on = $_SERVER['QUERY_STRING'];
		if ( $FORM['start'] == 0 ) {
			print "[<b>Prev</b>]";
		} else {
			$previous_link_start_value = $FORM['start'] - $records_per_page;
			$previous_link_start_value = ( $previous_link_start_value < 0 ) ? 0 : $previous_link_start_value;
         print "[<a href=\"$_SERVER[SCRIPT_NAME]?$add_on\">Beg</a>] ";
			print "[<a href=\"$_SERVER[SCRIPT_NAME]?start=$previous_link_start_value$add_on\">Prev</a>]";
		}
	
		if ( ($maximum_result_pages%2) ) {
			$maximum_result_pages_before = ceil( $maximum_result_pages / 2 );
			$maximum_result_pages_after = ceil( $maximum_result_pages / 2 );
		} else {
			$maximum_result_pages_before = floor( $maximum_result_pages / 2 );
			$maximum_result_pages_after = ceil( $maximum_result_pages / 2 );
		}
	
		$start_number = 0;
		if ( $current_page > $maximum_result_pages_before ) {
			$start_number = $FORM['start'] - ( $records_per_page * $maximum_result_pages_before );
			$end_number = $FORM['start'] + ( $records_per_page * $maximum_result_pages_after );
			if ( $start_number < 0 ) {
				$start_number = 0;
				$end_number = $records_per_page * $maximum_result_pages;
			}
			if ( $end_number > $total_records ) {
				$end_number = $total_records;
				$start_number = ( $total_pages - $maximum_result_pages ) * $records_per_page;
				if ( $start_number < 0 ) {
					$start_number = 0;
				}
			}
		} else {
			$end_number = $records_per_page * $maximum_result_pages;
			if ( $end_number > $total_records ) {
				$end_number = $total_records;
			}
		}
	
		for ( $x = $start_number; $x < $end_number; $x += $records_per_page ) {
			$page_number = floor( $x / $records_per_page ) + 1;
			if ( $current_page == $page_number ) {
				$page_numbers[] = "<b>$page_number</b>";
			} else {
				$page_numbers[] = "<a href=\"$_SERVER[SCRIPT_NAME]?start=$x$add_on\">$page_number</a>";
			}
		}
		if ( $page_numbers ) {
		   print " &nbsp;" . join( " &nbsp; ", $page_numbers ) . "&nbsp; ";
		} else {
		   print " No Pages ";
		}

		if ( $current_page == $total_pages ) {
			print "[<b>Next</b>]";
		} else {
			print "[<a href=\"$_SERVER[SCRIPT_NAME]?start=" . ( $FORM['start'] + $records_per_page ) . "$add_on\">Next</a>]";
			print " [<a href=\"$_SERVER[SCRIPT_NAME]?start=" . ( ( $total_pages - 1 ) * $records_per_page ) . "$add_on\">End</a>]";
			print " [<a href=\"$_SERVER[SCRIPT_NAME]?start=all$add_on\">All</a>]";
		}
?>
                </td>
						<td width="150" align="right">Records <?php print $starting ?>-<?php print $ending ?> of <?php print $total_records ?>&nbsp;&nbsp;</td>
					</tr>
				</table></td>
        </tr>
<?php
   } else if ( $FORM['f'] == "add" ) {
      #$return_to_menu_query_fields = array( "start", "sort_field", "sort_type" );
      $return_to_menu_url = ( $FORM['return_query'] ) ? $_SERVER['PHP_SELF'] . "?" . $FORM['return_query'] : $_SERVER['PHP_SELF'];

      $table_structure = mysql_table_structure( $mysql_connect_id, $table_name );
      if ( $FORM['pri'] ) {
         # fills the add record form with data found in another record [duplicates record]
         $primary_key_field = ( !$primary_key_alternative ) ? mysql_primary_key_field( $table_structure ) : $primary_key_alternative;
         $record = mysql_extract_record_id( $mysql_connect_id, $table_name, array( $primary_key_field => $FORM['pri'] ) );
         if ( preg_match( "/auto_increment/", $table_structure[$primary_key_field]['extra'] ) ) {
            $record[$primary_key_field] = "";
         }
      } else if ( $FORM['fieldz'] ) {
         $record = $FORM['fieldz'];
      } else if ( $FORM['add_fields'] ) {
         # fills the add record form with data found in the query
         foreach ( $FORM['add_fields'] as $key => $value ) {
            if ( array_key_exists($key,$table_structure) ) {
               $record[$key] = $value;
            }
         }
      } else if ( $field_features ) {
         foreach ( $field_features as $field_name => $features ) {
            if ( $field_features[$field_name]['default'] ) {
               $record[$field_name] = $field_features[$field_name]['default'];
            }
         }
      }

      if ( $field_features ) {
         foreach ( $field_features as $field_name => $features ) {
            if ( $field_features[$field_name]['fkey'] OR $field_features[$field_name]['build_cat'] ) {
               $fkey_used = 1;
               break;
            }
         }
      }

      if ( is_string($fields_to_display) ) {
         $fields_to_display = preg_split( "/\s*,\s*/", trim($fields_to_display) );
      }

      if ( $fields_not_to_display ) {
         $fields_to_display = array_keys( $table_structure );
         foreach ( $fields_not_to_display as $value ) {
            $find_key = array_search( $value, $fields_to_display );
            if ( $find_key !== False ) {
               unset( $fields_to_display[$find_key] );
            }
         }
      }
      if ( $FORM['full_rec'] == "y" ) {
         $fields_to_display = array();
      }
      if ( $FORM['fkey'] == "off" ) {
         foreach ( $field_features as $field_name => $features ) {
            $field_features[$field_name]['fkey'] = "";
            $field_features[$field_name]['build_cat'] = "";
         }
      }
      if ( $FORM['add_close'] ) {
         $form_properties['submit_button_title'] = "Add and Close";
      } else {
         $form_properties['other_submit_buttons'] = "Add and New,Add and Duplicate";
      }
      $form_properties['table_class'] = 'arial_10pt';

      $generated_form = mysql_generate_form( $mysql_connect_id, $table_name, $fields_to_display, $field_features, $record, $form_properties );
      #if ( !preg_match( "/Opera[ \/]?\d+\.\d+/", $_SERVER['HTTP_USER_AGENT'] ) ) {
      if ( preg_match( "/Gecko|Firefox/i", $_SERVER['HTTP_USER_AGENT'] ) AND !preg_match( "/Opera[ \/]?\d+\.\d+/", $_SERVER['HTTP_USER_AGENT'] ) ) {
         preg_match_all( "/(<textarea name=\"[^\"]+\" cols=\"[^\"]+\" rows=\")([^\"]+)(\"[^>]*>)/", $generated_form, $preg_results, PREG_SET_ORDER );
         foreach ( $preg_results as $array ) {
            $generated_form = str_replace( $array[0], $array[1].($array[2]-1). $array[3], $generated_form );
         }
      }
?>
        <tr> 
          <td><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr> 
                <td align="center" bgcolor="#A8EAFF"><b><a href="<?php print $_SERVER['PHP_SELF'] ?>" style="color: black">Main Menu</a> [Add Record]</b></td>
              </tr>
              <tr> 
                <td><img src="../images/non.gif" width="1" height="5"></td>
              </tr>
<?php
   if ( $update_error_message ) {
?>
              <tr> 
                <td align="center"><?php print $update_error_message ?></td>
              </tr>
              <tr> 
                <td><img src="../images/non.gif" width="1" height="5"></td>
              </tr>
<?php
   }
?>
              <tr> 
                <td><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
                     <form name="form1" method="post" action="<?php print $_SERVER['PHP_SELF'] ?>">
                      <tr> 
                        <td class="arial_10pt"><?php print $generated_form ?> 
                          <input name="f" type="hidden" value="add">
                          <input name="return_query" type="hidden" value="<?php print $FORM['return_query'] ?>">
                          <input name="fkey" type="hidden" value="<?php print $FORM['fkey'] ?>">
                          <input name="add_close" type="hidden" value="<?php print $FORM['add_close'] ?>">
                          <input name="p_execute" type="hidden" value="<?php print $FORM['p_execute'] ?>">
<?php
   if ( $FORM['fieldz'] ) {
   foreach ( $FORM['fieldz'] as $key => $value ) {
      if ( in_array($key,$fields_not_to_display) ) {
?>
                          <input name="fieldz[<?php print $key ?>]" type="hidden" value="<?php print $value ?>">
<?php
      }
   }
   }
?>
                         </td>
                      </tr>
<?php
   if ( $FORM['fkey'] != "off" AND $fkey_used ) {
?>
                      <tr> 
                        <td align="center"><a href="<?php print $_SERVER['REQUEST_URI'] ?>&fkey=off">Turn Off [Foreign Key] Fields</a></td>
                      </tr>
<?php
   }
?>
<?php
   if ( !$FORM['full_rec'] AND count($fields_to_display) ) {
?>
								<tr> 
									<td align="center"><a href="<?php print $_SERVER['REQUEST_URI'] ?>&full_rec=y">Show Full Record</a></td>
								</tr>
<?php
   }
?>
                    </form>
                  </table>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td><img src="../images/non.gif" width="1" height="5"></td>
							</tr>
							<tr>
								<form action="<?php print $return_to_menu_url ?>" method="post">
								<td align="center"><input type="submit" id="submit" value="Return to Menu"></td>
								</form>
							</tr>
						</table></td>
              </tr>
            </table></td>
        </tr>
<?php
   } else if ( $FORM['f'] == "edt" OR $FORM['f'] == "edit" ) {
      $table_structure = mysql_table_structure( $mysql_connect_id, $table_name );
      $primary_key_field = ( !$primary_key_alternative ) ? mysql_primary_key_field( $table_structure ) : $primary_key_alternative;
      $return_to_menu_url = ( $FORM['return_query'] ) ? $_SERVER['PHP_SELF'] . "?" . $FORM['return_query'] : $_SERVER['PHP_SELF'];

      $record = mysql_extract_record_id( $mysql_connect_id, $table_name, array( $primary_key_field => $FORM['pri'] ) );

      if ( $field_features ) {
         foreach ( $field_features as $field_name => $features ) {
            if ( $field_features[$field_name]['fkey'] OR $field_features[$field_name]['build_cat'] ) {
               $fkey_used = 1;
               break;
            }
         }
      }

      if ( $fields_not_to_display ) {
         $fields_to_display = array_keys( $table_structure );
         foreach ( $fields_not_to_display as $value ) {
            $find_key = array_search( $value, $fields_to_display );
            if ( $find_key !== False ) {
               unset( $fields_to_display[$find_key] );
            }
         }
      }
      if ( $FORM['full_rec'] == "y" ) {
         $fields_to_display = array();
      }
      if ( $FORM['fkey'] == "off" ) {
         foreach ( $field_features as $field_name => $features ) {
            $field_features[$field_name]['fkey'] = "";
            $field_features[$field_name]['build_cat'] = "";
         }
      }

      $form_properties['table_class'] = "arial_10pt";
      if ( $FORM['add_close'] ) {
         $form_properties['submit_button_title'] = "Edit and Close";
      } else {
         $form_properties['other_submit_buttons'] = "Edit and Close";
      }
      $generated_form = mysql_generate_form( $mysql_connect_id, $table_name, $fields_to_display, $field_features, $record, $form_properties, 1 );
      if ( preg_match( "/Gecko|Firefox/i", $_SERVER['HTTP_USER_AGENT'] ) AND !preg_match( "/Opera[ \/]?\d+\.\d+/", $_SERVER['HTTP_USER_AGENT'] ) ) {
         preg_match_all( "/(<textarea name=\"[^\"]+\" cols=\"[^\"]+\" rows=\")([^\"]+)(\"[^>]*>)/", $generated_form, $preg_results, PREG_SET_ORDER );
         foreach ( $preg_results as $array ) {
            $generated_form = str_replace( $array[0], $array[1].($array[2]-1). $array[3], $generated_form );
         }
      }
?>
        <tr> 
          <td><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr> 
               <td align="center" bgcolor="#A8EAFF"><b><a href="<?php print $_SERVER['PHP_SELF'] ?>" style="color: black">Main Menu</a> [Edit Record]</b></td>
              </tr>
              <tr> 
                <td><img src="../images/non.gif" width="1" height="5"></td>
              </tr>
              <tr> 
                <td><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <form name="form1" method="post" action="<?php print $_SERVER['PHP_SELF'] ?>">
							<tr> 
								<td><?php print $generated_form ?> <input name="f" type="hidden" value="edt"> <input name="pri_value" type="hidden" value="<?php print $record[$primary_key_field] ?>"> 
									<input name="return_query" type="hidden" id="return_query" value="<?php print $FORM['return_query'] ?>"> <input name="p_execute" type="hidden" value="<?php print $FORM['p_execute'] ?>"></td>
							</tr>
							</form>
<?php
   if ( $FORM['fkey'] != "off" AND $fkey_used ) {
?>
								<tr> 
									<td align="center"><a href="<?php print $_SERVER['REQUEST_URI'] ?>&fkey=off">Turn Off [Foreign Key] Fields</a></td>
								</tr>
<?php
   }
?>
<?php
   if ( !$FORM['full_rec'] AND count($fields_to_display) ) {
?>
								<tr> 
									<td align="center"><a href="<?php print $_SERVER['REQUEST_URI'] ?>&full_rec=y">Show Full Record</a></td>
								</tr>
<?php
   }
?>
								<tr> 
									<td><img src="../images/non.gif" width="1" height="5"></td>
								</tr>
								<tr> 
									<form action="<?php print $return_to_menu_url ?>" method="post">
										<td align="center"><input type="submit" id="submit" value="Return to Menu"></td>
									</form>
								</tr>
						</table></td>
              </tr>
            </table></td>
        </tr>
<?php
   } else if ( $FORM['f'] == "view" ) {
      $table_structure = mysql_table_structure( $mysql_connect_id, $table_name );
      if ( !$primary_key_field ) {
         $primary_key_field = mysql_primary_key_field( $table_structure );
      }
      $record = mysql_extract_record_id( $mysql_connect_id, $table_name, array( $primary_key_field => $FORM['pri'] ) );

      $return_to_menu_url = $_SERVER[PHP_SELF] . "?" . $FORM['return_query'];
		if ( $field_features ) {
         foreach ( $field_features as $field_name => $features ) {
            if ( $field_features[$field_name]['fkey'] OR $field_features[$field_name]['build_cat'] ) {
               $fkey_used = 1;
               break;
            }
         }
      }
      if ( $FORM['fkey'] == "off" ) {
         foreach ( $field_features as $field_name => $features ) {
            $field_features[$field_name]['fkey'] = "";
            $field_features[$field_name]['build_cat'] = "";
         }
      }
?>
        <tr> 
          <td><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
               <tr> 
                <td align="center" bgcolor="#A8EAFF"><b><a href="<?php print $_SERVER['PHP_SELF'] ?>" style="color: black">Main Menu</a> [View Record]</b></td>
              </tr>
              <tr> 
                <td><img src="../images/non.gif" width="1" height="5"></td>
              </tr>
              <tr> 
                <td><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr> 
               <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
<?php
   foreach ( $record as $key => $value ) {
      $field_properties = $field_features[$key];
      $key = convert_field_name( $key );

      if ( gettype($value) == "NULL" ) {
         $new_value = "[NULL]";
      } else if ( $value == "" ) {
         $new_value = "[EMPTY]";
      } else if ( $FORM['fkey'] != "off" AND $fkey_used AND is_array($field_properties['fkey']) ) {
         $new_value = mysql_get_foreign_key_text( $mysql_connect_id, $field_properties['fkey']['DB'], $field_properties['fkey']['TB'], array( $field_properties['fkey']['FIELD_KEY'] => $value ), $field_properties['fkey']['FIELD_DISPLAY'] );
      } else {
         $value_lines = preg_split( "/\r*\n/", $value );
         $new_valuez = array();
         foreach ( $value_lines as $linez ) {
            $new_valuez[] = wordwrap( $linez, 50 );
         }
         $new_value = htmlspecialchars( join( "\n", $new_valuez ) );
      }
?>
                  <tr> 
                     <td width="20%" class="arial_10pt" nowrap><b>&nbsp;<?php print $key ?></b></td>
                     <td width="10"></td>
                     <td><pre><?php print $new_value ?></pre></td>
                  </tr>
                  <tr> 
                     <td colspan="3"><img src="../images/non.gif" width="1" height="5"></td>
                  </tr>
                  <tr> 
                     <td colspan="3" bgcolor="#A8EAFF"><img src="../images/non.gif" width="1" height="1"></td>
                  </tr>
                  <tr>
                     <td colspan="3"><img src="../images/non.gif" width="1" height="5"></td>
                  </tr>
<?php
   }
?>
                  </table></td>
               </tr>
               <tr>
                  <td><img src="../images/non.gif" width="1" height="5"></td>
               </tr>
<?php
         $url_parse = parse_url( $_SERVER['REQUEST_URI'] );
         parse_str( $url_parse['query'], $post_fields );
         unset( $post_fields['f'] );
?>
               <tr> 
                  <form name="form1" method="post" action="<?php print $_SERVER['PHP_SELF'] ?>">
                     <td align="center"><input name="submit" type="submit" id="submit" value="Previous Record"> <input name="submit" type="submit" id="submit" value="Next Record"></td>
                     <input name="f" type="hidden" value="view">
<?php
      if ( $post_fields ) { foreach ( $post_fields as $name => $value ) { print "<input name=\"$name\" type=\"hidden\" value=\"$value\">"; } }
?>
                  </form>
               </tr>
               <tr> 
                  <form name="form1" method="get" action="<?php print $_SERVER['PHP_SELF'] ?>">
                     <td align="center"><input type="submit" value="Edit Record"></td>
                     <input name="f" type="hidden" value="edt">
<?php
      if ( $post_fields ) { foreach ( $post_fields as $name => $value ) { print "<input name=\"$name\" type=\"hidden\" value=\"$value\">"; } }
?>
                  </form>
               </tr>
               <tr> 
                  <td><img src="../images/non.gif" width="1" height="10"></td>
                </tr>
<?php
   if ( $FORM['fkey'] != "off" AND $fkey_used ) {
?>
                <tr> 
                  <td align="center"><a href="<?php print $_SERVER['REQUEST_URI'] ?>&fkey=off">Turn Off [Foreign Key] Fields</a></td>
                </tr>
               <tr> 
                  <td><img src="../images/non.gif" width="1" height="10"></td>
               </tr>
<?php
   }
?>
               <tr> 
                  <form action="<?php print $return_to_menu_url ?>" method="post">
                     <td align="center"><input type="submit" id="submit" value="Return to Menu"></td>
                  </form>
               </tr>
               </table></td>
              </tr>
            </table></td>
        </tr>
<?php
   }
?>
        <tr> 
          <td> </td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>
<?php

   function upcase_first_letter ( $string ) {
      return ucwords( strtolower( $string ) );
   }

   function convert_field_name ( $string ) {
      $string = str_replace( "_", " ", $string );
      return my_ucwords( $string );
   }

   function my_ucwords($str, $is_name=false) {
      // exceptions to standard case conversion
      if ($is_name) {
          $all_uppercase = '';
          $all_lowercase = 'De La|De Las|Der|Van De|Van Der|Vit De|Von|Or|And';
      } else {
          // addresses, essay titles ... and anything else
          $all_uppercase = 'Po|Rr|Se|Sw|Ne|Nw';
          $all_lowercase = 'A|And|As|By|In|Of|Or|To';
          // Philipz Inc. addon
          $all_uppercase .= '|Ii|Iii|Iv|Vi|Vii|Viii|Ix|Xi|';
          $all_lowercase .= '|At|The|On';
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

      # Philipz Inc. - Capitilizing words that have no vowels
      $str = preg_replace("/\\b([^aeiouy]{1,4})\\b/ie", "strtoupper('$1')", $str);
//      $words = explode( " ", $str );
//      foreach ( $words as $key => $word ) {
//         if ( !preg_match( "/[aeiou]/", $word ) AND strlen($word) < 5 ) {
//            $words[$key] = strtoupper($word);
//         }
//      }
//      $str = join( " ", $words );
      $common_upcase_short_words = array( "CEO", "PA", "IP", "ID", "IDs", "URL", "UID" );
      foreach ( $common_upcase_short_words as $value ) {
         $str = preg_replace( "/\b" . $value . "\b/i", $value, $str );
      }

      return $str;
   }
?>
