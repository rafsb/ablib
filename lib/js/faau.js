/**************************************************************************
                                                              _
 	 ___ _ __  _   _ _ __ ___   ___   ___ ___   __      _____| |__
	/ __| '_ \| | | | '_ ` _ \ / _ \ / __/ _ \  \ \ /\ / / _ \ '_ \
	\__ \ |_) | |_| | | | | | |  __/| (_| (_) |  \ V  V /  __/ |_) |
	|___/ .__/ \__,_|_| |_| |_|\___(_)___\___/    \_/\_/ \___|_.__/
	    |_|
  	 ___                                             _
 	/  _|_ __ __ _ _ __ ___   _____      _____  _ __| | __
	| |_| '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /
	|  _| | | (_| | | | | | |  __/\ V  V / (_) | |  |   <
	|_| |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\


****************************************************************************/
//                      _
//   ___ ___  _ __  ___| |_ ___
//  / __/ _ \| '_ \/ __| __/ __|
// | (_| (_) | | | \__ \ |_\__ \
//  \___\___/|_| |_|___/\__|___/
//

const
SP_ANIMDURATION = 400;
SP_RESPONSIVE_TRESHOLD = 1080;
DEBUG = true;

var
mouseAxis = { x:0, y:0 };

// 					_        _
//  _ __  _ __ ___ | |_ ___ | |_ _   _ _ __   ___  ___
// | '_ \| '__/ _ \| __/ _ \| __| | | | '_ \ / _ \/ __|
// | |_) | | | (_) | || (_) | |_| |_| | |_) |  __/\__ \
// | .__/|_|  \___/ \__\___/ \__|\__, | .__/ \___||___/
// |_|                           |___/|_|
//
/*
==> Animate any html or svg element with css animation capabilities */
Element.prototype.anime = function(o=null, len=SP_ANIMDURATION, fn = null, trans = null, delay = 0) {
    if (o===null) return this;
    // if(this.dataset.animationFunction) clearInterval(this.dataset.animationFunction);
    len/=1000;
    trans = trans ? trans : "ease";
    this.style.transition = "all " + len.toFixed(2) + "s "+trans;
    // console.log(("all " + len.toFixed(2) + "s "+trans+" "+(delay?delay/1000:0)+"s"));
    this.style.transitionDelay = (delay?delay/1000:0).toFixed(2)+"s";
    for(let i in o){
        switch(i){
            case "skew"  : this.style.transform = 'skew('+o[i]+','+o[i]+')'; break;
            case "skewX" : this.style.transform = 'skewX('+o[i]+')'; break;
            case "skewY" : this.style.transform = 'skewY('+o[i]+')'; break;
            case "scale" : this.style.transform = 'scale('+o[i]+')'; break;
            case "scaleX" : this.style.transform = 'scaleX('+o[i]+')'; break;
            case "scaleY" : this.style.transform = 'scaleY('+o[i]+')'; break;
            case "translate" : this.style.transform = 'translate('+o[i]+','+o[i]+')'; break;
            case "translateX" : this.style.transform = 'translateX('+o[i]+')'; break;
            case "translateY" : this.style.transform = 'translateY('+o[i]+')'; break;
            case "rotate" : this.style.transform = 'rotate('+o[i]+')'; break;
            default : this.style[i] = o[i]; break;
        }
    }
    if(fn!==null&&typeof fn=="function") this.dataset.animationFunction = setTimeout(fn,len*1000+delay+1,this);
    else this.dataset.animationFunction = "";
    return this
}

Element.prototype.empty = function(a=null){
    if(a){
        if(typeof a == 'string') a = a.split(',');
    }else a = []
    this.get("*").each((x)=>{if(!(a.indexOf(x.tagName)+1)) x.parentElement.removeChild(x)});
    return this;
}

Element.prototype.setStyle = function(o=null, fn = null) {
    if (o===null) return this;
    this.style.transition = "none";
    this.style.transitionDuration = 0;
    for(let i in o){
        switch(i){
            case "skew"  : this.style.transform = 'skew('+o[i]+','+o[i]+')'; break;
            case "skewX" : this.style.transform = 'skewX('+o[i]+')'; break;
            case "skewY" : this.style.transform = 'skewY('+o[i]+')'; break;
            case "scale" : this.style.transform = 'scale('+o[i]+')'; break;
            case "scaleX" : this.style.transform = 'scaleX('+o[i]+')'; break;
            case "scaleY" : this.style.transform = 'scaleY('+o[i]+')'; break;
            case "translate" : this.style.transform = 'translate('+o[i]+','+o[i]+')'; break;
            case "translateX" : this.style.transform = 'translateX('+o[i]+')'; break;
            case "translateY" : this.style.transform = 'translateY('+o[i]+')'; break;
            case "rotate" : this.style.transform = 'rotate('+o[i]+')'; break;
            default : this.style[i] = o[i]; break;
        }
    }
    if(fn!==null&&typeof fn=="function") fn(this);
    return this;
}

