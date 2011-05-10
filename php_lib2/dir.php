<?php
   function files_in_dir ( $directory, $filter = "/\./", $dont_lowercase = 0 ) {
      $file_names = array( );
      if ( !preg_match( "/\/$/", $directory ) ) {
         $directory .= "/";
      }
      $directory_handle = @opendir( $directory );
      if ( $directory_handle ) {
         while ( $file_name = readdir($directory_handle) ) {
            if ( !is_dir($directory . $file_name) and preg_match($filter, $file_name) ) {
               if ( $dont_lowercase ) {
                  array_push( $file_names, $file_name );
               } else {
                  array_push( $file_names, strtolower($file_name) );
               }
            }
         }
         closedir( $directory_handle );
         sort( $file_names );
         return $file_names;
      }
   }

   function dirs_in_dir ( $directory ) {
      $file_names = array( );
      if ( !preg_match( "/\/$/", $directory ) ) {
         $directory .= "/";
      }
      $directory_handle = opendir( $directory );
      while ( $file_name = readdir($directory_handle) ) {
         if ( is_dir($directory . $file_name) ) {
            array_push( $file_names, strtolower($file_name) );
         }
      }
      closedir( $directory_handle );
      sort( $file_names );
      return $file_names;
   }
?>