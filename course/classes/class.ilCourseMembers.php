<?php
/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2001 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/


/**
* class ilobjcourse
*
* @author Stefan Meyer <smeyer@databay.de> 
* @version $Id$
* 
* @extends Object
* @package ilias-core
*/

class ilCourseMembers
{
	var $course_obj;
	var $ilErr;
	var $ilDB;
	var $lng;

	var $member_data;
	var $subscribers;

	function ilCourseMembers(&$course_obj)
	{
		global $ilErr,$lng,$ilDB;

		$this->STATUS_NOTIFY = 1;
		$this->STATUS_NO_NOTIFY = 2;
		$this->STATUS_BLOCKED = 3;
		$this->STATUS_UNBLOCKED = 4;

		$this->ROLE_ADMIN = 1;
		$this->ROLE_MEMBER = 2;

		$this->ilErr =& $ilErr;
		$this->ilDB =& $ilDB;

		$this->lng =& $lng;
		$this->lng->loadLanguageModule("search");

		$this->course_obj =& $course_obj;

	}

	function add(&$user_obj,$a_role,$a_status = 0)
	{
		global $rbacadmin;

		switch($a_role)
		{
			case $this->ROLE_MEMBER:
				if($a_status and ($a_status == $this->STATUS_BLOCKED or $a_status == $this->STATUS_UNBLOCKED))
				{
					$status = $a_status;
				}
				else if($a_status)
				{
					$this->ilErr->raiseError($this->lng->txt("crs_status_not_allowed",$this->ilErr->MESSAGE));
				}
				else
				{
					$status = $this->__getDefaultMemberStatus();
				}
				$role = $this->course_obj->getDefaultMemberRole();
				break;

			case $this->ROLE_ADMIN:
				if($a_status and ($a_status == $this->STATUS_NOTIFY or $a_status == $this->STATUS_NO_NOTIFY))
				{
					$status = $a_status;
				}
				else if($a_status)
				{
					$this->ilErr->raiseError($this->lng->txt("crs_status_not_allowed",$this->ilErr->MESSAGE));
				}
				else
				{
					$status = $this->__getDefaultAdminStatus();
				}
				$role = $this->course_obj->getDefaultAdminRole();
				break;
		}
		$this->__createMemberEntry($user_obj->getId(),$a_role,$status);

		return $rbacadmin->assignUser($role,$user_obj->getId());
	}

	function update($a_usr_id,$a_role,$a_status)
	{
		$this->__read($a_usr_id);

		switch($a_role)
		{
			case $this->ROLE_ADMIN:
				if($a_status != $this->STATUS_NOTIFY or $a_status != $this->STATUS_NO_NOTIFY)
				{
					$this->ilErr->raiseError($this->lng->txt("crs_status_not_allowed",$this->ilErr->MESSAGE));
				}
				break;

			case $this->ROLE_MEMBER:
				if($a_status != $this->STATUS_BLOCKED or $a_status != $this->STATUS_UNBLOCKED)
				{
					$this->ilErr->raiseError($this->lng->txt("crs_status_not_allowed",$this->ilErr->MESSAGE));
				}

			default:
				$this->ilErr->raiseError($this->lng->txt("crs_role_not_allowed",$this->ilErr->MESSAGE));
				break;
		}

		// UPDATE RBAC ROLES
		if($this->member_data["role"] == $this->ROLE_ADMIN and $a_role == $this->ROLE_MEMBER)
		{
			global $rbacadmin;

			$rbacadmin->deassignUser($this->course_obj->getDefaultAdminRole(),$a_usr_id);
			$rbacadmin->assignUser($this->course_obj->getDefaultMemberRole(),$a_usr_id);
		}
		
		if($this->member_data["role"] == $this->ROLE_MEMBER and $a_role == $this->ROLE_ADMIN)
		{
			global $rbacadmin;

			$rbacadmin->deassignUser($this->course_obj->getDefaultMemberRole(),$a_usr_id);
			$rbacadmin->assignUser($this->course_obj->getDefaultAdminRole(),$a_usr_id);
		}
		if($a_status == $this->STATUS_BLOCKED)
		{
			global $rbacadmin;

			$rbacadmin->deassignUser($this->course_obj->getDefaultMemberRole(),$a_usr_id);
		}			

		$query = "UPDATE crs_members ".
			"SET role = '".$a_role."', ".
			"status = '".$a_status."' ".
			"WHERE obj_id = '".$this->course_obj->getId()."' ".
			"AND usr_id = '".$a_usr_id."'";

		$res = $this->ilDB->query($query);

		return true;
	}
	function deleteMembers($a_usr_ids)
	{
		if(!is_array($a_usr_ids) or !count($a_usr_ids))
		{
			$this->course_obj->setMessage("");
			$this->course_obj->appendMessage($this->lng->txt("no_usr_ids_given"));
			
			return false;
		}
		foreach($a_usr_ids as $id)
		{
			if(!$this->delete($id))
			{
				$this->course_obj->appendMessage($this->lng->txt("error_delete_member"));
					
				return false;
			}
		}
		return true;
	}