Element.prototype.setData = function(o=null, fn = null){
    if (o===null) return this;
    for(let i in o) this.dataset[i] = o[i];
    if(fn!==null&&typeof fn=="function") fn(this);
    return this;
}

Element.prototype.setAttr = function(o=null, fn = null){
    if (o===null) return this;
    for(let i in o) this.setAttribute(i,o[i]);
    if(fn!==null&&typeof fn=="function") fn(this);
    return this;
}

Element.prototype.aft = function(obj=null){
    if(obj) this.insertAdjacentElement("afterend",obj);
    return this
}

Element.prototype.bef = function(obj=null){
    if(obj) this.insertAdjacentElement("beforebegin",obj);
    return this
}

Element.prototype.app = function(obj=null){
    if(obj) this.insertAdjacentElement("beforeend",obj);
    return this
}

Element.prototype.pre = function(obj=null){
    if(obj) this.insertAdjacentElement("afterbegin",obj);
    return this
}

Element.prototype.dataSort = function(data=null,dir="asc"){
    let
    me = this,
    all = [].slice.call(this.children);
    if(all.length){
        for(var i=all.length;i--;){
            for(var j=0;j<i;j++){
                if((dir=="asc"&&(all[j].dataset[data]>all[j+1].dataset[data]))||(dir=="desc"&&(all[j].dataset[data]<all[j+1].dataset[data]))) {
                    let
                    tmp = all[j];
                    all[j] = all[j+1];
                    all[j+1] = tmp;
                }
            }
        }
        all.each((x)=>{ me.app(x) })
    }
    return this
};

Element.prototype.index = function(){
    return [].slice.call(this.parent().children).indexOf(this)-1;
}

// returns a String encrypted, ex.: "rafael".hash()
String.prototype.hash = function() {
    let
    h = 0, c = "", i = 0, j = this.length;
    if (!j) return h;
    while (i++ < j) {
        c = this.charCodeAt(i - 1);
        h = ((h << 5) - h) + c;
        h |= 0;
    }
    return Math.abs(h).toString();
};

// returns a String encrypted, ex.: "rafael".hash()
String.prototype.json = function() {
    let
    result = null;
    try{
        result = JSON.parse(this);
    } catch(e) {
        // statements
        console.log(e);
    }
    return result;
};

// Object.prototype.delay = function(len=null){
// 	if(len) this.dataset.animeuntill = (new Date()).getTime() + len;
// 	var
// 	x = this.dataset.animeuntill? parseInt(this.dataset.animeuntill) : null;
// 	//console.log(x);
// 	if(!x||(new Date()).getTime() > x) return this;
// 	else return this.delay();
// }

/*
==> Transmute an ordinary string into an html elemnt */
String.prototype.morph = function() {
    let
    x = document.createElement("div");
    x.innerHTML = this.replace(/\t+/g, "").trim();
    return x.firstChild;
};

Element.prototype.on = function(action,fn,passive=true){
    this.addEventListener(action,fn, {passive:passive})
    return this
};

Element.prototype.parent = function(pace=1){
    let
    tmp = this;
    while(pace--) tmp = tmp.parentElement;
    return tmp;
}

Element.prototype.inPage = function() {
    var
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
};

/*
==> Make a container element with overflow-y's scrollable scrolls to a given 'el' element */
Element.prototype.scrollTo = function(el) {
    if (!el) return -1;
    var length = 0;
    do {
        length += el.offsetTop;
        el = el.parentElement;
    } while (el.uid() != this.uid());
    this.scroll({top:length,behavior:"smooth"});
};

Element.prototype.stopScroll = function(){
    this.scroll({top:this.scrollTop+1});
}

Element.prototype.get = function(el){
	if(el) return this.querySelectorAll(el);
	else return this;
}


Element.prototype.remClass = function(c) {
    if (this.classList.contains(c)) {
        this.classList.remove(c);
    }
    return this;
};

Element.prototype.addClass = function(c) {
    var
    tmp = c.split(/\s+/g), i=tmp.length;
    while(i--) this.classList.add(tmp[i]);
    return this;
};

