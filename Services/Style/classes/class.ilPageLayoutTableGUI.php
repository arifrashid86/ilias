<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("Services/Table/classes/class.ilTable2GUI.php");

/**
* TableGUI class for page layouts
*
* @author Hendrik Holtmann <holtmann@me.com>
* @version $Id$
*
*/
class ilPageLayoutTableGUI extends ilTable2GUI
{

	function __construct($a_parent_obj, $a_parent_cmd)
	{
		global $ilCtrl, $lng;
		
		parent::__construct($a_parent_obj, $a_parent_cmd);
		$this->addColumn("", "", "2%");
		
		$this->addColumn($lng->txt("active"), "", "6%");
		$this->addColumn($lng->txt("thumbnail"), "", "22%");
		$this->addColumn($lng->txt("title"), "", "40%");
		$this->addColumn($lng->txt("description"), "", "30%");
		
		$this->addMultiCommand("activate", $lng->txt("activate"));
		$this->addMultiCommand("deactivate", $lng->txt("deactivate"));
		$this->addMultiCommand("deletePgl", $lng->txt("delete"));
		
		$this->getPageLayouts();
		
		$this->setSelectAllCheckbox("pglayout");
		$this->setEnableHeader(true);
		$this->setFormAction($ilCtrl->getFormAction($a_parent_obj));
		$this->setRowTemplate("tpl.stys_pglayout_table_row.html",
			"Services/Style");
		$this->setTitle($lng->txt("page_layouts"));
		
		//build form
		/*
		$opts = ilUtil::formSelect(12,"new_type",array($lng->txt("page_layout")));
		$this->tpl->setCurrentBlock("add_object");
		$this->tpl->setVariable("SELECT_OBJTYPE", $opts);
		$this->tpl->setVariable("BTN_NAME", "createPgGUI");
		$this->tpl->setVariable("TXT_ADD", $this->lng->txt("add"));
		$this->tpl->parseCurrentBlock();
		*/
	}
	
	/**
	* Get a List of all Page Layouts
	*/
	function getPageLayouts() {
	    $this->setData(ilPageLayout::getLayoutsAsArray());
	}
	
	/**
	* Standard Version of Fill Row. Most likely to
	* be overwritten by derived class.
	*/
	protected function fillRow($a_set)
	{
		global $lng, $ilCtrl;
		if ($a_set['active']) {
			$this->tpl->setVariable("IMG_ACTIVE",ilUtil::getImagePath("icon_led_on_s.png"));
		}	else {
			$this->tpl->setVariable("IMG_ACTIVE",ilUtil::getImagePath("icon_led_off_s.png"));
		}
		$this->tpl->setVariable("VAL_TITLE", $a_set['title']);
		$this->tpl->setVariable("VAL_DESCRIPTION", $a_set['description']);
		$this->tpl->setVariable("CHECKBOX_ID", $a_set['layout_id']);
		
		$ilCtrl->setParameterByClass("ilobjstylesettingsgui", "obj_id", $a_set['layout_id']);
		$this->tpl->setVariable("HREF_EDIT_PGLAYOUT",$ilCtrl->getLinkTargetByClass("ilobjstylesettingsgui","editPg"));
		
		$pgl_obj = new ilPageLayout($a_set['layout_id']);
		$this->tpl->setVariable("VAL_PREVIEW_HTML",$pgl_obj->getPreview());
		
		
	}

}
?>
