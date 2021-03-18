<?php
/***************/
/* 
 * USERS
 */
// define("USER",1);
define("UUID",0);

define("DS",DIRECTORY_SEPARATOR);
define("NL",PHP_EOL);

/* 
 * CALL TYPES
 */
abstract class EBehavior
{
    const SYNC   = true;
    const ASYNC  = false;
    const IN     = true;
    const OUT    = false;
    const SCAN   = '*';
    const ALL    = '*';
    const NOSCAN = false;
}


/* 
 * TEXT TYPES
 */
abstract class EText
{
    const LEFT    = true;
    const RIGHT   = false;
    const REVERSE = true;
    const CUT     = true;
    const WHERE   = true;
}
/*
 * HANDLER MODES
 */
abstract class ESql
{
    const INSERT = 0;
    const UPDATE = 1;
    const DELETE = 3;
    const __ARRAY  = 0;
    const __ASSOC  = 1;
    const __JSON   = 2;
    const __OBJECT = 3;
    const __MYSQLI = 4;    
}

abstract class EModes
{
    const NEW         = 0;
    const EDIT        = 1;
    const REMOVE      = 2; 
    const VIEW        = 4;
    const REPLACE     = 5;
    const APPEND      = 6;
    const PREPEND     = 7;
    const RECURSIVE   = true;
    const NORECURSIVE = false;
    const FORCE       = true;
    const NOFORCE     = false;
    const LOG         = true;
    const NOLOG       = false;
    const PRINT       = true;
    const NOPRINT     = false;

}

abstract class ETypes
{
    const APP    = true;
    const CLIENT = false;
}
/*
 * ENVIROMENTS
 */
define("SESSION",1);
define("COOKIE",2);

/*
 * USER LEVELS
 */
abstract class EUser
{
    const LOGGED    =  0;
    const USER      =  1;
    const EDITOR    =  2;
    const MANAGER   =  3;
    const TI        =  4;
    const DIRECTOR  =  5;
    const ADMIN     =  6;
    const DEV       =  7;
    const SYSTEM    =  8;
    const ROOT      =  9;

}
abstract class EHash
{
    const SHA1   = "sha1";
    const SHA256 = "sha256";
    const SHA512 = "sha512";
    const MD5    = "md5";
}

abstract class EPersistance
{
    const DISK = "disk";
    const DATABASE = "database";
    const DEFAULT_DB = "defaultdb";
    const DEFAULT_COLLECTION = "default";
    
}

abstract class ELayouts
{
    const DEFAULT = "default";
    const THIN    = "thin";
}


abstract class ERequest
{
    const POST   = "POST";
    const GET    = "GET";
    const OPTION = "OPTION";
    const PUT    = "PUT";
}

abstract class ECalculate
{
    const SUM        = 0;
    const TREND      = 1;
    const HARMONIC   = 2;
    const POLINOMIAL = 3;
    const PROGRESS   = 4;
    const MAX        = 5;
    const MIN        = 6;
    const AVERAGE    = 7;
}
