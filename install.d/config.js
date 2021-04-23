
const
/*
 *
 * CONFIG FILE
 *
 */
START 				    = 0
, ANIMATION_LENGTH 	    = 400
, AL 				    = ANIMATION_LENGTH

, API_PROTOCOL 		    = "https://"
, API_PREFIX 	        = ""
, API_HOST 		        = "localhost"
, API_SUFIX 		    = ":7443/"

, APP_DEFAULT_THEME     = "light"
, APP_NEEDS_LOGIN       = false

/*
 * USER LEVELS
 */
, EEvents = {
    swipe:              new Swipe()
    , come:             new Event("come")
    , go:               new Event("go")
    , click:            new Event("click")
    , submit:           new Event("click")
    , CLICK:            "click"
    , MOUSEENTER:       "mouseenter"
    , MOUSELEAVE:       "mouseleave"
    , SUBMIT:           "submit"
}
, EUsers = {
    LOGGED: 		0
    , USER: 		1
    , EDITOR: 		2
    , MANAGER: 		3
    , TI: 			4
    , DIRECTOR: 	5
    , ADMIN: 		6
    , DEV: 			7
    , SYSTEM: 		8
    , ROOT: 		9
}
  /*
 *
 * CLIENT PREDEFINED CONSTANTS
 *
 **********************************************************************/

  

/**********************************************************************
 * ROUTER HOST NAME
 */
, API_HOSTNAME = API_PROTOCOL + API_PREFIX + API_HOST + API_SUFIX
;;