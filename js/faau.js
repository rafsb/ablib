/**************************************************************************
     ___                                             _
    /  _|_ __ __ _ _ __ ___   _____      _____  _ __| | __
    | |_| '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /
    |  _| | | (_| | | | | | |  __/\ V  V / (_) | |  |   <
    |_| |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\

****************************************************************************/
var 
DEBUG = false

const
ANIMATION_LENGTH    = 400
, SUM               = 0
, AVERAGE           = 1
, HARMONIC          = 2
, TREND             = 3
, PROGRESS          = 4
, INTERPOLATE       = 5
, MAX               = 6
, MIN               = 7
, PASSWD_AUTO_HASH  = 0
, NUMBER            = 0
, STRING            = 1
;

var
bind = function(e,o){
    let
    a = Object.keys(o);
    for(let i=a.length;i--;) e[a[a.length-i-1]]=o[a[a.length-i-1]];
    return e
};
bind(Number.prototype, {
    fill: function(c,l,d){ return (this+"").fill(c,l,d) }
    , nerdify: function(){ 
        let n = this*1;
        return n > 1000000000000 ? ((n / 1000000000000).toFixed(1))+"tri" : (
            n > 1000000000 ? ((n / 1000000000).toFixed(1))+"bi" : (
                n > 1000000 ? ((n / 1000000).toFixed(1))+"mi" : (
                    n > 1000 ? ((n / 1000).toFixed(1))+"k" : Math.ceil(n)
                )
            )
        )
    }
})
bind(Date.prototype, {
    plus: function(n) {
        let
        date = new Date(this.valueOf());
        date.setDate(date.getDate() + n);
        return date
    }
    , export(format = "Y-m-d"){
        let
        d = this
        , arr = format.split("");
        arr.each(n => {
            switch(n){
                case "Y": format = format.replace(n, d.getFullYear());                   break;
                case "y": format = format.replace(n, (d.getYear()-100).fill("0", 2));    break;
                case "m": format = format.replace(n, (d.getMonth()+1).fill("0", 2));     break;
                case "d": format = format.replace(n, d.getDate().fill("0", 2));          break;
                case "h": format = format.replace(n, d.getHours().fill("0", 2));         break;
                case "i": format = format.replace(n, d.getMinutes().fill("0", 2));       break;
                case "s": format = format.replace(n, d.getSeconds().fill("0", 2));       break;
                case "k": format = format.replace(n, d.getMilliseconds().fill("0", 3));  break;
                case "t": format = format.replace(n, d.getTime());                       break;
            }
        })
        return format
    }
    , refresh: function(){
        return new Date()
    }
});

