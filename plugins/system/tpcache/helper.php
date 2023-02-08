<?php
/**
 * @package TPCache Helper
 * @version $Id: helper.php,v 1.4.7 2015-04-07 10:40:00 elizovsky Exp $;
 *
 * @author		Alex Segal <elizovsky@gmail.com>
 * @copyright	Copyright (C) 2015 Alex Segal
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;

class TPCacheHelper
{
	/**
	 * Method to add the merged URL to the HTML-document
	 *
	 * @param string $body
	 * @param array  $matches
	 *
	 * @return string
	 */
	static public function addMergeUrl($body = null, $matches = array())
	{
		// Treat CSS and JS seperately
		foreach ($matches as $type => $list)
		{
			if (!empty($list))
			{
				// Create an unique signature for this filelist
				$url = TPCacheHelper::buildCacheUrl($type, $list);

				if ($type == 'css')
				{
					$tag = '<link rel="stylesheet" href="' . $url . '" type="text/css" />';
					$body = str_replace('</head>', $tag . '</head>', $body);
					$body = str_replace('<!-- plg_tpcache_' . md5($type) . ' -->', '', $body);
				}
				else
				{
					$tag = '<script type="text/javascript" src="' . $url . '"' . /*($this->_defer_js ? '  defer=" defer"' : '') .*/ '></script>';
					$body = str_replace('<!-- plg_tpcache_' . md5($type) . ' -->', $tag, $body);
				}
			}
		}

		return $body;
	}

	/**
	 * Method to build the CSS / JavaScript cache
	 *
	 * @param string $type
	 * @param array  $matches
	 *
	 * @return string
	 */

	static public function buildCacheUrl($type, $list = array())
	{
		// Check for the cache-path
		$tmp_path = JPATH_SITE . '/cache/plg_tpcache/';

		if (@is_dir($tmp_path) == false)
		{
			jimport( 'joomla.filesystem.folder' );
			JFolder::create($tmp_path);
		}

		if (!empty($list))
		{
			$cacheId = md5(var_export($list, true));
			$cacheFile = $cacheId . '.' . $type;
			$cachePath = $tmp_path . '/' . $cacheFile;

			$hasExpired = false;
			if (TPCacheHelper::hasExpired($cachePath, 180))
			{
				$hasExpired = true;
			}

			// @todo: Make this optional
			foreach ($list as $file)
			{
				if (@filemtime($file['file']) > @filemtime($cachePath))
				{
					$hasExpired = true;
					break;
				}
			}

			// Check the cache
			if ($hasExpired)
			{
				$buffer = null;

				foreach ($list as $file)
				{
					if (isset($file['file']))
					{
						if( $type == 'css' ) {
							$buffer .= TPCacheHelper::getCssContent($file['file']); // CSS-code
						}
						else {
							$buffer .= TPCacheHelper::getJsContent($file['file']); // JS-code
						}
					}
				}

				// Clean up the final CSS-code
				if( $type == 'css' )
				{
					// Move all @import-lines to the top of the CSS-file
					$regexp = '/@import[^;]+;/i';
					if (preg_match_all($regexp, $buffer, $matches)) {
						$buffer = preg_replace($regexp, '', $buffer);
						$buffer = implode("\n", $matches[0])."\n".$buffer;
					}
				}

				// Write this buffer to a file
				jimport( 'joomla.filesystem.file' );
				JFile::write($cachePath, $buffer);
			}
		}

		// Return the cache-file itself
		$url = JURI::root() . 'cache/plg_tpcache/' . $cacheFile;

		if (JURI::getInstance()->isSSL())
		{
			$url = str_replace('http://', 'https://', $url);
		}
		else
		{
			$url = str_replace('https://', 'http://', $url);
		}

		return $url;
	}

	/**
	 * Method to remove obsolete tags in the HTML body
	 *
	 * @param string $body
	 * @param array  $files
	 *
	 * @return string
	 */
	static public function removeObsoleteTags($body = null, $matches = array())
	{
		foreach ($matches as $typename => $type)
		{
			if (!empty($type))
			{
				$first = true;

				foreach ($type as $file)
				{
					if ($first)
					{
						$replacement = '<!-- plg_tpcache_' . md5($typename) . ' -->';
						$first = false;
					}
					else
					{
						$replacement = '';
					}

					$body = str_replace($file['html'], $replacement, $body);
				}
			}
		}

		return $body;
	}

	/**
	 * Method to detect all the CSS style sheets in the HTML-body
	 *
	 * @param string $body
	 * @return array
	 */
	static public function getCssMatches($body = null)
	{
		// Remove conditional comments from matching
		$buffer = preg_replace('/<!--(.*)-->/msU', '', $body);

		// Detect all CSS
		preg_match_all('#<link(.*)href=(["\']+)([^\"\']+)(["\']+)([^>]+)>#msiU', $buffer, $matches);

		// Parse all the matched entries
		$files = array();

		if (isset($matches[3]))
		{
			// Loop through the rules
			foreach ($matches[3] as $index => $match)
			{
				$m0i = $matches[0][$index];

				// Skip certain entries
				if (stripos($m0i, 'stylesheet') === false && stripos($m0i, 'css') === false)
				{
					continue;
				}

				if (stripos($m0i, 'media="print"'))
				{
					continue;
				}

				$match = str_replace(JURI::base(), '', $match);
				$match = preg_replace('#^' . str_replace('/', '\/', JURI::base(true)) . '#', '', $match);

				// Only try to match local CSS
				if (preg_match('#^http:\/\/#', $match))
				{
					continue;
				}

				/** @from 1.3.30 * FoxContact-style includes support */
				if (preg_match('#\.css(\?\w+=\w+)?$#i', $match) || preg_match('#\/css\/?$#i', $match))
				{
					// Only include files that can be read
					$file = preg_replace('#\?(.*)#', '', $match);

					// Try to determine the path to this file
					$filepath = TPCacheHelper::getFilePath($file);

					if (!empty($filepath))
					{
						$files[] = array(
							'remote' => 0,
							'file' => $filepath,
							'html' => $matches[0][$index],
						);
					}
				}
			}
		}

		return $files;
	}

	/**
	 * Method to detect all the JavaScript-scripts in the HTML-body
	 *
	 * @param string $body
	 *
	 * @return array
	 */
	static public function getJsMatches($body = null)
	{
		// Remove conditional comments from matching
		$buffer = preg_replace('#<!--(.*)-->#msU', '', $body);

		// Detect all JavaScripts
		preg_match_all('#<script([^\>]+)src="([^\"]+)"(.*)><\/script>#msiU', $buffer, $matches);

		// Add extra scripts in the backend
		$application = JFactory::getApplication();

		// Parse all the matched entries
		$files = array();

		if (isset($matches[2]))
		{
			foreach ($matches[2] as $index => $match)
			{
				// Only try to match local JavaScript
				$match = str_replace(JURI::base(), '', $match);
				$match = preg_replace('#^' . str_replace('/', '\/', JURI::base(true)) . '#', '', $match);

				if (empty($match))
				{
					continue;
				}

				// Only try to match local JS
				if (preg_match('#^http:\/\/#', $match))
				{
					continue;
				}

				/** @from 1.3.30 * FoxContact-style includes support */
				if (preg_match('#\.js(\?\w+=\w+)?$#i', $match) || preg_match('#\/js\/?$#i', $match))
				{
					// Only include files that can be read
					$file = preg_replace('#\?(.*)#', '', $match);

					// Try to determine the path to this file
					$filepath = TPCacheHelper::getFilePath($file);

					if (!empty($filepath))
					{
						$add = true;

						if ($add)
						{
							$files[] = array(
								'remote' => 0,
								'file' => $filepath,
								'html' => $matches[0][$index],
							);
						}
					}
				}
			}
		}

		return $files;
	}

	/**
	 * Method to return the output of a CSS file
	 *
	 * @param string $string
	 * @return string
	 */
	static public function getCssContent($file, $follow_imports = true, $compress_css = false)
	{
		// Only inlude a file once
		static $parsed_files = array();
		if (in_array($file, $parsed_files)) {
			return " ";
		}
		$parsed_files[] = $file;

		// Don't try to parse empty (or non-existing) files
		if (empty($file)) return null;

		/** @from 1.3.30 * commented for new features support
		* if (@is_readable($file) == false) return null; */

		// Skip files that have already been included
		static $files = array();
		if (in_array($file, $files)) {
			return null;
		} else {
			$files[] = $file;
		}

		// Initialize the buffer
		$buffer = @file_get_contents($file);
		if (empty($buffer)) return null;

		// Create a raw buffer with comments stripped
		$regex = array(
			"`^([\t\s]+)`ism"=>'',
			"`^\/\*(.+?)\*\/`ism"=>"",
			"`([\n\A;]+)\/\*(.+?)\*\/`ism"=>"$1",
			"`([\n\A;\s]+)//(.+?)[\n\r]`ism"=>"$1\n",
			"`(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+`ism"=>"\n"
		);
		$rawBuffer = preg_replace(array_keys($regex), $regex, $buffer);

		// Initialize the basepath
		$basefile = TPCacheHelper::getFileUrl($file, false);

		// Follow all @import rules
		$imports = array();
		if ($follow_imports) {
			if (preg_match_all('#@import\ (.*);#i', $rawBuffer, $matches)) {
				foreach ($matches[1] as $index => $match) {

					// Strip quotes
					$match = str_replace('url(', '', $match);
					$match = str_replace('\'', '', $match);
					$match = str_replace('"', '', $match);
					$match = str_replace(')', '', $match);
					$match = trim($match);

					// Skip URLs and data-URIs
					if (preg_match('#^(http|https):\/\/#', $match)) continue;

					$importFile = TPCacheHelper::getFilePath($match, $file);
					if (empty($importFile) && strstr($importFile, '/') == false) $importFile = dirname($file).'/'.$match;
					$importBuffer = TPCacheHelper::getCssContent($importFile);
					$importUrl = TPCacheHelper::getFileUrl($importFile, false);

					if (!empty($importBuffer)) {
						TPCacheHelper::comment($buffer, "CSS import of {$importUrl}", null, 'Notice');
						$buffer .= "\n". $importBuffer ."\n";
						$buffer = str_replace($matches[0][$index], "\n", $buffer);
						$imports[] = $matches[1][$index];

					} else {
						TPCacheHelper::comment($buffer, "CSS import of {$importUrl} returned empty", null, 'Error');
					}
				}
			}
		}

		// Replace all relative paths with absolute paths
		if (preg_match_all('#url\(([^\(]+)\)#i', $rawBuffer, $url_matches)) {
			foreach ($url_matches[1] as $url_index => $url_match) {

				// Strip quotes
				$url_match = str_replace('\'', '', $url_match);
				$url_match = str_replace('"', '', $url_match);

				// Skip CSS-stylesheets which need to be followed differently anyway
				if (strstr($url_match, '.css')) continue;

				// Skip URLs and data-URIs
				if (preg_match('#^(http|https):\/\/#', $url_match)) continue;
				if (preg_match('#^\/\/#', $url_match)) continue;
				if (preg_match('#^data\:#', $url_match)) continue;

				// Normalize this path
				$url_match_path = TPCacheHelper::getFilePath($url_match, $file);
				if (empty($url_match_path) && strstr($url_match, '/') == false) $url_match_path = dirname($file).'/'.$url_match;
				if (!empty($url_match_path)) $url_match = TPCacheHelper::getFileUrl($url_match_path);

				$buffer = str_replace($url_matches[0][$url_index], 'url('.$url_match.')', $buffer);
			}
		}

		// Move all @import-lines to the top of the CSS-file
		$regexp = '#@import (.*);#i';
		if (preg_match_all($regexp, $rawBuffer, $matches)) {
			$buffer = preg_replace($regexp, '', $buffer);
			$matches[0] = array_unique($matches[0]);
			foreach($matches[0] as $index => $match) {
				if (in_array($matches[1][$index], $imports)) {
					unset($matches[0][$index]);
				}
			}
			$buffer = implode("\n", $matches[0])."\n".$buffer;
		}

		if ($compress_css)
		{
			// Compression is enabled
			$buffer = preg_replace('#[\r\n\t\s]+//[^\n\r]+#', ' ', $buffer);
			$buffer = preg_replace('#[\r\n\t\s]+#s', ' ', $buffer);
			$buffer = preg_replace('#/\*.*?\*/#', '', $buffer);
			$buffer = preg_replace('#[\s]*([\{\},;:])[\s]*#', '\1', $buffer);
			$buffer = preg_replace('#^\s+#', '', $buffer);
			$buffer .= "\n";
		}
		else
		{
			// Append the CSS-stylesheet filename to the CSS-code
			TPCacheHelper::comment($buffer, $basefile);
		}

		return $buffer;
	}

	/**
	 * Method to return the output of a JavaScript file
	 *
	 * @param string $string
	 * @return string
	 */
	static public function getJsContent($file)
	{
		// Don't try to parse empty (or non-existing) files
		if (empty($file)) return null;

		/** @from 1.3.30 * commented for new features support
		if (@is_readable($file) == false) return null; */

		// Initialize the buffer
		$buffer = @file_get_contents($file);
		if (empty($buffer)) return null;

		// Initialize the basepath
		$basefile = TPCacheHelper::getFileUrl($file, false);

		// JS-compression is disabled //

		// Make sure the JS-content ends with ;
		$buffer = trim($buffer);
		if (preg_match('#;\$#', $buffer) == false) $buffer .= ';'."\n";

		// Remove extra semicolons
		$buffer = preg_replace("#;;\n#", ';', $buffer);

		// Append the filename to the JS-code
		TPCacheHelper::comment($buffer, $basefile);

		// Detect jQuery
		if (strstr($buffer, 'define("jquery",')) {
			$buffer .= "jQuery.noConflict();\n";
		}

		return $buffer;
	}

	/**
	 * Check if the cache has expired
	 *
	 * @param	string	$cacheFile
	 * @param	integer	$cacheExpireTime
	 * @return	null
	 */
	static public function hasExpired($cacheFile, $cacheExpireTime = 180)
	{
		// Check for browser request
		if (isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] == 'no-cache') {
			return true;
		}

		// Check if cache file is exists
		if (file_exists($cacheFile) && @is_file($cacheFile)) {
			if (time() - @filemtime($cacheFile) > $cacheExpireTime) {
				jimport( 'joomla.filesystem.file' );
				JFile::delete($cacheFile);
				return true;
			}
			return false;
		}

		return true;
	}

	/**
	 * Get a valid filename
	 *
	 * @param string $file
	 * @param string $base_path
	 * @return string
	 */
	static public function getFilePath($file, $base_path = null)
	{
		// If this begins with a data URI, skip it
		if (preg_match('#^data\:#', $file)) {
			return null;
		}

		/** @from 1.3.30
		* If path begins with /index.php, return it as URL */
		if (preg_match('#^\/?index\.php#', $file)) {
			return JURI::root().trim($file, '/');
		}

		// Strip any URL parameter from this
		$file = preg_replace('#\?(.*)#', '', $file);

		// If this is already a correct path, return it
		if (@is_file($file) && @is_readable($file)) {
			return realpath($file);
		}

		// Strip the base-URL from this path
		$file = str_replace(JURI::root(), '', $file);

		// Make sure the basepath is not a file
		if ($base_path !== null && @is_file($base_path)) {
			$base_path = dirname($base_path);
		}

		// Determine the basepath
		if (empty($base_path)) {
			$base_path = JPATH_SITE;
		}

		// Append the root
		if (@is_file(JPATH_SITE.'/'.$file)) {
			return realpath(JPATH_SITE.'/'.$file);
		}

		// Append the base_path
		if (!empty($base_path) && strstr($file, $base_path) == false) {
			$file = $base_path.'/'.$file;
			if (@is_file($file)) {
				return realpath($file);
			}
		}

		// Detect the right application-path
		if (strstr($file, JPATH_SITE) == false && @is_file(JPATH_SITE.'/'.$file)) {
			$file = JPATH_SITE.'/'.$file;
		}

		// If this is not a file, return empty
		if (@is_file($file) == false || @is_readable($file) == false) {
			return null;
		}

		return realpath($file);
	}

	/**
	 * Get a valid file URL
	 *
	 * @param	string $path
	 * @return	string
	 */
	static public function getFileUrl($path, $include_url = true)
	{
		$path = str_replace(JPATH_SITE.'/', '', $path);

		if ($include_url) {
			$path = JURI::root().$path;
		}

		if (JURI::getInstance()->isSSL()) {
			$path = str_replace('http://', 'https://', $path);
		} else {
			$path = str_replace('https://', 'http://', $path);
		}

		return $path;
	}

	/**
	 * Generates a multi-line comment (a comment block).
	 *
	 * return	void
	 */
	static public function comment(& $buffer, $comment, $start = 'Start', $end = 'End')
	{
		// Append the comment to the code
		if (null !== $start) $start = "/*/--[TPCache/{$start}]--> {$comment} */\n";
		if (null !== $end) $end = "\n/*/--[TPCache/{$end}]--> {$comment} */\n\n";
		
		$buffer = $start . trim($buffer) . $end;
	}

}
