<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

class OPCUser {

	
	
	
	/**
	 * Bind the post data to the JUser object and the VM tables, then saves it
	 * It is used to register new users
	 * This function can also change already registered users, this is important when a registered user changes his email within the checkout.
	 *
	 * @author Max Milbers
	 * @author Oscar van Eijk
	 * @return boolean True is the save was successful, false otherwise.
	 */
	public static function storeVM25(&$data,$checkToken = TRUE, &$userModel, $opc_no_activation=false, &$opc){

		$message = '';
		$user = '';
		$newId = 0;

		//include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php');
 

		
		if($checkToken){
			JRequest::checkToken() or jexit( 'Invalid Token, while trying to save user' );
			
		}
		
		$mainframe = JFactory::getApplication() ;

		if(empty($data)){
			vmError('Developer notice, no data to store for user');
			return false;
		}

		//To find out, if we have to register a new user, we take a look on the id of the usermodel object.
		//The constructor sets automatically the right id.
		$user = JFactory::getUser();
		$user_id = $user->id; 
		$new = ($user->id < 1);
		
		
		
		if(empty($user_id)){
			$user = new JUser();	//thealmega http://forum.virtuemart.net/index.php?topic=99755.msg393758#msg393758
		} else {
			$user = JFactory::getUser($user_id);
		}

		$gid = $user->get('gid'); // Save original gid

		// Preformat and control user datas by plugin
		JPluginHelper::importPlugin('vmuserfield');
		$dispatcher = JDispatcher::getInstance();

		$valid = true ;
		$dispatcher->trigger('plgVmOnBeforeUserfieldDataSave',array(&$valid,$user_id,&$data,$user ));
		// $valid must be false if plugin detect an error
		if( $valid == false ) {
		
			return false;
		}

		// Before I used this "if($cart && !$new)"
		// This construction is necessary, because this function is used to register a new JUser, so we need all the JUser data in $data.
		// On the other hand this function is also used just for updating JUser data, like the email for the BT address. In this case the
		// name, username, password and so on is already stored in the JUser and dont need to be entered again.

		if(empty ($data['email'])){
			$email = $user->get('email');
			if(!empty($email)){
				$data['email'] = $email;
			}
		} 
		
		$data['email'] = str_replace(array('\'','"',',','%','*','/','\\','?','^','`','{','}','|','~'),array(''),$data['email']);

		//This is important, when a user changes his email address from the cart,
		//that means using view user layout edit_address (which is called from the cart)
		$user->set('email',$data['email']);

			if(empty ($data['name'])){
			$name = $user->get('name');
			if(!empty($name)){
				$data['name'] = $name;
			}
		} 
		
		if (empty($data['name']))
		 {
		    $data['name'] = ''; 
		    if (!empty($data['first_name']))
		    $data['name'] = $data['first_name']; 
			if ($data['name'] == '_') $data['name'] = ''; 
			
			if (!empty($data['last_name']))
		    $data['name'] = $data['last_name']; 
			if ($data['name'] == '_') $data['name'] = ''; 
			
			if (empty($data['name']))
		    $data['name'] = $data['username']; 
			if ($data['name'] == '_') $data['name'] = ''; 
			
			if (empty($data['name']))
			$data['name'] = $data['email']; 
			
		
		 }
		

		if(empty ($data['username'])){
			$username = $user->get('username');
			if(!empty($username)){
				$data['username'] = $username;
			} else {
				$data['username'] = JRequest::getVar('username', '', 'post', 'username');
				
				if (empty($data['username']))
				$data['username'] = $data['email']; 
			}
		}


		if(empty ($data['password'])){
			$data['password'] = JRequest::getVar('password', '', 'post', 'string' ,JREQUEST_ALLOWRAW);
		}

		if(empty ($data['password2'])){
			$data['password2'] = JRequest::getVar('password2', '', 'post', 'string' ,JREQUEST_ALLOWRAW);
		}

		
		if(!$new && !empty($data['password']) && empty($data['password2'])){
			unset($data['password']);
			unset($data['password2']);
		}
		
		$usersConfig = JComponentHelper::getParams( 'com_users' );
		$usernamechange = $usersConfig->get( 'change_login_name', true );
		if (!$new)
		if (empty($usernamechange))
		 {
		   $data['username'] = $user->get('username'); 
		 }
		 
		// Bind Joomla userdata
		if (!$user->bind($data)) {

			foreach($user->getErrors() as $error) {
				// 				vmError('user bind '.$error);
				vmError('user bind '.$error,JText::sprintf('COM_VIRTUEMART_USER_STORE_ERROR',$error));
			}
			$message = 'Couldnt bind data to joomla user';
			array('user'=>$user,'password'=>$data['password'],'message'=>$message,'newId'=>$newId,'success'=>false);
		}

		if($new){
			// If user registration is not allowed, show 403 not authorized.
			// But it is possible for admins and storeadmins to save
			/*
			JPluginHelper::importPlugin('user');
			JPluginHelper::importPlugin('system');
			$dispatcher = JDispatcher::getInstance();

			$valid = true ;
			$dispatcher->trigger('onAfterStoreUser',array($user,true,true,'' ));
			*/
			if (JVM_VERSION < 3)
			{
			if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');

			if (!Permissions::getInstance()->check("admin,storeadmin") && $usersConfig->get('allowUserRegistration') == '0') {
				VmConfig::loadJLang('com_virtuemart');
				 //JError::raiseError( 403, JText::_('COM_VIRTUEMART_ACCESS_FORBIDDEN'));
				 $data['virtuemart_user_id'] = 0; 
				 
				 unset($data['username']);
				 unset($data['password']);
			     unset($data['password2']);
				 $user = new JUser(); 
				 $userModel->_id = 0; 
				 
				 //$userModel->saveUserData($data); 
			     $opc->userStoreAddress($userModel, $data); 
				 return false;
			}
			$authorize	= JFactory::getACL();
			}
			else
			{
			  $authorize = JFactory::getUser();
			 if(!($authorize->authorise('core.admin','com_virtuemart') or $authorize->authorise('core.manage','com_virtuemart')) and $usersConfig->get('allowUserRegistration') == '0') {
				VmConfig::loadJLang('com_virtuemart');
				vmError( vmText::_('COM_VIRTUEMART_ACCESS_FORBIDDEN'));
				
			    $data['virtuemart_user_id'] = 0; 
				 
				 unset($data['username']);
				 unset($data['password']);
			     unset($data['password2']);
				 $user = new JUser(); 
				 $userModel->_id = 0; 
				 
				 //$userModel->saveUserData($data); 
			     $opc->userStoreAddress($userModel, $data); 
				 return false;
				
				
			} 
				
			
			}
			  
			  
			
			// Initialize new usertype setting
			$newUsertype = $usersConfig->get( 'new_usertype' );
			
			
			if (!$newUsertype) {
				if ( JVM_VERSION===1){
					$newUsertype = 'Registered';

				} else {
					$newUsertype=2;
				}
			}
			// Set some initial user values
			$user->set('usertype', $newUsertype);

			if ( JVM_VERSION===1){
				$user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));
			} else {
				$user->groups[] = $newUsertype;
			}

