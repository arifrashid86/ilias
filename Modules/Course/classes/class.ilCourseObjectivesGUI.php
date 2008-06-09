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
* class ilobjcourseobjectivesgui
*
* @author Stefan Meyer <smeyer@databay.de> 
* @version $Id$
* 
* @extends Object
*/

class ilCourseObjectivesGUI
{
	const MODE_UNDEFINED = 0;
	const MODE_CREATE = 1;
	const MODE_UPDATE = 2;
	
	
	var $ctrl;
	var $ilias;
	var $ilErr;
	var $lng;
	var $tpl;

	var $course_obj;
	var $course_id;
	
	function ilCourseObjectivesGUI($a_course_id)
	{
		include_once './Modules/Course/classes/class.ilCourseObjective.php';

		global $ilCtrl,$lng,$ilErr,$ilias,$tpl,$tree,$ilTabs;

		$this->ctrl =& $ilCtrl;
		$this->ctrl->saveParameter($this,array("ref_id"));

		$this->ilErr =& $ilErr;
		$this->lng =& $lng;
		$this->lng->loadLanguageModule('crs');
		$this->tpl =& $tpl;
		$this->tree =& $tree;
		$this->tabs_gui =& $ilTabs;

		$this->course_id = $a_course_id;
		$this->__initCourseObject();
	}

	/**
	 * execute command
	 */
	function &executeCommand()
	{
		global $ilTabs;

		$ilTabs->setTabActive('crs_objectives');
		
		$cmd = $this->ctrl->getCmd();


		if (!$cmd = $this->ctrl->getCmd())
		{
			$cmd = "list";
		}
		
		$this->setSubTabs();
		$this->$cmd();
	}
	
	/**
	 * list objectives
	 *
	 * @access protected
	 * @param
	 * @return
	 */
	protected function listObjectives()
	{
	 	global $ilAccess,$ilErr,$ilObjDataCache;
	 	
		$_SESSION['objective_mode'] = self::MODE_UNDEFINED;
		if(!$ilAccess->checkAccess("write",'',$this->course_obj->getRefId()))
		{
			$this->ilErr->raiseError($this->lng->txt("msg_no_perm_write"),$this->ilErr->MESSAGE);
		}
		
		$this->tpl->addBlockFile('ADM_CONTENT','adm_content','tpl.crs_objectives.html','Modules/Course');
		
		include_once('./Modules/Course/classes/class.ilCourseObjectivesTableGUI.php');
		$table = new ilCourseObjectivesTableGUI($this,$this->course_obj);
		$table->setTitle($this->lng->txt('crs_objectives'),'icon_lobj.gif',$this->lng->txt('crs_objectives'));
		$table->parse(ilCourseObjective::_getObjectiveIds($this->course_obj->getId()));
		
		$this->tpl->setVariable('OBJECTIVES_TABLE',$table->getHTML());
	}
	
	/**
	 * save position
	 *
	 * @access protected
	 * @return
	 */
	protected function saveSorting()
	{
	 	global $ilAccess,$ilErr,$ilObjDataCache;
	 	
		if(!$ilAccess->checkAccess("write",'',$this->course_obj->getRefId()))
		{
			$this->ilErr->raiseError($this->lng->txt("msg_no_perm_write"),$this->ilErr->MESSAGE);
		}
		
		asort($_POST['position'],SORT_NUMERIC);
		
		$counter = 1;
		foreach($_POST['position'] as $objective_id => $position)
		{
			$objective = new ilCourseObjective($this->course_obj,$objective_id);
			$objective->writePosition($counter++);
		}
		ilUtil::sendInfo($this->lng->txt('crs_objective_saved_sorting'));
		$this->listObjectives();
	}

	function askDeleteObjective()
	{
		global $rbacsystem;

		// MINIMUM ACCESS LEVEL = 'write'
		if(!$rbacsystem->checkAccess("write", $this->course_obj->getRefId()))
		{
			$this->ilias->raiseError($this->lng->txt("msg_no_perm_write"),$this->ilErr->MESSAGE);
		}
		if(!count($_POST['objective']))
		{
			ilUtil::sendInfo($this->lng->txt('crs_no_objective_selected'));
			$this->listObjectives();
			
			return true;
		}

		$this->tpl->addBlockFile("ADM_CONTENT", "adm_content", "tpl.crs_objectives.html",'Modules/Course');

		ilUtil::sendInfo($this->lng->txt('crs_delete_objectve_sure'));

		$tpl =& new ilTemplate("tpl.table.html", true, true);
		$tpl->addBlockfile("TBL_CONTENT", "tbl_content", "tpl.crs_objectives_delete_row.html",'Modules/Course');

		$counter = 0;
		foreach($_POST['objective'] as $objective_id)
		{
			$objective_obj =& $this->__initObjectivesObject($objective_id);

			$tpl->setCurrentBlock("tbl_content");
			$tpl->setVariable("ROWCOL",ilUtil::switchColor(++$counter,"tblrow2","tblrow1"));
			$tpl->setVariable("TITLE",$objective_obj->getTitle());
			$tpl->setVariable("DESCRIPTION",$objective_obj->getDescription());
			$tpl->parseCurrentBlock();
		}

		$tpl->setVariable("FORMACTION",$this->ctrl->getFormAction($this));

		// Show action row
		$tpl->setCurrentBlock("tbl_action_btn");
		$tpl->setVariable("BTN_NAME",'deleteObjectives');
		$tpl->setVariable("BTN_VALUE",$this->lng->txt('delete'));
		$tpl->parseCurrentBlock();

		$tpl->setCurrentBlock("tbl_action_btn");
		$tpl->setVariable("BTN_NAME",'listObjectives');
		$tpl->setVariable("BTN_VALUE",$this->lng->txt('cancel'));
		$tpl->parseCurrentBlock();

		$tpl->setCurrentBlock("tbl_action_row");
		$tpl->setVariable("COLUMN_COUNTS",1);
		$tpl->setVariable("IMG_ARROW",ilUtil::getImagePath('arrow_downright.gif'));
		$tpl->parseCurrentBlock();


		// create table
		$tbl = new ilTableGUI();
		$tbl->setStyle('table','std');

		// title & header columns
		$tbl->setTitle($this->lng->txt("crs_objectives"),"icon_lobj.gif",$this->lng->txt("crs_objectives"));

		$tbl->setHeaderNames(array($this->lng->txt("title")));
		$tbl->setHeaderVars(array("title"), 
							array("ref_id" => $this->course_obj->getRefId(),
								  "cmdClass" => "ilcourseobjectivesgui",
								  "cmdNode" => $_GET["cmdNode"]));
		$tbl->setColumnWidth(array("50%"));

		$tbl->setLimit($_GET["limit"]);
		$tbl->setOffset($_GET["offset"]);
		$tbl->setMaxCount(count($_POST['objective']));

		// footer
		$tbl->disable("footer");
		$tbl->disable('sort');

		// render table
		$tbl->setTemplate($tpl);
		$tbl->render();

		$this->tpl->setVariable("OBJECTIVES_TABLE", $tpl->get());
		

		// Save marked objectives
		$_SESSION['crs_delete_objectives'] = $_POST['objective'];

		return true;
	}