bind(NodeList.prototype, {
    array: function() {
        return [].slice.call(this);
    }
});
bind(HTMLCollection.prototype, {
    array: function() {
        return [].slice.call(this);
    }
})
bind(HTMLFormElement.prototype,{
    json: function(){
        let
        tmp = {};
        this.get("input, textarea, select, .-value").each(o=>{
            if(!o.has("-skip")&&o.name){
                tmp[o.name] = (o.tagName.toUpperCase()=="TEXTAREA"&&o.has("-list") ? o.value.trim().split('\n').clear() : o.value);
                if((PASSWD_AUTO_HASH||o.has("-hash"))&&o.getAttribute("type")&&o.getAttribute("type").toUpperCase()=="PASSWORD") tmp[o.name] = tmp[o.name].hash();
            }
        });
        return tmp;
    }
    , stringify: function(){
        return JSON.stringify(this.json())
    }
});
bind(HTMLInputElement.prototype, {
    val: function(v=null) {
        if(v!==null) this.value = v;
        return this
    }
    , up: function(name, path, fn=null, mini=false) {
        let
        ctnr = this.uid()
        , form = new FormData()
        , counter = 0;

        name = name || app.uid(13);

        form.append("picture", this.files[0]);
        form.append("name", name);
        form.append("path", path);
        form.append("minify", mini?1:0);

        xhr = new XMLHttpRequest();

        if(fn) xhr.upload.onload = function() {
            console.log(xhr.responseText)
        }
        xhr.upload.onerror = _ => app.error("Ops! N茫o foi poss铆vel subir esta imagem... chama o berts...");
        xhr.open("POST", "image/upload");
        xhr.send(form);

        const formData = new FormData();
    }
});
bind(Element.prototype,{
    anime: function(obj,len=ANIMATION_LENGTH,delay=0,trans=null) {
        let
        el = this
        return new Promise(function(ok, err){
            len/=1000;
            trans = trans ? trans : "ease";
            el.style.transition = "all " + len.toFixed(2) + "s "+trans;
            el.style.transitionDelay = (delay?delay/1000:0).toFixed(2)+"s";
            for(let i in obj) el.style[i] = obj[i];
            setTimeout(function(el){ return ok(el) },len*1000+delay+1, el)
        })
    }
    , mimic: function(){
        return this.cloneNode(true)
    }
    , stop: function() {
        if(this.dataset.animationFunction) clearInterval(this.dataset.animationFunction);
        this.dataset.animationFunction = "";
        return this
    }
    , empty: function() {
        this.html("");
        return this
    }
    , css: function(o=null, fn = null) {
        if (o===null) return this;
        this.style.transition = "none";
        this.style.transitionDuration = 0;
        for(let i in o) this.style[i] = o[i];
        if(fn!==null&&typeof fn=="function") setTimeout(fn.bind(this),16, this);
        return this
    }
    , text: function(t=null, fn=null){
        if(t==null||t==undefined) return this.textContent;
        this.textContent = t;
        if(fn) return fn.bind(this)(this);
        return this;
    }
    , html: function(tx=null) {
        if(tx!==null&&tx!==false) this.innerHTML = tx;
        else return this.innerHTML;
        return this
    }
    , data: function(o=null, fn=null) {
        if (o===null) return this.dataset;
        bind(this.dataset, o);
        if(fn!==null&&typeof fn=="function") fn.bind(this)(this);
        return this;
    }
    , attr: function(o=null, fn = null) {
        if (o===null) return null;
        let el = this;
        Object.keys(o).each(x=>el.setAttribute(x,o[x]));
        if(fn!==null&&typeof fn=="function") fn.bind(this)(this);
        return this;
    }
    , _put_where_: function(obj=null,w="beforeend"){
        let
        el=this;
        if(Array.isArray(obj)) obj.each(o=>el.app(o));
        else if(obj) el.insertAdjacentElement("beforeend",obj);
        return this;
    }
    , aft: function(obj=null) { return this._put_where_(obj,"afterend")     }
    , bef: function(obj=null) { return this._put_where_(obj,"beforebegin")  }
    , app: function(obj=null) { return this._put_where_(obj,"beforeend")    }
    , pre: function(obj=null) { return this._put_where_(obj,"afterbegin")   }
    , has: function(cls=null) {
        if(cls) return this.classList.contains(cls);
        return false
    }
    , dataSort: function(data=null,dir="asc") {
        let
        all = [].slice.call(this.children);
        if(all.length) all.sort(function(a,b){ return dir=="asc" ? a.dataset[data]*1 - b.dataset[data]*1 : b.dataset[data]*1 - a.dataset[data]*1 });
        all.each(el => el.raise())
        return this
    }
    , index: function() {
        return [].slice.call(this.parent().children).indexOf(this)-1;
    }
    , evalute: function() {
        this.get("script").each(x=>{ eval(x.textContent)&&x.remove() })
        return this
    }
    , on: function(action,fn,passive=true) {
        this.addEventListener(action,fn, {passive:passive})
        return this
    }
    , parent: function(pace=1) {
        let
        tmp = this;
        while(pace--) tmp = tmp.parentElement;
        return tmp;
    }
    , upFind(tx=null){
        if(tx){
            let
            x = this;
            while (x.parentElement.tagName.toLowerCase() != "body" && !(x.parentElement.tagName.toLowerCase()==tx || x.parentElement.has(tx))) x = x.parentElement;
            return x.parentElement
        }
        return this.parentElement
    }
    , inPage: function() {
        let
        page = {
            top: this.parentElement.scrollTop,
            bottom: this.parentElement.scrollTop + window.innerHeight,
            height: window.innerHeight
        },
        element = {
            top: this.offsetTop,
            bottom: this.offsetTop + this.offsetHeight
        };
        return (element.top <= page.bottom + 1 && element.bottom >= page.top - 1) ? {
            offset: element.top - page.top,
            where: 1 - (element.top - page.top) / page.height
        } : false;
    }
    , scrollTo: function(el,fn=null) {
        if (!el) return -1;
        let
        length = 0;
        do {
            length += el.offsetTop;
            el = el.parentElement;
        } while (el.uid() != this.uid());
        this.scroll({top:length,behavior:"smooth"});
        fn&&fn();
    }
    , stopScroll: function() {
        this.scroll({top:this.scrollTop+1});
    }
    , get: function(el) {
        if(el) return [].slice.call(this.querySelectorAll(el));
        else return this;
    }
    , remClass: function(c) {
        if (this.classList.contains(c)) {
            this.classList.remove(c);
        }
        return this;
    }
    , addClass: function(c) {
        if(c){
            let
            tmp = c.trim().split(/\s+/g)
            , i=tmp.length;
            if(c.length) while(i--) this.classList.add(tmp[i]);
        }
        return this;
    }
    , toggleClass: function(c) {
        let
        tmp = c.split(/\s+/g), i=tmp.length;
        while(i--) {
          if (tmp[i]) {
            if(!this.classList.contains(tmp[i]))
              this.classList.add(tmp[i]); else this.classList.remove(tmp[i]);
            }
          } return this;
    }
    , uid: function(name=null, hash = false) {
        if(name) this.id = name.replace(/[^0-9a-zA-Z]/g,"");
        if(!this.id) this.id = app.nuid(8);
        return (hash ? "#" :"") + this.id;
    }
    , move: function(obj,len=ANIMATION_LENGTH, anim="linear") {
        len /= 1000;
        this.style.transition = "all "+len+"s "+anim;
        if(obj.top!==undefined)this.style.transform = "translateY("+(this.offsetTop-obj.top)+")";
        if(obj.left!==undefined)this.style.transform = "translateX("+(this.offsetLeft-obj.left)+")";
    }
    , raise: function(){
        this.parentElement.appendChild(this)
        return this
    }
    , appear: function(len = ANIMATION_LENGTH, fn=null) {
        return this.stop().css({display:'inline-block'}, x=>x.anime({opacity:1}, len).then(fn))
    }
    , desappear: function(len = ANIMATION_LENGTH, remove = false, fn=null) {
        return this.stop().anime({opacity:0}, len).then(x=>{ if(remove) x.remove(); else x.css({display:"none"}); if(fn) fn(remove ? null : this); });
    }
    , remove: function() { if(this&&this.parent()) this.parent().removeChild(this) }
});
bind(String.prototype,{
    hash: function() {
        let
        h = 0, c = "", i = 0, j = this.length;
        if (!j) return h;
        while (i++ < j) {
            c = this.charCodeAt(i - 1);
            h = ((h << 5) - h) + c;
            h |= 0;
        }
        return Math.abs(h).toString();
    }
    , btoa: function(){
        return btoa(this);
    }
    , atob: function(){
        return atob(this);
    }
    , fill: function(c=" ", l=8, d=-1) {
        let
        s = this;
        c = !c ? " " : c;
        d = d==0||d==null||d==undefined ? -1 : d;
        while(s.length < l) s = (d<0?c:"")+s+(d>0?c:"");
        return s
    }
    , desnerdify: function(){
        let
        n = Number(this.replace(/[^0-9\.]/g,'').replace(',','.'))
        , s = this.replace(/[^a-zA-Z]/g,'');
        switch(s){
            case "tri": n *= 1000000000000; break;
            case "bi" : n *= 1000000000; break;
            case "mi" : n *= 1000000; break;
            case "k"  : n *= 1000; break;
            default   : n *= 1; break;
        }
        return n
    }
    , json: function() {
        let
        result = null;
        try{
            result = JSON.parse(this);
        } catch(e) {
            // statements
            console.log(e);
        }
        return result;
    }
    , morph: function() {
        let
        x = document.createElement("div");
        x.innerHTML = this.replace(/\t+/g, "").trim();
        return x.firstChild.tagName.toLowerCase()=="template" ? x.firstChild.content.children.array() : x.children.array();
    }
    , prepare: function(obj=null){
        if(!obj) return this;
        let
        str = this.trim();
        Object.keys(obj).each(x=>{
            let
            rgx = new RegExp("@"+x,"g");
            str = str.replace(rgx,obj[x]);
        })
        return str;
    }
    , uri: function(){
        return this.replace(/[^a-zA-Z0-9]/g,'_')
    }
    , check: function(tx=null, flag="gi"){
        if(Array.isArray(tx)) tx = tx.join('|');
        if(typeof tx == "string"){
            let
            rx = new RegExp(tx, flag);
            return rx.test(this)
        }
        return false
    }
});
bind(Object.prototype,{
    json:function(){ return JSON.stringify(this) }
    , each: function(fn=null){
        let
        me = this
        , arr = me.keys()
        , final = [];
        if(fn && arr.length){
            arr.each(x => final.push({ key: x, content: me[x] }));
            final.each(fn)
        }
        return this
    }
    , array: function(){
        return this.extract(n => n.content)
    }
    , extract: function(fn=null){
        let
        final = [];
        if(fn){
            this.each((x,i) => {
                let
                y = fn.bind(x)(x, i);
                if(y!=null||y!=false) final.push(y)
            })
        }
        return final
    }
    , keys: function(){
        return Object.keys(this);
    }
});
bind(Array.prototype, {
    json: function(){ return JSON.stringify(this); }
    , clone: function() { return this.slice(0) }
    , each: function(fn) { if(fn) { for(let i=0;i++<this.length;) fn.bind(this[i-1])(this[i-1], i-1); } return this }
    , extract: function(fn=null){
        if(!fn||!this.length) return this;
        let
        narr = [];
        this.each(function(o,i){ 
            let
            x = fn.bind(this)(this,i);
            if(x||x===0) narr.push(x) 
        })
        return narr
    }
    , fill: function(n=1, v=null){
        let x = this;
        app.iterate(0, Math.max(1,n), i => x[i] = x[i] || v.prepare({i:i}));
        return x
    }
    , cast: function(filter=STRING){
        return this.extract(x => { return filter==STRING?x+"":(filter==NUMBER?x*1:x) })
    }
    , fit: function(n=10){
        let
        narr=[ this.first() ]
        , x = this.length / (n-1)
        , i = x
        ;
        while(i<this.length){
            narr.push(this.calc(TREND, i));
            i+=x;
        }
        narr.push(this.last())
        return narr
    }
    , calc: function(type=SUM, helper=null){
        let
        res = 0;
        switch (type){
            case (SUM): this.each(x=>res+=x); break
            case (AVERAGE): this.each(x=>res+=x); res=res/this.length; break
            case (HARMONIC): this.each(x=>res+=1/x); res=this.length/res; break
            case (TREND): {
                let
                m, b, x, y, x2, xy, z, np = this.length;
                m = b = x = y = x2 = xy = z = 0;
                if(!helper) helper = np;
                this.each((n, i) => {
                    x = x + i;
                    y = y + n;
                    xy = xy + i * n;
                    x2 = x2 + i * i;
                });
                z = np*x2 - x*x
                if(z){
                    m = (np*xy - x*y)/z;
                    b = (y*x2 - x*xy)/z;
                }
                res = m * helper + b
            } break
            
            /* TODO POLINOMIAL FORMULA */
            case (INTERPOLATE): {
                if(helper==null||helper==undefined) return app.error("Ops! a 'x' value is needed for array basic interpolation...")
                let
                x = helper
                , yi = this.extract(_y => Array.isArray(_y) ? _y[1] : y*1)
                , xi = yi.extract((_x, i) => Array.isArray(_x) ? _x[0] : i*1)
                , N  = xi.length
                , sum = 0
                ;;

                //for (k=0; k<N; k++) {
                xi.each(k => {
                //     if(k==x) break;
                     let
                     product = 1;
                //     for (i=0; i<N; i++){
                    xi.each(j => {
                         if(k!=j) product = product * (x-j) / (k-j);   
                //         console.log(xi[k], xi[i]    )
                    })
                //     }
                     sum += yi[k] * product;
                })
                // }

                // let
                // x = helper
                // , yi = this
                // , xi = yi.extract((_,i) => i)
                // , N  = xi.length
                // , sum = 0
                // ;;
                // for (k=0; k<N; k++) {
                //     let
                //     product = 1;
                //     for (i=0; i<N; i++) if (i!=k) product = product*(x - xi[i]) / (xi[k] - xi[i]);
                //     sum += yi[k] * product;
                // }
                res = sum;
            } break;

            case (PROGRESS): {
                let
                me = this;
                res = this.extract((x,i)=>{ return i ? me[i]/me[i-1] : 1 }).calc(AVERAGE)
            }break;
            case (MAX): {
                res = Number.MIN_SAFE_INTEGER;
                this.each(x=>res=Math.max(res,x))
            }break;
            case (MIN): {
                res = Number.MAX_SAFE_INTEGER;
                this.each(x=>res=Math.min(res,x))
            }break;
        }
        return res;
    }
    , fillNulls: function(){
        let
        final
        , nulls = []
        , narr = this.extract((el,i) => {
            let
            y = Array.isArray(el) ? el[1] : el
            , x = Array.isArray(el) ? el[0] : i
            ;;
            if(y==null || y==undefined) nulls.push(x);
            else return [ x, y ];
        })
        nulls.each(n => narr.push([ n, narr.calc(INTERPOLATE, n)]));
        narr.sort(function(a,b){ return a[0] - b[0] })
        return narr;
    }
    , last: function(n=null) { 
        if (!this.length) return null;
        if (n === null) return this[this.length - 1];
        return this.slice(Math.max(this.length - n, 0));
    }
    , first: function(n=null) { 
        if (!this.length) return null;
        if (n === null) return this[0];
        return this.slice(0, n);  
    }
    , at: function(n=0) { 
        if(n>=0) return this.length>=n ? this[n] : null;
        return this.length > n*-1 ? this[this.length+n] : null
    }
    , not: function(el) { 
        let
        arr = this;
        while(arr.indexOf(el)+1) arr.splice(arr.indexOf(el),1);
        return arr;
    }
    , anime: function(obj,len=ANIMATION_LENGTH,delay=0,trans=null) {
        this.each(x=>x.anime(obj,len,delay,trans));
        return this
    }
    , stop: function(){
        this.each(x => x.stop())
        return this
    }
    , raise: function() {
        this.each(x => x.raise());
        return this
    }
    , css: function(obj,fn=null) {
        this.each(x=>x.css(obj,fn));
        return this
    }
    , data: function(obj,fn=null) {
        this.each(x=>x.data(obj,fn));
        return this
    }
    , attr: function(obj,fn=null) {
        this.each(x=>x.attr(obj,fn));
        return this
    }
    , text: function(txt,fn=null) {
        this.each(x=>x.text(txt,fn));
        return this
    }
    , addClass: function(cl=null) {
        if(cl) this.each(x=>x.addClass(cl));
        return this
    }
    , remClass: function(cl=null) {
        if(cl) this.each(x=>x.remClass(cl));
        return this
    }
    , toggleClass: function(cl=null) {
        if(cl) this.each(x=>x.toggleClass(cl));
        return this
    }
    , remove: function() {
        this.each(x=>x.remove());
        return this
    }
    , setValue: function(v='') {
        this.each(x=>x.value = v);
        return this
    }
    , on: function(act=null,fn=null) {
        if(act&&fn) this.each(x=>x.on(act,fn));
        return this
    }
    , empty: function(){
        this.each(x => x.empty())
        return this
    }
    , clear: function(){
        return this.extract(function(){
            return this && this != "" ? (this instanceof String ? this+"" : (this instanceof Number ? this*1 : this)) : null
         })
    }
    , evalute: function(){
        this.each(me=>{ 
            if(me.tagName.toLowerCase()=="script") eval(me.textContent); 
            else me.get("script").evalute()
        })
    }
    , appear: function(len = ANIMATION_LENGTH) {
        return this.each(x=>x.css({display:'block'},x=>x.anime({opacity:1}, len, 1)))
    }
    , desappear: function(len = ANIMATION_LENGTH, remove = false, fn=null){
        return this.each(x=>x.desappear(len,remove,fn))
    }
    , val: function(v=null){
        if(v) this.each(x=>{ if(x.tagName.toLowerCase()=="input") x.value = v })
        return this
    }
});

