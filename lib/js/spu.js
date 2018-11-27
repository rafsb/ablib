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
SP_ANIMDURATION = 800;
SP_RESPONSIVE_TRESHOLD = 1080;

// 					_        _                         
//  _ __  _ __ ___ | |_ ___ | |_ _   _ _ __   ___  ___ 
// | '_ \| '__/ _ \| __/ _ \| __| | | | '_ \ / _ \/ __|
// | |_) | | | (_) | || (_) | |_| |_| | |_) |  __/\__ \
// | .__/|_|  \___/ \__\___/ \__|\__, | .__/ \___||___/
// |_|                           |___/|_|
//
/*
==> Animate any html or svg element with css animation capabilities */
Object.prototype.anime = function(o=null, len=SP_ANIMDURATION, fn = null, trans = 'ease') {
    if (o===null) return;
    this.dataset.animeuntill = (new Date()).getTime()+len;
    len/=1000;
    this.style.transition = "all " + len + "s "+trans;
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
    if(fn!==null&&typeof fn=="function") this.dataset.animationFunction = setTimeout(fn,len*1000,this);
    return this;
}

Object.prototype.delay = function(len=null){
	if(len) this.dataset.animeuntill = (new Date()).getTime() + len;
	var
	x = this.dataset.animeuntill? parseInt(this.dataset.animeuntill) : null;
	//console.log(x);
	if(!x||(new Date()).getTime() > x) return this;
	else return this.delay();
}

/*
==> Transmute an ordinary string into an html elemnt */
String.prototype.morph = function() {
    var
    x = document.createElement("div");
    x.innerHTML = this.replace(/\t+/g, "").trim();
    return x.firstChild;
};

HTMLElement.prototype.parent = function(pace=1){
    var tmp = this;
    while(pace--) tmp = tmp.parentElement;
    return tmp;
}

HTMLElement.prototype.inPage = function() {
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
    //console.log(page,element);
    return (element.top <= page.bottom + 1 && element.bottom >= page.top - 1) ? {
        offset: element.top - page.top,
        where: 1 - (element.top - page.top) / page.height
    } : false;
};

/*
==> Make a container element with overflow-y's scrollable scrolls to a given 'el' element */
HTMLElement.prototype.scrollTo = function(el) {
    if (!el) return -1;
    var length = 0;
    do {
        length += el.offsetTop;
        el = el.parentElement;
    } while (el.uid() != this.uid());
    this.scroll({top:length,behavior:"smooth"});
};

HTMLElement.prototype.stopScroll = function(){
    this.scroll({top:this.scrollTop+1});
}

HTMLElement.prototype.get = function(el){
	if(el) return this.querySelectorAll(el);
	else return this;
}

HTMLElement.prototype.remClass = function(c) {
    if (this.classList.contains(c)) {
        var
            tmp = this.className.split(/\s+/g);
        tmp.splice(this.className.split(/\s+/g).indexOf(c), 1);
        this.className = tmp.join(" ");
        return this.className;
    } else return false;
};

HTMLElement.prototype.addClass = function(c) {
    if (!this.classList.contains(c)) this.className += " " + c;
    else return false;
};

HTMLElement.prototype.get = function(el){ return this.querySelectorAll(el); }

HTMLElement.prototype.uid = function(name=null){
	if(name) this.id = name;
	if(!this.id) this.id = spu.nuid(8);
	return this.id;
}

NodeList.prototype.each = function(fn){
	if(fn){ for(var i=0;i++<this.length;) fn(this[i-1],i-1); }
}

NodeList.prototype.first = function(){ return this.length ? this[0] : null; }

NodeList.prototype.last = function(){ return this.length ? this[this.length-1] : null; }

NodeList.prototype.at = function(n=0){ return this.length>=n ? this[n] : null; }

HTMLFormElement.prototype.json = function(){
    var
    json = {};
    this.get("input, select, textarea").each(function(el){
        json[el.name] = el.value;
    })
    return json;
};

//       _                         
//   ___| | __ _ ___ ___  ___  ___ 
//  / __| |/ _` / __/ __|/ _ \/ __|
// | (__| | (_| \__ \__ \  __/\__ \
//  \___|_|\__,_|___/___/\___||___/
//


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
        var
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

    notify(n, c = ["white", "black"]){
        let
        toast = document.createElement("toast");
        toast.style.background = c[0] ? c[0] : "rgba(255,255,255,.8)";
        toast.style.color = c[1] ? c[1] : "black";
        toast.innerHTML = n ? n : "Hello World!!!";
        if(window.innerWidth>SP_RESPONSIVE_TRESHOLD){
            toast.anime({
                opacity:0,
                position:"fixed",
                top:0,
                left:"78vw",
                width:"20vw",
                padding:".5rem",
                zIndex:10000,
                borderRadius:"3px",
                boxShadow:"0 0 8px gray"
            },1);
        }else{
            toast.anime({
                opacity:0,
                position:"fixed",
                top:".5rem",
                left:".5rem",
                width:"calc(100% - 4rem)",
                padding:"1.5rem",
                zIndex:10000,
                boxShadow:"0 0 8px gray"
            },1);
        }
        toast.onclick = function() { this.parentElement.removeChild(this); };
        toast.onmouseenter = function() { clearTimeout(this.dataset.delay); };
        toast.onmouseleave = function() {
            this.dataset.delay = setTimeout(function(t) { t.parentElement.removeChild(t); }, 2000, this);
        };
        spu.get('body')[0].appendChild(toast);
        let
        notfys = spu.get("toast");
        for (let i=notfys.length; i--;){
            notfys[i].anime({ top: (notfys[i].offsetTop + toast.offsetHeight + 8) + "px", opacity: 1 }, 220);
        }
        toast.dataset.delay = setTimeout(function() { toast.remove(); }, 8000);
    }

    modal(obj=null, fn=null, template="default", scrolls=false){
        this.load('../etc/modal.php',{data:obj,template:template,scrolls:scrolls},null,fn);
    }

    apply(fn,obj){ return (fn ? eval(fn)(obj) : null); }

    constructor(){ this.nodes = document; }
}

//  _       _ _   
// (_)_ __ (_) |_ 
// | | '_ \| | __|
// | | | | | | |_ 
// |_|_| |_|_|\__|
//
window.spu = new SPUME();
console.log(' ___ _ __  _   _ _ __ ___   ___   ___ ___  \n/ __| \'_ \\| | | | \'_ ` _ \\ / _ \\ / __/ _ \\ \n\\__ \\ |_) | |_| | | | | | |  __/| (_| (_) |\n|___/ .__/ \\__,_|_| |_| |_|\\___(_)___\\___/ \n    |_|');