	function deleteObjectives()
	{
		global $rbacsystem;

		// MINIMUM ACCESS LEVEL = 'write'
		if(!$rbacsystem->checkAccess("write", $this->course_obj->getRefId()))
		{
			$this->ilias->raiseError($this->lng->txt("msg_no_perm_write"),$this->ilErr->MESSAGE);
		}
		if(!count($_SESSION['crs_delete_objectives']))
		{
			ilUtil::sendInfo($this->lng->txt('crs_no_objective_selected'));
			$this->listObjectives();
			
			return true;
		}

		foreach($_SESSION['crs_delete_objectives'] as $objective_id)
		{
			$objective_obj =& $this->__initObjectivesObject($objective_id);
			$objective_obj->delete();
		}

		ilUtil::sendInfo($this->lng->txt('crs_objectives_deleted'));
		$this->listObjectives();

		return true;
	}



	function editQuestionAssignment()
	{
		global $rbacsystem;

		$this->tabs_gui->setSubTabActive('crs_objective_overview_question_assignment');

		// MINIMUM ACCESS LEVEL = 'write'
		if(!$rbacsystem->checkAccess("write", $this->course_obj->getRefId()))
		{
			$this->ilias->raiseError($this->lng->txt("msg_no_perm_write"),$this->ilErr->MESSAGE);
		}

		$this->tpl->addBlockfile('ADM_CONTENT','adm_content','tpl.crs_objectives_edit_question_assignments.html','Modules/Course');


		$this->tpl->setVariable("FORMACTION",$this->ctrl->getFormAction($this));
		$this->tpl->setVariable("CSS_TABLE",'fullwidth');
		$this->tpl->setVariable("WIDTH",'80%');
		$this->tpl->setVariable("COLUMN_COUNT",5);
		$this->tpl->setVariable("TBL_TITLE_IMG",ilUtil::getImagePath('icon_lobj.gif'));
		$this->tpl->setVariable("TBL_TITLE_IMG_ALT",$this->lng->txt('crs_objectives'));
		$this->tpl->setVariable("TBL_TITLE",$this->lng->txt('crs_objectives_edit_question_assignments'));
		
		$head_titles = array(array($this->lng->txt('title'),"35%"),
							 array($this->lng->txt('crs_objectives_nr_questions'),"10%"),
							 array($this->lng->txt('crs_objectives_max_points'),"10%"),
							 array($this->lng->txt('options'),"35%"));

		$counter = 0;
		foreach($head_titles as $title)
		{
			$this->tpl->setCurrentBlock("tbl_header_no_link");

			if(!$counter)
			{
				$this->tpl->setVariable("TBL_HEADER_COLSPAN",' colspan="2"');
				++$counter;
			}
			$this->tpl->setVariable("TBL_HEADER_CELL_NO_LINK",$title[0]);
			$this->tpl->setVariable("TBL_COLUMN_WIDTH_NO_LINK",$title[1]);
			$this->tpl->parseCurrentBlock();
		}

		foreach(ilCourseObjective::_getObjectiveIds($this->course_obj->getId()) as $objective_id)
		{
			$tmp_objective_obj =& $this->__initObjectivesObject($objective_id);
			
			$this->__initQuestionObject($objective_id);

			$counter = 1;
			foreach($this->objectives_qst_obj->getTests() as $test_data)
			{
				$show_buttons = true;

				$tmp_test =& ilObjectFactory::getInstanceByRefId($test_data['ref_id']);

				$this->tpl->setCurrentBlock("test_row");
				$this->tpl->setVariable("TEST_TITLE",$tmp_test->getTitle());
				$this->tpl->setVariable("TEST_QST",$this->objectives_qst_obj->getNumberOfQuestionsByTest($test_data['ref_id']));
				$this->tpl->setVariable("TEST_POINTS",$this->objectives_qst_obj->getMaxPointsByTest($test_data['ref_id']));

				// Options
				$this->tpl->setVariable("TXT_CHANGE_STATUS",$this->lng->txt('crs_change_status'));
				$this->tpl->setVariable("CHECK_CHANGE_STATUS",ilUtil::formCheckbox((int) $test_data['tst_status'],
																				   'test['.$test_data['test_objective_id'].'][status]'
																				   ,1));
				$this->tpl->setVariable("TXT_SUGGEST",$this->lng->txt('crs_suggest_lm'));
				$this->tpl->setVariable("SUGGEST_NAME",'test['.$test_data['test_objective_id'].'][limit]');
				$this->tpl->setVariable("SUGGEST_VALUE",(int) $test_data['tst_limit']);

				$this->tpl->parseCurrentBlock();



				++$counter;
			}
			$this->tpl->setCurrentBlock("objective_row");
			$this->tpl->setVariable("OBJ_TITLE",$tmp_objective_obj->getTitle());
			$this->tpl->setVariable("OBJ_DESCRIPTION",$tmp_objective_obj->getDescription());
			$this->tpl->setVariable("OBJ_QST",count($this->objectives_qst_obj->getQuestions()));
			$this->tpl->setVariable("OBJ_POINTS",$this->objectives_qst_obj->getMaxPointsByObjective());
			$this->tpl->setVariable("ROWSPAN",$counter);
			$this->tpl->parseCurrentBlock();
			
			// Options
			unset($tmp_objective_obj);
		}
		// Buttons
		if($show_buttons)
		{
			$this->tpl->setCurrentBlock("edit_footer");
			$this->tpl->setVariable("TXT_RESET",$this->lng->txt('reset'));
			$this->tpl->setVariable("TXT_UPDATE",$this->lng->txt('save'));
			$this->tpl->setVariable("CMD_UPDATE",'updateQuestionAssignment');
			$this->tpl->parseCurrentBlock();
		}
	}

