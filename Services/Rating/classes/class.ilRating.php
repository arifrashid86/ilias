<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/** @defgroup ServicesRating Services/Rating
 */

/**
* Class ilRating
*
* @author Alex Killing <alex.killing@gmx.de>
* @version $Id$
*
* @ingroup ServicesRating
*/
class ilRating
{	
	/**
	* Write rating for a user and an object.
	*
	* @param	int			$a_obj_id			Object ID
	* @param	string		$a_obj_type			Object Type
	* @param	int			$a_sub_obj_id		Subobject ID
	* @param	string		$a_sub_obj_type		Subobject Type
	* @param	int			$a_user_id			User ID
	* @param	int			$a_rating			Rating
	* @param	int			$a_category_id		Category ID
	*/
	static function writeRatingForUserAndObject($a_obj_id, $a_obj_type, $a_sub_obj_id, $a_sub_obj_type,
		$a_user_id, $a_rating, $a_category_id = 0)
	{
		global $ilDB;

		if ($a_user_id == ANONYMOUS_USER_ID)
		{
			return;
		}
		
		if($a_category_id)
		{
			$ilDB->manipulate("DELETE FROM il_rating WHERE ".
				"user_id = ".$ilDB->quote($a_user_id, "integer")." AND ".
				"obj_id = ".$ilDB->quote((int) $a_obj_id, "integer")." AND ".
				"obj_type = ".$ilDB->quote($a_obj_type, "text")." AND ".
				"sub_obj_id = ".$ilDB->quote((int) $a_sub_obj_id, "integer")." AND ".
				$ilDB->equals("sub_obj_type", $a_sub_obj_type, "text", true)." AND ".
				"category_id = ".$ilDB->quote(0, "integer"));
		}
		
		$ilDB->manipulate("DELETE FROM il_rating WHERE ".
			"user_id = ".$ilDB->quote($a_user_id, "integer")." AND ".
			"obj_id = ".$ilDB->quote((int) $a_obj_id, "integer")." AND ".
			"obj_type = ".$ilDB->quote($a_obj_type, "text")." AND ".
			"sub_obj_id = ".$ilDB->quote((int) $a_sub_obj_id, "integer")." AND ".
			$ilDB->equals("sub_obj_type", $a_sub_obj_type, "text", true)." AND ".
			"category_id = ".$ilDB->quote((int) $a_category_id, "integer"));
		
		$ilDB->manipulate("INSERT INTO il_rating (user_id, obj_id, obj_type,".
			"sub_obj_id, sub_obj_type, category_id, rating) VALUES (".
			$ilDB->quote($a_user_id, "integer").",".
			$ilDB->quote((int) $a_obj_id, "integer").",".
			$ilDB->quote($a_obj_type, "text").",".
			$ilDB->quote((int) $a_sub_obj_id, "integer").",".
			$ilDB->quote($a_sub_obj_type, "text").",".
			$ilDB->quote($a_category_id, "integer").",".
			$ilDB->quote((int) $a_rating, "integer").")");
	}
	
	/**
	* Get rating for a user and an object.
	*
	* @param	int			$a_obj_id			Object ID
	* @param	string		$a_obj_type			Object Type
	* @param	int			$a_sub_obj_id		Subobject ID
	* @param	string		$a_sub_obj_type		Subobject Type
	* @param	int			$a_user_id			User ID
	* @param	int			$a_category_id		Category ID
	*/
	static function getRatingForUserAndObject($a_obj_id, $a_obj_type, $a_sub_obj_id, $a_sub_obj_type,
		$a_user_id, $a_category_id = 0)
	{
		global $ilDB;
		
		$q = "SELECT * FROM il_rating WHERE ".
			"user_id = ".$ilDB->quote($a_user_id, "integer")." AND ".
			"obj_id = ".$ilDB->quote((int) $a_obj_id, "integer")." AND ".
			"obj_type = ".$ilDB->quote($a_obj_type, "text")." AND ".
			"sub_obj_id = ".$ilDB->quote((int) $a_sub_obj_id, "integer")." AND ".
			$ilDB->equals("sub_obj_type", $a_sub_obj_type, "text", true)." AND ".
			"category_id = ".$ilDB->quote((int) $a_category_id, "integer");
		$set = $ilDB->query($q);
		$rec = $ilDB->fetchAssoc($set);
		return $rec["rating"];
	}
	
	/**
	* Get overall rating for an object.
	*
	* @param	int			$a_obj_id			Object ID
	* @param	string		$a_obj_type			Object Type
	* @param	int			$a_sub_obj_id		Subobject ID
	* @param	string		$a_sub_obj_type		Subobject Type
	* @param	int			$a_category_id		Category ID
	*/
	static function getOverallRatingForObject($a_obj_id, $a_obj_type, $a_sub_obj_id, $a_sub_obj_type, $a_category_id = null)
	{
		global $ilDB;
		
		$q = "SELECT count(*) as cnt, AVG(rating) as av FROM il_rating WHERE ".
			"obj_id = ".$ilDB->quote((int) $a_obj_id, "integer")." AND ".
			"obj_type = ".$ilDB->quote($a_obj_type, "text")." AND ".
			"sub_obj_id = ".$ilDB->quote((int) $a_sub_obj_id, "integer")." AND ".
			$ilDB->equals("sub_obj_type", $a_sub_obj_type, "text", true);
		if ($a_category_id !== null)
		{
			$q .= " AND category_id = ".$ilDB->quote((int) $a_category_id, "integer");
		}		
		$set = $ilDB->query($q);
		$rec = $ilDB->fetchAssoc($set);
		return array("cnt" => $rec["cnt"], "avg" => $rec["av"]);
	}
}

?>