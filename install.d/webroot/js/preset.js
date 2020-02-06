const 
START 			= 0
, DEFAULT_APP_THEME = "dark"
;

var
__come = new Event('come')
, __go = new Event('go');

app.hash = app.storage("hash") || null;
app.theme = app.storage("theme") || DEFAULT_APP_THEME;

app.body = $("body")[0];
app.initial_pragma = START;
app.components = { /**/ };

bootloader.loaders = { 
	// components
	splash : 	0
	, header : 	0
	, body : 	0
	, menu : 	0
	, footer : 	0
	// api calls
	, theme : 	0
};

bootloader.loadComponents.add(_=>{
	
	app.call("/themes/get/"+app.theme).then(theme=>{
		
	if(theme.data){
			app.theme = theme = theme.data.json()
			bind(app.color_pallete, theme)
		}else theme = app.colors()
		
		$(".--background").css({ background: theme.BACKGROUND });
		$(".--foreground").css({ background: theme.FOREGROUND });

		// USER IS LOGGED ?
		if(app.hash){	
			// SPLASH SCREEN
			app.load("/webroot/views/splash.php")
		// LOGIN
		}else app.load("webroot/views/login.php")

		bootloader.ready("theme")
	})
})

bootloader.onFinishLoading.add(function(){ 
	$(".--screen.--splash")[0].desappear(ANIMATION_LENGTH, true);
	tileClickEffectSelector(".-tile")
})

app.onPragmaChange.add(x => {
	switch (x) {
		case START: 		/*********/ 
		;
		break;
		case FRACTAL: 		/*********/ 
		;
		break;
		case RELATIONAL:	/*********/ 
		;
		break;
		case COMPARATIVE: 	/*********/ 
		;
		break;
		case DOT2NEWS: 		/*********/ 
		;
		break;
	}
});

// __scroll = new Swipe(app.body);
// __scroll.up(()=>{});
// __scroll.down(()=>{});
// __scroll.right(()=>{ });
// __scroll.left(()=>{ });
// __scroll.fire();

app.initPool.add(_ => {
	if(bootloader) bootloader.loadComponents.fire();
})