<?php
/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2006 ILIAS open source, University of Cologne            |
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
* Presentation of search results using object list gui
* 
* @author Stefan Meyer <meyer@leifos.com>
* @version $Id$
* 
*
* @ingroup ServicesSearch
*/
class ilLuceneSearchResultPresentation
{
	protected $tpl;
	protected $lng;

	private $results = array();
	private $searcher = null;
	
	private $container = null;
	
	/**
	 * Constructor 
	 * @param object	$container container gui object
	 */
	public function __construct($container = null)
	{
		global $tpl,$lng;
		
		$this->lng = $lng;
		$this->container = $container;
		
		if(isset($_GET['details']))
		{
			include_once './Services/Object/classes/class.ilSubItemListGUI.php';
			ilSubItemListGUI::setShowDetails((int) $_GET['details']);
		}
	}
	
	/**
	 * Get container gui
	 */
	public function getContainer()
	{
		return $this->container;
	}
	
	/**
	 * Set result array 
	 * @param array $a_result_data result array
	 */
	public function setResults($a_result_data)
	{
		$this->results = $a_result_data;
	}
	
	/**
	 * get results 
	 * @return array result array
	 */
	public function getResults()
	{
		return $this->results ? $this->results : array();
	}
	
	/**
	 * Get HTML 
	 * @return string HTML 
	 */
	public function getHTML()
	{
		return $this->html;
	}
	
	/**
	 * set searcher 
	 * @param
	 * @return
	 */
	public function setSearcher($a_searcher)
	{
		$this->searcher = $a_searcher;
	}
	
	/**
	 * Parse results 
	 * @param void
	 * @return string html
	 */
	public function render()
	{
		return $this->renderItemList();
	}
	
	/**
	 * Render item list 
	 * @return void
	 */
	protected function renderItemList()
	{
		$this->html = '';
		$this->newBockTemplate();
		$item_html = array();
		foreach($this->getResults() as $ref_id => $res_data)
		{
			$obj_id = $res_data;
			#$obj_id = ilObject::_lookupObjId($res_data);
			$type = ilObject::_lookupType($obj_id);
			$title = ilObject::_lookupTitle($obj_id);
			$title = $this->lookupTitle($obj_id,0);
			$description = $this->lookupDescription($obj_id,0);
			
			include_once './Services/Search/classes/Lucene/class.ilLuceneSearchObjectListGUIFactory.php';
			$item_list_gui = ilLuceneSearchObjectListGUIFactory::factory($type);
			$item_list_gui->setContainerObject($this->getContainer());
			$item_list_gui->setSearchFragment($this->lookupContent($obj_id,0));
			
			if($html = $item_list_gui->getListItemHTML($ref_id,$obj_id,$title,$description))
			{
				$html .= $this->appendSubItems($item_list_gui,$ref_id,$obj_id,$type);
				$html .= $this->appendPath($ref_id);
				$html .= $this->appendRelevance($obj_id);
				//$html = $this->appendRelevance($obj_id).$html;
				
				$item_html[$ref_id]['html'] = $html;
				$item_html[$ref_id]['type'] = $type;
			}
		}
		
		if(!count($item_html))
		{
			return false;
		}
		$this->newBockTemplate();
		foreach($item_html as $ref_id => $data)
		{
			$this->addStandardRow($ref_id,$data['type'],$data['html']);
		}
		$this->html = $this->tpl->get();
		return true;
	}
	
	/**
	 * Add block for search results
	 * @return
	 */
	protected function newBockTemplate()
	{
		$this->tpl = new ilTemplate("tpl.lucene_search_list_block.html", true, true, "Services/Search");

		// Header
		$this->tpl->setCurrentBlock('container_header_row_image');
		$this->tpl->setVariable('HEADER_IMG',ilUtil::getImagePath('icon_src.gif'));
		$this->tpl->setVariable('HEADER_ALT',$this->lng->txt('search_results'));
		$this->tpl->setVariable('BLOCK_HEADER_CONTENT',$this->lng->txt('search_results'));
		$this->tpl->parseCurrentBlock();
	}
	
