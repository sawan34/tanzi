<?php
/*

*/
  //====================================
  //SHORTCODE: wholesale_pricing_msgs_by_rule
  //====================================
  
  //shortcode documentation here - wholestore
  //WHOLESTORE MESSAGES SHORTCODE     'vtwpr_wholesale_pricing_store_msgs'
  /* ================================================================================= 
  => "rules" parameter is Required - Show msgs only for these rules => if not supplied, all msgs will be produced
  
   A list can be a single code [ example: rules => '123' }, or a group of codes [ example: rules => '123,456,789' }  with no spaces in the list

  As a shortcode:
    [wholesale_pricing_msgs_by_rule  rules="10,15,30"]
  
  As a template code with a passed variable containing the list:
    $rules="10,15,30"; //or it is a generated list 
    echo do_shortcode('[wholesale_pricing_msgs_by_rule rules=' .$rules. ']');
        OR
    echo do_shortcode('[wholesale_pricing_msgs_by_rule  rules="10,15,30"]');
   

  ====================================
  PARAMETER DEFAULTS and VALID VALUES
  ==================================== 
        rules => '123,',            //'123,456,789'     
  ================================================================================= */
  //
  add_shortcode('wholesale_pricing_msgs_by_rule','vtwpr_wholesale_pricing_msgs_by_rule');   
  function vtwpr_wholesale_pricing_msgs_by_rule($atts) {
    global $vtwpr_rules_set, $post, $vtwpr_setup_options;
    extract(shortcode_atts (
      array (
        rules => '',            //'123,456,789'                                      
      ), $atts));  //override default value with supplied parameters...

    //if no lists are present, then the skip tests are all there is.  Print the msg and exit.
    if ($rules <= ' ' ){ 
      return;      
    }
    
    vtwpr_set_selected_timezone();


    $output = '<div class="vtwpr-rule-msg-area">';
    $msg_counter = 0;
    
    $vtwpr_rules_set = get_option( 'vtwpr_rules_set' );
    
    $rules_array = explode(",", $rules);   //remove comma separator, make list an array  
      
    $sizeof_rules_set = sizeof($vtwpr_rules_set);
    for($i=0; $i < $sizeof_rules_set; $i++) { 

      //BEGIN skip tests      
      if ( $vtwpr_rules_set[$i]->rule_status != 'publish' ) {
        continue;
      }      

      $rule_is_date_valid = vtwpr_rule_date_validity_test($i);
      if (!$rule_is_date_valid) {
         continue;
      }  
      //IP is immediately available, check against Lifetime limits
      if ( (defined('VTWPR_PRO_DIRNAME')) && ($vtwpr_setup_options['use_lifetime_max_limits'] == 'yes') )  {  
        $rule_has_reached_lifetime_limit = vtwpr_rule_lifetime_validity_test($i,'shortcode');
        if ($rule_has_reached_lifetime_limit) {
           continue;
        }
      }

      //END skip tests
      
      //INclusion test begin  -  all are implicit 'or' functions     
    
      if (in_array($vtwpr_rules_set[$i]->post_id, $rules_array)) {
        $msg_counter++;
        $output .= vtwpr_store_deal_msg($i);  //Print
        continue;
      }
 
       
    } //end 'for' loop
    
    if ($msg_counter == 0) {
      return;
    }

    //close owning div 
    $output .= '</div>';
  //  vtwpr_enqueue_front_end_css();
        
    return $output;  
  }


  //====================================
  //SHORTCODE: wholesale_pricing_msgs_standard
  //====================================
  
  //shortcode documentation here - wholestore
  //WHOLESTORE MESSAGES SHORTCODE     'vtwpr_wholesale_pricing_msgs_standard'
  /* ================================================================================= 
  => rules is OPTIONAL - Show msgs only for these rules => if not supplied, all msgs will be produced
  
   A list can be a single code [ example: rules => '123' }, or a group of codes [ example: rules => '123,456,789' }  with no spaces in the list
  A switch can be sent to just display the whole store messages
  
  As a shortcode:
    [wholesale_pricing_whole_store_msgs  rules="10,15,30"]
  
  As a template code with a passed variable containing the list:
    $rules="10,15,30"; //or it is a generated list 
    echo do_shortcode('[wholesale_pricing_msgs_standard rules=' .$rules. ']');
        OR
    echo do_shortcode('[wholesale_pricing_msgs_standard  rules="10,15,30"]');
    echo do_shortcode('[wholesale_pricing_msgs_standard  wholestore_msgs_only="yes"  rules="10,15,30" ]');     

  ====================================
  PARAMETER DEFAULTS and VALID VALUES
  ==================================== 
        type => 'cart',            //'cart' (default) / 'catalog' / 'all' ==> "cart" msgs = cart rules type, "catalog" msgs = realtime catalog rules type        
                                       // AND (implicit)
        wholestore_msgs_only => 'no',  //'yes' / 'no' (default) 
                                       // AND [implicit]
                                       //   (  
        roles => '',          //'Administrator,Customer,Not logged in (just visiting),Member'  
                                       // OR  [implicit]
        rules => '',            //'123,456,789'     
                                       // OR  [implicit]
        product_category => '',  //'123,456,789'      only active if in this list / 'any' - if on a category page, show any msg for that category
                                      // OR  [implicit]
        plugin_category => ''   //'123,456,789'      only active if in this list 
                                      // OR  [implicit]                                              
         products => ''          //'123,456,789'    (ONLY WORKS in the LOOP, or if the Post-id is available as a passed variable ) / 'any' - if on a product page, show any msg for that product
                                       //   )       
  ================================================================================= */
  //
  add_shortcode('wholesale_pricing_msgs_standard','vtwpr_wholesale_pricing_msgs_standard');
  add_shortcode('wholesale_pricing_store_msgs','vtwpr_wholesale_pricing_msgs_standard'); //for backwards compatability   
  function vtwpr_wholesale_pricing_msgs_standard($atts) {
    global $vtwpr_rules_set, $post, $vtwpr_setup_options, $vtwpr_info, $wpdb;
    extract(shortcode_atts (
      array (
        type => 'cart',            //'cart' (default) / 'catalog' / 'all' ==> "cart" msgs = cart rules type, "catalog" msgs = realtime catalog rules type        
                                       // AND (implicit)
        wholestore_msgs_only => 'no',  //'yes' / 'no' (default) 
                                       // AND [implicit]
                                       //   (  
        roles => '',          //'Administrator,Customer,Not logged in (just visiting),Member'  
                                       // OR  [implicit]
        rules => '',            //'123,456,789'     
                                       // OR  [implicit]
        product_category => '',  //'123,456,789'    / 'any' - if on a category page, show any msg for that category   
                                        // OR  [implicit]
        plugin_category => '',   //'123,456,789'       
                                                // OR  [implicit]                                   
        products => ''          //'123,456,789'    (ONLY WORKS in the LOOP, or if the Post is available )   / 'any' - if on a product page, show any msg for that product
                                       //   )                                  
      ), $atts));  //override default value with supplied parameters...
    
    vtwpr_set_selected_timezone();


    $output = '<div class="vtwpr-store-deal-msg-area">';
    $msg_counter = 0;
    
    $vtwpr_rules_set = get_option( 'vtwpr_rules_set' );
    
    $sizeof_rules_set = sizeof($vtwpr_rules_set);
    for($i=0; $i < $sizeof_rules_set; $i++) { 

      //BEGIN skip tests      
      if ( $vtwpr_rules_set[$i]->rule_status != 'publish' ) {
        continue;
      }      

      $rule_is_date_valid = vtwpr_rule_date_validity_test($i);
      if (!$rule_is_date_valid) {
         continue;
      }  
      //IP is immediately available, check against Lifetime limits
      if ( (defined('VTWPR_PRO_DIRNAME')) && ($vtwpr_setup_options['use_lifetime_max_limits'] == 'yes') )  {  
        $rule_has_reached_lifetime_limit = vtwpr_rule_lifetime_validity_test($i,'shortcode');
        if ($rule_has_reached_lifetime_limit) {
           continue;
        }
      }

      $exit_stage_left = 'no';
      switch( $type ) {
        case 'cart':
          if ($vtwpr_rules_set[$i]->rule_execution_type == 'display') {
            $exit_stage_left = 'yes';
          }
          break;
        case 'catalog':                                                                                   
          if ($vtwpr_rules_set[$i]->rule_execution_type == 'cart') {
            $exit_stage_left = 'yes';
          }  
          break;
        default:
          break; 
      }     
      if ($exit_stage_left == 'yes') {
         continue;
      }      
    
      if ($wholestore_msgs_only == 'yes') {
        if ( ($vtwpr_rules_set[$i]->inPop != 'wholeStore') && ($vtwpr_rules_set[$i]->actionPop != 'wholeStore' ) ) {
          continue;
        }
      } 
      
      if ($roles > ' ') {
        $userRole = vtwpr_get_current_user_role();
        $userRole_name = translate_user_role( $userRole );
        $roles_array = explode(",", $roles);   //remove comma separator, make list an array
        if (!in_array($userRole_name, $roles_array)) {
          continue;
        }
      }
      //END skip tests
      
      //INclusion test begin  -  all are implicit 'or' functions     
      
      //if no lists are present, then the skip tests are all there is.  Print the msg and exit.
      if (($rules <= ' ' ) && ($products <= ' ') && ($products <= '')) { 
        $msg_counter++;
        $output .= vtwpr_store_deal_msg($i);  //Print
        continue;      
      }
      
      if ($rules > ' ') {
        $rules_array = explode(",", $rules);   //remove comma separator, make list an array
        if (in_array($vtwpr_rules_set[$i]->post_id, $rules_array)) {
          $msg_counter++;
          $output .= vtwpr_store_deal_msg($i);  //Print
          continue;
        }
      } 
      


      if ($product_category > ' ') {
        $product_category_array = explode(",", $product_category);   //remove comma separator, make list an array
        if ( ( array_intersect($vtwpr_rules_set[$i]->prodcat_in_checked,  $product_category_array ) ) ||
             ( array_intersect($vtwpr_rules_set[$i]->prodcat_out_checked, $product_category_array ) ) ) {  
           $msg_counter++;
           $output .= vtwpr_category_deal_msg($i);
            continue; //only output the msg once 
        }
      } 

      if ($plugin_category > ' ') {
        $plugin_category_array = explode(",", $plugin_category);   //remove comma separator, make list an array
        if ( ( array_intersect($vtwpr_rules_set[$i]->rulecat_in_checked,  $plugin_category_array ) ) ||
             ( array_intersect($vtwpr_rules_set[$i]->rulecat_out_checked, $plugin_category_array ) ) ) {  
           $msg_counter++;
           $output .= vtwpr_category_deal_msg($i);
            continue; //only output the msg once 
        }
      }      
            
          //**********************************
          // ONLY works in the loop
          //**********************************
      switch( true ) {
        /* FUTURE enhancement => needs a bunch of functions pulled from apply-rules and put into pro-functions.php ...
        http://stackoverflow.com/questions/19558545/check-if-current-page-is-category-page-in-wordpress
        http://stackoverflow.com/questions/3435945/wordpress-display-other-posts-from-current-category?rq=1
        case ( $products == 'any' ):
            //if product in a category rule  OR in a product rule
            switch( true ) {
                case ( $vtwpr_rules_set[$i]->inPop == 'groups' ):
                  $prod_cat_list = wp_get_object_terms( $cart_item['product_id'], $vtwpr_info['parent_plugin_taxonomy'], $args = array('fields' => 'ids') );
                  $rule_cat_list = wp_get_object_terms( $cart_item['product_id'], $vtwpr_info['rulecat_taxonomy'], $args = array('fields' => 'ids') ); 
                break;
            }
          break;   */
        case ( $products > ' ' ):                                                                                   
            $products_array = explode(",", $products);   //remove comma separator, make list an array
            // $post->ID = $product_id in this instance
            if (in_array($post->ID, $products_array)) {
              $msg_counter++;
              $output .= vtwpr_store_deal_msg($i);  //Print
              $exit_stage_left = 'yes';
            }  
          break;
        default:
          break; 
      }     
      if ($exit_stage_left == 'yes') {
         continue;
      }     
      //PRINT test end     
       
    } //end 'for' loop
    
    if ($msg_counter == 0) {
      return;
    }

    //close owning div 
    $output .= '</div>';
  //  vtwpr_enqueue_front_end_css();
        
    return $output;  
  }
  
  function vtwpr_store_deal_msg($i) {
    global $vtwpr_rules_set;
    $output  = '<span class="vtwpr-store-deal-msg" id="vtwpr-store-deal-msg-' . $vtwpr_rules_set[$i]->post_id . '">';
    $output .= stripslashes($vtwpr_rules_set[$i]->discount_product_full_msg);
    $output .= '</span>';
    $output .= '<span class="vtwpr-line-skip-with-display-block"></span>';
    return $output;
  }
 //====================================
 //SHORTCODE: wholesale_pricing_msgs_by_category
 //==================================== 
 
 //shortcode documentation here - category
 //STORE CATEGORY MESSAGES SHORTCODE    vtwpr_wholesale_pricing_msgs_by_category
  /* ================================================================================= 
  => either prodcat_id_list or rulecat_id_list or rules is REQUIRED
  => if both lists supplied, the shortcode will find rule msgs in EITHER prodcat_id_list OR rulecat_id_list OR rules.
  
        A list can be a single code [ example: rules => '123' }, or a group of codes [ example: rules => '123,456,789' }  with no spaces in the list 
        
        REQUIRED => Data MUST be sent in ONE of the list parameters, or nothing is returned.
        
  As a shortcode:
    [wholesale_pricing_msgs_by_category  prodcat_id_list="10,15,30"  rulecat_id_list="12,17,32"]
  
  As a template code with a passed variable containing the list:
    to show only the current category messages, for example:
    GET CURRENT CATEGORY 
    
    if (is_category()) {
      $prodcat_id_list = get_query_var('cat');
      echo do_shortcode('[wholesale_pricing_msgs_by_category  prodcat_id_list=' .$prodcat_id_list. ']');
    }
        OR 
    USING A HARDCODED CAT LIST   
    echo do_shortcode('[wholesale_pricing_msgs_by_category  prodcat_id_list="10,15,30" ]');

  ====================================
  PARAMETER DEFAULTS and VALID VALUES
  ====================================
          type => 'cart',     //'cart' (default) / 'catalog' / 'all' ==> "cart" msgs = cart rules type, "catalog" msgs = realtime catalog rules type 
                                // AND [implicit]                                               
                                //   ( 
        product_category => '',  //'123,456,789'      only active if in this list
                                // OR  [implicit]
        plugin_category => ''   //'123,456,789'      only active if in this list
                                //   )                      
  ================================================================================= */
  add_shortcode('wholesale_pricing_msgs_by_category','vtwpr_wholesale_pricing_msgs_by_category');   
  add_shortcode('wholesale_pricing_category_msgs','vtwpr_wholesale_pricing_msgs_by_category');  
  function vtwpr_wholesale_pricing_msgs_by_category($atts) {
    global $vtwpr_rules_set, $vtwpr_setup_options;
    extract(shortcode_atts (
      array (
        type => 'cart',     //'cart' (default) / 'catalog' / 'all' ==> "cart" msgs = cart rules type, "catalog" msgs = realtime catalog rules type 
                                // AND [implicit]                                               
                                //   ( 
        product_category => '',  //'123,456,789'      only active if in this list
                                // OR  [implicit]
        plugin_category => ''   //'123,456,789'      only active if in this list
                                //   ) 
      ), $atts));               
    
    vtwpr_set_selected_timezone();
    
    if ( ($product_category <= ' ') && ($plugin_category <= ' ') && ($rules <= ' ') ) {   //MUST supply one or the other
       return;
    }
    
    $vtwpr_rules_set = get_option( 'vtwpr_rules_set' );

    $output = '<div class="vtwpr-category-deal-msg-area">';
    $msg_counter = 0;
    
    $sizeof_rules_set = sizeof($vtwpr_rules_set);
    for($i=0; $i < $sizeof_rules_set; $i++) { 

      if ( $vtwpr_rules_set[$i]->rule_status != 'publish' ) {
        continue;
      }      
      
      $rule_is_date_valid = vtwpr_rule_date_validity_test($i);
      if (!$rule_is_date_valid) {
         continue;
      }  
      if ( (defined('VTWPR_PRO_DIRNAME')) && ($vtwpr_setup_options['use_lifetime_max_limits'] == 'yes') )  {
      //IP is immediately available, check against Lifetime limits
        $rule_has_reached_lifetime_limit = vtwpr_rule_lifetime_validity_test($i,'shortcode');
        if ($rule_has_reached_lifetime_limit) {
           continue;
        }
      }

      $exit_stage_left = 'no';
      switch( $type ) {
        case 'cart':
          if ($vtwpr_rules_set[$i]->rule_execution_type == 'display') {
            $exit_stage_left = 'yes';
          }
          break;
        case 'catalog':                                                                                   
          if ($vtwpr_rules_set[$i]->rule_execution_type == 'cart') {
            $exit_stage_left = 'yes';
          }  
          break;
        case 'all':
          break; 
      }     
      if ($exit_stage_left == 'yes') {
         continue;
      }      

      //the rest are implied 'or' relationships


      if ($product_category > ' ') {
        $product_category_array = explode(",", $product_category);   //remove comma separator, make list an array
        if ( ( array_intersect($vtwpr_rules_set[$i]->prodcat_in_checked,  $product_category_array ) ) ||
             ( array_intersect($vtwpr_rules_set[$i]->prodcat_out_checked, $product_category_array ) ) ) {  
           $msg_counter++;
           $output .= vtwpr_category_deal_msg($i);
            continue; //only output the msg once 
        }
      } 

      if ($plugin_category > ' ') {
        $plugin_category_array = explode(",", $plugin_category);   //remove comma separator, make list an array
        if ( ( array_intersect($vtwpr_rules_set[$i]->rulecat_in_checked,  $plugin_category_array ) ) ||
             ( array_intersect($vtwpr_rules_set[$i]->rulecat_out_checked, $plugin_category_array ) ) ) {  
           $msg_counter++;
           $output .= vtwpr_category_deal_msg($i);
            continue; //only output the msg once 
        }
      }
      
    }
    
    if ($msg_counter == 0) {
      return;
    }

    //close owning div 
    $output .= '</div>';  
 //   vtwpr_enqueue_front_end_css();
    
    return $output;  
  }
  
  function vtwpr_category_deal_msg($i) {
    global $vtwpr_rules_set;
    $output  = '<span class="vtwpr-category-deal-msg" id="vtwpr-category-deal-msg-' . $vtwpr_rules_set[$i]->post_id . '">';
    $output .= stripslashes($vtwpr_rules_set[$i]->discount_product_full_msg);
    $output .= '</span>';
    $output .= '<span class="vtwpr-line-skip-with-display-block"></span>';
    return $output;
  }
