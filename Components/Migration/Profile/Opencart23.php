<?php

namespace Shopware\SwagMigration\Components\Migration\Profile;

use Shopware\SwagMigration\Components\Migration\Profile;

class Opencart23 extends Profile
{
    /**
     * Returns the directory of the article images.
     *
     * @return string {String} | image path
     */
    public function getProductImagePath()
    {
        return 'image/';
    }

    /**
     * Returns the sql statement to select default shop system language
     *
     * @return string {String} | sql for default language
     */
    public function getDefaultLanguageSelect()
    {
        return "
            SELECT `language_id`
            FROM {$this->quoteTable('language')}
            WHERE status = 1
            ORDER BY `sort_order` ASC
        ";
    }

    /**
     * Returns the sql statement to select the shop system languages
     *
     * @return string {String} | sql for languages
     */
    public function getLanguageSelect()
    {
        return "
			SELECT `language_id` as id, `name`
			FROM {$this->quoteTable('language')}
		";
    }

    /**
     * Returns the property options (e.g. "color") of the shop
     */
    public function getPropertyOptionSelect()
    {
        return "
   			SELECT DISTINCT
   			  `name` as name,
   			  `name` as id

   			FROM {$this->quoteTable('attribute_description')}

            WHERE language_id = {$this->Db()->quote($this->getDefaultLanguage())}
   		";
    }

    /**
     * This function returns the profile sub shops
     *
     * @return array
     */
    public function getShops()
    {
        return array_merge(
            $this->db->fetchPairs($this->getDefaultShopSelect()),
            $this->db->fetchPairs($this->getShopSelect())
        );
    }

    /**
     * Returns a SQL statement for the default shop
     *
     * @return string {String} | sql for default shop
     */
    public function getDefaultShopSelect()
    {
        return "
			SELECT 0 as `id`, `value` as `name`
            FROM {$this->quoteTable('setting')}
			WHERE `key` = {$this->Db()->quote('config_name')}
	    ";
    }

    /**
     * Returns a SQL statement
     *
     * @return string {String} | sql for sub shops
     */
    public function getShopSelect()
    {
        return "
			SELECT `store_id` as id, `name`
            FROM {$this->quoteTable('store')}
	    ";
    }

    /**
     * Returns the sql statement to select the shop system customer groups
     *
     * @return string {String} | sql for customer groups
     */
    public function getCustomerGroupSelect()
    {
        return "
			SELECT g.customer_group_id as id, d.name as `name`
			FROM {$this->quoteTable('customer_group', 'g')}
			
            INNER JOIN {$this->quoteTable('customer_group_description', 'd')}
            ON g.customer_group_id = d.customer_group_id

			WHERE d.language_id = {$this->Db()->quote($this->getDefaultLanguage())}
		";
    }

    /**
     * Returns the sql statement to select the shop system payments
     *
     * @return string {String} | sql for the payments
     */
    public function getPaymentMeanSelect()
    {
        return "
			SELECT payment_code as id, payment_method as `name`
			FROM {$this->quoteTable('order')}
			GROUP BY payment_code, payment_method
		";
    }

    /**
     * Returns the sql statement to select the shop system order states
     *
     * @return string {String} | sql for the order states
     */
    public function getOrderStatusSelect()
    {
        return "
			SELECT `order_status_id` as id, `name` as name
			FROM {$this->quoteTable('order_status')}
			WHERE `language_id` = {$this->Db()->quote($this->getDefaultLanguage())}
		";
    }

    /**
     * Returns the sql statement to select the shop system tax rates
     *
     * @return string {String} | sql for the tax rates
     */
    public function getTaxRateSelect()
    {
        return "
			SELECT `tax_rate_id` as id, `name` as name
			FROM {$this->quoteTable('tax_rate')}
		";
    }

    /**
     * Returns the sql statement to select articles with properties
     *
     * @param $id
     *
     * @return string
     */
    public function getProductPropertiesSelect($id)
    {
        return "
            SELECT
                ''					                    as 'group',
                ad.name                                 as 'option',
                pa.product_id                           as productId,
                pa.text                                 as 'value'

            FROM {$this->quoteTable('product_attribute', 'pa')}

            INNER JOIN {$this->quoteTable('attribute', 'a')}
            ON pa.attribute_id = a.attribute_id

           INNER JOIN {$this->quoteTable('attribute_description', 'ad')}
            ON pa.attribute_id = ad.attribute_id

            WHERE ad.language_id = {$this->Db()->quote($this->getDefaultLanguage())}
            AND pa.language_id = {$this->Db()->quote($this->getDefaultLanguage())}
            AND pa.product_id = {$id}
        ";
    }