	function updateQuestionAssignment()
	{
		global $rbacsystem;

		$this->tabs_gui->setSubTabActive('crs_objective_overview_question_assignment');


		// MINIMUM ACCESS LEVEL = 'write'
		if(!$rbacsystem->checkAccess("write", $this->course_obj->getRefId()))
		{
			$this->ilias->raiseError($this->lng->txt("msg_no_perm_write"),$this->ilErr->MESSAGE);
		}
		if(!is_array($_POST['test']))
		{
			ilUtil::sendInfo('Internal error: CRSM learning objectives');
			$this->editQuestionAssignment();

			return false;
		}
		// Validate
		foreach($_POST['test'] as $test_obj_id => $data)
		{
			if(!preg_match('/1?[0-9][0-9]?/',$data['limit']) or 
			   $data['limit'] < 0 or 
			   $data['limit'] > 100)
			{
				ilUtil::sendInfo($this->lng->txt('crs_objective_insert_percent'));
				$this->editQuestionAssignment();

				return false;
			}
		}
		
		foreach($_POST['test'] as $test_obj_id => $data)
		{
			include_once './Modules/Course/classes/class.ilCourseObjectiveQuestion.php';

			$test_data = ilCourseObjectiveQuestion::_getTest($test_obj_id);

			$this->__initQuestionObject($test_data['objective_id']);
			$this->objectives_qst_obj->setTestStatus($data['status'] ? 1 : 0);
			$this->objectives_qst_obj->setTestSuggestedLimit($data['limit']);
			$this->objectives_qst_obj->updateTest($test_obj_id);
		}
		ilUtil::sendInfo($this->lng->txt('crs_objective_updated_test'));
		$this->editQuestionAssignment();

		return true;
	}
		

	// PRIVATE
	function __initCourseObject()
	{
		if(!$this->course_obj =& ilObjectFactory::getInstanceByRefId($this->course_id,false))
		{
			$this->ilErr->raiseError("ilCourseObjectivesGUI: cannot create course object",$this->ilErr->MESSAGE);
			exit;
		}
		// do i need members?
		$this->course_obj->initCourseMemberObject();

		return true;
	}

	function &__initObjectivesObject($a_id = 0)
	{
		return $this->objectives_obj = new ilCourseObjective($this->course_obj,$a_id);
	}

	function __initLMObject($a_objective_id = 0)
	{
		include_once './Modules/Course/classes/class.ilCourseObjectiveMaterials.php';
		$this->objectives_lm_obj =& new ilCourseObjectiveMaterials($a_objective_id);

		return true;
	}

	function __initQuestionObject($a_objective_id = 0)
	{
		include_once './Modules/Course/classes/class.ilCourseObjectiveQuestion.php';
		$this->objectives_qst_obj =& new ilCourseObjectiveQuestion($a_objective_id);

		return true;
	}

	/**
	* set sub tabs
	*/
	function setSubTabs()
	{
		global $ilTabs;

		$ilTabs->addSubTabTarget("crs_objective_overview_objectives",
								 $this->ctrl->getLinkTarget($this, "listObjectives"),
								 array("listObjectives", "moveObjectiveUp", "moveObjectiveDown", "listAssignedLM"),
								 array(),
								 '',
								 true);
			
		$ilTabs->addSubTabTarget("crs_objective_overview_question_assignment",
								 $this->ctrl->getLinkTarget($this, "editQuestionAssignment"),
								 "editQuestionAssignment",
								 array(),
								 '',
								 false);

	}
	
	
	/**
	 * create objective
	 *
	 * @access public
	 * @param
	 * @return
	 */
	public function create()
	{
		global $tpl;
		
		$_SESSION['objective_mode'] = self::MODE_CREATE;
		
		$this->ctrl->saveParameter($this,'objective_id');
		$w_tpl = $this->initWizard(1);
		
		if(!is_object($this->objective))
		{
			$this->objective = new ilCourseObjective($this->course_obj,(int) $_GET['objective_id']);
		}
		
		
		$this->initFormTitle('create',1);
		$w_tpl->setVariable('WIZ_CONTENT',$this->form->getHtml());
		$tpl->setContent($w_tpl->get());
	}

