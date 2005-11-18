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
* Class ilObjUserTrackingGUI
*
* @author Stefan Meyer <smeyer@databay.de>
*
* @version $Id$
*
* @ilCtrl_Calls ilLearningProgressGUI: ilLPListOfObjectsGUI, ilLPListOfSettingsGUI, ilLPListOfProgressGUI
*
* @extends ilObjectGUI
* @package ilias-core
*
*/

include_once './Services/Tracking/classes/class.ilLearningProgressBaseGUI.php';

class ilLearningProgressGUI extends ilLearningProgressBaseGUI
{
	function ilLearningProgressGUI($a_mode,$a_ref_id = 0)
	{
		parent::ilLearningProgressBaseGUI($a_mode,$a_ref_id);
	}

	/**
	* execute command
	*/
	function &executeCommand()
	{
		$this->ctrl->setReturn($this, "");

		// E.g personal desktop mode needs locator header icon ...
		$this->__buildHeader();

		switch($this->__getNextClass())
		{
			case 'illplistofprogressgui':
				include_once 'Services/Tracking/classes/class.ilLPListOfProgressGUI.php';

				$lop_gui = new ilLPListOfProgressGUI($this->getMode(),$this->getRefId());
				$this->ctrl->setCmdClass('illplistofprogressgui');
				$this->ctrl->forwardCommand($lop_gui);
				$this->__setSubTabs(LP_ACTIVE_PROGRESS);
				break;

			case 'illplistofobjectsgui':
				include_once 'Services/Tracking/classes/class.ilLPListOfObjectsGUI.php';

				$loo_gui = new ilLPListOfObjectsGUI($this->getMode(),$this->getRefId());
				$this->ctrl->setCmdClass('illplistofobjectsgui');
				$this->ctrl->forwardCommand($loo_gui);
				$this->__setSubTabs(LP_ACTIVE_OBJECTS);
				break;

			case 'illplistofsettingsgui':
				include_once 'Services/Tracking/classes/class.ilLPListOfSettingsGUI.php';

				$los_gui = new ilLPListOfSettingsGUI($this->getMode(),$this->getRefId());
				$this->ctrl->setCmdClass('illplistofsettingsgui');
				$this->ctrl->forwardCommand($los_gui);
				$this->__setSubTabs(LP_ACTIVE_SETTINGS);
				break;

			default:
				die("No mode given");
		}

		// E.G personal desktop mode needs $tpl->show();
		$this->__buildFooter();

		return true;
	}

	function __getNextClass()
	{
		if(strlen($next_class = $this->ctrl->getNextClass()))
		{
			return $next_class;
		}
		switch($this->getMode())
		{
			case LP_MODE_ADMINISTRATION:
				return 'illplistofobjectsgui';

			case LP_MODE_REPOSITORY:
				return 'illplistofobjectsgui';

			case LP_MODE_PERSONAL_DESKTOP:
				return 'illplistofprogressgui';
		}
	}
}
?>