    /**
     * Get ids of all products with properties
     *
     * @return string
     */
    public function getProductsWithPropertiesSelect()
    {
        return "
            SELECT
            DISTINCT pa.product_id as productID

            FROM  {$this->quoteTable('product_attribute', 'pa')}

            WHERE pa.language_id = {$this->Db()->quote($this->getDefaultLanguage())}
        ";
    }

    /**
     * Get productIds for all products with attributes
     *
     * @return string
     */
    public function getAttributedProductsSelect()
    {
        return "
            SELECT
            DISTINCT pov.product_id as productID

            FROM  {$this->quoteTable('product_option_value', 'pov')}
        ";
    }

    /**
     * Select attributes for a given article
     *
     * @param $id
     *
     * @return string
     */
    public function getProductAttributesSelect($id)
    {
        return "
            SELECT
                od.name                                 as group_name,
                p.product_id                            as productId,
                ovd.name                                as option_name,
                IF(pv.price_prefix='+', pv.price, CONCAT('-', pv.price)) as price,
                ov.sort_order                           as option_position

            FROM {$this->quoteTable('product', 'p')}
            
            INNER JOIN {$this->quoteTable('product_option_value', 'pv')}
            ON p.product_id = pv.product_id

           INNER JOIN {$this->quoteTable('option_value', 'ov')}
            ON pv.option_value_id = ov.option_value_id

           INNER JOIN {$this->quoteTable('option_description', 'od')}
            ON pv.option_id = od.option_id

           INNER JOIN {$this->quoteTable('option_value_description', 'ovd')}
            ON pv.option_value_id = ovd.option_value_id
            AND pv.option_id = ovd.option_id

            WHERE od.language_id = {$this->Db()->quote($this->getDefaultLanguage())}
            AND ovd.language_id = {$this->Db()->quote($this->getDefaultLanguage())}
            AND p.product_id = {$id}
        ";
    }

    /**
     * Returns the sql statement to select the shop system articles
     *
     * @return string {String} | sql for the articles
     */
    public function getProductSelect()
    {
        return "
			SELECT
				a.product_id							as productID,

				a.quantity				        		as instock,
				a.model			            			as ordernumber,

				a.price				            		as net_price,
				a.purchase_price	             		as purchase_price,

				a.date_available 		        		as releasedate,
				a.date_added			        		as added,
				a.date_modified 						as 'changed',
				a.weight		        				as weight,
				a.tax_class_id				        	as taxID,
				s.name					                as supplier,
				a.status				        		as active,

				''						                as fsk18,
				a.ean							        as ean,

				d.name 					            	as name,
				d.description 					        as description_long,
				d.description 			                as description,
				d.tag        				          	as tags,
				d.meta_title					        as meta_title,
				d.meta_description 			            as meta_description,
				d.meta_keyword  				        as keywords,
				u.keyword    							as link

			FROM {$this->quoteTable('product', 'a')}

			LEFT JOIN {$this->quoteTable('manufacturer', 's')}
			ON s.manufacturer_id = a.manufacturer_id

			LEFT JOIN {$this->quoteTable('product_description', 'd')}
			ON d.product_id = a.product_id
			AND d.language_id = {$this->Db()->quote($this->getDefaultLanguage())}
			
			LEFT JOIN {$this->quoteTable('url_alias', 'u')}
			ON a.product_id = REPLACE(u.query, 'product_id=', '')
		";
    }

    /**
     * Returns the sql statement to select the shop system article prices
     *
     * @return string {String} | sql for the article prices
     */
    public function getProductPriceSelect()
    {
        return "
            SELECT
                product_id              as productID,
                quantity                as `from`,
                price                   as price,
                customer_group_id       as pricegroup

			FROM {$this->quoteTable('product_discount')}

            ORDER BY productID, `from`
		";
    }

