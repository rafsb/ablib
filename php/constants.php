<?php
/***************/
/* 
 * USERS
 */
define("USER",1);
define("UUID",2);

/* 
 * CALL TYPES
 */
define("SYNC",true);
define("ASYNC",false);
define("IN",true);
define("OUT",false);
define("SCAN",'{*}');
define("NOSCAN",false);


/* 
 * TEXT TYPES
 */
define("LEFT", true);
define("RIGHT",false);
define("REVERSE",true);
define("CUT",true);
define("WHERE",true);
/*
 * HANDLER MODES
 */
define("NEW",0);
define("INSERT",0);
define("EDIT",1);
define("UPDATE",1);
define("REMOVE",2);
define("DELETE",2);
define("VIEW",3);
define("REPLACE",4);
define("APPEND",5);
define("PREPEND",6);
define("RECURSIVE",true);
define("NORECURSIVE",false);
define("FORCE",true);
define("NOFORCE",false);
define("LOG",true);
define("NOLOG",false);
define("PRINT",true);
define("NOPRINT",false);
define("APP",true);
define("CLIENT",false);

/*
 * ENVIROMENTS
 */
define("SESSION",1);
define("COOKIE",2);

/*
 * RESPONSE FORMATS
 */
define("__ASSOC__",0);
define("__ARRAY__",1);
define("__JSON__",2);
define("__OBJECT__",3);
define("__MYSQLI_OBJ__",4);

define("SHA1","sha1");
define("SHA256","sha256");
define("SHA512","sha512");
define("MD5","md5");

define("DISK","disk");
define("DATABASE","database");
define("DEFAULT_DB","default");
define("DEFAULT_COLLECTION","default");

define("LAYOUTS_DEFAULT","default");
define("LAYOUTS_THIN","thin");

define("DS","/");
define("NL","\n");
define("NULL",null);

define("POST","POST");
define("GET","GET");


define("SUM"		, 0);
define("TREND"		, 1);
define("HARMONIC"	, 2);
define("POLINOMIAL"	, 3);
define("PROGRESS"	, 4);
define("MAX"		, 5);
define("MIN"		, 6);
define("AVERAGE"	, 7);