	/**
	 * edit objective
	 *
	 * @access public
	 * @return
	 */
	public function edit()
	{
		global $tpl;
		
		$_SESSION['objective_mode'] = self::MODE_UPDATE;
		
		$this->ctrl->saveParameter($this,'objective_id');
		$w_tpl = $this->initWizard(1);
		
		if(!$_GET['objective_id'])
		{
			ilUtil::sendInfo($this->lng->txt('crs_no_objective_selected'),true);
			$this->ctrl->redirect($this,'listObjectives');
		}
		
		if(!is_object($this->objective))
		{
			$this->objective = new ilCourseObjective($this->course_obj,(int) $_GET['objective_id']);
		}
		
		$this->initFormTitle('create',1);
		$w_tpl->setVariable('WIZ_CONTENT',$this->form->getHtml());
		$tpl->setContent($w_tpl->get());
	}

	/**
	 * save
	 *
	 * @access public
	 * @param
	 * @return
	 */
	public function save()
	{
		global $ilAccess,$ilErr;
		
		if(!$ilAccess->checkAccess('write','',$this->course_obj->getRefId()))
		{
			$ilErr->raiseError($this->lng->txt('permission_denied'),$ilErr->WARNING);
		}
		
		$this->objective = new ilCourseObjective($this->course_obj,(int) $_GET['objective_id']);
		$this->objective->setTitle(ilUtil::stripSlashes($_POST['title']));
		$this->objective->setDescription(ilUtil::stripSlashes($_POST['description']));
		
		if(!$this->objective->validate())
		{
			ilUtil::sendInfo($this->lng->txt('crs_no_title_given'));
			$this->create();
			return false;
		}
		
		if(!$_GET['objective_id'])
		{
			$objective_id = $this->objective->add();
			ilUtil::sendInfo($this->lng->txt('crs_added_objective'),true);
		}
		else
		{
			$this->objective->update();
			ilUtil::sendInfo($this->lng->txt('crs_objective_modified'),true);
			$objective_id = $_GET['objective_id'];
		}
		
		$this->ctrl->saveParameter($this,'objective_id');
		$this->ctrl->setParameter($this,'objective_id',$objective_id);
		$this->ctrl->redirect($this,'materialAssignment');
		return true;
	}
	
	/**
	 * material assignment
	 *
	 * @access protected
	 * @return
	 */
	protected function materialAssignment()
	{
		global $ilAccess,$ilErr,$tpl;
		
		if(!$ilAccess->checkAccess('write','',$this->course_obj->getRefId()))
		{
			$ilErr->raiseError($this->lng->txt('permission_denied'),$ilErr->WARNING);
		}
		if(!$_GET['objective_id'])
		{
			ilUtil::sendInfo($this->lng->txt('crs_no_objective_selected'),true);
			$this->ctrl->redirect($this,'listObjectives');
		}

		$this->ctrl->saveParameter($this,'objective_id');

		$this->objective = new ilCourseObjective($this->course_obj,(int) $_GET['objective_id']);
		
		include_once('./Modules/Course/classes/class.ilCourseObjectiveMaterialAssignmentTableGUI.php');
		$table = new ilCourseObjectiveMaterialAssignmentTableGUI($this,$this->course_obj,(int) $_GET['objective_id']);
		$table->setTitle($this->lng->txt('crs_objective_wiz_materials'),
			'icon_lobj.gif',$this->lng->txt('crs_objectives'));

		include_once('Modules/Course/classes/class.ilCourseObjectiveMaterials.php');
		$table->parse(ilCourseObjectiveMaterials::_getAssignableMaterials($this->course_obj->getRefId()));
		
		$w_tpl = $this->initWizard(2);
		$w_tpl->setVariable('WIZ_CONTENT',$table->getHTML());
		$tpl->setContent($w_tpl->get());
	}
	
	/**
	 * update material assignment
	 *
	 * @access protected
	 * @param
	 * @return
	 */
	protected function updateMaterialAssignment()
	{
		global $ilAccess,$ilErr,$ilObjDataCache;
		
		if(!$ilAccess->checkAccess('write','',$this->course_obj->getRefId()))
		{
			$ilErr->raiseError($this->lng->txt('permission_denied'),$ilErr->WARNING);
		}
		if(!$_GET['objective_id'])
		{
			ilUtil::sendInfo($this->lng->txt('crs_no_objective_selected'),true);
			$this->ctrl->redirect($this,'listObjectives');
		}

		$this->__initLMObject((int) $_GET['objective_id']);
		$this->objectives_lm_obj->deleteAll();
		
		if(is_array($_POST['materials']))
		{
			foreach($_POST['materials'] as $node_id)
			{
				$obj_id = $ilObjDataCache->lookupObjId($node_id);
				$type = $ilObjDataCache->lookupType($obj_id);
				
				$this->objectives_lm_obj->setLMRefId($node_id);
				$this->objectives_lm_obj->setLMObjId($obj_id);
				$this->objectives_lm_obj->setType($type);
				$this->objectives_lm_obj->add();
			}
		}
		if(is_array($_POST['chapters']))
		{
			foreach($_POST['chapters'] as $chapter)
			{
				include_once('./Modules/LearningModule/classes/class.ilLMObject.php');
				
				list($ref_id,$chapter_id) = explode('_',$chapter);
				
				$this->objectives_lm_obj->setLMRefId($ref_id);
				$this->objectives_lm_obj->setLMObjId($chapter_id);
				$this->objectives_lm_obj->setType(ilLMObject::_lookupType($chapter_id));
				$this->objectives_lm_obj->add();
			}
		}
		ilUtil::sendInfo($this->lng->txt('crs_objectives_assigned_lm'));
		$this->selfAssessmentAssignment();
		
	}

