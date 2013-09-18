<?php
/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once 'Modules/Test/classes/class.ilTestRandomQuestionSetConfig.php';
require_once 'Modules/Test/classes/class.ilTestRandomQuestionSetSourcePoolDefinitionList.php';
require_once 'Modules/Test/classes/class.ilTestRandomQuestionSetSourcePoolDefinitionFactory.php';

/**
 * GUI class that manages the question set configuration for continues tests
 *
 * @author		Björn Heyser <bheyser@databay.de>
 * @version		$Id$
 *
 * @package		Modules/Test
 * 
 * @ilCtrl_Calls ilTestRandomQuestionSetConfigGUI: ilTestRandomQuestionSetGeneralConfigFormGUI
 * @ilCtrl_Calls ilTestRandomQuestionSetConfigGUI: ilTestRandomQuestionSetSourcePoolDefinitionListToolbarGUI
 * @ilCtrl_Calls ilTestRandomQuestionSetConfigGUI: ilTestRandomQuestionSetSourcePoolDefinitionListTableGUI
 * @ilCtrl_Calls ilTestRandomQuestionSetConfigGUI: ilTestRandomQuestionSetPoolDefinitionFormGUI
 */
class ilTestRandomQuestionSetConfigGUI
{
	/**
	 * command constants
	 */
	const CMD_SHOW_GENERAL_CONFIG_FORM = 'showGeneralConfigForm';
	const CMD_SAVE_GENERAL_CONFIG_FORM = 'saveGeneralConfigForm';
	const CMD_SHOW_SRC_POOL_DEF_LIST = 'showSourcePoolDefinitionList';
	const CMD_SAVE_SRC_POOL_DEF_LIST = 'saveSourcePoolDefinitionList';
	const CMD_DELETE_SINGLE_SRC_POOL_DEF = 'deleteSingleSourcePoolDefinition';
	const CMD_DELETE_MULTI_SRC_POOL_DEFS = 'deleteMultipleSourcePoolDefinitions';
	const CMD_SHOW_CREATE_SRC_POOL_DEF_FORM = 'showCreateSourcePoolDefinitionForm';
	const CMD_SAVE_CREATE_SRC_POOL_DEF_FORM = 'saveCreateSourcePoolDefinitionForm';
	const CMD_SHOW_EDIT_SRC_POOL_DEF_FORM = 'showEditSourcePoolDefinitionForm';
	const CMD_SAVE_EDIT_SRC_POOL_DEF_FORM = 'saveEditSourcePoolDefinitionForm';
	
	/**
	 * global $ilCtrl object
	 * 
	 * @var ilCtrl
	 */
	public $ctrl = null;
	
	/**
	 * global $ilAccess object
	 * 
	 * @var ilAccess
	 */
	public $access = null;
	
	/**
	 * global $ilTabs object
	 *
	 * @var ilTabsGUI
	 */
	public $tabs = null;
	
	/**
	 * global $lng object
	 * 
	 * @var ilLanguage
	 */
	public $lng = null;
	
	/**
	 * global $tpl object
	 * 
	 * @var ilTemplate
	 */
	public $tpl = null;
	
	/**
	 * global $ilDB object
	 * 
	 * @var ilDB
	 */
	public $db = null;
	
	/**
	 * global $tree object
	 * 
	 * @var ilTree
	 */
	public $tree = null;
	
	/**
	 * object instance for current test
	 *
	 * @var ilObjTest
	 */
	public $testOBJ = null;
	
	/**
	 * object instance managing the dynamic question set config
	 *
	 * @var ilTestRandomQuestionSetConfig 
	 */
	protected $questionSetConfig = null;

	/**
	 * @var ilTestRandomQuestionSetSourcePoolDefinitionFactory
	 */
	protected $sourcePoolDefinitionFactory = null;
	