//====================================
 //SHORTCODE: wholesale_pricing_msgs_advanced
 //==================================== 
 
 //shortcode documentation here - advanced
 //ADVANCED MESSAGES SHORTCODE    vtwpr_wholesale_pricing_msgs_advanced
  /* ================================================================================= 
   
        A list can be a single code [ example: rules => '123' }, or a group of codes [ example: rules => '123,456,789' }  with no spaces in the list 
        
        NB - please be careful to follow the comma use exactly as described!!!  
        
  As a shortcode:
    [wholesale_pricing_msgs_advanced  
        group1_type => 'cart'
        group1_and_or_wholestore_msgs_only => 'and'
        group1_wholestore_msgs_only => 'no'
          and_or_group1_to_group2 => 'and'
        group2_rules => ''
        group2_and_or_roles => 'and'
        group2_roles => ''
          and_or_group2_to_group3 => 'and'
        group3_product_category => ''
        group3_and_or_plugin_category => 'or'
        group3_plugin_category => ''   
    ]
  
  As a template code with passed variablea
    echo do_shortcode('[wholesale_pricing_msgs_advanced  
        group1_type => 'cart'
        group1_and_or_wholestore_msgs_only => 'and'
        group1_wholestore_msgs_only => 'no'
          and_or_group1_to_group2 => 'and'
        group2_rules => ''
        group2_and_or_roles => 'and'
        group2_roles => ''
          and_or_group2_to_group3 => 'and'
        group3_product_category => ''
        group3_and_or_plugin_category => 'or'
        group3_plugin_category => '' 
    ]');
  
  ====================================
  PARAMETER DEFAULTS and VALID VALUES
  ====================================
                                                    //   (  group 1
        group1_type => 'cart',                   //'cart' (default) / 'catalog' / 'all' ==> "cart" msgs = cart rules type, "catalog" msgs = realtime catalog rules type  
        group1_and_or_wholestore_msgs_only => 'and', //'and'(default) / 'or' 
        group1_wholestore_msgs_only => 'no',         //'yes' / 'no' (default)   only active if rule active for whole store
                                                   //   )
        and_or_group1_to_group2 => 'and',              //'and'(default) / 'or' 
                                                   //   (  group 2
        group2_rules => '',                   //'123,456,789'          only active if in this list
        group2_and_or_roles => 'and',       //'and'(default) / 'or' 
        group2_roles => '',                 //'Administrator,Customer,Not logged in (just visiting),Member'         Only active if in this list 
                                                   //   )
        and_or_group2_to_group3 => 'and',              //'and'(default) / 'or' 
                                                   //   (  group 3
        group3_product_category => '',                //'123,456,789'      only active if in this list
        group3_and_or_plugin_category => 'or',       //'and' / 'or'(default) 
        group3_plugin_category => ''                 //'123,456,789'      only active if in this list
                                                   //   )   
  ================================================================================= */
  add_shortcode('wholesale_pricing_msgs_advanced','vtwpr_wholesale_pricing_msgs_advanced'); 
  add_shortcode('wholesale_pricing_advanced_msgs','vtwpr_wholesale_pricing_msgs_advanced');  //for backwards compatability  
  function vtwpr_wholesale_pricing_msgs_advanced($atts) {
    global $vtwpr_rules_set, $vtwpr_setup_options;
    extract(shortcode_atts (
      array (
                                                   //   (  group 1
        group1_type => 'cart',                   //'cart' (default) / 'catalog' / 'all' ==> "cart" msgs = cart rules type, "catalog" msgs = realtime catalog rules type  
        group1_and_or_wholestore_msgs_only => 'and', //'and'(default) / 'or' 
        group1_wholestore_msgs_only => 'no',         //'yes' / 'no' (default)   only active if rule active for whole store
                                                   //   )
        and_or_group1_to_group2 => 'and',              //'and'(default) / 'or' 
                                                   //   (  group 2
        group2_rules => '',                   //'123,456,789'          only active if in this list
        group2_and_or_roles => 'and',       //'and'(default) / 'or' 
        group2_roles => '',                 //'Administrator,Customer,Not logged in (just visiting),Member'         Only active if in this list 
                                                   //   )
        and_or_group2_to_group3 => 'and',              //'and'(default) / 'or' 
                                                   //   (  group 3
        group3_product_category => '',                //'123,456,789'      only active if in this list
        group3_and_or_plugin_category => 'or',       //'and' / 'or'(default) 
        group3_plugin_category => ''                 //'123,456,789'      only active if in this list
                                                   //   )
      ), $atts));  //override default value with supplied parameters...
    
    vtwpr_set_selected_timezone();

    $vtwpr_rules_set = get_option( 'vtwpr_rules_set' );

    $output = '<div class="vtwpr-advanced-deal-msg-area">';
    $msg_counter = 0;
//echo 'incoming attributes= ' .$atts. '<br>'; //mwnt 
    $sizeof_rules_set = sizeof($vtwpr_rules_set);
    for($i=0; $i < $sizeof_rules_set; $i++) { 
      
      if ( $vtwpr_rules_set[$i]->rule_status != 'publish' ) {
        continue;
      }      
            
      $rule_is_date_valid = vtwpr_rule_date_validity_test($i);
      if (!$rule_is_date_valid) {
         continue;
      }  
      if ( (defined('VTWPR_PRO_DIRNAME')) && ($vtwpr_setup_options['use_lifetime_max_limits'] == 'yes') )  {
      //IP is immediately available, check against Lifetime limits
        $rule_has_reached_lifetime_limit = vtwpr_rule_lifetime_validity_test($i,'shortcode');
        if ($rule_has_reached_lifetime_limit) {
           continue;
        }
      }
      
      $status =       array (
        'group1_type' => '',                  
        'group1_wholestore_msgs_only' => '',           
        'group2_rules' => '',                        
        'group2_roles' => '',                       
        'group3_product_category' => '',                
        'group3_plugin_category' => '',
        'group1' => '',
        'group2' => '',
        'group3' => '',
        'total' => ''                 
      );
      
      //SET Status success/failed for each parameter
      switch( $group1_type ) {
        case 'cart':      
          if ($vtwpr_rules_set[$i]->rule_execution_type == 'display') {
            $status['group1_type'] = 'failed';      
          } else {
            $status['group1_type'] = 'success';      
          }
          break;
        case 'catalog':                                                                                          
          if ($vtwpr_rules_set[$i]->rule_execution_type == 'cart') {
            $status['group1_type'] = 'failed';          
          } else {
            $status['group1_type'] = 'success';         
          } 
          break;
        case 'all':
          $status['group1_type'] = 'success';
          break;
        default:
          $status['group1_type'] = 'failed';
          break; 
      }     

      if ($group1_wholestore_msgs_only == 'yes') {
        if ( ($vtwpr_rules_set[$i]->inPop == 'wholeStore') || ($vtwpr_rules_set[$i]->actionPop == 'wholeStore' ) ) {
          $status['group1_wholestore_msgs_only'] = 'success';
        } else {
          $status['group1_wholestore_msgs_only'] = 'failed';
        }
      } else {
        $status['group1_wholestore_msgs_only'] = 'success';
      }
            
      if ($group2_roles > ' ') {
        $userRole = vtwpr_get_current_user_role();
        $userRole_name = translate_user_role( $userRole );
        $group2_roles_array = explode(",", $group2_roles);   //remove comma separator, make list an array
        if (in_array($userRole_name, $group2_roles_array)) {
          $status['group2_roles'] = 'success';
        } else {
          $status['group2_roles'] = 'failed';
        }
      } else {
        $status['group2_roles'] = 'success';
      }

      if ($group2_rules > ' ') {
        $group2_rules_array = explode(",", $group2_rules);   //remove comma separator, make list an array
        if (in_array($vtwpr_rules_set[$i]->post_id, $group2_rules_array)) {
          $status['group2_rules'] = 'success';
        } else {
          $status['group2_rules'] = 'failed';
        }
      } else {
        $status['group2_rules'] = 'success';
      }

      if ($group3_product_category > ' ') {
        $group3_product_category_array = explode(",", $group3_product_category);   //remove comma separator, make list an array
        if ( ( array_intersect($vtwpr_rules_set[$i]->prodcat_in_checked,  $group3_product_category_array ) ) ||
             ( array_intersect($vtwpr_rules_set[$i]->prodcat_out_checked, $group3_product_category_array ) ) ) {  
           $status['group3_product_category'] = 'success'; 
        } else {
           $status['group3_product_category'] = 'failed'; 
        }
      } else {
        $status['group3_product_category'] = 'success';
      }

      if ($group3_plugin_category > ' ') {
        $group3_plugin_category_array = explode(",", $group3_plugin_category);   //remove comma separator, make list an array
        if ( ( array_intersect($vtwpr_rules_set[$i]->rulecat_in_checked,  $group3_plugin_category_array ) ) ||
             ( array_intersect($vtwpr_rules_set[$i]->rulecat_out_checked, $group3_plugin_category_array ) ) ) {  
           $status['group3_plugin_category'] = 'success'; 
        } else {
           $status['group3_plugin_category'] = 'failed'; 
        }
      } else {
        $status['group3_plugin_category'] = 'success';
      }
      
      //Evaluate status settings

      //evaluate group1
      switch( $group1_and_or_wholestore_msgs_only ) {
        case 'and':        
            if (($status['group1_type'] == 'success') &&
                ($status['group1_wholestore_msgs_only'] == 'success')) {
              $status['group1'] = 'success';
            } else {
              $status['group1'] = 'failed';
            }            
          break;
        case 'or':
            if (($status['group1_type'] == 'success') ||
                ($status['group1_wholestore_msgs_only'] == 'success')) {
              $status['group1'] = 'success';  
            } else {
              $status['group1'] = 'failed';
            }          
          break;
        default:
            $status['group1'] = 'failed';         
          break;
      } 
      
      //evaluate group2
      switch( $group2_and_or_roles ) {
        case 'and': 
            if (($status['group2_rules'] == 'success') &&
                ($status['group2_roles'] == 'success')) {
              $status['group2'] = 'success';  
            } else {
              $status['group2'] = 'failed';
            }            
          break;
        case 'or':
            if (($status['group2_rules'] == 'success') ||
                ($status['group2_roles'] == 'success')) {
              $status['group2'] = 'success';  
            } else {
              $status['group2'] = 'failed';
            }          
          break;
        default:
            $status['group2'] = 'failed';         
          break;
      } 

      //evaluate group3
      switch( $group3_and_or_plugin_category ) {
        case 'and': 
            if (($status['group3_product_category'] == 'success') &&
                ($status['group3_plugin_category'] == 'success')) {
              $status['group3'] = 'success';  
            } else {
              $status['group3'] = 'failed';
            }            
          break;
        case 'or':
            if (($status['group3_product_category'] == 'success') ||
                ($status['group3_plugin_category'] == 'success')) {
              $status['group3'] = 'success';  
            } else {
              $status['group3'] = 'failed';
            }          
          break;
        default:
            $status['group3'] = 'failed';         
          break;          
      } 

      //evaluate all 3 groups together
      switch( true ) {
        case ( ($and_or_group1_to_group2 == 'and') &&
               ($and_or_group2_to_group3 == 'and') ) : 
            if ( ($status['group1'] == 'success') &&
                 ($status['group2'] == 'success') &&
                 ($status['group3'] == 'success') ) {
              $status['total'] = 'success';  
            } else {
              $status['total'] = 'failed';
            }            
          break;
        case ( ($and_or_group1_to_group2 == 'and') &&
               ($and_or_group2_to_group3 == 'or') ) : 
            if ( (($status['group1'] == 'success')  &&
                  ($status['group2'] == 'success')) ||
                  ($status['group3'] == 'success') ) {
              $status['total'] = 'success';  
            } else {
              $status['total'] = 'failed';
            }            
          break;
        case ( ($and_or_group1_to_group2 == 'or') &&
               ($and_or_group2_to_group3 == 'and') ) : 
            if ( (($status['group1'] == 'success')  ||
                  ($status['group2'] == 'success')) &&
                  ($status['group3'] == 'success') ) {
              $status['total'] = 'success';  
            } else {
              $status['total'] = 'failed';
            }            
          break;
        case ( ($and_or_group1_to_group2 == 'or') &&
               ($and_or_group2_to_group3 == 'or') ) : 
            if ( ($status['group1'] == 'success') ||
                 ($status['group2'] == 'success') ||
                 ($status['group3'] == 'success') ) {
              $status['total'] = 'success';  
            } else {
              $status['total'] = 'failed';
            }            
          break;                    
      } 

      if ($status['total'] == 'success') {
        $msg_counter++;
        $output .= '<span class="vtwpr-advanced-deal-msg" id="vtwpr-advanced-deal-msg-' . $vtwpr_rules_set[$i]->post_id . '">';
        $output .= stripslashes($vtwpr_rules_set[$i]->discount_product_full_msg);
        $output .= '</span>';
        $output .= '<span class="vtwpr-line-skip-with-display-block"></span>';      
      }
      
    } //end 'for' loop
    
    
    if ($msg_counter == 0) {
      return;
    }

    //close owning div 
    $output .= '</div>';
  //  vtwpr_enqueue_front_end_css();
    
    return $output;  
  }


  
  add_shortcode('wholesale_pricing_product_msgs','vtwpr_wholesale_pricing_product_msgs');
	function vtwpr_wholesale_pricing_product_msgs(){
    global $post, $vtwpr_info;


    $product_id = the_ID(); 

        
    //routine has been called, but no product_id supplied or available
    if (!$product_id) {
      return;
    }
   
    vtwpr_get_product_session_info($product_id);
 
    //CUSTOM function created by CUSTOMER
    if (function_exists('custom_show_product_realtime_discount_full_msgs')) {
      custom_show_product_realtime_discount_full_msgs($product_id, $vtwpr_info['product_session_info']['product_rule_full_msg_array']);
      return;
    } 

    $sizeof_msg_array = sizeof($vtwpr_info['product_session_info']['product_rule_full_msg_array']);
    for($y=0; $y < $sizeof_msg_array; $y++) {
      ?>
				<p class="pricedisplay <?php echo wpsc_the_product_id(); ?> vtwpr-single-product-msgs"><?php echo stripslashes($vtwpr_info['product_session_info']['product_rule_full_msg_array'][$y]); ?> </p>
      <?php
    } 
         
    return;
  } 



  
  /**
  ***************************** 
  *** FOR WPEC VERSION 3.9+ ***
  *****************************  
  COPIED FROM WPSC-INCLUDES/PRODUCT-TEMPLATE.PHP  WPEC VERSION 3.8.10  
 * WPSC The Product Price Display
 *
 * @param  $args  (array)   Array of args.
 * @return        (string)  HTML formatted prices
 *
 * @uses   apply_filters()                      Calls 'wpsc_the_product_price_display_old_price_class' passing class and product ID
 * @uses   apply_filters()                      Calls 'wpsc_the_product_price_display_old_price_amount_class' passing class and product ID
 * @uses   apply_filters()                      Calls 'wpsc_the_product_price_display_price_class' passing class and product ID
 * @uses   apply_filters()                      Calls 'wpsc_the_product_price_display_price_amount_class' passing class and product ID
 * @uses   apply_filters()                      Calls 'wpsc_the_product_price_display_you_save_class' passing class and product ID
 * @uses   apply_filters()                      Calls 'wpsc_the_product_price_display_you_save_amount_class' passing class and product ID
 * @uses   wpsc_product_normal_price()          Get the normal price
 * @uses   wpsc_the_product_price()             Get the current price
 * @uses   wpsc_you_save()                      Get pricing saving
 * @uses   wpsc_product_on_special()            Is product on sale?
 * @uses   wpsc_product_has_variations()        Checks if product has variations
 * @uses   wpsc_product_variation_price_from()  Gets the lowest variation price
 * @uses   wpsc_currency_display()              Display price as currency
 */
