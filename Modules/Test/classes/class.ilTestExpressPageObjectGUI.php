<?php

include_once("./Services/COPage/classes/class.ilPageObjectGUI.php");
include_once 'Modules/Test/classes/class.ilTestExpressPage.php';

/**
 *
 *
 * @ilCtrl_Calls ilTestExpressPageObjectGUI: assMultipleChoiceGUI, assClozeTestGUI, assMatchingQuestionGUI
 * @ilCtrl_Calls ilTestExpressPageObjectGUI: assOrderingQuestionGUI, assImagemapQuestionGUI, assJavaAppletGUI
 * @ilCtrl_Calls ilTestExpressPageObjectGUI: assNumericGUI
 * @ilCtrl_Calls ilTestExpressPageObjectGUI: assTextSubsetGUI
 * @ilCtrl_Calls ilTestExpressPageObjectGUI: assSingleChoiceGUI
 * @ilCtrl_Calls ilTestExpressPageObjectGUI: assTextQuestionGUI
 * @ilCtrl_Calls ilTestExpressPageObjectGUI: ilPageEditorGUI, ilEditClipboardGUI, ilMediaPoolTargetSelector
 * @ilCtrl_Calls ilTestExpressPageObjectGUI: ilRatingGUI, ilPublicUserProfileGUI, ilPageObjectGUI, ilNoteGUI
 * @ilCtrl_Calls ilTestExpressPageObjectGUI: ilObjQuestionPoolGUI
 * @ilCtrl_IsCalledBy ilTestExpressPageObjectGUI: assMultipleChoiceGUI, assClozeTestGUI, assMatchingQuestionGUI
 * @ilCtrl_IsCalledBy ilTestExpressPageObjectGUI: assOrderingQuestionGUI, assImagemapQuestionGUI, assJavaAppletGUI
 * @ilCtrl_IsCalledBy ilTestExpressPageObjectGUI: assNumericGUI
 * @ilCtrl_IsCalledBy ilTestExpressPageObjectGUI: assTextSubsetGUI
 * @ilCtrl_IsCalledBy ilTestExpressPageObjectGUI: assSingleChoiceGUI
 * @ilCtrl_IsCalledBy ilTestExpressPageObjectGUI: assTextQuestionGUI
 */
class ilTestExpressPageObjectGUI extends ilPageObjectGUI {

    public function nextQuestion() {
        $obj = new ilObjTest($_REQUEST['ref_id']);
        $questions = array_keys($obj->getQuestionTitlesAndIndexes());

        $pos = array_search($_REQUEST['q_id'], $questions);

        if ($pos == count($questions) - 1) {
            ilUtil::sendInfo('test_express_end_reached_moved_to_first', true);
            $next = $questions[0];
        } else if ($pos !== false) {
            $next = $questions[$pos + 1];
        } else {
            $next = $questions[0];
        }

        $this->ctrl->setParameter($this, 'q_id', $next);
        $link = $this->ctrl->getLinkTarget($this, 'edit', '', '', false);

        ilUtil::redirect($link);
    }

    public function prevQuestion() {
        $obj = new ilObjTest($_REQUEST['ref_id']);
        $questions = array_keys($obj->getQuestionTitlesAndIndexes());

        $pos = array_search($_REQUEST['q_id'], $questions);

        if ($pos == 0) {
            ilUtil::sendInfo('test_express_start_reached_moved_to_last', true);
            $next = $questions[count($questions) - 1];
        } else if ($pos !== false) {
            $next = $questions[$pos - 1];
        } else {
            $next = $questions[0];
        }

        $this->ctrl->setParameter($this, 'q_id', $next);
        $link = $this->ctrl->getLinkTarget($this, 'edit', '', '', false);

        ilUtil::redirect($link);
    }

