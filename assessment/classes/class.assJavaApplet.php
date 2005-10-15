<?php
/*
   +----------------------------------------------------------------------------+
   | ILIAS open source                                                          |
   +----------------------------------------------------------------------------+
   | Copyright (c) 1998-2001 ILIAS open source, University of Cologne           |
   |                                                                            |
   | This program is free software; you can redistribute it and/or              |
   | modify it under the terms of the GNU General Public License                |
   | as published by the Free Software Foundation; either version 2             |
   | of the License, or (at your option) any later version.                     |
   |                                                                            |
   | This program is distributed in the hope that it will be useful,            |
   | but WITHOUT ANY WARRANTY; without even the implied warranty of             |
   | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the              |
   | GNU General Public License for more details.                               |
   |                                                                            |
   | You should have received a copy of the GNU General Public License          |
   | along with this program; if not, write to the Free Software                |
   | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA. |
   +----------------------------------------------------------------------------+
*/
require_once "./assessment/classes/class.assQuestion.php";

define ("JAVAAPPLET_QUESTION_IDENTIFIER", "JAVA APPLET QUESTION");

/**
* Class for Java Applet Questions
*
* ASS_JavaApplet is a class for Java Applet Questions.
*
* @author		Helmut Schottmüller <hschottm@tzi.de>
* @version	$Id$
* @module   class.assJavaApplet.php
* @modulegroup   Assessment
*/
class ASS_JavaApplet extends ASS_Question
{
	/**
	* Question string
	*
	* The question string of the multiple choice question
	*
	* @var string
	*/
	var $question;

	/**
	* Java applet file name
	*
	* The file name of the java applet
	*
	* @var string
	*/
	var $javaapplet_filename;

	/**
	* Java Applet code parameter
	*
	* Java Applet code parameter
	*
	* @var string
	*/
	var $java_code;

	/**
	* Java Applet width parameter
	*
	* Java Applet width parameter
	*
	* @var integer
	*/
	var $java_width;

	/**
	* Java Applet height parameter
	*
	* Java Applet height parameter
	*
	* @var integer
	*/
	var $java_height;

	/**
	* Additional java applet parameters
	*
	* Additional java applet parameters
	*
	* @var array
	*/
	var $parameters;

	/**
	* ASS_JavaApplet constructor
	*
	* The constructor takes possible arguments an creates an instance of the ASS_JavaApplet object.
	*
	* @param string $title A title string to describe the question
	* @param string $comment A comment string to describe the question
	* @param string $author A string containing the name of the questions author
	* @param integer $owner A numerical ID to identify the owner/creator
	* @param string $question The question string of the multiple choice question
	* @param integer $response Indicates the response type of the multiple choice question
	* @param integer $output_type The output order of the multiple choice answers
	* @access public
	* @see ASS_Question:ASS_Question()
	*/
	function ASS_JavaApplet(
		$title = "",
		$comment = "",
		$author = "",
		$owner = -1,
		$question = "",
		$javaapplet_filename = ""
	)
	{
		$this->ASS_Question($title, $comment, $author, $owner);
		$this->question = $question;
		$this->javaapplet_filename = $javaapplet_filename;
		$this->parameters = array();
	}


