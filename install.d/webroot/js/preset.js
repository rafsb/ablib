binds(app, {
    components: {}
    , theme_name: app.storage("theme_name", app.storage("theme_name") || APP_DEFAULT_THEME)
    , initial_pragma: EPragmas.START
    , clear_cache: NULL => {
        [
            /*
            * Clear Storage variables
            */
            "hash", "theme_name", "custom_theme", "v"

        ].each(item => app.storage(item, ""))
    }
});;

bootloader.dependencies = [
    /*
     * Set the components to be loaded before
     * the system boot
     */
    "theme", "splash", "home"
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
            if(custom_theme) binds(theme, custom_theme.json());
            binds(app.color_pallete, theme);
        }
        [ "background", "foreground" ].each(x => $(".--"+x).css({ background: theme[x.toUpperCase()] }));

        /*
         * Splash/Login boot depends on config
         */
        if(!APP_NEEDS_LOGIN||app.hash) router.load("splash").then(NULL => {

            router.load("home");

        });
        else router.load("login");
        
        // assign true to loader.theme
        setTimeout(NULL => bootloader.ready("theme"), AL*2)
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