    function &executeCommand() {
        global $ilCtrl, $ilTabs, $ilUser;

        $next_class = $this->ctrl->getNextClass($this);
        $cmd = $this->ctrl->getCmd();

        switch ($next_class) {
            case 'ilobjquestionpoolgui':
                $nodeParts = explode(':', $_GET['cmdNode']);

                $params = array(
                    'ref_id' => $_GET['ref_id'],
                    'calling_test' => $_GET['ref_id'],
                    'q_id' => $_GET['q_id'],
                    'cmd' => $_GET['cmd'],
                    'cmdClass' => $_GET['cmdClass'],
                    'cmdNode' => $nodeParts[count($nodeParts) - 2] . ':' . $nodeParts[count($nodeParts) - 1],
                    'baseClass' => 'ilObjQuestionPoolGUI',
                    'test_express_mode' => '1'
                );
                ilUtil::redirect('ilias.php?' . http_build_query($params, null, '&'));
                break;

            case "ilpageeditorgui":
                if (!$this->getEnableEditing()) {
                    ilUtil::sendFailure($lng->txt("permission_denied"), true);
                    $ilCtrl->redirect($this, "preview");
                }
                $page_editor = & new ilPageEditorGUI($this->getPageObject(), $this);
                $page_editor->setLocator($this->locator);
                $page_editor->setHeader($this->getHeader());
                $page_editor->setPageBackTitle($this->page_back_title);
                $page_editor->setEnableInternalLinks($this->getEnabledInternalLinks());
                $page_editor->setEnableKeywords($this->getEnableKeywords());
                $page_editor->setIntLinkHelpDefault($this->int_link_def_type,
                        $this->int_link_def_id);
                $page_editor->setIntLinkReturn($this->int_link_return);


                $this->ctrl->saveParameterByClass('ilpageeditorgui', 'q_mode');

                $ret = & $this->ctrl->forwardCommand($page_editor);
                break;

            case '':
            case 'iltestexpresspageobjectgui':
                include_once 'Modules/TestQuestionPool/classes/class.assQuestionGUI.php';

		if ($cmd == 'view')
                    $cmd = 'showPage';

                $q_gui = & assQuestionGUI::_getQuestionGUI('', $_REQUEST["q_id"]);
                if ($q_gui->object) {
                    $obj = ilObjectFactory::getInstanceByRefId($_REQUEST['ref_id']);
                    $q_gui->object->setObjId($obj->getId());
                }
                if (in_array($cmd, array('handleToolbarCommand', 'addQuestion'))) {
                    return $this->$cmd();
                }
                else if ($q_gui->object) {
                    $this->setOutputMode($this->test_object->evalTotalPersons() == 0 ? IL_PAGE_EDIT : IL_PAGE_PREVIEW);
                    if (in_array($cmd, array('view', 'showPage')) || $cmd == 'edit' && $this->test_object->evalTotalPersons()) {
                        return $this->showPage();
                    }
                    return parent::executeCommand();
                }
                break;

            default:
                $qtype = $_REQUEST['qtype'];
                $type = ilObjQuestionPool::getQuestionTypeByTypeId($qtype);
echo 123;
		if (!$_GET['q_id']) {
                    $q_gui = $this->addPageOfQuestions(preg_replace('/(.*?)gui/i', '$1', $_GET['sel_question_types']));
                    $q_gui->setQuestionTabs();

                    global $___test_express_mode;
                    $___test_express_mode = true;
                    $ret = $this->ctrl->forwardCommand($q_gui);
                    unset($___test_express_mode);

                    break;
                } else {

                }
                $this->ctrl->setReturn($this, "questions");
                include_once "./Modules/TestQuestionPool/classes/class.assQuestionGUI.php";
                $q_gui = & assQuestionGUI::_getQuestionGUI($type, $_GET["q_id"]);

                if ($q_gui->object) {
                    $obj = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
                    $q_gui->object->setObjId($obj->getId());
                }
                $this->ctrl->saveParameterByClass('ilpageeditorgui', 'q_id');
                $this->ctrl->saveParameterByClass('ilpageeditorgui', 'q_mode');

                $q_gui->setQuestionTabs();
                $ret = & $this->ctrl->forwardCommand($q_gui);
                break;
        }
    }

    public function addPageOfQuestions($type = '') {
        global $ilCtrl;
        if (!$type) {
            $qtype = $_REQUEST['qtype'];
            $pool = new ilObjQuestionPool();
            $type = ilObjQuestionPool::getQuestionTypeByTypeId($qtype);
        }
        $this->ctrl->setReturn($this, "questions");
        include_once "./Modules/TestQuestionPool/classes/class.assQuestionGUI.php";
        $q_gui = & assQuestionGUI::_getQuestionGUI($type);
        $obj = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
        $q_gui->object->setObjId($obj->getId());
        return $q_gui;
    }