	/**
	* Returns a QTI xml representation of the question
	*
	* Returns a QTI xml representation of the question and sets the internal
	* domxml variable with the DOM XML representation of the QTI xml representation
	*
	* @return string The QTI xml representation of the question
	* @access public
	*/
	function to_xml($a_include_header = true, $a_include_binary = true, $a_shuffle = false, $test_output = false)
	{
		if (!empty($this->domxml))
		{
			$this->domxml->free();
		}
		$xml_header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<questestinterop></questestinterop>\n";
		$this->domxml = domxml_open_mem($xml_header);
		$root = $this->domxml->document_element();

		// qti comment with version information
		$qtiComment = $this->domxml->create_element("qticomment");

		// qti ident
		$qtiIdent = $this->domxml->create_element("item");
		$qtiIdent->set_attribute("ident", "il_".IL_INST_ID."_qst_".$this->getId());
		$qtiIdent->set_attribute("title", $this->getTitle());
		$root->append_child($qtiIdent);

		// add question description
		$qtiComment = $this->domxml->create_element("qticomment");
		$qtiCommentText = $this->domxml->create_text_node($this->getComment());
		$qtiComment->append_child($qtiCommentText);
		$qtiIdent->append_child($qtiComment);

		// add estimated working time
		$qtiDuration = $this->domxml->create_element("duration");
		$workingtime = $this->getEstimatedWorkingTime();
		$qtiDurationText = $this->domxml->create_text_node(sprintf("P0Y0M0DT%dH%dM%dS", $workingtime["h"], $workingtime["m"], $workingtime["s"]));
		$qtiDuration->append_child($qtiDurationText);
		$qtiIdent->append_child($qtiDuration);

		// add ILIAS specific metadata
		$qtiItemmetadata = $this->domxml->create_element("itemmetadata");
		$qtiMetadata = $this->domxml->create_element("qtimetadata");
		
		$qtiMetadatafield = $this->domxml->create_element("qtimetadatafield");
		$qtiFieldlabel = $this->domxml->create_element("fieldlabel");
		$qtiFieldlabelText = $this->domxml->create_text_node("ILIAS_VERSION");
		$qtiFieldlabel->append_child($qtiFieldlabelText);
		$qtiFieldentry = $this->domxml->create_element("fieldentry");
		$qtiFieldentryText = $this->domxml->create_text_node($this->ilias->getSetting("ilias_version"));
		$qtiFieldentry->append_child($qtiFieldentryText);
		$qtiMetadatafield->append_child($qtiFieldlabel);
		$qtiMetadatafield->append_child($qtiFieldentry);
		$qtiMetadata->append_child($qtiMetadatafield);

		$qtiMetadatafield = $this->domxml->create_element("qtimetadatafield");
		$qtiFieldlabel = $this->domxml->create_element("fieldlabel");
		$qtiFieldlabelText = $this->domxml->create_text_node("QUESTIONTYPE");
		$qtiFieldlabel->append_child($qtiFieldlabelText);
		$qtiFieldentry = $this->domxml->create_element("fieldentry");
		$qtiFieldentryText = $this->domxml->create_text_node(JAVAAPPLET_QUESTION_IDENTIFIER);
		$qtiFieldentry->append_child($qtiFieldentryText);
		$qtiMetadatafield->append_child($qtiFieldlabel);
		$qtiMetadatafield->append_child($qtiFieldentry);
		$qtiMetadata->append_child($qtiMetadatafield);
		
		$qtiMetadatafield = $this->domxml->create_element("qtimetadatafield");
		$qtiFieldlabel = $this->domxml->create_element("fieldlabel");
		$qtiFieldlabelText = $this->domxml->create_text_node("AUTHOR");
		$qtiFieldlabel->append_child($qtiFieldlabelText);
		$qtiFieldentry = $this->domxml->create_element("fieldentry");
		$qtiFieldentryText = $this->domxml->create_text_node($this->getAuthor());
		$qtiFieldentry->append_child($qtiFieldentryText);
		$qtiMetadatafield->append_child($qtiFieldlabel);
		$qtiMetadatafield->append_child($qtiFieldentry);
		$qtiMetadata->append_child($qtiMetadatafield);
		
		$qtiItemmetadata->append_child($qtiMetadata);
		$qtiIdent->append_child($qtiItemmetadata);
		
		// PART I: qti presentation
		$qtiPresentation = $this->domxml->create_element("presentation");
		$qtiPresentation->set_attribute("label", $this->getTitle());

		// add flow to presentation
		$qtiFlow = $this->domxml->create_element("flow");

		// add material with question text to presentation
		$qtiMaterial = $this->domxml->create_element("material");
		$qtiMatText = $this->domxml->create_element("mattext");
		$qtiMatTextText = $this->domxml->create_text_node($this->getQuestion());
		$qtiMatText->append_child($qtiMatTextText);
		$qtiMaterial->append_child($qtiMatText);
		$qtiFlow->append_child($qtiMaterial);

		$solution = $this->getSuggestedSolution(0);
		if (count($solution))
		{
			if (preg_match("/il_(\d*?)_(\w+)_(\d+)/", $solution["internal_link"], $matches))
			{
				$qtiMaterial = $this->domxml->create_element("material");
				$qtiMaterial->set_attribute("label", "suggested_solution");
				$qtiMatText = $this->domxml->create_element("mattext");
				$intlink = "il_" . IL_INST_ID . "_" . $matches[2] . "_" . $matches[3];
				if (strcmp($matches[1], "") != 0)
				{
					$intlink = $solution["internal_link"];
				}
				$qtiMatTextText = $this->domxml->create_text_node($intlink);
				$qtiMatText->append_child($qtiMatTextText);
				$qtiMaterial->append_child($qtiMatText);
				$qtiFlow->append_child($qtiMaterial);
			}
		}
		
		$qtiMaterial = $this->domxml->create_element("material");
		$qtiMatApplet = $this->domxml->create_element("matapplet");
		$qtiMatApplet->set_attribute("label", "applet data");
		$qtiMatApplet->set_attribute("uri", $this->getJavaAppletFilename());
		$qtiMatApplet->set_attribute("height", $this->getJavaHeight());
		$qtiMatApplet->set_attribute("width", $this->getJavaWidth());
		$qtiMatApplet->set_attribute("embedded", "base64");
		$javapath = $this->getJavaPath() . $this->getJavaAppletFilename();
		$fh = @fopen($javapath, "rb");
		if ($fh == false)
		{
			//global $ilErr;
			//$ilErr->raiseError($this->lng->txt("error_open_java_file"), $ilErr->MESSAGE);
			return;
		}
		$javafile = fread($fh, filesize($javapath));
		fclose($fh);
		$base64 = base64_encode($javafile);
		$qtiBase64Data = $this->domxml->create_text_node($base64);
		$qtiMatApplet->append_child($qtiBase64Data);
		$qtiMaterial->append_child($qtiMatApplet);
		if ($this->buildParamsOnly())
		{
			if ($this->java_code)
			{
				$qtiMatText = $this->domxml->create_element("mattext");
				$qtiMatText->set_attribute("label", "java_code");
				$qtiAppletParams = $this->domxml->create_text_node($this->java_code);
				$qtiMatText->append_child($qtiAppletParams);
				$qtiMaterial->append_child($qtiMatText);
			}
			foreach ($this->parameters as $key => $value)
			{
				$qtiMatText = $this->domxml->create_element("mattext");
				$qtiMatText->set_attribute("label", $value["name"]);
				$qtiAppletParams = $this->domxml->create_text_node($value["value"]);
				$qtiMatText->append_child($qtiAppletParams);
				$qtiMaterial->append_child($qtiMatText);
			}
			if ($test_output)
			{
				require_once "./assessment/classes/class.ilObjTest.php";
				$qtiMatText = $this->domxml->create_element("mattext");
				$qtiMatText->set_attribute("label", "test_type");
				$qtiAppletParams = $this->domxml->create_text_node(ilObjTest::_getTestType($test_output));
				$qtiMatText->append_child($qtiAppletParams);
				$qtiMaterial->append_child($qtiMatText);
				$qtiMatText = $this->domxml->create_element("mattext");
				$qtiMatText->set_attribute("label", "test_id");
				$qtiAppletParams = $this->domxml->create_text_node($test_output);
				$qtiMatText->append_child($qtiAppletParams);
				$qtiMaterial->append_child($qtiMatText);
				$qtiMatText = $this->domxml->create_element("mattext");
				$qtiMatText->set_attribute("label", "question_id");
				$qtiAppletParams = $this->domxml->create_text_node($this->getId());
				$qtiMatText->append_child($qtiAppletParams);
				$qtiMaterial->append_child($qtiMatText);
				$qtiMatText = $this->domxml->create_element("mattext");
				$qtiMatText->set_attribute("label", "user_id");
				global $ilUser;
				$qtiAppletParams = $this->domxml->create_text_node($ilUser->id);
				$qtiMatText->append_child($qtiAppletParams);
				$qtiMaterial->append_child($qtiMatText);
				$qtiMatText = $this->domxml->create_element("mattext");
				$qtiMatText->set_attribute("label", "points_max");
				$qtiAppletParams = $this->domxml->create_text_node($this->getPoints());
				$qtiMatText->append_child($qtiAppletParams);
				$qtiMaterial->append_child($qtiMatText);
				$qtiMatText = $this->domxml->create_element("mattext");
				$qtiMatText->set_attribute("label", "post_url");
				$qtiAppletParams = $this->domxml->create_text_node(ilUtil::removeTrailingPathSeparators(ILIAS_HTTP_PATH) . "/assessment/save_java_question_result.php");
				$qtiMatText->append_child($qtiAppletParams);
				$qtiMaterial->append_child($qtiMatText);
				$info = $this->getReachedInformation($ilUser->id, $test_output);
				foreach ($info as $kk => $infodata)
				{
					$qtiMatText->append_child($qtiAppletParams);
					$qtiMaterial->append_child($qtiMatText);
					$qtiMatText = $this->domxml->create_element("mattext");
					$qtiMatText->set_attribute("label", "value_" . $infodata["order"] . "_1");
					$qtiAppletParams = $this->domxml->create_text_node($infodata["value1"]);
					$qtiMatText->append_child($qtiAppletParams);
					$qtiMaterial->append_child($qtiMatText);
					$qtiMatText->append_child($qtiAppletParams);
					$qtiMaterial->append_child($qtiMatText);
					$qtiMatText = $this->domxml->create_element("mattext");
					$qtiMatText->set_attribute("label", "value_" . $infodata["order"] . "_2");
					$qtiAppletParams = $this->domxml->create_text_node($infodata["value2"]);
					$qtiMatText->append_child($qtiAppletParams);
					$qtiMaterial->append_child($qtiMatText);
				}
			}
		}

		$qtiFlow->append_child($qtiMaterial);

		// add available points as material
		$qtiMaterial = $this->domxml->create_element("material");
		$qtiMatText = $this->domxml->create_element("mattext");
		$qtiMatText->set_attribute("label", "points");
		$qtiMatTextText = $this->domxml->create_text_node($this->getPoints());
		$qtiMatText->append_child($qtiMatTextText);
		$qtiMaterial->append_child($qtiMatText);
		$qtiFlow->append_child($qtiMaterial);

		$qtiPresentation->append_child($qtiFlow);
		$qtiIdent->append_child($qtiPresentation);

		$xml = $this->domxml->dump_mem(true);
		if (!$a_include_header)
		{
			$pos = strpos($xml, "?>");
			$xml = substr($xml, $pos + 2);
		}
//echo htmlentities($xml);
		return $xml;

	}

