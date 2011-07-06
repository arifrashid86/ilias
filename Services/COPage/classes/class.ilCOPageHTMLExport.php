<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * HTML export class for pages
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id: $
 * @ingroup ServicesCOPage
 */
class ilCOPageHTMLExport
{
	private $mobs = array();
	private $files = array();
	private $exp_dir = "";
	private $content_style_id = 0;

	/**
	 * Initialisation
	 */
	function __construct($a_exp_dir)
	{
		$this->exp_dir = $a_exp_dir;
		$this->mobs_dir = $a_exp_dir."/mobs";
		$this->files_dir = $a_exp_dir."/files";
		$this->tex_dir = $a_exp_dir."/teximg";
		$this->content_style_dir = $a_exp_dir."/content_style";
		$this->content_style_img_dir = $a_exp_dir."/content_style/images";
		
		$this->services_dir = $a_exp_dir."/Services";
		$this->media_service_dir = $this->services_dir."/MediaObjects";
		$this->flv_dir = $this->media_service_dir."/flash_flv_player";
		$this->mp3_dir = $this->media_service_dir."/flash_mp3_player";

		$this->js_dir = $a_exp_dir.'/js';
		$this->js_yahoo_dir = $a_exp_dir.'/js/yahoo';
		$this->css_dir = $a_exp_dir.'/css';

		$GLOBALS["teximgcnt"] = 0;
	}

	/**
	 * Set content style id
	 *
	 * @param int $a_val content style id	
	 */
	function setContentStyleId($a_val)
	{
		$this->content_style_id = $a_val;
	}
	
	/**
	 * Get content style id
	 *
	 * @return int content style id
	 */
	function getContentStyleId()
	{
		return $this->content_style_id;
	}
	
	/**
	 * Create directories
	 *
	 * @param
	 * @return
	 */
	function createDirectories()
	{
		ilUtil::makeDir($this->mobs_dir);
		ilUtil::makeDir($this->files_dir);
		ilUtil::makeDir($this->tex_dir);
		ilUtil::makeDir($this->content_style_dir);
		ilUtil::makeDir($this->content_style_img_dir);
		ilUtil::makeDir($this->services_dir);
		ilUtil::makeDir($this->media_service_dir);
		ilUtil::makeDir($this->flv_dir);
		ilUtil::makeDir($this->mp3_dir);
		
		ilUtil::makeDir($this->js_dir);
		ilUtil::makeDir($this->js_yahoo_dir);
		ilUtil::makeDir($this->css_dir);
	}
	
	/**
	 * Export content style
	 *
	 * @param
	 * @return
	 */
	function exportStyles()
	{
		// export content style sheet
		if ($this->getContentStyleId() < 1)
		{
			$cont_stylesheet = "./Services/COPage/css/content.css";

			$css = fread(fopen($cont_stylesheet,'r'),filesize($cont_stylesheet));
			preg_match_all("/url\(([^\)]*)\)/",$css,$files);
			foreach (array_unique($files[1]) as $fileref)
			{
				if (is_file(str_replace("..", ".", $fileref)))
				{
					copy(str_replace("..", ".", $fileref), $this->content_style_img_dir."/".basename($fileref));
				}
				$css = str_replace($fileref, "images/".basename($fileref),$css);
			}
			fwrite(fopen($this->content_style_dir."/content.css",'w'),$css);
		}
		else
		{
			$style = new ilObjStyleSheet($this->getContentStyleId());
			$style->writeCSSFile($this->content_style_dir."/content.css", "images");
			$style->copyImagesToDir($this->content_style_img_dir);
		}
		
		// export syntax highlighting style
		$syn_stylesheet = ilObjStyleSheet::getSyntaxStylePath();
		copy($syn_stylesheet, $this->exp_dir."/syntaxhighlight.css");
	}
	