    /**
     * Returns the sql statement to select the shop system article image allocation
     *
     * @return string {String} | sql for the article image allocation
     */
    public function getProductImageSelect()
    {
        return "
            (
                SELECT
                    product_id              as productID,
                    image                   as image,
                    1                       as main,
                    sort_order              as position

                FROM {$this->quoteTable('product')}

            ) UNION (

                SELECT
                    product_id              as productID,
                    image                   as image,
                    0                       as main,
                    sort_order              as position

                FROM {$this->quoteTable('product_image')}
            )
        ";
    }

    /**
     * Returns the sql statement to select the shop system article translations
     *
     * @return string {String} | sql for the article translations
     */
    public function getProductTranslationSelect()
    {
        return "
			SELECT
				d.product_id 		    as productID,
				d.language_id 	        as languageID,
				d.name 				    as name,
				d.description 	        as description_long,
				d.tag 			        as tags,
				d.meta_title		    as meta_title,
				d.meta_description 	    as description,
				d.meta_keyword		    as keywords

			FROM {$this->quoteTable('product_description', 'd')}

			WHERE `language_id` != {$this->Db()->quote($this->getDefaultLanguage())}
		";
    }

    /**
     * Returns the sql statement to select the shop system article relations
     *
     * @return string {String} | sql for the article relations
     */
    public function getProductRelationSelect()
    {
        return "
			SELECT
			  product_id                as productID,
			  related_id                as relatedID,
			  ''                        as groupID

			FROM {$this->quoteTable('product_related')}
		";
    }

    /**
     * Returns the sql statement to select the shop system customer
     *
     * @return string {String} | sql for the customer data
     */
    public function getCustomerSelect()
    {
        return "
			SELECT
				u.customer_id 							as customerID,
				u.customer_id 							as customernumber,

				''                                      as salutation,
				a.firstname								as firstname,
				a.lastname 	 							as lastname,

				''		                                as billing_salutation,
				a.firstname								as billing_firstname,
				a.lastname 	 							as billing_lastname,
				a.company		 						as billing_company,
				a.address_2								as billing_department,
				a.address_1	 							as billing_street,
				'' 										as billing_streetnumber,
				a.postcode 								as billing_zipcode,
				a.city	 								as billing_city,
				c.iso_code_2 						    as billing_countryiso,

				''                          			as shipping_salutation,
				''							            as shipping_company,
				''							            as shipping_firstname,
				'' 							            as shipping_lastname,
				'' 							            as shipping_street,
				''  									as shipping_streetnumber,
				''								        as shipping_city,
				''							            as shipping_countryiso,
				''							            as shipping_zipcode,

				u.telephone 							as phone,
				u.email 							    as email,
				DATE('1970-01-01 00:00:00')				as birthday,
				u.newsletter							as newsletter,

				u.password 								as md5_password,
				u.salt   								as salt,

				u.customer_group_id						as customergroupID,

				u.date_added 							as firstlogin,
				u.date_added							as lastlogin,
				u.status								as active,
				u.store_id               				as subshopID

			FROM {$this->quoteTable('customer', 'u')}

			JOIN {$this->quoteTable('address', 'a')}
			ON a.customer_id = u.customer_id
			AND a.address_id = u.address_id

			LEFT JOIN {$this->quoteTable('country', 'c')}
			ON c.country_id = a.country_id
		";
    }

    /**
     * Returns the sql statement to select the shop system article category allocation
     *
     * @return string {String} | sql for the article category allocation
     */
    public function getProductCategorySelect()
    {
        return "
			SELECT
			    product_id              as productID,
			    category_id             as categoryID

			FROM {$this->quoteTable('product_to_category')}

			ORDER BY `product_id`
		";
    }

    /**
     * Returns the sql statement to select the shop system categories
     *
     * @return string {String} | sql for the categories
     */
    public function getCategorySelect()
    {
        return "
			SELECT
				c.category_id           as categoryID,
				c.parent_id             as parentID,
				d.language_id           as languageID,
				d.name                  as description,
				c.sort_order            as position,
				d.meta_title            as meta_title,
				d.meta_keyword          as metaKeywords,
				d.meta_description      as metaDescription,
				d.title_text            as cmsheadline,
				d.description           as cmstext,
				c.status                as active

			FROM {$this->quoteTable('category', 'c')}

            JOIN {$this->quoteTable('category_description', 'd')}
			ON c.category_id = d.category_id

			ORDER BY c.parent_id
		";
    }

