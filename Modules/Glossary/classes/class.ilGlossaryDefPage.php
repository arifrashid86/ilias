<?php
/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/COPage/classes/class.ilPageObject.php");

/**
 * Glossary definition page object
 * 
 * @author Alex Killing <alex.killing@gmx.de> 
 * @version $Id$
 *
 * @ingroup ModulesGlossary
 */
class ilGlossaryDefPage extends ilPageObject
{
	/**
	* Constructor
	* @access	public
	* @param	wiki page id
	*/
	function __construct($a_id = 0, $a_old_nr = 0)
	{
		parent::__construct("gdf", $a_id, $a_old_nr);
	}

}
?>