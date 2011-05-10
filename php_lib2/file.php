<?php
   if ( preg_match( "/\(win32\)/i", $_SERVER['SERVER_SOFTWARE'] ) ) {
      $global_line_deliminater = "\r\n";
   } else if ( preg_match( "/\(darwin\)/i", $_SERVER['SERVER_SOFTWARE'] ) ) {
      #For Macintosh
      $global_line_deliminater = "\r";
   } else {
   #} else if ( preg_match( "/\(unix\)/i", $_SERVER['SERVER_SOFTWARE'] ) ) {
      $global_line_deliminater = "\n";
   }

   function file_put ( $filename = "", $contents_array = array() ) {
      #Opposite of file()
      if ( !$filename ) return;
      $handle = fopen( $filename, "w" );
      fwrite( $handle, join( "", $contents_array ) );
      fclose( $handle );
   }

   if(!function_exists('file_put_contents')) {
      function file_put_contents ( $filename, $contents ) {
         #Opposite of file_get_contents()
         $handle = fopen( $filename, "w" );
         fwrite( $handle, $contents );
         fclose( $handle );
      }
   }

   function file_add_line ( $filename, $new_line, $line_ender = "" ) {
      global $global_line_deliminater;
      $new_line = preg_replace( "/[\r\n]+$/", "", $new_line );
      $handle = fopen( $filename, "a" );
      $line_ender = ( $line_ender ) ? $line_ender : $global_line_deliminater;
      fwrite( $handle, $new_line . $line_ender );
      fclose( $handle );
   }

   function file_append ( $filename, $new_lines ) {
      $handle = fopen( $filename, "a" );
      fwrite( $handle, $new_lines );
      fclose( $handle );
   }

   function file_get_line ( $filename, $line_number ) {
      #note - $line_number starts with 1 but file starts with 0
      if ( $line_number < 1 ) {
         return FALSE;
      }
      $file_contents = file( $filename );
      if ( $line_number > count($file_contents) ) {
         return FALSE;
      }
      return $file_contents[$line_number-1];
      #$handle = fopen( $filename, "r" );
      #$buffer = fgets( $handle );
   }

   function file_put_line ( $filename, $line_number, $new_line, $line_deliminater = "\n" ) {
      #Opposite of file_get_line
      if ( $line_number < 1 ) {
         return FALSE;
      }
      $file_contents = file( $filename );
      $file_contents[$line_number-1] = chop($new_line) . $line_deliminater;
      for ( $x = 1; $x <= count($file_contents); $x++ ) {
         if ( !$file_contents[$x-1] ) {
            $file_contents[$x-1] = $line_deliminater;
         }
      }
      file_put( $filename, $file_contents );
   }

   function file_del_line ( $filename, $line_number ) {
      #Opposite of file_ins_line
      if ( $line_number < 1 ) {
         return FALSE;
      }
      $file_contents = file( $filename );
      $file_contents[$line_number-1] = "";
      file_put( $filename, $file_contents );
   }

/* function file2($filename) {
        $fp = fopen($filename, "rb");
        $buffer = fread($fp, filesize($filename));
        fclose($fp);
        $lines = preg_split("/\r?\n|\r/", $buffer);
        return $lines;
}

$fd = fopen ("log_file.txt", "r");
while (!feof ($fd)) {
   $buffer = fgets($fd, 4096);
   $lines[] = $buffer;
}
fclose ($fd);

   function bs_file( $filename ) {
      $fp = @fopen($filename, "r");
      if (!($fp)) {
        return 0;
      }
      while (!feof($fp)) {
        $temp .= fread($fp, 4096);
      }
      $arr = explode("\n", $temp);
      return $arr;
   }
*/

?>
