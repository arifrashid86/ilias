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

//require_once ("classes/class.ilDOMUtil.php");
require_once ("content/classes/Pages/class.ilPageObjectGUI.php");
require_once ("content/classes/Pages/class.ilPCMediaObjectGUI.php");
require_once ("content/classes/Pages/class.ilPCParagraphGUI.php");
require_once ("content/classes/Pages/class.ilPCSourcecodeGUI.php");
require_once ("content/classes/Pages/class.ilPCTableGUI.php");
require_once ("content/classes/Pages/class.ilPCTableDataGUI.php");
require_once ("content/classes/Pages/class.ilPCListGUI.php");
require_once ("content/classes/Pages/class.ilPCListItemGUI.php");
require_once ("content/classes/Pages/class.ilPCFileListGUI.php");
require_once ("content/classes/Pages/class.ilPCFileItemGUI.php");
require_once ("content/classes/Media/class.ilObjMediaObjectGUI.php");
require_once ("classes/class.ilTabsGUI.php");

/**
* Page Editor GUI class
*
* @author Alex Killing <alex.killing@gmx.de>
* @version $Id$
*
* @package content
*/
class ilPageEditorGUI
{
	/**
	* ilias object
	* @var object ilias
	* @access public
	*/
	var $ilias;
	var $tpl;
	var $lng;
	var $ctrl;
	var $objDefinition;
	var $page;
	var $target_script;
	var $return_location;
	var $header;
	var $tabs;
	var $cont_obj;

	/**
	* Constructor
	*
	* @param	object		$a_page_object		page object
	* @access	public
	*/
	function ilPageEditorGUI(&$a_page_object)
	{
		global $ilias, $tpl, $lng, $objDefinition, $ilCtrl;

		// initiate variables
		$this->ilias =& $ilias;
		$this->ctrl =& $ilCtrl;
		$this->tpl =& $tpl;
		$this->lng =& $lng;
		$this->objDefinition = $objDefinition;

		$this->page =& $a_page_object;

		$this->ctrl->saveParameter($this, "hier_id");
	}

	/**
	* get all gui classes that are called from this one (see class ilCtrl)
	*
	* @param	array		array of gui classes that are called
	*/
	function _forwards()
	{
		return array("ilPCParagraphGUI", "ilPCTableGUI",
		"ilPCTableDataGUI", "ilPCMediaObjectGUI", "ilPCListGUI",
		"ilPCListItemGUI", "ilPCFileListGUI", "ilPCFileItemGUI",
		"ilObjMediaObjectGUI","ilPCSourcecodeGUI");
	}

	/**
	* set header title
	*
	* @param	string		$a_header		header title
	*/
	function setHeader($a_header)
	{
		$this->header = $a_header;
	}

	/**
	* get header title
	*
	* @return	string		header title
	*/
	function getHeader()
	{
		return $this->header;
	}

	/**
	* set locator object
	*
	* @param	object		$a_locator		locator object
	*/
	function setLocator(&$a_locator)
	{
		$this->locator =& $a_locator;
	}

	/**
	* redirect to parent context
	*/
	function returnToContext()
	{
		$this->ctrl->returnToParent($this);
	}

