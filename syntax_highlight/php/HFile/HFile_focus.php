<?php
$BEAUT_PATH = realpath(".")."/syntax_highlight/php";
if (!isset ($BEAUT_PATH)) return;
require_once("$BEAUT_PATH/Beautifier/HFile.php");
  class HFile_focus extends HFile{
   function HFile_focus(){
     $this->HFile();	
/*************************************/
// Beautifier Highlighting Configuration File 
// FOCUS
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

$this->stringchars       	= array("'");
$this->delimiters        	= array("!", "^", "*", "+", "(", ")", "=", "|", ";", "\"", "'", ",");
$this->escchar           	= "";

// Comment settings

$this->linecommenton     	= array("-*");
$this->blockcommenton    	= array("");
$this->blockcommentoff   	= array("");

// Keywords (keyword mapping to colour number)

$this->keywords          	= array(
			"A" => "1", 
			"ABS" => "1", 
			"ACCESS" => "1", 
			"ACROSS" => "1", 
			"ADD" => "1", 
			"AFTER" => "1", 
			"AGAIN" => "1", 
			"ALL" => "1", 
			"ANALYSE" => "1", 
			"AND" => "1", 
			"ANOVA" => "1", 
			"AS" => "1", 
			"ASNAMES" => "1", 
			"ASQ" => "1", 
			"AVE" => "1", 
			"B" => "1", 
			"BACK" => "1", 
			"BIN" => "1", 
			"BINS" => "1", 
			"BY" => "1", 
			"C1" => "1", 
			"C2" => "1", 
			"C3" => "1", 
			"CASE" => "1", 
			"CDN" => "1", 
			"CHANGE" => "1", 
			"CHECK" => "1", 
			"CLEAR" => "1", 
			"CNT" => "1", 
			"CO" => "1", 
			"COLUMN" => "1", 
			"COLUMNS" => "1", 
			"COLUMN-TOTAL" => "1", 
			"COMBINE" => "1", 
			"COMPILE" => "1", 
			"COMPUTE" => "1", 
			"CONTAINS" => "1", 
			"CONTINUE" => "1", 
			"CORRE" => "1", 
			"COUNT" => "1", 
			"CREATE" => "1", 
			"CRTFORM" => "1", 
			"CT" => "1", 
			"DBA" => "1", 
			"DECODE" => "1", 
			"DECRYPT" => "1", 
			"DEFINE" => "1", 
			"DEL" => "1", 
			"DELETE" => "1", 
			"DIS" => "1", 
			"DISK" => "1", 
			"DISPLAY" => "1", 
			"DMTY" => "1", 
			"DMY" => "1", 
			"DMYY" => "1", 
			"DUPL" => "1", 
			"ECHO" => "1", 
			"EDIT" => "1", 
			"ELSE" => "1", 
			"ENCRYPT" => "1", 
			"END" => "1", 
			"ENDCASE" => "1", 
			"ENDREPEAT" => "1", 
			"EO1" => "1", 
			"EO2" => "1", 
			"EO3" => "1", 
			"EQ" => "1", 
			"EX" => "1", 
			"EXCEEDS" => "1", 
			"EXCLUDES" => "1", 
			"EXEC" => "1", 
			"EXIT" => "1", 
			"EXITREPEAT" => "1", 
			"EXPLAIN" => "1", 
			"EXSMO" => "1", 
			"FACTO" => "1", 
			"FI" => "1", 
			"FILE" => "1", 
			"FIN" => "1", 
			"FINISH" => "1", 
			"FIXFORM" => "1", 
			"FML" => "1", 
			"FOCUS" => "1", 
			"FOOTING" => "1", 
			"FOR" => "1", 
			"FORM" => "1", 
			"FORMAT" => "1", 
			"FREEFORM" => "1", 
			"FROM" => "1", 
			"FST" => "1", 
			"GE" => "1", 
			"GOTO" => "1", 
			"GRAPH" => "1", 
			"GT" => "1", 
			"GTREND" => "1", 
			"HEADING" => "1", 
			"HIST" => "1", 
			"HOLD" => "1", 
			"I" => "1", 
			"IF" => "1", 
			"IN" => "1", 
			"INCLUDE" => "1", 
			"INCLUDES" => "1", 
			"INDEX" => "1", 
			"INIT" => "1", 
			"INPUT" => "1", 
			"INT" => "1", 
			"INTO" => "1", 
			"INVALID" => "1", 
			"IS" => "1", 
			"IS-LESS-THAN" => "1", 
			"IS-MORE-THAN" => "1", 
			"IS-NOT" => "1", 
			"JOIN" => "1", 
			"JUMP" => "1", 
			"LAST" => "1", 
			"LE" => "1", 
			"LET" => "1", 
			"LINES" => "1", 
			"LINK" => "1", 
			"LIST" => "1", 
			"LOC" => "1", 
			"LOCATE" => "1", 
			"LOCATION" => "1", 
			"LOG" => "1", 
			"LST" => "1", 
			"LT" => "1", 
			"MAINTAIN" => "1", 
			"MARK" => "1", 
			"MATCH" => "1", 
			"MATCH/NOMATCH" => "1", 
			"MAX" => "1", 
			"MDISC" => "1", 
			"MDY" => "1", 
			"MDYY" => "1", 
			"MIN" => "1", 
			"MISSING" => "1", 
			"MODIFY" => "1", 
			"MOVE" => "1", 
			"MSG" => "1", 
			"MT" => "1", 
			"MTDY" => "1", 
			"MULTR" => "1", 
			"N" => "1", 
			"NE" => "1", 
			"NEW" => "1", 
			"NEW-OR-OLD" => "1", 
			"NEW-NOR-OLD" => "1", 
			"NEW-NOT-OLD" => "1", 
			"NEXT" => "1", 
			"NOMATCH" => "1", 
			"NOPRINT" => "1", 
			"NOR" => "1", 
			"NOT" => "1", 
			"NOTOTAL" => "1", 
			"OFF" => "1", 
			"OFFLINE" => "1", 
			"OLD" => "1", 
			"OLD-OR-NEW" => "1", 
			"OLD-NOT-NEW" => "1", 
			"OLD-NOR-NEW" => "1", 
			"OM" => "1", 
			"OMITS" => "1", 
			"ON" => "1", 
			"ONLINE" => "1", 
			"OR" => "1", 
			"OVER" => "1", 
			"PAGE" => "1", 
			"PAGE-BREAK" => "1", 
			"PANEL" => "1", 
			"PASS" => "1", 
			"PAUSE" => "1", 
			"PCT" => "1", 
			"PERFORM" => "1", 
			"PICKUP" => "1", 
			"PIE" => "1", 
			"POLRG" => "1", 
			"POST" => "1", 
			"PRINT" => "1", 
			"PROMPT" => "1", 
			"QUIT" => "1", 
			"RANKED" => "1", 
			"READLIMIT" => "1", 
			"REBUILD" => "1", 
			"RECAP" => "1", 
			"RECOMPUTE" => "1", 
			"RECORDLIMIT" => "1", 
			"RECTYP" => "1", 
			"RECTYPE" => "1", 
			"REORG" => "1", 
			"REPEAT" => "1", 
			"REPLACE" => "1", 
			"REPLOT" => "1", 
			"RESTRICT" => "1", 
			"RETYPE" => "1", 
			"ROW" => "1", 
			"ROWS" => "1", 
			"ROW-TOTAL" => "1", 
			"RPCT" => "1", 
			"RUN" => "1", 
			"SAVB" => "1", 
			"SAVE" => "1", 
			"SCAN" => "1", 
			"SEG" => "1", 
			"SEGMENT" => "1", 
			"SEGNAME" => "1", 
			"SET" => "1", 
			"SHOW" => "1", 
			"SKIP-LINE" => "1", 
			"SQRT" => "1", 
			"ST" => "1", 
			"STATSET" => "1", 
			"STOR" => "1", 
			"STORE" => "1", 
			"SUBFOOT" => "1", 
			"SUBHEAD" => "1", 
			"SUBTOTAL" => "1", 
			"SUB-TOTAL" => "1", 
			"SUM" => "1", 
			"SUMMARIZE" => "1", 
			"SUPPRINT" => "1", 
			"TABLE" => "1", 
			"TABLEF" => "1", 
			"TED" => "1", 
			"TEMP" => "1", 
			"THEN" => "1", 
			"TLOCATE" => "1", 
			"TO" => "1", 
			"TOP" => "1", 
			"TOT" => "1", 
			"TOTAL" => "1", 
			"TRACE" => "1", 
			"TYPE" => "1", 
			"UNDERLINE" => "1", 
			"UP" => "1", 
			"UPDATE" => "1", 
			"USAGE" => "1", 
			"USE" => "1", 
			"USER" => "1", 
			"VALIDATE" => "1", 
			"WITH" => "1", 
			"WITHIN" => "1", 
			"WRITE" => "1", 
			"X1" => "1", 
			"X2" => "1", 
			"X3" => "1", 
			"XFER" => "1", 
			"Y" => "1", 
			"YMD" => "1", 
			"YMTD" => "1", 
			"YYMD" => "1", 
			"&ACCEPTS" => "2", 
			"&BASEIO" => "2", 
			"&CHNGD" => "2", 
			"&CURSOR" => "2", 
			"&DATE" => "2", 
			"&ECHO" => "2", 
			"&FOCCPU" => "2", 
			"&FOCDISORG" => "2", 
			"&FOCERRNUM" => "2", 
			"&FOCEXTTRM" => "2", 
			"&FOCFIELDNAME" => "2", 
			"&FOCFOCEXEC" => "2", 
			"&FOCINCLUDE" => "2", 
			"&FOCMODE" => "2", 
			"&FOCPRINT" => "2", 
			"&FOCPUTLVL" => "2", 
			"&FOCQUALCHAR" => "2", 
			"&FOCREL" => "2", 
			"&FOCSBORDER" => "2", 
			"&FOCSYSTYP" => "2", 
			"&FOCTMPDSK" => "2", 
			"&FOCTRMSD" => "2", 
			"&FOCTRMSW" => "2", 
			"&FORTRMTYP" => "2", 
			"&FOCTTIME" => "2", 
			"&FOCVTIME" => "2", 
			"&FORMAT" => "2", 
			"&HIPERFOCUS" => "2", 
			"&INPUT" => "2", 
			"&INVALID" => "2", 
			"&IORETURN" => "2", 
			"&LINES" => "2", 
			"&MDY" => "2", 
			"&MDYY" => "2", 
			"&NOMATCH" => "2", 
			"&PFKEY" => "2", 
			"&QUIT" => "2", 
			"&READS" => "2", 
			"&RECORDS" => "2", 
			"&REJECTS" => "2", 
			"&RETCODE" => "2", 
			"&STACK" => "2", 
			"&TOD" => "2", 
			"&TRANS" => "2", 
			"&WINDOWNAME" => "2", 
			"&WINDOWVALUE" => "2", 
			"&YMD" => "2", 
			"&YYMD" => "2", 
			"-CMS" => "3", 
			"-CLOSE" => "3", 
			"-CRTCLEAR" => "3", 
			"-CRTFORM" => "3", 
			"-DEFAULTS" => "3", 
			"-EXIT" => "3", 
			"-GOTO" => "3", 
			"-IF" => "3", 
			"-INCLUDE" => "3", 
			"-MVS" => "3", 
			"-PASS" => "3", 
			"-PROMPT" => "3", 
			"-QUIT" => "3", 
			"-READ" => "3", 
			"-REPEAT" => "3", 
			"-RUN" => "3", 
			"-SET" => "3", 
			"-TSO" => "3", 
			"-TYPE" => "3", 
			"-WINDOW" => "3", 
			"-WRITE" => "3", 
			"-?" => "3", 
			"TSO" => "3", 
			"MVS" => "3", 
			"CMS" => "3", 
			"**" => "4", 
			"/A" => "4", 
			"/D" => "4", 
			"/F" => "4", 
			"/I" => "4", 
			"//" => "4", 
			"/DMY" => "4", 
			"/DMYY" => "4", 
			"/MDY" => "4", 
			"/MDYY" => "4", 
			"/YMD" => "4", 
			"/YYMD" => "4");

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