			$date = JFactory::getDate();
			
			if (method_exists($date, 'toMySQL'))
			$user->set('registerDate', $date->toMySQL());
			else $user->set('registerDate', $date->toSQL());
			
			// If user activation is turned on, we need to set the activation information
			$useractivation = $usersConfig->get( 'useractivation' );
			if (!empty($opc_no_activation))
			 {
			   $useractivation = false; 
			 }
			$doUserActivation=false;
			if ( JVM_VERSION===1){
				if ($useractivation == '1' ) {
					$doUserActivation=true;
				}
			} else {
				if ($useractivation == '1' or $useractivation == '2') {
					$doUserActivation=true;
				}
			}
			vmdebug('user',$useractivation , $doUserActivation);
		
			
			if ($doUserActivation )
			{
				jimport('joomla.user.helper');
				if (method_exists('JApplication', 'getHash'))
				$user->set('activation', JApplication::getHash( JUserHelper::genRandomPassword()) );
				else
				$user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
				//$user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
				$user->set('block', '1');
				//$user->set('lastvisitDate', '0000-00-00 00:00:00');
			}
		}

		$option = JRequest::getCmd( 'option');
		// If an exising superadmin gets a new group, make sure enough admins are left...
		if (!$new && $user->get('gid') != $gid && $gid == __SUPER_ADMIN_GID) {
		    if (method_exists($userModel, 'getSuperAdminCount'))
			if ($userModel->getSuperAdminCount() <= 1) {
				vmError(JText::_('COM_VIRTUEMART_USER_ERR_ONLYSUPERADMIN'));
				return false;
			}
		}
		
		// Save the JUser object
		$regfail = false; 
		if (!$user->save()) {
		    
			vmError(JText::_( $user->getError()) , JText::_( $user->getError()));
			$regfail = true; 
		}
		
		//vmdebug('my user, why logged in? ',$user);
		if (!$regfail) 
		{
		$newId = $user->get('id');
		}
		else
		$newId = 0; 
		$data['virtuemart_user_id'] = $newId;	//We need this in that case, because data is bound to table later
		
		
		
		$regid = $user->get('id'); 
		if (!empty($regid))
		$GLOBALS['opc_new_user'] = $user->get('id'); 
		else
		$GLOBALS['opc_new_user'] = $newId; 
		
		//$this->setUserId($newId);
		$userModel->_id = $newId; 

		//Save the VM user stuff
		if (!empty($data['quite']))
		{
		  $msgqx1 = JFactory::getApplication()->get('messageQueue', array()); 
		  $msgqx2 = JFactory::getApplication()->get('_messageQueue', array()); 
		}
		
		
		if (!empty($newId) && (($new) || ($allow_sg_update)))
		{
		  $userdata = $userModel->saveUserData($data); 
		
			$groups = array(); 
			if (method_exists($userModel, 'getCurrentUser'))
			{
				$user2 = $userModel->getCurrentUser();
				$groups = $user2->shopper_groups; 
			}
		
		//if(Permissions::getInstance()->check("admin,storeadmin")) 
		{
			$shoppergroupmodel = VmModel::getModel('ShopperGroup');
			
			$default = $shoppergroupmodel->getDefault(0);
			if (!empty($default))
		   $default_id = $default->virtuemart_shoppergroup_id; 
		   else
		   $default_id = 1; 
			
			$default1 = $shoppergroupmodel->getDefault(1);
			if (!empty($default1))
		   $default1 = $default1->virtuemart_shoppergroup_id; 
		   else
		   $default1 = 2; 
			
			
			require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'shoppergroups.php'); 
			OPCShopperGroups::getSetShopperGroup(false); 
			
			$session = JFactory::getSession();
			$ids = $session->get('vm_shoppergroups_add',array(),'vm');
			if (!empty($groups))
			$ids = array_merge($ids, $groups); 
			
			$remove = $session->get('vm_shoppergroups_remove',array(),'vm');
			if (!empty($remove))
			foreach ($remove as $sr)
			foreach ($ids as $key=>$sg)
			{
			  if ($sg == $sr) unset($ids[$key]); 
			
			}
			if (!empty($ids))
			foreach ($ids as $key=>$sg)
			{
			  if ($sg == $default) unset($ids[$key]); 
			  if (empty($sg)) unset($ids[$key]);
			  if ($sg == $default1) unset($ids[$key]);
			}
		
			if(empty($data['virtuemart_shoppergroup_id']) or $data['virtuemart_shoppergroup_id']==$default->virtuemart_shoppergroup_id){
				$data['virtuemart_shoppergroup_id'] = array();
			}
			
			if (!empty($ids))
			{
			$ids = array_unique($ids); 
			//stAn, opc 250: $data['virtuemart_shoppergroup_id'] = $sg; 
			$data['virtuemart_shoppergroup_id'] = $ids; 
			// Bind the form fields to the table
			$db = JFactory::getDBO(); 
			if (!empty($ids))
			foreach ($ids as $ssg)
			{
			
			$q = 'select * from #__virtuemart_vmuser_shoppergroups where virtuemart_user_id = '.(int)$newId.' and virtuemart_shoppergroup_id = '.(int)$ssg.' limit 0,1'; 
			$db->setQuery($q); 
			$res = $db->loadAssocList(); 
			if (empty($res))
			{
			$q = "insert into `#__virtuemart_vmuser_shoppergroups` (id, virtuemart_user_id, virtuemart_shoppergroup_id) values (NULL, ".(int)$newId.", ".(int)$ssg.")"; 
				$db->setQuery($q); 
				$db->query(); 
			}
			}
			}
		}
		
		
		}
		//$userAddress = $userModel->storeAddress($data); 
		$userAddress = $opc->userStoreAddress($userModel, $data); 
		if (!empty($data['quite']))
		{
		  $x = JFactory::getApplication()->set('messageQueue', $msgqx1); 
		  $x = JFactory::getApplication()->set('_messageQueue', $msgqx2); 
		}
		
		if((empty($userdata) || (empty($userAddress)))) {
			// we will not show the error because if we display only register fields, but an account field is marked as required, it still gives an error
			if (empty($data['quite']))
			vmError('COM_VIRTUEMART_NOT_ABLE_TO_SAVE_USER_DATA');
			// 			vmError(Jtext::_('COM_VIRTUEMART_NOT_ABLE_TO_SAVE_USERINFO_DATA'));
		} 
		if (!$regfail)
		{
			if ($new) {
			    
				OPCUser::sendRegistrationEmail($user,$user->password_clear, $doUserActivation);
				if ($doUserActivation ) {
					vmInfo('COM_VIRTUEMART_REG_COMPLETE_ACTIVATE');
				} else {
					//vmInfo('COM_VIRTUEMART_REG_COMPLETE');
					$user->set('activation', '' );
					$user->set('block', '0');
					$user->set('guest', '0');
				}
			}		
		}

		//The extra check for isset vendor_name prevents storing of the vendor if there is no form (edit address cart)
		// stAn, let's not alter vendor
		/*
		if((int)$data['user_is_vendor']==1 and isset($data['vendor_name'])){
			vmdebug('vendor recognised '.$data['virtuemart_vendor_id']);
			if($userModel->storeVendorData($data)){
				if ($new) {
					if ($doUserActivation ) {
						vmInfo('COM_VIRTUEMART_REG_VENDOR_COMPLETE_ACTIVATE');
					} else {
						vmInfo('COM_VIRTUEMART_REG_VENDOR_COMPLETE');
					}
				} else {
					vmInfo('COM_VIRTUEMART_VENDOR_DATA_STORED');
				}
			}
		}
		*/

		return array('user'=>$user,'password'=>$data['password'],'message'=>$message,'newId'=>$newId,'success'=>!$regfail);

	}
	
		/**
	 * This uses the shopFunctionsF::renderAndSendVmMail function, which uses a controller and task to render the content
	 * and sents it then.
	 *
	 *
	 * @author Oscar van Eijk
	 * @author Max Milbers
	 * @author Christopher Roussel
	 * @author Valérie Isaksen
	 */
	private static function sendRegistrationEmail($user, $password, $doUserActivation){
		if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php');
		$vars = array('user' => $user);

		// Send registration confirmation mail

		$password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); //Disallow control chars in the email
		$vars['password'] = $password;
		//if (empty($vars['name'])) $vars['name'] = ' '; 
		if ($doUserActivation) {
			jimport('joomla.user.helper');
			if(JVM_VERSION >= 2) {
				$com_users = 'com_users';
				$activationLink = 'index.php?option='.$com_users.'&task=registration.activate&token='.$user->get('activation');
			} else {
				$com_users = 'com_user';
				$activationLink = 'index.php?option='.$com_users.'&task=activate&activation='.$user->get('activation');
			}
			$vars['activationLink'] = $activationLink;
		}
		$vars['doVendor']=true;
		// public function renderMail ($viewName, $recipient, $vars=array(),$controllerName = null)
		shopFunctionsF::renderMail('user', $user->get('email'), $vars);



	}

	

}