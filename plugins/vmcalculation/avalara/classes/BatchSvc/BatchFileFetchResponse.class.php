<?php
if (!defined('_JEXEC'))
die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');
/**
 * BatchFileFetchResponse.class.php
 */

/**
 * 
 *
 * @author    Avalara
 * @copyright � 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Batch
 */
class BatchFileFetchResponse {
  private $BatchFileFetchResult; // BatchFileFetchResult

  public function setBatchFileFetchResult($value){$this->BatchFileFetchResult=$value;} // BatchFileFetchResult
  public function getBatchFileFetchResult(){return $this->BatchFileFetchResult;} // BatchFileFetchResult

}

?>