    /**
     * Returns the sql statement to select the shop system article ratings
     *
     * @return string {String} | sql for the article ratings
     */
    public function getProductRatingSelect()
    {
        return "
			SELECT
				r.product_id            as productID,
				r.customer_id           as customerID,
				r.author                as `name`,
				c.email                 as email,
				r.rating                as rating,
				r.date_added            as `date`,
				r.status                as active,
				r.text                  as comment,
				''                      as title

			FROM {$this->quoteTable('review', 'r')}

			LEFT JOIN {$this->quoteTable('customer', 'c')}
			ON r.customer_id = c.customer_id
		";
    }

    /**
     * Returns the sql statement to select the shop system customer
     *
     * @return string {String} | sql for the customer data
     */
    public function getOrderSelect()
    {
        return "
			SELECT
				o.order_id									as orderID,
				CONCAT(o.invoice_prefix, o.invoice_no)      as ordernumber,
				o.customer_id								as customerID,

				''                                          as billing_salutation,
				o.payment_firstname                         as billing_firstname,
				o.payment_lastname                          as billing_lastname,
				o.payment_company                           as billing_company,
				o.payment_address_1						    as billing_street,
				o.payment_city                              as billing_city,
				o.payment_postcode							as billing_zipcode,
				pc.iso_code_2					            as billing_countryiso,

				''                                          as shipping_salutation,
				o.shipping_firstname						as shipping_firstname,
				o.shipping_lastname							as shipping_lastname,
				o.shipping_company							as shipping_company,
				o.shipping_address_1						as shipping_street,
				o.shipping_city								as shipping_city,
				o.shipping_postcode							as shipping_zipcode,
				sc.iso_code_2					            as shipping_countryiso,

				o.telephone							        as phone,
				o.payment_code							    as paymentID,
				o.shipping_method							as dispatchID,
				o.currency_code								as currency,
				o.currency_value							as currency_factor,
				o.language_id							    as languageID,
				o.comment									as customercomment,
				o.date_added								as date,
				o.order_status_id							as statusID,
				o.ip								        as remote_addr,
				o.store_id 									as subshopID,

				(
					SELECT SUM(`value`)
					FROM order_total
					WHERE `code` = 'shipping'
					AND order_id = o.order_id
				)										    as invoice_shipping,
				(
					SELECT SUM(`value`)
					FROM order_total
					WHERE `code` = 'shipping'
					AND order_id = o.order_id
				)											as invoice_shipping_net,
				(
					SELECT SUM(`value`)
					FROM order_total
					WHERE `code` = 'sub_total'
					AND order_id = o.order_id
				)											as invoice_amount,
				(
					SELECT SUM(`value`)
					FROM order_total
					WHERE `code` = 'total'
					AND order_id = o.order_id
				)											as invoice_amount_net

			FROM {$this->quoteTable('order', 'o')}

			LEFT JOIN {$this->quoteTable('customer', 'u')}
			ON u.customer_id = o.customer_id

            LEFT JOIN {$this->quoteTable('language', 'l')}
			ON l.directory = o.language_id

			LEFT JOIN {$this->quoteTable('address', 'a')}
			ON a.customer_id = u.customer_id
			AND a.address_id = u.address_id

			LEFT JOIN {$this->quoteTable('country', 'pc')}
			ON pc.country_id = o.payment_country_id

			LEFT JOIN {$this->quoteTable('country', 'sc')}
			ON sc.country_id = o.shipping_country_id
		";
    }

    /**
     * Returns the sql statement to select all shop system order details
     *
     * @return string {String} | sql for order details
     */
    public function getOrderDetailSelect()
    {
        return "
            SELECT
                op.order_id                 as orderID,
                op.product_id               as productID,
                op.model                    as article_ordernumber,

                IFNULL(CONCAT(
                    op.name,
                    ' ',
                    GROUP_CONCAT(oo.value SEPARATOR ', '),
                    ' (',
                    GROUP_CONCAT(oo.name SEPARATOR ', '),
                    ')'
                ), op.name)                 as name,

                op.price                    as price,
                op.quantity                 as quantity

            FROM {$this->quoteTable('order_product', 'op')}

            -- Join attributes in order to name the article by its attribute
            LEFT JOIN {$this->quoteTable('order_option', 'oo')}
            ON oo.order_product_id = op.order_product_id

            GROUP BY (op.order_product_id)
		";
    }
}
