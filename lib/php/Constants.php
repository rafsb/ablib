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
define("SCAN",true);
define("NOSCAN",false);

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

/*
 * ENVIROMENTS
 */
define("SESSION",1);
define("COOKIE",2);

/*
 * RESPONSE FORMATS
 */
define("__ASSOC",0);
define("__ARRAY",1);
define("__JSON",2);
define("__OBJECT",3);
define("__MYSQLI_OBJ",4);
/***************/?>