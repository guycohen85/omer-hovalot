-- Remove all virtuemart data

DELETE FROM `#__virtuemart_calcs`;
DELETE FROM `#__virtuemart_calc_categories`;
DELETE FROM `#__virtuemart_calc_shoppergroups`;
DELETE FROM `#__virtuemart_calc_countries`;
DELETE FROM `#__virtuemart_calc_states`;
DELETE FROM `#__virtuemart_categories`;
DELETE FROM `#__virtuemart_category_categories`;
DELETE FROM `#__virtuemart_category_medias`;
DELETE FROM `#__virtuemart_coupons`;
DELETE FROM `#__virtuemart_countries`;
DELETE FROM `#__virtuemart_customs`;
DELETE FROM `#__virtuemart_manufacturers`;
DELETE FROM `#__virtuemart_manufacturercategories`;
DELETE FROM `#__virtuemart_manufacturer_medias`;
DELETE FROM `#__virtuemart_medias`;
DELETE FROM `#__virtuemart_migration_oldtonew_ids`;
DELETE FROM `#__virtuemart_orders`;
DELETE FROM `#__virtuemart_order_histories`;
DELETE FROM `#__virtuemart_order_items`;
DELETE FROM `#__virtuemart_order_userinfos`;
DELETE FROM `#__virtuemart_paymentmethods`;
DELETE FROM `#__virtuemart_paymentmethod_creditcards` IF EXISTS;
DELETE FROM `#__virtuemart_paymentmethod_shoppergroups`;
DELETE FROM `#__virtuemart_products`;
DELETE FROM `#__virtuemart_product_categories`;
DELETE FROM `#__virtuemart_product_customfields`;
DELETE FROM `#__virtuemart_product_downloads`;
DELETE FROM `#__virtuemart_product_manufacturers`;
DELETE FROM `#__virtuemart_product_medias`;
DELETE FROM `#__virtuemart_product_prices`;
DELETE FROM `#__virtuemart_product_relations`;
DELETE FROM `#__virtuemart_ratings`;
DELETE FROM `#__virtuemart_rating_reviews`;
DELETE FROM `#__virtuemart_rating_votes`;
DELETE FROM `#__virtuemart_shipmentmethods`;
DELETE FROM `#__virtuemart_shoppergroups`;
DELETE FROM `#__virtuemart_states`;
DELETE FROM `#__virtuemart_userinfos`;
DELETE FROM `#__virtuemart_userfield_values`;
DELETE FROM `#__virtuemart_vendors`;
DELETE FROM `#__virtuemart_vendor_medias`;
DELETE FROM `#__virtuemart_vmusers`;
DELETE FROM `#__virtuemart_vmuser_shoppergroups`;
DELETE FROM `#__virtuemart_waitingusers`;
DELETE FROM `#__virtuemart_worldzones`;