	/**
	* Sets the applet parameters from a parameter string containing all parameters in a list
	*
	* Sets the applet parameters from a parameter string containing all parameters in a list
	*
	* @param string $params All applet parameters in a list
	* @access public
	*/
	function splitParams($params = "")
	{
		$params_array = split("<separator>", $params);
		foreach ($params_array as $pair)
		{
			if (preg_match("/(.*?)\=(.*)/", $pair, $matches))
			{
				switch ($matches[1])
				{
					case "java_code" :
						$this->java_code = $matches[2];
						break;
					case "java_width" :
						$this->java_width = $matches[2];
						break;
					case "java_height" :
						$this->java_height = $matches[2];
						break;
				}
				if (preg_match("/param_name_(\d+)/", $matches[1], $found_key))
				{
					$this->parameters[$found_key[1]]["name"] = $matches[2];
				}
				if (preg_match("/param_value_(\d+)/", $matches[1], $found_key))
				{
					$this->parameters[$found_key[1]]["value"] = $matches[2];
				}
			}
		}
	}

	/**
	* Returns a string containing the applet parameters
	*
	* Returns a string containing the applet parameters. This is used for saving the applet data to database
	*
	* @return string All applet parameters
	* @access public
	*/
	function buildParams()
	{
		$params_array = array();
		if ($this->java_code)
		{
			array_push($params_array, "java_code=$this->java_code");
		}
		if ($this->java_width)
		{
			array_push($params_array, "java_width=$this->java_width");
		}
		if ($this->java_height)
		{
			array_push($params_array, "java_height=$this->java_height");
		}
		foreach ($this->parameters as $key => $value)
		{
			array_push($params_array, "param_name_$key=" . $value["name"]);
			array_push($params_array, "param_value_$key=" . $value["value"]);
		}
		return join($params_array, "<separator>");
	}