function vtwpr_the_product_price_display( $args = array() ) {
   global $vtwpr_info,  $vtwpr_setup_options;
  if ( empty( $args['id'] ) )
		$id = get_the_ID();
	else
		$id = (int) $args['id'];

       
 //-+--+-+-+-+-+-+-+-+-+-+-++--+-+-+-+-+-+-+-+-+-+-++--+-+-+-+-+-+-+-+-+-+-+
 //  if $id is a variation and has a parent, sent the PARENT!!!

  //gets all of the info we need and puts it into 'product_session_info'
  vtwpr_get_product_session_info($id);
  
   
 //-+--+-+-+-+-+-+-+-+-+-+-++--+-+-+-+-+-+-+-+-+-+-++--+-+-+-+-+-+-+-+-+-+-+
 //  if $id is a variation, refigure product_yousave_total_amt!!


  //refigure yousave amts for WPEC 
  if (VTWPR_PARENT_PLUGIN_NAME == 'WP E-Commerce') { 
    vtwpr_WPEC_recompute_theme_amts();
  }   
 
  
  //if we have no yousave amt, do the default routine and exit
  if ($vtwpr_info['product_session_info']['product_yousave_total_amt'] == 0) {
     wpsc_the_product_price_display($args);
     return;
  }

	$defaults = array(
		'id' => $id,
		'old_price_text'   => __( 'Old Price: %s', 'wpsc' ),
		'price_text'       => __( 'Price: %s', 'wpsc' ),
		/* translators     : %1$s is the saved amount text, %2$s is the saved percentage text, %% is the percentage sign */
		'you_save_text'    => __( 'You save: %s', 'wpsc' ),
		'old_price_class'  => 'pricedisplay wpsc-product-old-price ' . $id,
		'old_price_before' => '<p %s>',
		'old_price_after'  => '</p>',
		'old_price_amount_id'     => 'old_product_price_' . $id,
		'old_price_amount_class' => 'oldprice',
		'old_price_amount_before' => '<span class="%1$s" id="%2$s">',
		'old_price_amount_after' => '</span>',
		'price_amount_id'     => 'product_price_' . $id,
		'price_class'  => 'pricedisplay wpsc-product-price ' . $id,
		'price_before' => '<p %s>',
		'price_after' => '</p>',
		'price_amount_class' => 'currentprice pricedisplay ' . $id,
		'price_amount_before' => '<span class="%1$s" id="%2$s">',
		'price_amount_after' => '</span>',
		'you_save_class' => 'pricedisplay wpsc-product-you-save product_' . $id,
		'you_save_before' => '<p %s>',
		'you_save_after' => '</p>',
		'you_save_amount_id'     => 'yousave_' . $id,
		'you_save_amount_class' => 'yousave',
		'you_save_amount_before' => '<span class="%1$s" id="%2$s">',
		'you_save_amount_after'  => '</span>',
		'output_price'     => true,
		'output_old_price' => true,
		'output_you_save'  => true,
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );


  $amt = $vtwpr_info['product_session_info']['product_list_price'];
  $amt = vtwpr_format_money_element($amt);
  $old_price  =  $amt;

  $amt = $vtwpr_info['product_session_info']['product_yousave_total_amt'];
  $amt = vtwpr_format_money_element($amt);  
  $you_save            = $amt . '! (' . $vtwpr_info['product_session_info']['product_yousave_total_pct'] . '%)';
  
  $you_save_percentage = $vtwpr_info['product_session_info']['product_yousave_total_pct'];

	// if the product has no variations, these amounts are straight forward...
//	$old_price           = wpsc_product_normal_price( $id );
	$current_price       = wpsc_the_product_price( false, false, $id );
//	$you_save            = wpsc_you_save( 'type=amount' ) . '! (' . wpsc_you_save() . '%)';
//	$you_save_percentage = wpsc_you_save();

//	$show_old_price = $show_you_save = wpsc_product_on_special( $id );
  
  /*
	// but if the product has variations and at least one of the variations is on special, we have
	// a few edge cases...
	if ( wpsc_product_has_variations( $id ) && wpsc_product_on_special( $id ) ) {
		// generally it doesn't make sense to display "you save" amount unless the user has selected
		// a specific variation
		$show_you_save = false;

		$old_price_number = wpsc_product_variation_price_from( $id, array( 'only_normal_price' => true ) );
		$current_price_number = wpsc_product_variation_price_from( $id );

		// if coincidentally, one of the variations are not on special, but its price is equal to
		// or lower than the lowest variation sale price, old price should be hidden, and current
		// price should reflect the "normal" price, not the sales price, to avoid confusion
		if ( $old_price_number == $current_price_number ) {
			$show_old_price = false;
			$current_price = wpsc_product_normal_price( $id );
		}
	}
  */
	// replace placeholders in arguments with correct values
	$old_price_class = apply_filters( 'wpsc_the_product_price_display_old_price_class', $old_price_class, $id );
	$old_price_amount_class = apply_filters( 'wpsc_the_product_price_display_old_price_amount_class', $old_price_amount_class, $id );
	$attributes = 'class="' . esc_attr( $old_price_class ) . '"';
//	if ( ! $show_old_price )
//		$attributes .= ' style="display:none;"';
	$old_price_before = sprintf( $old_price_before, $attributes );
	$old_price_amount_before = sprintf( $old_price_amount_before, esc_attr( $old_price_amount_class ), esc_attr( $old_price_amount_id ) );

	$price_class = 'class="' . esc_attr( apply_filters( 'wpsc_the_product_price_display_price_class', esc_attr( $price_class ), $id )  ) . '"';
	$price_amount_class = apply_filters( 'wpsc_the_product_price_display_price_amount_class', esc_attr( $price_amount_class ), $id );
	$price_before = sprintf( $price_before, $price_class );
	$price_amount_before = sprintf( $price_amount_before, esc_attr( $price_amount_class ), esc_attr( $price_amount_id ) );

	$you_save_class = apply_filters( 'wpsc_the_product_price_display_you_save_class', $you_save_class, $id );
	$you_save_amount_class = apply_filters( 'wpsc_the_product_price_display_you_save_amount_class', $you_save_amount_class, $id );
	$attributes = 'class="' . esc_attr( $you_save_class ) . '"';
//	if ( ! $show_you_save )
//		$attributes .= ' style="display:none;"';
	$you_save_before = sprintf( $you_save_before, $attributes );
	$you_save_amount_before = sprintf( $you_save_amount_before, esc_attr( $you_save_amount_class ), esc_attr( $you_save_amount_id ) );
//	$you_save = wpsc_currency_display ( $you_save );

	$old_price     = $old_price_amount_before . $old_price . $old_price_amount_after;
	$current_price = $price_amount_before . $current_price . $price_amount_after;
	$you_save      = $you_save_amount_before . $you_save . $you_save_amount_after;

	$old_price_text = sprintf( $old_price_text, $old_price );
	$price_text     = sprintf( $price_text, $current_price );
	$you_save_text  = sprintf( $you_save_text, $you_save );

 // if ( $vtwpr_setup_options['show_old_price'] == 'yes' ) {
	if (($output_old_price) && ($old_price_text > ' ')) {
		echo $old_price_before . $old_price_text . $old_price_after . "\n";
  }
	if ( $output_price )
		echo $price_before . $price_text . $price_after . "\n";

 // if ( $vtwpr_setup_options['show_you_save'] == 'yes' ) {
	if ($output_you_save) {
  	if ($you_save_text > ' ') {
      echo $you_save_before . $you_save_text . $you_save_after . "\n";
    } else  
    if ($vtwpr_info['product_session_info']['show_yousave_one_some_msg'] > ' ') {
      echo $vtwpr_info['product_session_info']['show_yousave_one_some_msg'] . "\n";
    }
  }  

  return;   
}

