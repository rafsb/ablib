class _Router_Traits {

    /*
     *
     *
     *
     *
     */
    routes = {

        /* VIEWS */
        splash:     "webroot/view/splash.htm"
        , home:     "webroot/view/home.htm"
        , login:    "webroot/view/login.htm"
        
        /* API CALLS */
        , theme:    "themes/get/"
        , auth:     "user/login"

    }
    /*
     *
     *
     *
     *
     */
    async load(name, args=null, container=null, replacement=null){
        if(this.routes[name]) name = this.routes[name];
        if(!container) container = $("#app").at();
        return app.load(name, args, container, replacement)
    }

    async call(name, args=null){
        if(this.routes[name]) name = this.routes[name];
        return app.call(name, args)
    }

    async exec(name, args=null){
        if(this.routes[name]) name = this.routes[name];
        return app.exec(name, args)
    }

};

const
router = new _Router_Traits()
;;