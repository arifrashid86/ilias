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
* Error Handling & global info handling
* uses PEAR error class
*
* @author	Stefan Meyer <smeyer@databay.de>
* @author	Sascha Hofmann <shofmann@databay.de>
* @version	$Id$
* @package	application
* @todo		when an error occured and clicking the back button to return to previous page the referer-var in session is deleted -> server error
*/
class ilErrorHandling
{
	/**
	* Toggle debugging on/off
	* @var		boolean
	* @access	private
	*/
	var $DEBUG_ENV;

	/**
	* Error level 1: exit application immedietly
	* @var		integer
	* @access	public
	*/
	var $FATAL;

	/**
	* Error level 2: show warning page
	* @var		integer
	* @access	public
	*/
	var $WARNING;

	/**
	* Error level 3: show message in recent page
	* @var		integer
	* @access	public
	*/
	var $MESSAGE;

	/**
	* Constructor
	* @access	public
	*/
	function ilErrorHandling()
	{
		// init vars
		$this->DEBUG_ENV = true;
		$this->FATAL	 = 1;
		$this->WARNING	 = 2;
		$this->MESSAGE	 = 3;
	}
	
	/**
	* defines what has to happen in case of error
	* @access	private
	* @param	object	Error
	*/
	function errorHandler($a_error_obj)
	{
		global $log;
		
		if ($_SESSION["message"])
		{
			return;
		}

		if (is_object($log) and $log->enabled == true)
		{
			$log->logError($a_error_obj->getCode(),$a_error_obj->getMessage());
		}

		if ($a_error_obj->getCode() == $this->FATAL)
		{
			die (stripslashes($a_error_obj->getMessage()));
		}

		if ($a_error_obj->getCode() == $this->WARNING)
		{
			if ($this->DEBUG_ENV)
			{
				$message = $a_error_obj->getMessage();
			}
			else
			{
				$message = "Under Construction";
			}

			$_SESSION["message"] = $message;

			if (!defined("ILIAS_MODULE"))
			{
				header("location: error.php");
			}
			else
			{
				header("location: ../error.php");
			}
			
			exit();
		}

		if ($a_error_obj->getCode() == $this->MESSAGE)
		{
			// check if already GET-Parameters exists in Referer-URI
			if (substr($_SESSION["referer"],-4) == ".php")
			{
				$glue = "?";
			}
			else
			{
				$glue = "&";
			}

			$_SESSION["message"] = $a_error_obj->getMessage();
			// save post vars to session in case of error
			$_SESSION["error_post_vars"] = $_POST;

			header("location: ".$_SESSION["referer"].$glue);
			exit();
		}
	}
} // END class.ilErrorHandling
?>