	/**
	 * Export support scripts
	 *
	 * @param
	 * @return
	 */
	function exportSupportScripts()
	{
		// export flv/mp3 player
		copy("./Services/MediaObjects/flash_flv_player/flvplayer.swf",
			$this->flv_dir."/flvplayer.swf");
		copy("./Services/MediaObjects/flash_mp3_player/mp3player.swf",
			$this->mp3_dir."/mp3player.swf");
		
		// basic js
		copy('./Services/JavaScript/js/Basic.js', $this->js_dir.'/Basic.js');
		
		// yui stuff we use
		include_once("./Services/YUI/classes/class.ilYuiUtil.php");
		copy(ilYuiUtil::getLocalPath('yahoo/yahoo-min.js'),
			$this->js_yahoo_dir.'/yahoo-min.js');
		copy(ilYuiUtil::getLocalPath('yahoo-dom-event/yahoo-dom-event.js'),
			$this->js_yahoo_dir.'/yahoo-dom-event.js');
		copy(ilYuiUtil::getLocalPath('animation/animation-min.js'),
			$this->js_yahoo_dir.'/animation-min.js');
		
		// accordion
		copy('./Services/Accordion/js/accordion.js',
			$this->js_dir.'/accordion.js');
		copy('./Services/Accordion/css/accordion.css',
			$this->css_dir.'/accordion.css');
		
		// page presentation js
		copy('./Services/COPage/js/ilCOPagePres.js',
			$this->js_dir.'/ilCOPagePres.js');

	}

	/**
	 * Get prepared main template
	 *
	 * @param
	 * @return
	 */
	function getPreparedMainTemplate()
	{
		global $ilUser;
		
		// template workaround: reset of template
		$tpl = new ilTemplate("tpl.main.html", true, true);
		
		// scripts needed
		$scripts = array("./js/yahoo/yahoo-min.js", "./js/yahoo/yahoo-dom-event.js",
			"./js/yahoo/container_core-min.js", "./js/yahoo/animation-min.js",
			"./js/Basic.js",
			"./js/ilOverlay.js", "./js/accordion.js", "./js/ilCOPagePres.js");
		foreach ($scripts as $script)
		{
			$tpl->setCurrentBlock("js_file");
			$tpl->setVariable("JS_FILE", $script);
			$tpl->parseCurrentBlock();
		}

		// css files needed
		$css_files = array("./css/accordion.css");
		foreach ($css_files as $css)
		{
			$tpl->setCurrentBlock("css_file");
			$tpl->setVariable("CSS_FILE", $css);
			$tpl->parseCurrentBlock();
		}

		$tpl->setCurrentBlock("ContentStyle");
		$tpl->setVariable("LOCATION_CONTENT_STYLESHEET", "./content_style/content.css");
		$tpl->parseCurrentBlock();
		$style_name = $ilUser->prefs["style"].".css";
		$tpl->setVariable("LOCATION_STYLESHEET","./style/".$style_name);
		
		return $tpl;
	}
	
	/**
	 * Collect page elements (that need to be exported separately)
	 *
	 * @param string $a_pg_type page type
	 * @param int $a_pg_id page id
	 */
	function collectPageElements($a_type, $a_id)
	{
		// collect media objects
		$pg_mobs = ilObjMediaObject::_getMobsOfObject($a_type, $a_id);
		foreach($pg_mobs as $pg_mob)
		{
			$this->mobs[$pg_mob] = $pg_mob;
		}
		
		// collect all files
		include_once("./Modules/File/classes/class.ilObjFile.php");
		$files = ilObjFile::_getFilesOfObject($a_type, $a_id);
		foreach($files as $f)
		{
			$this->files[$f] = $f;
		}

		// get all snippets of page
		/*
		$pcs = ilPageContentUsage::getUsagesOfPage($page["id"], $this->getType().":pg");
		foreach ($pcs as $pc)
		{
			if ($pc["type"] == "incl")
			{
				$incl_mobs = ilObjMediaObject::_getMobsOfObject("mep:pg", $pc["id"]);
				foreach($incl_mobs as $incl_mob)
				{
					$mobs[$incl_mob] = $incl_mob;
				}
			}
		}*/

		// get all internal links of page
/*
				$pg_links = ilInternalLink::_getTargetsOfSource($this->getType().":pg", $page["id"]);
				$int_links = array_merge($int_links, $pg_links);

				// get all files of page
				include_once("./Modules/File/classes/class.ilObjFile.php");
				$pg_files = ilObjFile::_getFilesOfObject($this->getType().":pg", $page["id"]);
				$this->offline_files = array_merge($this->offline_files, $pg_files);
*/

	}
	