Object.defineProperty(Object.prototype, "spy", {
    value: function (p,fn) {
        let
        o = this[p]
        , n = o
        , get = function() { return n }
        , set = function(v) { o = n; return n = fn.bind(this)(v,p) };
        if(delete this[p]) { // can't watch constants
            Object.defineProperty(this,p,{ get: get, set: set })
        }
    }
});
// object.unwatch
Object.defineProperty(Object.prototype, "unspy", {
    value: function (prop) {
        let
        val = this[prop];
        delete this[prop];
        this[prop] = val;
    }
});
//       _
//   ___| | __ _ ___ ___  ___  ___
//  / __| |/ _` / __/ __|/ _ \/ __|
// | (__| | (_| \__ \__ \  __/\__ \
//  \___|_|\__,_|___/___/\___||___/
//
class Pool {
    add(x=null,v=null) {
        if(x) {
            if(Array.isArray(x)) this.sort(x);
            if(typeof x === 'function') { 
                this.execution.push(x);
                if(this.execution.length > this.timeline.length) this.at(v)
            }
            else this.conf(x,v)
        }
        return this;
    }
    push(x) {
        this.add(x);
        return this
    }
    sort(x) {
        let
        pool = this;
        if(Array.isArray(x)) {
            x.each(z=>pool.add(z))
        }
        return this;
    }
    conf(k=null,v=null) {
        if(k!==null) {            
            if(v!==null) this.setup[k]=v;
        }
        return this
    }
    at(t=null) {
        this.moment = t&&parseInt(t) ? t : this.moment+1;
        this.timeline.push(this.moment);
        return this
    }

