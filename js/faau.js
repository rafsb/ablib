/**************************************************************************
     ___                                             _
    /  _|_ __ __ _ _ __ ___   _____      _____  _ __| | __
    | |_| '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /
    |  _| | | (_| | | | | | |  __/\ V  V / (_) | |  |   <
    |_| |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\

****************************************************************************/
const
ANIMATION_LENGTH = 400
, DEBUG = false
, REVERSE_PROXY_CLIENT_URI = "https://cors-anywhere.herokuapp.com/"
, SUM               = 0
, AVERAGE           = 1
, HARMONIC          = 2
, PASSWD_AUTO_HASH  = false;
;
var
bind = function(e,o){
    let
    a = Object.keys(o);
    for(let i=a.length;i--;) e[a[a.length-i-1]]=o[a[a.length-i-1]];
    return e
};
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
                if(PASSWD_AUTO_HASH&&o.getAttribute("type")&&o.getAttribute("type").toUpperCase()=="PASSWORD") tmp[o.name] = tmp[o.name].hash();
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
        return this.value
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
        xhr.upload.onerror = function() {
            app.notify("Ops! N茫o foi poss铆vel subir esta imagem... chama o berts...",["#ff0066","white"]);
        };
        xhr.open("POST", "image/upload");
        xhr.send(form);

        const formData = new FormData();
        // const fileField = document.querySelector('input[type="file"]');

        // formData.append('username', 'abc123');
        // formData.append('avatar', fileField.files[0]);

        // try {
        // const response = await fetch('https://example.com/profile/avatar', {
        //     method: 'PUT',
        //     body: formData
        // });
        // const result = await response.json();
        // console.log('Success:', JSON.stringify(result));
        // } catch (error) {
        // console.error('Error:', error);
        // }



        // const formData = new FormData();
        // const photos = document.querySelector('input[type="file"][multiple]');

        // formData.append('title', 'My Vegas Vacation');
        // for (let i = 0; i < photos.files.length; i++) {
        // formData.append('photos', photos.files[i]);
        // }

        // try {
        // const response = await fetch('https://example.com/posts', {
        //     method: 'POST',
        //     body: formData
        // });
        // const result = await response.json();
        // console.log('Success:', JSON.stringify(result));
        // } catch (error) {
        // console.error('Error:', error);
        // }
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
            for(let i in obj) {
                switch(i) {
                    case "skew"       : el.style.transform = 'skew('+obj[i]+','+obj[i]+')';      break;
                    case "skewX"      : el.style.transform = 'skewX('+obj[i]+')';                break;
                    case "skewY"      : el.style.transform = 'skewY('+obj[i]+')';                break;
                    case "scale"      : el.style.transform = 'scale('+obj[i]+')';                break;
                    case "scaleX"     : el.style.transform = 'scaleX('+obj[i]+')';               break;
                    case "scaleY"     : el.style.transform = 'scaleY('+obj[i]+')';               break;
                    case "translate"  : el.style.transform = 'translate('+obj[i]+','+obj[i]+')'; break;
                    case "translateX" : el.style.transform = 'translateX('+obj[i]+')';           break;
                    case "translateY" : el.style.transform = 'translateY('+obj[i]+')';           break;
                    case "rotate"     : el.style.transform = 'rotate('+obj[i]+')';               break;
                    // case "opacity"    : el.style.filter = 'opacity('+obj[i]+')'; break;
                    case "grayscale"  : el.style.filter    = 'grayscale('+obj[i]+')';            break;
                    case "invert"     : el.style.filter    = 'invert('+obj[i]+')';               break;
                    default           : el.style[i]        = obj[i];                             break;
                }
            }
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
        for(let i in o) {
            switch(i) {
                case "skew"         : this.style.transform = 'skew('+o[i]+','+o[i]+')';      break;
                case "skewX"        : this.style.transform = 'skewX('+o[i]+')';              break;
                case "skewY"        : this.style.transform = 'skewY('+o[i]+')';              break;
                case "scale"        : this.style.transform = 'scale('+o[i]+')';              break;
                case "scaleX"       : this.style.transform = 'scaleX('+o[i]+')';             break;
                case "scaleY"       : this.style.transform = 'scaleY('+o[i]+')';             break;
                case "translate"    : this.style.transform = 'translate('+o[i]+','+o[i]+')'; break;
                case "translateX"   : this.style.transform = 'translateX('+o[i]+')';         break;
                case "translateY"   : this.style.transform = 'translateY('+o[i]+')';         break;
                case "rotate"       : this.style.transform = 'rotate('+o[i]+')';             break;
                default             : this.style[i]        = o[i];                           break;
            }
        }
        if(fn!==null&&typeof fn=="function") setTimeout(fn.bind(this),16, this);
        return this
    }
    , text: function(t=null, fn=null){
        if(!t) return this.textContent;
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
    , aft: function(obj=null) {
        let
        el=this;
        if(Array.isArray(obj)) obj.each(o=>el.aft(o));
        else if(obj) el.insertAdjacentElement("afterend",obj);
        return this;
    }
    , bef: function(obj=null) {
        let
        el=this;
        if(Array.isArray(obj)) obj.each(o=>el.bef(o));
        else if(obj) el.insertAdjacentElement("beforebegin",obj);
        return this;
    }
    , app: function(obj=null) {
        let
        el=this;
        if(Array.isArray(obj)) obj.each(o=>el.app(o));
        else if(obj) el.insertAdjacentElement("beforeend",obj);
        return this;
    }
    , pre: function(obj=null) {
        let
        el=this;
        if(Array.isArray(obj)) obj.each(o=>el.pre(o));
        else if(obj) el.insertAdjacentElement("afterbegin",obj);
        return this;
    }
    , has: function(cls=null) {
        if(cls) return this.classList.contains(cls);
        return false
    }
    , dataSort: function(data=null,dir="asc") {
        let
        me = this,
        all = [].slice.call(this.children);
        if(all.length) {
            for(let i=all.length;i--;) {
                for(let j=0;j<i;j++) {
                    if((dir=="asc"&&(all[j].dataset[data]>all[j+1].dataset[data]))||(dir=="desc"&&(all[j].dataset[data]<all[j+1].dataset[data]))) {
                        let
                        tmp = all[j];
                        all[j] = all[j+1];
                        all[j+1] = tmp;
                    }
                }
            }
            all.each(x=>me.append(x))
        }
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
    , uid: function(hash = false, name=null) {
        if(name) this.id = name;
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
        return this.css({display:'inline-block'}, x=>x.anime({opacity:1}, len).then(fn))
    }
    , desappear: function(len = ANIMATION_LENGTH, remove = false, fn=null) {
        return this.anime({opacity:0}, len).then(x=>{ if(remove) x.remove(); else x.css({display:"none"}); if(fn) fn(remove ? null : this); });
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
            if(x) narr.push(x) 
        })
        return narr
    }
    , calc: function(type=SUM){
        let
        res = 0;
        switch (type){
            case (SUM): this.each(x=>res+=x); break;
            case (AVERAGE): this.each(x=>res+=x); res=res/this.length; break;
            case (HARMONIC): this.each(x=>res+=1/x); res=this.length/res; break;
        }
        return res;
    }
    , last: function() { return this.length ? this[this.length-1] : null; }
    , first: function() { return this.length ? this[0] : null; }
    , at: function(n=0) { return this.length>=n ? this[n] : null; }
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
        return this.extract(x=>{ return x.tagName.toLowerCase()=="input" ? x.value || " " : null})
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
            setTimeout(function(ok){ return pass(ok) }, pool.timeserie.calc(SUM)+ANIMATION_LENGTH/4, true)
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
        app.error("needs to be overwritten... =}")
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

        console.log(text, typeof text, this.node);
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
        if(obj){
            if(obj.css) this.node.css(obj.css);
            if(obj.class) this.node.addClass(obj.class);
        }
        return this
    }

    export(){ return this.node.mimic() }

    constructor(obj){
        this.emptyElement();
        if(obj){
            if(obj.title) this.title(obj.title);
            if(obj.icon) this.icon(obj.icon);
            if(obj.content) this.content(obj.content);
            if(obj.tags) this.tags(obj.tags);
            if(obj.class) this.node.toggleClass(obj.class);
            if(obj.css) this.node.css(obj.css);
        }
        
    }
}

