<?php
      # the variable $query_string should be set from query_results_begin.php

      $arrow_links = array();
      $add_on = ( $query_string ) ? "?$query_string" : "";
      $add_on_start = ( $add_on ) ? $add_on . "&start" : "?start";

      # $show_double_arrow_results_links - shows the [beg] and [end] links
      # $show_all_results_link - shows the [all] link
      # $pagination_short_form - outputs in this format [prev] Page X of X [next]
//      $pagination_short_form = 1;
//      $show_double_arrow_results_links = 0;
//      $show_all_results_link  = 0;

		if ( $FORM['start'] == 0 ) {
			#print "[<b>Prev</b>]";
			$arrow_links['Prev'] = "";
		} else  {
			$previous_link_start_value = $FORM['start'] - $records_per_page;
			$previous_link_start_value = ( $previous_link_start_value < 0 ) ? 0 : $previous_link_start_value;
         #print "[<a href=\"$_SERVER[SCRIPT_NAME]?$add_on\">Beg</a>] ";
			#print "[<a href=\"$_SERVER[SCRIPT_NAME]?$add_on&start=$previous_link_start_value\">Prev</a>]";
			if ( $total_pages > $maximum_result_pages AND $show_double_arrow_results_links ) {
			   $arrow_links['Beg'] = "$_SERVER[SCRIPT_NAME]$add_on";
			}
			if ( $previous_link_start_value ) {
			   $arrow_links['Prev'] = "$_SERVER[SCRIPT_NAME]$add_on_start=$previous_link_start_value";
			} else {
			   $arrow_links['Prev'] = "$_SERVER[SCRIPT_NAME]$add_on";
			}
		}

      if ( !$pagination_short_form ) {
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
   				#$page_numbers[] = "<b>$page_number</b>";
   				$page_numbers[$page_number] = "";
   			} else {
   				#$page_numbers[] = "<a href=\"$_SERVER[SCRIPT_NAME]?$add_on&start=$x\">$page_number</a>";
   				if ( $x ) {
   				   $page_numbers[$page_number] = "$_SERVER[SCRIPT_NAME]$add_on_start=$x";
   				} else {
   				   $page_numbers[$page_number] = "$_SERVER[SCRIPT_NAME]$add_on";
   				}
   			}
   		}
	   }

//		if ( $page_numbers ) {
//		   print " &nbsp;" . join( " &nbsp; ", $page_numbers ) . "&nbsp; ";
//		} else {
//		   print " No Pages ";
//		}

		if ( $current_page == $total_pages ) {
			#print "[<b>Next</b>]";
			$arrow_links['Next'] = "";
		} else {
			#print "[<a href=\"$_SERVER[SCRIPT_NAME]?$add_on&start=" . ( $FORM['start'] + $records_per_page ) . "\">Next</a>]";
			#print " [<a href=\"$_SERVER[SCRIPT_NAME]?$add_on&start=" . ( ( $total_pages - 1 ) * $records_per_page ) . "\">End</a>]";
			$arrow_links['Next'] = "$_SERVER[SCRIPT_NAME]$add_on_start=" . ( $FORM['start'] + $records_per_page );
			if ( $total_pages > $maximum_result_pages AND $show_double_arrow_results_links ) {
			   $arrow_links['End'] = "$_SERVER[SCRIPT_NAME]$add_on_start=" . ( ( $total_pages - 1 ) * $records_per_page );
			}
		}
      if ( $total_pages > 1 AND $show_all_results_link == 1 ) {
		   #print " [<a href=\"$_SERVER[SCRIPT_NAME]?$add_on&start=all\">All</a>]";
			$arrow_links['All'] = "$_SERVER[SCRIPT_NAME]$add_on_start=all";
		}

      if ( !$dont_display_pagination_output ) {
         output_pagination_html( $arrow_links, $page_numbers );
      }

   function output_pagination_html ( $arrow_links, $page_numbers ) {
      global $pagination_short_form, $current_page, $total_pages;

      if ( isset($arrow_links['Beg']) ) {
         if ( !$arrow_links['Beg'] ) {
            print "[<b>Beg</b>] ";
         } else {
            print "[<a href='{$arrow_links['Beg']}'>Beg</a>] ";
         }
      }
      if ( isset($arrow_links['Prev']) ) {
         if ( !$arrow_links['Prev'] ) {
            print "[<b>Prev</b>] ";
         } else {
            print "[<a href='{$arrow_links['Prev']}'>Prev</a>] ";
         }
      }
      if ( $pagination_short_form ) {
         print "Page $current_page of $total_pages";
      } else {
         if ( $page_numbers ) {
            foreach ( $page_numbers as $key => $value ) {
               if ( !$value ) {
                  $page_numbers_html[] = "<b>$key</b>";
               } else {
                  $page_numbers_html[] = "<a href='$value'>$key</a>";
               }
            }
            print " &nbsp;" . join( " &nbsp; ", $page_numbers_html ) . "&nbsp; ";
         } else {
            print " No Pages ";
         }
      }
      if ( isset($arrow_links['Next']) ) {
         if ( !$arrow_links['Next'] ) {
            print " [<b>Next</b>]";
         } else {
            print " [<a href='{$arrow_links['Next']}'>Next</a>]";
         }
      }
      if ( isset($arrow_links['End']) ) {
         if ( !$arrow_links['End'] ) {
            print " [<b>End</b>]";
         } else {
            print " [<a href='{$arrow_links['End']}'>End</a>]";
         }
      }
      if ( isset($arrow_links['All']) ) {
         if ( !$arrow_links['All'] ) {
            print " [<b>All</b>]";
         } else {
            print " [<a href='{$arrow_links['All']}'>All</a>]";
         }
      }
   }
      
?>