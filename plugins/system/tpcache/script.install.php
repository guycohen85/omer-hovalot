<?php
/**
 * @package	TPCache Installer
 * @version $Id: script.install.php,v 1.4.14 2015-04-14 11:57:00 elizovsky Exp $;
 *
 * @author		Alex Segal <elizovsky@gmail.com>
 * @copyright	Copyright (C) 2015 Alex Segal
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;

class PlgSystemTPCacheInstallerScript
{
	public function install($parent)
	{
		// Enable plug-in
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__extensions');
		$query->set($db->quoteName('enabled') .' = 1');
		$query->set($db->quoteName('ordering') .' = 100');
		$query->where($db->quoteName('type') .' = '. $db->quote('plugin'));
		$query->where($db->quoteName('element') .' = '. $db->quote('tpcache'));
		$db->setQuery($query);
		$db->execute();
	}
}
