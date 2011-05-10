<?php
   function validate_form ( $fields_to_validate, $form_fields ) {
      foreach ( $fields_to_validate as $field_validation ) {
         $other_valid_chars = preg_quote( $field_validation[4] );
         $form_field_data = $form_fields[$field_validation[0]];
         if ( preg_match( "/(\d+)\+/", $field_validation[2], $preg_results ) ) {
            $field_validation[2] = $preg_results[1] . "," . "100000000000000";
         }
         preg_match( "/(\d+),*(\d+)*/", $field_validation[2], $field_lengths );
         $field_validation[1] = strtolower( $field_validation[1] );
         if ( $field_validation[1] == "alpha" ) {
            $regex_code = "/^[a-z\_" . $other_valid_chars . "]+$/i";
         } else if ( $field_validation[1] == "alphanumeric" ) {
            $regex_code = "/^\w+$/i";
         } else if ( $field_validation[1] == "numeric" ) {
            $regex_code = "/^[0-9]+$/i";
         } else if ( $field_validation[1] == "email" ) {
            $regex_code = "/^[a-z0-9]+([_\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\.[a-z]{2,3}$/i";
         } else if ( $field_validation[1] == "all" ) {
            $regex_code = "/^.+$/i";
         } else if ( $field_validation[1] == "date" ) {
            preg_match( "/(\d{4})-(\d{2})-(\d{2})/", $form_field_data, $date_pieces );
            if ( $date_pieces ) {
               if ( !checkdate( $date_pieces[2], $date_pieces[3], $date_pieces[1] ) ) {
                  return $field_validation[3];
               } else {
                  continue;
               }
            }
         }

         #print "$regex_code - $form_field_data<br>";
         
         if ( !preg_match( $regex_code, $form_field_data ) ) {
            $found_error = 1;
         } else if ( count($field_lengths) == 2 and strlen($form_field_data) != $field_lengths[1] ) {
            $found_error = 1;
         } else if ( count($field_lengths) AND $field_validation[1] == "numeric" and ( $form_field_data < $field_lengths[1] or $form_field_data > $field_lengths[2] ) ) {
            #For 'numeric' the min,max is the value while for everything else it is
            $found_error = 1;
         } else if ( count($field_lengths) AND $field_validation[1] != "numeric" and ( strlen($form_field_data) < $field_lengths[1] or strlen($form_field_data) > $field_lengths[2] ) ) {
            $found_error = 1;
         }
         if ( $found_error ) {
            return $field_validation[3];
         }
      }
   }
?>