	/**
	 * self assessment assignemnt
	 *
	 * @access protected
	 * @return
	 */
	protected function selfAssessmentAssignment()
	{
		global $ilAccess,$ilErr,$tpl;
		
		if(!$ilAccess->checkAccess('write','',$this->course_obj->getRefId()))
		{
			$ilErr->raiseError($this->lng->txt('permission_denied'),$ilErr->WARNING);
		}
		if(!$_GET['objective_id'])
		{
			ilUtil::sendInfo($this->lng->txt('crs_no_objective_selected'),true);
			$this->ctrl->redirect($this,'listObjectives');
		}

		$this->ctrl->saveParameter($this,'objective_id');

		$this->objective = new ilCourseObjective($this->course_obj,(int) $_GET['objective_id']);
		
		include_once('./Modules/Course/classes/class.ilCourseObjectiveQuestionAssignmentTableGUI.php');
		$table = new ilCourseObjectiveQuestionAssignmentTableGUI($this,
			$this->course_obj,
			(int) $_GET['objective_id'],
			ilCourseObjectiveQuestion::TYPE_SELF_ASSESSMENT);
		$table->setTitle($this->lng->txt('crs_objective_wiz_self'),
			'icon_lobj.gif',$this->lng->txt('crs_objective'));
		$table->parse(ilCourseObjectiveQuestion::_getAssignableTests($this->course_obj->getRefId()));
		
		$w_tpl = $this->initWizard(3);
		$w_tpl->setVariable('WIZ_CONTENT',$table->getHTML());
		$tpl->setContent($w_tpl->get());
	}
	
	/**
	 * update self assessment assignment
	 *
	 * @access protected
	 * @param
	 * @return
	 */
	protected function updateSelfAssessmentAssignment()
	{
		global $ilAccess,$ilErr,$ilObjDataCache;
		
		$checked_questions = $_POST['questions'] ? $_POST['questions'] : array();
		
		
		if(!$ilAccess->checkAccess('write','',$this->course_obj->getRefId()))
		{
			$ilErr->raiseError($this->lng->txt('permission_denied'),$ilErr->WARNING);
		}
		if(!$_GET['objective_id'])
		{
			ilUtil::sendInfo($this->lng->txt('crs_no_objective_selected'),true);
			$this->ctrl->redirect($this,'listObjectives');
		}

		$this->__initQuestionObject((int) $_GET['objective_id']);

		// Delete unchecked
		foreach($this->objectives_qst_obj->getSelfAssessmentQuestions() as $question)
		{
			$id = $question['ref_id'].'_'.$question['question_id'];
			if(!in_array($id,$checked_questions))
			{
				$this->objectives_qst_obj->delete($question['qst_ass_id']);
			}
		}
		// Add checked
		foreach($checked_questions as $question_id)
		{
			list($test_ref_id,$qst_id) = explode('_',$question_id);
			$test_obj_id = $ilObjDataCache->lookupObjId($test_ref_id);
	
			if($this->objectives_qst_obj->isSelfAssessmentQuestion($qst_id))
			{
				continue;
			}
			$this->objectives_qst_obj->setTestStatus(ilCourseObjectiveQuestion::TYPE_SELF_ASSESSMENT);
			$this->objectives_qst_obj->setTestRefId($test_ref_id);
			$this->objectives_qst_obj->setTestObjId($test_obj_id);
			$this->objectives_qst_obj->setQuestionId($qst_id);
			$this->objectives_qst_obj->add();
		}
		
		// TODO: not nice
		include_once './Modules/Course/classes/class.ilCourseObjectiveQuestion.php';
		$this->questions = new ilCourseObjectiveQuestion((int) $_GET['objective_id']);
		$this->questions->updateLimits();

		ilUtil::sendInfo($this->lng->txt('crs_objectives_assigned_lm'));
		$this->selfAssessmentLimits();
	}
	
	/**
	 * self assessment limits
	 *
	 * @access protected
	 * @param
	 * @return
	 */
	protected function selfAssessmentLimits()
	{
		global $ilAccess,$ilErr,$tpl;
		
		if(!$ilAccess->checkAccess('write','',$this->course_obj->getRefId()))
		{
			$ilErr->raiseError($this->lng->txt('permission_denied'),$ilErr->WARNING);
		}
		if(!$_GET['objective_id'])
		{
			ilUtil::sendInfo($this->lng->txt('crs_no_objective_selected'),true);
			$this->ctrl->redirect($this,'listObjectives');
		}

		$this->ctrl->saveParameter($this,'objective_id');
		$this->objective = new ilCourseObjective($this->course_obj,(int) $_GET['objective_id']);
		
		$this->__initQuestionObject((int) $_GET['objective_id']);
		
		$this->initFormLimits('selfAssessment');
		$w_tpl = $this->initWizard(4);
		$w_tpl->setVariable('WIZ_CONTENT',$this->form->getHtml());
		$tpl->setContent($w_tpl->get());
	}
	
