<?php
/**
 * @version     2.2.2
 * @package     com_vitabook
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomVita - http://www.joomvita.com
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Import helper.
 */
abstract class VitabookHelperImport
{
    /**
    * Method parse easybook messages into proper html
    * @input array with easybook messages
    * @return array with html formatted messages
    */
    public static function parseMessages($messages)
    {
        foreach($messages as &$message)
        {         
            $message['message'] = VitabookHelperImport::parseMessage($message['message']);
        }
        
        return $messages;
    }
    
    /**
    * Method create html message
    * @input unformatted message text
    * @return html formatted message text
    */    
    private static function parseMessage($text)
    {
        $text = VitabookHelperImport::createHtmlTags($text);
        $text = VitabookHelperImport::convertBbCode($text);
        $text = VitabookHelperImport::replaceSmilies($text);
        
        $out  = '<p>';
        $out .= $text;
        $out .= '</p>';
        
        return $out;
    }
    
    // Convert CR and LF to HTML BR command
    private static function createHtmlTags($text)
    {
        return preg_replace('@(\015\012)|(\015)|(\012)@', '<br />', $text);
    }
    
    /**
    * Method to convert BBcode to correct HTML
    * @input string $text
    * @return converted string $text
    */   
    private static function convertBbCode($text)
    {
        $text = preg_replace('@\[quote\](.*?)\[/quote]@si', '<strong>Quote:</strong><br /><blockquote>\\1</blockquote>', $text);
        $text = preg_replace('@\[b\](.*?)\[/b\]@si', '<strong>\\1</strong>', $text);
        $text = preg_replace('@\[i\](.*?)\[/i\]@si', '<i>\\1</i>', $text);
        $text = preg_replace('@\[u\](.*?)\[/u\]@si', '<u>\\1</u>', $text);
        $text = preg_replace('@\[center\](.*)\[/center\]@siU', '<p style="text-align:center;">\\1</p>', $text);

        $text = preg_replace('@\[url=(http://)?(.*?)\](.*?)\[/url\]@si', '<a href="http://\\2" title="\\3" rel="nofollow" target="_blank">\\3</a>', $text);

        $text = preg_replace('@\[CODE=?(.*?)\](<br />)*(.*?)(<br />)*\[/code\]@si', '<pre xml:\\1>\\3</pre>', $text);

        if(preg_match_all('@\[email\](.*?)\[/email\]@si', $text, $matches))
        {
            foreach($matches[1] as $value)
            {
                $text = preg_replace('@\[email\](.*?)\[/email\]@si', JHtml::_('email.cloak', $value), $text);
            }
        }

        $text = preg_replace('@\[img\](.*)\[/img\]@siU', '<img src="\\1" alt="\\1"  title="\\1" />', $text);
        $text = preg_replace('@\[imglink=(http://)?(.*?)\](.*?)\[/imglink\]@si', '<a href="http://\\2" title="\\2" rel="nofollow" target="_blank"><img src="\\3" alt="\\3" /></a>', $text);

        preg_match_all('@\[youtube\](.*)\[/youtube\]@siU', $text, $matches);

        if(!empty($matches[1]))
        {
            $count = 0;

            foreach($matches[1] as $match)
            {
                if(preg_match('@v=([^&]+)&?.*@', $match, $video_id))
                {
                    $match = $video_id[1];
                }

                $text = str_replace($matches[0][$count], '[youtube]'.$match.'[/youtube]', $text);

                $count++;
            }

            $text = preg_replace('@\[youtube\](.*)\[/youtube\]@siU', '<iframe src="http://www.youtube.com/embed/\\1" frameborder="0" allowfullscreen></iframe>', $text);
        }


        $matchCount = preg_match_all('@\[list\](.*?)\[/list\]@si', $text, $matches);

        for($i = 0; $i < $matchCount; $i++)
        {
            $currMatchTextBefore = preg_quote($matches[1][$i]);
            $currMatchTextAfter = preg_replace('@\[\*\]@si', '<li>', $matches[1][$i]);
            $text = preg_replace('@\[list\]'.$currMatchTextBefore.'\[/list\]@si', '<ul>'.$currMatchTextAfter.'</ul>', $text);
        }

        $matchCount = preg_match_all('@\[list=([a1])\](.*?)\[/list\]@si', $text, $matches);

        for($i = 0; $i < $matchCount; $i++)
        {
            $currMatchTextBefore = preg_quote($matches[2][$i]);
            $currMatchTextAfter = preg_replace('@\[\*\]@si', '<li>', $matches[2][$i]);
            $text = preg_replace('@\[list=([a1])\]'.$currMatchTextBefore.'\[/list\]@si', '<ol type=\\1>'.$currMatchTextAfter.'</ol>', $text);
        }

        return $text;
    }
    
    /**
    * Method to replace all BBcode smilies into images
    * @input string $text
    * @return converted string $text
    */  
    static function replaceSmilies($text)
    {
        $smiley = VitabookHelperImport::getSmilies();

        // Save code blocks temporarily in an array to avoid replacement
        preg_match_all('@\[code=?(.*?)\](<br />)*(.*?)(<br />)*\[/code\]@si', $text, $matches);

        $i = 0;

        foreach($matches[0] as $match)
        {
            str_replace($match, '[codetemp'.$i.']', $text);
            $i++;
        }

        foreach($smiley as $i => $sm)
        {
            $text = str_replace($i, '<img src="components/com_vitabook/assets/vitabookemoticons/img/smilies/'.$sm.'" alt="" border="0" class="smiley" />', $text);
        }

        // Reset all code blocks
        $i = 0;

        foreach($matches[0] as $match)
        {
            $text = str_replace('[codetemp'.$i.']', $match, $text);
            $i++;
        }

        return $text;
    }
    
    /**
    * Method to make an array of smilies with corresponding image
    * @input
    * @return array with smilies
    */  
    static function getSmilies()
    {
        $smilies = array();

        $smilies[':zzz'] = 'sm_sleep.gif';
        $smilies[';)'] = 'sm_wink.gif';
        $smilies[';-)'] = 'sm_wink.gif';
        $smilies['8)'] = 'sm_cool.gif';
        $smilies[':p'] = 'sm_razz.gif';
        $smilies[':P'] = 'sm_razz.gif';
        $smilies[':roll'] = 'sm_rolleyes.gif';
        $smilies[':eek'] = 'sm_bigeek.gif';
        $smilies[':grin'] = 'sm_biggrin.gif';
        $smilies[':D'] = 'sm_biggrin.gif';
        $smilies[':d'] = 'sm_biggrin.gif';
        $smilies[':)'] = 'sm_smile.gif';
        $smilies[':-)'] = 'sm_smile.gif';
        $smilies[':sigh'] = 'sm_sigh.gif';
        $smilies[':?'] = 'sm_confused.gif';
        $smilies[':cry'] = 'sm_cry.gif';
        $smilies[':('] = 'sm_mad.gif';
        $smilies[':-('] = 'sm_mad.gif';
        $smilies[':x'] = 'sm_dead.gif';
        $smilies[':upset'] = 'sm_upset.gif';

        return $smilies;
    }
}