    plus(t=0) { return this.at(this.moment +t) }
    fire(x=null) {
        if(typeof x == "function"){
            this.add(x,this.moment+1);
            x=null
        }
        let
        pool=this;

        const
        o = new Promise(function(pass, deny){
            pool.execution.each((z, i) => {
                pool.timeserie[i] = setTimeout(z, pool.timeline[i], x, pool.setup);
            })
            setTimeout(function(ok){ return pass(ok) }, pool.timeserie.calc(MAX)+ANIMATION_LENGTH/4, true)
        })
        
        return o
    }
    stop(i=null) {
        if(i!==null){ if(this.timeserie[i]) clearInterval(this.timeserie[i]) }
        else this.timeserie.each(x=>clearInterval(x))
        return this
    }
    clear() {
        this.stop();
        this.moment = 0;
        this.timeline = [];
        this.timeserie = [];
        this.execution = [];
        this.setup = {};
        return this
    }
    debug() {
        console.log("CONFIGURATION");
        console.log(this.setup);
        console.log("TIMESERIE");
        this.timeline.each((i,x)=>{console.log("AT:"+x+" => DO:"+this.execution[i])})
    }
    after(fn=null) {
        if(fn&&typeof fn=='function') setTimeout(fn,this.moment+1);
        return this
    }
    constructor(x) {
        this.moment = 0;
        this.timeline = [];
        this.timeserie = [];
        this.execution = [];
        this.setup = {};
        return this.add(x)
    }
};

class __BaseElement__ {

    emptyElement(){
        this.node = _();
        app.error("__BaseElement__::emptyElement needs to be overwritten... =}")
    }

    icon(path){ 
        let
        node = this.node.get(".--icon");
        if(!node.length) return null;
        node = node[0];
        if(path) node.attr({ src: path })
        return node
    }
    invertIcon(){ this.icon().toggleClass("-inverted") }

    title(text){
        let
        node = this.node.get(".--title");
        if(!node.length) return null;
        node = node[0];
        if(text) typeof text == "string" ? node.html(text) : node.app(text);
        return node
    }

    content(text){
        let
        node = this.node.get(".--content");
        if(!node.length) return null;
        node = node[0];
        if(text) typeof text == "string" ? node.html(text) : node.app(text);
        return node
    }

