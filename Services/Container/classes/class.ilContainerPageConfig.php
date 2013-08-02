<?php

/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/COPage/classes/class.ilPageConfig.php");

/**
 * Container page configuration 
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 * @ingroup ServicesContainer
 */
class ilContainerPageConfig extends ilPageConfig
{
	/**
	 * Constructor
	 *
	 * @param
	 * @return
	 */
	function __construct()
	{
		global $ilSetting;
		
		parent::__construct();
		
		$this->setIntLinkHelpDefaultType("RepositoryItem");
		$this->setIntLinkHelpDefaultId($_GET["ref_id"]);
		
		$this->setEnablePCType("FileList", false);
		$this->setEnablePCType("Map", true);
		$this->setEnablePCType("Resources", true);
	}
	
}

?>