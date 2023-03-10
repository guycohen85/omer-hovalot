<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * HTML View class for the search component
 *
 * @package     Joomla.Site
 * @subpackage  com_search
 * @since       1.0
 */
class SearchViewSearch extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/search.php';

		$app     = JFactory::getApplication();
		$uri     = JUri::getInstance();
		$error   = null;
		$rows    = null;
		$results = null;
		$total   = 0;

		// Get some data from the model
		$areas      = $this->get('areas');
		$state      = $this->get('state');
		$searchword = $state->get('keyword');
		$params     = $app->getParams();

		$menus = $app->getMenu();
		$menu  = $menus->getActive();

		// Because the application sets a default page title, we need to get it right from the menu item itself
		if (is_object($menu))
		{
			$menu_params = new JRegistry;
			$menu_params->loadString($menu->params);

			if (!$menu_params->get('page_title'))
			{
				$params->set('page_title', JText::_('COM_SEARCH_SEARCH'));
			}
		}
		else
		{
			$params->set('page_title', JText::_('COM_SEARCH_SEARCH'));
		}

		$title = $params->get('page_title');

		if ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($params->get('menu-meta_description'))
		{
			$this->document->setDescription($params->get('menu-meta_description'));
		}

		if ($params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $params->get('menu-meta_keywords'));
		}

		if ($params->get('robots'))
		{
			$this->document->setMetadata('robots', $params->get('robots'));
		}

		// Built select lists
		$orders   = array();
		$orders[] = JHtml::_('select.option', 'newest', JText::_('COM_SEARCH_NEWEST_FIRST'));
		$orders[] = JHtml::_('select.option', 'oldest', JText::_('COM_SEARCH_OLDEST_FIRST'));
		$orders[] = JHtml::_('select.option', 'popular', JText::_('COM_SEARCH_MOST_POPULAR'));
		$orders[] = JHtml::_('select.option', 'alpha', JText::_('COM_SEARCH_ALPHABETICAL'));
		$orders[] = JHtml::_('select.option', 'category', JText::_('JCATEGORY'));

		$lists             = array();
		$lists['ordering'] = JHtml::_('select.genericlist', $orders, 'ordering', 'class="inputbox"', 'value', 'text', $state->get('ordering'));

		$searchphrases         = array();
		$searchphrases[]       = JHtml::_('select.option', 'all', JText::_('COM_SEARCH_ALL_WORDS'));
		$searchphrases[]       = JHtml::_('select.option', 'any', JText::_('COM_SEARCH_ANY_WORDS'));
		$searchphrases[]       = JHtml::_('select.option', 'exact', JText::_('COM_SEARCH_EXACT_PHRASE'));
		$lists['searchphrase'] = JHtml::_('select.radiolist', $searchphrases, 'searchphrase', '', 'value', 'text', $state->get('match'));

		// Log the search
		JSearchHelper::logSearch($searchword, 'com_search');

		// Limit searchword
		$lang        = JFactory::getLanguage();
		$upper_limit = $lang->getUpperLimitSearchWord();
		$lower_limit = $lang->getLowerLimitSearchWord();

		if (SearchHelper::limitSearchWord($searchword))
		{
			$error = JText::sprintf('COM_SEARCH_ERROR_SEARCH_MESSAGE', $lower_limit, $upper_limit);
		}

		// Sanitise searchword
		if (SearchHelper::santiseSearchWord($searchword, $state->get('match')))
		{
			$error = JText::_('COM_SEARCH_ERROR_IGNOREKEYWORD');
		}

		if (!$searchword && !empty($this->input) && count($this->input->post))
		{
			// $error = JText::_('COM_SEARCH_ERROR_ENTERKEYWORD');
		}

		// Put the filtered results back into the model
		// for next release, the checks should be done in the model perhaps...
		$state->set('keyword', $searchword);

		if ($error == null)
		{
			$results    = $this->get('data');
			$total      = $this->get('total');
			$pagination = $this->get('pagination');

			require_once JPATH_SITE . '/components/com_content/helpers/route.php';

			for ($i = 0, $count = count($results); $i < $count; $i++)
			{
				$row = & $results[$i]->text;

				if ($state->get('match') == 'exact')
				{
					$searchwords = array($searchword);
					$needle      = $searchword;
				}
				else
				{
					$searchworda = preg_replace('#\xE3\x80\x80#s', ' ', $searchword);
					$searchwords = preg_split("/\s+/u", $searchworda);
					$needle      = $searchwords[0];
				}

				$row         = SearchHelper::prepareSearchContent($row, $needle);
				$searchwords = array_values(array_unique($searchwords));
				$srow        = strtolower(SearchHelper::remove_accents($row));
				$hl1         = '<span class="highlight">';
				$hl2         = '</span>';
				$cnt         = 0;

				foreach ($searchwords as $hlword)
				{
					if (extension_loaded('mbstring'))
					{
						if (($pos = mb_strpos($srow, strtolower(SearchHelper::remove_accents($hlword)))) !== false)
						{
							$pos += $cnt * mb_strlen($hl1 . $hl2);
							$cnt++;

							// iconv transliterates '???' to 'EUR'
							// TODO: add other expanding translations?
							$eur_compensation = $pos > 0 ? substr_count($row, "\xE2\x82\xAC", 0, $pos) * 2 : 0;
							$pos -= $eur_compensation;
							$row = mb_substr($row, 0, $pos) . $hl1 . mb_substr($row, $pos, mb_strlen($hlword)) . $hl2 . mb_substr($row, $pos + mb_strlen($hlword));
						}
					}
					else
					{
						if (($pos = JString::strpos($srow, strtolower(SearchHelper::remove_accents($hlword)))) !== false)
						{
							$pos += $cnt * JString::strlen($hl1 . $hl2);
							$cnt++;

							// iconv transliterates '???' to 'EUR'
							// TODO: add other expanding translations?
							$eur_compensation = $pos > 0 ? substr_count($row, "\xE2\x82\xAC", 0, $pos) * 2 : 0;
							$pos -= $eur_compensation;
							$row = JString::substr($row, 0, $pos) . $hl1 . JString::substr($row, $pos, JString::strlen($hlword)) . $hl2 . JString::substr($row, $pos + JString::strlen($hlword));
						}
					}
				}

				$result = & $results[$i];

				if ($result->created)
				{
					$created = JHtml::_('date', $result->created, JText::_('DATE_FORMAT_LC3'));
				}
				else
				{
					$created = '';
				}

				$result->text    = JHtml::_('content.prepare', $result->text, '', 'com_search.search');
				$result->created = $created;
				$result->count   = $i + 1;
			}
		}

		// Check for layout override
		$active = JFactory::getApplication()->getMenu()->getActive();

		if (isset($active->query['layout']))
		{
			$this->setLayout($active->query['layout']);
		}

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));
		$this->pagination    = &$pagination;
		$this->results       = &$results;
		$this->lists         = &$lists;
		$this->params        = &$params;
		$this->ordering      = $state->get('ordering');
		$this->searchword    = $searchword;
		$this->origkeyword   = $state->get('origkeyword');
		$this->searchphrase  = $state->get('match');
		$this->searchareas   = $areas;
		$this->total         = $total;
		$this->error         = $error;
		$this->action        = $uri;

		parent::display($tpl);
	}
}