    public function handleToolbarCommand() {
        include_once "./Modules/TestQuestionPool/classes/class.assQuestionGUI.php";
	if ($_REQUEST['qtype']) {
	    include_once 'Modules/TestQuestionPool/classes/class.ilObjQuestionPool.php';
	    $questionType = ilObjQuestionPool::getQuestionTypeByTypeId($_REQUEST['qtype']);
	}
	else if ($_REQUEST['sel_question_types']) {
	    $questionType = $_REQUEST['sel_question_types'];
	}
        $q_gui =& assQuestionGUI::_getQuestionGUI($questionType);
        $q_gui->object->setObjId($_GET['ref_id']);
        $q_gui->object->createNewQuestion();

	//$prev_qid = $_REQUEST['q_id'];

	$previousQuestionId = $_REQUEST['position'];
	//$this->test_object->moveQuestionAfterExisting($prev_qid, $previousQuestionId);
#var_dump($prev_qid, $previousQuestionId);exit;
	switch($_REQUEST['usage']) {
	    case 3: // existing pool
		global $ilCtrl;
		$ilCtrl->setParameterByClass('ilobjtestgui', 'sel_qpl', $_REQUEST['sel_qpl']);
		$ilCtrl->setParameterByClass('ilobjtestgui', 'sel_question_types', $questionType);
		$ilCtrl->setParameterByClass('ilobjtestgui', 'q_id', $q_gui->object->getId());
		$ilCtrl->setParameterByClass('ilobjtestgui', 'prev_qid', $previousQuestionId);
		$ilCtrl->setParameterByClass('ilobjtestgui', 'test_express_mode', 1);

		$link = $ilCtrl->getLinkTargetByClass('ilobjtestgui', 'executeCreateQuestion', false, false, false);
		ilUtil::redirect($link);
		break;
	    case 2: // new pool
		global $ilCtrl;
		$ilCtrl->setParameterByClass('ilobjtestgui', 'txt_qpl', $_REQUEST['txt_qpl']);
		$ilCtrl->setParameterByClass('ilobjtestgui', 'sel_question_types', $questionType);
		$ilCtrl->setParameterByClass('ilobjtestgui', 'q_id', $q_gui->object->getId());
		$ilCtrl->setParameterByClass('ilobjtestgui', 'prev_qid', $previousQuestionId);
		$ilCtrl->setParameterByClass('ilobjtestgui', 'test_express_mode', 1);

		$link = $ilCtrl->getLinkTargetByClass('ilobjtestgui', 'executeCreateQuestion', false, false, false);
		
		ilUtil::redirect($link);
		break;
	    case 1: // no pool
	    default:
		$this->redirectToQuestionEditPage($questionType, $q_gui->object->getId(), $previousQuestionId);
		break;
	}
    }

