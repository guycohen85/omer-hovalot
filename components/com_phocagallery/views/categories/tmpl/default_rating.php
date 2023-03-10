<?php
defined('_JEXEC') or die('Restricted access');

// Rating

if ($this->tmpl['display_rating'] == 1) {
	$votesCount = $votesAverage = $votesWidth = 0;
	if (!empty($this->cv->ratingcount)) {
		$votesCount = $this->cv->ratingcount;
	}
	if (!empty($this->cv->ratingaverage)) {
		$votesAverage = $this->cv->ratingaverage;
		if ($votesAverage > 0) {
			$votesAverage 	= round(((float)$votesAverage / 0.5)) * 0.5;
			$votesWidth		= 16 * $votesAverage;
		}
		
	}
	if ((int)$votesCount > 1) {
		$votesText = 'COM_PHOCAGALLERY_VOTES';
	} else {
		$votesText = 'COM_PHOCAGALLERY_VOTE';
	}
	
	echo '<div class="pg-csv-rate">' . JText::_('COM_PHOCAGALLERY_RATING'). ': '
		. $votesAverage .' / '.$votesCount . ' ' . JText::_($votesText). '</div>'
		.' <ul class="star-rating-small">'
		.'  <li class="current-rating" style="width:'.$votesWidth.'px"></li>'
		.'   <li><span class="star1"></span></li>';
	for ($r = 2;$r < 6;$r++) {
		echo '<li><span class="stars'.$r.'"></span></li>';
	}
	echo '</ul>'."\n";
}
?>