/* ************************************************
  **   Template Tag / Filter  -  Get display info for single product   & return list price amt
  *************************************************** */  
  function vtwpr_show_product_list_price($product_id=null) {
    global $post, $vtwpr_info, $vtwpr_setup_options;    
      
    //can only be executed when WPEC version less than 3.8.9
    if( !(version_compare(strval('3.8.9'), strval(WPSC_VERSION), '>') == 1) ) {   //'==1' = 2nd value is lower
       return;
    } 
    
    
    if ($post->ID > ' ' ) {
      $product_id = $post->ID;
    }
    if (!$product_id) {
      return;
    }    
    $amt = vtwpr_get_product_list_price_amt($product_id);

    //CUSTOM function created by CUSTOMER
    if (function_exists('custom_show_product_list_price_amt')) {
      custom_show_product_list_price_amt($product_id, $amt);
      return;
    }

    if ($amt) {
      ?>
				<p class="pricedisplay <?php echo wpsc_the_product_id(); ?>"><?php _e('Old Price', 'wpsc'); ?>: <span class="oldprice" id="old_product_price_<?php echo wpsc_the_product_id(); ?>"><?php echo $amt; ?></span></p>
      <?php
    } else {
      //original code from wpsc-single_product.php
      ?>
      
      <?php if(wpsc_product_on_special()) : ?>
				<p class="pricedisplay <?php echo wpsc_the_product_id(); ?>"><?php _e('Old Price', 'wpsc'); ?>: <span class="oldprice" id="old_product_price_<?php echo wpsc_the_product_id(); ?>"><?php echo wpsc_product_normal_price(); ?></span></p>
			<?php endif; ?>
      
      <?php
    }        
    return;
  }   

    function vtwpr_get_product_list_price_amt($product_id=null) {
    global $post, $vtwpr_info, $vtwpr_setup_options;
        
   //  only applies if one rule set to $rule_execution_type_selected == 'display'.  Carried in an option, set into info...     
    if ($vtwpr_info['ruleset_has_a_display_rule'] == 'no') {
      return;
    }
    
    if ($post->ID > ' ' ) {
      $product_id = $post->ID;
    }   
    
    //routine has been called, but no product_id supplied or available
    if (!$product_id) {
      return;
    }
   
    vtwpr_get_product_session_info($product_id);
    
    //if the product does not participate in any rule which allows use at display time, only messages are available - send back nothing
    if ( !$vtwpr_info['product_session_info']['product_in_rule_allowing_display']  == 'yes') {
       return;
    }
        
    //refigure yousave amts for WPEC 
    if (VTWPR_PARENT_PLUGIN_NAME == 'WP E-Commerce') { 
      vtwpr_WPEC_recompute_theme_amts();
    }   

    
    //list price
    $amt = $vtwpr_info['product_session_info']['product_list_price'];
    $amt = vtwpr_format_money_element($amt);        
    return $amt;

  }   

  

  /* ************************************************
  ** Template Tag / Filter -  Get display info for single product   & return you save line - amt and pct
  *************************************************** */
	function vtwpr_show_product_you_save($product_id=null){
    global $post, $vtwpr_setup_options, $vtwpr_info;
    
    //can only be executed when WPEC version less than 3.8.9
    if( !(version_compare(strval('3.8.9'), strval(WPSC_VERSION), '>') == 1) ) {   //'==1' = 2nd value is lower
       return;
    }
         
    $pct = vtwpr_get_single_product_you_save_pct($product_id); 
    $amt = $vtwpr_info['product_session_info']['product_yousave_total_amt'];
    $amt = vtwpr_format_money_element($amt);
    
    //CUSTOM function created by CUSTOMER
    if (function_exists('custom_show_single_product_you_save')) {
      custom_show_single_product_you_save($product_id, $pct, $amt);
      return;
    }    

    if ($pct) {
      ?>
				<p class="pricedisplay product_<?php echo wpsc_the_product_id(); ?>"><?php _e('You save', 'wpsc'); ?>: <span class="yousave" id="yousave_<?php echo wpsc_the_product_id(); ?>"><?php echo $amt; ?>! (<?php echo $pct; ?>%)</span></p>
			<?php
    } else {
      //original code from wpsc-single_product.php
      ?>
      
        <?php if(wpsc_product_on_special()) : ?>
					<p class="pricedisplay product_<?php echo wpsc_the_product_id(); ?>"><?php _e('You save', 'wpsc'); ?>: <span class="yousave" id="yousave_<?php echo wpsc_the_product_id(); ?>"><?php echo wpsc_currency_display(wpsc_you_save('type=amount'), array('html' => false)); ?>! (<?php echo wpsc_you_save(); ?>%)</span></p>
				<?php endif; ?>
      
      <?php
     }
    return;
  } 
	
  function vtwpr_get_single_product_you_save_pct($product_id=null){
    global $post, $vtwpr_setup_options, $vtwpr_info;
    
   //  only applies if one rule set to $rule_execution_type_selected == 'display'.  Carried in an option, set into info...     
    if ($vtwpr_info['ruleset_has_a_display_rule'] == 'no') {
      return;
    }
    
    if ($post->ID > ' ' ) {
      $product_id = $post->ID;
    }
            
    //routine has been called, but no product_id supplied or available
    if (!$product_id) {
      return;
    }
        
    vtwpr_get_product_session_info($product_id);
    
    //if the product does not participate in any rule which allows use at display time, only messages are available - send back nothing
    if ( !$vtwpr_info['product_session_info']['product_in_rule_allowing_display']  == 'yes') {
       return;
    }

    //refigure yousave amts for WPEC 
    if (VTWPR_PARENT_PLUGIN_NAME == 'WP E-Commerce') { 
      vtwpr_WPEC_recompute_theme_amts();
    }   

    
    if ( $vtwpr_info['product_session_info']['product_yousave_total_pct']  > 0) {
       return $vtwpr_info['product_session_info']['product_yousave_total_pct'];
    }
     
    return;
  } 



  /* ************************************************
  **   
  *************************************************** */