	/**
	 * Constructor
	 */
	public function __construct(ilCtrl $ctrl, ilAccessHandler $access, ilTabsGUI $tabs, ilLanguage $lng, ilTemplate $tpl, ilDB $db, ilTree $tree, ilObjTest $testOBJ)
	{
		$this->ctrl = $ctrl;
		$this->access = $access;
		$this->tabs = $tabs;
		$this->lng = $lng;
		$this->tpl = $tpl;
		$this->db = $db;
		$this->tree = $tree;
		
		$this->testOBJ = $testOBJ;
		
		$this->questionSetConfig = new ilTestRandomQuestionSetConfig(
			$this->tree, $this->db, $this->testOBJ
		);

		$this->sourcePoolDefinitionFactory = new ilTestRandomQuestionSetSourcePoolDefinitionFactory(
			$this->db, $this->testOBJ
		);

		$this->sourcePoolDefinitionList = new ilTestRandomQuestionSetSourcePoolDefinitionList(
			$this->db, $this->testOBJ, $this->sourcePoolDefinitionFactory
		);
	}
	
	/**
	 * Command Execution
	 */
	public function executeCommand()
	{
		// allow only write access
		
		if (!$this->access->checkAccess("write", "", $this->testOBJ->getRefId())) 
		{
			ilUtil::sendInfo($this->lng->txt("cannot_edit_test"), true);
			$this->ctrl->redirectByClass('ilObjTestGUI', "infoScreen");
		}
		
		// manage sub tabs and tab activation
		
		$this->handleTabs();
		
		// process command
		
		$nextClass = $this->ctrl->getNextClass();
		
		switch($nextClass)
		{
			case 'ilTestRandomQuestionSetPoolDefinitionFormGUI':
				
				$formGUI = new ilTestRandomQuestionSetPoolDefinitionFormGUI(
						$this->ctrl, $this->lng, $this->testOBJ, $this, $this->questionSetConfig
				);
				
				$this->ctrl->forwardCommand($formGUI);
				
				break;
				
			default:
				
				$cmd = $this->ctrl->getCmd(self::CMD_SHOW_GENERAL_CONFIG_FORM).'Cmd';
				
				$this->$cmd();
		}
	}
	
	private function handleTabs()
	{
		$this->tabs->activateTab('assQuestions');
		
		$this->tabs->addSubTab(
				'tstRandQuestSetGeneralConfig',
				$this->lng->txt('tst_rnd_quest_cfg_tab_general'),
				$this->ctrl->getLinkTarget($this, self::CMD_SHOW_GENERAL_CONFIG_FORM)
		);
		
		$this->tabs->addSubTab(
				'tstRandQuestSetPoolConfig',
				$this->lng->txt('tst_rnd_quest_cfg_tab_pool'),
				$this->ctrl->getLinkTarget($this, self::CMD_SHOW_SRC_POOL_DEF_LIST)
		);
		
		switch( $this->ctrl->getCmd(self::CMD_SHOW_GENERAL_CONFIG_FORM) )
		{
			case self::CMD_SHOW_GENERAL_CONFIG_FORM:
			case self::CMD_SAVE_GENERAL_CONFIG_FORM:
				
				$this->tabs->activateSubTab('tstRandQuestSetGeneralConfig');
				break;

			case self::CMD_SHOW_SRC_POOL_DEF_LIST:
			case self::CMD_SAVE_SRC_POOL_DEF_LIST:
			case self::CMD_SHOW_CREATE_SRC_POOL_DEF_FORM:
			case self::CMD_SAVE_CREATE_SRC_POOL_DEF_FORM:
			case self::CMD_SHOW_EDIT_SRC_POOL_DEF_FORM:
			case self::CMD_SAVE_EDIT_SRC_POOL_DEF_FORM:

				$this->tabs->activateSubTab('tstRandQuestSetPoolConfig');
				break;
		}
	}

	private function showGeneralConfigFormCmd(ilTestRandomQuestionSetGeneralConfigFormGUI $form = null)
	{
		if($form === null)
		{
			$this->questionSetConfig->loadFromDb();
			$form = $this->buildGeneralConfigFormGUI();
		}
		
		$this->tpl->setContent( $this->ctrl->getHTML($form) );
	}
	