	/**
	* Returns a string containing the additional applet parameters
	*
	* Returns a string containing the additional applet parameters
	*
	* @return string All additional applet parameters
	* @access public
	*/
	function buildParamsOnly()
	{
		$params_array = array();
		if ($this->java_code)
		{
			array_push($params_array, "java_code=$this->java_code");
		}
		foreach ($this->parameters as $key => $value)
		{
			array_push($params_array, "param_name_$key=" . $value["name"]);
			array_push($params_array, "param_value_$key=" . $value["value"]);
		}
		return join($params_array, "<separator>");
	}

	/**
	* Returns true, if a imagemap question is complete for use
	*
	* Returns true, if a imagemap question is complete for use
	*
	* @return boolean True, if the imagemap question is complete for use, otherwise false
	* @access public
	*/
	function isComplete()
	{
		if (($this->title) and ($this->author) and ($this->question) and ($this->javaapplet_filename) and ($this->java_width) and ($this->java_height) and ($this->points != ""))
		{
			return true;
		}
			else
		{
			return false;
		}
	}


	/**
	* Saves a ASS_JavaApplet object to a database
	*
	* Saves a ASS_JavaApplet object to a database (experimental)
	*
	* @param object $db A pear DB object
	* @access public
	*/
	function saveToDb($original_id = "")
	{
		global $ilias;

		$complete = 0;
		if ($this->isComplete())
		{
			$complete = 1;
		}

		$db = & $ilias->db;

		$params = $this->buildParams();
		$estw_time = $this->getEstimatedWorkingTime();
		$estw_time = sprintf("%02d:%02d:%02d", $estw_time['h'], $estw_time['m'], $estw_time['s']);

		if ($original_id)
		{
			$original_id = $db->quote($original_id);
		}
		else
		{
			$original_id = "NULL";
		}

		if ($this->id == -1)
		{
			// Neuen Datensatz schreiben
			$now = getdate();
			$question_type = $this->getQuestionType();
			$created = sprintf("%04d%02d%02d%02d%02d%02d", $now['year'], $now['mon'], $now['mday'], $now['hours'], $now['minutes'], $now['seconds']);
			$query = sprintf("INSERT INTO qpl_questions (question_id, question_type_fi, obj_fi, title, comment, author, owner, question_text, points, working_time, shuffle, complete, image_file, params, created, original_id, TIMESTAMP) VALUES (NULL, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, NULL)",
				$db->quote($question_type . ""),
				$db->quote($this->obj_id . ""),
				$db->quote($this->title . ""),
				$db->quote($this->comment . ""),
				$db->quote($this->author . ""),
				$db->quote($this->owner . ""),
				$db->quote($this->question . ""),
				$db->quote($this->points . ""),
				$db->quote($estw_time . ""),
				$db->quote($this->shuffle . ""),
				$db->quote($complete . ""),
				$db->quote($this->javaapplet_filename . ""),
				$db->quote($params . ""),
				$db->quote($created . ""),
				$original_id
			);

			$result = $db->query($query);
			if ($result == DB_OK)
			{
				$this->id = $this->ilias->db->getLastInsertId();

				// create page object of question
				$this->createPageObject();

				// Falls die Frage in einen Test eingef�gt werden soll, auch diese Verbindung erstellen
				if ($this->getTestId() > 0)
				{
					$this->insertIntoTest($this->getTestId());
				}
			}
		}
		else
		{
			// Vorhandenen Datensatz aktualisieren
			$query = sprintf("UPDATE qpl_questions SET obj_fi = %s, title = %s, comment = %s, author = %s, question_text = %s, points = %s, working_time=%s, shuffle = %s, complete = %s, image_file = %s, params = %s WHERE question_id = %s",
				$db->quote($this->obj_id. ""),
				$db->quote($this->title . ""),
				$db->quote($this->comment . ""),
				$db->quote($this->author . ""),
				$db->quote($this->question . ""),
				$db->quote($this->points . ""),
				$db->quote($estw_time . ""),
				$db->quote($this->shuffle . ""),
				$db->quote($complete . ""),
				$db->quote($this->javaapplet_filename . ""),
				$db->quote($params . ""),
				$db->quote($this->id . "")
			);
			$result = $db->query($query);
		}
		parent::saveToDb($original_id);
	}

