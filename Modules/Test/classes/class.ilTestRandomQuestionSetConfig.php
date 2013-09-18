<?php
/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once 'Modules/Test/classes/class.ilTestQuestionSetConfig.php';

/**
 * class that manages/holds the data for a question set configuration for continues tests
 *
 * @author		Björn Heyser <bheyser@databay.de>
 * @version		$Id$
 *
 * @package		Modules/Test
 */
class ilTestRandomQuestionSetConfig extends ilTestQuestionSetConfig
{
	const QUESTION_AMOUNT_CONFIG_MODE_PER_TEST = 'TEST';
	const QUESTION_AMOUNT_CONFIG_MODE_PER_POOL = 'POOL';
	
	/**
	 * @var boolean
	 */
	private $requirePoolsWithHomogeneousScoredQuestions = null;
	
	/**
	 * @var string
	 */
	private $questionAmountConfigurationMode = null;
	
	/**
	 * @var integer
	 */
	private $questionAmountPerTest = null;
	
	/**
	 * @var integer
	 */
	private $lastQuestionSyncTimestamp = null;

	/**
	 * @param ilTree $tree
	 * @param ilDB $db
	 * @param ilObjTest $testOBJ
	 */
	public function __construct(ilTree $tree, ilDB $db, ilObjTest $testOBJ)
	{
		parent::__construct($tree, $db, $testOBJ);
	}

	/**
	 * @param boolean $requirePoolsWithHomogeneousScoredQuestions
	 */
	public function setPoolsWithHomogeneousScoredQuestionsRequired($requirePoolsWithHomogeneousScoredQuestions)
	{
		$this->requirePoolsWithHomogeneousScoredQuestions = $requirePoolsWithHomogeneousScoredQuestions;
	}
	
	/**
	 * @return boolean
	 */
	public function arePoolsWithHomogeneousScoredQuestionsRequired()
	{
		return $this->requirePoolsWithHomogeneousScoredQuestions;
	}
	
	/**
	 * @param string $questionAmountConfigurationMode
	 */
	public function setQuestionAmountConfigurationMode($questionAmountConfigurationMode)
	{
		$this->questionAmountConfigurationMode = $questionAmountConfigurationMode;
	}
	
	/**
	 * @return string
	 */
	public function getQuestionAmountConfigurationMode()
	{
		return $this->questionAmountConfigurationMode;
	}
	
	/**
	 * @return boolean
	 */
	public function isQuestionAmountConfigurationModePerPool()
	{
		return $this->getQuestionAmountConfigurationMode() == self::QUESTION_AMOUNT_CONFIG_MODE_PER_POOL;
	}
	
	/**
	 * @param integer $questionAmountPerTest
	 */
	public function setQuestionAmountPerTest($questionAmountPerTest)
	{
		$this->questionAmountPerTest = $questionAmountPerTest;
	}
	
	/**
	 * @return integer
	 */
	public function getQuestionAmountPerTest()
	{
		return $this->questionAmountPerTest;
	}
	
	/**
	 * @param integer $lastQuestionSyncTimestamp
	 */
	public function setLastQuestionSyncTimestamp($lastQuestionSyncTimestamp)
	{
		$this->lastQuestionSyncTimestamp = $lastQuestionSyncTimestamp;
	}
	
	/**
	 * @return integer
	 */
	public function getLastQuestionSyncTimestamp()
	{
		return $this->lastQuestionSyncTimestamp;
	}
	
	// -----------------------------------------------------------------------------------------------------------------
	
	/**
	 * initialises the current object instance with values
	 * from matching properties within the passed array
	 * 
	 * @param array $dataArray
	 */
	public function initFromArray($dataArray)
	{
		foreach($dataArray as $field => $value)
		{
			switch($field)
			{
				case 'req_pools_homo_scored':		$this->setPoolsWithHomogeneousScoredQuestionsRequired($value);	break;
				case 'quest_amount_cfg_mode':		$this->setQuestionAmountConfigurationMode($value);				break;
				case 'quest_amount_per_test':		$this->setQuestionAmountPerTest($value);						break;
				case 'quest_sync_timestamp':		$this->setLastQuestionSyncTimestamp($value);					break;
			}
		}
	}
	