	/**
	 * Export page elements
	 *
	 * @param
	 * @return
	 */
	function exportPageElements()
	{
		// export all media objects
		$linked_mobs = array();
		foreach ($this->mobs as $mob)
		{
			if (ilObject::_exists($mob) && ilObject::_lookupType($mob) == "mob")
			{
				$this->exportHTMLMOB($mob, $linked_mobs);
			}
		}
		$linked_mobs2 = array();				// mobs linked in link areas
		foreach ($linked_mobs as $mob)
		{
			if (ilObject::_exists($mob))
			{
				$this->exportHTMLMOB($mob, $linked_mobs2);
			}
		}

		// export all file objects
		foreach ($this->files as $file)
		{
			$this->exportHTMLFile($file);
		}
	}
	
	/**
	 * Export media object to html
	 */
	function exportHTMLMOB($a_mob_id, &$a_linked_mobs)
	{
		global $tpl;

		$source_dir = ilUtil::getWebspaceDir()."/mobs/mm_".$a_mob_id;
		if (@is_dir($source_dir))
		{
			ilUtil::makeDir($this->mobs_dir."/mm_".$a_mob_id);
			ilUtil::rCopy($source_dir, $this->mobs_dir."/mm_".$a_mob_id);
		}

/*		$tpl = new ilTemplate("tpl.main.html", true, true);
		$tpl->addBlockFile("CONTENT", "content", "tpl.adm_content.html");
//		$_GET["obj_type"]  = "MediaObject";
//		$_GET["mob_id"]  = $a_mob_id;
//		$_GET["cmd"] = "";
$content =& $a_lm_gui->media();
		$file = $this->export_dir."/media_".$a_mob_id.".html";

		// open file
		if (!($fp = @fopen($file,"w+")))
		{
			die ("<b>Error</b>: Could not open \"".$file."\" for writing".
				" in <b>".__FILE__."</b> on line <b>".__LINE__."</b><br />");
		}
		chmod($file, 0770);
		fwrite($fp, $content);
		fclose($fp);*/

		// fullscreen
		include_once("./Services/MediaObjects/classes/class.ilObjMediaObject.php");
		$mob_obj = new ilObjMediaObject($a_mob_id);
		if ($mob_obj->hasFullscreenItem())
		{
			$tpl = new ilTemplate("tpl.main.html", true, true);
			$tpl->addBlockFile("CONTENT", "content", "tpl.adm_content.html");
			//$_GET["obj_type"]  = "";
			//$_GET["mob_id"]  = $a_mob_id;
			//$_GET["cmd"] = "fullscreen";
//			$content =& $a_lm_gui->fullscreen();
			$file = $this->exp_dir."/fullscreen_".$a_mob_id.".html";

			// open file
			if (!($fp = @fopen($file,"w+")))
			{
				die ("<b>Error</b>: Could not open \"".$file."\" for writing".
					" in <b>".__FILE__."</b> on line <b>".__LINE__."</b><br />");
			}
			chmod($file, 0770);
			fwrite($fp, $content);
			fclose($fp);
		}
		$linked_mobs = $mob_obj->getLinkedMediaObjects();
		$a_linked_mobs = array_merge($a_linked_mobs, $linked_mobs);
	}

	/**
	 * Export file object
	 */
	function exportHTMLFile($a_file_id)
	{
		$file_dir = $this->files_dir."/file_".$a_file_id;
		ilUtil::makeDir($file_dir);
		include_once("./Modules/File/classes/class.ilObjFile.php");
		$file_obj = new ilObjFile($a_file_id, false);
		$source_file = $file_obj->getDirectory($file_obj->getVersion())."/".$file_obj->getFileName();
		if (!is_file($source_file))
		{
			$source_file = $file_obj->getDirectory()."/".$file_obj->getFileName();
		}
		if (is_file($source_file))
		{
			copy($source_file, $file_dir."/".$file_obj->getFileName());
		}
	}

}

?>