Element.prototype.toggleClass = function(c) {
    var
    tmp = c.split(/\s+/g), i=tmp.length;
    while(i--) {
      if (tmp[i]){
        if(!this.classList.contains(tmp[i]))
          this.classList.add(tmp[i]); else this.classList.remove(tmp[i]);
        }
      } return this;
};

Element.prototype.uid = function(name=null){
	if(name) this.id = name;
	if(!this.id) this.id = spu.nuid(8);
	return this.id;
}


Element.prototype.appear = function(len = SP_ANIMDURATION){
    this.setStyle({transition:"none",display:'inline', opacity:0});
    this.anime({opacity:1},len);
}

Element.prototype.desappear = function(len = SP_ANIMDURATION, remove = false){
    this.anime({opacity:0},len,(me)=>{ if(remove&&me&&me.parent()) me.parent().removeChild(me); else me.style.display = "none" });
}

Element.prototype.remove = function(){ this.parent().removeChild(this) }

Element.prototype.at = function(i=0){
    return this.nodearray.at(i)
};

Array.prototype.each = function(fn){ if(fn){ for(var i=0;i++<this.length;) fn(this[i-1],i-1); } return this }

Array.prototype.last = function(){ return this.length ? this[this.length-1] : null; }

Array.prototype.first = function(){ return this.length ? this[0] : null; }

Array.prototype.at = function(n=0){ return this.length>=n ? this[n] : null; }

NodeList.prototype.each = function(fn){
	if(fn){ for(var i=0;i++<this.length;) fn(this[i-1],i-1); }
    return this
}

NodeList.prototype.on = function(act=null,fn=null){
    if(act&&fn)this.each((x)=>{ x.on(act,fn) });
    return this
};

NodeList.prototype.first = function(){ return this.length ? this[0] : null; }

NodeList.prototype.last = function(){ return this.length ? this[this.length-1] : null; }

NodeList.prototype.at = function(n=0){ return this.length>=n ? this[n] : null; }

NodeList.prototype.anime = function(obj,len=SP_ANIMDURATION,fn=null,trans=null,delay=null){
    this.each((x)=>{x.anime(obj,len,fn,trans,delay)});
    return this
}
NodeList.prototype.setStyle = function(obj,fn=null){
    this.each((x)=>{x.setStyle(obj,fn)});
    return this
}

NodeList.prototype.setData = function(obj,fn=null){
    this.each((x)=>{x.setData(obj,fn)});
    return this
}

NodeList.prototype.addClass = function(cl=null){
    if(cl) this.each((x)=>{x.addClass(cl)});
    return this
}

NodeList.prototype.remClass = function(cl=null){
    if(cl) this.each((x)=>{x.remClass(cl)});
    return this
}

NodeList.prototype.toggleClass = function(cl=null){
    if(cl) this.each((x)=>{x.toggleClass(cl)});
    return this
}


NodeList.prototype.remove = function(){
    this.each((x)=>{x.parentElement.removeChild(x)});
    return this
}

HTMLCollection.prototype.each = function(fn){
    if(fn){ for(var i=0;i++<this.length;) fn(this[i-1],i-1); }
    return this
}

HTMLFormElement.prototype.json = function(){
    var
    json = {};
    this.get("input, select, textarea").each(function(el){
        json[el.name] = el.value;
    })
    return json;
};

Object.defineProperty(Object.prototype, "spy", {
    value: function (p,fn) {
        var
        o = this[p]
        , n = o
        , get = function(){ return n }
        , set = function(v){ o = n; return n = fn(v,p,this) };
        if(delete this[p]){ // can't watch constants
            Object.defineProperty(this,p,{ get: get, set: set })
        }
    }
});