	/**
	* execute command
	*/
	function &executeCommand()
	{
//echo "execute";
		$cmd = $this->ctrl->getCmd();

		$hier_id = $_GET["hier_id"];
		if(isset($_POST["new_hier_id"]))
		{
			$hier_id = $_POST["new_hier_id"];
		}
//echo "GEThier_id:".$_GET["hier_id"]."<br>";
//echo "hier_id:".$hier_id."<br>";

		$new_type = (isset($_GET["new_type"]))
			? $_GET["new_type"]
			: $_POST["new_type"];

		if (substr($cmd, 0, 5) == "exec_")
		{
			$cmd = explode("_", key($_POST["cmd"]));
			unset($cmd[0]);
			$hier_id = implode($cmd, "_");
			$cmd = $_POST["command".$hier_id];
		}

		$this->page->buildDom();
		$this->page->addHierIDs();

		// determine command and content object
		$com = explode("_", $cmd);
		$cmd = $com[0];

//echo "type:$type:cmd:$cmd:";

		// determine content type
		if ($cmd == "insert" || $cmd == "create")
		{
			$ctype = $com[1];
		}
		else
		{
//echo $cmd;
//echo "<br>is_object:".is_object($this->page).":";
			if ($cmd != "insertFromClipboard" && $cmd != "pasteFromClipboard" &&
				$cmd != "setMediaMode")
			{
				$cont_obj =& $this->page->getContentObject($hier_id);
				$ctype = $cont_obj->getType();
			}
		}


		$this->tpl->addBlockFile("CONTENT", "content", "tpl.adm_content.html");
		$this->tpl->addBlockFile("STATUSLINE", "statusline", "tpl.statusline.html");


		if ($ctype != "mob" || !is_object ($cont_obj))
		{
			$this->tpl->setVariable("HEADER", $this->getHeader());
			$this->displayLocator();
		}

		$this->cont_obj =& $cont_obj;

		// special command / command class handling
		$this->ctrl->setParameter($this, "hier_id", $hier_id);
		$this->ctrl->setCmd($cmd);
		$next_class = $this->ctrl->getNextClass($this);

		if ($next_class == "")
		{
			switch($ctype)
			{
				case "src":
					$this->ctrl->setCmdClass("ilPCSourcecodeGUI");
					break;
				
				case "par":
					$this->ctrl->setCmdClass("ilPCParagraphGUI");
					break;

				case "tab":
					$this->ctrl->setCmdClass("ilPCTableGUI");
					break;

				case "td":
					$this->ctrl->setCmdClass("ilPCTableDataGUI");
					break;

				case "mob":
					$this->ctrl->setCmdClass("ilPCMediaObjectGUI");
					break;

				case "list":
					$this->ctrl->setCmdClass("ilPCListGUI");
					break;

				case "li":
					$this->ctrl->setCmdClass("ilPCListItemGUI");
					break;

				case "flst":
					$this->ctrl->setCmdClass("ilPCFileListGUI");
					break;

				case "flit":
					$this->ctrl->setCmdClass("ilPCFileItemGUI");
					break;
			}
			$next_class = $this->ctrl->getNextClass($this);
		}

//echo "hier_id:$hier_id:type:$type:cmd:$cmd:ctype:$ctype:next_class:$next_class:<br>";

		switch($next_class)
		{
			// Sourcecode
			case "ilpcsourcecodegui":

				$src_gui =& new ilPCSourcecodeGUI($this->page, $cont_obj, $hier_id);
				$ret =& $src_gui->executeCommand();
				break;

			// Paragraph
			case "ilpcparagraphgui":
				$par_gui =& new ilPCParagraphGUI($this->page, $cont_obj, $hier_id);
				$ret =& $par_gui->executeCommand();
				break;

			// Table
			case "ilpctablegui":
				$tab_gui =& new ilPCTableGUI($this->page, $cont_obj, $hier_id);
				$ret =& $tab_gui->executeCommand();
				break;

			// Table Cell
			case "ilpctabledatagui":
				$td_gui =& new ilPCTableDataGUI($this->page, $cont_obj, $hier_id);
				$ret =& $td_gui->executeCommand();
				break;

			// PC Media Object
			case "ilpcmediaobjectgui":
			case "ilobjmediaobjectgui":
				$pcmob_gui =& new ilPCMediaObjectGUI($this->page, $cont_obj, $hier_id);
				$tabs_gui =& new ilTabsGUI();
				if (is_object ($cont_obj))
				{
					$pcmob_gui->getTabs($tabs_gui);
					$this->tpl->setVariable("HEADER", $this->lng->txt("mob").": ".
						$cont_obj->getTitle());
					$this->displayLocator("mob");
					$mob_gui =& new ilObjMediaObjectGUI("", $cont_obj->getId(),false, false);
					$mob_gui->getTabs($tabs_gui);
				}
				else
				{
					$pcmob_gui->getTabs($tabs_gui, true);
				}
				$this->tpl->setVariable("TABS", $tabs_gui->getHTML());
				if ($next_class == "ilpcmediaobjectgui")
				{
					$pcmob_gui->executeCommand();
				}
				else
				{
					$ret =& $mob_gui->executeCommand();
				}
				break;

			// List
			case "ilpclistgui":
				$list_gui =& new ilPCListGUI($this->page, $cont_obj, $hier_id);
				$ret =& $list_gui->executeCommand();
				break;

			// List Item
			case "ilpclistitemgui":
				$list_item_gui =& new ilPCListItemGUI($this->page, $cont_obj, $hier_id);
				$ret =& $list_item_gui->executeCommand();
				break;

			// File List
			case "ilpcfilelistgui":
				$file_list_gui =& new ilPCFileListGUI($this->page, $cont_obj, $hier_id);
				$ret =& $file_list_gui->executeCommand();
				break;

			// File List Item
			case "ilpcfileitemgui":
				$file_item_gui =& new ilPCFileItemGUI($this->page, $cont_obj, $hier_id);
				$ret =& $file_item_gui->executeCommand();
				break;

			default:
				if ($cmd == "pasteFromClipboard")
				{
					$ret =& $this->pasteFromClipboard($hier_id);
				}
				else
				{
					$ret =& $this->$cmd();
				}
				break;

		}

		return $ret;
	}

	/**
	*
	*/
	function setMediaMode()
	{
		global $ilUser;

		$ilUser->writePref("ilPageEditor_MediaMode", $_POST["media_mode"]);
		$this->ctrl->returnToParent($this);
	}

	/**
	* paste from clipboard (redirects to clipboard)
	*/
	function pasteFromClipboard($a_hier_id)
	{
		global $ilCtrl;

		$ilCtrl->setParameter($this, "hier_id", $a_hier_id);
		$ilCtrl->setParameterByClass("ilEditClipboardGUI", "returnCommand",
			rawurlencode($ilCtrl->getLinkTarget($this,
			"insertFromClipboard")));
//echo ":".$ilCtrl->getLinkTarget($this, "insertFromClipboard").":";
		$ilCtrl->redirectByClass("ilEditClipboardGUI", "getObject");
	}

	/**
	* insert object from clipboard
	*/
	function insertFromClipboard()
	{
		if ($_GET["clip_obj_id"] != "")
		{
			if ($_GET["clip_obj_type"] == "mob")
			{
//$this->dom =& $this->page->getDom();
//echo "is_object:".is_object($this->dom).":";
				$this->content_obj = new ilObjMediaObject($_GET["clip_obj_id"]);
				$this->content_obj->setDom($this->page->getDom());
				$this->content_obj->createAlias($this->page, $_GET["hier_id"]);
				$this->updated = $this->page->update();
			}
		}
		$this->ctrl->returnToParent($this);
	}



	/**
	* display locator
	*/
	function displayLocator()
	{
		if(is_object($this->locator))
		{
			$this->locator->display();
		}
	}

}
?>
