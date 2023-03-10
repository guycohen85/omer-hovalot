INSERT IGNORE INTO `#__virtuemart_calcs` (`virtuemart_calc_id`, `virtuemart_vendor_id`, `calc_jplugin_id`, `calc_name`, `calc_descr`, `calc_kind`, `calc_value_mathop`, `calc_value`, `calc_currency`, `calc_shopper_published`, `calc_vendor_published`, `publish_up`, `publish_down`, `for_override`, `calc_params`, `ordering`, `shared`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
	(1, 1, 0, 'Tax 20%', '', 'VatTax', '+%', 20.0000, 47, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', 0, 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(2, 1, 0, 'Discount 5% per bill', '', 'DATaxBill', '-%', 5.0000, 47, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', 0, 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0);

INSERT IGNORE INTO `#__virtuemart_categories` (`virtuemart_category_id`, `virtuemart_vendor_id`, `category_template`, `category_layout`, `category_product_layout`, `products_per_row`, `limit_list_step`, `limit_list_initial`, `hits`, `metarobot`, `metaauthor`, `ordering`, `shared`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
	(1, 1, '0', '0', '0', 0, '0', 0, 0, '', '', 1, 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(2, 1, '0', '0', '0', 0, '0', 0, 0, '', '', 2, 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(3, 1, '0', '0', '0', 0, '0', 0, 0, '', '', 3, 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(4, 1, '0', '0', '0', 0, '0', 0, 0, '', '', 4, 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(5, 1, '0', '0', '0', 0, '0', 0, 0, '', '', 1, 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(6, 1, '0', '0', '0', 0, '0', 0, 0, '', '', 2, 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(7, 1, '0', '0', '0', 0, '0', 0, 0, '', '', 5, 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(8, 1, '0', '0', '0', 0, '0', 0, 0, '', '', 1, 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(9, 1, '0', '0', '0', 0, '0', 0, 0, '', '', 1, 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0);

INSERT IGNORE INTO `#__virtuemart_categories_XLANG` (`virtuemart_category_id`, `category_name`, `category_description`, `metadesc`, `metakey`, `customtitle`, `slug`) VALUES
	(1, 'Default Products', '<p>Sample of several default products. You will find settings displayed.</p>', '', '', '', 'default-products'),
	(2, 'Default Pattern', '<p><span style="background-color: #FCDB73; text-align: center;padding: 5px 40px; ">Example for usage of product pattern. For showcase reason the PATTERN is NOT unpublished.</span></p>', '', '', '', 'default-pattern'),
	(3, 'Pagination testarea', '<p><span style="background-color: #FCDB73; text-align: center;padding: 5px 40px;">Notice: for correct ordering in product view set valid ordering in BE.</span><br />Ordering showcase category. Use this category to test the ordering of products. You can also select several Manufacturer.</p>', '', '', '', 'pagination-testarea'),
	(4, 'Headgear', '<p><span style="background-color: #FCDB73; text-align: center;padding: 5px 40px;">Showcase for subcategory with several sample product.</span></p>', '', '', '', 'headgear'),
	(5, 'Hats', '<p><span style="background-color: #FCDB73; text-align: center;padding: 5px 40px;">Example for usage of product pattern. For showcase reason the PATTERN is NOT unpublished.</span><br />Sample for product category. Create new category in VM BE > <em>Product Categories</em> > <em>New</em></p>', '', '', '', 'hats'),
	(6, 'Caps', '<p><span style="background-color: #FCDB73; text-align: center;padding: 5px 40px;">Example for usage of product pattern. For showcase reason the PATTERN is NOT unpublished.</span><br />Sample for product category. Create new category in VM BE > <em>Product Categories</em> > <em>New</em></p>', '', '', '', 'caps'),
	(7, 'Clothing', '', '', '', '', 'clothing'),
	(8, 'Mister', '<p>Sample for Subcategory. <br />Select superordinated category in VM BE > <em>Product Categories</em> > Your Category in section <em>Details > Category Ordering </em></p>', '', '', '', 'mister'),
	(9, 'Ladies', '<p>Sample for Subcategory. <br />Select superordinated category in VM BE > <em>Product Categories</em> > Your Category in section <em>Details > Category Ordering </em></p>', '', '', '', 'ladies');

INSERT IGNORE INTO `#__virtuemart_category_categories` (`id`, `category_parent_id`, `category_child_id`, `ordering`) VALUES
	(1, 0, 1, 0),
	(2, 0, 2, 0),
	(3, 0, 3, 0),
	(4, 0, 4, 0),
	(5, 4, 5, 5),
	(6, 4, 6, 6),
	(7, 0, 7, 1),
	(8, 7, 8, 1),
	(9, 7, 9, 1);

INSERT IGNORE INTO `#__virtuemart_category_medias` (`id`, `virtuemart_category_id`, `virtuemart_media_id`, `ordering`) VALUES
	(1, 1, 2, 1),
	(2, 2, 2, 1),
	(3, 3, 2, 1),
	(4, 4, 3, 1),
	(8, 7, 6, 1),
	(6, 6, 5, 1),
	(7, 5, 3, 1),
	(9, 8, 6, 1),
	(10, 9, 7, 1);

INSERT IGNORE INTO `#__virtuemart_coupons` (`virtuemart_coupon_id`, `coupon_code`, `percent_or_total`, `coupon_type`, `coupon_value`, `coupon_start_date`, `coupon_expiry_date`, `coupon_value_valid`, `coupon_used`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
	(1, 'Sample Coupon', 'total', 'permanent', 0.01000, '0000-00-00 00:00:00', '2014-02-14 16:12:02', 0.00000, '0', 1, '2014-02-13 16:12:45', 635, '2014-02-13 16:12:45', 635, '0000-00-00 00:00:00', 0);

INSERT IGNORE INTO `#__virtuemart_customs` (`virtuemart_custom_id`, `custom_parent_id`, `virtuemart_vendor_id`, `custom_jplugin_id`, `custom_element`, `admin_only`, `custom_title`, `show_title`, `custom_tip`, `custom_value`, `is_input`, `custom_desc`, `field_type`, `is_list`, `is_hidden`, `is_cart_attribute`, `layout_pos`, `custom_params`, `shared`, `published`, `created_on`, `created_by`, `ordering`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
	(1, 0, 1, 0, '', 0, 'COM_VIRTUEMART_RELATED_PRODUCTS', 1, 'COM_VIRTUEMART_RELATED_PRODUCTS_TIP', '', 0, 'COM_VIRTUEMART_RELATED_PRODUCTS_DESC', 'R', 0, 0, 0, 'related_products', NULL, 0, 1, '2011-05-25 21:52:43', 62, 0, '2011-05-25 21:52:43', 62, '0000-00-00 00:00:00', 0),
	(2, 0, 1, 0, '', 0, 'COM_VIRTUEMART_RELATED_CATEGORIES', 1, 'COM_VIRTUEMART_RELATED_CATEGORIES_TIP', NULL, 0, 'COM_VIRTUEMART_RELATED_CATEGORIES_DESC', 'Z', 0, 0, 0, 'related_categories', NULL, 0, 1, '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(3, 0, 1, 0, '0', 0, 'String', 1, '', '', 0, '', 'S', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 635, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(4, 0, 1, 0, '0', 0, 'Textarea', 1, '', '', 0, '', 'Y', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 635, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(5, 0, 1, 0, '0', 0, 'Group1', 1, '', '', 0, '', 'G', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 635, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(6, 5, 1, 0, '0', 0, 'Group1 String 1', 1, '', '', 0, '', 'S', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 635, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(7, 5, 1, 0, '0', 0, 'Group1 String 2', 1, '', '', 0, '', 'S', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 635, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(8, 5, 1, 0, '0', 0, 'Group1 Textarea' , 1, '', '', 0, '', 'Y', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 635, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(9, 0, 1, 0, '0', 0, 'Cart Variant', 1, '', '', 1, '', 'S', 0, 0, 1, 'addtocart', '0', 0, 1, '0000-00-00 00:00:00', 635, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(10, 0, 1, 0, '0', 0, 'Child Variant', 1, '', '', 0, '', 'A', 0, 0, 1, 'addtocart', '0', 0, 1, '0000-00-00 00:00:00', 635, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(11, 0, 1, 0, '0', 0, 'Caps Group', 1, '', '', 0, '', 'G', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 635, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(12, 0, 1, 0, '0', 0, 'Cap Size', 1, '', '', 1, '', 'S', 0, 0, 1, 'addtocart', '0', 0, 1, '0000-00-00 00:00:00', 635, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(13, 11, 1, 0, '0', 0, 'Caps Group Details', 1, '', '', 0, '', 'S', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 635, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(14, 11, 1, 0, '0', 0, 'Caps Group Components', 1, '', '', 0, '', 'S', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 635, 2, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(15, 0, 1, 0, '0', 0, 'Clothing weave', 1, '', '', 1, '', 'S', 0, 0, 1, 'addtocart', '0', 0, 1, '0000-00-00 00:00:00', 635, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(16, 0, 1, 0, '0', 0, 'Clothing size', 1, '', '', 1, '', 'S', 0, 0, 1, 'addtocart', '0', 0, 1, '0000-00-00 00:00:00', 635, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(17, 0, 1, 0, '0', 0, 'Clothing', 1, '', '', 0, '', 'G', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 635, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(18, 17, 1, 0, '0', 0, 'Clothing Composit', 1, '', '', 0, '', 'S', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 635, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(19, 17, 1, 0, '0', 0, 'Clothing textarea', 1, '', '', 0, '', 'Y', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 635, 2, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0);

INSERT IGNORE INTO `#__virtuemart_manufacturercategories` (`virtuemart_manufacturercategories_id`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
	(1, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0);
	
INSERT IGNORE INTO `#__virtuemart_manufacturercategories_XLANG` (`virtuemart_manufacturercategories_id`, `mf_category_name`, `mf_category_desc`, `slug`) VALUES
	(1, 'default', 'This is the default manufacturer category ', 'default');

INSERT IGNORE INTO `#__virtuemart_manufacturers` (`virtuemart_manufacturer_id`, `virtuemart_manufacturercategories_id`, `hits`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
	(1, 1, 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(2, 1, 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(3, 1, 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0);

INSERT IGNORE INTO `#__virtuemart_manufacturers_XLANG` (`virtuemart_manufacturer_id`, `mf_name`, `mf_email`, `mf_desc`, `mf_url`, `slug`) VALUES
	(1, 'Manufacturer', 'manufacturer@example.org', '<p>An example for a manufacturer</p>', 'http://www.example.org', 'manufacturer'),
	(2, 'Default', 'example@manufacturer.net', '<p>Default manufacturer</p>', 'example.manufacturer.net', 'default'),
	(3, 'Producer', 'info@producer.com', '<p>An example for another manufacturer.</p>', 'producer.com', 'producer');

INSERT IGNORE INTO `#__virtuemart_manufacturer_medias` (`id`, `virtuemart_manufacturer_id`, `virtuemart_media_id`, `ordering`) VALUES
	(1, 1, 9, 1),
	(2, 2, 9, 1),
	(3, 3, 9, 1);

INSERT IGNORE INTO `#__virtuemart_medias` (`virtuemart_media_id`, `virtuemart_vendor_id`, `file_title`, `file_description`, `file_meta`, `file_mimetype`, `file_type`, `file_url`, `file_url_thumb`, `file_is_product_image`, `file_is_downloadable`, `file_is_forSale`, `file_params`, `file_lang`, `shared`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
	(1, 1, 'vendor.gif', '', '', 'image/gif', 'vendor', 'images/stories/virtuemart/vendor/vendor.gif', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(2, 1, 'student_hat_16.jpg', '', '', 'image/jpeg', 'category', 'images/stories/virtuemart/category/student_hat_16.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(3, 1, 'hat_category8.jpg', '', '', 'image/jpeg', 'category', 'images/stories/virtuemart/category/hat_category8.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(5, 1, 'cap6.jpg', '', '', 'image/jpeg', 'category', 'images/stories/virtuemart/category/cap6.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(6, 1, 'jacket_classic7.jpg', '', '', 'image/jpeg', 'category', 'images/stories/virtuemart/category/jacket_classic7.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(7, 1, 'black_dress_2.jpg', '', '', 'image/jpeg', 'category', 'images/stories/virtuemart/category/black_dress_2.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(8, 1, 'cart_logo.jpg', '', '', 'image/jpeg', 'product', 'images/stories/virtuemart/product/cart_logo.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(9, 1, 'manufacturer.jpg', '', '', 'image/jpeg', 'manufacturer', 'images/stories/virtuemart/manufacturer/manufacturer.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(10, 1, 'classic_hat.jpg', '', '', 'image/jpeg', 'product', 'images/stories/virtuemart/product/classic_hat.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(11, 1, 'cowboy_hat.jpg', '', '', 'image/jpeg', 'product', 'images/stories/virtuemart/product/cowboy_hat.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(12, 1, 'derbyhat.jpg', '', '', 'image/jpeg', 'product', 'images/stories/virtuemart/product/derbyhat.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(13, 1, 'santa_cap.jpg', '', '', 'image/jpeg', 'product', 'images/stories/virtuemart/product/santa_cap.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(14, 1, 'baseballcap.jpg', '', '', 'image/jpeg', 'product', 'images/stories/virtuemart/product/baseballcap.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(15, 1, 'marinecap.jpg', '', '', 'image/jpeg', 'product', 'images/stories/virtuemart/product/marinecap.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(16, 1, 'jumper.jpg', '', '', 'image/jpeg', 'product', 'images/stories/virtuemart/product/jumper.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(17, 1, 'wide_dress_2.jpg', '', '', 'image/jpeg', 'product', 'images/stories/virtuemart/product/wide_dress_2.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(18, 1, 'jacket_classic.jpg', '', '', 'image/jpeg', 'product', 'images/stories/virtuemart/product/jacket_classic.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(19, 1, 'poncho.jpg', '', '', 'image/jpeg', 'product', 'images/stories/virtuemart/product/poncho.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(20, 1, 'dress.jpg', '', '', 'image/jpeg', 'product', 'images/stories/virtuemart/product/dress.jpg', '', 0, 0, 0, '', '', 0, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0);

INSERT IGNORE INTO `#__virtuemart_products` (`virtuemart_product_id`, `virtuemart_vendor_id`, `product_parent_id`, `product_sku`, `product_gtin`, `product_mpn`, `product_weight`, `product_weight_uom`, `product_length`, `product_width`, `product_height`, `product_lwh_uom`, `product_url`, `product_in_stock`, `product_ordered`, `low_stock_notification`, `product_available_date`, `product_availability`, `product_special`, `product_sales`, `product_unit`, `product_packaging`, `product_params`, `hits`, `intnotes`, `metarobot`, `metaauthor`, `layout`, `published`, `pordering`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
	(1, 1, 0, 'DP1', NULL, NULL, 50.0000, 'KG', 45.0000, 5.0000, 5.0000, 'M', '', 78, 0, 5, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(2, 1, 0, 'DP2', NULL, NULL, 15.0000, 'KG', 10.0000, 25.0000, 10.0000, 'M', '', 10, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', 0.1000, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(3, 1, 0, 'DP3', NULL, NULL, 0.1000, 'KG', 0.0100, 0.0100, 0.0300, 'M', '', 55, 0, 10, '0000-00-00 00:00:00', '', 0, 0, 'KG', 1.0000, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(4, 1, 0, 'DP4', NULL, NULL, 1.0000, 'KG', 0.2000, 0.1000, 0.3000, 'M', '', 100, 0, 5, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(5, 1, 0, 'DP5', NULL, NULL, 0.1000, 'KG', 0.2000, 0.0100, 0.0300, 'M', '', 10, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(6, 1, 0, 'DP6', NULL, NULL, 0.1000, 'KG', 0.2000, 0.0100, 0.3000, 'M', '', 50, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(7, 1, 0, 'DPP1', NULL, NULL, 0.4000, 'KG', 0.1000, 0.2000, 0.3000, 'M', '', 80, 0, 10, '0000-00-00 00:00:00', '', 0, 0, 'KG', 0.1000, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box="1"|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(8, 1, 7, 'DPP2', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(9, 1, 7, 'DPP3', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 2, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(10, 1, 7, 'DPP4', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 3, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(11, 1, 0, 'DPP5', NULL, NULL, 0.4000, 'KG', 0.1000, 0.2000, 0.3000, 'M', '', 150, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(12, 1, 11, 'DPP6', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(13, 1, 11, 'DPP7', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 2, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(14, 1, 11, 'DPP8', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 3, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(15, 1, 0, '001', NULL, NULL, 0.1000, 'KG', 0.1000, 0.2000, 0.3000, 'M', '', 100, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(16, 1, 15, '002', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(17, 1, 15, '003', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 2, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(18, 1, 15, '004', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 3, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(19, 1, 0, '005', NULL, NULL, 0.4000, 'KG', 0.1000, 0.2000, 0.3000, 'M', '', 100, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box="1"|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(20, 1, 19, '006', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(21, 1, 19, '007', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(22, 1, 19, '008', NULL, NULL, 4.0000, 'KG', 1.0000, 2.0000, 3.0000, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box="10"|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(23, 1, 0, '009', NULL, NULL, 0.4000, 'KG', 0.1000, 0.2000, 0.3000, 'M', '', 80, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box="1"|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(24, 1, 23, '010', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(25, 1, 23, '011', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 2, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(26, 1, 23, '012', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box="10"|', NULL, '', '', '', '0', 1, 3, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(27, 1, 0, '013', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(28, 1, 27, '014', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(29, 1, 27, '015', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(30, 1, 27, '016', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(31, 1, 27, '017', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(32, 1, 27, '018', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(33, 1, 27, '019', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(34, 1, 27, '020', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(35, 1, 27, '021', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(36, 1, 27, '022', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(37, 1, 27, '023', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(38, 1, 0, '024', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(39, 1, 38, '025', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(40, 1, 38, '026', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(41, 1, 38, '027', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(42, 1, 38, '028', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(43, 1, 38, '029', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(44, 1, 38, '030', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(45, 1, 38, '031', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(46, 1, 38, '032', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(47, 1, 38, '033', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(48, 1, 38, '034', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(49, 1, 0, '035', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(50, 1, 49, '036', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(51, 1, 49, '037', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(52, 1, 49, '038', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(53, 1, 49, '039', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(54, 1, 49, '040', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(55, 1, 49, '041', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(56, 1, 49, '042', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(57, 1, 49, '043', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(58, 1, 49, '044', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(59, 1, 49, '045', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(60, 1, 0, '046', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(61, 1, 60, 'PCML', NULL, NULL, 125.0000, 'G', 20.0000, 20.0000, 10.0000, 'CM', '', 35, 2, 5, '0000-00-00 00:00:00', '', 0, 0, '100G', 0.5000, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box="1"|', NULL, '', '', '', '0', 1, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(62, 1, 60, 'TPCM', NULL, NULL, 150.0000, 'G', 35.0000, 30.0000, 15.0000, 'CM', '', 15, 1, 5, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box="1"|', NULL, '', '', '', '0', 1, 2, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(63, 1, 60, 'XSF', NULL, NULL, 200.0000, 'G', 25.0000, 25.0000, 25.0000, 'CM', '', 122, 2, 10, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box="1"|', NULL, '', '', '', '0', 1, 3, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(64, 1, 0, 'SR5R', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(65, 1, 64, 'AA1A', NULL, NULL, 0.1000, 'KG', 25.0000, 20.0000, 2.0000, 'CM', '', 77, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', 1.0000, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(66, 1, 64, 'ERT', NULL, NULL, 0.0750, 'KG', 0.2000, 0.2000, 0.1500, 'M', '', 152, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box="1"|', NULL, '', '', '', '0', 1, 2, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(67, 1, 64, 'WER', NULL, NULL, 150.0000, 'G', 25.0000, 25.0000, 15.0000, 'CM', '', 50, 0, 5, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box="1"|', NULL, '', '', '', '0', 1, 3, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(68, 1, 0, 'QWE', NULL, NULL, NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(69, 1, 68, 'ZUI', NULL, NULL, 100.0000, 'G', NULL, NULL, NULL, 'M', '', 45, 0, 2, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 1, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(70, 1, 68, 'NMH', NULL, NULL, 100.0000, 'G', NULL, NULL, NULL, 'M', '', 12, 0, 1, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 2, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(71, 1, 68, 'VGB', NULL, NULL, 100.0000, 'G', NULL, NULL, NULL, 'M', '', 15, 0, 0, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 3, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(72, 1, 68, 'SRF', NULL, NULL, 100.0000, 'G', NULL, NULL, NULL, 'M', '', 45, 0, 5, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 4, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(73, 1, 68, 'ASD', NULL, NULL, 100.0000, 'G', NULL, NULL, NULL, 'M', '', 54, 0, 5, '0000-00-00 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 5, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0);

INSERT IGNORE INTO `#__virtuemart_products_XLANG` (`virtuemart_product_id`, `product_s_desc`, `product_desc`, `product_name`, `metadesc`, `metakey`, `customtitle`, `slug`) VALUES
	(1, 'This is a default product.', '<p>Default product with standart settings no customfields. You can set:</p>\r\n<p>Tab Product Information<br />- General: Published, On Featured, Product SKU, Product Name, Produkt alias, URL, <br />- Assignation:Manufacturer, Product Categories, Shopper Groups, Type of Product detail page<br />- Product pricing: Cost price, Base price, Final price, Override, and priceranges dependant on Shopper group.<br />- You can add Child products here also.</p>\r\n<p>Tab Product Description<br />- Description, Short description, Meta information</p>\r\n<p>Tab Product Status<br />- Stock amount, Low Stock notification, Minimum and maximum purchase quantity, Availability Date + image<br />- Booked, ordered products amount, Quantity Steps<br />- Also it is possible to send email to shopper who bought this product.</p>\r\n<p>Tab Dimension and Weight<br />- Lenght, Width, Height, Weight, Packing, and Units in Box</p>\r\n<p>Tab Product images<br />- Use already uploaded images<br />- Set image information<br />- Upload new image<br />- manage thumbnail</p>\r\n<p>Tab Custom Fields<br />- Set related Categories &amp; Products<br />- Select customfields</p>\r\n<p>??</p>\r\n<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</p>\r\n<p>At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>\r\n<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.???? <br /><br /></p>', 'Default product', '', '', '', 'default-product'),
	(2, 'It\'s a free Product!', '<p>This product shows how a free product is set up. The shopper can purchase without beeing charged. In all cases the shopper needs to checkout.</p>\r\n<p>It can be used e.g. if you want to offer catalogues or sample products.</p>', 'Free product', '', '', '', 'free-product'),
	(3, 'Default product with customfield string.', '<p>This is a default product with standart settings and a customfield type string. You can set:<br /> - Title (text)<br />- Show title (select)<br />- Published (select)<br />- Select parent customfield (for building a pattern of multiple customfields at once) (select)<br />- Cart Attribut (select)<br />- Description (text)<br />- Default (text)<br />- Tooltip (text)<br />- Layout position<br />- Admin only (select)<br />- Is a list (select)<br />- Hidden (select)</p>', 'Product w/customfield string', '', '', '', 'product-w-customfield-string'),
	(4, 'This is a default product with customfield textarea.', '<p>Default product with one customfield textarea you see this customfield content below.</p>\r\n<p>You can use customfield textarea to add further informating apart from product description.</p>\r\n<p>??</p>', 'Product w/customfield textarea', '', '', '', 'product-w-customfield-textarea'),
	(5, 'This is a default product with standart settings and a customfield type parent.', '<p>You can set use customfield type parent to bind multiple child customfields into a parental bundle.<br /><br />For example for books, you may wish to always give the following description:</p>\r\n<p>Reading level: Ages 9-12<br />Hardcover: 224 pages<br />Publisher: Amulet Books (November 15, 2011)<br />Language: English<br />ISBN-10: 1419702238<br />ISBN-13: 978-1419702235</p>\r\n<p>Therefore bind multiple customfields e.g. string into a parent customfield to use it as a pattern.</p>', 'Product w/customfield parent', '', '', '', 'product-w-customfield-parent'),
	(6, 'Default product with cart variants modifies price.', '<p>Custom Field with Cart Attribute allows you to add some options to a product that can modify the product price. For example, you may wish to sell a book, or the downladable version of it. And the price is different in both case.<br /><br />In this case you can select different variants. Default, Variant A,B, or C. The price will be modified by selection. In VM configuration you can also select either tax should be applied to cart variant in customfield.</p>', 'Product w/cart variant', '', '', '', 'product-w-cart-variant'),
	(7, 'Product with child variant; parent ordable', '<p>This product will explain the usage of customfield generic (dynamic) child variant. The base product is ordable in this case.</p>\r\n<p>Consider you sell products in different color settings: you want to change the color of the product by selecting a color variant.<br />Therefore dynamic child variants can be used to allow you different description, images, or product status for every variant of your base product.<br /><br />Set up a new product, set price, add child products. Add created customfield generic child variant. <br />The child products are assigned to another unpublished category as the parent product for calculation or llike in this case to no category.<br />Tick the checkboxes <em>Display parent as option</em>.<br /><br /></p>', 'Product w/child variant', '', '', '', 'product-w-child-variant'),
	(8, '', '', 'child variant 1', '', '', '', 'child-variant-1'),
	(9, '', '', 'child variant 2', '', '', '', 'child-variant-2'),
	(10, '', '', 'child variant 3', '', '', '', 'child-variant-3'),
	(11, 'Product with child variant; parent not ordable', '<p>This product will explain the usage of customfield generic (dynamic) child variant. The base product is not ordable in this case.</p>\r\n<p>Consider you sell products in different color settings: you want to change the color of the product by selecting a color variant.<br />Therefore dynamic child variants can be used to allow you different description, images, or product status for every variant of your base product.<br /><br />Set up a new product, set price, add child products. Add created customfield generic child variant. <br />The child products are assigned to another category as the parent product for caclulation or like in this case to no category.<br />Do not tick the checkbox <em>Display parent as option in this case</em>.</p>', 'Product w/child variant parent not ordable', '', '', '', 'product-w-child-variant-parent-not-ordable'),
	(12, '', '', 'Child variant 1 15???', '', '', '', 'child-variant-1-15'),
	(13, '', '', 'Child variant 1 20???', '', '', '', 'child-variant-1-20'),
	(14, '', '', 'Child variant 1 25???', '', '', '', 'child-variant-1-25'),
	(15, 'Default product with child variant and cart variant.', '<p>This product is a showcase to present the combination of product price, child variant price, and cart variant price.</p>', 'Product w/child variant w/cart variant', '', '', '', 'product-w-child-variant-w-cart-variant'),
	(16, '', '', 'child variant w/cart variant 1', '', '', '', 'child-variant-w-cart-variant-1'),
	(17, '', '', 'child variant w/cart variant 2', '', '', '', 'child-variant-w-cart-variant-2'),
	(18, '', '', 'child variant w/cart variant 3', '', '', '', 'child-variant-w-cart-variant-3'),
	(19, 'Showcase for pattern usage.', '<p>This product is used as a pattern for other products. It is a parent product and has multiple child products. <br />You can set several settings (content, customfields) for parent product. Childs of this parent will basically have the same settings as the parent automatically inherite until you overwrite.<br /><br /></p>\r\n<p>In this case product price is set in pattern.</p>', 'Basic PATTERN', '', '', '', 'basic-pattern'),
	(20, 'This is a basic child of Product PATTERN.', '<p>This is a basic child of Product PATTERN. You see inherited settings, only Product description is overwritten.<br /><span style="background-color: #FCDB73; text-align: center;padding: 5px 40px;">Basically Manufacturer and Category are not inherited, for showcase reason both are set in this case.</span></p>', 'Basic child', '', '', '', 'basic-pattern197'),
	(21, 'This is a basic child of Product PATTERN. You see inherited settings.', '<p>This is a basic child of Product PATTERN. You see inherited settings. <br />Overwritten are following setting/content:<br />- Product desc<br />- Product price<br /><span style="background-color: #FCDB73; text-align: center;padding: 5px 40px;">Basically Manufacturer and Category are not inherited, for showcase reason both are set in this case.</span></p>', 'Basic price overwrite', '', '', '', 'basic-price-overwrite'),
	(22, 'Multiple overwrites short desc.', '<p>This is a child of Product PATTERN. Most inherited settings are overwritten: <br />- Short desc<br />- Product desc<br />- Product price<br />- Product Images<br />- Product Dimension and Weight (Units in Box)<br /><span style="background-color: #FCDB73; text-align: center;padding: 5px 40px;"><br />Manufacturer and Category are not inherited.</span></p>', 'Basic multiple overwrites', '', '', '', 'basic-multiple-overwrites'),
	(23, 'Showcase advanced pattern usage.', '<p>This product is used as a pattern for other products. It is a parent product and has multiple child products. <br />You can set several settings (content, customfields) for parent product. Childs of this parent will basically have the same settings as the parent automatically inherite until you overwrite.</p>\r\n<p>One of the hugest advantages is stock control ability.</p>\r\n<p>??</p>', 'Advanced PATTERN', '', '', '', 'advanced-pattern'),
	(24, '', '<p>This is a basic child of Product PATTERN. You see inherited settings, only Product description is overwritten.<br /><span style="background-color: #FCDB73; text-align: center;padding: 5px 40px;">Basically Manufacturer and Category are not inherited, for showcase reason both are set in this case.</span></p>', 'Advanced child', '', '', '', 'advanced-child'),
	(25, '', '<p>This is a advanced child of Advanced PATTERN. You see inherited settings. <br />Overwritten are following setting/content:<br />- Product desc<br />- Product price<br /><span style="background-color: #FCDB73; text-align: center;padding: 5px 40px;">Basically Manufacturer and Category are not inherited, for showcase reason both are set in this case.</span></p>', 'Advanced price overwrite', '', '', '', 'advanced-price-overwrite'),
	(26, 'Advanced multiple overrides', '<p>This is a child of Product PATTERN. Most inherited settings are overwritten: <br />- Short desc<br />- Product desc<br />- Product price<br />- Product Images<br />- Product Dimension and Weight (Units in Box)<br />- Customfields<br /><span style="background-color: #FCDB73; text-align: center;padding: 5px 40px;"><br />Manufacturer and Category are not inherited</span></p>', 'Advanced multiple overrides', '', '', '', 'advanced-multiple-overrides'),
	(27, '', '', '3- Product 1st PATTERN', '', '', '', '3-product-1st-pattern'),
	(28, '', '', '2- 1st pattern CHILD 1', '', '', '', '1-product-1st-pattern279'),
	(29, '', '', '7- 1st pattern CHILD 2', '', '', '', '7-1st-pattern-child-2'),
	(30, '', '', '5- 1st pattern CHILD 3', '', '', '', '5-1st-pattern-child-3'),
	(31, '', '', '4- 1st pattern CHILD 4', '', '', '', '4-1st-pattern-child-4'),
	(32, '', '', '1st pattern CHILD 5', '', '', '', '1-product-1st-pattern278'),
	(33, '', '', '32- 1st pattern CHILD 6', '', '', '', '32-1st-pattern-child-6'),
	(34, '', '', '25- 1st pattern CHILD 7', '', '', '', '25-1st-pattern-child-7'),
	(35, '', '', '24- 1st pattern CHILD 8', '', '', '', '24-1st-pattern-child-8'),
	(36, '', '', '27- 1st pattern CHILD 9', '', '', '', '27-1st-pattern-child-9'),
	(37, '', '', '28- 1st pattern CHILD 10', '', '', '', '28-1st-pattern-child-10'),
	(38, '', '', '8- Product 2st PATTERN', '', '', '', '8-product-2st-pattern'),
	(39, '', '', '2nd pattern CHILD 1', '', '', '', 'product-2st-pattern388'),
	(40, '', '', '15- 2nd pattern CHILD 2', '', '', '', '15-2nd-pattern-child-2'),
	(41, '', '', '30- 2nd pattern CHILD 3', '', '', '', '30-2nd-pattern-child-3'),
	(42, '', '', '17- 2nd pattern CHILD 4', '', '', '', '17-2nd-pattern-child-4'),
	(43, '', '', '16- 2nd pattern CHILD 5', '', '', '', '16-2nd-pattern-child-5'),
	(44, '', '', '22- 2nd pattern CHILD 6', '', '', '', '22-2nd-pattern-child-6'),
	(45, '', '', '23- 2nd pattern CHILD 7', '', '', '', '23-2nd-pattern-child-7'),
	(46, '', '', '21- 2nd pattern CHILD 8', '', '', '', '21-2nd-pattern-child-8'),
	(47, '', '', '18- 2nd pattern CHILD 9', '', '', '', '18-2nd-pattern-child-9'),
	(48, '', '', '33- 2nd pattern CHILD 10', '', '', '', '33-2nd-pattern-child-10'),
	(49, '', '', '20- Product 3rd PATTERN', '', '', '', '20-product-3rd-pattern'),
	(50, '', '', '3rd pattern child 1', '', '', '', 'product-3rd-pattern491'),
	(51, '', '', '3rd pattern child 2', '', '', '', 'product-3rd-pattern491-1'),
	(52, '', '', '3rd pattern child 3', '', '', '', 'product-3rd-pattern496'),
	(53, '', '', '3rd pattern child 4', '', '', '', 'product-3rd-pattern491-2'),
	(54, '', '', '26- 3rd pattern child 5', '', '', '', '26-3rd-pattern-child-5'),
	(55, '', '', '3rd pattern child 6', '', '', '', 'product-3rd-pattern496-1'),
	(56, '', '', '31- 3rd pattern child 7', '', '', '', '31-3rd-pattern-child-7'),
	(57, '', '', '3rd pattern child 8', '', '', '', 'product-3rd-pattern497-1'),
	(58, '', '', '29- 3rd pattern child 9', '', '', '', '29-3rd-pattern-child-9'),
	(59, '', '', '3rd pattern child 10', '', '', '', 'product-3rd-pattern492'),
	(60, '', '', 'PATTERN Hats', '', '', '', 'pattern-hats'),
	(61, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>', 'Classic Hat', '', '', '', 'classic-hat'),
	(62, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>', 'Cowboy Hat', '', '', '', 'cowboy-hat'),
	(63, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>', 'Derby Hat', '', '', '', 'derby-hat'),
	(64, '', '', 'PATTERN Caps', '', '', '', 'pattern-caps'),
	(65, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.', '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>', 'Santa Cap', '', '', '', 'santa-cap'),
	(66, 'Base lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>', 'Baseball Cap', '', '', '', 'baseball-cap'),
	(67, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>', 'Marine Cap', '', '', '', 'marine-cap'),
	(68, 'Pattern for Clothing. For showcase reason this pattern is NOT unpublished.', '<p>For showcase reason this pattern is NOT unpublished.</p>', 'PATTERN Clothing', '', '', '', 'pattern-outer-garments'),
	(69, '', '', 'Jumper', '', '', '', 'jumper'),
	(70, '', '<p>Wide dress ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>', 'Wide dress', '', '', '', 'wide-dress'),
	(71, '', '<p>Classic Jacket ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>', 'Classic Jacket', '', '', '', 'classic-jacket'),
	(72, '', '<p>Poncho ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>', 'Poncho', '', '', '', 'poncho'),
	(73, '', '<p>Dress ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>', 'Dress', '', '', '', 'dress');

INSERT IGNORE INTO `#__virtuemart_product_categories` (`id`, `virtuemart_product_id`, `virtuemart_category_id`, `ordering`) VALUES
	(1, 1, 1, 1),
	(2, 2, 1, 2),
	(3, 3, 1, 3),
	(4, 4, 1, 4),
	(5, 5, 1, 5),
	(6, 6, 1, 6),
	(7, 7, 1, 7),
	(8, 11, 1, 8),
	(9, 15, 1, 9),
	(13, 21, 2, 3),
	(11, 20, 2, 2),
	(12, 19, 2, 1),
	(14, 22, 2, 4),
	(15, 23, 2, 5),
	(16, 24, 2, 6),
	(17, 25, 2, 7),
	(20, 27, 3, 3),
	(19, 26, 2, 8),
	(21, 28, 3, 2),
	(22, 36, 3, 27),
	(23, 35, 3, 24),
	(24, 34, 3, 25),
	(25, 33, 3, 32),
	(26, 32, 3, 1),
	(27, 31, 3, 4),
	(28, 30, 3, 5),
	(29, 29, 3, 7),
	(30, 37, 3, 28),
	(31, 38, 3, 8),
	(32, 39, 3, 6),
	(33, 47, 3, 18),
	(34, 46, 3, 21),
	(35, 45, 3, 23),
	(36, 44, 3, 22),
	(37, 43, 3, 16),
	(38, 42, 3, 17),
	(39, 41, 3, 30),
	(40, 40, 3, 15),
	(41, 48, 3, 33),
	(42, 49, 3, 20),
	(43, 50, 3, 19),
	(44, 58, 3, 29),
	(45, 57, 3, 10),
	(46, 56, 3, 31),
	(47, 55, 3, 9),
	(48, 54, 3, 26),
	(49, 53, 3, 11),
	(50, 52, 3, 13),
	(51, 51, 3, 14),
	(52, 59, 3, 12),
	(53, 60, 5, 1),
	(54, 61, 5, 2),
	(55, 62, 5, 3),
	(56, 63, 5, 4),
	(57, 64, 6, 1),
	(58, 65, 6, 2),
	(59, 66, 6, 3),
	(60, 67, 6, 4),
	(61, 68, 7, 1),
	(62, 70, 7, 3),
	(63, 70, 9, 1),
	(64, 69, 7, 2),
	(65, 69, 8, 1),
	(66, 71, 7, 4),
	(67, 71, 8, 2),
	(68, 72, 7, 5),
	(69, 72, 9, 2),
	(70, 72, 8, 3),
	(71, 73, 7, 6),
	(72, 73, 9, 3),
	(73, 63, 4, 4),
	(74, 62, 4, 3),
	(75, 61, 4, 2),
	(76, 60, 4, 1),
	(77, 67, 4, 8),
	(78, 66, 4, 7),
	(79, 65, 4, 6),
	(80, 64, 4, 5);

INSERT IGNORE INTO `#__virtuemart_product_customfields` (`virtuemart_customfield_id`, `virtuemart_product_id`, `virtuemart_custom_id`, `customfield_value`, `disabler`, `override`, `customfield_price`, `customfield_params`, `product_sku`, `product_gtin`, `product_mpn`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`, `ordering`) VALUES
	(1, 3, 3, 'This is the content of the customfield string.', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(2, 4, 4, 'Default product with this customfield textarea.</br></br>\r\n\r\nLorem ipsum dolor sit amet, set clita kasd gubergren, no sea takimata sanctus est dolor sit amet consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.</br></br>\r\n\r\nAt vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</br></br>\r\n', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(3, 5, 5, '', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(4, 5, 6, 'Customfield Child string 1 content', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(5, 5, 7, 'Customfield ChildCustomfield Child string 2 content 2: string content', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 2),
	(6, 5, 8, '</br>\r\nCustomfield Textarea Child content </br>\r\n>> This three customfields are assigned by adding Customfield Parent.</br>', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 3),
	(7, 6, 9, '(default)', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(8, 6, 9, 'Variant A', 0, 0, 2.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(9, 6, 9, 'Variant B', 0, 0, 5.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 2),
	(10, 6, 9, 'Variant C', 0, 0, 10.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 3),
	(11, 7, 10, 'product_sku', 0, 0, NULL, 'withParent="1"|parentOrderable="1"|', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(12, 11, 10, 'product_sku', 0, 0, NULL, 'withParent="1"|parentOrderable="0"|', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(13, 15, 1, '6', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(14, 15, 1, '7', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(15, 15, 10, 'product_sku', 0, 0, NULL, 'withParent="1"|parentOrderable="1"|', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(16, 15, 9, '(default)', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(17, 15, 9, 'Variante A', 0, 0, 10.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 2),
	(18, 15, 9, 'Variante B', 0, 0, 20.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 3),
	(19, 15, 9, 'Variante C', 0, 0, 30.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 4),
	(20, 23, 5, '', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(21, 23, 6, 'Customfield string 1: Child content', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(22, 23, 7, 'Customfield string 2: Child content', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 2),
	(23, 23, 8, '</br>\r\nAdvanced PATTERN content </br>\r\n>> This three customfields are assigned by adding Customfields Parent. </br>', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 3),
	(24, 26, 5, '', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(25, 26, 6, 'Advanced multiple overwrite', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(26, 26, 7, 'Advanced multiple overwrite', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 2),
	(27, 26, 8, '>> Advanced multiple overwrite', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 3),
	(28, 61, 1, '62', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(29, 61, 1, '63', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(30, 62, 1, '63', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(31, 62, 1, '61', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(32, 63, 1, '62', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(33, 63, 1, '61', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(44, 64, 13, 'Details: ', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 5),
	(43, 64, 11, '', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 4),
	(41, 64, 12, 'M-L', 0, 0, 1.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 2),
	(40, 64, 12, 'S-M', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(46, 65, 12, 'S', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(42, 64, 12, 'L-XL', 0, 0, 2.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 3),
	(45, 64, 14, 'Components: ', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 6),
	(47, 65, 12, 'M', 0, 0, 1.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 2),
	(48, 65, 12, 'L', 0, 0, 3.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 3),
	(49, 65, 11, '', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 4),
	(50, 65, 13, 'Extra fluffy cap your Santa will be amused', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 5),
	(51, 65, 14, '100% Synthetic Deerimitation', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 6),
	(52, 66, 12, 'S', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(53, 66, 12, 'M', 0, 0, 3.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 2),
	(54, 66, 12, 'L', 0, 0, 5.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 3),
	(55, 66, 11, '', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 4),
	(56, 66, 13, 'The players choice!', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 5),
	(57, 66, 14, '100% Cotton', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 6),
	(58, 66, 1, '65', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(59, 66, 1, '67', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(60, 65, 1, '66', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(61, 65, 1, '67', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(62, 67, 12, 'S-M', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(63, 67, 12, 'M-L', 0, 0, 1.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 2),
	(64, 67, 12, 'L-XL', 0, 0, 2.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 3),
	(65, 67, 11, '', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 4),
	(66, 67, 13, 'Your freetime and leisure heads friend', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 5),
	(67, 67, 14, '100% Cotton', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 6),
	(68, 67, 1, '65', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(69, 67, 1, '66', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(70, 68, 15, 'Twill', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(71, 68, 15, 'Rip-stop', 0, 0, 10.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(72, 68, 16, 'M', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 2),
	(73, 68, 16, 'L', 0, 0, 10.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 3),
	(74, 68, 17, '', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 4),
	(75, 68, 18, '100% natural wool', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 5),
	(76, 68, 19, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 6),
	(77, 69, 16, 'M-L', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 2),
	(78, 69, 16, 'L-XL', 0, 0, 15.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 3),
	(79, 69, 17, '', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 4),
	(80, 69, 18, '100% Cotton', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 5),
	(81, 69, 19, 'Jumper ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 6),
	(82, 70, 15, 'Fine', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(83, 70, 15, 'Extra fine', 0, 0, 100.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(84, 70, 16, 'S-M', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 2),
	(85, 70, 16, 'M-L', 0, 0, 50.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 3),
	(86, 70, 17, '', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 4),
	(87, 70, 18, '100% Cotton special', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 5),
	(88, 70, 19, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 6),
	(89, 71, 15, 'Cord', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(90, 71, 15, 'Twill', 0, 0, 100.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(91, 71, 16, 'M-L', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 2),
	(92, 71, 16, 'L-XL', 0, 0, 100.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 3),
	(93, 71, 17, '', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 4),
	(94, 71, 18, '100% Cotton', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 5),
	(95, 71, 19, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 6),
	(96, 72, 15, 'Rubber', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(97, 72, 15, 'Polyethylen', 0, 0, 5.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(98, 72, 16, 'S-M', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 2),
	(99, 72, 16, 'L-XL', 0, 0, 5.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 3),
	(100, 72, 17, '', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 4),
	(101, 72, 18, '100% Synthetic', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 5),
	(102, 72, 19, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 6),
	(103, 73, 16, 'XS', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 0),
	(104, 73, 16, 'S', 0, 0, 10.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 1),
	(105, 73, 16, 'M', 0, 0, 20.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 2),
	(106, 73, 16, 'L', 0, 0, 30.000000, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 3),
	(107, 73, 17, '', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 4),
	(108, 73, 19, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 0, 0, NULL, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0, 6);

INSERT IGNORE INTO `#__virtuemart_product_manufacturers` (`id`, `virtuemart_product_id`, `virtuemart_manufacturer_id`) VALUES
	(1, 1, 2),
	(2, 2, 2),
	(3, 3, 2),
	(4, 4, 2),
	(5, 5, 2),
	(6, 6, 2),
	(7, 7, 2),
	(8, 8, 2),
	(9, 9, 2),
	(10, 10, 2),
	(11, 11, 2),
	(12, 15, 2),
	(13, 19, 2),
	(14, 20, 2),
	(15, 21, 2),
	(16, 22, 2),
	(17, 23, 2),
	(18, 24, 1),
	(19, 25, 2),
	(20, 26, 1),
	(21, 27, 2),
	(22, 28, 2),
	(23, 36, 1),
	(24, 35, 3),
	(25, 34, 2),
	(26, 33, 1),
	(27, 32, 3),
	(28, 31, 2),
	(29, 30, 2),
	(30, 29, 1),
	(31, 37, 3),
	(32, 38, 2),
	(33, 39, 2),
	(34, 47, 1),
	(35, 46, 3),
	(36, 45, 2),
	(37, 44, 1),
	(38, 43, 3),
	(39, 42, 2),
	(40, 41, 1),
	(41, 40, 3),
	(42, 48, 2),
	(43, 49, 2),
	(44, 50, 2),
	(45, 58, 1),
	(46, 57, 3),
	(47, 56, 2),
	(48, 55, 1),
	(49, 54, 3),
	(50, 53, 2),
	(51, 52, 2),
	(52, 51, 1),
	(53, 59, 1),
	(54, 60, 3),
	(55, 61, 3),
	(56, 62, 2),
	(57, 63, 2),
	(58, 64, 2),
	(59, 65, 2),
	(60, 66, 1),
	(61, 67, 3),
	(62, 68, 2),
	(63, 70, 1),
	(64, 69, 3),
	(65, 71, 3),
	(66, 72, 1),
	(67, 73, 2);

INSERT IGNORE INTO `#__virtuemart_product_medias` (`id`, `virtuemart_product_id`, `virtuemart_media_id`, `ordering`) VALUES
	(1, 1, 8, 1),
	(2, 2, 8, 1),
	(3, 3, 8, 1),
	(4, 4, 8, 1),
	(5, 5, 8, 1),
	(6, 6, 8, 1),
	(7, 7, 8, 1),
	(8, 11, 8, 1),
	(9, 15, 8, 1),
	(10, 19, 8, 1),
	(11, 23, 8, 1),
	(12, 26, 8, 1),
	(13, 22, 8, 1),
	(14, 27, 8, 1),
	(15, 49, 8, 1),
	(16, 38, 8, 1),
	(17, 60, 8, 1),
	(18, 61, 10, 1),
	(19, 62, 11, 1),
	(20, 63, 12, 1),
	(21, 64, 8, 1),
	(22, 65, 13, 1),
	(23, 66, 14, 1),
	(24, 67, 15, 1),
	(25, 68, 8, 1),
	(26, 69, 16, 1),
	(27, 70, 17, 1),
	(28, 71, 18, 1),
	(29, 72, 19, 1),
	(30, 73, 20, 1);

INSERT IGNORE INTO `#__virtuemart_product_prices` (`virtuemart_product_price_id`, `virtuemart_product_id`, `virtuemart_shoppergroup_id`, `product_price`, `override`, `product_override_price`, `product_tax_id`, `product_discount_id`, `product_currency`, `product_price_publish_up`, `product_price_publish_down`, `price_quantity_start`, `price_quantity_end`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
	(1, 1, 0, 10.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(2, 2, 0, 0.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(3, 3, 0, 10.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(4, 5, 0, 10.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(5, 6, 0, 10.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(6, 7, 0, 10.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(7, 8, 0, 15.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(8, 9, 0, 20.00000, 0, 0.00000, 0, 0, 191, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(9, 10, 0, 25.00000, 0, 0.00000, 0, 0, 191, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(10, 11, 0, 10.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(11, 12, 0, 15.00000, 0, 0.00000, 0, 0, 191, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(12, 13, 0, 20.00000, 0, 0.00000, 0, 0, 191, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(13, 14, 0, 25.00000, 0, 0.00000, 0, 0, 191, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(14, 15, 0, 10.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(15, 16, 0, 15.00000, 0, 0.00000, 0, 0, 191, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(16, 17, 0, 20.00000, 0, 0.00000, 0, 0, 191, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(17, 18, 0, 25.00000, 0, 0.00000, 0, 0, 191, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(18, 4, 0, 10.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(19, 19, 0, 10.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(20, 21, 0, 20.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(21, 22, 0, 30.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(22, 23, 0, 100.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(23, 25, 0, 150.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(24, 26, 0, 200.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(25, 27, 0, 40.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(26, 38, 0, 210.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(27, 49, 0, 300.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(28, 61, 0, 29.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(29, 62, 0, 34.90000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(30, 63, 0, 44.90000, 0, 0.00000, 0, 0, 191, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(31, 64, 0, 25.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(32, 65, 0, 24.90000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(33, 66, 0, 15.00000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(34, 67, 0, 17.90000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(35, 68, 0, 249.90000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(36, 69, 0, 149.90000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(37, 70, 0, 490.90000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(38, 71, 0, 899.90000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(39, 72, 0, 24.90000, 0, 0.00000, 0, 0, 182, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0),
	(40, 73, 0, 449.90000, 0, 0.00000, 0, 0, 47, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 0);

INSERT IGNORE INTO `#__virtuemart_ratings` (`virtuemart_rating_id`, `virtuemart_product_id`, `rates`, `ratingcount`, `rating`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`) VALUES
	(1, 4, 4, 1, 4.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(2, 5, 5, 1, 5.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(3, 6, 4, 1, 4.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(4, 7, 4, 1, 4.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(5, 11, 5, 1, 5.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(6, 15, 5, 1, 5.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(7, 22, 3, 1, 3.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(8, 23, 5, 1, 5.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(9, 24, 4, 1, 4.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(10, 25, 4, 1, 4.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(11, 26, 5, 1, 5.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(12, 21, 5, 1, 5.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(13, 20, 3, 1, 3.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(14, 19, 5, 1, 5.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(15, 63, 5, 1, 5.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(16, 60, 5, 1, 5.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(17, 61, 4, 1, 4.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(18, 62, 4, 1, 4.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(19, 67, 5, 1, 5.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(20, 64, 4, 1, 4.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(21, 65, 5, 1, 5.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(22, 66, 5, 1, 5.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(23, 71, 5, 1, 5.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(24, 72, 3, 1, 3.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(25, 73, 4, 1, 4.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(26, 70, 5, 1, 5.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(27, 69, 4, 1, 4.0, 0, '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635);

INSERT IGNORE INTO `#__virtuemart_rating_votes` (`virtuemart_rating_vote_id`, `virtuemart_product_id`, `vote`, `lastip`, `created_on`, `created_by`, `modified_on`, `modified_by`) VALUES
	(1, 4, 4, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(2, 5, 5, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(3, 6, 4, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(4, 7, 4, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(5, 11, 5, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(6, 15, 5, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(7, 22, 3, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(8, 23, 5, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(9, 24, 4, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(10, 25, 4, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(11, 26, 5, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(12, 21, 5, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(13, 20, 3, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(14, 19, 5, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(15, 63, 5, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(16, 60, 5, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(17, 61, 4, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(18, 62, 4, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(19, 67, 5, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(20, 64, 4, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(21, 65, 5, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(22, 66, 5, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(23, 71, 5, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(24, 72, 3, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(25, 73, 4, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(26, 70, 5, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635),
	(27, 69, 4, '::1', '0000-00-00 00:00:00', 635, '0000-00-00 00:00:00', 635);

INSERT INTO `#__virtuemart_shoppergroups` (`virtuemart_shoppergroup_id`, `virtuemart_vendor_id`, `shopper_group_name`, `shopper_group_desc`, `default`, `shared`, `published`) VALUES
(NULL, 1, 'Gold Level', 'Gold Level Shoppers.', 0,1,1),
(NULL, 1, 'Wholesale', 'Shoppers that can buy at wholesale.', 0,1,1);