	/**
	 * update self assessment limits
	 *
	 * @access protected
	 * @param
	 * @return
	 */
	protected function updateSelfAssessmentLimits()
	{
		global $ilAccess,$ilErr,$ilObjDataCache;
		
		if(!$ilAccess->checkAccess('write','',$this->course_obj->getRefId()))
		{
			$ilErr->raiseError($this->lng->txt('permission_denied'),$ilErr->WARNING);
		}
		if(!$_GET['objective_id'])
		{
			ilUtil::sendInfo($this->lng->txt('crs_no_objective_selected'),true);
			$this->ctrl->redirect($this,'listObjectives');
		}

		$this->__initQuestionObject((int) $_GET['objective_id']);

		if((int) $_POST['limit'] <= 0 or (int) $_POST['limit'] > $this->objectives_qst_obj->getSelfAssessmentPoints())
		{
			ilUtil::sendInfo(sprintf($this->lng->txt('crs_objective_err_limit'),0,$this->objectives_qst_obj->getSelfAssessmentPoints()));
			$this->selfAssessmentLimits();
			return false;
		}
		
		foreach($this->objectives_qst_obj->getSelfAssessmentTests() as $test)
		{
			$this->objectives_qst_obj->setTestStatus(ilCourseObjectiveQuestion::TYPE_SELF_ASSESSMENT);
			$this->objectives_qst_obj->setTestSuggestedLimit((int) $_POST['limit']);
			$this->objectives_qst_obj->updateTest($test['test_objective_id']);
		}

		ilUtil::sendInfo($this->lng->txt('settings_saved'));
		$this->finalTestAssignment();
		
	}
	
	
	/**
	 * final test assignment
	 *
	 * @access protected
	 * @param
	 * @return
	 */
	protected function finalTestAssignment()
	{
		global $ilAccess,$ilErr,$tpl;
		
		if(!$ilAccess->checkAccess('write','',$this->course_obj->getRefId()))
		{
			$ilErr->raiseError($this->lng->txt('permission_denied'),$ilErr->WARNING);
		}
		if(!$_GET['objective_id'])
		{
			ilUtil::sendInfo($this->lng->txt('crs_no_objective_selected'),true);
			$this->ctrl->redirect($this,'listObjectives');
		}

		$this->ctrl->saveParameter($this,'objective_id');

		$this->objective = new ilCourseObjective($this->course_obj,(int) $_GET['objective_id']);
		
		include_once('./Modules/Course/classes/class.ilCourseObjectiveQuestionAssignmentTableGUI.php');
		$table = new ilCourseObjectiveQuestionAssignmentTableGUI($this,
			$this->course_obj,
			(int) $_GET['objective_id'],
			ilCourseObjectiveQuestion::TYPE_FINAL_TEST);

		$table->setTitle($this->lng->txt('crs_objective_wiz_final'),
			'icon_lobj.gif',$this->lng->txt('crs_objective'));
		$table->parse(ilCourseObjectiveQuestion::_getAssignableTests($this->course_obj->getRefId()));
		
		$w_tpl = $this->initWizard(5);
		$w_tpl->setVariable('WIZ_CONTENT',$table->getHTML());
		$tpl->setContent($w_tpl->get());
		
	}

	/**
	 * update self assessment assignment
	 *
	 * @access protected
	 * @param
	 * @return
	 */
	protected function updateFinalTestAssignment()
	{
		global $ilAccess,$ilErr,$ilObjDataCache;
		
		$checked_questions = $_POST['questions'] ? $_POST['questions'] : array();
		
		
		if(!$ilAccess->checkAccess('write','',$this->course_obj->getRefId()))
		{
			$ilErr->raiseError($this->lng->txt('permission_denied'),$ilErr->WARNING);
		}
		if(!$_GET['objective_id'])
		{
			ilUtil::sendInfo($this->lng->txt('crs_no_objective_selected'),true);
			$this->ctrl->redirect($this,'listObjectives');
		}

		$this->__initQuestionObject((int) $_GET['objective_id']);

		// Delete unchecked
		foreach($this->objectives_qst_obj->getFinalTestQuestions() as $question)
		{
			$id = $question['ref_id'].'_'.$question['question_id'];
			if(!in_array($id,$checked_questions))
			{
				$this->objectives_qst_obj->delete($question['qst_ass_id']);
			}
		}
		// Add checked
		foreach($checked_questions as $question_id)
		{
			list($test_ref_id,$qst_id) = explode('_',$question_id);
			$test_obj_id = $ilObjDataCache->lookupObjId($test_ref_id);
	
			if($this->objectives_qst_obj->isFinalTestQuestion($qst_id))
			{
				continue;
			}
			
			$this->objectives_qst_obj->setTestStatus(ilCourseObjectiveQuestion::TYPE_FINAL_TEST);
			$this->objectives_qst_obj->setTestRefId($test_ref_id);
			$this->objectives_qst_obj->setTestObjId($test_obj_id);
			$this->objectives_qst_obj->setQuestionId($qst_id);
			$this->objectives_qst_obj->add();
		}
		
		// TODO: not nice
		include_once './Modules/Course/classes/class.ilCourseObjectiveQuestion.php';
		$this->questions = new ilCourseObjectiveQuestion((int) $_GET['objective_id']);
		$this->questions->updateLimits();

		ilUtil::sendInfo($this->lng->txt('crs_objectives_assigned_lm'));
		$this->finalTestLimits();
	}
	
