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
* Class ilObjLanguageGUI
*
* @author Stefan Meyer <smeyer@databay.de> 
* $Id$Id: class.ilObjLanguageGUI.php,v 1.2 2003/03/28 10:30:36 shofmann Exp $
* 
* @extends ilObject
* @package ilias-core
*/

require_once "class.ilObjectGUI.php";

class ilObjLanguageGUI extends ilObjectGUI
{
	/**
	* Constructor
	* @access public
	*/
	function ilObjLanguageGUI($a_data,$a_id,$a_call_by_reference)
	{
		$this->type = "lng";
		$this->ilObjectGUI($a_data,$a_id,$a_call_by_reference);
	}
} // END class.LanguageObjectOut
?>