class Tile  extends __BaseElement__ {
    emptyElement() {
        this.node = _("div", "-row -tile -no-scrolls", {
            borderRadius: ".25em"
            , boxShadow: "0 0 .5em rgba(0,0,0,.32)"
            , background: "#f0f0f0"
            , marginBottom: ".25em"
            , padding:".25em"
        }).app(
            _("header", "-row -keep", { borderBottom: "1px solid rgba(0,0,0,.32)", paddingBottom: ".25em" }).app(
                _("img", "-left -keep --icon", { width: "2.5em", height: "2.5em", padding: ".5em", scale: .8, opacity: .8 })
            ).app(
                _("b", "-left -content-left -ellipsis --title", { width: "calc(100% - 3em)", padding: ".75em 0", opacity: .8 })
            )
        ).app(
            _("section", "--content -row -content-left", { padding: ".25em 0" })
        ).app(
            _("footer", "-row --tags", { borderTop: "1px solid rgba(0,0,0,.32)" })
        )
        return this.node
    }
};

class Row  extends __BaseElement__ {
    emptyElement() {
        this.node = _("div", "-row -tile", {
            borderRadius: ".25em"
            , background: "#00000032"
        }).app(
            _("img", "-left -keep --icon", { width: "2em", height: "2em", scale: .8, opacity: .8 })
        ).app(
            _("div", "-left -content-left -ellipsis --content", { width: "calc(100% - 2.5em)", padding: ".5em 0" })
        )
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
            else if(this.__DOWN__) this.__DOWN__.fire();
        }
        this.x = this.y = null;
    }

    fire() {
        this.e&&this.e.on('touchmove', function(v) { this.move(v) }.bind(this));
    }
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
        let
        now = (new Date()).getTime();
        if (now - this.delay > this.timer) {
            eval(this.func)(d);
            this.timer = now;
        }
    }
};
class Bootloader {   
    pace(){
        var 
        i=0;
        for(;++i<Object.keys(this.loaders).length;);
        return 100/i;
    }
    loadLength(){
        var
        i=0
        , count=0
        , loaders = Object.keys(this.loaders);
        for(;i++<=loaders.length;) if(this.loaders[loaders[i-1]]) count++;
        return count*this.pace();
    }
    check(scr){
        return scr ? this.loaders[scr] : this.alreadyLoaded
    }
    ready(scr){
        if(scr) this.loaders[scr] = true;

        let
        perc = this.loadLength()
        , bootprogress = $(".--boot-progress");
        
        if(bootprogress.length) bootprogress.anime({width:(perc)+"%"})

        if(perc>=99&&!this.alreadyLoaded){ 
            this.onFinishLoading.fire(()=>{ return app ? app.pragma = app.initial_pragma : true; }, ANIMATION_LENGTH);
            this.alreadyLoaded=true; 
        }

        return this.alreadyLoaded || false;
    }
    constructor(){
        this.alreadyLoaded = false;
        this.loadComponents = new Pool();
        this.onFinishLoading = new Pool();
        this.loaders = {
            continuing: true 
        }
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
            args = args ? args : {};
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    o.status = xhr.status;
                    o.data = xhr.responseText.trim();
                   return accepted(o);
                };
            }
            head = head ? head : new Headers();
            xhr.open(method,url);   
            head["Content-Type"] = head["Content-Type"] || "text/plain"
            head["FA-Custom"] = "@rafsb"
            o.headers = head;
            // Object.keys(head).each(h=>xhr.setRequestHeader(h,head[h]));
            xhr.send(args.json());

        });
        return o;
    }

    async post(url, args, head=null){
        return this.call(url, args, "POST", head)
    }

    async load(url, args=null, target=null) {
        return this.call(url, args).then( r => {
            if(!r.status) return app.error("error loading "+url);
            r = r.data.prepare(app.colors()).morph();
            if(!target) target = app.get('body')[0];
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

    get(el,scop=document) { return [].slice.call(scop ? scop.querySelectorAll(el) : document.querySelectorAll(el)); }

    nuid(n=8, prefix="_") { 
        let 
        a = prefix;
        n -= a.length; 
        while(n>0 && n-->n) { a+="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789".split('')[parseInt((Math.random()*36)%36)] }
        return a 
    }

    loading(show = true, target= null) {
        if (!show) {
            $(".--default-loading").each(x => { clearInterval(x.dataset.animation); x.remove() });
            return
        }
        $(target || "body")[0].app(app.new("div","-fixed -view -zero --default-loading").css({ boxShadow:"none" }));

        // app.load("src/img/loading.svg", null, $(".--default-loading")[0], function () {
        //     let
        //     circle = $(".--default-loading .--loading-circle")[0];
        //     if (!circle) return;
        //     circle.css({ transformOrigin: "top left", scale: window.innerWidth / 1920, "stroke-dasharray": circle.getTotalLength() + "," + circle.getTotalLength() + "," + circle.getTotalLength() });
        //     $(".--default-loading")[0].dataset.animation = setInterval(() => {
        //         let
        //             circle = $(".--default-loading .--loading-circle")[0];
        //         if (circle) {
        //             circle.css({ "stroke-dashoffset": 0 });
        //             circle.anime({ "stroke-dashoffset": circle.getTotalLength() * 4 }, ANIMATION_LENGTH * 4)
        //         }
        //     }, 2201)
        // })
        
    }

    notify(n, c=null) {
        let
        toast = document.createElement("toast")
        , clr = app.colors();
        toast.addClass("-fixed -tile -content-left --notification").css({
            background: c&&c[0] ? c[0] : clr.CLOUDS
            , color: c&&c[1] ? c[1] : clr.BACKGROUND
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
                left:"80vw",
                width:"calc(20vw - 1em)",
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
        notfys = $("toast.--notification");
        notfys.each((x, i) => { x.anime({translateY:((toast.offsetHeight+8)*i+16)+"px", opacity:1}, ANIMATION_LENGTH/4) });
        toast.dataset.delay = setTimeout(function() { toast.desappear(ANIMATION_LENGTH/2,true); }, ANIMATION_LENGTH*5);
    }

    error(message=null) {
        app.notify(message || "Ops! Something went wrong...", [this.color_pallete.ALIZARIN,this.color_pallete.FONT])
    }
    success(message=null) {
        app.notify(message || "Hooray! Success!", [this.color_pallete.NEPHIRITIS, this.color_pallete.FONT])
    }
    advise(message = null) {
        app.notify(message || "Ops! take attention...", [this.color_pallete.ORANGE, this.color_pallete.FONT])
    }
    working(message = null) {
        app.notify(message || "Hooray! Success!", [this.color_pallete.MIDNIGHT_BLUE, this.color_pallete.FONT])
    }

    hintify(n=null, o={}, delall=true, keep=false, special=false, evenSpecial=false) {

        if(delall) $(".--hintifyied"+(evenSpecial?", .--hintifyied-sp":"")).each(x=>x.desappear(ANIMATION_LENGTH, true));

        o.top = o.top||o.top==0 ? o.top : (mouseAxis.y)+"px";
        o.left = o.left||o.left==0 ? o.left : (mouseAxis.x)+"px";
        o.padding = o.padding||o.padding==0 ? o.padding : ".5em";
        o.borderRadius = o.borderRadius ? o.borderRadius : ".25em";
        o.boxShadow =  o.boxShadow ? o.boxShadow :  "0 0 .5em "+app.colors().DARK1;
        o.background =  o.background ? o.background : this.colors().DARK4;
        o.color =  o.color ?  o.color : this.colors("FONT");
        o.fontSize = o.fontSize ? o.fontSize : "1em";

        if(typeof n == "string") n = ("<f>"+n+"</f>").morph()

        let
        toast = _("toast","-block -absolute --hintifyied"+(special?"-sp":""),o).css({opacity:0}).app(n||"<b>路路路!!!</b>".morph());
        if(toast.get(".--close").length) toast.get(".--close").at().on("click",function(){ this.upFind("toast").desappear(ANIMATION_LENGTH, true) })
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
        head = _("header","-row -left -content-left -zero",{background:app.colors().DARK3,padding:".25em", color:app.colors("FONT")}).text(n || "¬¬").app(
            _("div","-absolute -zero-tr -pointer --close -tile",{padding:".25em", fontWeight:"bolder"}).app(
                 _("img",null,{height:"1em",marginRight:".25em", filter:"invert(1)"}).attr({src:"src/img/icons/cross.svg"})
            )
        )
        , wrapper = _("div", "-wrapper -zero -no-scrolls",{background:"inherit",boxShadow:"0 0 1em "+app.colors().DARK2})
            .app(_("blur"))
            .app(head);

        if(html) wrapper.app(html);
        
        css["top"]        = css["top"]        || "4.5em";
        css["left"]       = css["left"]       || "2em";
        css["width"]      = css["width"]      || "calc(100vw - 4em)";
        css["height"]     = css["height"]     || "calc(100vh - 10em)";
        css["padding"]    = css["padding"]    || "none";
        css["background"] = css["background"] || "inherit";
        css["color"]      = css["color"]      || app.colors().WET_ASPHALT;

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
        if(!field) return false;
        if(value===null) return window.localStorage.getItem(field);
        window.localStorage.setItem(field,value);
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

    hashit(o){ if(typeof o == "object" || typeof o == "array") o = JSON.stringify(o); return { hash: btoa(o) } }

    async sleep(time=ANIMATION_LENGTH){
        return new Promise(function(ok){
            setTimeout(function(){ return ok() }, time)
        })
    }

    makeServerHashToken(o){ return this.hashit(o).hash; }

    rgb2hex(color) {
        let
        hex = "#";
        color.split(/[\s+,.-]/g).each(clr => {
            let
            tmp = (clr*1).toString(16);
            hex += tmp.length == 1 ? "0" + tmp : tmp;
        })
        return hex
    }

    hex2rgb(color) {
        let
        rgb = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(color);
        return rgb ? [ parseInt(rgb[1], 16), parseInt(rgb[2], 16), parseInt(rgb[3], 16) ] : null;
    }

    constructor() {
        this.initial_pragma = 0
        this.current        = 0
        this.last           = 0
        this.initPool        = new Pool()
        this.onPragmaChange = new Pool()
        this.nodes = document
        this.nodearray = []
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
            , WET_ASPHALT:"#34495E"
            , MIDNIGHT_BLUE:"#2D3E50"
            , CONCRETE:"#95A5A5"
            , ASBESTOS:"#7E8C8D"
            , AMETHYST:"#9C56B8"
            , WISTERIA:"#8F44AD"
            , CLOUDS:"#ECF0F1"
            , SILVER:"#BDC3C8"
            , PETER_RIVER:"#2C97DD"
            , BELIZE_HOLE:"#2A80B9"
            , ALIZARIN:"#E84C3D"
            , POMEGRANATE:"#C0382B"
            , EMERLAND:"#53D78B"
            , NEPHIRITIS:"#27AE61"
            , CARROT:"#E67D21"
            , PUMPKIN: "#D35313"
            , TURQUOISE:"#00BE9C"
            , GREEN_SEA:"#169F85"
            , SUN_FLOWER:"#F2C60F"
            , ORANGE: "#F39C19"
            , BURRO_QNDO_FOJE: "#8C887B"
        }
    }
};
bind(window, {
    mouseAxis: { x:0, y:0 }
    , $: function(wrapper=null, context=document){ return [].slice.call(context.querySelectorAll(wrapper)) }
    , _:function(node='div', cls="faau", style={ display: "inline-block" }, fn){ return app.new(node,cls,style,fn) }
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
                        , top           : (mouseAxis.y - bounds.height/2)+"px"
                        , left          : (mouseAxis.x - bounds.left - bounds.width/2)+"px"
                        , filter        : "invert(.2)"
                    }, x=>x.anime({scale:2},ANIMATION_LENGTH/2).then(x=>x.desappear(ANIMATION_LENGTH/4,true))))
                }).addClass("--effect-selector-attached")

            }
        })
    }
});
app.spy("pragma",function(x){
    app.last = app.current;
    app.current = x;
    if(bootloader&&!bootloader.ready()) return setTimeout((x)=>{ app.pragma = x }, ANIMATION_LENGTH, x);
    this.onPragmaChange.fire(x);
});
window.onmousemove = e =>{
    mouseAxis = { x: e.clientX, y: e.clientY  };
    app.mouseFire&&app.mouseFire.fire()
}
document.addEventListener("touchstart", function() {}, true);
console.log('  __\n\ / _| __ _  __ _ _   _\n\| |_ / _` |/ _` | | | |\n\|  _| (_| | (_| | |_| |\n\|_|  \\__,_|\\__,_|\\__,_|');