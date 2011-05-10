<?php
   $default_mysql_datatype_sizes['tinyint'] = 3; # -128 to 127 OR 255
   $default_mysql_datatype_sizes['bit'] = 1; # same as tinyint(1)
   $default_mysql_datatype_sizes['bool'] = 1; # same as tinyint(1)
   $default_mysql_datatype_sizes['smallint'] = 5; #
   $default_mysql_datatype_sizes['mediumint'] = 8; #
   $default_mysql_datatype_sizes['int'] = 10; #
   $default_mysql_datatype_sizes['integer'] = 10; #
   $default_mysql_datatype_sizes['bigint'] = 20; #
   $default_mysql_datatype_sizes['decimal'] = 10; #

   if ( !function_exists('convert_field_name')) {
      # just incase mysql_forms.php is run without db_view.php
      function convert_field_name ( $string ) {
         $string = str_replace( "_", " ", $string );
         if ( function_exists('my_ucwords') ) {
            return my_ucwords( $string, 1 );
         } else {
            return ucsmart( $string );
         }
      }
      function ucsmart( $text ) {
         # http://www.php.net/ucwords - lev at phpfox dot com (06-May-2006 01:44)
         return preg_replace('/([^a-z]|^)([a-z])/e', '"$1".strtoupper("$2")', strtolower($text));
      }
   }

   function mysql_generate_form ( $connect_id, $table, $fields_to_display = "", $fields_to_display_properties = array(), $edit_record_values = array(), $form_properties = array(), $edit_mode = 0 ) {
      global $form_field_input_format;
      $table_structure = mysql_table_structure( $connect_id, $table );

      if ( !$fields_to_display ) {
         $fields_to_display = $table_structure;
         $structure_used = 1;
      } else {
         # if $fields_to_display is set then it should be an non-associated array of the field names
         # example - ( 'id', 'name', 'country_code' )
         foreach ( $fields_to_display as $value ) {
            if ( $table_structure[$value] ) {
               # verifies that the field is actually in the table
               $fields_to_display1[$value] = "";
            }
         }
         $fields_to_display = $fields_to_display1;
      }

      $form_field_group_name = ( $form_properties['form_field_group_name'] ) ? $form_properties['form_field_group_name'] : "fieldz";
      $field_name_column_width = ( $form_properties['field_name_width'] ) ? " width=\"$form_properties[field_name_width]\"" : "";
      $field_name_extra = $field_name_column_width;
      $field_input_align = ( $form_properties['field_input_align'] ) ? " align=\"$form_properties[field_input_align]\"" : "";
      $field_input_extra = $field_input_align;

      foreach ( $fields_to_display as $key => $field_properties ) {
         if ( $fields_to_display_properties ) {
            $field_structure = $fields_to_display_properties[$key];
            if ( $field_structure ) {
               # If field properties are set for this field in $fields_to_display_properties then
               # put them into $field_structure
               $field_properties = $field_structure;
               #foreach ( $field_structure as $keyz => $value ) {
               #   if ( !$field_properties[$keyz] ) {
               #      $field_properties[$keyz] = $value;
               #   }
               #}
            }
         }

         if ( !$structure_used ) {
            # Add mysql structure fields returned from the mysql_table_structure function including
            # 'type', 'size', 'others', 'null', 'keys', 'default', 'extra', if they are not already sent
            # in the $fields_to_display_properties array
            $field_structure = $table_structure[$key];
            foreach ( $field_structure as $keyz => $value ) {
               if ( !$field_properties[$keyz] ) {
                  $field_properties[$keyz] = $value;
               }
            }
         }

         # -------------- FIELD FEATURES -----------------------
         # value        - if set, its contents will be added to the field
         # size         - if set, it is used to set the maximum characters which can be entered in the field
         # html_addon   - addition html code which will be added to the field

         # TEXTBOX attributes
         # set_password - if set, a text field will stars in it
         # width        - if set, it will be used to set a fields width
         # multiline    - FORMAT - width,height [field type change to 'tinytext']
         #                if set, it converts a text field to a textarea with the provided dimension
         # multibox     - if set, it converts a text field to multiple text boxes which will be separated with \r\n

         # TEXTAREA attributes
         # text_wrap    - if set, a textarea will have the text wrap feature
         # dimension    - if set, it will be used to set a fields width and height
         # singleline   - FORMAT width [field type change to 'varchar']

         # LISTBOX attributes
         # multi_select - if set, drop down menus will appear as listbox with the mutliselect feature
         # sort_enum    - if set, enum/set fields will be sorted
         # enum         - FORMAT - value=field,value=field,...... [field type change 'enum']
         #                if set, it converts a field to a drop down menu
         # boolen_yn    - if set, the listbox will have 'Yes' and 'No' entries

         # OTHERS
         # css_style    - if set, the provided css style will be used for the field
         # html_style   - if set, its contents will be added in ' style=""' and added to the fields html code
         # display_html - if set, the html code found in it will be displayed [field type change to 'html']

         # -------------- Non-MySQL types -----------------------
         # html         - html code to replace the generated html code
         # fkey         - contains an array with 'DB' as database_name, 'TB' as table name, 'FIELD_KEY' as the foreign
         #                 'FIELD_KEY' as the foreign key field in the table and 'FIELD_DISPLAY' as the fields in the
         #                 table that should be displayed in place of the value in 'FIELD_DISPLAY'
         # build_cat    - builds a tree like list

         if ( $field_properties['fkey'] OR $field_properties['fkey_view'] ) {
            if ( $field_properties['fkey_view'] ) $field_properties['fkey'] = $field_properties['fkey_view'];
            $field_properties['type'] = "enum";
            if ( is_string($field_properties['fkey']) ) {
               $fkey_fields = explode( ",", $field_properties['fkey'] );
               if ( !$fkey_fields[3] ) $fkey_fields[3] = $fkey_fields[2];
               $field_properties['enum'] = mysql_get_foreign_key_list( $connect_id, $fkey_fields[0], $fkey_fields[1], $fkey_fields[2], $fkey_fields[3], $fkey_fields[4], $fkey_fields[5] );
            } else {
               if ( !$field_properties['fkey']['FIELD_DISPLAY'] ) $field_properties['fkey']['FIELD_DISPLAY'] = $field_properties['fkey']['FIELD_KEY'];
               $field_properties['enum'] = mysql_get_foreign_key_list( $connect_id, $field_properties['fkey']['DB'], $field_properties['fkey']['TB'], $field_properties['fkey']['FIELD_KEY'], $field_properties['fkey']['FIELD_DISPLAY'], $field_properties['fkey']['WHERE_CLAUSE'], $field_properties['fkey']['ORDER_BY'] );
            }
            if ( !$field_properties['size'] ) $field_properties['size'] = "";
         } else if ( $field_properties['build_cat'] ) {
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
               } else if ( $field_properties['fkey']['FIELD_KEY'] == "code" AND $field_properties['fkey']['OPTIONS'] ) {
                  $options = array( 'use_codes' => "Y" );
               }

               $field_properties['build_cat'] = array( 'DB' => $field_properties['fkey']['DB'], 'TB' => $field_properties['fkey']['TB'], 'FIELDS' => $fields, 'WHERE_CLAUSE' => $field_properties['fkey']['WHERE_CLAUSE'], 'ORDER_BY' => $field_properties['fkey']['ORDER_BY'], 'SEPARATOR' => "||", 'INDENT' => 1, 'OPTIONS' => $options );
               #print_r2( $field_properties['build_cat'], 1 );
            }
            $field_properties['type'] = "enum";
            $database = $field_properties['build_cat']['DB'];
            if ( $database ) {
               $current_db = mysql_current_databse( $connect_id );
               if ( $current_db != $database ) {
                  mysql_change_db( $connect_id, $database );
               } else {
                  $database = "";
               }
            }
            $category_records = mysql_make_assoc_array( $connect_id, $field_properties['build_cat']['TB'], $field_properties['build_cat']['FIELDS'], $field_properties['build_cat']['WHERE_CLAUSE'], $field_properties['build_cat']['ORDER_BY'] );
            $field_properties['enum'] = build_category_paths( $category_records, $field_properties['build_cat']['SEPARATOR'], $field_properties['build_cat']['INDENT'], $field_properties['build_cat']['OPTIONS'] );
            if ( $database ) {
               mysql_change_db( $connect_id, $current_db );
            }
            if ( !$field_properties['size'] ) $field_properties['size'] = "";
         }

         if ( $field_properties['title'] ) {
            $field_display_name = $field_properties['title'];
         } else if ( ($field_properties['fkey'] OR $field_properties['fkey_view'] OR $field_properties['build_cat']) AND preg_match( "/(.*)_id$/i", $key, $preg_results ) ) {
            $field_display_name = convert_field_name( $preg_results[1] );
         } else {
            #$field_display_name = ucwords( strtolower( str_replace( "_", " ", $key ) ) );
            $field_display_name = convert_field_name( $key );
         }

         $field_properties['name'] = $key;
         $field_value = $edit_record_values[$key];
         $field_properties['form_field_group_name'] = $form_field_group_name;

         if ( $field_properties['print_value'] AND $edit_mode == 1 ) {
            $form_field_html = $field_value;
         } else if ( $field_properties['use_field'] ) {
            $form_field_html = str_replace( "($form_field_group_name}[{$field_properties[use_field]}]", "($form_field_group_name}[$key]", $form_fields_html[$field_properties['use_field']] );
            #$form_fields_html[$key] = $form_field_html;
         } else {
            $form_field_input_format = "";
            $form_field_html = mysql_generate_form_field( $connect_id, $table, $key, $field_properties, $field_value, $table_structure, $edit_mode );
            if ( !$form_field_html ) {
               $form_field_html = "Unknown Type [" . $field_properties['type'] . "][" . $field_properties['size'] . "]";
            }
            $form_fields_html[$key] = $form_field_html;
            if ( $form_field_input_format ) {
               $field_display_name .= "</b><br><font size=\"1\" face=\"verdana\">$form_field_input_format</font><b>";
            }
         }

         if ( $field_properties['field_input_under_name'] OR $form_properties['field_input_under_name'] ) {
            $html_code .= "<tr><td colspan=\"2\" style=\"padding: 5\"><b>$field_display_name</b><br>$form_field_html</td></tr>\r\n";
         } else {
            $html_code .= "<tr><td valign=\"top\" style=\"padding: 7 0 5 5\"$field_name_extra><b>$field_display_name</b></td><td style=\"padding: 5 5 5 0\"$field_input_extra>$form_field_html</td></tr>\r\n";
         }
         $html_code .= '<tr><td colspan="2" bgcolor="#808080"><img src="/images/non.gif" width="1" height="1"></td></tr>' . "\r\n";
      }

      $submit_record_title = "Submit Record";
      if ( $form_properties['submit_button_title'] ) {
         $submit_record_title = $form_properties['submit_button_title'];
      }
      $html_code .= '<tr><td colspan="2" align="center" style="padding: 5 0 0"><input type="submit" name="submit" value="' . $submit_record_title . '">';
      if ( $form_properties['other_submit_buttons'] ) {
         $other_submit_button_texts = explode( ",", $form_properties['other_submit_buttons'] );
         foreach ( $other_submit_button_texts as $other_submit_button_text ) {
            $html_code .= ' <input type="submit" name="submit" value="' . $other_submit_button_text . '">';
         }
      }
      if ( !$form_properties['no_reset'] ) {
         $html_code .= ' <input type="reset" name="reset" value="Reset Fields"></td></tr>' . "\r\n";
      }

      $table_width = ( isset($form_properties['table_width']) ) ? $form_properties['table_width'] : "98%";
      $table_class = ( $form_properties['table_class'] ) ? " class='" . $form_properties['table_class'] . "'" : "";
      $table_class .= ( $form_properties['table_style'] ) ? " style='" . $form_properties['table_style'] . "'" : "";

      $result = '<table width="' . $table_width . '" border="0" cellspacing="0" cellpadding="0" align="center"' . $table_class . '>';
      $result .= $html_code;
      $result .= '</table>';

      return $result;
   }

   function mysql_generate_form_field ( $connect_id, $table, $field_name, $field_properties = array(), $edit_value = "", $table_structure = array(), $edit_mode = 0 ) {
      global $default_mysql_datatype_sizes, $form_field_input_format;
      if ( !count($table_structure) ) {
         # so it doesnt request the table structure everytime it creates a form field when executing mysql_generate_form
         $table_structure = mysql_table_structure( $connect_id, $table );
      }

      if ( array_key_exists($field_name,$table_structure) ) {
         # verifies that the field is in the table

         # Add mysql structure fields returned from the mysql_table_structure function including
         # 'type', 'size', 'others', 'null', 'keys', 'default', 'extra', if they are not already sent
         # in the $field_properties
         $field_structure = $table_structure[$field_name];
         foreach ( $field_structure as $keyz => $value ) {
            #if ( !$field_properties[$keyz] ) {
            if ( !isset($field_properties[$keyz]) ) {
               $field_properties[$keyz] = $value;
            }
         }

         $field_db_name = $field_name;
         if ( $field_properties['form_field_group_name'] ) {
            $field_db_name = $field_properties['form_field_group_name'] . "[" . $field_name . "]";
         }
         if ( preg_match("/^set$/i", $field_properties['type']) OR $field_properties['multi_select'] ) {
            # as a set is a multiple selectable dropdown, we need to set this also in the fieldname
            $field_db_name .= "[]";
         }

         if ( $field_properties['enum'] ) $field_properties['type'] = "enum";
         if ( $field_properties['multiline'] ) {
            $field_properties['type'] = "tinytext";
            $field_properties['dimension'] = $field_properties['multiline'];
         } else if ( $field_properties['boolen_yn'] ) {
            $field_properties['type'] = "enum";
            $field_properties['enum'] = array( 'Y' => "Yes", 'N' => "No" );
         } else if ( isset($field_properties['singleline']) ) {
            $field_properties['type'] = "varchar";
            $field_properties['size'] = $field_properties['singleline'];
         }

         if ( isset($field_properties['multibox']) ) {
            $size_per_textbox = floor( $field_properties['size'] / $field_properties['multibox'] );
            $input_box_chars = $size_per_textbox;
            if ( $input_box_chars > 50 ) {
               $input_box_chars = 52;
            }
            $edit_value_array = explode( "\r\n", $edit_value );
            if ( $field_properties['multibox'] == "" ) {
               $field_properties['multibox'] = count( $edit_value_array );
            }
            for ( $x = 0; $x < $field_properties['multibox']; $x++ ) {
               $field_properties['name'] = $field_name . "']['$x";
               $form_field_html1 = "<input name=\"$field_db_name" . "[]\" type=\"text\" maxlength=\"$size_per_textbox\" size=\"$input_box_chars\"";
               $form_field_html1 .= ' value="' . mysql_generate_form_field_value( $field_properties, $edit_value_array[$x], $edit_mode ) . '"';
               $form_field_html1 .= mysql_generate_form_style_sheet( $field_properties );
               $form_field_html1 .= " id=\"$field_db_name\">";
               $form_field[] = $form_field_html1;
            }
            $form_field_html = join( "<br>", $form_field );
         } else if ( preg_match( "/^(var)?char|(var)?binary$/i", $field_properties['type'] ) ) {
            $form_field_html = "<input name=\"$field_db_name\"";

            if ( $field_properties['set_password'] ) {
               $form_field_html .= ' type="password"';
            } else {
               $form_field_html .= ' type="text"';
            }

            if ( $field_properties['width'] ) {
               $input_box_chars = $field_properties['width'];
            } else if ( $field_properties['size'] > 50 ) {
               $input_box_chars = 52;
            } else {
               $input_box_chars = $field_properties['size'] + 2;
            }
            if ( $field_properties['singleline'] ) $field_properties['size'] = "";

            $form_field_html .= mysql_generate_form_max_length( $field_properties, $input_box_chars );
            $form_field_html .= ' value="' . mysql_generate_form_field_value( $field_properties, $edit_value, $edit_mode ) . '"';
            $form_field_html .= mysql_generate_form_style_sheet( $field_properties );
            #list( $form_field_html, $field_display_name ) = mysql_generate_form_read_only( $field_properties, $form_field_html, $field_display_name );
            if ( ( preg_match( "/add/i", $field_properties['readonly'] ) AND $edit_mode == 0 ) OR ( preg_match( "/edit/i", $field_properties['readonly'] ) AND $edit_mode == 1 ) ) {
               $form_field_html .= " readonly='true'";
            }
            $form_field_html .= " id=\"$field_db_name\">";
         } else if ( preg_match( "/^date$/i", $field_properties['type'] ) ) {
            $form_field_html = "<input name=\"$field_db_name\" type=\"text\" size=\"12\" maxlength=\"10\"";

            $form_field_html .= ' value="' . mysql_generate_form_field_value( $field_properties, $edit_value, $edit_mode ) . '"';
            $form_field_html .= mysql_generate_form_style_sheet( $field_properties );
            #$form_field_html .= "> [date selector] (YYYY-MM-DD)";
            $form_field_html .= " id=\"$field_db_name\">";
            $form_field_input_format =  "(YYYY-MM-DD)";
         } else if ( preg_match( "/^datetime$/i", $field_properties['type'] ) ) {
            $form_field_html = "<input name=\"$field_db_name\" type=\"text\" size=\"22\" maxlength=\"20\"";

            $form_field_html .= ' value="' . mysql_generate_form_field_value( $field_properties, $edit_value, $edit_mode ) . '"';
            $form_field_html .= mysql_generate_form_style_sheet( $field_properties );
            #$form_field_html .= "> [date selector] (YYYY-MM-DD HH:MM:SS)";
            $form_field_html .= " id=\"$field_db_name\">";
            $form_field_input_format =  "(YYYY-MM-DD HH:MM:SS)";
         } else if ( preg_match( "/^time$/i", $field_properties['type'] ) ) {
            $form_field_html = "<input name=\"$field_db_name\" type=\"text\" size=\"10\" maxlength=\"8\"";

            $form_field_html .= ' value="' . mysql_generate_form_field_value( $field_properties, $edit_value, $edit_mode ) . '"';
            $form_field_html .= mysql_generate_form_style_sheet( $field_properties );
            #$form_field_html .= "> [date selector] (HH:MM:SS)";
            $form_field_html .= " id=\"$field_db_name\">";
            $form_field_input_format =  "(HH:MM:SS)";
         } else if ( preg_match( "/^year$/i", $field_properties['type'] ) ) {
            $form_field_html = "<input name=\"$field_db_name\" type=\"text\" size=\"" . ($field_properties['size']+2) . "\" maxlength=\"{$field_properties['size']}\"";

            $form_field_html .= ' value="' . mysql_generate_form_field_value( $field_properties, $edit_value, $edit_mode ) . '"';
            $form_field_html .= mysql_generate_form_style_sheet( $field_properties );
            $form_field_html .= " id=\"$field_db_name\">";
            #$form_field_input_format =  "(HH:MM:SS)";
         } else if ( preg_match( "/^((tiny|small|medium|big)?int(eger)?|decimal|float|double|real)$/i", $field_properties['type'] ) ) {
            $form_field_html = "<input name=\"$field_db_name\"";

            if ( $field_properties['width'] ) {
               $input_box_chars = $field_properties['width'];
            } else if ( $field_properties['size'] > 18 ) {
               $input_box_chars = 20;
            } else {
               $input_box_chars = $field_properties['size'] + 2;
            }
//            if ( !preg_match( "/^decimal|float|double$/i", $field_properties['type'] ) ) {
//            } else {
//            }

            $form_field_html .= mysql_generate_form_max_length( $field_properties, $input_box_chars );
            $form_field_html .= ' value="' . mysql_generate_form_field_value( $field_properties, $edit_value, $edit_mode ) . '"';
            $form_field_html .= mysql_generate_form_style_sheet( $field_properties );
            #list( $form_field_html, $field_display_name ) = mysql_generate_form_read_only( $field_properties, $form_field_html, $field_display_name );
            if ( preg_match( "/auto_increment/i", $field_properties['extra'] ) AND ( !isset($edit_value) OR $edit_mode == 0 ) ) {
               #$form_field_html .= " READONLY";
               $form_field_ending .= " <font color='red'>(AUTO INCREMENT=" . mysql_get_table_auto_increment($connect_id, $table) . ")</font>";
            }
            if ( ( preg_match( "/add/i", $field_properties['readonly'] ) AND $edit_mode == 0 ) OR ( preg_match( "/edit/i", $field_properties['readonly'] ) AND $edit_mode == 1 ) ) {
               $form_field_html .= " readonly='true'";
            }
            $form_field_html .= " id=\"$field_db_name\">" . $form_field_ending;
         } else if ( preg_match("/^(tiny|medium|long)?(text|blob)$/i", $field_properties['type'], $preg_results) ) {
            $form_field_html = "<textarea name=\"$field_db_name\"";
            if ( $field_properties['text_wrap'] ) {
               #$form_field_html .= ' wrap="OFF"';
               $form_field_html .= ' wrap="' . $field_properties['text_wrap'] . '"';
            }
            if ( $field_properties['dimension'] ) {
               list( $field_width, $field_height ) = explode( ",", $field_properties['dimension'] );
               $form_field_html .= " cols=\"$field_width\" rows=\"$field_height\"";
            } else {
               if ( $preg_results[1] == "tiny" ) {
                  $form_field_html .= " cols=\"50\" rows=\"2\"";
               } else if ( $preg_results[1] == "medium" ) {
                  $form_field_html .= " cols=\"50\" rows=\"5\"";
               } else if ( $preg_results[1] == "long" ) {
                  $form_field_html .= " cols=\"50\" rows=\"7\"";
               } else {
                  $form_field_html .= " cols=\"50\" rows=\"3\"";
               }
            }
            $form_field_html .= mysql_generate_form_style_sheet( $field_properties );
            $form_field_html .= " id=\"$field_db_name\">";

            if ( $edit_mode == 2 ) {
               $form_field_html .= "<?php print \$FORM['" . $field_properties['form_field_group_name'] . "']['" . $field_properties['name'] . "'] ?>";
            } else if ( isset($edit_value) ) {
               $form_field_html .= $edit_value;
            } else if ( $field_properties['default'] AND !$edit_mode ) {
               $form_field_html .= $field_properties['default'];
            }
            $form_field_html .= "</textarea>";
         } else if ( preg_match("/^enum|set$/i", $field_properties['type']) OR $field_properties['enum'] ) {
            $form_field_html = "<select name=\"$field_db_name\"";

            if ( $field_properties['type'] == "set" OR $field_properties['multi_select'] ) {
               if ( intval($field_properties['multi_select']) == 0 ) {
                  $multi_select = 2;
               } else {
                  $multi_select = $field_properties['multi_select'];
               }
               $form_field_html .= " size=\"$multi_select\" multiple";
            }
            $form_field_html .= mysql_generate_form_style_sheet( $field_properties );
            $form_field_html .= " id=\"$field_db_name\">";

            if ( $field_properties['enum'] ) {
               # use user defined list in dropdown/list
               if ( is_array($field_properties['enum']) ) {
                  $option_fields = $field_properties['enum'];
               } else {
                  $option_fields = explode( ",", $field_properties['enum'] );
                  if ( substr_count($field_properties['enum'],"=") == (substr_count($field_properties['enum'],",")+1) ) $check_equal_to = 1;
               }
            } else if ( $field_properties['size'] ) {
               if ( is_array($field_properties['size']) ) {
                  $option_fields = $field_properties['size'];
                  #foreach ( $field_properties['size'] as $key => $value ) {
                  #   $option_fields[$value] = $value;
                  #}
               } else {
                  $option_fields = explode( ",", $field_properties['size'] );
                  #if ( substr_count($field_properties['size'],"=") == (substr_count($field_properties['size'],",")+1) ) $check_equal_to = 1;
               }
               foreach ( $option_fields as $key => $value ) {
                  $option_fieldz[$value] = $value;
               }
               $option_fields = $option_fieldz;
            }

            if ( $option_fields ) {
               $enum_values = array();
               if ( $check_equal_to ) {
                  $enum_values = array();
                  foreach( $option_fields as $value => $option_field ) {
                     #if ( preg_match( "/=/", $option_field ) AND $check_equal_to ) {
                        list( $value, $title ) = explode( "=", $option_field );
                        $enum_values[$value] = str_replace( "&#44;", ",", $title );
                     #} else {
                     #   $enum_values[$value] = $option_field;
                     #}
                  }
               } else {
                  $enum_values = $option_fields;
               }
               if ( strtolower($field_properties['sort_enum']) == "ksort" ) {
                  ksort( $enum_values, SORT_STRING );
               } else if ( strtolower($field_properties['sort_enum']) == "krsort" ) {
                  krsort( $enum_values, SORT_STRING );
               } else if ( strtolower($field_properties['sort_enum']) == "arsort" ) {
                  arsort( $enum_values, SORT_STRING );
               } else if ( $field_properties['sort_enum'] ) {
                  asort( $enum_values, SORT_STRING );
               }
            }

            if ( $field_properties['null'] == "YES" AND $field_properties['allow_null'] != "N" ) {
               $form_field_html .= '<option value=""></option>';
            }

            if ( $edit_mode == 2 ) {
               $form_field_html .= "<?php print array_to_html_options( \$$field_name" . "_list, \$FORM['" . $field_properties['form_field_group_name'] . "']['" . $field_properties['name'] . "'] ) ?>";
            } else {
               if ( isset($edit_value) ) {
                  if ( is_string($edit_value) ) {
                     $selected_values = explode( ",", $edit_value );
                  } else {
                     $selected_values = $edit_value;
                  }
               } else if ( preg_match("/^set$/i", $field_properties['type']) AND $field_properties['default'] ) {
                  # handles the 'set' datatype and if it has multiple default set values
                  $selected_values = explode( ",", $field_properties['default'] );
               } else {
                  $selected_values = array();
               }
   
               if ( $enum_values ) {
                  foreach( $enum_values as $key => $value ) {
                     $form_field_html .= "<option";
                     if ( in_array($key,$selected_values) OR $key == $edit_value ) {
                        $form_field_html .= " selected";
                        $selected = 1;
                     } else if ( $key == $field_properties['default'] AND !$selected_values AND !$edit_mode ) {
                        $form_field_html .= " selected";
                        $selected = 1;
                     }
      
                     if ( strval($key) == strval($value) ) {
                        $form_field_html .= ">$value</option>";
                     } else {
                        $form_field_html .= " value=\"$key\">$value</option>";
                     }
                  }
               }
            }

            $form_field_html .= "</select>";

            if ( $field_properties['fkey'] AND $edit_value AND !$selected ) {
               # incase the value is not found in the listbox or menubox
               $form_field_html = "<input name=\"$field_db_name\" type=\"text\" size=\"18\" value=\"$edit_value\" id=\"$field_db_name\">";
            }
         } else if ( $field_properties['type'] == "html" ) {
            $form_field_html = $field_properties['display_html'];
         }

         return $form_field_html;
      }
   }