    tags(text){
        let
        node = this.node.get(".--tags");
        if(!node.length) return null;
        node = node[0];
        if(text) typeof text == "string" ? node.html(text) : node.app(text);
        return node
    }

    custom(obj){
        if (obj) {
            if (obj.title) this.title(obj.title);
            if (obj.icon) this.icon(obj.icon);
            if (obj.content) this.content(obj.content);
            if (obj.tags) this.tags(obj.tags);
            if (obj.class) this.node.toggleClass(obj.class);
            if (obj.css) this.node.css(obj.css);
        }
        return this
    }

    export(){ return this.node.mimic() }

    constructor(obj){
        this.emptyElement();
        if(obj) this.custom(obj)
    }
}

class Tile extends __BaseElement__ {
    emptyElement() {
        this.node = _("div", "-row -tile -no-scrolls", {
            borderRadius: ".5em"
            , boxShadow: "none"//"0 0 .5em rgba(0,0,0,.64)"
            , background: app.colors("FOREGROUND")
            , marginBottom: ".5em"
        }).app(
            _("header", "-row -keep", { padding: ".5em" }).app(
                _("img", "-left -keep --close --icon", { width: "2em", height: "2em", opacity: .8, transform:"scale(.8)" })
            ).app(
                _("b", "-left -content-left -ellipsis --title", { width: "calc(100% - 3em)", padding: ".5em .25em", opacity: .8 })
            )
        ).app(
            _("section", "--content -keep -row -content-left", { padding: "0 .5em" })
        ).app(
            _("footer", "-row -keep --tags", { padding:".5em" })
        )
        return this.node
    }
};

class Row  extends __BaseElement__ {
    emptyElement() {
        this.node = _("div", "-relative -row ", {
            borderRadius: ".25em"
            , background: app.colors("FOREGROUND")
        }).app(
            _("img", "-right -keep --icon", { height: "2em", padding:".5em", opacity: .8 })
        ).app(
            _("div", "-left -keep -content-left -ellipsis --content", { width: "calc(100% - 2.5em)", padding: "calc(.5em + 2px)" })
        )
        return this.node
    }
};

class Tag extends __BaseElement__ {
    emptyElement() {
        this.node = _("div", "-pointer", {
            borderRadius: ".25em"
            , background: "#00000032"
            , padding: ".25em"
            , margin: ".25em"
        }).app(_("div", "-left -row -ellipsis --content"))
        return this.node
    }
};

class Swipe {
    constructor(el,len=10) {
        this.len = len;
        this.x = null;
        this.y = null;
        this.e = typeof(el) === 'string' ? $(el).at() : el;
        if(!this.e) return;
        this.e.on('touchstart', function(v) {
            this.x = v.touches[0].clientX;
            this.y = v.touches[0].clientY;
        }.bind(this));        
    }

    left(fn) { this.__LEFT__ = new Throttle(fn,this.len); return this }

    right(fn) { this.__RIGHT__ = new Throttle(fn,this.len); return this }

    up(fn) { this.__UP__ = new Throttle(fn,this.len); return this }

    down(fn) { this.__DOWN__ = new Throttle(fn,this.len); return this }

    move(v) {
        if(!this.x || !this.y) return;
        let
        diff = (x,i)=>{ return x-i }, 
        X = v.touches[0].clientX,
        Y = v.touches[0].clientY;

        this.xdir = diff(this.x,X);
        this.ydir = diff(this.y,Y);

        if(Math.abs(this.xdir)>Math.abs(this.ydir)) { // Most significant.
            if(this.__LEFT__&&this.xdir>0) this.__LEFT__.fire();
            else if(this.__RIGHT__) this.__RIGHT__.fire();
        }else{
            if(this.__UP__&&this.ydir>0) this.__UP__.fire();
            else if(this.__DOWN__) this.__DOWN__.fire()
        }
        this.x = this.y = null
    }

    fire() { this.e&&this.e.on('touchmove', function(v) { this.move(v) }.bind(this)) }
};
/*
 * @class
 *
 * handle the minimum amount of time to wait until executions of a given function
 * good to prevent events like scroll and typing to fire some actions multiple
 * times decreasing performance affecting user's experience
 *
 */
class Throttle {
    /*
     * @constructor
     *
     * f = javascript function to be applied
     * t = time betwin executions of 'f' (250ms is the default)
     * ex.: new __self.Throttle(minha_funcao,400);
     *
     */
    constructor(f, t = ANIMATION_LENGTH/2) {
        this.assign(f,t);
    }

    /*
     * @member function
     *
     * assign values to inner class attributes
     * f = javascript function to be applied
     * t = time betwin executions of 'f' (250ms is the default)
     * ex.: (new __self.Throttle).assign(minha_funcao) // assuming default delay time
     *
     */
    assign(f, t) {
        this.func = f;
        this.delay = t;
        this.timer = (new Date()).getTime();
    }

    /*
     * @member function
     *
     * execute given function assigned on constructor or assign() mmber function
     * ex.: (new __self.Throttle).apply()
     * obs.: the fire() member function will only execute the inner function if the
     * given ammount of time is passed, otherway if won't do anything
     *
     */
    fire(d) {
        if(!this.func) return;
        let
        now = (new Date()).getTime();
        if (now - this.delay > this.timer) {
            eval(this.func)(d);
            this.timer = now;
        }
    }
};
class Bootloader {   
    loadLength(){
        var
        count=this.loaders.array();        
        return count.extract(n => n*1 || null).length/count.length;
    }
    check(scr){
        return scr ? this.loaders[scr] : this.alreadyLoaded
    }
    ready(scr){
        if(scr) this.loaders[scr] = 1;

        let
        perc = this.loadLength();

        if(!this.alreadyLoaded&&perc>=1){
            this.alreadyLoaded=true; 
            setTimeout(boot => boot.onFinishLoading.fire(_ => app.pragma = app.initial_pragma || true), ANIMATION_LENGTH, this);
        } else if(!this.alreadyLoaded) setTimeout((x,perc) => x.onReadyStateChange.fire(perc), ANIMATION_LENGTH/4, this, perc);

        return this.alreadyLoaded || false;
    }
    constructor(){
        this.alreadyLoaded      = false;
        this.loadComponents     = new Pool();
        this.onReadyStateChange = new Pool();
        this.onFinishLoading    = new Pool();
        this.loaders = { pass: false }
        if(DEBUG) this.onReadyStateChange.add(nil => console.log(bootloader.loaders))
    }
};
class CallResponse {
    constructor(url=location.href, args={}, method="POST", header={}, data=null){
        this.url = url;
        this.args=args;
        this.method=method;
        this.headers=header;
        this.data=data;
        this.status = this.data ? true : false;
    }
}
class FAAU {
    get(e,w){ return faau.get(e,w||document).nodearray; }
    declare(obj){ Object.keys(obj).each(x=>window[x]=obj[x]) }
    initialize(){ this.initPool.fire() }
    async fetch(url, args=null, method='GET', head=null) {
        if(!head) head = new Headers();
        head["Content-Type"] = head["Content-Type"] || "application/json";
        //head["FA-Custom"] = "@rafsb"
        const 
        req = await fetch(url, {
            method: method
            , body: args ? args.json() : null
            , headers : head
            , mode: "no-cors"
            , credentials: "omit"
            , cache: "no-cache"
            , redirect: "follow"
            , referrer: "no-referrer"
        })
        , ans = await req.text();
        return new CallResponse(url, args, method, head, ans.trim());
    }