/*  Now loaded into wp-head directly in wholesale-pricing.php
  function vtwpr_enqueue_front_end_css() {
    global $vtwpr_setup_options;
    if ( $vtwpr_setup_options['use_plugin_front_end_css'] == 'yes' ){
      wp_register_style( 'vtwpr-front-end-style', VTWPR_URL.'/core/css/vtwpr-front-end.css' );  
      wp_enqueue_style('vtwpr-front-end-style');
    }
  }
*/

     
  /* ************************************************
  **   WPSC needs Recompute Discount Info for theme display  

    //refigure yousave amts for WPEC 
    if (VTWPR_PARENT_PLUGIN_NAME == 'WP E-Commerce') { 
      vtwpr_WPEC_recompute_theme_amts();
    }    
  *************************************************** */
  function vtwpr_WPEC_recompute_theme_amts(){
      global $vtwpr_info;  

      if ( ($vtwpr_info['product_session_info']['product_special_price'] > 0) &&
          ($vtwpr_info['product_session_info']['product_special_price'] < $vtwpr_info['product_session_info']['product_list_price']) ) {
         $orig_price = $vtwpr_info['product_session_info']['product_special_price']; 
      } else {
         $orig_price = $vtwpr_info['product_session_info']['product_list_price']; 
      }

      $vtwpr_info['product_session_info']['product_yousave_total_amt'] = ( $orig_price - $vtwpr_info['product_session_info']['product_discount_price'] );
      
      //compute yousave_pct
      $computed_pct =  $vtwpr_info['product_session_info']['product_discount_price'] /  $orig_price ;
      //$computed_pct_2decimals = bcdiv($vtwpr_info['product_session_info']['product_discount_price'] , $orig_price , 2); 
      $computed_pct_2decimals = round( ($vtwpr_info['product_session_info']['product_discount_price'] / $orig_price ) , 2);
      
      $remainder = $computed_pct - $computed_pct_2decimals;
      if ($remainder > 0.005) {
        $yousave_pct = ($computed_pct_2decimals + .01) * 100;
      } else {
        $yousave_pct = $computed_pct_2decimals * 100;
      }
      
      $vtwpr_info['product_session_info']['product_yousave_total_pct'] = $yousave_pct;
       
     return;
  }