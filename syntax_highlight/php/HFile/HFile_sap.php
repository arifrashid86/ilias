<?php
$BEAUT_PATH = realpath(".")."/syntax_highlight/php";
if (!isset ($BEAUT_PATH)) return;
require_once("$BEAUT_PATH/Beautifier/HFile.php");
  class HFile_sap extends HFile{
   function HFile_sap(){
     $this->HFile();	
/*************************************/
// Beautifier Highlighting Configuration File 
// SAP - ABAP/4
/*************************************/
// Flags

$this->nocase            	= "0";
$this->notrim            	= "0";
$this->perl              	= "0";

// Colours

$this->colours        	= array("blue", "purple");
$this->quotecolour       	= "blue";
$this->blockcommentcolour	= "green";
$this->linecommentcolour 	= "green";

// Indent Strings

$this->indent            	= array("FORM", "CONSTANTS:", "DATA:", "TABLES:", "TYPE-POOLS:", "PARAMETERS:", "FUNCITON", "IF", "ELSE", "ELSEIF", "SELECT", "LOOP", "DO", "WHILE", "CASE", "AT", "ON", "MODULE", "PROVIDE");
$this->unindent          	= array("ENDFORM", "ENDFUNCTION", "ENDIF", "ENDSELECT", "ENDLOOP", "ENDDO", "ENDWHILE", "ENDCASE", "ENDAT", "ENDON", "ENDMODULE", "ENDPROVIDE");

// String characters and delimiters

$this->stringchars       	= array();
$this->delimiters        	= array("~", "!", "@", "%", "^", "&", "*", "(", ")", "+", "=", "|", "\\", "/", "{", "}", "[", "]", ";", "\"", "'", "<", ">", " ", ".", ",", " ", " ", " ", " ", " ", " ", " ", " ", "?");
$this->escchar           	= "";

// Comment settings

$this->linecommenton     	= array("");
$this->blockcommenton    	= array("");
$this->blockcommentoff   	= array("");

// Keywords (keyword mapping to colour number)

$this->keywords          	= array(
			"ABS" => "1", 
			"ACOS" => "1", 
			"ADD" => "1", 
			"ADD-CORRESPONDING" => "1", 
			"ADJACENT" => "1", 
			"ALL" => "1", 
			"AND" => "1", 
			"ANALYZER" => "1", 
			"APPEND" => "1", 
			"ASCENDING" => "1", 
			"ASIN" => "1", 
			"AT" => "1", 
			"ATAN" => "1", 
			"AUTHORITY-CHECK" => "1", 
			"AVG" => "1", 
			"BACK" => "1", 
			"BEGIN" => "1", 
			"BETWEEN" => "1", 
			"BINARY" => "1", 
			"BLANK" => "1", 
			"BLOCK" => "1", 
			"BREAK-POINT" => "1", 
			"BY" => "1", 
			"CA" => "1", 
			"CALL" => "1", 
			"CASE" => "1", 
			"CATCH" => "1", 
			"CEIL" => "1", 
			"CENTERED" => "1", 
			"CHANGE" => "1", 
			"CHECK" => "1", 
			"CHECKBOX" => "1", 
			"CLEAR" => "1", 
			"CLEAR:" => "1", 
			"CLOSE" => "1", 
			"CN" => "1", 
			"CNT" => "1", 
			"CO" => "1", 
			"COLLECT" => "1", 
			"COLOR" => "1", 
			"COMMIT" => "1", 
			"COMMUNICATION" => "1", 
			"COMPUTE" => "1", 
			"CONCATENATE" => "1", 
			"CONDENSE" => "1", 
			"CONSTANTS" => "1", 
			"CONSTANTS:" => "1", 
			"CONTINUE" => "1", 
			"CONTROL" => "1", 
			"CONTROLS" => "1", 
			"CONVERT" => "1", 
			"COPY" => "1", 
			"COS" => "1", 
			"COSH" => "1", 
			"COUNT" => "1", 
			"COUNTRY" => "1", 
			"CP" => "1", 
			"CURRENCY" => "1", 
			"CUSTOMER-FUNCTION" => "1", 
			"DATA" => "1", 
			"DATA:" => "1", 
			"DATASET" => "1", 
			"DECIMALS" => "1", 
			"DEFINE" => "1", 
			"DEFAULT" => "1", 
			"DELETE" => "1", 
			"DESCENDING" => "1", 
			"DESCRIBE" => "1", 
			"DIALOG" => "1", 
			"DISTINCT" => "1", 
			"DIV" => "1", 
			"DIVIDE" => "1", 
			"DIVIDE-CORRESPONDING" => "1", 
			"DO" => "1", 
			"DUPLICATES" => "1", 
			"EDITOR-CALL" => "1", 
			"ELSE" => "1", 
			"ELSEIF" => "1", 
			"END" => "1", 
			"ENDCATCH" => "1", 
			"END-OF-DEFINITION" => "1", 
			"END-OF-PAGE" => "1", 
			"END-OF-SELECTION" => "1", 
			"ENDAT" => "1", 
			"ENDCASE" => "1", 
			"ENDDO" => "1", 
			"ENDFORM" => "1", 
			"ENDFUNCTION" => "1", 
			"ENDIF" => "1", 
			"ENDLOOP" => "1", 
			"ENDMODULE" => "1", 
			"ENDON" => "1", 
			"ENDPROVIDE" => "1", 
			"ENDSELECT" => "1", 
			"ENDWHILE" => "1", 
			"ENTRIES" => "1", 
			"EQ" => "1", 
			"EXCEPTIONS" => "1", 
			"EXEC" => "1", 
			"EXIT" => "1", 
			"EXP" => "1", 
			"EXPONENT" => "1", 
			"EXPORT" => "1", 
			"EXPORTING" => "1", 
			"EXTENDED" => "1", 
			"EXTRACT" => "1", 
			"FETCH" => "1", 
			"FIELD-GROUP" => "1", 
			"FIELD-SYMBOLS" => "1", 
			"FIELD-SYMBOLS:" => "1", 
			"FLOOR" => "1", 
			"FOR" => "1", 
			"FORM" => "1", 
			"FORMAT" => "1", 
			"FRAC" => "1", 
			"FRAME" => "1", 
			"FREE" => "1", 
			"FROM" => "1", 
			"FUNCTION" => "1", 
			"FUNCTION-POOL" => "1", 
			"GE" => "1", 
			"GENERATE" => "1", 
			"GET" => "1", 
			"GT" => "1", 
			"HIDE" => "1", 
			"HOTSPOT" => "1", 
			"HEADER" => "1", 
			"HEADING." => "1", 
			"HEADING" => "1", 
			"ICON" => "1", 
			"IF" => "1", 
			"IMPORT" => "1", 
			"IMPORTING" => "1", 
			"IN" => "1", 
			"INCLUDE" => "1", 
			"INDEX" => "1", 
			"INFOTYPES" => "1", 
			"INITIAL" => "1", 
			"INITIALIZATION" => "1", 
			"INPUT" => "1", 
			"INSERT" => "1", 
			"INTO" => "1", 
			"INTENSIFIED" => "1", 
			"INVERSE" => "1", 
			"IS" => "1", 
			"KEY" => "1", 
			"LANGUAGE" => "1", 
			"LE" => "1", 
			"LEAVE" => "1", 
			"LEFT-JUSTIFIED" => "1", 
			"LIKE" => "1", 
			"LINE" => "1", 
			"LINE-COUNT" => "1", 
			"LINE-SIZE" => "1", 
			"LINES" => "1", 
			"LIST-PROCESSING" => "1", 
			"LOAD" => "1", 
			"LOCAL" => "1", 
			"LOCALE" => "1", 
			"LOG" => "1", 
			"LOG10" => "1", 
			"LOOP" => "1", 
			"LT" => "1", 
			"MESSAGE-ID" => "1", 
			"MESSAGE" => "1", 
			"M" => "1", 
			"MARGIN" => "1", 
			"MAX" => "1", 
			"MIN" => "1", 
			"MOD" => "1", 
			"MODE" => "1", 
			"MODIFY" => "1", 
			"MODULE" => "1", 
			"MOVE" => "1", 
			"MOVE-CORRESPONDING" => "1", 
			"MULTIPLY" => "1", 
			"MULTIPLY-CORRESPONDING" => "1", 
			"NA" => "1", 
			"NE" => "1", 
			"NEW-LINE" => "1", 
			"NEW-PAGE" => "1", 
			"NO-GAP" => "1", 
			"NO-HEADING" => "1", 
			"NO-SCROLLING" => "1", 
			"NO-SIGN" => "1", 
			"NO-TITLE" => "1", 
			"NO-ZERO" => "1", 
			"NOT" => "1", 
			"NP" => "1", 
			"NS" => "1", 
			"NO" => "1", 
			"STANDARD" => "1", 
			"PAGE" => "1", 
			"O" => "1", 
			"OBLIGATORY" => "1", 
			"OCCURS" => "1", 
			"OF" => "1", 
			"OFF" => "1", 
			"ON" => "1", 
			"OPEN" => "1", 
			"OR" => "1", 
			"ORDER" => "1", 
			"OUTPUT" => "1", 
			"OVERLAY" => "1", 
			"PACK" => "1", 
			"PARAMETERS" => "1", 
			"PARAMETERS:" => "1", 
			"PERFORM" => "1", 
			"PF-STATUS" => "1", 
			"POSITION" => "1", 
			"PRINT" => "1", 
			"PRINT-CONTROL" => "1", 
			"PROGRAM" => "1", 
			"PROVIDE" => "1", 
			"PUT" => "1", 
			"RADIOBUTTON" => "1", 
			"RAISE" => "1", 
			"RAISING" => "1", 
			"RANGES" => "1", 
			"READ" => "1", 
			"RECEIVE" => "1", 
			"REFRESH" => "1", 
			"REJECT" => "1", 
			"REPLACE" => "1", 
			"REPORT" => "1", 
			"RESERVE" => "1", 
			"RESET" => "1", 
			"RIGHT-JUSTIFIED" => "1", 
			"ROLLBACK" => "1", 
			"ROUND" => "1", 
			"RUN" => "1", 
			"SCAN" => "1", 
			"SCREEN" => "1", 
			"SCROLL" => "1", 
			"SCROLL-BOUNDARY" => "1", 
			"SEARCH" => "1", 
			"SELECT" => "1", 
			"SELECT-OPTIONS" => "1", 
			"SELECT-OPTIONS:" => "1", 
			"SELECTION-SCREEN" => "1", 
			"SELECTION-SCREEN:" => "1", 
			"SELECTION-TABLE" => "1", 
			"SET" => "1", 
			"SHIFT" => "1", 
			"SIGN" => "1", 
			"SIN" => "1", 
			"SINGLE" => "1", 
			"SINH" => "1", 
			"SKIP" => "1", 
			"SORT" => "1", 
			"SPACE" => "1", 
			"SPLIT" => "1", 
			"SQL" => "1", 
			"SQRT" => "1", 
			"START-OF-SELECTION" => "1", 
			"STATICS" => "1", 
			"STOP" => "1", 
			"STRLEN" => "1", 
			"STRUCTURE" => "1", 
			"SUBMIT" => "1", 
			"SUBSTRACT" => "1", 
			"SUBSTRACT-CORRESPONDING" => "1", 
			"SUM" => "1", 
			"SUPPRESS" => "1", 
			"SYMBOL" => "1", 
			"SYNTAX-CHECK" => "1", 
			"SYNTAX-TRACE" => "1", 
			"SYSTEM-EXCEPTIONS" => "1", 
			"TABLE" => "1", 
			"TABLES" => "1", 
			"TABLES:" => "1", 
			"TAN" => "1", 
			"TANH" => "1", 
			"THEN" => "1", 
			"TIME" => "1", 
			"TITLE" => "1", 
			"TITLEBAR" => "1", 
			"TO" => "1", 
			"TOP-OF-PAGE" => "1", 
			"TRANSACTION" => "1", 
			"TRANSFER" => "1", 
			"TRANSLATE" => "1", 
			"TRUNC" => "1", 
			"TYPE-POOLS" => "1", 
			"TYPE-POOL" => "1", 
			"TYPES" => "1", 
			"TYPE" => "1", 
			"ULINE" => "1", 
			"UNDER" => "1", 
			"UNIT" => "1", 
			"UNPACK" => "1", 
			"UPDATE" => "1", 
			"USER-COMMAND" => "1", 
			"USING" => "1", 
			"USING:" => "1", 
			"VALUE" => "1", 
			"WHEN" => "1", 
			"WHERE" => "1", 
			"WHILE" => "1", 
			"WINDOW" => "1", 
			"WITH" => "1", 
			"WITH-TITLE" => "1", 
			"WORK" => "1", 
			"WRITE" => "1", 
			"WRITE:" => "1", 
			"WRITE-TO" => "1", 
			"Z" => "1", 
			"BDCDATA" => "2", 
			"SY-ABCDE" => "2", 
			"SY-APPLI" => "2", 
			"SY-BATCH" => "2", 
			"SY-BATZD" => "2", 
			"SY-BATZM" => "2", 
			"SY-BATZO" => "2", 
			"SY-BATZS" => "2", 
			"SY-BATZW" => "2", 
			"SY-BINPT" => "2", 
			"SY-BREP4" => "2", 
			"SY-BSPLD" => "2", 
			"SY-CALLD" => "2", 
			"SY-CALLR" => "2", 
			"SY-CCURS" => "2", 
			"SY-CCURT" => "2", 
			"SY-CDATE" => "2", 
			"SY-COLNO" => "2", 
			"SY-CPAGE" => "2", 
			"SY-CPROG" => "2", 
			"SY-CTABL" => "2", 
			"SY-CTYPE" => "2", 
			"SY-CUCOL" => "2", 
			"SY-CUROW" => "2", 
			"SY-DATAR" => "2", 
			"SY-DATLO" => "2", 
			"SY-DATUM" => "2", 
			"SY-DATUT" => "2", 
			"SY-DAYST" => "2", 
			"SY-DBCNT" => "2", 
			"SY-DBNAM" => "2", 
			"SY-DBSYS" => "2", 
			"SY-DCSYS" => "2", 
			"SY-DSNAM" => "2", 
			"SY-DYNGR" => "2", 
			"SY-DYNNR" => "2", 
			"SY-FDAYW" => "2", 
			"SY-FDPOS" => "2", 
			"SY-FMKEY" => "2", 
			"SY-HOST" => "2", 
			"SY-INDEX" => "2", 
			"SY-LANGU" => "2", 
			"SY-LDBPG" => "2", 
			"SY-LILLI" => "2", 
			"SY-LINCT" => "2", 
			"SY-LINNO" => "2", 
			"SY-LINSZ" => "2", 
			"SY-LISEL" => "2", 
			"SY-LISTI" => "2", 
			"SY-LOCDB" => "2", 
			"SY-LOCOP" => "2", 
			"SY-LOOPC" => "2", 
			"SY-LSIND" => "2", 
			"SY-LSTAT" => "2", 
			"SY-MACDB" => "2", 
			"SY-MACOL" => "2", 
			"SY-MANDT" => "2", 
			"SY-MARKY" => "2", 
			"SY-MAROW" => "2", 
			"SY-MODNO" => "2", 
			"SY-MSGID" => "2", 
			"SY-MSGLI" => "2", 
			"SY-MSGNO" => "2", 
			"SY-MSGTY" => "2", 
			"SY-MSGV1" => "2", 
			"SY-MSGV2" => "2", 
			"SY-MSGV3" => "2", 
			"SY-MSGV4" => "2", 
			"SY-OPSYS" => "2", 
			"SY-PAART" => "2", 
			"SY-PAGCT" => "2", 
			"SY-PAGNO" => "2", 
			"SY-PDEST" => "2", 
			"SY-PEXPI" => "2", 
			"SY-PFKEY" => "2", 
			"SY-PLIST" => "2", 
			"SY-PRABT" => "2", 
			"SY-PRBIG" => "2", 
			"SY-PRCOP" => "2", 
			"SY-PRDSN" => "2", 
			"SY-PREFX" => "2", 
			"SY-PRIMM" => "2", 
			"SY-PRNEW" => "2", 
			"SY-PRREC" => "2", 
			"SY-PRREL" => "2", 
			"SY-PRTXT" => "2", 
			"SY-REPID" => "2", 
			"SY-SPONR" => "2", 
			"SY-SROWS" => "2", 
			"SY-STACO" => "2", 
			"SY-STARO" => "2", 
			"SY-STEPL" => "2", 
			"SY-SUBRC" => "2", 
			"SY-SUBTY" => "2", 
			"SY-SYSID" => "2", 
			"SY-TABIX" => "2", 
			"SY-TCODE" => "2", 
			"SY-TFDSN" => "2", 
			"SY-TFILL" => "2", 
			"SY-TIMLO" => "2", 
			"SY-TIMUT" => "2", 
			"SY-TITLE" => "2", 
			"SY-TLENG" => "2", 
			"SY-TMAXL" => "2", 
			"SY-TNAME" => "2", 
			"SY-TOCCU" => "2", 
			"SY-TPAGI" => "2", 
			"SY-TSTLO" => "2", 
			"SY-TSTUT" => "2", 
			"SY-TTABC" => "2", 
			"SY-TTABI" => "2", 
			"SY-TVAR0" => "2", 
			"SY-TVAR1" => "2", 
			"SY-TVAR2" => "2", 
			"SY-TVAR3" => "2", 
			"SY-TVAR4" => "2", 
			"SY-TVAR5" => "2", 
			"SY-TVAR6" => "2", 
			"SY-TVAR7" => "2", 
			"SY-TVAR8" => "2", 
			"SY-TVAR9" => "2", 
			"SY-TZONE" => "2", 
			"SY-UCOMM" => "2", 
			"SY-ULINE" => "2", 
			"SY-UNAME" => "2", 
			"SY-UZEIT" => "2", 
			"SY-VLINE" => "2", 
			"SY-WAERS" => "2", 
			"SY-WILLI" => "2", 
			"SY-WINCO" => "2", 
			"SY-WINDI" => "2", 
			"SY-WINRO" => "2", 
			"SY-WINSL" => "2", 
			"SY-WINX2" => "2", 
			"SY-WINXI" => "2", 
			"SY-WINY1" => "2", 
			"SY-WINY2" => "2", 
			"SY-WTITL" => "2", 
			"SY-XCODE" => "2", 
			"SY-ZON" => "2");

// Special extensions

// Each category can specify a PHP function that returns an altered
// version of the keyword.
        
        

$this->linkscripts    	= array(
			"1" => "donothing", 
			"2" => "donothing");
}


function donothing($keywordin)
{
	return $keywordin;
}

}?>