//   function mysql_generate_form_read_only ( $field_properties, $form_field_html, $field_display_name ) {
//      if ( preg_match( "/auto_increment/i", $field_properties['extra'] ) ) {
//         $form_field_html .= " READONLY";
//         $field_display_name .= " <font color='red'>(auto)</a>";
//      }
//
//      return array( $form_field_html, $field_display_name );
//   }

   function mysql_generate_form_max_length ( $field_properties, $field_size = 0 ) {
      if ( $field_properties['size'] ) {
         if ( preg_match( "/^(\d+),(\d+)$/", $field_properties['size'], $preg_results ) ) {
            $return_html .= " maxlength=\"" . ($preg_results[1]+$preg_results[2]) . "\"";
         } else {
            $return_html .= " maxlength=\"$field_properties[size]\"";
         }
      }

      if ( $field_size ) {
         $return_html .= " size=\"" . ( $field_size ) . "\"";
      }

      return $return_html;
   }

   function mysql_generate_form_field_value ( $field_properties, $edit_value, $edit_mode = 0 ) {
      if ( $edit_mode == 2 ) {
         $return_html = "<?php print \$FORM['" . $field_properties['form_field_group_name'] . "']['" . $field_properties['name'] . "'] ?>";
      } else if ( isset($edit_value) ) {
         $return_html = str_replace( '"', "&quot;", $edit_value );
      } else if ( $field_properties['value'] ) {
         $return_html = str_replace( '"', "&quot;", $field_properties['value'] );
      } else if ( $field_properties['default'] != NULL AND !$edit_mode ) {
         $return_html = $field_properties['default'];
      }

      return $return_html;
   }

   function mysql_generate_form_style_sheet( $field_properties ) {
      if ( $field_properties['css_style'] ) {
         $return_html .= " class=\"$field_properties[css_style]\"";
      }
      if ( $field_properties['html_style'] ) {
         $return_html .= " style=\"$field_properties[html_style]\"";
      }

      return $return_html;
   }

   function mysql_get_table_auto_increment ( $connect_id, $table ) {
      $sql_statement = "SHOW TABLE STATUS";
      $sql_result = mysql_query( $sql_statement, $connect_id );

      if ( $sql_result ) {
         while( $sql_record = mysql_fetch_assoc($sql_result) ) {
            if ( $sql_record["Name"] == $table ) {
               return $sql_record["Auto_increment"];
            }
         }
      }

      return FALSE;
   }