// object.unwatch
Object.defineProperty(Object.prototype, "unspy", {
    value: function (prop) {
        var
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
    add(x=null,v=null){
        if(x){
            if(Array.isArray(x)) this.sort(x);
            if(typeof x === 'function'){ 
                this.execution.push(x);
                if(this.execution.length > this.timeline.length) this.at(v)
            }
            else this.conf(x,v)
        }
        return this;
    }
    push(x){
        this.add(x);
        return this
    }
    sort(x){
        if(Array.isArray(x)){
            x.each((x)=>{ this.add(x) })
        }
        return this;
    }
    conf(k=null,v=null){
        if(k!==null){            
            if(v!==null) this.setup[k]=v;
        }
        return this
    }
    at(t=null){
        this.moment = t&&parseInt(t) ? t : this.moment+1;
        this.timeline.push(this.moment);
        return this
    }
    plus(t=0){ return this.at(this.moment +t) }
    fire(x){
        this.execution.each((f,i)=>{ 
            this.timeserie.push(setTimeout(f,this.timeline[i]+(typeof x == 'number' ? (i+1)*x : 0),this.setup,i)) 
            // f(this.setup,i) 
        });
        if(typeof x == 'function') setTimeout(x,this.moment+1,this.setup);
        return this
    }
    stop(i=null){
        if(i!==null){ if(this.timeserie[i]) clearInterval(this.timeserie[i]) }
        else this.timeserie.each((x)=>{ clearInterval(x) })
        return this
    }
    clear(){
        this.stop();
        this.moment = 0;
        this.timeline = [];
        this.timeserie = [];
        this.execution = [];
        this.setup = {};
        return this
    }
    debug(){
        console.log("CONFIGURATION");
        console.log(this.setup);
        console.log("TIMESERIE");
        this.timeline.each((x,i)=>{console.log("AT:"+x+" => DO:"+this.execution[i])})
    }
    after(fn=null){
        if(fn&&typeof fn=='function') setTimeout(fn,this.moment+1);
        return this
    }
    constructor(x){
        this.moment = 0;
        this.timeline = [];
        this.timeserie = [];
        this.execution = [];
        this.setup = {};
        return this.add(x)
    }
}

/*
 * @class
 *
 * handle the minimum amount of time to wait until executions of a given function
 * good to prevent events like scroll and typing to fire some actions multiple
 * times decreasing performance affecting user's experience
 *
 */
class THROTTLE {
    /*
     * @constructor
     *
     * f = javascript function to be applied
     * t = time betwin executions of 'f' (250ms is the default)
     * ex.: new __self.Throttle(minha_funcao,400);
     *
     */
    constructor(f, t = SP_ANIMDURATION/2) {
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
    apply(d) {
        let
        now = (new Date()).getTime()    ;
        if (now - this.delay > this.timer) {
            eval(this.func)(d);
            this.timer = now;
        }
    }
}

class SPUME {
	call(url, args=null, fn=false, sync=false, __VP = window.innerWidth>SP_RESPONSIVE_TRESHOLD) {
        var
        xhr = new XMLHttpRequest();
        args = args ? args : {};
        if(!sync&&fn){
	        xhr.onreadystatechange = function() {
	            if (xhr.readyState == 4) {
	                fn({ status: xhr.status, data: xhr.responseText.trim(), url:url, args:args });
	            };
	        }
	    }
        xhr.open("POST", url, !sync);
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.send(JSON.stringify(args));
        if(sync) {
            let
            o = { status: xhr.status, data: xhr.responseText.trim(), url:url, args:args };
            return (fn ? fn(o) : o);
        }
    }

    load(url, args=null, target=null, fn=false, sync=false){
    	this.call(url, args, function(r){
    		if(r.status) r = r.data.morph();
            if(!r.id) r.id = spu.nuid();
    		var
    		tmp = r.get("script");
    		if(!target) target = spu.get('body')[0];
    		target.appendChild(r);
    		if(tmp.length){
    			for(var i=0;i++<tmp.length;){ eval(tmp[i-1].textContent); }
    		}
    		if(fn) fn({id:r.id,data:r});
            else spu.get("#"+r.id).first().anime({opacity:1},600);
    	}, sync);
    }

	get(el,scop=null){ return scop ? scop.querySelectorAll(el) : this.nodes.querySelectorAll(el); }

	nuid(n=8){ var a = "SP"; n-=2; while(n-->0){ a+="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789".split('')[parseInt((Math.random()*36)%36)]; } return a; }

    notify(n, c=null){
        let
        toast = document.createElement("toast");
        toast.setStyle({
            background: c&&c[0] ? c[0] : "rgba(255,255,255,.8)",
            color: c&&c[1] ? c[1] : "black",
            boxShadow:"0 0 8px gray",
            zIndex:200000,
            display:'block',
            opacity:0,
            position:"fixed"
        }).innerHTML = n ? n : "Hello <b>World</b>!!!";
        if(window.innerWidth>SP_RESPONSIVE_TRESHOLD){
            toast.setStyle({
                top:0,
                left:"78vw",
                width:"calc(20vw - 4rem)",
                padding:"2rem",
                borderRadius:"3px",
            });
        }else{
            toast.setStyle({
                opacity:0,
                top:".5rem",
                left:".5rem",
                width:"calc(100% - 4rem)",
                padding:"1.5rem",
            });
        }
        toast.onclick = function() { clearTimeout(this.dataset.delay);this.desappear(400,true); };
        toast.onmouseenter = function() { clearTimeout(this.dataset.delay); };
        toast.onmouseleave = function() {
            this.dataset.delay = setTimeout(function(t) { t.desappear(400,true); }, 1000, this);
        };
        document.getElementsByTagName('body')[0].appendChild(toast);
        let
        notfys = spu.get("toast");

        notfys.each((x,i)=>{x.anime({ top: ( ( toast.offsetHeight + 8 ) * i + 16) + "px", opacity: 1 }, 220) });
        toast.dataset.delay = setTimeout(function() { toast.desappear(400,true); }, 4000);
    }

    hintify(n, o={},delall=true,keep=false,special=false,evenSpecial=false){
        if(delall) $(".--hintifyied"+(evenSpecial?", .--hintifyied-sp":"")).each((x)=>{x.parent().removeChild(x)});
        let
        toast = document.createElement("toast");
        n = (typeof n == 'string' ? n.morph() : n);
        o.display = 'block';
        o.opacity = 0;
        o.zIndex = 100000;
        o.top = o.top ? o.top : (mouseAxis.y+24)+"px";
        o.left = o.left ? o.left : (mouseAxis.x+24)+"px";
        o.padding = o.padding ? o.padding : ".5rem";
        o.borderRadius = o.borderRadius ? o.borderRadius : "3px";
        o.boxShadow =  o.boxShadow ? o.boxShadow :  "0 0 8px gray";
        o.background =  o.background ? o.background : "rgba(40,40,40,.92)";
        o.color =  o.color ?  o.color : "white";
        o.position = "fixed";
        o.fontSize = o.fontSize ? o.fontSize : "1rem";
        toast.setStyle(o).addClass("--hintifyied"+(special?"-sp":"")).appendChild(n ? n : ("<b>···</b>!!!").morph());
        toast.on("click",function() { this.desappear(400,true) });
        if(!keep) toast.on("mouseleave",function() {$(".--hintifyied"+(special?", .--hintifyied-sp":"")).each((x)=>{x.parent().removeChild(x)}) });
        toast.anime({opacity:1});
        $('body').at().appendChild(toast);
    }

    apply(fn,obj){ return (fn ? eval(fn)(obj) : null); }

    get(w=null,c=null){ this.nodearray = $(w,c); return this }

    get length() { return this.nodearray.length }

    each(fn=null){this.nodearray.each(fn);return this }

    at(n=0){ return this.nodearray.at(n) }

    first(){return this.nodearray.first()}

    last(){return this.nodearray.last()}

    empty(except=null){ this.nodearray.each((x)=>{x.empty(except)})}

    remove(){ this.nodearray.each((x)=>{x.remove()})}

    anime(obj,len=SP_ANIMDURATION,fn=null,trans=null,delay=null){
        this.nodearray.each((x,y)=>{x.anime(obj,len,fn,trans,delay)})
        return this;
    }

    constructor(wrapper,context){
        this.nodes = document;
        this.nodearray = [];
        if(wrapper){
            var el = (context ? (typeof context == 'string' ? document.querySelectorAll(context)[0] : context) : document);
            this.nodearray = el ? el.querySelectorAll(wrapper) : [];
        }
    }
}

window.spu = new SPUME();
window.$ = function(wrapper=null,context=document){
    return (new SPUME(wrapper,context)).nodearray;
}

// try{
//     if(SVG){
//         SVG.extend(SVG.Text, {
//           path: function(d) {
//               var
//               track, path  = new SVG.TextPath;
//               if (d instanceof SVG.Path) track = d;
//               else track = this.doc().defs().path(d);
//               while (this.node.hasChildNodes()) path.node.appendChild(this.node.firstChild);
//               this.node.appendChild(path.node);
//               path.attr('href', '#' + track, SVG.xlink);
//               return this;
//           }
//         });
//     }
// }catch(e){ console.log(e) }

window.onmousemove = (e) => mouseAxis = { x: e.clientX, y: e.clientY }

console.log(' ___ _ __  _   _ _ __ ___   ___   ___ ___  \n/ __| \'_ \\| | | | \'_ ` _ \\ / _ \\ / __/ _ \\ \n\\__ \\ |_) | |_| | | | | | |  __/| (_| (_) |\n|___/ .__/ \\__,_|_| |_| |_|\\___(_)___\\___/ \n    |_|');
