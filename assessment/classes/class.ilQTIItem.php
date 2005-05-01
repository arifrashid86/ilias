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

include_once ("./assessment/classes/class.ilQTIResponse.php");

/**
* QTI item class
*
* @author Helmut Schottmüller <hschottm@gmx.de>
* @version $Id$
*
* @package assessment
*/
class ilQTIItem
{
	var $ident;
	var $title;
	var $maxattempts;
	var $label;
	var $xmllang;
	
	var $comment;
	var $ilias_version;
	var $author;
	var $questiontype;
	var $duration;
	var $questiontext;
	var $response;
	var $resprocessing;
	var $itemfeedback;
	
	function ilQTIItem()
	{
		$this->response = array();
		$this->resprocessing = array();
		$this->itemfeedback = array();
	}
	
	function setIdent($a_ident)
	{
		$this->ident = $a_ident;
	}
	
	function getIdent()
	{
		return $this->ident;
	}
	
	function setTitle($a_title)
	{
		$this->title = $a_title;
	}
	
	function getTitle()
	{
		return $this->title;
	}
	
	function setComment($a_comment)
	{
		if (preg_match("/(.*?)\=(.*)/", $a_comment, $matches))
		{
			// special comments written by ILIAS
			switch ($matches)
			{
				case "ILIAS Version":
					$this->ilias_version = $matches[2];
					return;
					break;
				case "Questiontype":
					$this->questiontype = $matches[2];
					return;
					break;
				case "Author":
					$this->author = $matches[2];
					return;
					break;
			}
		}
		$this->comment = $a_comment;
	}
	
	function setDuration($a_duration)
	{
		if (preg_match("/P(\d+)Y(\d+)M(\d+)DT(\d+)H(\d+)M(\d+)S/", $a_duration, $matches))
		{
			$this->duration = array(
				"h" => $matches[4], 
				"m" => $matches[5], 
				"s" => $matches[6]
			);
		}
	}
	
	function getDuration()
	{
		return $this->duration;
	}
	
	function setQuestiontext($a_questiontext)
	{
		$this->questiontext = $a_questiontext;
	}
	
	function getQuestiontext()
	{
		return $this->questiontext;
	}
	
	function setResponse($response_type)
	{
		$this->response = new ilQTIResponse($response_type);
	}
	
	function addResponse($a_response)
	{
		array_push($this->response, $a_response);
	}
	
	function addResprocessing($a_resprocessing)
	{
		array_push($this->resprocessing, $a_resprocessing);
	}
	
	function addItemfeedback($a_itemfeedback)
	{
		array_push($this->itemfeedback, $a_itemfeedback);
	}
	
	function setMaxattempts($a_maxattempts)
	{
		$this->maxattempts = $a_maxattempts;
	}
	
	function getMaxattempts()
	{
		return $this->maxattempts;
	}
	
	function setLabel($a_label)
	{
		$this->label = $a_label;
	}
	
	function getLabel()
	{
		return $this->label;
	}
	
	function setXmllang($a_xmllang)
	{
		$this->xmllang = $a_xmllang;
	}
	
	function getXmllang()
	{
		return $this->xmllang;
	}
}
?>
