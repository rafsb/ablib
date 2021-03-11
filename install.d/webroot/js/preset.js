var
__come = new Event('come')
, __go = new Event('go')
;;

app.components = {};
app.theme_name = app.storage("theme_name", app.storage("theme_name") || APP_DEFAULT_THEME);
app.initial_pragma = START;

app.clear_cache = NULL => {
    /*
     * Clear Storage variables
     */
    app.storage("theme", "")
}

app.wd = window.innerWidth;
app.ht = window.innerHeight;

bootloader.loaders = {
    /*
     * Set the components to be loaded before
     * the system boot
     */
    splash: false
    , theme: false
}

/*
 * These components are loaded at system boot time
 * the splash screen will let the system procede
 * after this execution queue and all bootloader`s
 * loaders are all done
 */
bootloader.loadComponents.add(NULL => {

    app.call("https://"+API_PREFIX+API_HOST+"/themes/get/" + app.theme).then(theme => {
        
        /*
         * Load App theme and assign
         */
        if (theme.data) {
            theme = theme.data.json()
            let custom_theme = app.storage("custom_theme")
            if(custom_theme) _Bind(theme, custom_theme.json());
            _Bind(app.color_pallete, theme)
        }
        [ "background", "foreground" ].each(x => $(".--"+x).css({ background: theme[x.toUpperCase()] }));

        /*
         * Splash/Login boot depends on config
         */
        if(!APP_NEEDS_LOGIN&&app.hash) app.exec("js/screens/splash.js");
        else app.exec("js/screens/login.js")
        
        // assign true to loader.theme
        bootloader.ready("theme")
    })
})

/*
 * This pool will fire after all loaders are true
 */
bootloader.onFinishLoading.add(function() {
    /*
     * commonly used helpers, uncommnt to fire
     */
    // tileClickEffectSelector(".--tile");
    // tooltips();
    
})

/*
 * a key pair value used for tooltips
 * tooltip() function must be fired to
 * make these hints work
 */
app.hints = {
    // some_id: "A simple tootlip used as example"
}

/*
 * The system will boot with bootloader rules
 * comment to normal initialization without
 * possible system dependencies
 */
app.initPool.add(NULL => bootloader.loadComponents.fire())