    async call(url, args=null, method="GET", head=null){
        const
        o = new Promise(function(accepted,rejected){
            let
            o = new CallResponse(url, args, method)
            , xhr = new XMLHttpRequest();
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    o.status = xhr.status;
                    o.data = xhr.responseText.trim();
                   return accepted(o);
                };
            }
            xhr.open(method,url);
            // xhr.setRequestHeader("Content-Type", method=="POST" ? "application/json;charset=UTF-8": "text/plain");
            // xhr.setRequestHeader("FA-Custom", "@rafsb");
            // if(app.hash) xhr.setRequestHeader("hash", app.hash);
            if(head) Object.keys(head).each(h=>xhr.setRequestHeader(h,head[h]));
            xhr.send(args ? args.json() : null);

        });
        return o;
    }

    async post(url, args, head={ "Content-Type": "application/json;charset=UTF-8" })    {
        return this.call(url, args, "POST", head)
    }

    async load(url, args=null, target=null) {
        return this.post(url, args).then( r => {
            if(!r.status) return app.error("error loading "+url);
            r = r.data.prepare(app.colors()).morph();
            if(!target) target = $('#app')[0];
            target.app(r);
            return r.evalute();
        });
    }

    async exec(url, args=null){
        return this.call(url).then(r=>{
            if(!r.status) return app.error("error loading "+url);
            if(args) r.data = r.data.prepare(args);
            return eval(r.data.prepare(app.colors()));
        })
    }

    nuid(n=8, prefix="_") { 
        let 
        a = prefix;
        n -= a.length; 
        while(n>0 && n-->n) { a+="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789".split('')[parseInt((Math.random()*36)%36)] }
        return a 
    }

    /* 
     * @Override
     */
    loading(show = true, target =null) {
        let
        loads = $(".--default-loading");
        if (!show) {
            loads.each(x => { 
                clearInterval(x.dataset.offloading); 
                x.desappear(ANIMATION_LENGTH, true) 
            });
        }else{
            if(loads.length){
                clearInterval(loads[0].dataset.offloading);
                loads[0].data({ animation: setTimeout(nil => app.loading(false), ANIMATION_LENGTH*8) })
            } else {
                let
                load = _("section", "-fixed -view --default-loading", { 
                    background: "linear-gradient(135deg, "+app.color_pallete.FONT+"22, transparent, "+app.color_pallete.FOREGROUND+"88)"
                    , zIndex:100 
                }).data({
                    offloading: setTimeout(nil => app.loading(false), ANIMATION_LENGTH*8)
                });
                app.load("img/loading.svg", null, load);
                (target || $("#app")[0]).app(load);
            }
        }
    }

    notify(n, c=null) {
        let
        toast = document.createElement("toast")
        , clr = this.color_pallete;
        toast.addClass("-fixed -tile -content-left --notification").css({
            background: c&&c[0] ? c[0] : clr.FOREGROUND
            , color: c&&c[1] ? c[1] : clr.FONT
            , boxShadow:"0 0 .5em "+clr.DARK2
            , borderRadius: ".25em"
            , padding:"1em"
            , display:'block'
            , opacity:0
            , zIndex: 2000
        }).innerHTML = n ? n : "Hello <b>World</b>!!!";
        if(!this.isMobile()) {
            toast.css({
                top:0,
                right:0,
                width:"20vw",
                margin:".5em"
            });
        }else{
            toast.css({
                top:0,
                left:0,
                width:"100vw"
            });
        }
        toast.onclick = function() { clearTimeout(this.dataset.delay);this.desappear(ANIMATION_LENGTH/2,true); };
        toast.onmouseenter = function() { clearTimeout(this.dataset.delay); };
        toast.onmouseleave = function() {
            this.dataset.delay = setTimeout(t=>{ t.desappear(ANIMATION_LENGTH/2,true); }, ANIMATION_LENGTH, this);
        };
        document.getElementsByTagName('body')[0].appendChild(toast);
        tileClickEffectSelector("-tile");
        
        let
        notfys = $("toast.--notification")
        , ht = 0
        ;
        notfys.each((x, i) => { 
            x.anime({transform: "translateY("+ht+"px)", opacity:1}, ANIMATION_LENGTH/4) 
            ht += toast.getBoundingClientRect().height+8;
        });
        toast.dataset.delay = setTimeout(function() { toast.desappear(ANIMATION_LENGTH/2,true); }, ANIMATION_LENGTH*5);
    }

    error(message=null) {
        app.notify(message || "Ops! Something went wrong...", [ this.color_pallete.POMEGRANATE,this.color_pallete.CLOUDS ])
    }
    success(message=null) {
        app.notify(message || "Hooray! Success!", [ this.color_pallete.GREEN_SEA, this.color_pallete.WHITE ])
    }
    warning(message = null) {
        app.notify(message || "Ops! take attention...", [ this.color_pallete.SUN_FLOWER, this.color_pallete.WET_ASPHALT ])
    }
    working(message = null) {
        app.notify(message || "Hooray! Success!", [ this.color_pallete.PETER_RIVER, this.color_pallete.WHITE ])
    }

    hintify(n=null, o={}, delall=true, keep=false, special=false, evenSpecial=false) {

        if(delall) $(".--hintifyied"+(evenSpecial?", .--hintifyied-sp":"")).each(x=>x.desappear(ANIMATION_LENGTH, true));

        o = o || {};
        o.top = o.top || o.top == 0 ? o.top : (window.maxis.y)+"px";
        o.left   = o.left||o.left==0 ? o.left : (maxis.x)+"px";
        o.padding   = o.padding||o.padding==0 ? o.padding : ".5em";
        o.borderRadius = o.borderRadius ? o.borderRadius : ".25em";
        o.boxShadow   =  o.boxShadow ? o.boxShadow :  "0 0 .5em "+app.colors().DARK1;
        o.background =  o.background ? o.background : this.colors().DARK4;
        o.color     =  o.color ?  o.color : this.colors("FONT");
        o.fontSize = o.fontSize ? o.fontSize : "1em";

        if(typeof n == "string") n = ("<f>"+n+"</f>").morph()

        let
        toast = _("toast","-block -absolute --hintifyied"+(special?"-sp":""),o).css({opacity:0}).app(n||"<b>路路路!!!</b>".morph());
        if(toast.get(".--close").length) toast.get(".--close").on("click",function(){ this.upFind("toast").desappear(ANIMATION_LENGTH, true) })
        else toast.on("click",function(){ this.desappear(ANIMATION_LENGTH, true) });
        
        if(!keep){
            toast.on("mouseleave",function(){ 
                $(".--hintifyied"+(special?", .--hintifyied-sp":"")).stop().desappear(ANIMATION_LENGTH, true) 
            }).on("mouseenter", function(){
                this.stop()
            }).dataset.animationFunction = setTimeout(toast => toast.desappear(ANIMATION_LENGTH, true), ANIMATION_LENGTH*8, toast)
        }

        $('body')[0].app(toast.css({ zIndex: 1000 }).appear());
    }

    window(n=null, html=null, css={}){
        let
        head = _("header","-col-12 -content-left -zero",{ height:"3em", padding:"1em", color:app.colors("FONT") }).text(n || "¬¬").app(
            _("div","-absolute -zero-tr -pointer --close -tile").app(
                 _I("spheres/img/icons/cross.svg", null, { height:"3em", padding:"1em", filter:app.theme=="dark" ? "invert(1)" : ""})
            )
        )
        , wrapper = _("div", "-wrapper -zero -no-scrolls")
            .app(_("blur"))
            .app(head)
            .app(_("div", "--content -row -scrolls", { height: "calc(100% - 3em)"}))
        ;;

        if(html) wrapper.get(".--content")[0].app(html);
        
        css.top        = css.top        || "4.5em";
        css.left       = css.left       || "2em";
        css.width      = css.width      || "calc(100vw - 4em)";
        css.height     = css.height     || "calc(100vh - 10em)";
        css.padding    = css.padding    || 0;
        css.background = css.background || "inherit";
        css.color      = css.color      || app.colors().FONT;
        css.boxShadow  = css.boxShadow  || "0 0 4em " + app.colors().BLACK;

        this.hintify(wrapper,css,true,true,true, true);

        tileClickEffectSelector(".-tile")

    }

    apply(fn,obj=null) { return (fn ? fn(obj) : null) }

    get(w=null,c=null) { return $(w,c); }

    args(field=null){
        let
        args = {}
        , parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (nil, k, v) {
            args[k] = v;
        });
        return field===null?args:(args[field]?args[field]:null);
    }

    new(node='div', cls="auto-created", style={display:"inline-block"}, fn) {
        return document.createElement(node).addClass(cls).css(style,fn);
    }

    svg(type="svg", cls="--self-generated", attr={}, css={}){
        return document.createElementNS("http://www.w3.org/2000/svg", type).addClass(cls).attr(attr).css(css)
    }

    storage(field=null,value=null){
        if(field==null||field==undefined) return false;
        if(value===null) return window.localStorage.getItem(field);
        window.localStorage.setItem(field,value===false ? "" : value);
        return window.localStorage.getItem(field);
    }

    cook(field=null, value=null, days=356){
        if(field){
            let
            date = new Date();
            if(value!==null){
                date.setTime(date.getTime()+(days>0?days*24*60*60*1000:days));
                document.cookie = field+"="+value+"; expires="+date.toGMTString()+"; path=/";
            }else{
                field += "=";
                document.cookie.split(';').each(c=>{
                    while (c.charAt(0)==' ') c = c.substring(1,c.length);
                    if(c.indexOf(field)==0) value = c.substring(field.length,c.length);
                });
                return value
            }
        }
    }

    ucook(field=null){
        if(field) app.cook(field,"",-1);
    }

    isMobile(){
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
    }

    colors(pallete="light"){
        return pallete&&this.color_pallete[pallete] ? this.color_pallete[pallete] : this.color_pallete;
    }

    hashit(o){ if(typeof o == "object" || typeof o == "array") o = JSON.stringify(o); return { 
        hash: btoa(o) 
    }}

    sanitize(str){
        return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+')
    }

    async sleep(time=ANIMATION_LENGTH){
        return new Promise(function(ok){
            setTimeout(function(){ return ok() }, time)
        })
    }

    iterate(s, e, fn, step=1){
        if(!fn) fn = i => i;
        for(let i = s; i != e; i += step) fn(i)
    }

    makeServerHashToken(o){ return this.hashit(o).hash; }

    rgb2hex(color) {
        let
        hex = "#";
        if(!Array.isArray(color)) color = color.split(/[\s+,.-]/g);
        color.each(clr => {
            let
            tmp = (clr*1).toString(16);
            hex += tmp.length == 1 ? "0" + tmp : tmp;
        })
        return hex.substring(0,9)
    }

    hex2rgb(color) {
        let
        rgb = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})|([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(color);
        return  rgb ? [ parseInt(rgb[1] || 255, 16), parseInt(rgb[2] || 255, 16), parseInt(rgb[3] || 255, 16), parseInt(rgb[3] || 255, 16) ] : null;
    }

    download(data, filename, type="text/json"){ 
        filename = filename || 'app.txt'; 
        if(typeof data === "object") data = JSON.stringify(data, undefined, 4);
        var 
        blob = new Blob([data], { type: type })
        , e = document.createEvent('MouseEvents')
        , a = document.createElement('a');
        a.download = filename;
        a.href = window.URL.createObjectURL(blob);
        a.dataset.downloadurl = [type, a.download, a.href].join(':');
        e.initMouseEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
        a.dispatchEvent(e);
    }
    

    constructor() {
        this.initial_pragma = 0
        this.current        = 0
        this.last           = 0
        this.initPool       = new Pool()
        this.onPragmaChange = new Pool()
        this.mousePool      = new Pool()
        this.mouseFire      = new Throttle(maxis => this.mousePool.fire(maxis), 200)   
        this.data = {}
        this.tips = {}
        this.components = {}
        this.nodearray = []
        this.prism = {
            ALIZARIN:"#E84C3D"
            , PETER_RIVER:"#2C97DD"
            , ICE_PINK: "#CA179E"
            , EMERLAND:"#53D78B"
            , SUN_FLOWER:"#F2C60F"
            , AMETHYST:"#9C56B8"
            , CONCRETE:"#95A5A5"
            , WET_ASPHALT:"#383C59"
            , TURQUOISE:"#00BE9C"
            , PURPLE_PINK:"#8628B8"
            , PASTEL: "#FEC200"
            , CLOUDS:"#ECF0F1"
            , CARROT:"#E67D21"
            , MIDNIGHT_BLUE:"#27283D"
            , WISTERIA:"#8F44AD"
            , BELIZE_HOLE:"#2A80B9"
            , NEPHIRITIS:"#27AE61"
            , GREEN_SEA:"#169F85"
            , ASBESTOS:"#7E8C8D"
            , SILVER:"#BDC3C8"
            , POMEGRANATE:"#C0382B"
            , PUMPKIN: "#D35313"
            , ORANGE: "#F39C19"
            , BURRO_QNDO_FOJE: "#8C887B"
        }
        this.color_pallete = {
            /*** SYSTEM***/
            BACKGROUND : "#FFFFFF"
            , FOREGROUND : "#ECF1F2"
            , FONT : "#2C3D4F"
            , FONTINVERTED: "#F2F2F2"
            , FONTBLURED:"#7E8C8D"
            , SPAN :"#2980B9"
            , DISABLED: "#BDC3C8"
            , DARK1:"rgba(0,0,0,.08)"
            , DARK2:"rgba(0,0,0,.16)"
            , DARK3:"rgba(0,0,0,.32)"
            , DARK4:"rgba(0,0,0,.64)"
            , LIGHT1:"rgba(255,255,255,.08)"
            , LIGHT2:"rgba(255,255,255,.16)"
            , LIGHT3:"rgba(255,255,255,.32)"
            , LIGHT4:"rgba(255,255,255,.64)"
            /*** PALLETE ***/
            , WHITE: "#FFFFFF"
            , BLACK: "#000000"
        }
        bind(this.color_pallete, this.prism);
    }
};
bind(window, {
    mouseAxis: { x:0, y:0 }
    , $: function(wrapper=null, context=document){ return [].slice.call(context.querySelectorAll(wrapper)) }
    , _:function(node='div', cls, style, fn){ return app.new(node,cls,style,fn) }
    , _S: function(type="svg", cls="--self-generated", attr={}, css={}){ return document.createElementNS("http://www.w3.org/2000/svg", type).addClass(cls).attr(attr||{}).css(css||{}) }
    , _I: function(path="img/icons/cross.svg", cls="--self-generated", css={}){ return _("img", cls, css).attr({ src: path }) }
    , bootloader: new Bootloader()
    , app: (new FAAU())
    , tileClickEffectSelector: function(cls=null){
        if(!cls) return;
        $(cls).each(x=>{
            if(!x.has("--effect-selector-attached")){
                x.addClass("-no-scrolls").on("click",function(e){
                    if(this.classList.contains("-skip")) return;
                    let
                    bounds = this.getBoundingClientRect()
                    , size = Math.max(bounds.width, bounds.height);
                    this.app(_("span","-absolute",{
                        background      : "inherit"
                        , display       : "inline-block"
                        , borderRadius  : "50%"
                        , width         : size+"px"
                        , height        : size+"px"
                        , scale         : 0
                        , opacity       : .4
                        , top: (maxis.y - -bounds.top - bounds.height/2 - size/2)+"px"
                        , left: (maxis.x - bounds.left - bounds.width/2 - size/2)+"px"
                        , filter        : "invert(.2)"
                        , transformOrigin:"center center"
                    }, x=>x.anime({scale:2},ANIMATION_LENGTH/2).then(x=>x.desappear(ANIMATION_LENGTH/4,true))))
                }).addClass("--effect-selector-attached")
            }
        })
    }
    , tooltips: function(){
        $(".--tooltip").each(ttip => {
            ttip.raise().on("mouseenter", function () { 
                $("tooltip.--tooltip-element")[0].stop().html(this.dataset.tip || "hooray").raise().css({ display:"block", background:this.dataset.tipbg || "#000A", color:this.dataset.tipft || "#FFF" })
            });
            ttip.on("mouseleave", function () { $("tooltip.--tooltip-element")[0].css({ display:"none" }) });
        }).remClass("--tooltip")
    }
});
app.spy("pragma",function(x){
    app.last = app.current;
    app.current = x;
    this.onPragmaChange.fire(x);

});
window.onmousemove = e =>{
    window.maxis = { x: e.clientX, y: e.clientY };
    app.mouseFire && app.mouseFire.fire(window.maxis)
}
document.addEventListener("touchstart", function() {}, true);
var
y = setInterval( _ => { window.scrollTo(0, ( document.body.scrollHeight)) }, 1000)
, z = 4000
, name = "relaxar_jul20.txt";
console.log('  __\n\ / _| __ _  __ _ _   _\n\| |_ / _` |/ _` | | | |\n\|  _| (_| | (_| | |_| |\n\|_|  \\__,_|\\__,_|\\__,_|');