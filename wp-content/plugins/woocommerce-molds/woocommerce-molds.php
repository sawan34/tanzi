<?php
/*
Plugin Name: Woocommerce Molds
Plugin URI: http://wordpress.org/#
Description: This plugin ease your work when it comes to using variable products with Woocommerce. Instead of manually configuring values and options for each and every variations and attributes combination you need, you just have to create in the product panel the variations for each attribute, and then create a mold to configure everything. With molds you can even add or substract an amount to the price (you can do it in % if needed), to precisely price the variations.
Version: 0.3
Author: Melina Donati
Author URI: http://donati.melina.perso.sfr.fr/
* Text Domain: easyclasses
* License: GPL2
*/

/*  Copyright 2013  Melina Donati  (email : serenafelis@yahoo.fr)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

## Checks if the WooCommerce plugins is installed and active.
if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))){

	## INIT ##
	add_action('init', 'wc_molds_init');
	function wc_molds_init() {
		load_plugin_textdomain('woocommerce-molds', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	#################
	## MOLDS ADMIN ##
	##/////////////##
	function wc_molds_admin() {
		include('wc-molds-admin.php'); 
	}
	##/////////////##
	#################

	######################
	## CUSTOM MENU ITEM ##
	##//////////////////##
	function wc_molds_admin_actions() {
		// Adds submenu page to Woocommerce
		add_submenu_page( 'edit.php?post_type=product', __( 'Molds', 'woocommerce-molds' ), __( 'Molds', 'woocommerce-molds' ), 'manage_options', 'woocommerce_molds', 'wc_molds_admin');
	}
	add_action('admin_menu', 'wc_molds_admin_actions');
	##//////////////////##
	######################
	
	########################
	## DISPLAY MOLDS LIST ##
	##////////////////////##
	function wc_molds_admin_list() {
		?>
		<div class="col-wrap">
		<table class="widefat fixed" style="width:100%">
			<thead>
				<tr>
					<th width="30px" scope="col"> </th>
				    <th scope="col"><?php _e( 'Name', 'woocommerce-molds' ) ?></th>
				    <th scope="col"><?php _e( 'Product', 'woocommerce-molds' ) ?></th>
				    <th scope="col"><?php _e( 'Attribute', 'woocommerce-molds' ) ?></th>
				    <th scope="col"><?php _e( 'Terms', 'woocommerce-molds' ) ?></th>
				    <th width="60px" scope="col"><?php _e( 'Edit', 'woocommerce-molds' ) ?></th>
					<th width="65px" scope="col"><?php _e( 'Apply', 'woocommerce-molds' ) ?></th>
					<th width="70px" scope="col"><?php _e( 'Remove', 'woocommerce-molds' ) ?></th>	
				</tr>
			</thead>
			<tbody>
				<?php
					// We interrogate the database to get all existing molds // 
					global $wpdb;
					$molds = $wpdb->get_results("SELECT meta_id, post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = '_mold'");
					// If the answer is positive we display each result in the list // 
				    if($molds) {
				        foreach ($molds as $mold) :
						// We unserialize the meta data in order to show it
						$mold_meta = unserialize($mold->meta_value);
				        ?>
						<form name="form<?php echo $mold->meta_id ?>" action="edit.php?post_type=product&page=woocommerce_molds" method="post">
									<div id="applying<?php echo $mold->meta_id ?>" class="modal_window ">
										<div class="modal_inner"><center>
											<?php _e( 'Anything you\'ve defined in the mold will be applied to its matching variations.', 'woocommerce-molds' ); ?><br><br>
											<button class="button" name="action" value="apply" type="submit"><?php _e('Apply', 'woocommerce-molds' ) ?></button><button class="button" name="action" value="home" type="submit"><?php _e('Cancel', 'woocommerce-molds' ) ?></button>
										</center></div>
									</div>
									<div id="reversing<?php echo $mold->meta_id ?>" class="modal_window ">
										<div class="modal_inner"><center>
											<?php _e( 'Anything you\'ve defined in the mold will be unapplied to its matching variations.', 'woocommerce-molds' ); ?><br><br>
											<b style="font-size:16px;"><?php _e('Reverse checkboxes', 'woocommerce-molds' ) ?> ?</b>
											<input class="css-checkbox" type="checkbox" id="reverse" name="reverse" value="true"><label class="css-label" for="reverse"> <?php _e( 'Yes', 'woocommerce-molds' ); ?></label><br>
											<?php _e( 'Checked boxes will be unchecked and vice versa', 'woocommerce-molds' ); ?><br><br>
											<button class="button" name="action" value="unapply" type="submit"><?php _e('Unapply', 'woocommerce-molds' ) ?></button><button class="button" name="action" value="home" type="submit"><?php _e('Cancel', 'woocommerce-molds' ) ?></button>
										</center></div>
									</div>
									<div id="removing<?php echo $mold->meta_id ?>" class="modal_window ">
										<div class="modal_inner"><center>
										<?php if($mold_meta['applied']=='yes') { ?>
											<b style="font-size:16px;"><?php _e('Unapply mold before removing it', 'woocommerce-molds' ) ?> ?</b>
											<input class="css-checkbox" type="checkbox" id="unapplying" name="unapplying" value="true"><label class="css-label" for="unapplying"> <?php _e( 'Yes', 'woocommerce-molds' ); ?></label><br>
											<?php _e( 'Don\'t check the box if you want to keep your variations\'s data intact.', 'woocommerce-molds' ); ?><br>
											<br>
										<?php } else { ?>
											<b style="font-size:16px;"><?php _e('Are you sure', 'woocommerce-molds' ) ?> ?</b><br><br>
										<?php } ?>
											<button class="button" name="action" value="remove" type="submit"><?php _e('Remove', 'woocommerce-molds' ) ?></button><button class="button" name="action" value="home" type="submit"><?php _e('Cancel', 'woocommerce-molds' ) ?></button>
										</center></div>
									</div>
						<tr>
							<td>
								<img style="border:1px solid #D1E5EE;" src="<?php if($mold_meta['image']!="none") { echo $mold_meta['image']; } else { echo esc_attr( woocommerce_placeholder_img_src()); } ?>" width="30" />
							</td>
							<td>
								<a><?php echo $mold_meta['name'] ?></a>
								<input type="hidden" name="mold_id" value="<?php echo $mold->meta_id ?>">
							</td>
							<td><a href="post.php?post=<?php echo $mold->post_id ?>&action=edit"><?php echo "<font color=\"black\">#</font> [".$mold_meta['product_name']."]"; ?></a></td>
							<td><a href="edit-tags.php?taxonomy=<?php echo $mold_meta['attribute'] ?>&post_type=product"><?php echo substr($mold_meta['attribute'],3); ?></a></td>
							<td>
								<?php 
								$terms = explode(",",$mold_meta['terms']);
								foreach ($terms as $term) :
								?><div class="wc_molds_term"><?php echo $term; ?></div>
								<?php endforeach; ?>
							</td>
							<td><button name="action" value="edit" class="wc_molds_img_button" title="<?php _e( 'Edit mold', 'woocommerce-molds' ) ?>" type="submit"><img src="<?php echo bloginfo('wpurl'); ?>/wp-content/plugins/woocommerce-molds/assets/images/edit.png" alt="<?php _e( 'EDIT', 'woocommerce-molds' ) ?>"></button>
							</td>
							<td><?php if($mold_meta['applied']=="yes") {?>
									<button class="wc_molds_img_button" title="<?php _e( 'Click to unapply mold', 'woocommerce-molds' ) ?>" onclick="show_it('reversing<?php echo $mold->meta_id ?>');" type="button"><img src="<?php echo bloginfo('wpurl'); ?>/wp-content/plugins/woocommerce-molds/assets/images/applied.png" alt="<?php _e( 'UNAPPLY', 'woocommerce-molds' ) ?>"></button>
								<?php } else {  ?>
									<button class="wc_molds_img_button" title="<?php _e( 'Click to apply mold', 'woocommerce-molds' ) ?>" onclick="show_it('applying<?php echo $mold->meta_id ?>');" type="button"><img src="<?php echo bloginfo('wpurl'); ?>/wp-content/plugins/woocommerce-molds/assets/images/notapplied.png" alt="<?php _e( 'APPLY', 'woocommerce-molds' ) ?>"></button>
								<?php } ?>
							</td>
							<td><button class="wc_molds_img_button" title="<?php _e( 'Remove mold', 'woocommerce-molds' ) ?>" onclick="show_it('removing<?php echo $mold->meta_id ?>');" type="button"><img src="<?php echo bloginfo('wpurl'); ?>/wp-content/plugins/woocommerce-molds/assets/images/remove.png" alt="<?php _e( 'REMOVE', 'woocommerce-molds' ) ?>"></button></td>
						</tr>
						</form><?php
				        endforeach;
				    } else {
				        ?><tr><td colspan="7"><?php _e( 'No molds currently exist.', 'woocommerce-molds' ) ?></td></tr><?php
				    }?>
			 </tbody>
		</table>
	</div><?php
	}
	##////////////////////##
	########################
	
	#####################
	##// REMOVE MOLD //##
	##/////////////////##
	function remove_mold($mold_id) {
		global $wpdb;
		$molds = $wpdb->query( $wpdb->prepare("DELETE FROM ".$wpdb->prefix."postmeta WHERE meta_id = %d",$mold_id));
		if($molds!=false) {
			?><div id="message" class="updated below-h2"><p style="color:darkOrange;"><?php _e( 'Deleted.', 'woocommerce-molds' ); ?></p></div><?php
		}
	}
	##/////////////////##
	#####################
	
	
	 /////////////////////////////////
	//// MOLDS //////////////////////
	if (!class_exists("WC_Molds")) {

		/**
		 * Molds Class
		 *
		 * Creates variations models to easily pass data to a Woocommerce product variations.
		 *
		 * @class 		WC_Molds
		 * @version		0.1
		 * @category	Class
		 * @author 		Melina Donati
		 */
		class WC_Molds {
			
			##########
			## VARS ##
			##########
			
			// Mold, parent, children //
			private $product_id = -1;
			private $product_name = "Product";
			private $mold_id = 1;
			private $mold_name = "Mold";
			private $mold_children = "";
			private $mold_attribute = "none";
			private $mold_terms = "";
			
			// Mold check data //
			private $mold_is_active = "";
			private $mold_is_downloadable = "";
			private $mold_file_paths = array();
			private $mold_download_limit = null;
			private $mold_download_expiry = null;
			private $mold_is_virtual = "";
			private $mold_is_main_image = "no";
			
			// Mold meta data //
			private $mold_image = "none";
			private $mold_weight = "";
			private $mold_length = "";
			private $mold_width = "";
			private $mold_height = "";
			private $mold_shipping_class = "none";
			
			// Mold price data //
			private $mold_regular_price = null;
			private $mold_is_base_price = "no";
			private $mold_is_add = "no";
			private $mold_is_sub = "no";
			private $mold_is_percent = "no";
			private $mold_sale_price = null;
			private $mold_sale_price_is_base_price = "no";
			private $mold_sale_price_is_add = "no";
			private $mold_sale_price_is_sub = "no";
			private $mold_sale_price_is_percent = "no";
			private $mold_sale_price_dates_from = null;
			private $mold_sale_price_dates_to = null;
			
			// Mold special //
			private $mold_stock = null;
			private $mold_sku = null;
			
			// Application
			private $applied = "no";
			private $old_regular = array();
			private $old_sale = array();
			
			#############******************************************************
			## CREATE  ##******************************************************
			#############******************************************************
			public function __construct() {
			}
			
			############################
			##// PROPERTIES GETTERS //##
			##////////////////////////##
			
			// GET Mold, parent, children //
			public function pm_get_product_id() {
				return $this->product_id;
			}
			public function pm_get_product_name() {
				return $this->product_name;
			}
			public function pm_get_mold_id() {
				return $this->mold_id;
			}
			public function pm_get_mold_name() {
				return $this->mold_name;
			}
			public function pm_get_mold_children() {
				return $this->mold_children;
			}
			public function pm_get_mold_attribute() {
				return $this->mold_attribute;
			}
			public function pm_get_mold_terms() {
				return $this->mold_terms;
			}
			
			// GET Mold check data //
			public function pm_get_mold_is_active() {
				return $this->mold_is_active;
			}
			public function pm_get_mold_is_downloadable() {
				return $this->mold_is_downloadable;
			}
			public function pm_get_mold_file_paths() {
				return $this->mold_file_paths;
			}
			public function pm_get_mold_download_limit() {
				return $this->mold_download_limit;
			}
			public function pm_get_mold_download_expiry() {
				return $this->mold_download_expiry;
			}
			public function pm_get_mold_is_virtual() {
				return $this->mold_is_virtual;
			}
			public function pm_get_mold_is_main_image() {
				return $this->mold_is_main_image;
			}
			
			// GET Mold meta data //
			public function pm_get_mold_image() {
				return $this->mold_image;
			}
			public function pm_get_mold_weight() {
				return $this->mold_weight;
			}
			public function pm_get_mold_length() {
				return $this->mold_length;
			}
			public function pm_get_mold_width() {
				return $this->mold_width;
			}
			public function pm_get_mold_height() {
				return $this->mold_height;
			}
			public function pm_get_mold_shipping_class() {
				return $this->mold_shipping_class;
			}
			
			// GET Mold regular price data //
			public function pm_get_mold_regular_price() {
				return $this->mold_regular_price;
			}
			public function pm_get_mold_is_base_price() {
				return $this->mold_is_base_price;
			}
			public function pm_get_mold_is_add() {
				return $this->mold_is_add;
			}
			public function pm_get_mold_is_sub() {
				return $this->mold_is_sub;
			}
			public function pm_get_mold_is_percent() {
				return $this->mold_is_percent;
			}
			
			// GET Mold sale price data //
			public function pm_get_mold_sale_price() {
				return $this->mold_sale_price;
			}
			public function pm_get_mold_sale_price_is_base_price() {
				return $this->mold_sale_price_is_base_price;
			}
			public function pm_get_mold_sale_price_is_add() {
				return $this->mold_sale_price_is_add;
			}
			public function pm_get_mold_sale_price_is_sub() {
				return $this->mold_sale_price_is_sub;
			}
			public function pm_get_mold_sale_price_is_percent() {
				return $this->mold_sale_price_is_percent;
			}
			public function pm_get_mold_sale_price_dates_from() {
				return $this->mold_sale_price_dates_from;
			}
			public function pm_get_mold_sale_price_dates_to() {
				return $this->mold_sale_price_dates_to;
			}
			
			// GET MOLD SPECIAL
			public function pm_get_mold_stock() {
				return $this->mold_stock;
			}
			public function pm_get_mold_sku() {
				return $this->mold_sku;
			}
			
			// APPLICATION
			public function pm_get_applied() {
				return $this->applied;
			}
			
			############################
			##// PROPERTIES SETTERS //##
			##////////////////////////##
			
			// SET Mold, parent, children //
			public function pm_set_product_id($id) {
				$this->product_id = $id;
			}
			public function pm_set_product_name($name) {
				$this->product_name = $name;
			}
			public function pm_set_mold_id($id) {
				$this->mold_id = $id;
			}
			public function pm_set_mold_name($name) {
				$this->mold_name = $name;
			}
			public function pm_set_mold_children($children) {
				$this->mold_children = $children;
			}
			public function pm_set_mold_attribute($attribute) {
				$this->mold_attribute = $attribute;
			}
			public function pm_set_mold_terms($terms) {
				$this->mold_terms = $terms;
			}
			
			// SET Mold check data //
			public function pm_set_mold_is_active($is_active) {
				$this->mold_is_active = $is_active;
			}
			public function pm_set_mold_is_downloadable($is_downloadable) {
				$this->mold_is_downloadable = $is_downloadable;
			}
			public function pm_set_mold_download_limit($download_limit) {
				$this->mold_download_limit = $download_limit;
			}
			public function pm_set_mold_download_expiry($download_expiry) {
				$this->mold_download_expiry = $download_expiry;
			}
			public function pm_set_mold_file_paths($file_paths) {
				$this->mold_file_paths = $file_paths;
			}
			public function pm_set_mold_is_virtual($is_virtual) {
				$this->mold_is_virtual = $is_virtual;
			}
			public function pm_set_mold_is_main_image($is_main_image) {
				$this->mold_is_main_image = $is_main_image;
			}
			
			// SET Mold meta data //
			public function pm_set_mold_image($image) {
				$this->mold_image = $image;
			}
			public function pm_set_mold_weight($weight) {
				$this->mold_weight = $weight;
			}
			public function pm_set_mold_length($length) {
				$this->mold_length = $length;
			}
			public function pm_set_mold_width($width) {
				$this->mold_width = $width;
			}
			public function pm_set_mold_height($height) {
				$this->mold_height = $height;
			}
			public function pm_set_mold_shipping_class($shipping_class) {
				$this->mold_shipping_class = $shipping_class;
			}
			
			// SET Mold regular price data //
			public function pm_set_mold_regular_price($regular_price) {
				$this->mold_regular_price = $regular_price;
			}
			public function pm_set_mold_is_base_price($is_base_price) {
				$this->mold_is_base_price = $is_base_price;
			}
			public function pm_set_mold_is_add($is_add) {
				$this->mold_is_add = $is_add;
			}
			public function pm_set_mold_is_sub($is_sub) {
				$this->mold_is_sub = $is_sub;
			}
			public function pm_set_mold_is_percent($is_percent) {
				$this->mold_is_percent = $is_percent;
			}
			
			// SET Mold sale price data //
			public function pm_set_mold_sale_price($sale_price) {
				$this->mold_sale_price = $sale_price;
			}
			public function pm_set_mold_sale_price_is_base_price($is_base_price) {
				$this->mold_sale_price_is_base_price = $is_base_price;
			}
			public function pm_set_mold_sale_price_is_add($is_add) {
				$this->mold_sale_price_is_add = $is_add;
			}
			public function pm_set_mold_sale_price_is_sub($is_sub) {
				$this->mold_sale_price_is_sub = $is_sub;
			}
			public function pm_set_mold_sale_price_is_percent($is_percent) {
				$this->mold_sale_price_is_percent = $is_percent;
			}
			public function pm_set_mold_sale_price_dates_from($sale_price_dates_from) {
				$this->mold_sale_price_dates_from = $sale_price_dates_from;
			}
			public function pm_set_mold_sale_price_dates_to($sale_price_dates_to) {
				$this->mold_sale_price_dates_to = $sale_price_dates_to;
			}
			
			// GET MOLD SPECIAL
			public function pm_set_mold_stock($stock) {
				$this->mold_stock = $stock;
			}
			public function pm_set_mold_sku($sku) {
				$this->mold_sku = $sku;
			}
			
			// APPLICATION
			public function pm_set_applied($applied) {
				$this->applied = $applied;
			}
			
			##############################################
			##// IF NOT EXISTS CREATE TEMPORARY TABLE //##
			##//////////////////////////////////////////##
			public function pm_create_temporary_table() {
			
				global $wpdb;
				
				## Checking if table exists, if not, create it ##
				$query = "CREATE TABLE IF NOT EXISTS wc_mold (
					product_id INT,
					product_name VARCHAR(150),
					mold_id INT,
					mold_name VARCHAR(150),
					children TEXT,
					attribute  VARCHAR(150),
					terms TEXT,
					is_active VARCHAR(3),
					is_downloadable VARCHAR(3),
					file_paths TEXT,
					download_limit INT,
					download_expiry INT,
					is_virtual VARCHAR(3),
					is_main_image VARCHAR(3),
					image TEXT,
					weight VARCHAR(50),
					length VARCHAR(50),
					width VARCHAR(50),
					height VARCHAR(50),
					shipping_class TEXT,
					regular_price INT,
					is_base_price VARCHAR(3),
					is_add VARCHAR(3),
					is_sub VARCHAR(3),
					is_percent VARCHAR(3),
					sale_price INT,
					sale_price_is_base_price VARCHAR(3),
					sale_price_is_add VARCHAR(3),
					sale_price_is_sub VARCHAR(3),
					sale_price_is_percent VARCHAR(3),
					sale_price_dates_from VARCHAR(150),
					sale_price_dates_to VARCHAR(150),
					stock INT,
					sku TEXT,
					applied VARCHAR(3)
				)";
				$wpdb->query($query);
				
				## Defining mold_id as primary key ##
				$query = "ALTER TABLE `wc_mold` ADD PRIMARY KEY ( `mold_id` )";
				$wpdb->query($query);
			}
			##//////////////////////////////////////////##
			##############################################
			
			##########################
			##// TEMPORARY SAVING //##
			##//////////////////////##
			public function pm_temporary_save() {
			
				global $wpdb;
				
				$this->pm_create_temporary_table();
				
				## Checking if temporary save already exists thanks to the primary key ##
				$query = "SELECT * FROM wc_mold WHERE mold_id = 1";
				$result = $wpdb->get_row($query);
				
				## If not we create our 'temporary saving' row ##
				if(empty($result)) {
				
					$wpdb->insert(
						'wc_mold',
						array(
							'product_id' => $this->product_id,
							'product_name' => $this->product_name,
							'mold_id' => $this->mold_id,
							'mold_name' => $this->mold_name,
							'children' => $this->mold_children,
							'attribute' => $this->mold_attribute,
							'terms' => $this->mold_terms,
							'is_active' => $this->mold_is_active,
							'is_downloadable' => $this->mold_is_downloadable,
							'file_paths' => implode(",",$this->mold_file_paths),
							'download_limit' => $this->mold_download_limit,
							'download_expiry' => $this->mold_download_expiry,
							'is_virtual' => $this->mold_is_virtual,
							'is_main_image' => $this->mold_is_main_image,
							'image' => $this->mold_image,
							'weight' => $this->mold_weight,
							'length' => $this->mold_length,
							'width' => $this->mold_width,
							'height' => $this->mold_height,
							'shipping_class' => $this->mold_shipping_class,
							'regular_price' => $this->mold_regular_price,
							'is_base_price' => $this->mold_is_base_price,
							'is_add' => $this->mold_is_add,
							'is_sub' => $this->mold_is_sub,
							'is_percent' => $this->mold_is_percent,
							'sale_price' => $this->mold_sale_price,
							'sale_price_is_base_price' => $this->mold_sale_price_is_base_price,
							'sale_price_is_add' => $this->mold_sale_price_is_add,
							'sale_price_is_sub' => $this->mold_sale_price_is_sub,
							'sale_price_is_percent' => $this->mold_sale_price_is_percent,
							'sale_price_dates_from' => $this->mold_sale_price_dates_from,
							'sale_price_dates_to' => $this->mold_sale_price_dates_to,
							'stock' => $this->mold_stock,
							'sku' => $this->mold_sku,
							'applied' => $this->applied
						),
						array(
							'%d', // PRODUCT ID
							'%s', // PRODUCT NAME
							'%d', // MOLD ID
							'%s', // MOLD NAME
							'%s', // CHILDREN
							'%s', // ATTRIBUTE
							'%s', // TERMS
							'%s', // IS ACTIVE
							'%s', // IS DOWNLOADABLE
							'%s', // FILE PATHS
							'%d', // DOWNLOAD LIMIT
							'%d', // DOWNLOAD EXPIRY
							'%s', // IS VIRTUAL
							'%s', // IS MAIN IMAGE
							'%s', // IMAGE
							'%s', // WEIGHT
							'%s', // LENGTH
							'%s', // WIDTH
							'%s', // HEIGHT
							'%s', // SHIPPING CLASS
							'%d', // REGULAR PRICE
							'%s', // IS BASE PRICE
							'%s', // IS ADD
							'%s', // IS SUB
							'%s', // IS PERCENT
							'%d', // SALE PRICE
							'%s', // IS BASE PRICE
							'%s', // IS ADD
							'%s', // IS SUB
							'%s', // IS PERCENT
							'%s', // DATES FROM
							'%s', // DATES TO
							'%d', // STOCK
							'%s', // SKU
							'%s' // APPLIED
						)
					);
						
				## If the table exist we just update it ##
				} else {
				
					$wpdb->update(
						'wc_mold',
						array(
							'product_id' => $this->product_id,
							'product_name' => $this->product_name,
							'mold_name' => $this->mold_name,
							'children' => $this->mold_children,
							'attribute' => $this->mold_attribute,
							'terms' => $this->mold_terms,
							'is_active' => $this->mold_is_active,
							'is_downloadable' => $this->mold_is_downloadable,
							'file_paths' => implode(",",$this->mold_file_paths),
							'download_limit' => $this->mold_download_limit,
							'download_expiry' => $this->mold_download_expiry,
							'is_virtual' => $this->mold_is_virtual,
							'is_main_image' => $this->mold_is_main_image,
							'image' => $this->mold_image,
							'weight' => $this->mold_weight,
							'length' => $this->mold_length,
							'width' => $this->mold_width,
							'height' => $this->mold_height,
							'shipping_class' => $this->mold_shipping_class,
							'regular_price' => $this->mold_regular_price,
							'is_base_price' => $this->mold_is_base_price,
							'is_add' => $this->mold_is_add,
							'is_sub' => $this->mold_is_sub,
							'is_percent' => $this->mold_is_percent,
							'sale_price' => $this->mold_sale_price,
							'sale_price_is_base_price' => $this->mold_sale_price_is_base_price,
							'sale_price_is_add' => $this->mold_sale_price_is_add,
							'sale_price_is_sub' => $this->mold_sale_price_is_sub,
							'sale_price_is_percent' => $this->mold_sale_price_is_percent,
							'sale_price_dates_from' => $this->mold_sale_price_dates_from,
							'sale_price_dates_to' => $this->mold_sale_price_dates_to,
							'stock' => $this->mold_stock,
							'sku' => $this->mold_sku,
							'applied' => $this->applied
						),
						array( 'mold_id' => $this->mold_id ),
						array(
							'%d', // PRODUCT ID
							'%s', // PRODUCT NAME
							'%s', // MOLD NAME
							'%s', // CHILDREN
							'%s', // ATTRIBUTE
							'%s', // TERMS
							'%s', // IS ACTIVE
							'%s', // IS DOWNLOADABLE
							'%s', // FILE PATHS
							'%d', // DOWNLOAD LIMIT
							'%d', // DOWNLOAD EXPIRY
							'%s', // IS VIRTUAL
							'%s', // IS MAIN IMAGE
							'%s', // IMAGE
							'%s', // WEIGHT
							'%s', // LENGTH
							'%s', // WIDTH
							'%s', // HEIGHT
							'%s', // SHIPPING CLASS
							'%d', // REGULAR PRICE
							'%s', // IS BASE PRICE
							'%s', // IS ADD
							'%s', // IS SUB
							'%s', // IS PERCENT
							'%d', // SALE PRICE
							'%s', // IS BASE PRICE
							'%s', // IS ADD
							'%s', // IS SUB
							'%s', // IS PERCENT
							'%s', // DATES FROM
							'%s', // DATES TO
							'%d', // STOCK
							'%s', // SKU
							'%s' // APPLIED
						),
						array( '%d' )
					);
				}
			}
			##//////////////////////##
			##########################
			
			##################################
			##// LOADING TEMPORARY SAVING //##
			##//////////////////////////////##
			public function pm_load_temporary_save() {
			
				global $wpdb;
				
					$query = "SELECT * FROM wc_mold WHERE mold_id = 1";
					$result = $wpdb->get_results($query,ARRAY_A);

					if($result) {
						foreach($result as $data) {
								$this->product_id = $data[product_id];
								$this->product_name = $data[product_name];
								$this->mold_id = $data[mold_id];
								$this->mold_name = $data[mold_name];
								$this->mold_children = $data[children];
								$this->mold_attribute = $data[attribute];
								$this->mold_terms = $data[terms];
								$this->mold_is_active = $data[is_active];
								$this->mold_is_downloadable = $data[is_downloadable];
								$this->mold_file_paths = explode(",",$data[file_paths]);
								$this->mold_download_limit = $data[download_limit];
								$this->mold_download_expiry = $data[download_expiry];
								$this->mold_is_virtual = $data[is_virtual];
								$this->mold_is_main_image = $data[is_main_image];
								$this->mold_image = $data[image];
								$this->mold_weight = $data[weight];
								$this->mold_length = $data[length];
								$this->mold_width = $data[width];
								$this->mold_height = $data[height];
								$this->mold_shipping_class = $data[shipping_class];
								$this->mold_regular_price = $data[regular_price];
								$this->mold_is_base_price = $data[is_base_price];
								$this->mold_is_add = $data[is_add];
								$this->mold_is_sub = $data[is_sub];
								$this->mold_is_percent = $data[is_percent];
								$this->mold_sale_price = $data[sale_price];
								$this->mold_sale_price_is_base_price = $data[sale_price_is_base_price];
								$this->mold_sale_price_is_add = $data[sale_price_is_add];
								$this->mold_sale_price_is_sub = $data[sale_price_is_sub];
								$this->mold_sale_price_is_percent = $data[sale_price_is_percent];
								$this->mold_sale_price_dates_from = $data[sale_price_dates_from];
								$this->mold_sale_price_dates_to = $data[sale_price_dates_to];
								$this->mold_stock = $data[stock];
								$this->mold_sku = $data[sku];
								$this->applied = $data[applied];
						}
					}
			}
			###//////////////////////////////##
			###################################
			
			###################################
			##// DELETING TEMPORARY SAVING //##
			##///////////////////////////////##
			public function pm_delete_temporary_save() {
				global $wpdb;
				$query = "DROP TABLE IF EXISTS wc_mold";
				$wpdb->query($query);
			}
			##///////////////////////////////##
			###################################
			
			###############################
			##// SET TO DEFAULT VALUES //##
			##///////////////////////////##
			public function pm_default_values() {
				// Mold, parent, children //
				$this->product_id = -1;
				$this->product_name = "Product";
				$this->mold_id = 1;
				$this->mold_name = "Mold";
				$this->mold_children = "";
				$this->mold_attribute = "none";
				$this->mold_terms = "";
				
				// Mold check data //
				$this->mold_is_active = "";
				$this->mold_is_downloadable = "";
				$this->mold_file_paths = array();
				$this->mold_download_limit = null;
				$this->mold_download_expiry = null;
				$this->mold_is_virtual = "";
				$this->mold_is_main_image = "no";
				
				// Mold meta data //
				$this->mold_image = "none";
				$this->mold_weight = "";
				$this->mold_length = "";
				$this->mold_width = "";
				$this->mold_height = "";
				$this->mold_shipping_class = "none";
				
				// Mold regular price data //
				$this->mold_regular_price = null;
				$this->mold_is_base_price = "no";
				$this->mold_is_add = "no";
				$this->mold_is_sub = "no";
				$this->mold_is_percent = "no";
				
				// Mold sale price data //
				$this->mold_sale_price = null;
				$this->mold_sale_price_is_base_price = "no";
				$this->mold_sale_price_is_add = "no";
				$this->mold_sale_price_is_sub = "no";
				$this->mold_sale_price_is_percent = "no";
				$this->mold_sale_price_dates_from = null;
				$this->mold_sale_price_dates_to = null;
				
				// Mold special //
				$this->mold_stock = null;
				$this->mold_sku = null;
				
				// Application
				$this->applied = "no";
			}
			##///////////////////////////##
			###############################
			
			##############################
			##// EASY VALUES CHECKING //##
			##//////////////////////////##
			public function pm_check_values($all = false) {
			
				echo '<div class="field">';
				echo '<b>MOLD OBJECT VALUES :</b><br>';
				
				// Mold, parent, children //
				echo '<u>Parent product ID : </u> ';
				echo $this->product_id;
				echo '<br>';
				echo '<u>Parent product NAME : </u> ';
				echo $this->product_name;
				echo '<br>';
				echo '<u>Mold ID : </u> ';
				echo $this->mold_id;
				echo '<br>';
				echo '<u>Mold NAME : </u> ';
				echo $this->mold_name;
				echo '<br>';
				echo '<u>Mold CHILDREN IDs : </u> ';
				echo $this->mold_children;
				echo '<br>';
				echo '<u>Mold ATTRIBUTE : </u> ';
				echo $this->mold_attribute;
				echo '<br>';
				echo '<u>Mold TERMS : </u> ';
				echo $this->mold_terms;
				echo '<br>';
				
				if($all) {
					// Mold check data //
					echo '<u>Mold IS ACTIVE : </u> ';
					echo $this->mold_is_active;
					echo '<br>';
					echo '<u>Mold IS DOWNLOADABLE : </u> ';
					echo $this->mold_is_downloadable;
					echo '<br>';
					echo '<u>Mold FILE PATHS: </u> ';
					echo implode(",",$this->mold_file_paths);
					echo '<br>';
					echo '<u>Mold DOWNLOAD LIMIT: </u> ';
					echo $this->mold_download_limit;
					echo '<br>';
					echo '<u>Mold DOWNLOAD EXPIRY : </u> ';
					echo $this->mold_download_expiry;
					echo '<br>';
					echo '<u>Mold IS VIRTUAL : </u> ';
					echo $this->mold_is_virtual;
					echo '<br>';
					echo '<u>Mold IS MAIN IMAGE : </u> ';
					echo $this->mold_is_main_image;
					echo '<br>';echo '<br>';
					
					// Mold meta data //
					echo '<u>Mold IMAGE : </u> ';
					echo $this->mold_image;
					echo '<br>';
					echo '<u>Mold WEIGHT : </u> ';
					echo $this->mold_weight;
					echo '<br>';
					echo '<u>Mold LENGTH : </u> ';
					echo $this->mold_length;
					echo '<br>';
					echo '<u>Mold WIDTH : </u> ';
					echo $this->mold_width;
					echo '<br>';
					echo '<u>Mold HEIGHT : </u> ';
					echo $this->mold_height;
					echo '<br>';
					echo '<u>Mold SHIPPING CLASS : </u> ';
					echo $this->mold_shipping_class;
					echo '<br>';echo '<br>';
					
					// Mold price data //
					echo '<u>Mold REGULAR PRICE : </u> ';
					echo $this->mold_regular_price;
					echo '<br>';
					echo '<u>Mold IS BASE PRICE : </u> ';
					echo $this->mold_is_base_price;
					echo '<br>';
					echo '<u>Mold IS ADD : </u> ';
					echo $this->mold_is_add;
					echo '<br>';
					echo '<u>Mold IS SUB : </u> ';
					echo $this->mold_is_sub;
					echo '<br>';
					echo '<u>Mold IS PERCENT : </u> ';
					echo $this->mold_is_percent;
					echo '<br>';
					
					echo '<u>Mold SALE PRICE : </u> ';
					echo $this->mold_sale_price;
					echo '<br>';
					echo '<u>Mold SALE IS BASE PRICE : </u> ';
					echo $this->mold_sale_price_is_base_price;
					echo '<br>';
					echo '<u>Mold SALE IS ADD : </u> ';
					echo $this->mold_sale_price_is_add;
					echo '<br>';
					echo '<u>Mold SALE IS SUB : </u> ';
					echo $this->mold_sale_price_is_sub;
					echo '<br>';
					echo '<u>Mold SALE IS PERCENT : </u> ';
					echo $this->mold_sale_price_is_percent;
					echo '<br>';
					echo '<u>Mold SALE DATES FROM : </u> ';
					echo $this->mold_sale_price_dates_from;
					echo '<br>';
					echo '<u>Mold SALE DATES TO : </u> ';
					echo $this->mold_sale_price_dates_to;
					echo '<br>';echo '<br>';
					
					echo '<u>STOCK : </u> ';
					echo $this->mold_stock;
					echo '<br>';echo '<br>';
					echo '<u>SKU : </u> ';
					echo $this->mold_sku;
					echo '<br>';echo '<br>';
					
					echo '<u>APPLIED : </u> ';
					echo $this->applied;
					echo '<br>';echo '<br>';
				}
				echo '</div>';
			}
			##//////////////////////////##
			##############################
			
			############################
			##// SAVING TO DATABASE //##
			##////////////////////////##
			public function pm_save_to_db($id = 0) {
				
				global $wpdb;
				
				$mold_array = array (
						'product_name' => $this->product_name,
						'name' => $this->mold_name,
						'children' => $this->mold_children,
						'attribute' => $this->mold_attribute,
						'terms' => $this->mold_terms,
						'is_active' => $this->mold_is_active,
						'is_downloadable' => $this->mold_is_downloadable,
						'file_paths' => implode(",",$this->mold_file_paths),
						'download_limit' => $this->mold_download_limit,
						'download_expiry' => $this->mold_download_expiry,
						'is_virtual' => $this->mold_is_virtual,
						'is_main_image' => $this->mold_is_main_image,
						'image' => $this->mold_image,
						'weight' => $this->mold_weight,
						'length' => $this->mold_length,
						'width' => $this->mold_width,
						'height' => $this->mold_height,
						'shipping_class' => $this->mold_shipping_class,
						'regular_price' => $this->mold_regular_price,
						'is_base_price' => $this->mold_is_base_price,
						'is_add' => $this->mold_is_add,
						'is_sub' => $this->mold_is_sub,
						'is_percent' => $this->mold_is_percent,
						'sale_price' => $this->mold_sale_price,
						'sale_price_is_base_price' => $this->mold_sale_price_is_base_price,
						'sale_price_is_add' => $this->mold_sale_price_is_add,
						'sale_price_is_sub' => $this->mold_sale_price_is_sub,
						'sale_price_is_percent' => $this->mold_sale_price_is_percent,
						'sale_price_dates_from' => $this->mold_sale_price_dates_from,
						'sale_price_dates_to' => $this->mold_sale_price_dates_to,
						'stock' => $this->mold_stock,
						'sku' => $this->mold_sku,
						'applied' => $this->applied
					);
					
					$packed_mold = serialize($mold_array);
				
				if($id!=0) {
					$result = $wpdb->update(
						$wpdb->prefix.'postmeta',
						array(
							'post_id' => $this->product_id,
							'meta_key' => '_mold',
							'meta_value' => $packed_mold
						),
						array(
							'meta_id' => $id
						),
						array(
							'%d',
							'%s',
							'%s'
						),
						array(
							'%d'
						)
						
					);
				} else {
					$result = $wpdb->insert(
						$wpdb->prefix.'postmeta',
						array(
							'post_id' => $this->product_id,
							'meta_key' => '_mold',
							'meta_value' => $packed_mold
						),
						array(
							'%d',
							'%s',
							'%s'
						)
					);
					if($result!=false) {
						?><div id="message" class="updated below-h2"><p style="color:green;"><?php _e( 'Saved.', 'woocommerce-molds' ); ?></p></div><?php
					} else {
						?><div id="message" class="updated below-h2"><p style="color:red;"><?php _e( 'Couldn\'t be saved.', 'woocommerce-molds' ); ?></p></div><?php
					}
				}
				
				$this->pm_delete_temporary_save();
			}
			##////////////////////////##
			############################
			
			###############################
			##// LOADING FROM DATABASE //##
			##///////////////////////////##
			public function pm_load_from_db($id) {
				
				global $wpdb;
				$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."postmeta WHERE meta_id = %d", $id));
				
				$this->mold_id = $data[0]->meta_id;
				$this->product_id = $data[0]->post_id;
				
				$properties = unserialize($data[0]->meta_value);
				
				$this->product_name = $properties['product_name'];
				$this->mold_name = $properties['name'];
				$this->mold_children = $properties['children'];
				$this->mold_attribute = $properties['attribute'];
				$this->mold_terms = $properties['terms'];
				
				$this->mold_is_active = $properties['is_active'];
				$this->mold_is_downloadable = $properties['is_downloadable'];
				$file_paths = $properties['file_paths'];
				$this->mold_file_paths = explode(",",$file_paths);
				$this->mold_download_limit = $properties['download_limit'];
				$this->mold_download_expiry = $properties['download_expiry'];
				$this->mold_is_virtual = $properties['is_virtual'];
				$this->mold_is_main_image = $properties['is_main_image'];
				
				$this->mold_image = $properties['image'];
				$this->mold_weight = $properties['weight'];
				$this->mold_length = $properties['length'];
				$this->mold_width = $properties['width'];
				$this->mold_height = $properties['height'];
				$this->mold_shipping_class = $properties['shipping_class'];
				
				$this->mold_regular_price = $properties['regular_price'];
				$this->mold_is_base_price = $properties['is_base_price'];
				$this->mold_is_add = $properties['is_add'];
				$this->mold_is_sub = $properties['is_sub'];
				$this->mold_is_percent = $properties['is_percent'];
				
				$this->mold_sale_price = $properties['sale_price'];
				$this->mold_sale_price_is_base_price = $properties['sale_price_is_base_price'];
				$this->mold_sale_price_is_add = $properties['sale_price_is_add'];
				$this->mold_sale_price_is_sub = $properties['sale_price_is_sub'];
				$this->mold_sale_price_is_percent = $properties['sale_price_is_percent'];
				$this->mold_sale_price_dates_from = $properties['sale_price_dates_from'];
				$this->mold_sale_price_dates_to = $properties['sale_price_dates_to'];	
				
				$this->mold_stock = $properties['stock'];	
				$this->mold_sku = $properties['sku'];	
				
				$this->applied = $properties['applied'];
			}
			##////////////////////////##
			############################
			
			###########################
			##// MOLD TO VARIATION //##
			##///////////////////////##
			protected function pm_mold_to_variation($variation_id) {
			
				global $wpdb;
			
				// We store what queries return in one number
				$result = 0;
				
				// STOCK
				if(!empty($this->mold_stock)) {
					$result = $this->pm_update_meta_value($variation_id,'_stock',$this->mold_stock);
				}
				// SKU
				if(!empty($this->mold_sku)) {
					$result = $this->pm_update_meta_value($variation_id,'_sku',$this->mold_sku);
				}
				
				// WEIGHT
				if(!empty($this->mold_weight)) {
					$result = $this->pm_update_meta_value($variation_id,'_weight',$this->mold_weight);
				}
				// LENGTH
				if(!empty($this->mold_length)) {
					$result += $this->pm_update_meta_value($variation_id,'_length',$this->mold_length);
				}
				// WIDTH
				if(!empty($this->mold_width)) {
					$result += $this->pm_update_meta_value($variation_id,'_width',$this->mold_width);
				}
				// HEIGHT
				if(!empty($this->mold_height)) {
					$result += $this->pm_update_meta_value($variation_id,'_height',$this->mold_height);
				}
				
				// CHECKING IF THE VARIATION ALREADY HAS AN IMAGE
				$variation_image = $wpdb->get_var($wpdb->prepare(
					"SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_thumbnail_id' AND post_id = %d",
					$variation_id
				));
				
				// IF VARIATION ALREADY HAS AN IMAGE
				if(!empty($variation_image)) {
				
					if($this->mold_is_main_image=="yes") {
						
						// IMAGE ATTACHMENT //
						// Get the image id
						$image_id = $wpdb->get_var($wpdb->prepare(
							"SELECT ID FROM ".$wpdb->prefix."posts WHERE guid = %s",
							$this->mold_image
						));
						// Update its parent post, now it is the variation id
						$result += $wpdb->update($wpdb->prefix.'posts',array('post_parent' => '%d'),array('ID' => '%d'),array($this->product_id),array($image_id));
						// Update the variation meta, giving it the image id
						$result += $this->pm_update_meta_value($variation_id,'_thumbnail_id',$image_id);
						/////////////////////
					}
				} else {
					
						if($this->mold_image!="none") {
				
							// IMAGE ATTACHMENT //
							// Get the image id
							$image_id = $wpdb->get_var($wpdb->prepare(
								"SELECT ID FROM ".$wpdb->prefix."posts WHERE guid = %s",
								$this->mold_image
							));
							// Update its parent post, now it is the variation id
							$result += $wpdb->update($wpdb->prefix.'posts',array('post_parent' => '%d'),array('ID' => '%d'),array($this->product_id),array($image_id));
							// Update the variation meta, giving it the image id
							$result += $this->pm_update_meta_value($variation_id,'_thumbnail_id',$image_id);
							/////////////////////
						}
				}
				
				
				// IS ACTIVE
				if($this->mold_is_active=="yes") {
					$result += $wpdb->query($wpdb->prepare(
						'UPDATE ".$wpdb->prefix."posts SET post_status = "publish" WHERE ID = %d',
						$variation_id
					));
				} else if($this->mold_is_active=="not") {
					$result += $wpdb->query($wpdb->prepare(
						'UPDATE ".$wpdb->prefix."posts SET post_status = "private" WHERE ID = %d',
						$variation_id
					));
				}
				// IS VIRTUAL
				if(($this->mold_is_virtual=="yes")||($this->mold_is_virtual=="no")) {
					$result += $this->pm_update_meta_value($variation_id,'_virtual',$this->mold_is_virtual);
				}
				// IS DOWNLOADABLE
				if($this->mold_is_downloadable=="yes") {
					$result += $this->pm_update_meta_value($variation_id,'_downloadable','yes');
					$result += $this->pm_update_meta_value($variation_id,'_download_limit',$this->mold_download_limit);
					$result += $this->pm_update_meta_value($variation_id,'_download_expiry',$this->mold_download_expiry);
					$result += $this->pm_update_meta_value($variation_id,'_file_paths',$this->mold_file_paths);
				} else if($this->mold_is_downloadable=="not") {
					$result += $this->pm_update_meta_value($variation_id,'_downloadable','no');
				}
				
				if(!empty($this->mold_regular_price)) {
				
					// REGULAR PRICE : is base price
					if($this->mold_is_base_price=="yes") {
					
						$result += $this->pm_update_meta_value($variation_id,'_regular_price',$this->mold_regular_price);
						
					// REGULAR PRICE : adding or substracting
					} else {
					
						// Get variation regular price, if not equals 0
						$regular_price = $this->pm_select_meta_value($variation_id,'_regular_price');
						if(empty($regular_price)) {
							$regular_price = 0;
						}
						
						// We save the old price //
						$this->old_regular[$variation_id]=$regular_price;
						
						// PRICE VAR
						$new_regular_price = 0;
						
						// ADDING TO REGULAR PRICE
						if($this->mold_is_add=='yes') {
							// IN %
							if($this->mold_is_percent=='yes') { 
								$new_regular_price = $this->pm_price_add($regular_price,$this->mold_regular_price,true); 
							// OR NOT
							} else {
								$new_regular_price = $this->pm_price_add($regular_price,$this->mold_regular_price);
							}
						// SUBSTRACTING FROM REGULAR PRICE
						} else if($this->mold_is_sub=='yes') {
							// IN %
							if($this->mold_is_percent=='yes') { 
								$new_regular_price = $this->pm_price_sub($regular_price,$this->mold_regular_price,true); 
							// OR NOT
							} else {
								$new_regular_price = $this->pm_price_sub($regular_price,$this->mold_regular_price);
							}
						}
						
						// CHANGING THE REGULAR PRICE TO OUR NEW VALUE
						$result += $this->pm_update_meta_value($variation_id,'_regular_price',$new_regular_price);
					}
				}
				
				if(!empty($this->mold_sale_price)) {
				
					// SALE PRICE : is base price
					if($this->mold_sale_price_is_base_price=="yes") {
					
						$result += $this->pm_update_meta_value($variation_id,'_sale_price',$this->mold_sale_price);
						
					// SALE PRICE : adding or substracting
					} else {
					
						// Get variation sale price, if not equals 0
						$sale_price = $this->pm_select_meta_value($variation_id,'_sale_price');
						if(empty($sale_price)) {
							$sale_price = 0;
						}
						
						// We save the old price //
						$this->old_sale[$variation_id]=$sale_price;
						
						// PRICE VAR
						$new_sale_price = 0;
						
						// ADDING TO REGULAR PRICE
						if($this->mold_sale_price_is_add=='yes') {
							// IN %
							if($this->mold_sale_price_is_percent=='yes') { 
								$new_sale_price = $this->pm_price_add($sale_price,$this->mold_sale_price,true); 
							// OR NOT
							} else {
								$new_sale_price = $this->pm_price_add($sale_price,$this->mold_sale_price);
							}
						// SUBSTRACTING FROM REGULAR PRICE
						} else if($this->mold_sale_price_is_sub=='yes') {
							// IN %
							if($this->mold_sale_price_is_percent=='yes') { 
								$new_sale_price = $this->pm_price_sub($sale_price,$this->mold_sale_price,true); 
							// OR NOT
							} else {
								$new_sale_price = $this->pm_price_sub($sale_price,$this->mold_sale_price);
							}
						}
						
						// CHANGING THE REGULAR PRICE TO OUR NEW VALUE
						$result += $this->pm_update_meta_value($variation_id,'_sale_price',$new_sale_price);
					}
				}
				
				// SALE PRICE DATES FROM
				if(!empty($this->mold_sale_price_dates_from)) {
					$result += $this->pm_update_meta_value($variation_id,'_sale_price_dates_from',$this->mold_sale_price_dates_from);
				}
				// SALE PRICE DATES TO
				if(!empty($this->mold_sale_price_dates_to)) {
					$result += $this->pm_update_meta_value($variation_id,'_sale_price_dates_to',$this->mold_sale_price_dates_to);
				}
				
				// PRICE (price displayed in the shop as the current price)
				$object = $this->pm_select_meta_value($variation_id,'_sale_price');
				$sale_price = intval($object->meta_value);
				// If there is a sale price it is the price
				if(!empty($sale_price)) {
					$result += $this->pm_update_meta_value($variation_id,'_price',$sale_price);
				// If not, regular price is the price
				} else {
					$object = $this->pm_select_meta_value($variation_id,'_regular_price');
					$regular_price = intval($object->meta_value);
					if(!empty($regular_price)) {
						$result += $this->pm_update_meta_value($variation_id,'_price',$regular_price);
					// If we have no price data we do nothing and let the price live its life
					}
				}
				
				// SHIPPING/TAX CLASS
				if(!empty($this->mold_shipping_class)) {
					$result += $this->pm_update_meta_value($variation_id,'_tax_class',$this->mold_shipping_class);
				}
				
				## END #######################################################
				return $result;
				##############################################################
			}
			##///////////////////////##
			###########################
			
			#############################
			##// TOOL : ADD TO PRICE //##
			##/////////////////////////##
			protected function pm_price_add($original_price,$added,$percent = false) {
				$price = $original_price;
				if($percent) {
					$to_add = ($added*$original_price)/100;
					$price = $original_price + $to_add;
				} else {
					$price = $original_price + $added;
				}
				return $price;
			}
			##/////////////////////////##
			##// TOOL : SUB TO PRICE //##
			##/////////////////////////##
			protected function pm_price_sub($original_price,$subbed,$percent = false) {
				$price = $original_price;
				if($percent) {
					$to_sub = ($subbed*$original_price)/100;
					$price = $original_price - $to_sub;
				} else {
					$price = $original_price - $subbed;
				}
				return $price;
			}
			##/////////////////////////##
			#############################
			
			#################################################
			##// TOOL : UPDATE META VALUE FOR VARIATIONS //##
			##/////////////////////////////////////////////##
			protected function pm_update_meta_value($id,$key,$value) {
				$update = update_post_meta( $id, $key, $value );
				if($update==false) {
					$add = add_post_meta( $id, $key, $value, true );
				}
				if($update||$add) {
					return 1;
				}
			}
			##/////////////////////////////////////////////##
			#################################################
			
			##################################################
			##// TOOL : SELECT META VALUE FROM VARIATIONS //##
			##//////////////////////////////////////////////##
			protected function pm_select_meta_value($id,$key) {
				global $wpdb;
				$result = $wpdb->get_var($wpdb->prepare(
						'SELECT meta_value FROM '.$wpdb->prefix.'postmeta WHERE post_id = %d AND meta_key = %s',
						$id,$key
					)			
				);
				
				return $result;
			}
			##//////////////////////////////////////////////##
			##################################################
			
			##############################################
			##// APPLYING MOLD TO CHILDREN VARIATIONS //##
			##//////////////////////////////////////////##
			public function pm_apply_mold() {
			
				global $wpdb;
				
				// We create the right meta_key for variations
				$attribute = "attribute_".$this->mold_attribute;
			
			// SPECIFIC TERM(s) GIVEN
			if($this->mold_terms!='all') {
				// We manipulate the terms array to adjust to SQL syntax
				$mold_terms = explode(',',$this->mold_terms);
				
				// WE MUST USE THE SLUG INSTEAD OF THE TERMS NAMES //
				$slug_terms = array();
				foreach($mold_terms as $term) {
					$slug = $wpdb->get_var($wpdb->prepare(
						"SELECT slug FROM ".$wpdb->prefix."terms WHERE name = %s",
						$term
					));
					$slug_terms[] = $slug;
				}
				$terms = "'" . implode("','", $slug_terms) . "'";
				
				## FIND VARIATIONS
				// We select the IDs of children variations of product_id, that are using mold_attribute and one or several of the mold_terms
				$query = "SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key = '".$attribute."' AND meta_value IN (".$terms.") AND post_id IN (SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type = 'product_variation' AND post_parent = ".$this->product_id.")";
				$product_variations = $wpdb->get_results($query);
			
			// ANY VARIATION CONCERNED BY THE ATTRIBUTE
			} else {
			
				## FIND VARIATIONS
				// We select the IDs of children variations of product_id, that are using mold_attribute and one or several of the mold_terms
				$query = "SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key = '".$attribute."' AND post_id IN (SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type = 'product_variation' AND post_parent = ".$this->product_id.")";
				$product_variations = $wpdb->get_results($query);
				
			}
				
				// IF $PRODUCT_VARIATIONS IS NOT EMPTY
				if(!empty($product_variations)) {
				
					?><div id="message" class="updated below-h2"><?php
				
					$count = 0;
					?><p style="color:green;line-height:12px;"><?php
					foreach($product_variations as $variation) {
									## UPDATE VARIATIONS
									// We turn the mold data into variation data
									$result = $this->pm_mold_to_variation($variation->post_id);
									// And count one variation more done #### NEEDS RETURN TRUE OR FALSE VALUE TO CHECK IF VARIATION DONE OR NOT
									if($result>0) {
										echo '#';
										echo $variation->post_id;
										echo ' [ ';
										echo $result;
										echo ' ] - ';
										$count++;
									} else if($result<=0) {
										?><b style="font-weight:normal;color:darkOrange;line-height:10px;"><?php _e( 'No update on #', 'woocommerce-molds' );
										echo $variation->post_id; 
										echo ' - '; ?></b><?php
									}
					}
					// SAVING OLD PRICES FOR UNAPPLYING //
					$old_regular_prices = serialize($this->old_regular);
					$old_sale_prices = serialize($this->old_sale);
					$wpdb->replace( 
						$wpdb->prefix.'postmeta', 
						array( 
							'post_id' => $this->mold_id,
							'meta_key' => 'old_regular_prices', 
							'meta_value' => $old_regular_prices 
						), 
						array( 
							'%d',
							'%s', 
							'%s' 
						) 
					);
					$wpdb->replace( 
						$wpdb->prefix.'postmeta', 
						array( 
							'post_id' => $this->mold_id,
							'meta_key' => 'old_sale_prices', 
							'meta_value' => $old_sale_prices 
						), 
						array( 
							'%d',
							'%s', 
							'%s' 
						) 
					);
					?></p><?php
					// RETURN number of existing variations updated /////////////////
					?><p><b><?php _e( 'Total : ', 'woocommerce-molds' );?></b><?php
					echo $count;
					_e( ' variations were molded.', 'woocommerce-molds' ); ?></p><?php
					$this->applied = "yes";
					$this->pm_save_to_db($this->mold_id);
					echo '</div>';
				} else {
					?><div id="message" class="updated below-h2"><p style="color:red;"><?php _e( 'No variations found.', 'woocommerce-molds' ); ?></p></div><?php
				}
			}
			##//////////////////////////////////////////##
			##############################################
			
			
			#########################
			##// UNSET VARIATION //##
			##/////////////////////##
			protected function pm_unset_variation($variation_id,$reverse) {
				global $wpdb;
			
				// We store what queries return in one number
				$result = 0;
				
				// STOCK
				if(!empty($this->mold_stock)) {
					$result = $this->pm_update_meta_value($variation_id,'_stock',null);
				}
				// SKU
				if(!empty($this->mold_sku)) {
					$result = $this->pm_update_meta_value($variation_id,'_sku','');
				}
				
				// WEIGHT
				if(!empty($this->mold_weight)) {
					$result = $this->pm_update_meta_value($variation_id,'_weight',null);
				}
				// LENGTH
				if(!empty($this->mold_length)) {
					$result = $this->pm_update_meta_value($variation_id,'_length',null);
				}
				// WIDTH
				if(!empty($this->mold_width)) {
					$result += $this->pm_update_meta_value($variation_id,'_width',null);
				}
				// HEIGHT
				if(!empty($this->mold_height)) {
					$result += $this->pm_update_meta_value($variation_id,'_height',null);
				}
				
				// CHECKING IF THE VARIATION ALREADY HAS AN IMAGE
				$variation_image = $wpdb->get_var($wpdb->prepare(
					"SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_thumbnail_id' AND post_id = %d",
					$variation_id
				));
				
				// IF VARIATION ALREADY HAS AN IMAGE
				if(!empty($variation_image)) {
				
					// Check variation image is mold image or not
					$guid = $wpdb->get_var($wpdb->prepare(
						"SELECT guid FROM ".$wpdb->prefix."posts WHERE post_type = 'attachment' AND ID = %d",
						$variation_image
					));
					// If this image is the same as the mold's one we delete it
					if($guid==$this->mold_image) {
						// Reset image post_parent to zero
						$result += $wpdb->update($wpdb->prefix.'posts',array('post_parent' => '%d'),array('ID' => '%d'),array(0),array($variation_image));
						// Nullify thumbnail id
						$result += $this->pm_update_meta_value($variation_id,'_thumbnail_id',null);
					}
				}
				
				
				// IS ACTIVE
				if($this->mold_is_active=="yes") {
					$result += $wpdb->query($wpdb->prepare(
						'UPDATE ".$wpdb->prefix."posts SET post_status = "private" WHERE ID = %d',
						$variation_id
					));
				} else if($this->mold_is_active=="no") {
					if($reverse) {
						$result += $wpdb->query($wpdb->prepare(
							'UPDATE ".$wpdb->prefix."posts SET post_status = "publish" WHERE ID = %d',
							$variation_id
						));
					}
				}
				// IS VIRTUAL
				if($this->mold_is_virtual=="yes") {
					$result += $this->pm_update_meta_value($variation_id,'_virtual',null);
				} else if($this->mold_is_active=="no") {
					if($reverse) {
						$result += $this->pm_update_meta_value($variation_id,'_virtual','yes');
					}
				}
				// IS DOWNLOADABLE
				if($this->mold_is_downloadable=="yes") {
					$result += $this->pm_update_meta_value($variation_id,'_downloadable',null);
					if($this->mold_download_limit==$this->pm_select_meta_value($variation_id,'_download_limit')) {
						$result += $this->pm_update_meta_value($variation_id,'_download_limit',null);
					}
					if($this->mold_download_expiry==$this->pm_select_meta_value($variation_id,'_download_expiry')) {
						$result += $this->pm_update_meta_value($variation_id,'_download_expiry',null);
					}
					if($this->mold_file_paths==$this->pm_select_meta_value($variation_id,'_file_paths')) {
						$result += $this->pm_update_meta_value($variation_id,'_file_paths',null);
					}
				} else if($this->mold_is_downloadable=="no") {
					if($reverse) {
						$result += $this->pm_update_meta_value($variation_id,'_downloadable','yes');
					}
				}
				
				if(!empty($this->mold_regular_price)) {
				
					// REGULAR PRICE : is base price
					if($this->mold_is_base_price=="yes") {
					
						$result += $this->pm_update_meta_value($variation_id,'_regular_price',null);
						
					// REGULAR PRICE : adding or substracting
					} else {
					
						// Get variation regular price, if not equals 0
						$regular_price = $this->old_regular[$variation_id];
						if(empty($regular_price)) {
							$regular_price = 0;
						}
						
						// CHANGING THE REGULAR PRICE TO THE OLD VALUE
						$result += $this->pm_update_meta_value($variation_id,'_regular_price',$regular_price);
					}
				}
				
				if(!empty($this->mold_sale_price)) {
				
					// SALE PRICE : is base price
					if($this->mold_sale_price_is_base_price=="yes") {
					
						$result += $this->pm_update_meta_value($variation_id,'_sale_price',null);
						
					// SALE PRICE : adding or substracting
					} else {
					
						// Get variation sale price, if not equals 0
						$sale_price = $this->old_sale[$variation_id];
						if(empty($sale_price)) {
							$sale_price = 0;
						}
						
						// CHANGING THE SALE PRICE TO THE OLD VALUE
						$result += $this->pm_update_meta_value($variation_id,'_sale_price',$sale_price);
						
					}
				}
				
				// DELETING OLD PRICE DATA //
				$wpdb->delete( $wpdb->prefix.'postmeta', array( 'post_id' => $this->mold_id, 'meta_key' => 'old_regular_prices' ), array( '%d', '%s' ) );
				$wpdb->delete( $wpdb->prefix.'postmeta', array( 'post_id' => $this->mold_id, 'meta_key' => 'old_sale_prices' ), array( '%d', '%s' ) );
				
				// SALE PRICE DATES FROM
				if(!empty($this->mold_sale_price_dates_from)&&($this->mold_sale_price_dates_from==$this->pm_select_meta_value($variation_id,'_sale_price_dates_from'))) {
					$result += $this->pm_update_meta_value($variation_id,'_sale_price_dates_from',null);
				}
				// SALE PRICE DATES TO
				if(!empty($this->mold_sale_price_dates_to)&&($this->mold_sale_price_dates_to==$this->pm_select_meta_value($variation_id,'_sale_price_dates_to'))) {
					$result += $this->pm_update_meta_value($variation_id,'_sale_price_dates_to',null);
				}
				
				// PRICE (price displayed in the shop as the current price)
				$object = $this->pm_select_meta_value($variation_id,'_sale_price');
				$sale_price = intval($object->meta_value);
				// If there is a sale price it is the price
				if(!empty($sale_price)) {
					$result += $this->pm_update_meta_value($variation_id,'_price',$sale_price);
				// If not, regular price is the price
				} else {
					$object = $this->pm_select_meta_value($variation_id,'_regular_price');
					$regular_price = intval($object->meta_value);
					if(!empty($regular_price)) {
						$result += $this->pm_update_meta_value($variation_id,'_price',$regular_price);
					// If we have no price data we do nothing and let the price live its life
					}
				}
				
				// SHIPPING/TAX CLASS
				if(!empty($this->mold_shipping_class)&&($this->mold_shipping_class==$this->pm_select_meta_value($variation_id,'_tax_class'))) {
					$result += $this->pm_update_meta_value($variation_id,'_tax_class',null);
				}
				
				## END #######################################################
				return $result;
				##############################################################
			}
			##/////////////////////##
			#########################
			
			#########################
			##// UNAPPLYING MOLD //##
			##//////////////////////#
			public function pm_unapply_mold($reverse = false) {
				
				// With the product ID we check for already existing variations
				global $wpdb;
				
				// We create the right meta_key for variations
				$attribute = "attribute_".$this->mold_attribute;
			
				// SPECIFIC TERM(s) GIVEN
				if($this->mold_terms!='all') {
					// We manipulate the terms array to adjust to SQL syntax
					$mold_terms = explode(',',$this->mold_terms);
					
					// WE MUST USE THE SLUG INSTEAD OF THE TERMS NAMES //
					$slug_terms = array();
					foreach($mold_terms as $term) {
						$slug = $wpdb->get_var($wpdb->prepare(
							"SELECT slug FROM ".$wpdb->prefix."terms WHERE name = %s",
							$term
						));
						$slug_terms[] = $slug;
					}
					$terms = "'" . implode("','", $slug_terms) . "'";
					
					## FIND VARIATIONS
					// We select the IDs of children variations of product_id, that are using mold_attribute and one or several of the mold_terms
					$query = "SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key = '".$attribute."' AND meta_value IN (".$terms.") AND post_id IN (SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type = 'product_variation' AND post_parent = ".$this->product_id.")";
					$product_variations = $wpdb->get_results($query);
				
				// ANY VARIATION CONCERNED BY THE ATTRIBUTE
				} else {
				
					## FIND VARIATIONS
					// We select the IDs of children variations of product_id, that are using mold_attribute and one or several of the mold_terms
					$query = "SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key = '".$attribute."' AND post_id IN (SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type = 'product_variation' AND post_parent = ".$this->product_id.")";
					$product_variations = $wpdb->get_results($query);
					
				}
			
				// IF $PRODUCT_VARIATIONS IS NOT EMPTY
				if(!empty($product_variations)) {
				
					// LOADING THE OLD PRICES //
					$old_regular = $wpdb->get_var($wpdb->prepare(
							"SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE post_id = %d AND meta_key = %s",
							$this->mold_id, 'old_regular_prices'
						));
					if(!empty($old_regular)) {
						$this->old_regular = unserialize($old_regular);
					}
					$old_sale = $wpdb->get_var($wpdb->prepare(
							"SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE post_id = %d AND meta_key = %s",
							$this->mold_id, 'old_sale_prices'
						));
					if(!empty($old_sale)) {
						$this->old_sale = unserialize($old_sale);
					}
					//////////////////////////
					
					?><div id="message" class="updated below-h2"><?php
					$count = 0;
					?><p style="color:GoldenRod;line-height:12px;"><?php
					foreach($product_variations as $variation) {
						## UPDATE VARIATIONS
						// We turn the mold data into variation data
						$result = $this->pm_unset_variation($variation->post_id,$reverse);
						
						// And count one variation more done #### NEEDS RETURN TRUE OR FALSE VALUE TO CHECK IF VARIATION DONE OR NOT
						if($result>0) {
							echo '#';
							echo $variation->post_id;
							echo ' [ ';
							echo $result;
							echo ' ] - ';
							$count++;
						} else if($result<=0) {
							?><b style="font-weight:normal;color:darkOrange;line-height:10px;"><?php _e( 'No update on #', 'woocommerce-molds' );
							echo $variation->post_id; 
							echo ' - '; ?></b><?php
						}
					}
					?></p><?php
					// RETURN number of existing variations updated /////////////////
					?><p><b><?php _e( 'Total : ', 'woocommerce-molds' );?></b><?php
					echo $count;
					_e( ' variations were unmolded.', 'woocommerce-molds' ); ?></p><?php
					$this->applied = "no";
					$this->pm_save_to_db($this->mold_id);
					echo '</div>';
				} else { 
					?><div id="message" class="updated below-h2"><p style="color:red;"><?php _e( 'No variations found.', 'woocommerce-molds' ); ?></p></div><?php
				}
			}
			##//////////////////////#
			#########################
		}
	}

}