	/**
	 * self assessment limits
	 *
	 * @access protected
	 * @param
	 * @return
	 */
	protected function finalTestLimits()
	{
		global $ilAccess,$ilErr,$tpl;
		
		if(!$ilAccess->checkAccess('write','',$this->course_obj->getRefId()))
		{
			$ilErr->raiseError($this->lng->txt('permission_denied'),$ilErr->WARNING);
		}
		if(!$_GET['objective_id'])
		{
			ilUtil::sendInfo($this->lng->txt('crs_no_objective_selected'),true);
			$this->ctrl->redirect($this,'listObjectives');
		}

		$this->ctrl->saveParameter($this,'objective_id');
		$this->objective = new ilCourseObjective($this->course_obj,(int) $_GET['objective_id']);
		
		$this->__initQuestionObject((int) $_GET['objective_id']);
		
		$this->initFormLimits('final');
		$w_tpl = $this->initWizard(6);
		$w_tpl->setVariable('WIZ_CONTENT',$this->form->getHtml());
		$tpl->setContent($w_tpl->get());
	}
	
	/**
	 * update self assessment limits
	 *
	 * @access protected
	 * @param
	 * @return
	 */
	protected function updateFinalTestLimits()
	{
		global $ilAccess,$ilErr,$ilObjDataCache;
		
		if(!$ilAccess->checkAccess('write','',$this->course_obj->getRefId()))
		{
			$ilErr->raiseError($this->lng->txt('permission_denied'),$ilErr->WARNING);
		}
		if(!$_GET['objective_id'])
		{
			ilUtil::sendInfo($this->lng->txt('crs_no_objective_selected'),true);
			$this->ctrl->redirect($this,'listObjectives');
		}

		$this->__initQuestionObject((int) $_GET['objective_id']);

		if((int) $_POST['limit'] <= 0 or (int) $_POST['limit'] > $this->objectives_qst_obj->getFinalTestPoints())
		{
			ilUtil::sendInfo(sprintf($this->lng->txt('crs_objective_err_limit'),0,$this->objectives_qst_obj->getFinalTestPoints()));
			$this->finalTestLimits();
			return false;
		}
		
		foreach($this->objectives_qst_obj->getFinalTests() as $test)
		{
			$this->objectives_qst_obj->setTestStatus(ilCourseObjectiveQuestion::TYPE_FINAL_TEST);
			$this->objectives_qst_obj->setTestSuggestedLimit((int) $_POST['limit']);
			$this->objectives_qst_obj->updateTest($test['test_objective_id']);
		}

		ilUtil::sendInfo($this->lng->txt('crs_added_objective'));
		$this->listObjectives();
	}
	
