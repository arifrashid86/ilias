<?php
/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once 'Modules/Test/classes/class.ilTestSession.php';

/**
 * Test session handler for tests with mode dynamic question set
 *
 * @author		Björn Heyser <bheyser@databay.de>
 * @version		$Id$
 * 
 * @package		Modules/Test
 */
class ilTestSessionDynamicQuestionSet extends ilTestSession
{
	private $taxonomyFilterSelection = array();

	public function getTaxonomyFilterSelection()
	{
		return $this->taxonomyFilterSelection;
	}

	public function setTaxonomyFilterSelection($taxonomyFilterSelection)
	{
		$this->taxonomyFilterSelection = $taxonomyFilterSelection;
	}
	
	public function loadFromDb($active_id)
	{
		global $ilDB;
		$result = $ilDB->queryF("SELECT * FROM tst_active WHERE active_id = %s", 
			array('integer'),
			array($active_id)
		);
		if ($result->numRows())
		{
			$row = $ilDB->fetchAssoc($result);
			$this->active_id = $row["active_id"];
			$this->user_id = $row["user_fi"];
			$this->anonymous_id = $row["anonymous_id"];
			$this->test_id = $row["test_fi"];
			$this->lastsequence = $row["lastindex"];
			$this->pass = $row["tries"];
			$this->submitted = ($row["submitted"]) ? TRUE : FALSE;
			$this->submittedTimestamp = $row["submittimestamp"];
			$this->tstamp = $row["tstamp"];
			$this->setTaxonomyFilterSelection(unserialize($row['taxfilter']));
		}
	}
	
	function loadTestSession($test_id, $user_id = "", $anonymous_id = "")
	{
		global $ilDB;
		global $ilUser;

		if (!$user_id)
		{
			$user_id = $ilUser->getId();
		}
		if (($_SESSION["AccountId"] == ANONYMOUS_USER_ID) && (strlen($_SESSION["tst_access_code"][$test_id])))
		{
			$result = $ilDB->queryF("SELECT * FROM tst_active WHERE user_fi = %s AND test_fi = %s AND anonymous_id = %s",
				array('integer','integer','text'),
				array($user_id, $test_id, $_SESSION["tst_access_code"][$test_id])
			);
		}
		else if (strlen($anonymous_id))
		{
			$result = $ilDB->queryF("SELECT * FROM tst_active WHERE user_fi = %s AND test_fi = %s AND anonymous_id = %s",
				array('integer','integer','text'),
				array($user_id, $test_id, $anonymous_id)
			);
		}
		else
		{
			if ($_SESSION["AccountId"] == ANONYMOUS_USER_ID)
			{
				return NULL;
			}
			$result = $ilDB->queryF("SELECT * FROM tst_active WHERE user_fi = %s AND test_fi = %s",
				array('integer','integer'),
				array($user_id, $test_id)
			);
		}
		if ($result->numRows())
		{
			$row = $ilDB->fetchAssoc($result);
			$this->active_id = $row["active_id"];
			$this->user_id = $row["user_fi"];
			$this->anonymous_id = $row["anonymous_id"];
			$this->test_id = $row["test_fi"];
			$this->lastsequence = $row["lastindex"];
			$this->pass = $row["tries"];
			$this->submitted = ($row["submitted"]) ? TRUE : FALSE;
			$this->submittedTimestamp = $row["submittimestamp"];
			$this->tstamp = $row["tstamp"];
			$this->setTaxonomyFilterSelection(strlen($row["taxfilter"]) ? unserialize($row["taxfilter"]) : array());
		}
	}
	
	function saveToDb()
	{
		global $ilDB, $ilLog;
		
		$submitted = ($this->isSubmitted()) ? 1 : 0;
		if ($this->active_id > 0)
		{
			$affectedRows = $ilDB->update('tst_active', 
				array(
					'lastindex' => array('integer', $this->getLastSequence()),
					'tries' => array('integer', $this->getPass()),
					'submitted' => array('integer', $submitted),
					'submittimestamp' => array('timestamp', (strlen($this->getSubmittedTimestamp())) ? $this->getSubmittedTimestamp() : NULL),
					'tstamp' => array('integer', time()-10),
					'taxfilter' => array('text', serialize($this->getTaxonomyFilterSelection()))
				),
				array(
					'active_id' => array('integer', $this->getActiveId())
				)
			);

			// update learning progress
			include_once("./Modules/Test/classes/class.ilObjTestAccess.php");
			include_once("./Services/Tracking/classes/class.ilLPStatusWrapper.php");
			ilLPStatusWrapper::_updateStatus(ilObjTestAccess::_lookupObjIdForTestId($this->getTestId()),
				ilObjTestAccess::_getParticipantId($this->getActiveId()));
		}
		else
		{
			if (!$this->activeIDExists($this->getUserId(), $this->getTestId()))
			{
				$anonymous_id = ($this->getAnonymousId()) ? $this->getAnonymousId() : NULL;

				$next_id = $ilDB->nextId('tst_active');
				$affectedRows = $ilDB->insert('tst_active',
					array(
						'active_id' => array('integer', $next_id),
						'user_fi' => array('integer', $this->getUserId()),
						'anonymous_id' => array('text', $anonymous_id),
						'test_fi' => array('integer', $this->getTestId()),
						'lastindex' => array('integer', $this->getLastSequence()),
						'tries' => array('integer', $this->getPass()),
						'submitted' => array('integer', $submitted),
						'submittimestamp' => array('timestamp', (strlen($this->getSubmittedTimestamp())) ? $this->getSubmittedTimestamp() : NULL),
						'tstamp' => array('integer', time()-10),
						'taxfilter' => array('text', serialize($this->getTaxonomyFilterSelection()))
					)
				);
				$this->active_id = $next_id;

				// update learning progress
				include_once("./Modules/Test/classes/class.ilObjTestAccess.php");
				include_once("./Services/Tracking/classes/class.ilLPStatusWrapper.php");
				ilLPStatusWrapper::_updateStatus(ilObjTestAccess::_lookupObjIdForTestId($this->getTestId()),
					$this->getUserId());
			}
		}
		
		include_once("./Services/Tracking/classes/class.ilLearningProgress.php");
		ilLearningProgress::_tracProgress($this->getUserId(),
										  ilObjTestAccess::_lookupObjIdForTestId($this->getTestId()),
										  $this->getRefId(),
										  'tst');
	}
	
	public function getCurrentQuestionId()
	{
		return $this->getLastSequence();
	}

	public function setCurrentQuestionId($currentQuestionId)
	{
		$this->setLastSequence((int)$currentQuestionId);
	}

}

