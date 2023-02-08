<?php
/**
 * @package TPCache Plugin
 * @version $Id: tpcache.php,v 1.4.14 2015-04-14 11:27:00 elizovsky Exp $;
 *
 * @author		Alex Segal <elizovsky@gmail.com>
 * @copyright	Copyright (C) 2015 Alex Segal
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;
jimport( 'joomla.plugin.plugin' );

class PlgSystemTPCache extends JPlugin
{
	public	$_cache,
			$_cache_key,
			$_content,
			$_joomapp,
			$_joomgen,
			$_scripts,
			$_template;

	private	$_param;


	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe.
	 * @param   array   $config    An optional associative array of configuration settings.
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		/** @from 1.3.22
		* Firstly check that this is not an AJAX call */
		if ($this->_isAjaxRequest())
		{
			return;
		}

		// Create new parameters object
		$this->_param = new stdClass();

		////////////////////////////////////////////////////////////////////
		// Basic Parameters :: Cache / Hreflang / Compress / Scripts Down //
		$this->_param->pagecaching	= $this->params->get('pagecaching', true);
		$this->_param->hreflangtag	= $this->params->get('hreflangtag', true);
		$this->_param->htmlcompress	= $this->params->get('htmlcompress', true);
		$this->_param->scriptsdown	= $this->params->get('scriptsdown', true);

		///////////////////////////////////////////////////////////////////
		// Basic Parameters :: Auto Clean / Global Check-In / Quick Icon //
		$this->_param->autoclean	= $this->params->get('autoclean', true);
		$this->_param->autocheckin	= $this->params->get('autocheckin', true);
		$this->_param->quickicon	= $this->params->get('quickicon', true);
		$this->_param->extfilter	= $this->params->get('extfilter', 'less, php, xml');

		////////////////////////////////////////
		// Basic Parameters :: IP Redirection //
		$this->_param->redirection	= $this->params->get('redirection', false);

		//////////////////////////////////////////////////////////////////
		// Advanced Parameters :: Disable Bootstrap / jQuery / Mootools //
		$this->_param->unloadbootstrap	= $this->params->get('unloadbootstrap', false);
		$this->_param->unloadjquery 	= $this->params->get('unloadjquery', false);
		$this->_param->unloadmootools	= $this->params->get('unloadmootools', false);

		/////////////////////////////////////////////////////////////////////
		// Advanced Parameters :: Browser Cache / Combine CSS / Combine JS //
		$this->_param->browsercache	= (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET')
			? $this->params->get('browsercache', false) /** @from 1.3.29 */
			: false; /* Disable browser cache on non HTTP GET requests */
		$this->_param->combinecss	= $this->params->get('combinecss', false);
		$this->_param->combinejs	= $this->params->get('combinejs', false);

		///////////////////////////////////////////
		// Advanced Parameters :: Debug Messages //
		$this->_param->debugging	= $this->params->get('debugging', false);
		if ($this->_param->debugging)
		{
			error_reporting(E_ALL ^ E_STRICT);
			ini_set('display_errors', 1);
		}

		///////////////////////////////////////////////////////////
		// Redirection from a temp address like IP-host/~account //
		if ($this->_param->redirection)
		{
			$this->_ipRedirect();
		}

		////////////////////////////////////////////////////////
		// Enable page caching (and optionally browser cache) //
		if ($this->_param->pagecaching)
		{
			$options = array(
				'defaultgroup'	=> 'page',
				'browsercache'	=> $this->_param->browsercache,
				'caching'		=> false,
			);

			jimport( 'joomla.cache.cache' );
			$this->_cache		= JCache::getInstance('page', $options);
			$this->_cache_key	= JURI::getInstance()->toString();
		}
	}

	/**
	 * Converting the site URL to fit to the HTTP request.
	 */
	public function onAfterInitialise()
	{
		// Check that cache is enabled and the document is HTML output
		if (!$this->_cache || $this->_joomApp()->isMessageQueue || JFactory::getDocument()->getType() != 'html')
		{
			return;
		}

		// Cache auto clean in admin-panel
		if ($this->_joomApp()->isAdmin)
		{
			/** @from 1.3.2
			* Smart auto clean for administrator */
			if ($this->_param->autoclean)
			{
				$this->_smartAutoCleanAdmin();
			}

			return;
		}
		else
		{
			/** @from 1.3.28
			* Smart auto clean for website user */
			if ($this->_param->autoclean)
			{
				$this->_smartAutoCleanSite();
			}

			/** @from 1.3.31
			* Path to abstract classes */
			$path_to_library = dirname(__FILE__) . '/lib/';

			// Replace class JHtmlBootstrap
			if ($this->params->get('unloadbootstrap', false))
			{
				include_once $path_to_library . 'bootstrap.php';
			}

			// Replace class JHtmlJquery
			if ($this->params->get('unloadjquery', false))
			{
				include_once $path_to_library . 'jquery.php';
			}

			// Replace class JHtmlBehavior
			if ($this->params->get('unloadmootools', false))
			{
				include_once $path_to_library . 'mootools.php';
			}
		}

		if ($this->_joomApp()->method == 'GET')
		{
			$user = JFactory::getUser();
			if ($user->get('guest')) {
				$this->_cache->setCaching(true);
			}
		}

		$response = $this->_cache->get($this->_cache_key);
		if ($response !== false)
		{
			global $_PROFILER;

			// Joomla 1.5 Support
			global $mainframe;
			if ($this->_joomGen() >= 2)
			{
				$mainframe = JFactory::getApplication();
			}

			if ($this->_joomGen() >= 3)
			{
				// Set cached body in J3.x
				$mainframe->setBody($response);
				echo $mainframe->toString($mainframe->get('gzip'));
				unset($response);

				if (JDEBUG)
				{
					$_PROFILER->mark('afterCache');
				}
			}
			else
			{
				// Set cached body in J1.5, J2.5
				JResponse::setBody($response);
				echo JResponse::toString($mainframe->getCfg('gzip'));

				if (JDEBUG)
				{
					$_PROFILER->mark('afterCache');
					echo implode('', $_PROFILER->getBuffer());
				}
			}

			$mainframe->close();
		}
	}

	/**
	 * After render event.
	 */
	public function onAfterRender()
	{
		if ($this->_joomApp()->isAdmin)
		{
			// Display "Quick Icon"
			if ($this->_param->quickicon)
			{
				// Get body content
				$this->_getBody();

				// Chaining method: render and display an icon
				$this->_renderQuickIcon()
					->_setBody();
			}

			return;
		}

		// Content manipulations: "Combine Files", "Move Scripts Down", "Hreflang Tag", "HTML Compress"
		if( $this->_param->combinecss || $this->_param->combinejs || $this->_param->scriptsdown || $this->_param->hreflangtag || $this->_param->htmlcompress )
		{
			// Get body content
			$this->_getBody();

			/** @from 1.3.28 - "Combine Files"
			* Combine external JavaScript and CSS files into one large file */
			if ($this->_param->combinecss || $this->_param->combinejs)
			{
				// Include the helper functions only once
				include_once dirname(__FILE__) . '/helper.php';
				$helper = new TPCacheHelper;

				// Fetch all the matches
				$matches = array();

				if ($this->_param->combinecss)
				{
					$matches['css'] = TPCacheHelper::getCssMatches($this->_content);
				}

				if ($this->_param->combinejs)
				{
					$matches['js'] = TPCacheHelper::getJsMatches($this->_content);
				}

				// Remove all current links from the document
				$this->_content = TPCacheHelper::removeObsoleteTags($this->_content, $matches);

				// Add the new URL to the document
				$this->_content = TPCacheHelper::addMergeUrl($this->_content, $matches);
			}

			/** @from 1.3.28 - "Scripts Down"
			* High Performance Web Sites: Rule 6  Move Scripts to the Bottom */
			if ($this->_param->scriptsdown)
			{
				// Move scripts to the page bottom
				if (stripos($this->_content, '</body>'))
				{
					$this->_scripts = ''; // Compiled scripts
					$this->_content = preg_replace_callback(
						'#(<script\s*(.*?)>(.*?)</script>\s*)|(<script\s*(.*?)\s*/>\s*)#is',
						function($matches) {
							$this->_scripts .= "<script {$matches[2]}>{$matches[3]}</script>\n";
							return '';
						},
						$this->_content
					);
					$this->_content = preg_replace('#(</body>)#i', $this->_scripts.'$1', $this->_content);
				}
			}

			/** @from 1.4.14 - "Hreflang Tag"
			* Google uses the rel="alternate" hreflang="x" attributes to serve the
			* correct language or regional URL in Search results. */
			if ($this->_param->hreflangtag)
			{
				$_hreflang = '<link rel="alternate" href="'. JURI::current() .'" hreflang="'. JFactory::getLanguage()->getTag() .'" />'. PHP_EOL;
				$this->_content = preg_replace('#(</head>)#i', $_hreflang.'$1', $this->_content);
			}

			/** @from 1.3.28 - "Compress HTML"
			* Compress HTML code by removing line breaks, tabs and extra spaces */
			if ($this->_param->htmlcompress)
			{
				$this->_content = preg_replace('#\n[\s]+\<#s', "\n<", $this->_content);
			}

			// Set body content
			$this->_setBody();
		}

		if ($this->_cache && !$this->_joomApp(true)->isMessageQueue)
		{
			$user = JFactory::getUser();
			if ($user->get('guest'))
			{
				// We need to check again here, because auto-login plug-ins
				// have not been fired before the first aid check.
				($this->_joomGen() >= 3)
					? $this->_cache->store(null, $this->_cache_key)
					: $this->_cache->store(); // J1.5, J2.5
			}
		}
	}

	/*************************************\
	|*|			PRIVATE METHODS			|*|
	\*************************************/

	/**
	 * Detects an AJAX Request.
	 *
	 * @return  boolean
	 */
	private function _isAjaxRequest()
	{
		global $_SERVER;

		return (
			isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
		);
	}

	/**
	 * Check whether a domain name is dummy / temporary or not.
	 *
	 * @return boolean
	 */
	private function _isDummyDomainName($domain_name)
	{
		return(	stripos($domain_name, 'dummy.') !== false ||
				stripos($domain_name, 'example.') !== false ||
				stripos($domain_name, 'temp.') !== false );
	}

	/**
	 * Custom clean cache method.
	 *
	 * @return  void
	 */
	private function _cleanCache($enqueueMessage = false)
	{
		/////////////////////////////////////////
		// Gets the model from cache component //
		JLoader::import('cache', JPATH_ADMINISTRATOR.'/components/com_cache/models');
		$cacheModel = ($this->_joomGen() >= 3)
			? JModelLegacy::getInstance('cache', 'CacheModel')
			: JModel::getInstance('cache', 'CacheModel'); // J1.5, J2.5

		/** @from 1.3.22c
		* Purge and clear cache */
		$cacheModel->purge();
		$cacheModel->clean();
		unset($cacheModel);

		$checkedInItems = '';
		if ($this->_param->autocheckin)
		{
			////////////////////////////////////////////
			// Gets the model from check-in component //
			JLoader::import('checkin', JPATH_ADMINISTRATOR.'/components/com_checkin/models');
			$checkinModel = ($this->_joomGen() >= 3)
				? JModelLegacy::getInstance('checkin', 'CheckinModel')
				: JModel::getInstance('checkin', 'CheckinModel'); // J1.5, J2.5

			/** @from 1.3.25
			* Performs global check-in */
			$checkedInItems = $checkinModel->checkin(
				array_keys($checkinModel->getItems())
			);
			unset($checkinModel);

			$checkedInItems = ', '. $checkedInItems .' item'. ($checkedInItems != 1 ? 's' : '') .' checked-in';
		}

		// Enqueue message (quick icon clicked or if debugging enabled)
		if ($enqueueMessage || $this->_param->debugging)
		{
			$this->_enqueueMessage(
				'Successfully cleaned all caches'. $checkedInItems,
				'notice'
			);
		}
	}

	/**
	 * Clean folder with temporary files.
	 *
	 * @return  void
	 */
	public function _cleanTemp($enqueueMessage = false)
	{
		// build and check path to the temp-folder
		jimport( 'joomla.filesystem.folder' );
		$path = JPATH_ROOT .'/tmp';
		if (!JFolder::exists($path)) return;

		// delete sub-folders
		$folders = JFolder::folders($path);
		foreach ($folders as $folder)
		{
			$folder_path = $path .'/'. $folder;

			// Enqueue message if debugging is enabled
			if (!JFolder::delete($folder_path) && ($enqueueMessage || $this->_param->debugging))
			{
				$this->_enqueueMessage(
					JText::sprintf('JLIB_FILESYSTEM_ERROR_FOLDER_DELETE', $folder_path),
					'error'
				);
			}
		}

		// delete files
		jimport( 'joomla.filesystem.file' );
		$files = JFolder::files($path);
		foreach ($files as $file)
		{
			if ($file == '.' || $file == '..' || $file == 'index.html') continue;
			$file_path = $path .'/'. $file;

			// Enqueue message if debugging is enabled
			if (!JFile::delete($file_path) && ($enqueueMessage || $this->_param->debugging))
			{
				$this->_enqueueMessage(
					JText::sprintf('JLIB_FILESYSTEM_DELETE_FAILED', $file_path),
					'error'
				);
			}
		}
	}

	/**
	 * Add a message to the message queue.
	 *
	 * @from	1.3.23
	 * @param	$msg	string	The message to enqueue.
	 * @param	$type	string	The message type: 'warning' - yellow, 'notice' - blue, 'error' - red, 'message' (or empty) - green. Default is message.
	 *
	 * @return  void
	 */
	private function _enqueueMessage($msg, $type = 'message')
	{
		// Joomla 1.5 Support
		global $mainframe;
		if ($this->_joomGen() >= 2)
		{
			$mainframe = JFactory::getApplication();
		}
		$mainframe->enqueueMessage($msg, $type);
	}

	/**
	 * Return the body content as a single string.
	 *
	 * @from	1.3.28
	 * @return  string
	 */
	private function _getBody()
	{
		if ($this->_joomGen() >= 3)
		{
			// Get body in J3.x
			$mainframe = JFactory::getApplication();
			$this->_content = $mainframe->getBody(false);
		}
		else
		{
			// Get body in J1.5, J2.5
			$this->_content = JResponse::getBody(false);
		}

		return $this->_content;
	}

	/**
	 * Gets current template name.
	 *
	 * @return  mixed	Template name or NULL
	 */
	private function _getTemplate()
	{
		if (null === $this->_template)
		{
			// Get current template name
			$mainframe = JFactory::getApplication();
			$this->_template = $mainframe->getTemplate();
		}

		return $this->_template;
	}

	/**
	 * Redirect from a temporary address like IP-host/~account.
	 *
	 * @return  void
	 */
	private function _ipRedirect()
	{
		// Joomla 1.5 Support
		global $mainframe;
		if ($this->_joomGen() >= 2)
		{
			// Check if client is site
			$mainframe = JFactory::getApplication();
			if( $mainframe->isAdmin() )
			{
				return;
			}
		}

		/** @from 1.4.12
		* Check if the domain name is present in an absolute path */
		if( preg_match('#/domains/([^/]+)/public_html#i', JPATH_BASE, $match) )
		{
			// If domain name is not dummy
			$domain_name = $match[1];
			if( $this->_isDummyDomainName($domain_name) )
			{
				return;
			}

			// Get current host and path
			$juri = JURI::getInstance();
			$host = $juri->getHost();
			$path = $juri->getPath();

			// Detected IP-host (temporary account URL)
			if( filter_var($host, FILTER_VALIDATE_IP) )
			{
				// Add www-prefix if not on sub-domain
				$host = preg_match('#^[^\.]+\..{2,5}$#', $domain_name)
					? 'www.' . $domain_name // redirect to www.domain_name
					: $domain_name; // stay at unchanged domain_name

				// Remove an account name from a path
				$path = preg_replace('#/~[^/]+/*#', '/', $path);

				// Set the new host and path
				$juri->setHost($host);
				$juri->setPath($path);

				// Redirect to the new URI
				$mainframe->redirect($juri->toString());
				$mainframe->close();
			}
		}
	}

	/**
	 * Returns Joomla! Application-related parameters.
	 *
	 * @return  stdObject
	 */
	private function _joomApp($update = false)
	{
		if (null === $this->_joomapp || $update)
		{
			// Joomla 1.5 Support
			global $mainframe;
			if ($this->_joomGen() >= 2)
			{
				$mainframe = JFactory::getApplication();
			}

			// Set isAdmin and count messages in queue
			$this->_joomapp = new stdClass();
			$this->_joomapp->isAdmin = $mainframe->isAdmin();
			$this->_joomapp->isMessageQueue = count($mainframe->getMessageQueue());

			if ($this->_joomGen() >= 3)
			{
				// Get request parameters in J3.x
				$this->_joomapp->method = $mainframe->input->getMethod();
				$this->_joomapp->option = $mainframe->input->getString('option', null);
				$this->_joomapp->task = $mainframe->input->getString('task', null);
			}
			else
			{
				// Set request parameters in J1.5, J2.5
				jimport( 'joomla.environment.request' );
				$this->_joomapp->method = JRequest::getMethod();
				$this->_joomapp->option = JRequest::getString('option', null);
				$this->_joomapp->task = JRequest::getString('task', null);
			}
		}

		return $this->_joomapp;
	}

	/**
	 * Returns "generation" of installed Joomla!
	 *
	 * @return  integer
	 */
	private function _joomGen()
	{
		if (null === $this->_joomgen)
		{
			if (version_compare(JVERSION, '3.0.0', 'ge'))
			{
				$this->_joomgen = 3; // Joomla! 3.0+
			}
			elseif (version_compare(JVERSION, '1.6.0', 'ge'))
			{
				$this->_joomgen = 2; // Joomla! 1.6+
			}
			else
			{
				$this->_joomgen = 1; // Joomla! 1.5
			}
		}

		return $this->_joomgen;
	}

	/**
	 * Render "Quick Icon" above the fold in Joomla! Administrator panel
	 *
	 * @from 1.2.28
	 * @return  $this
	 */
	private function _renderQuickIcon($body = null)
	{
		if (null !== $body)
		{
			$this->_content = $body;
		}

		if ($this->_joomGen() >= 3)
		{
			// RTL support
			$style = JFactory::getLanguage()->isRTL()
				? 'float:left; margin-right:16px'
				: 'float:right; margin-left:16px';

			$mainframe = JFactory::getApplication();
			$this->_content = str_replace('<div class="container-logo">', '<form
		method="post" action="" style="'. $style .'; cursor:pointer" title="'. JText::_('JGLOBAL_SUBMENU_CLEAR_CACHE') .'">
			<a href="index.php?option=com_cache" onclick="this.parentNode.submit(); return false">
				<span class="page-title icon-lightning"></span>
			</a>
			<input type="hidden" name="option" value="'. $mainframe->input->getString('option', 'com_cpanel') .'">
			<input type="hidden" name="task" value="null">
			<input type="hidden" name="'. JSession::getFormToken() .'" value="1">
		</form>
		<div class="container-logo">', $this->_content);
		}
		else
		{
			$this->_content = str_replace('<div id="module-status">', '<div id="module-status">
		<span class="clear-cache icon-16-clear" style="background-repeat:no-repeat; background-position:3px 3px">
			<form method="post" action="" style="display:inline">
				<a href="index.php?option=com_cache" onclick="this.parentNode.submit(); return false">
					'. JText::_('JGLOBAL_SUBMENU_CLEAR_CACHE') .'
				</a>
				<input type="hidden" name="option" value="'. JRequest::getString('option', 'com_cpanel') .'">
				<input type="hidden" name="task" value="null">
				<input type="hidden" name="'. JSession::getFormToken() .'" value="1">
			</form>
		</span>', $this->_content);
		}

		return $this;
	}

	/**
	 * Set body content.
	 *
	 * @from	1.3.28
	 * @return  void
	 */
	private function _setBody($body = null)
	{
		if (null !== $body)
		{
			$this->_content = $body;
		}

		if ($this->_joomGen() >= 3)
		{
			// Set body in J3.x
			$mainframe = JFactory::getApplication();
			$mainframe->setBody($this->_content);
		}
		else
		{
			// Set body in J1.5, J2.5
			JResponse::setBody($this->_content);
		}
	}

	/**
	 * Clear the cache on non-login/logout POST requests in administrator panel.
	 *
	 * @return  void
	 */
	private function _smartAutoCleanAdmin()
	{
		/** @from 1.3.2
		* Automagically clear the cache on non-login/logout POST requests */
		if ($this->_joomApp()->method == 'POST')
		{
			$option = $this->_joomApp()->option;
			$task = $this->_joomApp()->task;
			if ($option && $task && $task != 'login' && $task != 'logout')
			{
				$this->_cleanCache($task === 'null');

				/** @from 1.3.22
				* Delete temporary files */
				if ($task === 'null')
				{
					// Check token
					if (($this->_joomGen() >= 3)
						? JSession::checkToken() // J2.5.4+
						: JRequest::checkToken())
					{
						$this->_cleanTemp(true);
					}
				}
			}
		}
	}

	/**
	 * Smart auto clean is triggered on client site when you update the PHP files of the template.
	 *
	 * @return  void
	 */
	private function _smartAutoCleanSite()
	{
		/** @from 1.2.28
		* Build path to the current template folder */
		$templateFolder = JPATH_ROOT .'/templates/'. $this->_getTemplate();
		$templateIndexFile = $templateFolder .'/index.php';

		// Clean cache if modification time is less than 90 seconds
		if (time() - @filemtime($templateFolder) < 90)
		{
			$this->_cleanCache();
		}
		else {
			/** @from 1.4.6c
			* Build extensions filter RegEx from a raw comma-separated list */
			$filterRegex = '.'; /* no filtering by default */
			if ($this->_param->extfilter && $this->_param->extfilter != '*') {
				$extfilter_valid = preg_replace('#[^\,a-zA-Z0-9\._\-]+#', '', $this->_param->extfilter);
				if ($extfilter_array = array_filter(explode(',', $extfilter_valid), 'trim')) {
					$filterRegex = '(?i)\.('.implode('|', $extfilter_array).')$';
				}
			}

			// Fetch files list recursive from template folder
			jimport( 'joomla.filesystem.folder' );
			if ($templateFiles = JFolder::files($templateFolder, $filter = $filterRegex, $recurse = true, $fullpath = true))
			{
				foreach ($templateFiles as $templateFileName)
				{
					/** @from 1.3.4 - "Smart Cache"
					* Check if modification time is less than 180 seconds */
					if (time() - @filemtime($templateFileName) < 180)
					{
						// Update template folder modification time
						if ( !@touch($templateFolder) ) {
							if (@copy($templateIndexFile, $templateIndexFile.'.c$n')) {
								@unlink($templateIndexFile.'.c$n');
							}
						}
						$this->_cleanCache();
						break;
					}
				}
			}
		}
	}

}