	/**
	 * init limit form
	 *
	 * @access protected
	 * @param string mode selfAssessment or final
	 * @return
	 */
	protected function initFormLimits($a_mode)
	{
		if(!is_object($this->form))
		{
			include_once('./Services/Form/classes/class.ilPropertyFormGUI.php');
			$this->form = new ilPropertyFormGUI();
		}
		$this->form->setFormAction($this->ctrl->getFormAction($this));
		$this->form->setTableWidth('100%');
		$this->form->setTitleIcon(ilUtil::getImagePath('icon_lobj.gif'),$this->lng->txt('crs_objective'));
		
		switch($a_mode)
		{
			case 'selfAssessment':
				$this->form->setTitle($this->lng->txt('crs_objective_wiz_self_limit'));
				$this->form->addCommandButton('updateSelfAssessmentLimits',$this->lng->txt('crs_wiz_next'));
				$this->form->addCommandButton('selfAssessmentAssignment',$this->lng->txt('crs_wiz_back'));

				$tests = $this->objectives_qst_obj->getSelfAssessmentTests();
				$max_points = $this->objectives_qst_obj->getSelfAssessmentPoints();

				break;
			
			case 'final':
				$this->form->setTitle($this->lng->txt('crs_objective_wiz_final_limit'));
				$this->form->addCommandButton('updateFinalTestLimits',$this->lng->txt('crs_wiz_next'));
				$this->form->addCommandButton('finalTestAssignment',$this->lng->txt('crs_wiz_back'));

				$tests = $this->objectives_qst_obj->getFinalTests();
				$max_points = $this->objectives_qst_obj->getFinalTestPoints();

				break;
		}
		
		$over = new ilCustomInputGUI($this->lng->txt('crs_objective_qst_summary'),'');
		
		$tpl = new ilTemplate('tpl.crs_objective_qst_summary.html',true,true,'Modules/Course');
		
		
		$limit = 0;
		
		foreach($tests as $test)
		{
			$limit = $test['limit'];

			foreach($this->objectives_qst_obj->getQuestionsOfTest($test['obj_id']) as $question)
			{
				$tpl->setCurrentBlock('qst');
				$tpl->setVariable('QST_TITLE',$question['title']);
				if(strlen($question['description']))
				{
					$tpl->setVariable('QST_DESCRIPTION',$question['description']);
				}
				$tpl->setVariable('QST_POINTS',$question['points'].' '.
					'Punkt(e)');
					#$this->lng->txt('crs_objective_points'));
				$tpl->parseCurrentBlock();
			}
			$tpl->setCurrentBlock('tst');
			$tpl->setVariable('TST_TITLE',ilObject::_lookupTitle($test['obj_id']));
			if($desc = ilObject::_lookupDescription($test['obj_id']))
			{
				$tpl->setVariable('TST_DESC',$desc);
			}
			$tpl->setVariable('TST_TYPE_IMG',ilUtil::getTypeIconPath('tst',$test['obj_id'],'tiny'));
			$tpl->setVariable('TST_ALT_IMG',$this->lng->txt('obj_tst'));
			$tpl->parseCurrentBlock();
		}
		
		$tpl->setVariable('TXT_ALL_POINTS',$this->lng->txt('crs_objective_all_points'));
		$tpl->setVariable('TXT_POINTS',$this->lng->txt('crs_objective_points'));
		$tpl->setVariable('POINTS',$max_points);
		
		$over->setHtml($tpl->get());
		$this->form->addItem($over);
		
		$req = new ilTextInputGUI($this->lng->txt('crs_obj_required_points'),'limit');
		$req->setValue($limit);
		$req->setMaxLength(5);
		$req->setSize(3);
		$req->setRequired(true);
		$req->setInfo($this->lng->txt('crs_obj_required_info'));
		
		$this->form->addItem($req);
		
	}

	
	/**
	 * init form title
	 *
	 * @access protected
	 * @return
	 */
	protected function initFormTitle($a_mode,$a_step_number)
	{
		if(!is_object($this->form))
		{
			include_once('./Services/Form/classes/class.ilPropertyFormGUI.php');
			$this->form = new ilPropertyFormGUI();
		}
		$this->form->setFormAction($this->ctrl->getFormAction($this));
		$this->form->setTitleIcon(ilUtil::getImagePath('icon_lobj.gif'),$this->lng->txt('crs_objective'));
		
		switch($a_mode)
		{
			case 'create':
				$this->form->setTitle($this->lng->txt('crs_objective_wiz_title'));
				$this->form->addCommandButton('save',$this->lng->txt('crs_wiz_next'));
				$this->form->addCommandButton('listObjectives',$this->lng->txt('cancel'));
				break;
			
			case 'update':
				break;
		}
		
		$title = new ilTextInputGUI($this->lng->txt('title'),'title');
		$title->setValue($this->objective->getTitle());
		$title->setRequired(true);
		$title->setSize(40);
		$title->setMaxLength(70);
		$this->form->addItem($title);
		
		$desc = new ilTextAreaInputGUI($this->lng->txt('description'),'description');
		$desc->setValue($this->objective->getDescription());
		$desc->setCols(40);
		$desc->setRows(5);
		$this->form->addItem($desc);
		
		
	}
	
	
	/**
	 * init wizard
	 
	 * @access protected
	 * @param string mode 'create' or 'edit'
	 * @return
	 */
	protected function initWizard($a_step_number)
	{
		$options = array(
			1 => $this->lng->txt('crs_objective_wiz_title'),
			2 => $this->lng->txt('crs_objective_wiz_materials'),
			3 => $this->lng->txt('crs_objective_wiz_self'),
			4 => $this->lng->txt('crs_objective_wiz_self_limit'),
			5 => $this->lng->txt('crs_objective_wiz_final'),
			6 => $this->lng->txt('crs_objective_wiz_final_limit'));
			
		$info = array(
			1 => $this->lng->txt('crs_objective_wiz_title_info'),
			2 => $this->lng->txt('crs_objective_wiz_materials_info'),
			3 => $this->lng->txt('crs_objective_wiz_self_info'),
			4 => $this->lng->txt('crs_objective_wiz_self_limit_info'),
			5 => $this->lng->txt('crs_objective_wiz_final_info'),
			6 => $this->lng->txt('crs_objective_wiz_final_limit_info'));

		$links = array(
			1 => $this->ctrl->getLinkTarget($this,'edit'),
			2 => $this->ctrl->getLinkTarget($this,'materialAssignment'),
			3 => $this->ctrl->getLinkTarget($this,'selfAssessmentAssignment'),
			4 => $this->ctrl->getLinkTarget($this,'selfAssessmentLimits'),
			5 => $this->ctrl->getLinkTarget($this,'finalTestAssignment'),
			6 => $this->ctrl->getLinkTarget($this,'finalTestLimits'));
		
		
		$tpl = new ilTemplate('tpl.objective_wizard.html',true,true,'Modules/Course');
		
		if($_SESSION['objective_mode'] == self::MODE_CREATE)
		{
			$tpl->setCurrentBlock('step_info');
			$tpl->setVariable('STEP_INFO_STEP',$this->lng->txt('crs_objective_step'));
			$tpl->setVariable('STEP_INFO_NUM',$a_step_number);
			$tpl->setVariable('STEP_INFO_INFO',$info[$a_step_number]);
			$tpl->parseCurrentBlock();
		}
		
		
		$tpl->setVariable('WIZ_IMG',ilUtil::getImagePath('icon_lobj.gif'));
		$tpl->setVariable('WIZ_IMG_ALT',$this->lng->txt('crs_objectives'));
		
		if($_SESSION['objective_mode'] == self::MODE_CREATE)
		{
			$tpl->setVariable('WIZ_NAV_TITLE',$this->lng->txt('crs_add_objective'));
		}
		else
		{
			$tpl->setVariable('WIZ_NAV_TITLE',$this->lng->txt('crs_update_objective'));
		}
		
		foreach($options as $step => $title)
		{
			if($_SESSION['objective_mode'] == self::MODE_UPDATE)
			{
				$tpl->setCurrentBlock('begin_link_option');
				$tpl->setVariable('WIZ_OPTION_LINK',$links[$step]);
				$tpl->parseCurrentBlock();
			
				$tpl->touchBlock('end_link_option');
			}
			

			$tpl->setCurrentBlock('nav_option');
			$tpl->setVariable('OPTION_CLASS',$step == $a_step_number ? 'option_value_details' : 'std');
			$tpl->setVariable('WIZ_NUM',$step.'.');
			$tpl->setVariable('WIZ_OPTION',$title);
			$tpl->parseCurrentBlock();
		}
		
		
		return $tpl;
	}
	
}
?>