	private function saveGeneralConfigFormCmd()
	{
		$this->questionSetConfig->loadFromDb();
		$form = $this->buildGeneralConfigFormGUI();

		if( $this->testOBJ->participantDataExist() )
		{
			ilUtil::sendFailure($this->lng->txt("tst_msg_cannot_modify_random_question_set_conf_due_to_part"), true);
			return $this->showGeneralConfigFormCmd($form);
		}
		
		$errors = !$form->checkInput(); // ALWAYS CALL BEFORE setValuesByPost()
		$form->setValuesByPost(); // NEVER CALL THIS BEFORE checkInput()

		if($errors)
		{
			return $this->showGeneralConfigFormCmd($form);
		}
		
		$form->save();

		$this->testOBJ->saveCompleteStatus( $this->questionSetConfig );

		ilUtil::sendSuccess($this->lng->txt("tst_msg_random_question_set_config_modified"), true);
		$this->ctrl->redirect($this, self::CMD_SHOW_GENERAL_CONFIG_FORM);
	}

	private function buildGeneralConfigFormGUI()
	{
		require_once 'Modules/Test/classes/forms/class.ilTestRandomQuestionSetGeneralConfigFormGUI.php';

		$form = new ilTestRandomQuestionSetGeneralConfigFormGUI(
			$this->ctrl, $this->lng, $this->testOBJ, $this, $this->questionSetConfig
		);

		$form->build();

		return $form;
	}

	private function showSourcePoolDefinitionListCmd()
	{
		$toolbar = $this->buildSourcePoolDefinitionListToolbarGUI();
		$table = $this->buildSourcePoolDefinitionListTableGUI();

		$this->sourcePoolDefinitionList->loadDefinitions();
		$table->init( $this->sourcePoolDefinitionList );

		$this->tpl->setContent(
				$this->ctrl->getHTML($toolbar) . $this->ctrl->getHTML($table)
		);
	}

	private function buildSourcePoolDefinitionListToolbarGUI()
	{
		require_once 'Modules/Test/classes/toolbars/class.ilTestRandomQuestionSetSourcePoolDefinitionListToolbarGUI.php';

		$toolbar = new ilTestRandomQuestionSetSourcePoolDefinitionListToolbarGUI(
			$this->ctrl, $this->lng, $this->testOBJ, $this, $this->questionSetConfig
		);

		$toolbar->build();

		return $toolbar;
	}

	private function buildSourcePoolDefinitionListTableGUI()
	{
		require_once 'Modules/Test/classes/tables/class.ilTestRandomQuestionSetSourcePoolDefinitionListTableGUI.php';

		$table = new ilTestRandomQuestionSetSourcePoolDefinitionListTableGUI(
			$this->ctrl, $this->lng, $this, self::CMD_SHOW_SRC_POOL_DEF_LIST
		);

		$table->build();

		return $table;
	}

	private function saveSourcePoolDefinitionListCmd()
	{
		
	}

	private function showCreateSourcePoolDefinitionFormCmd(ilTestRandomQuestionSetPoolDefinitionFormGUI $form = null)
	{
		$this->questionSetConfig->loadFromDb();

		$poolId = $this->fetchQuestionPoolIdParameter();

		$sourcePoolDefinition = $this->getSourcePoolDefinitionByAvailableQuestionPoolId($poolId);

		if($form === null)
		{
			$form = $this->buildSourcePoolDefinitionFormGUI(
				$sourcePoolDefinition, $this->getAvailableTaxonomyIds($sourcePoolDefinition->getPoolId()),
				self::CMD_SAVE_CREATE_SRC_POOL_DEF_FORM
			);
		}

		$this->tpl->setContent( $this->ctrl->getHTML($form) );
	}

