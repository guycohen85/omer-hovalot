<?php
if (!defined('_JEXEC'))
die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');
/**
 * AuditMessage.class.php
 */

/**
 * 
 *
 * @author    Avalara
 * @copyright � 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Batch
 */
class AuditMessage {
  private $Message; // string

  public function setMessage($value){$this->Message=$value;} // string
  public function getMessage(){return $this->Message;} // string

}

?>