//   # the following code needs to be used with mysql_generate_edit_form
//   include( "../../php_lib3/mysql.php" );
//   include( "../../php_lib3/misc_categories.php" );
//   include( "../../php_lib2/mysql_forms.php" );
//
//   $db_parameters['mysql_server'] = $mysql_server;
//   $db_parameters['mysql_username'] = $mysql_username;
//   $db_parameters['mysql_password'] = $mysql_password;
//   $db_parameters['database_name'] = $database_name;
//   $db_parameters['table_name'] = $table_name;
//   
//   $display_fields['on'] = $fields_to_display;
//   $display_fields['off'] = $fields_not_to_display;
//
//   $form_properties['single_page'] = 1; # for a single page add or edit
//   $form_properties['popup_save'] = 1; # for saving to happen in popups
//
//   #$show_output_code = 1; # will execute the output code
//   header( "Content-type: text/plain" );
//   #header( "Content-type: text/html" );
//   print mysql_generate_edit_form( $db_parameters, $table_name, $display_fields, $field_features );

   function mysql_generate_edit_form ( $connection_parameters, $table_name, $display_fields = array(), $fields_to_display_properties = array(), $form_properties = array() ) {
      global $show_output_code;
      # in order to bring in html to be printed, change '"' to '\"', '$' to '\$', and then regexp '\r\n([^\r\n]+)' to '\r\n         $php_code .= "$1\\r\\n";'

      $php_code = "<?php\r\n";
      if ( !$form_properties['single_page'] ) {
      $php_code .= "   include( \"../../php_lib3/misc.php\" );\r\n";
      $php_code .= "   include( \"../../php_lib3/misc_categories.php\" );\r\n";
      $php_code .= "   include( \"../../php_lib3/mysql.php\" );\r\n\r\n";
      }

      if ( is_array($connection_parameters) ) {
         $connect_id = mysql_start( $connection_parameters['mysql_server'], $connection_parameters['database_name'], $connection_parameters['mysql_username'], $connection_parameters['mysql_password'] );

         if ( !$form_properties['single_page'] ) {
         $php_code .= "   \$mysql_server = \"$connection_parameters[mysql_server]\";\r\n";
         $php_code .= "   \$mysql_username = \"$connection_parameters[mysql_username]\";\r\n";
         $php_code .= "   \$mysql_password = \"$connection_parameters[mysql_password]\";\r\n\r\n";

         $php_code .= "   \$database_name = \"$connection_parameters[database_name]\";\r\n";
         }
         $php_code .= "   \$table_name = \"$table_name\";\r\n";
         $php_code .= "   \$mysql_connect_id = mysql_start( \$mysql_server, \$database_name, \$mysql_username, \$mysql_password );\r\n\r\n";
         
         $current_database = $connection_parameters['database_name'];
      } else {
         $connect_id = $connection_parameters;
         $current_database = mysql_current_databse( $connect_id );
         $connection_parameters['database_name'] = $current_database;

         $php_code .= "   \$database_name = \"$current_database\";\r\n";
         $php_code .= "   \$table_name = \"$table_name\";\r\n\r\n";
      }

      $form_field_group_name = ( $form_properties['form_field_group_name'] ) ? $form_properties['form_field_group_name'] : "fieldz";
      $table_structure = mysql_table_structure( $connect_id, $table_name );
      $fields_to_display = array_keys( $table_structure );
      $primary_key_field = mysql_primary_key_field( $table_structure );

      if ( $display_fields['on'] ) {
         foreach ( $display_fields['on'] as $value ) {
            if ( $table_structure[$value] ) {
               # verifies that the field is actually in the table
               $fields_to_display1[] = $value;
            }
         }
         $fields_to_display = $fields_to_display1;
      } else if ( $display_fields['off'] ) {
         foreach ( $display_fields['off'] as $value ) {
            $find_key = array_search( $value, $fields_to_display );
            if ( $find_key !== False ) {
               unset( $fields_to_display[$find_key] );
            }
         }
      } else {
         $all_fields = 1;
      }

      if ( $display_fields['hide'] ) {
         foreach ( $display_fields['hide'] as $value ) {
            if ( $table_structure[$value] ) {
               # verifies that the field is actually in the table
               $fields_to_hide[] = $value;
            }
         }
         $fields_to_hide1 = ",`" . join( "`,`", $fields_to_hide ) . "`";
      }

      if ( $all_fields ) {
         $fields = "*";
      } else {
         $fields = "`" . join( "`,`", $fields_to_display ) . "`";
         if ( !is_array($fields_to_hide) ) $fields_to_hide = array();
         if ( !in_array($primary_key_field,$fields_to_display) AND !in_array($primary_key_field,$fields_to_hide) ) {
            # primary key wasnt found in the lists
            $fields_to_hide1 .= ",`$primary_key_field`";
         }
      }

      if ( !$form_properties['single_page'] ) {
         $php_code .= "   \$table_structure = mysql_table_structure( \$mysql_connect_id, \$table_name );\r\n";
         //$php_code .= "   \$primary_key_field = mysql_primary_key_field( \$table_structure );\r\n\r\n";
         $php_code .= "   \$filter_field_names = array_keys( \$table_structure );\r\n";
         $php_code .= "   \$sql_field_names = \"$fields$fields_to_hide1\";\r\n";
         $php_code .= "   #\$sql_field_names = array_keys( \$table_structure );\r\n";
         $php_code .= "   \$primary_key_field = \"$primary_key_field\";\r\n\r\n";
      }

         $php_code .= "   if ( \$FORM['submit'] ) {\r\n";
         $php_code .= "      if ( \$FORM['f'] == \"add\" ) {\r\n";
         $php_code .= "         foreach ( \$FORM['$form_field_group_name'] as \$field_name => \$value ) {\r\n";
         $php_code .= "            if ( is_array(\$value) ) {\r\n";
         $php_code .= "               \$add_fields[\$field_name] = join( \",\", \$value );\r\n";
         $php_code .= "            } else {\r\n";
         $php_code .= "               #if ( trim(\$value) ) {\r\n";
         $php_code .= "                  \$add_fields[\$field_name] = trim(\$value);\r\n";
         $php_code .= "               #}\r\n";
         $php_code .= "            }\r\n";
         $php_code .= "         }\r\n\r\n";
         $php_code .= "         \$query_result = mysql_add_record( \"\", \$table_name, \$add_fields );\r\n\r\n";
         $php_code .= "         if ( !\$query_result ) {\r\n";
         $php_code .= "            print \"Error Adding Record\";\r\n";
         $php_code .= "            print mysql_info();\r\n";
         $php_code .= "            print mysql_error();\r\n";
         $php_code .= "            exit;\r\n";
         $php_code .= "         }\r\n";
         $php_code .= "      } else if ( \$FORM['f'] == \"edit\" ) {\r\n";
         $php_code .= "         foreach ( \$FORM['$form_field_group_name'] as \$field_name => \$value ) {\r\n";
         $php_code .= "            if ( is_array(\$value) ) {\r\n";
         $php_code .= "               \$update_fields[\$field_name] = join( \",\", \$value );\r\n";
         $php_code .= "            } else {\r\n";
         $php_code .= "               #if ( trim(\$value) ) {\r\n";
         $php_code .= "                  \$update_fields[\$field_name] = trim(\$value);\r\n";
         $php_code .= "               #}\r\n";
         $php_code .= "            }\r\n";
         $php_code .= "         }\r\n";
      if ( !$form_properties['single_page'] ) {
         $php_code .= "         if ( \$FORM['submit'] == \"Save\" ) {\r\n";
         $php_code .= "            mysql_upd_record( \"\", \$table_name, \$update_fields, array( \$primary_key_field => \$FORM['pri_key'] ) );\r\n";
         #$php_code .= "            mysql_upd_record( \"\", \$table_name, \$update_fields, array( \"$primary_key_field\" => \$FORM['pri_key'] ) );\r\n";
         $php_code .= "         } else if ( \$FORM['submit'] == \"Delete\" ) {\r\n";
         $php_code .= "            mysql_del_record( \"\", \$table_name, array( \$primary_key_field => \$FORM['pri_key'] ) );\r\n";
         $php_code .= "         }\r\n";
      } else {
         $php_code .= "         mysql_upd_record( \"\", \$table_name, \$update_fields, array( 'id' => \$FORM['id'] ) );\r\n";
      }
         $php_code .= "      }\r\n";
      if ( !$form_properties['single_page'] ) {
         if ( $form_properties['popup_save'] ) {
            $php_code .= "      close_page();\r\n";
         } else {
            $php_code .= "      header( \"location: \$FORM[ref_url]\" );\r\n";
         }
         $php_code .= "      exit;\r\n";
         $php_code .= "      \$FORM['submit'] = \"\";\r\n";
      } else {
         $php_code .= "      header( \"location: index.php\" );\r\n";
         $php_code .= "      exit;\r\n";
      }
         $php_code .= "   }\r\n\r\n";

//      $php_code .= "   \$FORM['start'] = ( \$FORM['start'] == \"\" OR \$FORM['start'] < 0 ) ? \"0\" : \$FORM['start'];\r\n";
//      $php_code .= "   \$edit_record = mysql_extract_records_where( \"\", \$table_name, \"\", \"*\", \"\", \"\$FORM[start],1\" );\r\n";
//      $php_code .= "   \$FORM['$form_field_group_name'] = \$edit_record[1];\r\n\r\n";

      if ( !$form_properties['single_page'] ) {
         $php_code .= "   \$filter_operators = array( '=' => '', '!=' => '', '<' => '', '>' => '', 'regexp' => '~' );\r\n";
         $php_code .= "   \$total_records = mysql_count_query_records( \"\", \$table_name, \"\" );\r\n";
         $php_code .= "   if ( \$FORM['f'] == \"add\" OR \$total_records == 0 ) {\r\n";
      } else {
         $php_code .= "   if ( \$FORM['f'] == \"add\" ) {\r\n";
      }

         # loads the default entries from db into form
         foreach ( $table_structure as $key => $array ) {
            if ( $array['default'] ) {
               #$FORM['fieldz'][$key] = $array['default'];
               $php_code .= "      \$FORM['$form_field_group_name']['$key'] = \"$array[default]\";\r\n";
            }
         }

      if ( !$form_properties['single_page'] ) {
         $php_code .= "\r\n";
         $php_code .= "      if ( \$total_records ) {\r\n";
         $php_code .= "         \$FORM['start'] = \$total_records;\r\n";
         $php_code .= "         \$total_records = \$FORM['start'] + 1;\r\n";
         $php_code .= "      }\r\n";
         $php_code .= "      \$save_type = \"add\";\r\n";
         $php_code .= "   } else {\r\n";
         $php_code .= "      if ( \$FORM['where_data'] ) {\r\n";
         $php_code .= "         \$where_clause = \$FORM['where_data'];\r\n";
         $php_code .= "			\$recount_total_records = 1;\r\n";
         $php_code .= "      } else if ( \$FORM['filter_field'] ) {\r\n";
         $php_code .= "         \$FORM['filter_data'] = mysql_real_escape_string( \$FORM['filter_data'] );\r\n";
         $php_code .= "         \$where_clause[\"`\$FORM[filter_field]` \$FORM[filter_operator] '\$FORM[filter_data]'\"] = '';\r\n";
         $php_code .= "			\$recount_total_records = 1;\r\n";
         $php_code .= "      } else if ( \$FORM['filter_operator'] AND \$FORM['filter_data'] ) {\r\n";
         $php_code .= "         foreach ( \$table_structure as \$key => \$record ) {\r\n";
         $php_code .= "            if ( \$FORM['filter_operator'] == \"=\" ) {\r\n";
         $php_code .= "               # change single slash to double in order for like to handle it properly\r\n";
         $php_code .= "               \$new_value = str_replace( chr(92), chr(92).chr(92), \$FORM['filter_data'] );\r\n";
         $php_code .= "               \$where_clause_filter[] = \"`\$key` LIKE \\\"%\" . mysql_real_escape_string( \$new_value ) . \"%\\\"\";\r\n";
         $php_code .= "            } else {\r\n";
         $php_code .= "               \$where_clause_filter[] = \"`\$key` \$FORM[filter_operator] '\" . mysql_real_escape_string( \$FORM['filter_data'] ) . \"'\";\r\n";
         $php_code .= "            }\r\n";
         $php_code .= "         }\r\n";
         $php_code .= "         \$where_clause[\"( \" . join( \" OR \", \$where_clause_filter ) . \" )\"] = '';\r\n";
         $php_code .= "			\$recount_total_records = 1;\r\n";
         $php_code .= "      }\r\n";
         $php_code .= "      if ( \$recount_total_records ) {\r\n";
         $php_code .= "         \$total_records = mysql_count_query_records( \"\", \$table_name, \$where_clause );\r\n";
         $php_code .= "      }\r\n\r\n";
         $php_code .= "      if ( \$FORM['starts'] ) \$FORM['start'] = \$FORM['starts'] - 1;\r\n";
         $php_code .= "      \$FORM['start'] = ( \$FORM['start'] == \"\" OR \$FORM['start'] < 0 ) ? \"0\" : \$FORM['start'];\r\n";
         $php_code .= "      \$edit_record = mysql_extract_records_where( \"\", \$table_name, \$where_clause, \$sql_field_names, \"\", \"\$FORM[start],1\" );\r\n";
         $php_code .= "      if ( \$where_clause AND \$edit_record == 1 ) {\r\n";
         $php_code .= "         print \"No Records Found\";\r\n";
         $php_code .= "         exit;\r\n";
         $php_code .= "      }\r\n";
         $php_code .= "      if ( \$FORM['start'] >= \$total_records ) {\r\n";
         $php_code .= "         header( \"Location: \" . \$_SERVER['PHP_SELF'] . \"?start=\" . (\$total_records - 1) );\r\n";
         $php_code .= "         exit;\r\n";
         $php_code .= "      }\r\n";
         $php_code .= "      \$FORM['fieldz'] = \$edit_record[1];\r\n";
         $php_code .= "      \$primary_key_id = \$edit_record[1][\$primary_key_field];\r\n";
         $php_code .= "      \$save_type = \"edit\";\r\n";
         $php_code .= "   }\r\n";
      } else {
         $php_code .= "   } else {\r\n";
         $php_code .= "      \$FORM['$form_field_group_name'] = mysql_extract_record_id( \"\", \$table_name, array( 'id' => \$FORM['id'] ) );\r\n";
         $php_code .= "   }\r\n";
      }

      $php_code .= "\r\n";
//      print "<pre>";
//      print_r( $table_structure );
//      print "</pre>";
//      print "<pre>";
//      print_r( $fields_to_display_properties );
//      print "</pre>";
      foreach ( $fields_to_display as $field_name ) {
         $field_structure = $table_structure[$field_name];
         $field_properties = $fields_to_display_properties[$field_name];
         #$field_properties = array_merge( $field_structure, $field_properties );

         if ( $field_properties['fkey'] ) {
            if ( is_string($field_properties['fkey']) ) {
               $fkey_fields = explode( ",", $field_properties['fkey'] );
               if ( !$fkey_fields[3] ) $fkey_fields[3] = $fkey_fields[2];
               $field_properties['fkey'] = array( $connect_id, 'DB' => $fkey_fields[0], 'TB' => $fkey_fields[1], 'FIELD_KEY' => $fkey_fields[2], 'FIELD_DISPLAY' => $fkey_fields[3], 'WHERE_CLAUSE' => $fkey_fields[4], 'ORDER_BY' => $fkey_fields[5] );
            }
            $fkey_data = $field_properties['fkey'];
            if ( !preg_match( "/field[\[<]|fk(?:ey)?[\[<]/", $fkey_data['FIELD_DISPLAY'] ) AND strtolower($fkey_data['DB']) == strtolower($current_database) ) {
               $php_code .= "   \$$field_name" . "_list = mysql_create_associate_array( \"\", \"$fkey_data[TB]\", \"$fkey_data[FIELD_KEY],$fkey_data[FIELD_DISPLAY]\" );\r\n";
            } else {
               $php_code .= "   \$$field_name" . "_list = mysql_get_foreign_key_list( \"\", \"$fkey_data[DB]\", \"$fkey_data[TB]\", \"$fkey_data[FIELD_KEY]\", \"$fkey_data[FIELD_DISPLAY]\", \"$fkey_data[WHERE_CLAUSE]\", \"$fkey_data[ORDER_BY]\" );\r\n";
            }
            $field_properties['type'] = "enum";
            $field_properties['size'] = "";
         } else if ( $field_properties['build_cat'] ) {
            if ( $field_properties['build_cat']['fkey'] ) {
               $field_properties['fkey'] = $field_properties['build_cat']['fkey'];
               if ( is_string($field_properties['build_cat']) ) {
                  $fkey_fields = explode( ",", $field_properties['fkey'] );
                  if ( !$fkey_fields[3] ) $fkey_fields[3] = $fkey_fields[2];
                  $field_properties['fkey'] = array( $connect_id, 'DB' => $fkey_fields[0], 'TB' => $fkey_fields[1], 'FIELD_KEY' => $fkey_fields[2], 'FIELD_DISPLAY' => $fkey_fields[3], 'WHERE_CLAUSE' => $fkey_fields[4], 'ORDER_BY' => $fkey_fields[5] );
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
               } else if ( $field_properties['fkey']['FIELD_KEY'] == "code" AND $field_properties['fkey']['OPTIONS'] ) {
                  $options = array( 'use_codes' => "Y" );
               }

               $field_properties['build_cat'] = array( 'DB' => $field_properties['fkey']['DB'], 'TB' => $field_properties['fkey']['TB'], 'FIELDS' => $fields, 'WHERE_CLAUSE' => $field_properties['fkey']['WHERE_CLAUSE'], 'ORDER_BY' => $field_properties['fkey']['ORDER_BY'], 'SEPARATOR' => "||", 'INDENT' => 1, 'OPTIONS' => $options );
            }

            $category_data = $field_properties['build_cat'];
            if ( strtolower($category_data['DB']) != strtolower($current_database) AND $category_data['DB'] != "" ) {
               $php_code .= "   mysql_change_db( \"\", \"{$category_data['DB']}\" );\r\n";
            }
            $php_code .= "   \${$field_name}_category_records = mysql_make_assoc_array( \"\", \"{$category_data['TB']}\", \"{$category_data['FIELDS']}\", \"{$category_data['WHERE_CLAUSE']}\", \"{$category_data['ORDER_BY']}\" );\r\n";
            $php_code .= "   \${$field_name}_list = build_category_paths( \${$field_name}_category_records, \"" . addcslashes( $category_data['SEPARATOR'], "\"\\" ) . "\", \"{$category_data['INDENT']}\", " . preg_replace( "/;$/", "", array_to_php( $category_data['OPTIONS'] ) ) . " );\r\n";
            if ( strtolower($category_data['DB']) != strtolower($current_database) AND $category_data['DB'] != "" ) {
               $php_code .= "   mysql_change_db( \"\", \"{$current_database}\" );\r\n";
            }
            $field_properties['type'] = "enum";
            $field_properties['size'] = "";
         } else if ( $field_properties['enum'] ) {
            #$php_code .= "   \$$field_name" . "_list = array( '" . join( "' => \"\", '", $field_properties['enum'] ) . "' => \"\" );\r\n";
            $php_code .= "   \$$field_name" . "_list = " . array_to_php( $field_properties['enum'] ) . "\r\n";
         } else if ( $field_structure['type'] == "set" OR $field_structure['type'] == "enum" ) {
            $php_code .= "   \$$field_name" . "_list = array( '" . join( "' => \"\", '", $field_structure['size'] ) . "' => \"\" );\r\n";
         }
         if ( $field_properties['multi_select'] OR $field_structure['type'] == "set" ) {
            $php_code .= "   \$FORM['$form_field_group_name']['$field_name'] = explode( ',', \$FORM['fieldz']['$field_name'] );\r\n";
         }

         $field_properties['form_field_group_name'] = $form_field_group_name;
         if ( !$field_properties['title'] ) {
            $field_display_name = convert_field_name( $field_name );
         } else {
            $field_display_name = $field_properties['title'];
         }
         $field_properties['name'] = $field_name;

         if ( !$form_properties['print_version'] ) {
            $form_field_html = mysql_generate_form_field( $connect_id, $table_name, $field_name, $field_properties, "", $table_structure, 2 );
         } else {
            /* $form_field_html = "<?php print \$FORM['$form_field_group_name']['$field_name'] ?>"; */
            if ( $field_properties['fkey'] OR $field_properties['build_cat'] ) {
               $form_field_html = "<?php print \${$field_name}_list[\$db_record['$field_name']] ?>";
            } else if ( $field_structure['type'] == "set" ) {
               /* $form_field_html = "<?php print join( \", \", \$db_record['$field_name'] ) ?>"; */
               $form_field_html = "<?php print str_replace( \",\", \", \", \$db_record['$field_name'] ) ?>";
            } else {
               $form_field_html = "<?php print \$db_record['$field_name'] ?>";
            }
         }

         # adds necessary code to textarea are shown correctly in both opera and firefix/ie
         /* preg_match_all( "/(<textarea name=\"[^\"]+\" cols=\"[^\"]+\" rows=\")([^\"]+)(\"[^>]*>)/", $form_field_html, $preg_results, PREG_SET_ORDER );
         foreach ( $preg_results as $array ) {
            $form_field_html = str_replace( $array[0], $array[1]."<?php if ( preg_match( \"/Opera[\\/ ]\\d+\\.\\d+/\", \$_SERVER['HTTP_USER_AGENT'] ) ) { print '$array[2]'; } else { print '" . ($array[2]-1) . "'; } ?>".$array[3], $form_field_html );
         } */
         
         if ( isset($field_properties['print_value']) ) {
            $form_field_html = "<?php if ( \$save_type == \"add\" ) { ?>$form_field_html<?php } else { print \$FORM['$form_field_group_name']['$field_name']; } ?>";
         }

         $html_code .= "<tr><td valign=\"top\" class=\"tb_title\"><b>$field_display_name</b></td><td class=\"tb_field\">$form_field_html</td></tr>\r\n";
         #$html_code .= "<tr><td valign=\"top\" style=\"padding: 7 0 5 5\"><b>$field_display_name</b></td><td style=\"padding: 5 5 5 0\">$form_field_html</td></tr>\r\n";
         $html_code .= '<tr><td colspan="2" bgcolor="#808080"><img src="/images/non.gif" width="1" height="1"></td></tr>' . "\r\n";
      }
      $php_code .= "?>\r\n";

//      $submit_record_title = "Submit Record";
//      if ( $form_properties['submit_button_title'] ) {
//         $submit_record_title = $form_properties['submit_button_title'];
//      }
//      $html_code .= '<tr><td colspan="2" align="center"><input type="submit" name="submit" value="' . $submit_record_title . '">';
//      if ( $form_properties['other_submit_buttons'] ) {
//         $other_submit_button_texts = explode( ",", $form_properties['other_submit_buttons'] );
//         foreach ( $other_submit_button_texts as $other_submit_button_text ) {
//            $html_code .= ' <input type="submit" name="submit" value="' . $other_submit_button_text . '">';
//         }
//      }
//      if ( !$form_properties['no_reset'] ) {
//         $html_code .= ' <input type="reset" name="reset" value="Reset Fields"></td></tr>' . "\r\n";
//      }

      if ( !$form_properties['single_page'] ) {
         $html_code .= "<tr><td colspan=\"2\" align=\"center\"><table border=\"0\" cellspacing=\"10\" cellpadding=\"0\">\r\n";
         $html_code .= "   <tr>\r\n";
         $html_code .= "      <td style=\"padding-top: 5px\"><input name=\"submit\" type=\"submit\" id=\"submit\" value=\"Save\">\r\n";
         $html_code .= "      <input name=\"submit\" type=\"submit\" id=\"submit\" value=\"Delete\">\r\n";
         $html_code .= "         <input name=\"pri_key\" type=\"hidden\" id=\"pri_key\" value=\"<?php print \$primary_key_id ?>\">\r\n";
         $html_code .= "         <input name=\"f\" type=\"hidden\" id=\"f\" value=\"<?php print \$save_type ?>\"> \r\n";
         $html_code .= "         <input type=\"reset\" name=\"Reset\" value=\"Revert\"></td>\r\n";
         $html_code .= "         </tr>\r\n";
         $html_code .= "      </table></td>\r\n";
         $html_code .= "   </tr>\r\n";
         $html_code .= "   <input name=\"ref_url\" type=\"hidden\" value=\"<?php print \$_SERVER['REQUEST_URI'] ?>\">\r\n";
      } else {
         $html_code .= "<tr style=\"padding-top: 10px\"><td colspan=\"2\" align=\"center\"><input name=\"submit\" type=\"submit\" id=\"submit\" value=\"Save\">\r\n";
         $html_code .= "   <input name=\"id\" type=\"hidden\" value=\"<?php print \$FORM['id'] ?>\">\r\n";
         $html_code .= "   <input name=\"f\" type=\"hidden\" value=\"<?php print \$FORM['f'] ?>\"></td>\r\n";
         $html_code .= "</tr>\r\n";
      }

      if ( $fields_to_hide ) {
//         if ( !$form_properties['single_page'] ) {
            $fields_to_hide = "\"" . join( "\", \"", $fields_to_hide ) . "\"";
            $html_code .= "<?php\r\n";
            $html_code .= "      \$hidden_fields = array( $fields_to_hide );\r\n";
            $html_code .= "      if ( \$hidden_fields ) {\r\n";
            $html_code .= "         foreach ( \$hidden_fields as \$name ) {\r\n";
            $html_code .= "            print \"<input name=\\\"{$form_field_group_name}[\$name]\\\" type=\\\"hidden\\\" value=\\\"\" . \$FORM['fieldz'][\$name] . \"\\\">\";\r\n";
            $html_code .= "         }\r\n";
            $html_code .= "      }\r\n";
            $html_code .= "?>\r\n";
//         } else {
//         }
      }
         $html_code .= "</form>\r\n";
         $html_code .= "</table>\r\n";

      if ( !$form_properties['single_page'] ) {
         $html_code .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\r\n";

         $html_code .= "<?php\r\n";
         $html_code .= "      \$url_parse = parse_url( \$_SERVER['REQUEST_URI'] );\r\n";
         $html_code .= "      parse_str( \$url_parse['query'], \$post_fields );\r\n";
         $html_code .= "      unset( \$post_fields['f'], \$post_fields['start'], \$post_fields['starts'] );\r\n";
         $html_code .= "      \$fields = array( \"filter_field\", \"filter_operator\", \"filter_data\", \"filter_null\" );\r\n";
         $html_code .= "      if ( \$post_fields ) {\r\n";
         $html_code .= "         foreach ( \$post_fields as \$name => \$value ) {\r\n";
         $html_code .= "            \$post_fields_html .= \"<input name=\\\"\$name\\\" type=\\\"hidden\\\" value=\\\"\$value\\\">\";\r\n";
         $html_code .= "         }\r\n";
         $html_code .= "      }\r\n";
         $html_code .= "?>\r\n";

         $html_code .= "   <tr>\r\n";
         $html_code .= "      <td align=\"center\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"arial_10pt\">\r\n";
         $html_code .= "            <tr>\r\n";
         $html_code .= "               <td>Record:</td>\r\n";
         $html_code .= "               <?php if ( \$FORM['start'] != 0 ) { ?>\r\n";
         $html_code .= "               <form>\r\n";
         $html_code .= "               <?php } ?>\r\n";
         $html_code .= "                  <td><input type=\"submit\" value=\"|<\"><input name=\"start\" type=\"hidden\" id=\"start\" value=\"0\"></td>\r\n";
         $html_code .= "                  <?php print \$post_fields_html ?>\r\n";
         $html_code .= "               </form>\r\n";
         $html_code .= "               <?php if ( \$FORM['start'] != 0 ) { ?>\r\n";
         $html_code .= "               <form>\r\n";
         $html_code .= "               <?php } ?>\r\n";
         $html_code .= "                  <td><input type=\"submit\" value=\"<\"><input name=\"start\" type=\"hidden\" id=\"start\" value=\"<?php print \$FORM['start'] - 1 ?>\"></td>\r\n";
         $html_code .= "                  <?php print \$post_fields_html ?>\r\n";
         $html_code .= "               </form>\r\n";
         $html_code .= "               <?php if ( \$total_records != 0 ) { ?>\r\n";
         $html_code .= "               <form>\r\n";
         $html_code .= "               <?php } else { \$FORM['start'] = -1; } ?>\r\n";
         $html_code .= "                  <td><input name=\"starts\" type=\"text\" id=\"starts\" value=\"<?php print \$FORM['start'] + 1 ?>\" size=\"6\"></td>\r\n";
         $html_code .= "                  <?php print \$post_fields_html ?>\r\n";
         $html_code .= "               </form>\r\n";
         $html_code .= "               <?php if ( \$FORM['start'] < (\$total_records-1) ) { ?>\r\n";
         $html_code .= "               <form>\r\n";
         $html_code .= "               <?php } ?>\r\n";
         $html_code .= "                  <td><input type=\"submit\" value=\">\"><input name=\"start\" type=\"hidden\" id=\"start\" value=\"<?php print \$FORM['start'] + 1 ?>\"></td>\r\n";
         $html_code .= "                  <?php print \$post_fields_html ?>\r\n";
         $html_code .= "               </form>\r\n";
         $html_code .= "               <?php if ( \$total_records != 0 AND \$FORM['start'] < (\$total_records-1) ) { ?>\r\n";
         $html_code .= "               <form>\r\n";
         $html_code .= "               <?php } ?>\r\n";
         $html_code .= "                  <td><input type=\"submit\" value=\">|\"><input name=\"start\" type=\"hidden\" id=\"start\" value=\"<?php print \$total_records - 1 ?>\"></td>\r\n";
         $html_code .= "                  <?php print \$post_fields_html ?>\r\n";
         $html_code .= "               </form>\r\n";
         $html_code .= "               <form>\r\n";
         $html_code .= "                  <td><input type=\"submit\" value=\">*\"><input name=\"f\" type=\"hidden\" id=\"f\" value=\"add\"></td>\r\n";
         $html_code .= "               </form>\r\n";
         $html_code .= "               <td>of <?php print \$total_records ?></td>\r\n";
         $html_code .= "            </tr>\r\n";
         $html_code .= "         </table></td>\r\n";
         $html_code .= "   </tr>\r\n";
         $html_code .= "   <tr>\r\n";
         $html_code .= "      <td><img src=\"/images/non.gif\" width=\"1\" height=\"10\"></td>\r\n";
         $html_code .= "   </tr>\r\n";
         $html_code .= "</table>\r\n";

         $html_code .= "<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" class=\"arial_10pt\">\r\n";
         $html_code .= "   <tr> \r\n";
         $html_code .= "      <td colspan=\"2\" bgcolor=\"#808080\"><img src=\"../images/non.gif\" width=\"1\" height=\"1\"></td>\r\n";
         $html_code .= "   </tr>\r\n";
         $html_code .= "   <tr> \r\n";
         $html_code .= "      <td colspan=\"2\"><img src=\"../images/non.gif\" width=\"1\" height=\"10\"></td>\r\n";
         $html_code .= "   </tr>\r\n";
         $html_code .= "   <tr> \r\n";
         $html_code .= "      <form>\r\n";
         $html_code .= "         <td style=\"padding-left: 10px\">Quick Filter \r\n";
         $html_code .= "            <?php if ( \$FORM['filter_operator'] ) { ?>\r\n";
         $html_code .= "            <span class=\"arial_11px\">[<a href=\"<?php print \$_SERVER['PHP_SELF']; ?>?filter_data=<?php print \$FORM['filter_data'] ?>\">Clear Filter</a>]</span>\r\n";
         $html_code .= "            <?php } ?>\r\n";
         $html_code .= "            <br> <select name=\"filter_field\">\r\n";
         $html_code .= "               <option value=\"\">Field Name</option>\r\n";
         $html_code .= "               <?php print array_to_html_options( \$filter_field_names, \$FORM['filter_field'], \"values\" ) ?> </select> <select name=\"filter_operator\" id=\"filter_operator\">\r\n";
         $html_code .= "               <?php print array_to_html_options( \$filter_operators, \$FORM['filter_operator'] ) ?> </select> <input name=\"filter_data\" type=\"text\" id=\"filter_data\" size=\"15\" value=\"<?php print \$FORM['filter_data'] ?>\"> \r\n";
         $html_code .= "            <input type=\"submit\" value=\"Go\"></td>\r\n";
         $html_code .= "      </form>\r\n";
         $html_code .= "      <form>\r\n";
         $html_code .= "         <td align=\"right\" style=\"padding-right: 10px\">Where Statement<br> <input name=\"where_data\" type=\"text\" id=\"where_data\" value=\"<?php print htmlspecialchars( \$FORM['where_data'] ) ?>\"> <input type=\"submit\" value=\"Go\"></td>\r\n";
         $html_code .= "      </form>\r\n";
         $html_code .= "   </tr>\r\n";
         $html_code .= "</table>\r\n";
      }

      $table_width = ( $form_properties['table_width'] ) ? $form_properties['table_width'] : "98%";
      $table_class = ( $form_properties['table_class'] ) ? "class='" . $form_properties['table_class'] . "'" : "";

      $css_code .= "<style type=\"text/css\">\r\n";
      $css_code .= "<!--\r\n";
      if ( !$form_properties['single_page'] ) {
         $css_code .= "   .arial_11px { font-family: Arial; font-size: 11px; line-height: normal; }\r\n";
         $css_code .= "   .arial_10pt { font-family: Arial; font-size: 10pt; line-height: normal; }\r\n";
         $css_code .= "   .verdana_10px { font-family: Verdana; font-size: 10px; }\r\n";
         $css_code .= "   .verdana_09px { font-family: Verdana; font-size: 9px; }\r\n";
      }
      $css_code .= "   .tb_title { padding: 7px 0 5px 5px }\r\n";
      $css_code .= "   .tb_field { padding: 5px 5px 5px 0 }\r\n";
      $css_code .= "-->\r\n";
      $css_code .= "</style>\r\n";

      $result = '<table width="' . $table_width . '" border="0" cellspacing="0" cellpadding="0" align="center" style="font-family: Arial; font-size: 10pt;" ' . $table_class . '>' . "\r\n";

      if ( $form_properties['popup_save'] ) {
         $result .= '<form method="post" target="_blank" action="<?php print $_SERVER[\'PHP_SELF\'] ?>">' . "\r\n";
      } else {
         $result .= '<form method="post" action="<?php print $_SERVER[\'PHP_SELF\'] ?>">' . "\r\n";
      }
      $result .= '<tr><td colspan="2"><img src="/images/non.gif" width="1" height="5"></td></tr><tr></tr>' . "\r\n";
      $result .= $html_code;
      #$result .= '<tr><td colspan="2"><img src="/images/non.gif" width="1" height="5"></td></tr><tr></tr>' . "\r\n";
      #$result .= '</table>';

      #$result = '<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">' . $html_code . '</table>';

      $html_start = '<html>' . "\r\n";
      $html_start .= '<head>' . "\r\n";
      $html_start .= "<title>Add/Edit $table_name : $connection_parameters[database_name]</title>\r\n";
      $html_start .= '</head>' . "\r\n";
      $html_start .= '<body>' . "\r\n";
         
      $html_end = '</body>' . "\r\n";
      $html_end .= '</html>' . "\r\n";

      $output_code = $php_code . $html_start . $css_code . $result . $html_end;
      if ( $show_output_code ) {
         header( "Content-type: text/html" );
         $output_code = str_replace( " include( \"", " #include( \"", $output_code );
         $temp_file = tempnam( "/tmp", gmdate( "Y-m-d_H:i:s" ) . "_" . rand( 0, 100000 ) );
         $handle = fopen( $temp_file, "w" );
         fwrite( $handle, $output_code );
         fclose( $handle );

         if ( !function_exists(array_to_html_options) ) {
            function array_to_html_options ( $associate_array, $selected_item = array() ) {
               return associate_array_to_html_options ( $associate_array, $selected_item );
            }
         }
         include( $temp_file );
         exit;
      }
      return $output_code;
   }

?>