	/**
	* Loads a ASS_JavaApplet object from a database
	*
	* Loads a ASS_JavaApplet object from a database (experimental)
	*
	* @param object $db A pear DB object
	* @param integer $question_id A unique key which defines the multiple choice test in the database
	* @access public
	*/
	function loadFromDb($question_id)
	{
		global $ilias;

		$db = & $ilias->db;
		$query = sprintf("SELECT * FROM qpl_questions WHERE question_id = %s",
			$db->quote($question_id)
		);
		$result = $db->query($query);

		if (strcmp(strtolower(get_class($result)), db_result) == 0)
		{
			if ($result->numRows() == 1)
			{
				$data = $result->fetchRow(DB_FETCHMODE_OBJECT);
				$this->id = $question_id;
				$this->title = $data->title;
				$this->comment = $data->comment;
				$this->obj_id = $data->obj_fi;
				$this->author = $data->author;
				$this->points = $data->points;
				$this->owner = $data->owner;
				$this->original_id = $data->original_id;
				$this->javaapplet_filename = $data->image_file;
				$this->question = $data->question_text;
				$this->solution_hint = $data->solution_hint;
				$this->splitParams($data->params);
				$this->setShuffle($data->shuffle);
				$this->setEstimatedWorkingTime(substr($data->working_time, 0, 2), substr($data->working_time, 3, 2), substr($data->working_time, 6, 2));
			}
		}
		parent::loadFromDb($question_id);
	}

	/**
	* Duplicates an ASS_JavaApplet
	*
	* Duplicates an ASS_JavaApplet
	*
	* @access public
	*/
	function duplicate($for_test = true, $title = "", $author = "", $owner = "")
	{
		if ($this->id <= 0)
		{
			// The question has not been saved. It cannot be duplicated
			return;
		}
		// duplicate the question in database
		$clone = $this;
		include_once ("./assessment/classes/class.assQuestion.php");
		$original_id = ASS_Question::_getOriginalId($this->id);
		$clone->id = -1;
		if ($title)
		{
			$clone->setTitle($title);
		}
		if ($author)
		{
			$clone->setAuthor($author);
		}
		if ($owner)
		{
			$clone->setOwner($owner);
		}
		if ($for_test)
		{
			$clone->saveToDb($original_id);
		}
		else
		{
			$clone->saveToDb();
		}

		// copy question page content
		$clone->copyPageOfQuestion($original_id);

		// duplicate the image
		$clone->duplicateApplet($original_id);
		return $clone->id;
	}

