
const
	/*
	 *
	 * CONFIG FILE
	 *
	 */
	ANIMATION_LENGTH = 400
	, AL = ANIMATION_LENGTH

	, API_PROTOCOL = "https://"
	, API_PREFIX = ""
	, API_HOST = "localhost"
	, API_SUFIX = ":443/"
	, API_HOSTNAME = API_PROTOCOL + API_PREFIX + API_HOST + API_SUFIX

	, APP_DEFAULT_THEME = "light"
	, APP_NEEDS_LOGIN = false

	/*
	 * USER LEVELS
	 */
	, EUsers = {
		LOGGED: 	0
		, USER: 	1
		, EDITOR: 	2
		, MANAGER: 	3
		, TI: 		4
		, DIRECTOR:	5
		, ADMIN: 	6
		, DEV: 		7
		, SYSTEM: 	8
		, ROOT: 	9
	}

	/*
	 * EVENTS
	 */
	, EEvents = {
		CLICK: 			"click"
		, MOUSEENTER:	"mouseenter"
		, MOUSELEAVE: 	"mouseleave"
		, SUBMIT: 		"submit"
	}
	
	/*
	 * PARADIGMS
	 */
	EPragmas = {
		START: 		0
		, MENU: 	1
		, RELOAD: 	2
	}

	/*
	 *
	 * CLIENT PREDEFINED CONSTANTS
	 *
	 */
;;