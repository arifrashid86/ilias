<?php
/**
* Class ilForumExplorer 
* class for explorer view of forum posts
* 
* @author Stefan Meyer <smeyer@databay.de>
* @version $Id$
* 
* @package ilias-forum
*/
require_once("./classes/class.ilExplorer.php");
require_once("./classes/class.ilForum.php");

class ilForumExplorer extends ilExplorer
{
	/**
	* id of thread
	* @var int thread_pk
	* @access private
	*/
	var $thread_id;

	/**
	* id of root node
	* @var int root_id
	* @access private
	*/
	var $root_id;

	/**
	* forum object, used for owerwritten tree methods
	* @var object forum object
	* @access private
	*/
	var $forum;

	/**
	* Constructor
	* @access	public
	* @param	string	scriptname
	*/
	function ilForumExplorer($a_target,$a_thread_id)
	{
		global $lng;

		$lng->loadLanguageModule("forum");

		parent::ilExplorer($a_target);
		$this->thread_id = $a_thread_id;
		$this->forum = new ilForum();
		$tmp_array = $this->forum->getFirstPostNode($this->thread_id);
		$this->root_id = $tmp_array["id"];
	}

	/**
	* set the varname in Get-string
	* recursive method
	* @access	public
	* @param	string		varname containing Ids to be used in GET-string
	*/
	function setTargetGet($a_target_get)
	{
		if (!isset($a_target_get) or !is_string($a_target_get))
		{
			$this->ilias->raiseError(get_class($this)."::setTargetGet(): No target given!",$this->ilias->error_obj->WARNING);
		}

		$this->target_get = $a_target_get;
	}

	/**
	* Creates output for explorer view in admin menue
	* recursive method
	* @access	public
	* @param	integer		parent_node_id where to start from (default=0, 'root')
	* @param	integer		depth level where to start (default=1)
	* @return	string
	*/
	function setOutput($a_parent, $a_depth = 1)
	{
		global $lng;
		static $counter = 0;

		if ($objects =  $this->forum->getPostChilds($a_parent,$this->thread_id))
		{
			$tab = ++$a_depth - 2;
			
			foreach ($objects as $key => $object)
			{
				if ($object["child"] != $this->root_id)
				{
					$parent_index = $this->getIndex($object);
				}
				$this->format_options["$counter"]["parent"] = $object["parent"];
				$this->format_options["$counter"]["child"] = $object["child"];
				$this->format_options["$counter"]["title"] = $object["title"];
				$this->format_options["$counter"]["type"] = $object["type"];
				$this->format_options["$counter"]["depth"] = $tab;
				$this->format_options["$counter"]["container"] = false;
				$this->format_options["$counter"]["visible"]	  = true;

				// Create prefix array
				for ($i = 0; $i < $tab; ++$i)
				{
					$this->format_options["$counter"]["tab"][] = 'blank';
				}
				// only if parent is expanded and visible, object is visible
				if ($object["child"] != $this->root_id  and (!in_array($object["parent"],$this->expanded) 
														  or !$this->format_options["$parent_index"]["visible"]))
				{
					$this->format_options["$counter"]["visible"] = false;
				}
				// if object exists parent is container
				if ($object["child"] != $this->root_id)
				{
					$this->format_options["$parent_index"]["container"] = true;

					if (in_array($object["parent"],$this->expanded))
					{
						$this->format_options["$parent_index"]["tab"][($tab-2)] = 'minus';
					}
					else
					{
						$this->format_options["$parent_index"]["tab"][($tab-2)] = 'plus';
					}
				}

				++$counter;

				// Recursive
				$this->setOutput($object["child"],$a_depth);
			} //foreach
		} //if
	} //function

	/**
	* Creates output
	* recursive method
	* @access	public
	* @return	string
	*/
	function getOutput()
	{
		$this->format_options[0]["tab"] = array();
		$depth = $this->forum->getPostMaximumDepth($this->thread_id);
		for ($i=0;$i<$depth;++$i)
		{
			$this->createLines($i);
		}

		foreach ($this->format_options as $key => $options)
		{
			if($key == 0)
			{
				$this->formatHeader();
			}
			if ($options["visible"])
			{
				$this->formatObject($options["child"],$options);
			}
		}

		return implode('',$this->output);
	}
	