	/**
	* Copies an ASS_JavaApplet object
	*
	* Copies an ASS_JavaApplet object
	*
	* @access public
	*/
	function copyObject($target_questionpool, $title = "")
	{
		if ($this->id <= 0)
		{
			// The question has not been saved. It cannot be duplicated
			return;
		}
		// duplicate the question in database
		$clone = $this;
		include_once ("./assessment/classes/class.assQuestion.php");
		$original_id = ASS_Question::_getOriginalId($this->id);
		$clone->id = -1;
		$source_questionpool = $this->getObjId();
		$clone->setObjId($target_questionpool);
		if ($title)
		{
			$clone->setTitle($title);
		}
		$clone->saveToDb();

		// copy question page content
		$clone->copyPageOfQuestion($original_id);

		// duplicate the image
		$clone->copyApplet($original_id, $source_questionpool);
		return $clone->id;
	}
	
	function duplicateApplet($question_id)
	{
		$javapath = $this->getJavaPath();
		$javapath_original = preg_replace("/([^\d])$this->id([^\d])/", "\${1}$question_id\${2}", $javapath);
		if (!file_exists($javapath))
		{
			ilUtil::makeDirParents($javapath);
		}
		$filename = $this->getJavaAppletFilename();
		if (!copy($javapath_original . $filename, $javapath . $filename)) {
			print "java applet could not be duplicated!!!! ";
		}
	}

	function copyApplet($question_id, $source_questionpool)
	{
		$javapath = $this->getJavaPath();
		$javapath_original = preg_replace("/([^\d])$this->id([^\d])/", "\${1}$question_id\${2}", $javapath);
		$javapath_original = str_replace("/$this->obj_id/", "/$source_questionpool/", $javapath_original);
		if (!file_exists($javapath))
		{
			ilUtil::makeDirParents($javapath);
		}
		$filename = $this->getJavaAppletFilename();
		if (!copy($javapath_original . $filename, $javapath . $filename)) {
			print "java applet could not be copied!!!! ";
		}
	}

	/**
	* Gets the multiple choice question
	*
	* Gets the question string of the ASS_JavaApplet object
	*
	* @return string The question string of the ASS_JavaApplet object
	* @access public
	* @see $question
	*/
	function getQuestion()
	{
		return $this->question;
	}

	/**
	* Sets the question text
	*
	* Sets the question string of the ASS_JavaApplet object
	*
	* @param string $question A string containing the question text
	* @access public
	* @see $question
	*/
	function setQuestion($question = "")
	{
		$this->question = $question;
	}

	/**
	* Returns the maximum points, a learner can reach answering the question
	*
	* Returns the maximum points, a learner can reach answering the question
	*
	* @access public
	* @see $points
	*/
	function getMaximumPoints()
	{
		return $this->points;
	}

	/**
	* Returns the java applet code parameter
	*
	* Returns the java applet code parameter
	*
	* @return string java applet code parameter
	* @access public
	*/
	function getJavaCode()
	{
		return $this->java_code;
	}

	/**
	* Sets the java applet code parameter
	*
	* Sets the java applet code parameter
	*
	* @param string java applet code parameter
	* @access public
	*/
	function setJavaCode($java_code = "")
	{
		$this->java_code = $java_code;
	}

	/**
	* Returns the java applet width parameter
	*
	* Returns the java applet width parameter
	*
	* @return integer java applet width parameter
	* @access public
	*/
	function getJavaWidth()
	{
		return $this->java_width;
	}

	/**
	* Sets the java applet width parameter
	*
	* Sets the java applet width parameter
	*
	* @param integer java applet width parameter
	* @access public
	*/
	function setJavaWidth($java_width = "")
	{
		$this->java_width = $java_width;
	}

	/**
	* Returns the java applet height parameter
	*
	* Returns the java applet height parameter
	*
	* @return integer java applet height parameter
	* @access public
	*/
	function getJavaHeight()
	{
		return $this->java_height;
	}

	/**
	* Sets the java applet height parameter
	*
	* Sets the java applet height parameter
	*
	* @param integer java applet height parameter
	* @access public
	*/
	function setJavaHeight($java_height = "")
	{
		$this->java_height = $java_height;
	}

	/**
	* Returns the points, a learner has reached answering the question
	*
	* Returns the points, a learner has reached answering the question
	* The points are calculated from the given answers including checks
	* for all special scoring options in the test container.
	*
	* @param integer $user_id The database ID of the learner
	* @param integer $test_id The database Id of the test containing the question
	* @access public
	*/
	function calculateReachedPoints($user_id, $test_id)
	{
		global $ilDB;
		
		$found_values = array();
		$query = sprintf("SELECT * FROM tst_solutions WHERE user_fi = %s AND test_fi = %s AND question_fi = %s",
			$ilDB->quote($user_id),
			$ilDB->quote($test_id),
			$ilDB->quote($this->getId())
		);
		$result = $ilDB->query($query);
		$points = 0;
		while ($data = $result->fetchRow(DB_FETCHMODE_OBJECT))
		{
			$points += $data->points;
		}

		// check for special scoring options in test
		$query = sprintf("SELECT * FROM tst_tests WHERE test_id = %s",
			$ilDB->quote($test_id)
		);
		$result = $ilDB->query($query);
		if ($result->numRows() == 1)
		{
			$row = $result->fetchRow(DB_FETCHMODE_ASSOC);
			if ($row["count_system"] == 1)
			{
				if ($points != $this->getMaximumPoints())
				{
					$points = 0;
				}
			}
		}
		else
		{
			$points = 0;
		}
		return $points;
	}