	/**
	 * Add object row 
	 * @param int $a_ref_id reference id
	 * @param string $a_obj_type object type
	 * @param string $a_html HTML
	 * @return
	 */
	protected function addStandardRow($a_ref_id,$a_type,$a_html)
	{
		$this->cur_row_type = ($this->cur_row_type == "row_type_1")
			? "row_type_2"
			: "row_type_1";

		$this->tpl->touchBlock($this->cur_row_type);

		// TODO: custom images, lm,sahs images
		$this->tpl->setCurrentBlock('block_row_image');
		$this->tpl->setVariable('ROW_IMG',ilUtil::getImagePath("icon_".$a_type.".gif"));
		$this->tpl->setVariable('ROW_ALT',$this->lng->txt("obj_".$a_type));
		$this->tpl->parseCurrentBlock();

		$this->tpl->setCurrentBlock("container_standard_row");
		$this->tpl->setVariable("BLOCK_ROW_CONTENT", $a_html);
		#$this->tpl->setVariable("BLOCK_ROW_CHECK",ilUtil::formCheckbox(0,'result[]',$a_ref_id));
		#$this->tpl->setVariable("ITEM_ID",$a_ref_id);
		$this->tpl->parseCurrentBlock();
		$this->tpl->touchBlock("container_row");
		
	}
	
	// searcher
	/**
	 * get relevance 
	 * @param
	 * @return
	 */
	protected function getRelevance($a_obj_id)
	{
		return $this->searcher->getResult()->getRelevance($a_obj_id);
	}
	
	/**
	 * 
	 * @param
	 * @return
	 */
	protected function lookupTitle($a_obj_id,$a_sub_id)
	{
		if(strlen($title = $this->searcher->getHighlighter()->getTitle($a_obj_id,$a_sub_id)))
		{
			return $title;
		}
		return ilObject::_lookupTitle($a_obj_id);
	}
	
	/**
	 * 
	 * @param
	 * @return
	 */
	protected function lookupDescription($a_obj_id,$a_sub_id)
	{
		if(strlen($title = $this->searcher->getHighlighter()->getDescription($a_obj_id,$a_sub_id)))
		{
			return $title;
		}
		return ilObject::_lookupDescription($a_obj_id);
	}
	
	/**
	 * get content 
	 * @param
	 * @return
	 */
	protected function lookupContent($a_obj_id,$a_sub_id)
	{
		return $this->searcher->getHighlighter()->getContent($a_obj_id,$a_sub_id);
	}
	
	
	/**
	 * Append path  
	 * @return
	 */
	protected function appendPath($a_ref_id)
	{
		include_once './Services/Tree/classes/class.ilPathGUI.php';
		$path_gui = new ilPathGUI();
		$path_gui->enableTextOnly(false);
		$path_gui->setUseImages(false);
		
		$tpl = new ilTemplate('tpl.lucene_path.html',true,true,'Services/Search');
		$tpl->setVariable('PATH_ITEM',$path_gui->getPath(ROOT_FOLDER_ID,$a_ref_id));
		return $tpl->get();		
	}
	
	/**
	 * Append relevance 
	 * @return
	 */
	protected function appendRelevance($a_obj_id)
	{
		if(!((int) $this->getRelevance($a_obj_id)))
		{
			return '';
		}

		$tpl = new ilTemplate('tpl.lucene_relevance.html',true,true,'Services/Search');
		
		$width1 = (int) $this->getRelevance($a_obj_id);
		$width2 = (int) (100 - $width1);
		
		$tpl->setCurrentBlock('relevance');
		#$tpl->setVariable('TXT_RELEVANCE',$lng->txt('search_relevance'));
		$tpl->setVariable('VAL_REL',sprintf("%.02f %%",$this->getRelevance($a_obj_id)));
		$tpl->setVariable('WIDTH_A',$width1);
		$tpl->setVariable('WIDTH_B',$width2);
		$tpl->setVariable('IMG_A',ilUtil::getImagePath("relevance_blue.gif"));
		$tpl->setVariable('IMG_B',ilUtil::getImagePath("relevance_dark.gif"));
		$tpl->parseCurrentBlock();
		return $tpl->get();
	}
	
	/**
	 * Append subitems 
	 * @return
	 */
	protected function appendSubItems($item_list_gui,$ref_id,$obj_id,$a_type)
	{
		if(!count($this->searcher->getHighlighter()->getSubItemIds($obj_id)))
		{
			return ;
		}
		
		include_once './Services/Search/classes/Lucene/class.ilLuceneSubItemListGUIFactory.php';
		$sub_list = ilLuceneSubItemListGUIFactory::getInstanceByType($a_type);
		$sub_list->setHighlighter($this->searcher->getHighlighter());
		$sub_list->init($item_list_gui,$ref_id,$this->searcher->getHighlighter()->getSubItemIds($obj_id));
		return $sub_list->getHTML();
		
	}
}
?>