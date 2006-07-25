<?php
$BEAUT_PATH = realpath(".")."/syntax_highlight/php";
if (!isset ($BEAUT_PATH)) return;
require_once("$BEAUT_PATH/Beautifier/HFile.php");
  class HFile_xhtmlbasic extends HFile{
   function HFile_xhtmlbasic(){
     $this->HFile();	
/*************************************/
// Beautifier Highlighting Configuration File 
// XHTML BASIC (Mobile Applications)
/*************************************/
// Flags

$this->nocase            	= "0";
$this->notrim            	= "0";
$this->perl              	= "0";

// Colours

$this->colours        	= array("blue", "purple", "gray", "brown");
$this->quotecolour       	= "blue";
$this->blockcommentcolour	= "green";
$this->linecommentcolour 	= "green";

// Indent Strings

$this->indent            	= array();
$this->unindent          	= array();

// String characters and delimiters

$this->stringchars       	= array("\"", "'");
$this->delimiters        	= array("~", "@", "$", "%", "^", "&", "*", "(", ")", "+", "=", "|", "\\", "{", "}", ";", "\"", "'", "<", ">", " ", ",", "	", ".");
$this->escchar           	= "";

// Comment settings

$this->linecommenton     	= array("");
$this->blockcommenton    	= array("<!--");
$this->blockcommentoff   	= array("-->");

// Keywords (keyword mapping to colour number)

$this->keywords          	= array(
			"<a>" => "1", 
			"<a" => "1", 
			"</a>" => "1", 
			"<abbr>" => "1", 
			"<abbr" => "1", 
			"</abbr>" => "1", 
			"<acronym>" => "1", 
			"<acronym" => "1", 
			"</acronym>" => "1", 
			"<address>" => "1", 
			"<address" => "1", 
			"</address>" => "1", 
			"<base" => "1", 
			"<blockquote>" => "1", 
			"<blockquote" => "1", 
			"</blockquote>" => "1", 
			"<body>" => "1", 
			"<body" => "1", 
			"</body>" => "1", 
			"<br" => "1", 
			"<caption>" => "1", 
			"<caption" => "1", 
			"</caption>" => "1", 
			"<cite>" => "1", 
			"<cite" => "1", 
			"</cite>" => "1", 
			"<code>" => "1", 
			"<code" => "1", 
			"</code>" => "1", 
			"<dd>" => "1", 
			"<dd" => "1", 
			"</dd>" => "1", 
			"<dfn>" => "1", 
			"<dfn" => "1", 
			"</dfn>" => "1", 
			"<div>" => "1", 
			"<div" => "1", 
			"</div>" => "1", 
			"<dl>" => "1", 
			"<dl" => "1", 
			"</dl>" => "1", 
			"<dt>" => "1", 
			"<dt" => "1", 
			"</dt>" => "1", 
			"<em>" => "1", 
			"<em" => "1", 
			"</em>" => "1", 
			"<form>" => "1", 
			"<form" => "1", 
			"</form>" => "1", 
			"<h1>" => "1", 
			"<h1" => "1", 
			"</h1>" => "1", 
			"<h2>" => "1", 
			"<h2" => "1", 
			"</h2>" => "1", 
			"<h3>" => "1", 
			"<h3" => "1", 
			"</h3>" => "1", 
			"<h4>" => "1", 
			"<h4" => "1", 
			"</h4>" => "1", 
			"<h5>" => "1", 
			"<h5" => "1", 
			"</h5>" => "1", 
			"<h6>" => "1", 
			"<h6" => "1", 
			"</h6>" => "1", 
			"<head>" => "1", 
			"<head" => "1", 
			"</head>" => "1", 
			"<html>" => "1", 
			"<html" => "1", 
			"</html>" => "1", 
			"<img" => "1", 
			"<input" => "1", 
			"<kbd>" => "1", 
			"<kbd" => "1", 
			"</kbd>" => "1", 
			"<label>" => "1", 
			"<label" => "1", 
			"</label>" => "1", 
			"<li>" => "1", 
			"<li" => "1", 
			"</li>" => "1", 
			"<link" => "1", 
			"<meta" => "1", 
			"<object>" => "1", 
			"<object" => "1", 
			"</object>" => "1", 
			"<ol>" => "1", 
			"<ol" => "1", 
			"</ol>" => "1", 
			"<option>" => "1", 
			"<option" => "1", 
			"</option>" => "1", 
			"<p>" => "1", 
			"<p" => "1", 
			"</p>" => "1", 
			"<param" => "1", 
			"<pre>" => "1", 
			"<pre" => "1", 
			"</pre>" => "1", 
			"<q>" => "1", 
			"<q" => "1", 
			"</q>" => "1", 
			"<samp>" => "1", 
			"<samp" => "1", 
			"</samp>" => "1", 
			"<select>" => "1", 
			"<select" => "1", 
			"</select>" => "1", 
			"<span>" => "1", 
			"<span" => "1", 
			"</span>" => "1", 
			"<strong>" => "1", 
			"<strong" => "1", 
			"</strong>" => "1", 
			"<table>" => "1", 
			"<table" => "1", 
			"</table>" => "1", 
			"<td>" => "1", 
			"<td" => "1", 
			"</td>" => "1", 
			"<textarea>" => "1", 
			"<textarea" => "1", 
			"</textarea>" => "1", 
			"<th>" => "1", 
			"<th" => "1", 
			"</th>" => "1", 
			"<title>" => "1", 
			"<title" => "1", 
			"</title>" => "1", 
			"<tr>" => "1", 
			"<tr" => "1", 
			"</tr>" => "1", 
			"<ul>" => "1", 
			"<ul" => "1", 
			"</ul>" => "1", 
			"<var>" => "1", 
			"<var" => "1", 
			"</var>" => "1", 
			"abbr=" => "2", 
			"accesskey=" => "2", 
			"action=" => "2", 
			"align=" => "2", 
			"alt=" => "2", 
			"archive=" => "2", 
			"axis=" => "2", 
			"charset=" => "2", 
			"checked=" => "2", 
			"cite=" => "2", 
			"class=" => "2", 
			"classid=" => "2", 
			"codebase=" => "2", 
			"codetype=" => "2", 
			"cols=" => "2", 
			"colspan=" => "2", 
			"content=" => "2", 
			"data=" => "2", 
			"declare=" => "2", 
			"enctype=" => "2", 
			"for=" => "2", 
			"headers=" => "2", 
			"height=" => "2", 
			"href=" => "2", 
			"hreflang=" => "2", 
			"http-equiv=" => "2", 
			"id=" => "2", 
			"longdesc=" => "2", 
			"maxlength=" => "2", 
			"media=" => "2", 
			"method=" => "2", 
			"multiple=" => "2", 
			"name=" => "2", 
			"onclick=" => "2", 
			"ondblclick=" => "2", 
			"onkeydown=" => "2", 
			"onkeypress=" => "2", 
			"onkeyup=" => "2", 
			"onmousedown=" => "2", 
			"onmousemove=" => "2", 
			"onmouseout=" => "2", 
			"onmouseover=" => "2", 
			"onmouseup=" => "2", 
			"rel=" => "2", 
			"rev=" => "2", 
			"rows=" => "2", 
			"rowspan=" => "2", 
			"scheme=" => "2", 
			"scope=" => "2", 
			"selected=" => "2", 
			"size=" => "2", 
			"src=" => "2", 
			"standby=" => "2", 
			"style=" => "2", 
			"summary=" => "2", 
			"tabindex=" => "2", 
			"title=" => "2", 
			"type=" => "2", 
			"valign=" => "2", 
			"value=" => "2", 
			"valuetype=" => "2", 
			"width=" => "2", 
			"xml:lang=" => "2", 
			"xml:space=" => "2", 
			"&;" => "3", 
			"<!DOCTYPE" => "4", 
			"<![CDATA[" => "4", 
			"<?phpxml" => "4", 
			"<?phpxml-stylesheet" => "4", 
			"?>" => "4", 
			"DTD" => "4", 
			"PUBLIC" => "4", 
			"SCHEMA" => "4", 
			"]]>" => "4");

// Special extensions

// Each category can specify a PHP function that returns an altered
// version of the keyword.
        
        

$this->linkscripts    	= array(
			"1" => "donothing", 
			"2" => "donothing", 
			"3" => "donothing", 
			"4" => "donothing");
}


function donothing($keywordin)
{
	return $keywordin;
}

}?>
