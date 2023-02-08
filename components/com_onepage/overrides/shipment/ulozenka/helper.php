<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

class UlozenkaHelper {
  public static $lastError; 
  
  public static function getPobocky(&$params, $cache=true)
   {
   
       if (isset(self::$pobocky_cache)) return self::$pobocky_cache; 
	   
       jimport('joomla.filesystem.file');
	   jimport('joomla.filesystem.folder');
		
		$retfalse = new stdClass(); 
		
		$document = JFactory::getDocument(); 
		
		if ($cache)
		if (!file_exists(JPATH_ROOT.DS.'cache'.DS.'ulozenka'))
		 {
		   if (@JFolder::create(JPATH_ROOT.DS.'cache'.DS.'ulozenka')===false) return $retfalse; 
		 }
		 
		$ts_filename = JPATH_ROOT.DS.'cache'.DS.'ulozenka'.DS.'timestamp.txt';
		$filename = JPATH_ROOT.DS.'cache'.DS.'ulozenka'.DS.'pobocky.xml';
		
		$url = 'http://www.ulozenka.cz/partner/pobocky.php?key='.$params->key.'&partners='.$params->partners; 
		$time = time(); 
		if (!$cache)
		{
		  require_once(dirname(__FILE__).DS.'api.php'); 
		  $request = new ulozenkaApi($params);
		  $data = $request->getPobocky(); 
		  
		
		  
		}
		else
		{
		    /*
				if (file_exists(JPATH_CACHE.DS.'ulozenka'.DS.'ulozenkaobj.php'))
				 //include(JPATH_CACHE.DS.'ulozenka'.DS.'ulozenkaobj.php'); 
				 $data = file_get_contents(JPATH_CACHE.DS.'ulozenka'.DS.'ulozenkaobj.php'); 
				 if (eval('?>'.$data.'<?php')!==false)
				 {
				  if (!empty($retObj)) return $retObj; 
			     }
			*/ 
			
			if (file_exists($filename))
			{
			
			}
			else
			{
			     require_once(dirname(__FILE__).DS.'api.php'); 
	 
			     $request = new ulozenkaApi($params);
				 $data = $request->getPobocky(); 
				 if (!empty($data))
				 {
				  JFile::write($filename, $data);
				  JFile::write($ts_filename, $time);
				 }
			}	 
				 
			
		
		         
				
				 

		}

		if ($cache)
		if (!JFile::exists($filename)) {
			return $retfalse;
		}

		if (!function_exists('simplexml_load_file')) return $retfalse; 
		if ($cache)
		{
		
		$xml = JFactory::getXml($filename); 
		//$xml = simplexml_load_file($filename, "SimpleXMLElement", true);
		
		
		
		if (empty($xml))
		 {
		   JFile::delete($ts_filename);
		   JFile::delete($filename);
		   return $retfalse; 
		 }
		 }
		 else
		 {
		   if (empty($data)) 
		   {
		   $retfalse->error = $request->error; 
		   return $retfalse; 
		   }
		   //$xml = simplexml_load_string($data, "SimpleXMLElement", true); 
		   $xml = JFactory::getXml($data, false); 
		 }
		 
		 
		 if (empty($xml)) $xml = new stdClass(); 
		 $copy = new stdClass(); 
		 $copy->pobocky = array(); 
		 $copy->branch = $xml->branch; 
		 if (isset($request->error)) $copy->error = (string)$request->error; 
		  
		  
		  
		  
		 if (isset($xml->branch))
		 {
		
		 foreach ($xml->branch as $p)
		  {
		    self::br2p($copy->pobocky, $p); 
		  }
		   self::$pobocky_cache = $copy; 
		   if ($cache)
		   self::saveObj($copy); 
		   
		   
           return $copy; 		  
		  
		 }
		 
		 
		 
		 
		  
		  
		 // if (isset($xml->body)) $copy->body = (string)$xml->error; 
		 
		 
		 
		 if (isset($xml->pobocky)) {
			if (count($xml->pobocky)) {
			   foreach ($xml->pobocky as $pobocka)
			    {
				  $newpobocka = new stdClass(); 
				  $ac = (array)$pobocka; 
				  foreach ($ac as $key=>$val)
				   {
				     $newpobocka->$key = $val; 
				   }
				  $copy->pobocky[] = $newpobocka; 
				}
			 if (isset($xml->error)) $copy->error = (string)$xml->error; 
			 if (isset($xml->body)) $copy->body = (string)$xml->error; 
			 self::$pobocky_cache = $copy; 
			 if ($cache)
			 //self::saveObj($copy); 
			 return $copy; 
			}
			}
			
		 self::$pobocky_cache = $xml; 
		 return $xml; 
		 
		
   }
   private static function saveObj($obj)
    {
	  return; 
	   $obj = (array)$obj; 
	   unset($obj['branch']); 
	  
	   jimport('joomla.filesystem.file');
	   jimport('joomla.filesystem.folder');
		
		
		
		if (!file_exists(JPATH_ROOT.DS.'cache'.DS.'ulozenka'))
		 {
		   if (@JFolder::create(JPATH_ROOT.DS.'cache'.DS.'ulozenka')===false) return $retfalse; 
		 }
		 
	   $file = JPATH_CACHE.DS.'ulozenka'.DS.'ulozenkaobj.php'; 
	   $data = '<?php 
	   if( !defined( \'_VALID_MOS\' ) && !defined( \'_JEXEC\' ) ) die( \'Direct Access to \'.basename(__FILE__).\' is not allowed.\' ); 
	   $retObj = '.var_export($obj, true).';
	   '; 
       @JFile::write($file, $data); 
	}
   private static function convertXml($xml)
    {
	
	}
	
	private static function br2p(&$copy, $p)
	 {
	    $np = new stdClass(); 
		$np->id = (int)$p->id; 
		$np->aktiv = (int)$p->active; 
		$np->zkratka = (string)$p->shortcut; 
		$np->nazev = (string)$p->name; 
		$np->telefon = (string)$p->phone; 
		$np->email = (string)$p->email; 
		$np->obec = (string)$p->town; 
		$np->psc = (string)$p->zip; 
		$np->gsm = (string)$p->phone; 
		$np->odkaz = (string)$p->link; 
		$np->ulice = (string)$p->street.' '.(string)$p->houseNumber; 
		$np->obrazek = (string)$p->picture; 
		$np->mapa = (string)$p->map; 
		$np->skype = ''; 
		$np->gps = $p->latitude.' '.$p->longtitude; 
		if ($p->country == 'SVK')
		$np->sk = 1; 
		else $np->sk = 0; 
		
		$np->provoz = ''; 
		$np->provoz_full = ''; 
		
		$odays = array(); 
		
		if (isset($p->openingHours))
		foreach ($p->openingHours as $item)
		 {
		    if (isset($item->regular))
			 foreach ($item->regular as $i2)
			  {
			    
			    //$np->provoz = $i2->hours->open.' '.$i2->hours->close; 
				$days = array('MON'=>'monday', 'TUE'=>'tuesday', 'WED'=>'wednesday', 'THU'=>'thursday', 'FRI'=>'friday', 'SAT'=>'saturday', 'SUN'=>'sunday'); 
				foreach ($days as $keyd=>$day)
				{
				
				 if (isset($i2->$day))
				 if (isset($i2->$day->hours))
				 {
				  
				  $np->provoz_full .= JText::_($keyd).': '.$i2->$day->hours->open.' - '.$i2->$day->hours->close."<br />\n"; 
				 }
				}
			  }
			
			  
		 }
		 $np->provoz = $np->provoz_full; 

		 
		 $np->prices = new stdClass(); 
		 $np->prices->parcel = (string)$p->prices->price->parcel; 
		 $np->prices->cashOnDelivery = (string)$p->prices->price->cashOnDelivery; 
		 $np->prices->currency = (string)$p->prices->price->currency; 
		 
		// new
		$np->country = (string)$p->country; 
		$np->partner = (int)$p->partner;
		$copy[] = $np; 
		
	/*
	"id"]=>
  string(1) "6"
  ["active"]=>
  string(1) "1"
  ["shortcut"]=>
  string(5) "brno2"
  ["name"]=>
  string(25) "Brno, Èernopolní 54/245"
  ["phone"]=>
  string(13) "+420777208204"
  ["email"]=>
  string(16) "info@ulozenka.cz"
  ["street"]=>
  string(12) "Èernopolní"
  ["houseNumber"]=>
  string(6) "54/245"
  ["town"]=>
  string(19) "Brno - Èerná Pole"
  ["zip"]=>
  string(5) "61300"
  ["district"]=>
  object(JXMLElement)#230 (3) {
    ["id"]=>
    string(2) "11"
    ["nutsNumber"]=>
    string(5) "CZ064"
    ["name"]=>
    string(18) "Jihomoravský kraj"
  }
  ["country"]=>
  string(3) "CZE"
  ["link"]=>
  string(55) "http://www.ulozenka.cz/pobocky/6/brno-cernopolni-54-245"
  ["openingHours"]=>
  object(JXMLElement)#232 (2) {
    ["regular"]=>
    object(JXMLElement)#227 (7) {
      ["monday"]=>
      object(JXMLElement)#224 (1) {
        ["hours"]=>
        object(JXMLElement)#188 (2) {
          ["open"]=>
          string(5) "11:00"
          ["close"]=>
          string(5) "19:00"
        }
      }
      ["tuesday"]=>
      object(JXMLElement)#196 (1) {
        ["hours"]=>
        object(JXMLElement)#188 (2) {
          ["open"]=>
          string(5) "11:00"
          ["close"]=>
          string(5) "19:00"
        }
      }
      ["wednesday"]=>
      object(JXMLElement)#198 (1) {
        ["hours"]=>
        object(JXMLElement)#188 (2) {
          ["open"]=>
          string(5) "11:00"
          ["close"]=>
          string(5) "19:00"
        }
      }
      ["thursday"]=>
      object(JXMLElement)#191 (1) {
        ["hours"]=>
        object(JXMLElement)#188 (2) {
          ["open"]=>
          string(5) "11:00"
          ["close"]=>
          string(5) "19:00"
        }
      }
      ["friday"]=>
      object(JXMLElement)#184 (1) {
        ["hours"]=>
        object(JXMLElement)#188 (2) {
          ["open"]=>
          string(5) "11:00"
          ["close"]=>
          string(5) "19:00"
        }
      }
      ["saturday"]=>
      object(JXMLElement)#183 (0) {
      }
      ["sunday"]=>
      object(JXMLElement)#192 (0) {
      }
    }
    ["exceptions"]=>
    object(JXMLElement)#226 (0) {
    }
  }
  ["picture"]=>
  string(41) "http://www.ulozenka.cz/cdn/branches/6.jpg"
  ["gps"]=>
  object(JXMLElement)#231 (2) {
    ["latitude"]=>
    string(9) "49.208607"
    ["longitude"]=>
    string(9) "16.614868"
  }
  ["prices"]=>
  object(JXMLElement)#225 (1) {
    ["price"]=>
    object(JXMLElement)#226 (3) {
      ["parcel"]=>
      string(2) "29"
      ["cashOnDelivery"]=>
      string(2) "12"
      ["currency"]=>
      string(3) "CZK"
    }
  }
  ["partner"]=>
  string(1) "0"
  ["preparing"]=>
  string(1) "0"
  ["navigation"]=>
  object(JXMLElement)#229 (3) {
    ["general"]=>
    string(130) "Poboèka se nachází v ulici Èernopolní 54/245 naproti Mendelovì univerzitì v obytném domì mezi lahùdkami a restaurací."
    ["car"]=>
    string(128) "Smìrem z centra ulicí Drobného, poté odboèit do ulice Erbenova a napojit se na Èernopolní. Poboèka je po levé stranì."
    ["publicTransport"]=>
    string(125) "<ul>
<li>Bus: 67 - zast. Schodová (cca 250 metrù)</li>
<li>Tram: 9, 11 – zast. Zemìdìlská (cca 300 metrù).</li>
</ul>"
  }
  ["otherInfo"]=>
  string(228) "<ul>
<li>doba úschovy zásilky na poboèce - 7 dnù</li>
<li>možnost prodloužení termínu úschovy o 3 dny resp. až 7 dnù</li>
<li>platba kartou možná  (pokud to eshop povoluje)</li>
<li>MHD, parkování autem</li>
</ul>"
  ["transport"]=>
  object(JXMLElement)#228 (3) {
    ["id"]=>
    string(1) "1"
    ["name"]=>
    string(9) "Uloženka"
    ["alias"]=>
    string(8) "ulozenka"
  }
  ["map"]=>
  string(912) "<iframe width="600" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.cz/maps?f=q&source=embed&hl=cs&geocode=&q=%C4%8Cernopoln%C3%AD+54,+Brno-%C4%8Cern%C3%A1+Pole&aq=0&oq=%C4%8Dernopoln%C3%AD+54&sll=49.930008,15.369873&sspn=5.885092,14.27124&brcurrent=5,0,0&num=10&ie=UTF8&hq=&hnear=%C4%8Cernopoln%C3%AD+245%2F54,+613+00+Brno-%C4%8Cern%C3%A1+Pole&t=m&ll=49.213785,16.621199&spn=0.022427,0.051413&z=14&output=embed"></iframe><br /><small><a href="http://maps.google.cz/maps?f=q&source=embed&hl=cs&geocode=&q=%C4%8Cernopoln%C3%AD+54,+Brno-%C4%8Cern%C3%A1+Pole&aq=0&oq=%C4%8Dernopoln%C3%AD+54&sll=49.930008,15.369873&sspn=5.885092,14.27124&brcurrent=5,0,0&num=10&ie=UTF8&hq=&hnear=%C4%8Cernopoln%C3%AD+245%2F54,+613+00+Brno-%C4%8Cern%C3%A1+Pole&t=m&ll=49.213785,16.621199&spn=0.022427,0.051413&z=14" style="color:#0000FF;text-align:left">Zvìtšit mapu</a></small>"
  ["destination"]=>
  string(1) "1"
  ["register"]=>
  string(1) "1"
  */
	
		
	 }

	 
   public static $pobocky_cache; 
   public static function getDataPobocky(&$params, $id)
   {
     $ret = new stdClass(); 
	 if (empty(self::$pobocky_cache))
	 {
     $pobocky = self::getPobocky($params); 
	 self::$pobocky_cache = $pobocky; 
	 }
	 else $pobocky = self::$pobocky_cache; 
	 
	 if (!empty($pobocky->pobocky))
	 foreach ($pobocky->pobocky as $p)
	  {
	     if ($p->id == $id) 
		 {
		 $arr = (array)$p; 
		 foreach ($arr as $key=>$val)
		  {
		    $ret->$key = $val; 
		  }
		
		 return $ret;   
		 }
	  }
	  
	  return $ret; 
   }
   	/**
	* download new page from the internet through https (using fsockopen)
	* @url URL of bank payments to be downloaded and returned
	* @return result of downloaded page
	*/
	public static function fetchURL( $url ) {
		$config = JFactory::getConfig();

		$url_parsed = parse_url($url);
		$host = $url_parsed["host"];
		$port = $url_parsed["port"];
		
		switch ($url_parsed['scheme']) {
			case 'https':
				$scheme = 'ssl://';
				$port = 443;
				break;
			case 'http':
			default:
				$scheme = '';
				$port = 80;    
		} 
		$path = $url_parsed["path"];
		if ($url_parsed["query"] != "")
			$path .= "?".$url_parsed["query"];

		if ($url_parsed['user']) {
			$authorization = "Authorization: Basic ".base64_encode($url_parsed['user'].':'.$url_parsed['pass'])."\r\n";
		} else {
			$authorization = '';
		}

		$out = "GET $path HTTP/1.0\r\nHost: $host\r\n$authorization\r\n";

		$fp = fsockopen($scheme . $host, $port, $errno, $errstr, 30);

		fwrite($fp, $out);
		if (method_exists($config, 'getValue'))
		$tmpfname = $config->getValue('config.tmp_path'). DS."pi_".time();
		else 
		$tmpfname = $config->get('tmp_path'). DS."pi_".time();
		
		$handle = fopen($tmpfname, "w");

		$body = false;
		$i=0;
		while (!feof($fp)) {
			$s = fgets($fp, 1024);
			if ( $body ) {
				//$in .= $s;
				fwrite($handle, $s);
			} else {
				if (eregi("^HTTP.*404", $s)) {
					fclose($handle);
					fclose($fp);
					unlink($tmpfname);
					return false;
				}
			}
			if ( $s == "\r\n" ) {
				$body = true;
			}
			$i++;
		}
		
		fclose($handle);
		fclose($fp);
		
		return $tmpfname;
	}

   
   
}