	/**
	 * loads the question set config for current test from the database
	 * 
	 * @return boolean
	 */
	public function loadFromDb()
	{
		$res = $this->db->queryF(
				"SELECT * FROM tst_rnd_quest_set_cfg WHERE test_fi = %s",
				array('integer'), array($this->testOBJ->getTestId())
		);
		
		while( $row = $this->db->fetchAssoc($res) )
		{
			$this->initFromArray($row);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * saves the question set config for current test to the database
	 * 
	 * @return boolean
	 */
	public function saveToDb()
	{
		if( $this->dbRecordExists($this->testOBJ->getTestId()) )
		{
			$this->updateDbRecord($this->testOBJ->getTestId());
		}
		else
		{
			$this->insertDbRecord($this->testOBJ->getTestId());
		}
	}
	
	/**
	 * deletes the question set config for current test from the database
	 */
	public function deleteFromDb()
	{
		$this->db->manipulateF(
				"DELETE FROM tst_rnd_quest_set_cfg WHERE test_fi = %s",
				array('integer'), array($this->testOBJ->getTestId())
		);
	}
	
	/**
	 * checks wether a question set config for current test exists in the database
	 * 
	 * @return boolean
	 */
	private function dbRecordExists()
	{
		$res = $this->db->queryF(
			"SELECT COUNT(*) cnt FROM tst_rnd_quest_set_cfg WHERE test_fi = %s",
			array('integer'), array($this->testOBJ->getTestId())
		);
		
		$row = $this->db->fetchAssoc($res);
		
		return (bool)$row['cnt'];
	}
	
	/**
	 * updates the record in the database that corresponds
	 * to the question set config for the current test
	 */
	private function updateDbRecord()
	{
		$this->db->update('tst_rnd_quest_set_cfg',
			array(
				'req_pools_homo_scored' => array('integer', $this->arePoolsWithHomogeneousScoredQuestionsRequired()),
				'quest_amount_cfg_mode' => array('text', $this->getQuestionAmountConfigurationMode()),
				'quest_amount_per_test' => array('integer', $this->getQuestionAmountPerTest()),
				'quest_sync_timestamp' => array('integer', $this->getLastQuestionSyncTimestamp())
			),
			array(
				'test_fi' => array('integer', $this->testOBJ->getTestId())
			)
		);
	}
	
	/**
	 * inserts a new record for the question set config
	 * for the current test into the database
	 */
	private function insertDbRecord()
	{
		$this->db->insert('tst_dyn_quest_set_cfg', array(
			'test_fi' => array('integer', $this->testOBJ->getTestId()),
			'req_pools_homo_scored' => array('integer', $this->arePoolsWithHomogeneousScoredQuestionsRequired()),
			'quest_amount_cfg_mode' => array('text', $this->getQuestionAmountConfigurationMode()),
			'quest_amount_per_test' => array('integer', $this->getQuestionAmountPerTest()),
			'quest_sync_timestamp' => array('integer', $this->getLastQuestionSyncTimestamp())
		));
	}
	
	// -----------------------------------------------------------------------------------------------------------------
	
	/**
	 * @return ilTestRandomQuestionSetSourcePoolDefinitionList
	 */
	public function getSourcePoolDefinitionList()
	{
		return $this->sourcePoolDefinitionList;
	}
	
	// -----------------------------------------------------------------------------------------------------------------

	public function isQuestionSetConfigured()
	{
		return true;
	}
	
	/**
	 * checks wether question set config related data exists or not
	 */
	public function doesQuestionSetRelatedDataExist()
	{
		return false;
	}
	
	/**
	 * removes all question set config related data
	 */
	public function removeQuestionSetRelatedData()
	{
		
	}
	
	// -----------------------------------------------------------------------------------------------------------------

	public function fetchRandomQuestionSet()
	{

	}
}