    public function addQuestion() {
	global $lng, $ilCtrl, $tpl;

	include_once "Services/Form/classes/class.ilPropertyFormGUI.php";

	$ilCtrl->setParameter($this, 'qtype', $_REQUEST['qtype']);

	$form = new ilPropertyFormGUI();
	$form->setFormAction($ilCtrl->getFormAction($this, "handleToolbarCommand"));
	$form->setTitle($lng->txt("test_add_new_question"));
	include_once 'Modules/TestQuestionPool/classes/class.ilObjQuestionPool.php';

	$pool = new ilObjQuestionPool();
	$questionTypes = $pool->getQuestionTypes();
	$options = array();

	// question type
	foreach($questionTypes as $label => $data) {
	    $options[$data['question_type_id']] = $label;
	}

	include_once("./Services/Form/classes/class.ilSelectInputGUI.php");
	$si = new ilSelectInputGUI($lng->txt("test_add_new_question"), "qtype");
	$si->setOptions($options);
	$form->addItem($si, true);

	// position
	$questions = $this->test_object->getQuestionTitlesAndIndexes();
	if ($questions) {
	    $si = new ilSelectInputGUI($lng->txt("position"), "position");
	    $options = array('0' => $lng->txt('first'));
	    foreach($questions as $key => $title) {
		$options[$key] = $lng->txt('behind') . ' '. $title;
	    }
	    $si->setOptions($options);
	    $si->setValue($_REQUEST['q_id']);
	    $form->addItem($si, true);
	}

	if ($this->test_object->getPoolUsage()) {
	    // use pool
	    $usage = new ilRadioGroupInputGUI($this->lng->txt("assessment_pool_selection"), "usage");
	    $usage->setRequired(true);
	    $no_pool = new ilRadioOption($this->lng->txt("assessment_no_pool"), 1);
	    $usage->addOption($no_pool);
	    $existing_pool = new ilRadioOption($this->lng->txt("assessment_existing_pool"), 3);
	    $usage->addOption($existing_pool);
	    $new_pool = new ilRadioOption($this->lng->txt("assessment_new_pool"), 2);
	    $usage->addOption($new_pool);
	    $form->addItem($usage);

            $usage->setValue(1);

	    $questionpools = ilObjQuestionPool::_getAvailableQuestionpools(FALSE, FALSE, TRUE, FALSE, FALSE, "write");
	    $pools_data = array();
	    foreach($questionpools as $key => $p) {
		$pools_data[$key] = $p['title'];
	    }
	    $pools = new ilSelectInputGUI($this->lng->txt("select_questionpool"), "sel_qpl");
	    $pools->setOptions($pools_data);
	    $existing_pool->addSubItem($pools);


	    $name = new ilTextInputGUI($this->lng->txt("cat_create_qpl"), "txt_qpl");
	    $name->setSize(50);
	    $name->setMaxLength(50);
	    $new_pool->addSubItem($name);
    	}
	

	$form->addCommandButton("handleToolbarCommand", $lng->txt("submit"));
	$form->addCommandButton("questions", $lng->txt("cancel"));

	return $tpl->setContent($form->getHTML());

    }

    public function questions() {
	global $ilCtrl;
	$ilCtrl->redirectByClass('ilobjtestgui', 'showPage');
    }

    private function redirectToQuestionEditPage($questionType, $qid, $prev_qid) {
        include_once 'Modules/TestQuestionPool/classes/class.ilObjQuestionPool.php';

        $ref_id = $_GET['ref_id'];
        $sel_question_types = $questionType;
        $cmd = 'editQuestion';
        $cmdClass = strtolower($questionType);
        $cmdNode = $_GET['cmdNode'];
        $baseClass = 'ilObjTestGUI';

        $node = ilTestExpressPage::getNodeId(strtolower($questionType) . 'gui');

        $cmdNodes = explode(':', $_GET['cmdNode']);
        $firstNode = $cmdNodes[0];

        $linkParams = array(
            'ref_id' => $_GET['ref_id'],
            'sel_question_types' => $questionType,
            'cmd' => 'editQuestion',
            'cmdClass' => strtolower($questionType) . 'gui',
            'cmdNode' => $firstNode . ':' . $node,
            'baseClass' => 'ilObjTestGUI',
            'test_ref_id' => $_GET['ref_id'],
            'express_mode' => 'true',
            'q_id' => $qid,
            'prev_qid' => $prev_qid
        );

        ilUtil::redirect('ilias.php?' . http_build_query($linkParams, 'null', '&'));
    }

    private function redirectToQuestionPoolSelectionPage($questionType, $qid, $prev_qid) {
        $this->ctrl->setParameterByClass('ilObjTestGUI', 'ref_id', $_REQUEST['ref_id']);
        $this->ctrl->setParameterByClass('ilObjTestGUI', 'q_id', $qid);
        $this->ctrl->setParameterByClass('ilObjTestGUI', 'sel_question_types',  $questionType);
        $this->ctrl->setParameterByClass('ilObjTestGUI', 'prev_qid',  $prev_qid);
        $redir = $this->ctrl->getLinkTargetByClass('ilObjTestGUI', 'createQuestion', '', false, false);

        ilUtil::redirect($redir);
    }

}

?>