	function delete($a_usr_id)
	{
		global $rbacadmin;

		$this->__read($a_usr_id);

		switch($this->member_data["role"])
		{
			case $this->ROLE_ADMIN:
				$role = $this->course_obj->getDefaultAdminRole();
				break;

			case $this->ROLE_MEMBER:
				$role = $this->course_obj->getDefaultMemberRole();
				break;
		}
		$rbacadmin->deassignUser($role,$a_usr_id);
		
		$query = "DELETE FROM crs_members ".
			"WHERE usr_id = '".$a_usr_id."' ".
			"AND obj_id = '".$this->course_obj->getId()."'";

		$res = $this->ilDB->query($query);

		return true;
	}
	
	function getAssignedUsers()
	{
		// ALL MEMBERS AND ADMINS
		return array_merge($this->getMembers(),$this->getAdmins());
	}
	function getUserData($a_usr_id)
	{
		$this->__read($a_usr_id);

		return $this->member_data;
	}

	function getCountMembers()
	{
		return count($this->getMembers(false));
	}

	function getMembers($a_all = true)
	{
		$query = "SELECT usr_id FROM crs_members ".
			"WHERE obj_id = '".$this->course_obj->getId()."' ".
			"AND role = '".$this->ROLE_MEMBER."'";

		if(!$a_all)
		{
			$query .= " AND status = '".$this->STATUS_UNBLOCKED."'";
		}

		$res = $this->ilDB->query($query);
		while($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
		{
			$usr_ids[] = $row->usr_id;
		}
		return $usr_ids ? $usr_ids : array();
	}
	function getAdmins()
	{
		$query = "SELECT usr_id FROM crs_members ".
			"WHERE obj_id = '".$this->course_obj->getId()."' ".
			"AND role = '".$this->ROLE_ADMIN."'";

		$res = $this->ilDB->query($query);
		while($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
		{
			$usr_ids[] = $row->usr_id;
		}
		return $usr_ids ? $usr_ids : array();
	}
	function isAdmin($a_usr_id)
	{
		$this->__read($a_usr_id);

		return $this->member_data["role"] == $this->ROLE_ADMIN ? true : false;
	}
	function isMember($a_usr_id)
	{
		$this->__read($a_usr_id);

		return $this->member_data["role"] == $this->ROLE_MEMBER ? true : false;
	}
	function isAssigned($a_usr_id)
	{
		return $this->isAdmin($a_usr_id) || $this->isMember($a_usr_id);
	}
	function isBlocked($a_usr_id)
	{
		$this->__read($a_usr_id);
		
		return $this->member_data["status"] == $this->STATUS_BLOCKED ? true : false;
	}
	function hasAccess($a_usr_id)
	{
		return $this->isAssigned($a_usr_id) && !$this->isBlocked($a_usr_id) ? true : false;
	}

	// METHODS FOR NEW REGISTRATIONS
	function getSubscribers()
	{
		$this->__readSubscribers();

		return $this->subscribers;
	}

	function getSubscriberData($a_usr_id)
	{
		return $this->__readSubscriberData($a_usr_id);
	}

	function assignSubscribers($a_usr_ids)
	{
		if(!is_array($a_usr_ids) or !count($a_usr_ids))
		{
			return false;
		}
		foreach($a_usr_ids as $id)
		{
			if(!$this->assignSubscriber($id))
			{
				return false;
			}
		}
		return true;
	}

	function assignSubscriber($a_usr_id,$a_role = 0,$a_status = 0)
	{
		$a_role = $a_role ? $a_role : $this->ROLE_MEMBER;
		$a_status = $a_status ? $a_status : $this->STATUS_UNBLOCKED;

		$this->course_obj->setMessage("");


		if(!$this->isSubscriber($a_usr_id))
		{
			$this->course_obj->appendMessage($this->lng->txt("crs_user_notsubscribed"));

			return false;
		}
		if($this->isAssigned($a_usr_id))
		{
			$tmp_obj = ilObjectFactory::getInstanceByObjId($a_usr_id);
			$this->course_obj->appendMessage($tmp_obj->getLogin().": ".$this->lng->txt("crs_user_already_assigned"));
			
			return false;
		}

		if(!$tmp_obj =& ilObjectFactory::getInstanceByObjId($a_usr_id))
		{
			$this->course_obj->appendMessage($this->lng->txt("crs_user_not_exists"));

			return false;
		}

		$this->add($tmp_obj,$a_role,$a_status);
		$this->deleteSubscriber($a_usr_id);

		return true;
	}

	function autoFillSubscribers()
	{
		$this->__readSubscribers();

		$counter = 0;
		foreach($this->subscribers as $subscriber)
		{
			if($this->course_obj->getSubscriptionMaxMembers() and
			   $this->course_obj->getSubscriptionMaxMembers() <= $this->getCountMembers())
			{
				return $counter;
			}
			if(!$this->assignSubscriber($subscriber))
			{
				continue;
			}
			++$counter;
		}
		
		return $counter;
	}

	function addSubscriber($a_usr_id)
	{
		$query = "INSERT INTO crs_subscribers ".
			" VALUES ('".$a_usr_id."','".$this->course_obj->getId()."','".time()."')";

		$res = $this->ilDB->query($query);

		return true;
	}

	function deleteSubscriber($a_usr_id)
	{
		$query = "DELETE FROM crs_subscribers ".
			"WHERE usr_id = '".$a_usr_id."' ".
			"AND obj_id = '".$this->course_obj->getId()."'";

		$res = $this->ilDB->query($query);

		return true;
	}

	function deleteSubscribers($a_usr_ids)
	{
		if(!is_array($a_usr_ids) or !count($a_usr_ids))
		{
			$this->course_obj->setMessage("");
			$this->course_obj->appendMessage($this->lng->txt("no_usr_ids_given"));
			
			return false;
		}
		foreach($a_usr_ids as $id)
		{
			if(!$this->deleteSubscriber($id))
			{
				$this->course_obj->appendMessage($this->lng->txt("error_delete_subscriber"));
					
				return false;
			}
		}
		return true;
	}
	function isSubscriber($a_usr_id)
	{
		$query = "SELECT * FROM crs_subscribers ".
			"WHERE usr_id = '".$a_usr_id."' ".
			"AND obj_id = '".$this->course_obj->getId()."'";

		$res = $this->ilDB->query($query);

		while($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
		{
			return true;
		}
		return false;
	}

	// PRIVATE METHODS
	function __getDefaultAdminStatus()
	{
		return $this->STATUS_NOTIFY;
	}
	function __getDefaultMemberStatus()
	{
		return $this->STATUS_UNBLOCKED;
	}

	function __createMemberEntry($a_usr_id,$a_role,$a_status)
	{
		$query = "INSERT INTO crs_members ".
			"SET usr_id = '".$a_usr_id."', ".
			"obj_id = '".$this->course_obj->getId()."', ".
			"status = '".$a_status."', ".
			"role = '".$a_role."'";

		$res = $this->ilDB->query($query);

		return true;
	}

	function __read($a_usr_id)
	{
		$query = "SELECT * FROM crs_members ".
			"WHERE usr_id = '".$a_usr_id."' ".
			"AND obj_id = '".$this->course_obj->getId()."'";

		$res = $this->ilDB->query($query);

		$this->member_data = array();
		while($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
		{
			$this->member_data["usr_id"]	= $row->usr_id;
			$this->member_data["role"]		= $row->role;
			$this->member_data["status"]	= $row->status;
		}
		return true;
	}


	function __readSubscribers()
	{
		$this->subscribers = array();

		$query = "SELECT usr_id FROM crs_subscribers ".
			"WHERE obj_id = '".$this->course_obj->getId()."' ".
			"ORDER BY sub_time ";

		$res = $this->ilDB->query($query);
		while($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
		{
			// DELETE SUBSCRIPTION IF USER HAS BEEN DELETED
			if(!ilObjectFactory::getInstanceByObjId($a_usr_id,false))
			{
				$this->deleteSubscriber($a_usr_id);
			}
			$this->subscribers[] = $row->usr_id;
		}
		return true;
	}

	function __readSubscriberData($a_usr_id)
	{
		$query = "SELECT * FROM crs_subscribers ".
			"WHERE obj_id = '".$this->course_obj->getId()."' ".
			"AND usr_id = '".$a_usr_id."'";

		$res = $this->ilDB->query($query);
		while($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
		{
			$data["time"] = $row->sub_time;
			$data["usr_id"] = $row->usr_id;
		}
		return $data ? $data : array();
	}

}
?>