	private function saveCreateSourcePoolDefinitionFormCmd()
	{
		$this->questionSetConfig->loadFromDb();

		$poolId = $this->fetchQuestionPoolIdParameter();
		$sourcePoolDefinition = $this->getSourcePoolDefinitionByAvailableQuestionPoolId($poolId);

		$availableTaxonomyIds = $this->getAvailableTaxonomyIds( $sourcePoolDefinition->getPoolId() );

		$form = $this->buildSourcePoolDefinitionFormGUI(
			$sourcePoolDefinition, $availableTaxonomyIds,
			self::CMD_SAVE_CREATE_SRC_POOL_DEF_FORM
		);

		if( $this->testOBJ->participantDataExist() )
		{
			ilUtil::sendFailure($this->lng->txt("tst_msg_cannot_modify_random_question_set_conf_due_to_part"));
			return $this->showSourcePoolDefinitionListCmd($form);
		}

		$errors = !$form->checkInput(); // ALWAYS CALL BEFORE setValuesByPost()
		$form->setValuesByPost(); // NEVER CALL THIS BEFORE checkInput()

		if($errors)
		{
			return $this->showSourcePoolDefinitionListCmd($form);
		}

		$form->applySubmit( $sourcePoolDefinition, $availableTaxonomyIds );

		$sourcePoolDefinition->saveToDb();

		$this->questionSetConfig->fetchRandomQuestionSet();

		$this->testOBJ->saveCompleteStatus( $this->questionSetConfig );

		ilUtil::sendSuccess($this->lng->txt("tst_msg_random_question_set_config_modified"), true);
		$this->ctrl->redirect($this, self::CMD_SHOW_SRC_POOL_DEF_LIST);
	}

	private function showEditSourcePoolDefinitionFormCmd(ilTestRandomQuestionSetPoolDefinitionFormGUI $form = null)
	{

	}

	private function saveEditSourcePoolDefinitionFormCmd(ilTestRandomQuestionSetPoolDefinitionFormGUI $form = null)
	{

	}

	private function buildSourcePoolDefinitionFormGUI(ilTestRandomQuestionSetSourcePoolDefinition $sourcePoolDefinition, $availableTaxonomyIds, $saveCommand)
	{
		require_once 'Modules/Test/classes/forms/class.ilTestRandomQuestionSetPoolDefinitionFormGUI.php';

		$form = new ilTestRandomQuestionSetPoolDefinitionFormGUI(
			$this->ctrl, $this->lng, $this->testOBJ, $this, $this->questionSetConfig
		);

		$form->setSaveCommand($saveCommand);

		$form->build( $sourcePoolDefinition, $availableTaxonomyIds );

		return $form;
	}

	private function fetchQuestionPoolIdParameter()
	{
		if( isset($_POST['quest_pool_id']) && (int)$_POST['quest_pool_id'] )
		{
			return (int)$_POST['quest_pool_id'];
		}

		require_once 'Modules/Test/exceptions/class.ilTestMissingQuestionPoolIdParameterException.php';
		throw new ilTestMissingQuestionPoolIdParameterException();
	}

	private function fetchSourcePoolDefinitionIdParameter()
	{
		if( isset($_POST['quest_pool_id']) && (int)$_POST['quest_pool_id'] )
		{
			return (int)$_POST['quest_pool_id'];
		}

		if( isset($_GET['src_pool_def_id']) && (int)$_GET['src_pool_def_id'] )
		{
			return (int)$_GET['src_pool_def_id'];
		}

		require_once 'Modules/Test/exceptions/class.ilTestMissingSourcePoolDefinitionIdParameterException.php';
		throw new ilTestMissingSourcePoolDefinitionIdParameterException();
	}

	private function getAvailableTaxonomyIds($objId)
	{
		require_once 'Services/Taxonomy/classes/class.ilObjTaxonomy.php';
		return ilObjTaxonomy::getUsageOfObject($objId);
	}

	private function getSourcePoolDefinitionByAvailableQuestionPoolId($poolId)
	{
		$availablePools = $this->testOBJ->getAvailableQuestionpools(
			true, $this->questionSetConfig->arePoolsWithHomogeneousScoredQuestionsRequired(), false, true, true
		);

		if( isset($availablePools[$poolId]) )
		{
			$originalPoolData = $availablePools[$poolId];

			$originalPoolData['qpl_path'] = $this->questionSetConfig->getQuestionPoolPathString($poolId);

			return $this->sourcePoolDefinitionFactory->getSourcePoolDefinitionByOriginalPoolData($originalPoolData);
		}

		require_once 'Modules/Test/exceptions/class.ilTestQuestionPoolNotAvailableAsSourcePoolException.php';
		throw new ilTestQuestionPoolNotAvailableAsSourcePoolException();
	}
}