	/**
	* Creates output
	* recursive method
	* @access	private
	* @param	integer
	* @param	array
	* @return	string
	*/
	function formatObject($a_node_id,$a_option)
	{
		if (!isset($a_node_id) or !is_array($a_option))
		{
			$this->ilias->raiseError(get_class($this)."::formatObject(): Missing parameter or wrong datatype! ".
									"node_id: ".$a_node_id." options:".var_dump($a_option),$this->ilias->error_obj->WARNING);
		}

		$tpl = new ilTemplate("tpl.tree.html", true, true);

		foreach ($a_option["tab"] as $picture)
		{
			if ($picture == 'plus')
			{
				$target = $this->createTarget('+',$a_node_id);
				$tpl->setCurrentBlock("expander");
				$tpl->setVariable("LINK_TARGET", $target);
				$tpl->setVariable("IMGPATH", ilUtil::getImagePath("browser/plus.gif"));
				$tpl->parseCurrentBlock();
			}

			if ($picture == 'minus')
			{
				$target = $this->createTarget('-',$a_node_id);
				$tpl->setCurrentBlock("expander");
				$tpl->setVariable("LINK_TARGET", $target);
				$tpl->setVariable("IMGPATH", ilUtil::getImagePath("browser/minus.gif"));
				$tpl->parseCurrentBlock();
			}

			if ($picture == 'blank' or $picture == 'winkel'
			   or $picture == 'hoch' or $picture == 'quer' or $picture == 'ecke')
			{
				$tpl->setCurrentBlock("expander");
				$tpl->setVariable("IMGPATH", ilUtil::getImagePath("browser/".$picture.".gif"));
				$tpl->parseCurrentBlock();
			}
		}

		$tpl->setCurrentBlock("row");
		$tpl->setVariable("TYPE", $a_option["type"]);
		$target = (strpos($this->target, "?") === false) ?
			$this->target."?" : $this->target."&";
		$tpl->setVariable("LINK_TARGET", $target.$this->target_get."=".$a_node_id."#".$a_node_id);
		$tpl->setVariable("TITLE", $a_option["title"]);

		if ($this->frameTarget != "")
		{
			$tpl->setVariable("TARGET", " target=\"".$this->frameTarget."\"");
		}

		$tpl->parseCurrentBlock();

		$this->output[] = $tpl->get();
	}
	
	/**
	* method to create a forum system specific header
	* @access	public
	* @param	integer obj_id
	* @param	integer array options
	* @return	string
	*/
	function formatHeader()
	{
		global $lng, $ilias;

		$tpl = new ilTemplate("tpl.tree.html", true, true);

		$frm = new ilForum();
		$frm->setWhereCondition("thr_pk = ".$this->thread_id);
		$threadData = $frm->getOneThread();

		$tpl->setVariable("TXT_PAGEHEADLINE", $threadData["thr_subject"]);
		

		$tpl->setCurrentBlock("row");
		$tpl->setVariable("TYPE", "cat");
		$tpl->setVariable("TITLE", $a_option["title"]." ".$lng->txt("forums_thread").": ".$threadData["thr_subject"]);

		$tpl->parseCurrentBlock();
		
		$this->output[] = $tpl->get();
	}

	/**
	* Creates Get Parameter
	* @access	private
	* @param	string
	* @param	integer
	* @return	string
	*/
	function createTarget($a_type,$a_node_id)
	{
		if (!isset($a_type) or !is_string($a_type) or !isset($a_node_id))
		{
			$this->ilias->raiseError(get_class($this)."::createTarget(): Missing parameter or wrong datatype! ".
									"type: ".$a_type." node_id:".$a_node_id,$this->ilias->error_obj->WARNING);
		}
		list($tmp,$get) = explode("?",$this->target);
		// SET expand parameter:
		//     positive if object is expanded
		//     negative if object is compressed
		$a_node_id = $a_type == '+' ? $a_node_id : -(int) $a_node_id;

		return $_SERVER["SCRIPT_NAME"]."?".$get."&fexpand=".$a_node_id;
	}

	
	/**
	* set the expand option
	* this value is stored in a SESSION variable to save it different view (lo view, frm view,...)
	* @access	private
	* @param	string		pipe-separated integer
	*/
	function setExpand($a_node_id)
	{
		// IF ISN'T SET CREATE SESSION VARIABLE
		if(!is_array($_SESSION["fexpand"]))
		{
			$_SESSION["fexpand"] = array();
		}
		// IF $_GET["expand"] is positive => expand this node
		if($a_node_id > 0 && !in_array($a_node_id,$_SESSION["fexpand"]))
		{
			array_push($_SESSION["fexpand"],$a_node_id);
		}
		// IF $_GET["expand"] is negative => compress this node
		if($a_node_id < 0)
		{
			$key = array_keys($_SESSION["fexpand"],-(int) $a_node_id);
			unset($_SESSION["fexpand"][$key[0]]);
		}
		$this->expanded = $_SESSION["fexpand"];
	}
} // END class.ilExplorer
?>
