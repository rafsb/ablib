class _Router_Traits {

    /*
     *
     *
     *
     *
     */
    _Routes_ = {
        home:       "webroot/views/home.htm"
        , theme:    "themes/get/"
        , splash:   "webroot/views/splash.htm"
        , login:    "webroot/views/login.htm"
        , auth:     "user/login"
    }
    /*
     *
     *
     *
     *
     */
    async load(name, args=null, container=null){
        if(this._Routes_[name]) name = this._Routes_[name];
        if(!container) container = $("#app").at();
        return app.load(name, args, container)
    }

    async call(name, args=null){
        if(this._Routes_[name]) name = this._Routes_[name];
        return app.call(name, args)
    }

    async exec(name, args=null){
        if(this._Routes_[name]) name = this._Routes_[name];
        return app.exec(name, args)
    }

};

const
router = new _Router_Traits()
;;