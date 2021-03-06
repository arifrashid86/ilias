<?php

/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

/** 
* 
* @author Stefan Meyer <meyer@leifos.com>
* @version $Id$
* 
* 
* @ingroup ServicesAdvancedMetaData
*/
include_once('Services/Table/classes/class.ilTable2GUI.php');
include_once('Services/AdvancedMetaData/classes/class.ilAdvancedMDFieldDefinition.php');

class ilAdvancedMDRecordTableGUI extends ilTable2GUI
{
	protected $lng = null;
	protected $ctrl;
	
	/**
	 * Constructor
	 *
	 * @access public
	 * @param
	 * 
	 */
	public function __construct($a_parent_obj,$a_parent_cmd = '')
	{
	 	global $lng,$ilCtrl;
	 	
	 	$this->lng = $lng;
	 	$this->ctrl = $ilCtrl;
	 	
	 	parent::__construct($a_parent_obj,$a_parent_cmd);
	 	$this->addColumn('','',1);
	 	$this->addColumn($this->lng->txt('title'),'title');
	 	$this->addColumn($this->lng->txt('md_fields'),'fields');
	 	$this->addColumn($this->lng->txt('md_adv_active'),'active');
	 	$this->addColumn($this->lng->txt('md_obj_types'),'obj_types');
	 	$this->addColumn($this->lng->txt('actions'));
	 	
		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));
		$this->setRowTemplate("tpl.show_records_row.html","Services/AdvancedMetaData");
		$this->setDefaultOrderField("title");
		$this->setDefaultOrderDirection("desc");
	}
	
	/**
	 * Fill row
	 *
	 * @access public
	 * @param
	 * 
	 */
	public function fillRow($a_set)
	{
		foreach(ilAdvancedMDRecord::_getAssignableObjectTypes(true) as $obj_type)
		{
			$this->tpl->setCurrentBlock('ass_obj_types');
			$this->tpl->setVariable('VAL_OBJ_TYPE', $obj_type["text"]);
			$this->tpl->setVariable('ASS_ID',$a_set['id']);
			$this->tpl->setVariable('ASS_NAME',$obj_type["obj_type"].":".$obj_type["sub_type"]);
			foreach ($a_set['obj_types'] as $t)
			{
				if ($obj_type["obj_type"] == $t["obj_type"] && 
					$obj_type["sub_type"] == $t["sub_type"])
				{
					$this->tpl->setVariable('ASS_CHECKED','checked="checked"');
				}
			}
			$this->tpl->parseCurrentBlock();
		}
		$this->tpl->setVariable('VAL_ID',$a_set['id']);
		$this->tpl->setVariable('VAL_TITLE',$a_set['title']);
		if(strlen($a_set['description']))
		{
			$this->tpl->setVariable('VAL_DESCRIPTION',$a_set['description']);
		}
		$defs = ilAdvancedMDFieldDefinition::_getDefinitionsByRecordId($a_set['id']);
		if(!count($defs))
		{
			$this->tpl->setVariable('TXT_FIELDS',$this->lng->txt('md_adv_no_fields'));
		}
		foreach($defs as $definition_obj)
		{
			$this->tpl->setCurrentBlock('field_entry');
			$this->tpl->setVariable('FIELD_NAME',$definition_obj->getTitle());
			$this->tpl->parseCurrentBlock();
		}
		
		$this->tpl->setVariable('ACTIVE_CHECKED',$a_set['active'] ? ' checked="checked" ' : '');
		$this->tpl->setVariable('ACTIVE_ID',$a_set['id']);
		
		$this->ctrl->setParameter($this->parent_obj,'record_id',$a_set['id']);
		$this->tpl->setVariable('EDIT_LINK',$this->ctrl->getLinkTarget($this->parent_obj,'editRecord'));
		$this->tpl->setVariable('TXT_EDIT_RECORD',$this->lng->txt('edit'));
	}
	
	/**
	 * Parse records
	 *
	 * @access public
	 * @param array array of record objects
	 * 
	 */
	public function parseRecords($a_records)
	{
	 	foreach($a_records as $record)
	 	{
			$tmp_arr['id'] = $record->getRecordId();
			$tmp_arr['active'] = $record->isActive();
			$tmp_arr['title'] = $record->getTitle();
			$tmp_arr['description'] = $record->getDescription();
			$tmp_arr['fields'] = array();
			$tmp_arr['obj_types'] = $record->getAssignedObjectTypes();
			
			$records_arr[] = $tmp_arr;
	 	}
	 	$this->setData($records_arr ? $records_arr : array());
	}
	
} 


?>