	/**
	* Returns the evaluation data, a learner has entered to answer the question
	*
	* Returns the evaluation data, a learner has entered to answer the question
	*
	* @param integer $user_id The database ID of the learner
	* @param integer $test_id The database Id of the test containing the question
	* @access public
	*/
	function getReachedInformation($user_id, $test_id)
	{
		$found_values = array();
		$query = sprintf("SELECT * FROM tst_solutions WHERE user_fi = %s AND test_fi = %s AND question_fi = %s",
			$this->ilias->db->quote($user_id),
			$this->ilias->db->quote($test_id),
			$this->ilias->db->quote($this->getId())
		);
		$result = $this->ilias->db->query($query);
		$counter = 1;
		$user_result = array();
		while ($data = $result->fetchRow(DB_FETCHMODE_OBJECT))
		{
			$true = 0;
			if ($data->points > 0)
			{
				$true = 1;
			}
			$solution = array(
				"order" => "$counter",
				"points" => "$data->points",
				"true" => "$true",
				"value1" => "$data->value1",
				"value2" => "$data->value2",
			);
			$counter++;
			array_push($user_result, $solution);
		}
		return $user_result;
	}

	/**
	* Adds a new parameter value to the parameter list
	*
	* Adds a new parameter value to the parameter list
	*
	* @param string $name The name of the parameter value
	* @param string $value The value of the parameter value
	* @access public
	* @see $parameters
	*/
	function addParameter($name = "", $value = "")
	{
		$index = $this->getParameterIndex($name);
		if ($index > -1)
		{
			$this->parameters[$index] = array("name" => $name, "value" => $value);
		}
		else
		{
			array_push($this->parameters, array("name" => $name, "value" => $value));
		}
	}

	/**
	* Adds a new parameter value to the parameter list at a given index
	*
	* Adds a new parameter value to the parameter list at a given index
	*
	* @param integer $index The index at which the parameter should be inserted
	* @param string $name The name of the parameter value
	* @param string $value The value of the parameter value
	* @access public
	* @see $parameters
	*/
	function addParameterAtIndex($index = 0, $name = "", $value = "")
	{
		$this->parameters[$index] = array("name" => $name, "value" => $value);
	}

	/**
	* Removes a parameter value from the parameter list
	*
	* Removes a parameter value from the parameter list
	*
	* @param string $name The name of the parameter value
	* @access public
	* @see $parameters
	*/
	function removeParameter($name)
	{
		foreach ($this->parameters as $key => $value)
		{
			if (strcmp($name, $value["name"]) == 0)
			{
				array_splice($this->parameters, $key, 1);
				return;
			}
		}
	}

	/**
	* Returns the paramter at a given index
	*
	* Returns the paramter at a given index
	*
	* @param intege $index The index value of the parameter
	* @return array The parameter at the given index
	* @access public
	* @see $parameters
	*/
	function getParameter($index)
	{
		if (($index < 0) or ($index >= count($this->parameters)))
		{
			return undef;
		}
		return $this->parameters[$index];
	}

	/**
	* Returns the index of an applet parameter
	*
	* Returns the index of an applet parameter
	*
	* @param string $name The name of the parameter value
	* @return integer The index of the applet parameter or -1 if the parameter wasn't found
	* @access private
	* @see $parameters
	*/
	function getParameterIndex($name)
	{
		foreach ($this->parameters as $key => $value)
		{
			if (array_key_exists($name, $value))
			{
				return $key;
			}
		}
		return -1;
	}

	/**
	* Returns the number of additional applet parameters
	*
	* Returns the number of additional applet parameters
	*
	* @return integer The number of additional applet parameters
	* @access public
	* @see $parameters
	*/
	function getParameterCount()
	{
		return count($this->parameters);
	}

	/**
	* Removes all applet parameters
	*
	* Removes all applet parameters
	*
	* @access public
	* @see $parameters
	*/
	function flushParams()
	{
		$this->parameters = array();
	}

