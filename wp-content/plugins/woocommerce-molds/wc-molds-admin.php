<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Check that the user has the required capability 
if (!current_user_can('manage_options'))
{
 wp_die( __('You do not have sufficient permissions to access this page.') );
}
?>

<style type="text/css">
<?php include 'css/admin.css';?>
</style>

<script>
<?php include 'js/admin.js'; ?>
</script>

<div class="wrap"> 

	<?php
	
			// CREATING MOLD OBJECT //
			$mold = new WC_Molds();
	
	###################
	## NEW MOLD PAGE ##
	###################
	
	if(isset($_POST['action']) && (($_POST['action'] == 'new')||($_POST['action'] == 'edit')) ) {
	
		// PAGE TITLE //
		?>
		<h2><img style="margin-right:2px;vertical-align:bottom;" src="<?php echo bloginfo('wpurl'); ?>/wp-content/plugins/woocommerce-molds/assets/images/molds.png">
		<?php
		if($_POST['action'] == 'new') {
			echo "" . __( 'New mold', 'woocommerce-molds' ) . "</h2>";
			## We load the temporary database ##
			$mold->pm_load_temporary_save();
		} else if($_POST['action'] == 'edit') {
			echo "" . __( 'Edit mold', 'woocommerce-molds' ) . "</h2>";
			## We load the database ##
			$mold->pm_load_from_db($_POST['mold_id']);
		}
			
		## This is where our form starts ## ?>
		<form name="moldForm" action="edit.php?post_type=product&page=woocommerce_molds" method="post" onsubmit="if((document.forms['moldForm']['mold_name'].value=='')||(document.forms['moldForm']['mold_name'].value==null)) { alert('Please enter a name.');document.forms['moldForm']['mold_name'].style.boxShadow = '0px 0px 1px 1px #FF0033';document.forms['moldForm']['mold_name'].focus(); return false;}">
		
			<?php ## We don't forget to stay in the "new" mold page ##
			if($_POST['action'] == 'new') { ?>
				<input id="action" name="action" type="hidden" value="new">
			<?php } else if($_POST['action'] == 'edit') { ?>
				<input name="action" type="hidden" value="edit">
				<input type="hidden" name="mold_id" value="<?php if(isset($_POST['mold_id'])) { echo $_POST['mold_id']; } ?>">
			<?php } ?>
			<input id="save" name="save" type="hidden" value="no">
			<input id="reset" name="reset" type="hidden" value="no">
			<input id="remove" name="remove" type="hidden" value="no">
			
			<button type="submit" onclick="document.getElementById('save').value='yes';" class="button button-primary button-large"><?php _e( 'Save', 'woocommerce-molds' ); ?></button>
			<?php if($_POST['action'] == 'edit') {
				$applied = $mold->pm_get_applied(); if($applied=="yes"){$mold->pm_unapply_mold();}
			}
			if($_POST['action'] == 'new') { ?>
				<button type="submit" onclick="document.getElementById('reset').value='yes';" class="button button-primary button-large red"><?php _e( 'Reset', 'woocommerce-molds' ); ?></button>
			<?php }
			
			## We handle the data passing through submitting ##
			// PRODUCT ID
			if (isset($_POST['product'])) {
				$mold->pm_set_product_id($_POST['product']);
			}
			// PRODUCT NAME
			if (isset($_POST['product_name'])) {
				$mold->pm_set_product_name($_POST['product_name']);
			}
			// MOLD NAME
			if (isset($_POST['mold_name'])) {
				$mold->pm_set_mold_name($_POST['mold_name']);
				echo '<input name="term" type="hidden" value="'.$_POST['mold_name'].'">';
			}
			// MOLD ATTRIBUTE
			if (isset($_POST['attribute'])) {
				$mold->pm_set_mold_attribute($_POST['attribute']);
			}
			// MOLD TERMS
			if (isset($_POST['terms'])) {
				$mold->pm_set_mold_terms(implode(",", $_POST['terms']));
			}
			
			// MOLD IMAGE
			if (isset($_POST['mold_image'])) {
				$mold->pm_set_mold_image($_POST['mold_image']);
			}
			
			// MOLD STOCK
			if (isset($_POST['mold_stock'])) {
				$mold->pm_set_mold_stock($_POST['mold_stock']);
			}
			// MOLD SKU
			if (isset($_POST['mold_sku'])) {
				$mold->pm_set_mold_sku($_POST['mold_sku']);
			}
			
			// MOLD IS ACTIVE
			if (isset($_POST['is_active'])) {
				$mold->pm_set_mold_is_active($_POST['is_active']);
			}
			if (isset($_POST['is_not_active'])) {
				$mold->pm_set_mold_is_active($_POST['is_not_active']);
			}
			
			// MOLD IS VIRTUAL
			if (isset($_POST['is_virtual'])) {
				$mold->pm_set_mold_is_virtual($_POST['is_virtual']);
			}
			if (isset($_POST['is_not_virtual'])) {
				$mold->pm_set_mold_is_virtual($_POST['is_not_virtual']);
			}
			
			// MOLD IS DOWNLOADABLE
			if (isset($_POST['is_downloadable'])) {
				$mold->pm_set_mold_is_downloadable($_POST['is_downloadable']);
				// MOLD FILE PATHS
				if(isset($_POST['file_paths'])&&!empty($_POST['file_paths'])) {
					$paths = explode(",",$_POST['file_paths']);
					if(empty($paths[0])) {
						unset($paths[0]);
					}
					$mold->pm_set_mold_file_paths($paths);
				}
				// MOLD DOWNLOAD LIMIT
				if(isset($_POST['download_limit'])) {
					$mold->pm_set_mold_download_limit($_POST['download_limit']);
				}
				// MOLD DOWNLOAD EXPIRY
				if(isset($_POST['download_expiry'])) {
					$mold->pm_set_mold_download_expiry($_POST['download_expiry']);
				}
			}
			if (isset($_POST['is_not_downloadable'])) {
				$mold->pm_set_mold_is_downloadable($_POST['is_not_downloadable']);
				$mold->pm_set_mold_file_paths(array());
				$mold->pm_set_mold_download_limit(null);
				$mold->pm_set_mold_download_expiry(null);
			}
			
			// MOLD IS MAIN IMAGE
			if (isset($_POST['is_main_image'])) {
				$mold->pm_set_mold_is_main_image($_POST['is_main_image']);
			}
			
			// REGULAR PRICE
			if (isset($_POST['regular_price'])) {
				$mold->pm_set_mold_regular_price($_POST['regular_price']);
			}
			// REGULAR IS BASE PRICE
			if (isset($_POST['is_base_price'])) {
				$mold->pm_set_mold_is_base_price($_POST['is_base_price']);
			}
			// REGULAR IS ADD
			if (isset($_POST['is_add'])) {
				$mold->pm_set_mold_is_add($_POST['is_add']);
			}
			// REGULAR IS SUB
			if (isset($_POST['is_sub'])) {
				$mold->pm_set_mold_is_sub($_POST['is_sub']);
			}
			// REGULAR IS PERCENT
			if (isset($_POST['is_percent'])) {
				$mold->pm_set_mold_is_percent($_POST['is_percent']);
			}
			
			// SALE PRICE
			if (isset($_POST['sale_price'])) {
				$mold->pm_set_mold_sale_price($_POST['sale_price']);
			}
			// SALE IS BASE PRICE
			if (isset($_POST['sale_price_is_base_price'])) {
				$mold->pm_set_mold_sale_price_is_base_price($_POST['sale_price_is_base_price']);
			}
			// SALE IS ADD
			if (isset($_POST['sale_price_is_add'])) {
				$mold->pm_set_mold_sale_price_is_add($_POST['sale_price_is_add']);
			}
			// SALE IS SUB
			if (isset($_POST['sale_price_is_sub'])) {
				$mold->pm_set_mold_sale_price_is_sub($_POST['sale_price_is_sub']);
			}
			// SALE IS PERCENT
			if (isset($_POST['sale_price_is_percent'])) {
				$mold->pm_set_mold_sale_price_is_percent($_POST['sale_price_is_percent']);
			}
			
			// SALE DATES FROM TO
			if (isset($_POST['sale_price_dates_from'])) {
				$dates_from = strtotime($_POST['sale_price_dates_from']);
				$mold->pm_set_mold_sale_price_dates_from($dates_from);
			}
			if (isset($_POST['sale_price_dates_to'])) {
				$dates_to = strtotime($_POST['sale_price_dates_to']);
				$mold->pm_set_mold_sale_price_dates_to($dates_to);
			}
			
			// WEIGHT
			if (isset($_POST['mold_weight'])) {
				$mold->pm_set_mold_weight($_POST['mold_weight']);
			}
			// LENGTH
			if (isset($_POST['mold_length'])) {
				$mold->pm_set_mold_length($_POST['mold_length']);
			}
			// WIDTH
			if (isset($_POST['mold_width'])) {
				$mold->pm_set_mold_width($_POST['mold_width']);
			}
			// HEIGHT
			if (isset($_POST['mold_height'])) {
				$mold->pm_set_mold_height($_POST['mold_height']);
			}
			
			// SHIPPING CLASS
			if (isset($_POST['mold_shipping_class'])) {
				$mold->pm_set_mold_shipping_class($_POST['mold_shipping_class']);
			}
	
			
			// RESET EVERYTHING
			if (isset($_POST['reset'])&&($_POST['reset']=="yes")) {
				$mold->pm_default_values();
			}
			if($_POST['action'] == 'new') {
				## TEMPORARY SAVING ##
				## We save to the temporary table ##
				$mold->pm_temporary_save();
			} else if($_POST['action'] == 'edit') {
				## We save to the database ##
				$mold_id = $mold->pm_get_mold_id();
				$mold->pm_save_to_db($mold_id);
			}
		
			## SAVING TO DB ##
			## This is when the temporary table will be suppressed ##
			if (isset($_POST['save'])&&($_POST['save']=="yes")) {
				$mold->pm_save_to_db($mold_id);
				if(isset($_POST['action'])&&($_POST['action']=="new")) {
					echo "<script> save_new(); </script>";
				}
			}
			
			// HEADER // ?>
			<h3>
			
				<?php // MOLD NAME text input //
				
				## Let's get the mold name already registered ##
				$the_mold = $mold->pm_get_mold_name();
				?>
				
				<label class="field"><b><?php _e( 'Name', 'woocommerce-molds' ); ?> :</b>
				<?php
				## If we have a user value here we can adjust the view ##
				if ($the_mold!="Mold") { ?> 
					<input type="text" id="mold_name" name="mold_name" placeholder="<?php _e( 'Type the mold name here...', 'woocommerce-molds' ); ?>" value="<?php echo $the_mold; ?>" size="20"> 
				<?php } else { ?> 
					<input type="text" id="mold_name" name="mold_name" placeholder="<?php _e( 'Type the mold name here...', 'woocommerce-molds' ); ?>" value="<?php echo $mold_name; ?>" size="20"> 
				<?php } ?>
				</label>
				
				
				
				<?php // PRODUCTS DIV // ?>
				<div id="products">
				
					<?php ## This modal windows div shows all products ## ?>
					<div class="modal_inner">
					
						<?php global $wpdb;
						
						// We connect to the DB to get the products
						$query = "SELECT ID, post_title FROM ".$wpdb->prefix."posts WHERE post_type = 'product'";
						$rows = $wpdb->get_results($query);
						
						// Check for errors
						if (!$rows) {
							echo 'Could not run query: ' . mysql_error();
						}
						
						// We show as a select option every product
						foreach ($rows as $row) {
						
							$id = $row->ID;
							$name = $row->post_title;
							
							## Displayed data is only the name
							echo '<div id="'.$id.'" class="wc_molds_term wc_molds_product">';
							echo '<button onclick="post_product(\''.$id.'\',\''.$name.'\');" type="button">'.$name.'</button>';
							echo '</div>';
							
						}
						?>
					</div>
					
				</div>
				
				<?php // SELECT PRODUCT PARENT // 
				## This useful lil' button will open the modal window fully loaded with the products list ## ?>
				<label class="field"><b><?php _e( 'Parent product', 'woocommerce-molds' ); ?> :</b>
				<button name="modal" type="button" class="button" onclick="show_it('products');">
					<?php 
					## The product name has been save in the temporary table and loaded into the object ##
					## Now we can get it simply ##
					$the_name = $mold->pm_get_product_name();
					## Display it if it contains user value ##
					if($the_name != "Product") { 
						echo $the_name; 
					## Or display default value
					} else { 
						_e( 'Select a product', 'woocommerce-molds' ); 
					} 
					?>
				</button>
				</label>

				<?php // SELECT ATTRIBUTE // ?>
				<label class="field"><b><?php _e( 'Attribute', 'woocommerce-molds' ); ?> :</b>
				<select name="attribute" onchange="this.form.submit()" id="selected_attribute">
					<option value="default"><?php _e( 'Select an attribute', 'woocommerce-molds' ); ?></option>
					<?php
					## Let's get the product id as it will be useful later ##
					$the_id = $mold->pm_get_product_id();
					
					## Let's do the same for the mold attribute, if it's already defined, we'll need it ##
					$the_attribute = $mold->pm_get_mold_attribute();
					
					## We already got the product name temporary registered, if it's user value we can get the linked attributes ##
					if ($the_name != "Product") {

						global $wpdb;
						
						// We connect to the DB to get the attributes
						$rows = $wpdb->get_results($wpdb->prepare("SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_product_attributes' AND post_id = '%d'",$the_id));
						
						// Check for errors
						if (!$rows) {
							echo 'Could not run query: ' . mysql_error();
						}
						
						// Let's foreach this data
						foreach ($rows as $row) {
							$attributes = maybe_unserialize($row->meta_value);
							foreach ($attributes as $attribute) {
								$attribute_name = $attribute['name'];
								## If an attribute has already been selected we keep it selected ##
								if(($the_attribute!="none")&&($the_attribute==$attribute_name)) {
									$attribute_name = substr($attribute_name,3);
									echo '<option name="attribute" value="'.$attribute['name'].'" selected>'.$attribute_name.'</option>';
								} else {
									$attribute_name = substr($attribute_name,3);
									echo '<option name="attribute" value="'.$attribute['name'].'">'.$attribute_name.'</option>'; ## sends ATTRIBUTE NAME like pa_attribute <<<
								}
							}
						}			
					}
					?>
				</select>
				</label>
				
				<?php // SELECT TERMS // ?>
				<label class="field"><a class="datatips" data-tip="<?php _e( 'If you do not select a term, the mold will be applied only to variations set on -any-', 'woocommerce-molds' ); ?>" href="#"><b><?php _e( 'Terms', 'woocommerce-molds' ); ?> :</b></a>
					<?php 
					## Let's get the mold terms already registered ##
					$the_terms = $mold->pm_get_mold_terms();
					
					## If we have an attribute we can get the terms ##
					if (($the_attribute != "none")&&($the_attribute != "default")) {
					
							global $wpdb;
							
							// We connect to the DB to get the terms
							$attribute_name = "order_".$the_attribute;
							$rows = $wpdb->get_results($wpdb->prepare("SELECT name FROM ".$wpdb->prefix."terms WHERE term_id IN ( SELECT woocommerce_term_id FROM ".$wpdb->prefix."woocommerce_termmeta WHERE meta_key = '%s')",$attribute_name));
							
							## ALL CHECKED FLAG ##
							$all = false;
							$script_terms = array();
							$counter = 0;
							
							// Get the damn terms
							foreach ($rows as $row) {
							
								$name = $row->name;
								
								// Registering the terms id for javascript use
								$script_terms[$counter] = $name;
								$counter++;
								
								## A little flag to know if box has already been showed ##
								$displayed = false;
								## If we already have user values ##
								if($the_terms) {
										$terms_array = explode(",", $the_terms);
										for($k=0;$k<sizeof($terms_array);$k++) {
											## And display the right option as selected ##
											if($terms_array[$k] == $name) {
												echo '<input onclick="disabled_all(\''.$name.'\');" class="css-checkbox" type="checkbox" name="terms[]" id="'.$name.'" value="'.$name.'" checked><label class="css-label" for="'.$name.'">'.$name.'</label> ';
												$displayed = true;
											## We also implement and check the all checkbox if selected ##
											} else if($terms_array[$k] == "all") {
												$all=true;
											}
										}
								## If we have no user values
								} else {
									## Display the terms checkboxes ##
									echo '<input onclick="disabled_all(\''.$name.'\');" class="css-checkbox" type="checkbox" name="terms[]" id="'.$name.'" value="'.$name.'"><label class="css-label" for="'.$name.'">'.$name.'</label> '; ## sends TERM <<<
									$displayed = true;
								}
								
								## If any term checkbox hasn't been displayed yet, do it too ##
								if(!$displayed) {
									echo '<input onclick="disabled_all(\''.$name.'\');" class="css-checkbox" type="checkbox" name="terms[]" id="'.$name.'" value="'.$name.'"><label class="css-label" for="'.$name.'">'.$name.'</label> ';
								}
			
							}
							
							$terms_id = implode(" ",$script_terms);
							
							## ALL CHECKBOX ##
							?><input class="css-checkbox" type="checkbox" name="terms[]" onclick="all_or_else('<?php echo $terms_id; ?>');" id="all" value="all" <?php if($all) { echo ' checked>'; } else { echo ' >'; }?>
							<label class="css-label" for="all"><a class="datatips" data-tip="<?php _e( 'Enable this option to apply the mold on every variation concerned by the attribute', 'woocommerce-molds' ); ?>" href="#"><?php _e( 'All', 'woocommerce-molds' ); ?></a></label>
					<?php } ?>
				</label>
				
				<br/>
				
			</h3> <?php // end of header //

			// A MOLD CONTENT // ?>
			<div class="content field">	
				<b><?php _e( 'Select the options you want to apply to every variation of', 'woocommerce-molds' ); ?> <font color="#01A9DB">[<?php if ($the_name != "Product") { echo $the_name; } else { echo 'the product'; } ?>]</font> <?php _e( 'using', 'woocommerce-molds' ); ?> <font color="#01A9DB">[<?php if ($the_attribute != "none") { echo substr($the_attribute,3); } else { echo 'a selected attribute'; } ?>]</font> : <font color="#01A9DB">[<?php if (!empty($the_terms)) { echo $the_terms; } else { echo 'chosen terms'; } ?>]</font>.</b><br>
			
				<?php // A MOLD IMAGE // 
				wp_enqueue_media(); 
				$the_image = $mold->pm_get_mold_image();
				?>
				<?php if($the_image == "none") { ?>
				<script>
					// Uploading files
					var file_frame;
					
					jQuery('.upload_image_button').live('click', function( event ){
					 
						event.preventDefault();
						 
						// If the media frame already exists, reopen it.
						if ( file_frame ) {
							file_frame.open();
							return;
						}
						 
						// Create the media frame.
						file_frame = wp.media.frames.file_frame = wp.media({
							title: jQuery( this ).data( 'uploader_title' ),
							button: {
							text: jQuery( this ).data( 'uploader_button_text' ),
							},
							multiple: false // Set to true to allow multiple files to be selected
						});
						 
						// When an image is selected, run a callback.
						file_frame.on( 'select', function() {
							// We set multiple to false so only get one image from the uploader
							attachment = file_frame.state().get('selection').first().toJSON();
							 
							// Do something with attachment.id and/or attachment.url here
							image_input = document.getElementById('mold_image');
							image_input.value = attachment.url;
							document.forms["moldForm"].submit();
						});
						 
						// Finally, open the modal
						file_frame.open();
					});
				</script>
				<?php } else { ?>
				<script>
						jQuery('.upload_image_button').live('click', function( event ){
							// Do something with attachment.id and/or attachment.url here
							image_input = document.getElementById('mold_image');
							image_input.value = 'none';
							document.forms["moldForm"].submit();
						});
				</script>
				<?php } ?>
				<div class="uploader" style="float:left;padding:5px;margin-right:10px;">
					<button class="upload_image_button" type="button">
					<span class="overlay">
						<?php if($the_image != "none") { ?>
							<img src="<?php echo bloginfo('wpurl'); ?>/wp-content/plugins/woocommerce-molds/assets/images/minus.png">
						<?php } else { ?>
							<img src="<?php echo bloginfo('wpurl'); ?>/wp-content/plugins/woocommerce-molds/assets/images/plus.png">
						<?php }?>
					</span>
					<img src="<?php if($the_image != "none") { echo $the_image; } else { echo esc_attr( woocommerce_placeholder_img_src()); } ?>" width="100" height="100"></img></button>
					<input type="hidden" id="mold_image" name="mold_image" value="<?php if($the_image != "none") { echo $the_image; } else { echo "none"; } ?>"/>
					<br>
				</div>
				
				<?php // A MOLD SKU OPTION //
					if ( get_option( 'woocommerce_enable_sku', true ) !== 'no' ) :
						$the_sku = $mold->pm_get_mold_sku();?>
						<div class="field">
							<label><a class="datatips" href="#" data-tip="<?php _e( 'Enter a SKU for this variation or leave blank to use the parent product SKU.', 'woocommerce' ); ?>">[?]</a>
							<b><?php _e( 'SKU', 'woocommerce' ); ?> :</b></label>
							<input type="text" size="5" name="mold_sku" value="<?php if(!empty($the_sku)) { echo esc_attr($the_sku); } ?>"/>
						</div>
				<?php endif; ?>
				
				<?php // A MOLD CHECK OPTIONS YES // ?>
				<div class="field">
					<?php ## IS ACTIVE ##
					$is_active = $mold->pm_get_mold_is_active();
					?><input type="hidden" name="is_active" value="no" /><?php
					if($is_active=="yes") { ?>
						<input onclick="uncheck_this('is_active','is_not_active');" class="css-checkbox" type="checkbox" id="is_active" name="is_active" value="yes" checked><label class="css-label" for="is_active"> <a class="datatips" data-tip="<?php _e( 'Enable this option to activate the variations in the shop', 'woocommerce-molds' ); ?>" href="#"><?php _e( 'Active', 'woocommerce' ); ?></a></label>
					<?php } else { ?>
						<input onclick="uncheck_this('is_active','is_not_active');" class="css-checkbox" type="checkbox" id="is_active" name="is_active" value="yes"><label class="css-label" for="is_active"> <a class="datatips" data-tip="<?php _e( 'Enable this option to activate the variations in the shop', 'woocommerce-molds' ); ?>" href="#"><?php _e( 'Active', 'woocommerce' ); ?></a></label><?php
					}?>
					<script>
					window.onload=function(){
						el=document.getElementById('is_downloadable');
						if(el.checked) {
							show_it('download_options',true,true,'is_downloadable');
						}
					};</script>
					<?php ## IS DOWNLOADABLE ##
					$is_downloadable = $mold->pm_get_mold_is_downloadable();
					?><input type="hidden" name="is_downloadable" value="no" /><?php
					if($is_downloadable=="yes") { ?>
						<input onclick="show_it('download_options',true,true,'is_downloadable');uncheck_this('is_downloadable','is_not_downloadable');" class="css-checkbox" type="checkbox" id="is_downloadable" name="is_downloadable" value="yes" checked><label class="css-label" for="is_downloadable"> <a class="datatips" data-tip="<?php _e( 'Enable this option if access is given to a downloadable file upon purchase of a product', 'woocommerce' ); ?>" href="#"><?php _e( 'Downloadable', 'woocommerce' ); ?></a> </label>
					<?php } else { ?>
						<input onclick="show_it('download_options',true,true,'is_downloadable');uncheck_this('is_downloadable','is_not_downloadable');" class="css-checkbox" type="checkbox" id="is_downloadable" name="is_downloadable" value="yes"><label class="css-label" for="is_downloadable"> <a class="datatips" data-tip="<?php _e( 'Enable this option if access is given to a downloadable file upon purchase of a product', 'woocommerce' ); ?>" href="#"><?php _e( 'Downloadable', 'woocommerce' ); ?></a></label> <?php
					}
					## IS VIRTUAL ##
					$is_virtual = $mold->pm_get_mold_is_virtual();
					?><input type="hidden" name="is_virtual" value="no" /><?php
					if($is_virtual=="yes") { ?>
						<input onclick="uncheck_this('is_virtual','is_not_virtual');" class="css-checkbox" type="checkbox" id="is_virtual" name="is_virtual" value="yes" checked><label class="css-label" for="is_virtual"> <a class="datatips" data-tip="<?php _e( 'Enable this option if a product is not shipped or there is no shipping cost', 'woocommerce' ); ?>" href="#"><?php _e( 'Virtual', 'woocommerce' ); ?></a></label><?php
					} else { ?>
						<input onclick="uncheck_this('is_virtual','is_not_virtual');" class="css-checkbox" type="checkbox" id="is_virtual" name="is_virtual" value="yes"><label class="css-label" for="is_virtual"> <a class="datatips" data-tip="<?php _e( 'Enable this option if a product is not shipped or there is no shipping cost', 'woocommerce' ); ?>" href="#"><?php _e( 'Virtual', 'woocommerce' ); ?></a></label><?php
					}
					## IS MAIN IMAGE ##
					$is_main_image = $mold->pm_get_mold_is_main_image();
					if($is_main_image=="yes") { ?>
						<input class="css-checkbox" type="checkbox" id="is_main_image" name="is_main_image" value="yes" checked><label class="css-label" for="is_main_image"> <a class="datatips" data-tip="<?php _e( 'Enable this option to replace all the variations images, or else the image will only be passed to variations without one', 'woocommerce-molds' ); ?>" href="#"><?php _e( 'Main image', 'woocommerce-molds' ); ?></a></label><?php
					} else { ?>
						<input class="css-checkbox" type="checkbox" id="is_main_image" name="is_main_image" value="yes"><label class="css-label" for="is_main_image"> <a class="datatips" data-tip="<?php _e( 'Enable this option to replace all the variations images, or else the image will only be passed to variations without one', 'woocommerce-molds' ); ?>" href="#"><?php _e( 'Main image', 'woocommerce-molds' ); ?></a></label><?php
					}
					?>
				</div><br>
				<?php // A MOLD CHECK OPTIONS NO // ?>
				<div class="field">
					<?php ## IS NOT ACTIVE ##
					if($is_active=="not") { ?>
						<input onclick="uncheck_this('is_not_active','is_active');" class="css-checkbox" type="checkbox" id="is_not_active" name="is_not_active" value="not" checked><label class="css-label" for="is_not_active"> <a class="datatips" data-tip="<?php _e( 'Unchecks the active option', 'woocommerce-molds' ); ?>" href="#"><?php _e( 'Inactive', 'woocommerce-molds' ); ?></a></label>
					<?php } else { ?>
						<input onclick="uncheck_this('is_not_active','is_active');" class="css-checkbox" type="checkbox" id="is_not_active" name="is_not_active" value="not"><label class="css-label" for="is_not_active"> <a class="datatips" data-tip="<?php _e( 'Unchecks the active option', 'woocommerce-molds' ); ?>" href="#"><?php _e( 'Inactive', 'woocommerce-molds' ); ?></a></label><?php
					}
					## IS NOT DOWNLOADABLE ##
					if($is_downloadable=="not") { ?>
						<input onclick="uncheck_this('is_not_downloadable','is_downloadable');" class="css-checkbox" type="checkbox" id="is_not_downloadable" name="is_not_downloadable" value="not" checked><label class="css-label" for="is_not_downloadable"> <a class="datatips" data-tip="<?php _e( 'Unchecks the downloadable option', 'woocommerce-molds' ); ?>" href="#"><?php _e( 'Not downloadable', 'woocommerce-molds' ); ?></a></label>
					<?php } else { ?>
						<input onclick="uncheck_this('is_not_downloadable','is_downloadable');" class="css-checkbox" type="checkbox" id="is_not_downloadable" name="is_not_downloadable" value="not"><label class="css-label" for="is_not_downloadable"> <a class="datatips" data-tip="<?php _e( 'Unchecks the downloadable option', 'woocommerce-molds' ); ?>" href="#"><?php _e( 'Not downloadable', 'woocommerce-molds' ); ?></a></label><?php
					}
					## IS NOT VIRTUAL ##
					$is_virtual = $mold->pm_get_mold_is_virtual();
					if($is_virtual=="not") { ?>
						<input onclick="uncheck_this('is_not_virtual','is_virtual');" class="css-checkbox" type="checkbox" id="is_not_virtual" name="is_not_virtual" value="not" checked><label class="css-label" for="is_not_virtual"> <a class="datatips" data-tip="<?php _e( 'Unchecks the virtual option', 'woocommerce-molds' ); ?>" href="#"><?php _e( 'Not virtual', 'woocommerce-molds' ); ?></a></label><?php
					} else { ?>
						<input onclick="uncheck_this('is_not_virtual','is_virtual');" class="css-checkbox" type="checkbox" id="is_not_virtual" name="is_not_virtual" value="not"><label class="css-label" for="is_not_virtual"> <a class="datatips" data-tip="<?php _e( 'Unchecks the virtual option', 'woocommerce-molds' ); ?>" href="#"><?php _e( 'Not virtual', 'woocommerce-molds' ); ?></a></label><?php
					}
					?>
				</div>
				
				<?php // A MOLD STOCK OPTION // 
				$the_stock = $mold->pm_get_mold_stock();?>
				<div class="field">
					<label><a class="datatips" data-tip="<?php _e( 'Enter a quantity to enable stock management at variation level, or leave blank to use the parent product\'s options.', 'woocommerce' ); ?>" href="#">[?]</a> <b><?php _e( 'Stock Qty:', 'woocommerce' ); ?></b></label>
					<input type="number" size="5" name="mold_stock" value="<?php if(!empty($the_stock)) { echo $the_stock; } ?>" step="any" min="0" />
				</div>
				
				<br>
				<?php // A MOLD PRICE OPTIONS // ?>
				<div class="field">
					<label><b><?php echo __( 'Regular Price:', 'woocommerce' ); ?></b></label>
					<input type="number" size="5" name="regular_price" value="<?php $regular_price = $mold->pm_get_mold_regular_price(); if($regular_price) { echo $regular_price; } ?>" step="any" min="0"/><br>
					<?php // A MOLD PRICE CALCULATION OPTIONS //
					## IS BASE PRICE ##
					$is_base_price = $mold->pm_get_mold_is_base_price();
					?><input type="hidden" name="is_base_price" value="no" /><?php
					if($is_base_price=="yes") { ?>
						<input class="css-checkbox" type="checkbox" onclick="base_price();" id="is_base_price" name="is_base_price" value="yes" checked><label class="css-label" for="is_base_price"><a class="datatips" data-tip="<?php _e( 'Enabling this option will replace the variations regular price by the one you entered', 'woocommerce-molds' ); ?>" href="#"><?php echo _e("Use as regular price", 'woocommerce-molds') . ' ('.get_woocommerce_currency_symbol().')'; ?></a></label><br><?php
					} else { ?>
						<input class="css-checkbox" type="checkbox" onclick="base_price();" id="is_base_price" name="is_base_price" value="yes"><label class="css-label" for="is_base_price"><a class="datatips" data-tip="<?php _e( 'Enabling this option will replace the variations regular price by the one you entered', 'woocommerce-molds' ); ?>" href="#"><?php echo _e("Use as regular price", 'woocommerce-molds') . ' ('.get_woocommerce_currency_symbol().')'; ?></a></label><br><?php
					}
					## IS ADD ##
					$is_add = $mold->pm_get_mold_is_add();
					?><input type="hidden" name="is_add" value="no" /><?php
					if($is_add=="yes") { ?>
						<input class="css-checkbox" type="checkbox" onclick="add_or_sub(true);" id="is_add" name="is_add" value="yes" checked><label class="css-label" for="is_add"><a class="datatips" data-tip="<?php _e( 'The price you entered will be added to the variations current price', 'woocommerce-molds' ); ?>" href="#"><?php _e("Add to regular price", 'woocommerce-molds'); ?></a></label><br><?php
					} else { ?>
						<input class="css-checkbox" type="checkbox" onclick="add_or_sub(true);" id="is_add" name="is_add" value="yes"><label class="css-label" for="is_add"><a class="datatips" data-tip="<?php _e( 'The price you entered will be added to the variations current price', 'woocommerce-molds' ); ?>" href="#"><?php _e("Add to regular price", 'woocommerce-molds'); ?></a></label><br><?php
					}
					## IS SUB ##
					$is_sub = $mold->pm_get_mold_is_sub();
					?><input type="hidden" name="is_sub" value="no" /><?php
					if($is_sub=="yes") { ?>
						<input class="css-checkbox" type="checkbox" onclick="add_or_sub(false);" id="is_sub" name="is_sub" value="yes" checked><label class="css-label" for="is_sub"><a class="datatips" data-tip="<?php _e( 'The price you entered will be substracted to the variations current price', 'woocommerce-molds' ); ?>" href="#"><?php _e("Substract", 'woocommerce-molds'); ?></a></label><br><?php
					} else { ?>
						<input class="css-checkbox" type="checkbox" onclick="add_or_sub(false);" id="is_sub" name="is_sub" value="yes"><label class="css-label" for="is_sub"><a class="datatips" data-tip="<?php _e( 'The price you entered will be substracted to the variations current price', 'woocommerce-molds' ); ?>" href="#"><?php _e("Substract", 'woocommerce-molds'); ?></a></label><br><?php
					}
					## IS PERCENT ##
					$is_percent = $mold->pm_get_mold_is_percent();
					?><input type="hidden" name="is_percent" value="no" /><?php
					if($is_percent=="yes") { ?>
						<input class="css-checkbox" type="checkbox" id="is_percent" name="is_percent" value="yes" checked><label class="css-label" for="is_percent"><a class="datatips" data-tip="<?php _e( 'The price you entered will be added or substracted as a percentage of the variations current price', 'woocommerce-molds' ); ?>" href="#"><?php _e("Calculate in %", 'woocommerce-molds'); ?></a></label><?php
					} else { ?>
						<input class="css-checkbox" type="checkbox" id="is_percent" name="is_percent" value="yes"><label class="css-label" for="is_percent"><a class="datatips" data-tip="<?php _e( 'The price you entered will be added or substracted as a percentage of the variations current price', 'woocommerce-molds' ); ?>" href="#"><?php _e("Calculate in %", 'woocommerce-molds'); ?></a></label><?php
					}
					?>
				</div>
				
				<div class="field">
					<label for="sale_price"><b><?php echo __( 'Sale Price:', 'woocommerce' ); ?></b></label>
					<input id="sale_price" type="number" size="5" name="sale_price" value="<?php $sale_price = $mold->pm_get_mold_sale_price(); if($sale_price) { echo $sale_price; } ?>" step="any" min="0" />
					<button type="button" class="bluebutton" onclick="show_it('sale_dates',true);"><?php _e( 'Schedule', 'woocommerce-molds' ) ?></button>
					<a class="datatips" data-tip="<?php _e( 'Define a sale price duration', 'woocommerce-molds' ); ?>" href="#">[?]</a>
					
					<?php // A MOLD SALE DATES // ?>
					<div id="sale_dates">
						<label for="sale_price_dates_from"><?php _e( 'Sale start date:', 'woocommerce' ) ?></label>
						<input type="text" id="sale_price_dates_from" class="sale_price_dates_from" name="sale_price_dates_from" value="<?php $sale_price_dates_from = strftime("%Y-%m-%d",$mold->pm_get_mold_sale_price_dates_from()); if($sale_price_dates_from) { echo $sale_price_dates_from; } ?>" placeholder="<?php echo _x( 'From&hellip;','woocommerce-molds' ) ?> YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
						<label for="sale_price_dates_to"><?php _e( 'Sale end date:', 'woocommerce' ) ?></label>
						<input type="text" id="sale_price_dates_to" name="sale_price_dates_to" value="<?php $sale_price_dates_to = strftime("%Y-%m-%d",$mold->pm_get_mold_sale_price_dates_to()); if($sale_price_dates_to) { echo $sale_price_dates_to; } ?>" placeholder="<?php echo _x('To&hellip;','woocommerce-molds') ?> YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
					</div><br>
					
					<?php // A MOLD PRICE CALCULATION OPTIONS //
					## SALE IS BASE PRICE ##
					$sale_price_is_base_price = $mold->pm_get_mold_sale_price_is_base_price();
					?><input type="hidden" name="sale_price_is_base_price" value="no" /><?php
					if($sale_price_is_base_price=="yes") { ?>
						<input class="css-checkbox" type="checkbox" onclick="base_price(true);"id="sale_price_is_base_price" name="sale_price_is_base_price" value="yes" checked><label class="css-label" for="sale_price_is_base_price"><?php echo _e("Use as sale price", 'woocommerce-molds') . ' ('.get_woocommerce_currency_symbol().')'; ?></label><br><?php
					} else { ?>
						<input class="css-checkbox" type="checkbox" onclick="base_price(true);"id="sale_price_is_base_price" name="sale_price_is_base_price" value="yes"><label class="css-label" for="sale_price_is_base_price"><?php echo _e("Use as sale price", 'woocommerce-molds') . ' ('.get_woocommerce_currency_symbol().')'; ?></label><br><?php
					} 
					## SALE IS ADD ##
					$sale_price_is_add = $mold->pm_get_mold_sale_price_is_add();
					?><input type="hidden" name="sale_price_is_add" value="no" /><?php
					if($sale_price_is_add=="yes") { ?>
						<input class="css-checkbox" type="checkbox" onclick="add_or_sub(true,true);" id="sale_price_is_add" name="sale_price_is_add" value="yes" checked><label class="css-label" for="sale_price_is_add"><?php _e("Add to sale price", 'woocommerce-molds'); ?></label><br><?php
					} else { ?>
						<input class="css-checkbox" type="checkbox" onclick="add_or_sub(true,true);" id="sale_price_is_add" name="sale_price_is_add" value="yes"><label class="css-label" for="sale_price_is_add"><?php _e("Add to sale price", 'woocommerce-molds'); ?></label><br><?php
					} 
					## SALE IS SUB ##
					$sale_price_is_sub = $mold->pm_get_mold_sale_price_is_sub();
					?><input type="hidden" name="sale_price_is_sub" value="no" /><?php
					if($sale_price_is_sub=="yes") { ?>
						<input class="css-checkbox" type="checkbox" onclick="add_or_sub(false,true);" id="sale_price_is_sub" name="sale_price_is_sub" value="yes" checked><label class="css-label" for="sale_price_is_sub"><?php _e("Substract", 'woocommerce-molds'); ?></label><br><?php
					} else { ?>
						<input class="css-checkbox" type="checkbox" onclick="add_or_sub(false,true);" id="sale_price_is_sub" name="sale_price_is_sub" value="yes"><label class="css-label" for="sale_price_is_sub"><?php _e("Substract", 'woocommerce-molds'); ?></label><br><?php
					} 
					## SALE IS PERCENT ##
					$sale_price_is_percent = $mold->pm_get_mold_sale_price_is_percent();
					?><input type="hidden" name="sale_price_is_percent" value="no" /><?php
					if($sale_price_is_percent=="yes") { ?>
						<input class="css-checkbox" type="checkbox" id="sale_price_is_percent" name="sale_price_is_percent" value="yes" checked><label class="css-label" for="sale_price_is_percent"><?php _e("Calculate in %", 'woocommerce-molds'); ?></label><?php
					} else { ?>
						<input class="css-checkbox" type="checkbox" id="sale_price_is_percent" name="sale_price_is_percent" value="yes"><label class="css-label" for="sale_price_is_percent"><?php _e("Calculate in %", 'woocommerce-molds'); ?></label><?php
					} 
					?>
				</div><br/>
				
				<?php // A MOLD WEIGHT AND DIMENSIONS //
				$length = $mold->pm_get_mold_length(); 
				$width = $mold->pm_get_mold_width(); 
				$height = $mold->pm_get_mold_height(); 
				$weight = $mold->pm_get_mold_weight(); ?>
				<?php if ( get_option( 'woocommerce_enable_weight', true ) !== 'no' || get_option( 'woocommerce_enable_dimensions', true ) !== 'no' ) : ?>
						<?php if ( get_option( 'woocommerce_enable_weight', true ) !== 'no' ) : ?>
							<div class="field">
							<label><a class="datatips" data-tip="<?php _e( 'Enter a weight for this variation or leave blank to use the parent product weight.', 'woocommerce' ); ?>" href="#">[?]</a> <b><?php _e( 'Weight', 'woocommerce' ) . ' (' . esc_html( get_option( 'woocommerce_weight_unit' ) ) . '):'; ?>:</b></label>
							<input type="number" size="5" name="mold_weight" value="<?php if ( isset( $weight ) ) echo esc_attr( $weight ); ?>" placeholder="" step="any" min="0" />
							</div>
						<?php endif; ?>
						<?php if ( get_option( 'woocommerce_enable_dimensions', true ) !== 'no' ) : ?>
							<div class="field">
							<label for="product_length"><b><?php echo __( 'Dimensions (L&times;W&times;H)', 'woocommerce' ); ?></b></label>
							<input id="product_length" class="input-text" size="6" type="number" step="any" min="0" name="mold_length" value="<?php if ( isset( $length ) ) echo esc_attr( $length ); ?>" placeholder="" />
							<input class="input-text" size="6" type="number" step="any" min="0" name="mold_width" value="<?php if ( isset( $width ) ) echo esc_attr( $width ); ?>" placeholder="" />
							<input class="input-text last" size="6" type="number" step="any" min="0" name="mold_height" value="<?php if ( isset( $height ) ) echo esc_attr( $height ); ?>" placeholder="" />
							</div>
						<?php endif; ?>
				<?php endif; ?>
				
				<?php // A MOLD SHIPPING CLASS // 
				$shipping_class = $mold->pm_get_mold_shipping_class();
				?>
				<div class="field">
				<label><?php _e( 'Shipping class:', 'woocommerce' ); ?></label> 
				<?php
					$the_shipping_class = $mold->pm_get_mold_shipping_class();
					$args = array(
						'taxonomy' 			=> 'product_shipping_class',
						'hide_empty'		=> 0,
						'show_option_none' 	=> __( 'Same as parent', 'woocommerce' ),
						'name' 				=> 'mold_shipping_class',
						'id'				=> 'mold_shipping_class',
						'selected'			=> isset( $the_shipping_class ) ? esc_attr( $the_shipping_class ) : '',
						'echo'				=> 0
					);

					echo wp_dropdown_categories( $args );
				?></div><br>
				
				<?php // DOWNLOADABLE OPTIONS ?>
				<div id="download_options" <?php if($is_downloadable=="yes") { echo 'style="visibility:visible;display:inline-block;"'; } else { echo 'style="visibility: hidden;display:none;"'; } ?>>
							<script>
								// Uploading files
								var file_frame;
								 
								jQuery('.upload_file_button').live('click', function( event ){
								 
									event.preventDefault();
									 
									// If the media frame already exists, reopen it.
									if ( file_frame ) {
										file_frame.open();
										return;
									}
									 
									// Create the media frame.
									file_frame = wp.media.frames.file_frame = wp.media({
										title: jQuery( this ).data( 'uploader_title' ),
										button: {
										text: jQuery( this ).data( 'uploader_button_text' ),
										},
										multiple: true // Set to true to allow multiple files to be selected
									});
									 
									// When an image is selected, run a callback.
									file_frame.on( 'select', function() {
									 
										var selection = file_frame.state().get('selection');
										 
										selection.map( function( attachment ) {
										 
											attachment = attachment.toJSON();
											 
											// Do something with attachment.id and/or attachment.url here
											paths_textarea = document.getElementById('file_paths');
											paths_textarea.value += ","+attachment.url;
											document.forms["moldForm"].submit();
										});
									});
									 
									// Finally, open the modal
									//file_frame.open();
								});
								
							</script>
							<?php 
							$_filepaths = $mold->pm_get_mold_file_paths();
							$the_file_paths = '';
							if(!empty($_filepaths)) {
								$the_file_paths = implode(",",$_filepaths);
							}
							$the_download_limit = $mold->pm_get_mold_download_limit();
							$the_download_expiry = $mold->pm_get_mold_download_expiry();?>
							<div class="field">
								<label><a class="datatips" data-tip="<?php _e( 'Enter one or more File Paths, one per line, to make this variation a downloadable product, or leave blank.', 'woocommerce' ); ?>" href="#">[?]</a> <?php _e( 'File paths:', 'woocommerce' ); ?> </label><br>
								<textarea id="file_paths" class="short file_paths" wrap="off" name="file_paths" placeholder="<?php _e( 'File paths/URLs, one per line', 'woocommerce' ); ?>" rows="2" cols="50"><?php if(!empty($the_file_paths)) { echo $the_file_paths; } ?></textarea><br>
								<input class="upload_file_button button" type="button" data-update="<?php _e( 'Insert file URL', 'woocommerce' ); ?>" data-choose="<?php _e( 'Choose a file', 'woocommerce' ); ?>" title="<?php _e( 'Upload', 'woocommerce' ); ?>" value="<?php _e( 'Choose a file', 'woocommerce' ); ?>"></input>
							</div>
							<div style="display:inline-block;vertical-align:top;">
								<div class="field" style="width:180px;">
									<label><a class="datatips" data-tip="<?php _e( 'Leave blank for unlimited re-downloads.', 'woocommerce' ); ?>" href="#">[?]</a> <?php _e( 'Download Limit:', 'woocommerce' ); ?> </label><br>
									<input type="number" min="0" step="1" placeholder="<?php _e( 'Unlimited', 'woocommerce' ); ?>" value="<?php if(!empty($the_download_limit)) { echo $the_download_limit; } ?>" name="download_limit" size="20"></input>
								</div><br>
								<div class="field" style="width:180px;">
									<label><a class="datatips" data-tip="<?php _e( 'Enter the number of days before a download link expires, or leave blank.', 'woocommerce' ); ?>" href="#">[?]</a> <?php _e( 'Download Expiry:', 'woocommerce' ); ?></label><br>
									<input type="number" size="20" name="download_expiry" value="<?php if(!empty($the_download_expiry)) { echo $the_download_expiry; } ?>" placeholder="<?php _e( 'Unlimited', 'woocommerce' ); ?>" step="1" min="0" /></input>
								</div>
							</div>
				</div><br>

			</div>
	<?php } else if(!isset($_POST['action'])||(isset($_POST['action']) && (($_POST['action'] == 'home')||($_POST['action'] == 'unapply')||($_POST['action'] == 'apply')||($_POST['action'] == 'remove') )) ) { 
			
			$mold->pm_delete_temporary_save();?>	

			<h2><img style='margin-right:1px;vertical-align:bottom;' src='<?php echo bloginfo('wpurl'); ?>/wp-content/plugins/woocommerce-molds/assets/images/molds.png'>
			<?php echo "" . __( 'Product Molds', 'woocommerce-molds' ) . "</h2>"; ?>
			<?php echo "<h4>" . __( 'Apply a mold to instantly feed product variations.', 'woocommerce-molds' ); ?> <a class="datatips" data-tip="<?php _e( 'The order in which molds are applied can matter, because a mold can override the effects of previous ones (percentages can surprise you if you\'re not careful)', 'woocommerce-molds' ); ?>" href="#">[?]</a></h4>
			<form name="form_new" action="edit.php?post_type=product&page=woocommerce_molds" method="post">
			<p class="submit"><button type="submit" name="action" id="submit" class="button" value="new"><?php _e( 'New Mold', 'woocommerce-molds' ); ?></button></p>
			</form>
			
			<?php // MENU // 
			$url = admin_url('edit-mold.php'); ?>

			<?php
				if(isset($_POST['action']) && ($_POST['action'] == 'remove')) {
					if(isset($_POST['mold_id'])) {
						// Select reverse for checkboxes
						if(isset($_POST['unapplying'])) {
							$mold->pm_load_from_db($_POST['mold_id']);
							$mold->pm_unapply_mold();
						}
						remove_mold($_POST['mold_id']);
					}
				}
				if(isset($_POST['action'])&&($_POST['action'] == 'apply')) {
					## Loading mold data
					$mold->pm_load_from_db($_POST['mold_id']);
					$mold->pm_apply_mold();
				} else if(isset($_POST['action'])&&($_POST['action'] == 'unapply')) {
					## Loading mold data
					$mold->pm_load_from_db($_POST['mold_id']);
					// Select reverse for checkboxes
					if(isset($_POST['reverse'])) {
						$mold->pm_unapply_mold($_POST['reverse']);
					} else {
						$mold->pm_unapply_mold();
					}
				}
			
				wc_molds_admin_list();
		
		}
	?>
	
</div>