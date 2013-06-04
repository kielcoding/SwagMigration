<?php
/**
 * Shopware 4.0
 * Copyright © 2012 shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

/**
 * Shopware SwagMigration Components - Migration
 *
 * Factory for the migration profiles
 *
 * @category  Shopware
 * @package Shopware\Plugins\SwagMigration\Components
 * @copyright Copyright (c) 2012, shopware AG (http://www.shopware.de)
 */
class Shopware_Components_Migration extends Enlight_Class
{
	static protected $profileNamespace = 'Shopware_Components_Migration_Profile';

    /**
     * For the generation of the profile is a factory used, because of the profile type is not known until runtime.
     * @static
     * @param $profile
     * @param array $config
     * @return Enlight_Class
     */
	public static function factory($profile, $config = array())
	{
		$profileNamespace = empty($config['profileNamespace']) ? self::$profileNamespace : $config['profileNamespace'];
		$profileName = $profileNamespace . '_';
		$profileName .= str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($profile))));
				
		$migrationAdapter = Enlight_Class::Instance($profileName, array($config));
		
		return $migrationAdapter;
	}
}