	/**
	* Saves the learners input of the question to the database
	*
	* Saves the learners input of the question to the database
	*
	* @param integer $test_id The database id of the test containing this question
  * @return boolean Indicates the save status (true if saved successful, false otherwise)
	* @access public
	* @see $answers
	*/
	function saveWorkingData($test_id, $limit_to = LIMIT_NO_LIMIT)
	{
    parent::saveWorkingData($test_id);
		return true;
		/*    global $ilDB;
			global $ilUser;
	    $db =& $ilDB->db;

    	if ($this->response == RESPONSE_SINGLE) {
			$query = sprintf("SELECT * FROM tst_solutions WHERE user_fi = %s AND test_fi = %s AND question_fi = %s",
				$db->quote($ilUser->id),
				$db->quote($test_id),
				$db->quote($this->getId())
			);
			$result = $db->query($query);
			$row = $result->fetchRow(DB_FETCHMODE_OBJECT);
			$update = $row->solution_id;
			if ($update) {
				$query = sprintf("UPDATE tst_solutions SET value1 = %s WHERE solution_id = %s",
					$db->quote($_POST["multiple_choice_result"]),
					$db->quote($update)
				);
			} else {
				$query = sprintf("INSERT INTO tst_solutions (solution_id, user_fi, test_fi, question_fi, value1, value2, TIMESTAMP) VALUES (NULL, %s, %s, %s, %s, NULL, NULL)",
					$db->quote($ilUser->id),
					$db->quote($test_id),
					$db->quote($this->getId()),
					$db->quote($_POST["multiple_choice_result"])
				);
			}
      $result = $db->query($query);
    } else {
			$query = sprintf("DELETE FROM tst_solutions WHERE user_fi = %s AND test_fi = %s AND question_fi = %s",
				$db->quote($ilUser->id),
				$db->quote($test_id),
				$db->quote($this->getId())
			);
			$result = $db->query($query);
      foreach ($_POST as $key => $value) {
        if (preg_match("/multiple_choice_result_(\d+)/", $key, $matches)) {
					$query = sprintf("INSERT INTO tst_solutions (solution_id, user_fi, test_fi, question_fi, value1, value2, TIMESTAMP) VALUES (NULL, %s, %s, %s, %s, NULL, NULL)",
						$db->quote($ilUser->id),
						$db->quote($test_id),
						$db->quote($this->getId()),
						$db->quote($value)
					);
          $result = $db->query($query);
        }
      }
    }
    //parent::saveWorkingData($limit_to);
		return true;
*/  }

	/**
	* Gets the java applet file name
	*
	* Gets the java applet file name
	*
	* @return string The java applet file of the ASS_JavaApplet object
	* @access public
	* @see $javaapplet_filename
	*/
	function getJavaAppletFilename()
	{
		return $this->javaapplet_filename;
	}

	/**
	* Sets the java applet file name
	*
	* Sets the java applet file name
	*
	* @param string $javaapplet_file.
	* @access public
	* @see $javaapplet_filename
	*/
	function setJavaAppletFilename($javaapplet_filename, $javaapplet_tempfilename = "")
	{
		if (!empty($javaapplet_filename))
		{
			$this->javaapplet_filename = $javaapplet_filename;
		}
		if (!empty($javaapplet_tempfilename))
		{
			$javapath = $this->getJavaPath();
			if (!file_exists($javapath))
			{
				ilUtil::makeDirParents($javapath);
			}
			
			//if (!move_uploaded_file($javaapplet_tempfilename, $javapath . $javaapplet_filename))
			if (!ilUtil::moveUploadedFile($javaapplet_tempfilename, $javaapplet_filename, $javapath.$javaapplet_filename))
			{
				print "java applet not uploaded!!!! ";
			}
		}
	}

	function syncWithOriginal()
	{
		global $ilias;
		if ($this->original_id)
		{
			$complete = 0;
			if ($this->isComplete())
			{
				$complete = 1;
			}
			$db = & $ilias->db;
	
			$estw_time = $this->getEstimatedWorkingTime();
			$estw_time = sprintf("%02d:%02d:%02d", $estw_time['h'], $estw_time['m'], $estw_time['s']);
	
			$query = sprintf("UPDATE qpl_questions SET obj_fi = %s, title = %s, comment = %s, author = %s, question_text = %s, points = %s, working_time=%s, shuffle = %s, complete = %s, image_file = %s, params = %s WHERE question_id = %s",
				$db->quote($this->obj_id. ""),
				$db->quote($this->title . ""),
				$db->quote($this->comment . ""),
				$db->quote($this->author . ""),
				$db->quote($this->question . ""),
				$db->quote($this->points . ""),
				$db->quote($estw_time . ""),
				$db->quote($this->shuffle . ""),
				$db->quote($complete . ""),
				$db->quote($this->javaapplet_filename . ""),
				$db->quote($params . ""),
				$db->quote($this->original_id . "")
			);
			$result = $db->query($query);

			parent::syncWithOriginal();
		}
	}

	/**
	* Returns the question type of the question
	*
	* Returns the question type of the question
	*
	* @return integer The question type of the question
	* @access public
	*/
	function getQuestionType()
	{
		return 7;
	}
}

?>
