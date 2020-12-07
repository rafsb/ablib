class _Router_Traits {

    /*
     *
     *
     *
     *
     */
    _Routes_ = {

        home: "views/home.htm"

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

};
const Router = new _Router_Traits();