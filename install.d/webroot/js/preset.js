_Bind(app, {
    components: {}
    , theme_name: app.storage("theme_name", app.storage("theme_name") || APP_DEFAULT_THEME)
    , initial_pragma: EPragmas.START
    , wd: window.innerWidth
    , ht: window.innerHeight
    , clear_cache: NULL => {
        const
        clear_array = [
        /*
         * Clear Storage variables
         */
            "theme", "custom_theme", "v"
        ];
        clear_array.each(item => app.storage(item, ""))
    }
    , on_login: response => {
        const hash = response.data.replace(/\s+/g,'').slice(0,128);
        if(hash.length > 32 && app.storage("hash", hash)) return app.sleep(200).then(NULL => location.reload());
        app.error("Ops! Algo deu errado, tente novamente...");
    }
});;

bootloader.dependencies = [
    /*
     * Set the components to be loaded before
     * the system boot
     */
    "theme", "splash"
];

/*
 * These components are loaded at system boot times
 * the splash screen will let the system procede
 * after this execution queue and all bootloader`s
 * loaders are all done 
 */
bootloader.loadComponents.add(NULL => {

    router.call("theme", { theme: app.theme_name }).then(theme => {
        
        /*
         * Load App theme and assign
         */
        if (theme.data) {
            theme = theme.data.json();
            let custom_theme = app.storage("custom_theme");
            if(custom_theme) _Bind(theme, custom_theme.json());
            _Bind(app.color_pallete, theme);
        }
        [ "background", "foreground" ].each(x => $(".--"+x).css({ background: theme[x.toUpperCase()] }));

        /*
         * Splash/Login boot depends on config
         */
        router.load((!APP_NEEDS_LOGIN||app.hash) ? "splash" : "login");
        
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