/* jshint esversion:6*/
/* jshint -W034 */

/* 
 * CONFIGURATION
 */
const AB_RESPONSIVENESS_THRESHOLD = 700;

/* 
 * CALL TYPES
 */
const AB_SYS = "aboxaboxaboxaboxaboxaboxaboxabox";
const AB_SYNC = true;
const AB_LOAD = true;
const AB_ASYNC = false;
const AB_NOLOAD = false;
const AB_IN = true;
const AB_ON = true;
const AB_OUT = false;
const AB_OFF = false;
const AB_DESTROY = true;
const AB_DEBUG = "DBG";
/* 
 * ABOX PECULIAR TYPES
 */
const AB_FIELD = 0;
const AB_INDEX = 1;
const AB_FOLDER = 2;
const AB_FILE = 3;
const AB_FLOAT = 4;
const AB_INTEGER = 5;
const AB_STRING = 6;
const AB_BINARY = 7;
const AB_ABSOLUTE = 8;
const AB_ARRAY = 9;
const AB_JSON = 10;
const AB_OBJECT = 11;
const AB_LOCAL = 12;
const AB_GLOBAL = 13;
const AB_ASSOC = 14;
const AB_MYSQLI_OBJ = 15;
const AB_NOABSOLUTE = 16;

/* 
 * HANDLER MODES
 */
const AB_NEW = 0;
const AB_EDIT = 1;
const AB_REMOVE = 2;
const AB_VIEW = 3;
const AB_REPLACE = 4;
const AB_APPEND = 5;
const AB_PREPEND = 6;
const AB_RECURSIVE = true;
const AB_NORECURSIVE = false;
const AB_FORCE = true;
const AB_NOFORCE = false
const AB_STANDARD = false;
const AB_PORTRAIT = false;
const AB_LANDSCAPE = true;
const AB_LOG = true;
const AB_NOLOG = false;
const AB_MINIFY = true;
const AB_NOMINIFY = false;
/* 
 * ENVIROMENTS
 */
const AB_SESSION = 1;
const AB_COOKIE = 2;

/* 
 * OUATH CLIENTS
 */
const AB_OLX = 1;
const AB_FACEBOOK = 2;
const AB_INSTAGRAM = 3;
const AB_GOOGLE = 4;

/* 
 * SPECIAL ELEMENTS ENUMERATOR
 */
const AB_TEXT = 1;
const AB_COMBO = 2;
const AB_TILE = 3;
const AB_ROW = 4;
const AB_WINDOW = 5;
const AB_PANEL = 6;
const AB_DIALOG = 7;
const AB_WRAPPER = 8;

/* 
 * XHR CALLBACK STATUS
 */
const AB_SUCCESS = 200;

/* 
 * WINDOW STATE
 */
const AB_NORMAL = 0;
const AB_MINIMIZED = 1;
const AB_MAXIMIZED = 2;


const AB_PUBLIC = 0;
const AB_USER = 1;
const AB_MANAGER = 2;
const AB_DIRECTOR = 3;
const AB_TI1 = 4;
const AB_TI2 = 5;
const AB_ADMIN = 6;
const AB_OWNER = 7;
const AB_ROOT = 8;
const AB_DEVELOPER = 9;

// returns a String encrypted, ex.: "rafael".hash()
String.prototype.hash = function() {
    var h = 0,
        c = "",
        i = 0,
        j = this.length;
    if (!j) return h;
    while (i++ < j) {
        c = this.charCodeAt(i - 1);
        h = ((h << 5) - h) + c;
        h |= 0;
    }
    return Math.abs(h).toString();
};

String.prototype.uton = function() { return this.replace(".php", "").replace(".htm", "").replace(".html", "").replace(".xhtml", "").replace(/[^a-zA-Z0-9]/g, ""); };
// Object.prototype.atou = function(){ return Object.keys(this).map(key => key + '=' + this[key]).join('&'); }
// return a intger value of an element such as input, for example
String.prototype.int = function() {
    if (!this) return 0;
    var
        a = this;
    if (a.split(/\./g).length > 1 && a.indexOf(",") < 0) a = a.replace(/[^0-9]/g, "");
    if (a.indexOf(",") < a.indexOf(".")) a = parseInt(a.replace(/[,]/g, "").replace(/[^-0-9.]/g, ""));
    else a = parseInt(a.replace(/[.]/g, "").replace(/[,]/g, ".").replace(/[^-0-9.]/g, ""));
    return a * 1;
};

String.prototype.float = function(f = 2) {
    var
        a = this;
    if (!a || a == "") return 0.00;
    if (a.indexOf(",") < a.indexOf(".")) a = parseFloat(a.replace(/\,/g, "").replace(/[^0-9.]/g, ""));
    else a = parseFloat(a.replace(/[.]/g, "").replace(/[,]/g, ".").replace(/[^-0-9.]/g, ""));
    return a;
};

String.prototype.money = function() {
    var
        t = this.replace(/[^0-9]/g, "").split(""),
        m = t.splice(t.length - 2, 2);
    return "R$ " + t.join("").replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.") + "," + m.join("");
};

String.prototype.date = function() {
    var
        t = this;
    t = t.split("-").reverse().join("").replace(/[^0-9]/g, "");
    if (t.length > 2) {
        t = [t.slice(0, 2), t.slice(2, 4), t.slice(4, 8)].join("/"); // jshint ignore:line
    }
    return t;
};

// masks with an hour format
String.prototype.hour = function() {
    var
        t = this.replace(/[^0-9]/g, "");
    if (t.length > 2) return [t.slice(0, 2), t.slice(2, 4)].join(":"); // jshint ignore:line
};

// masks with an phone format, supports both: "(99) 9999 9999" and "(99) 9 9999 9999" autmatically
String.prototype.phone = function() {
    if (this.length > 4) {
        var
            t = this.replace(/[^0-9]/g, "");
        if (t.length <= 10) t = ["(", t.slice(0, 2), ")", t.slice(2, 6), "-", t.slice(6, 10)].join("");
        else t = ["(", t.slice(0, 2), ")", t.slice(2, 7), "-", t.slice(7, 11)].join("");
        return t;
    }
    return this;
};

// format the input into some documents maks as CPF and CNPJ (brazilian docs)
String.prototype.document = function() {
    if (this.length > 2) {
        var
            t = this.replace(/[^0-9]/g, "");
        if (t.length < 10) t = [t.slice(0, 2), ".", t.slice(2, 5), ".", t.slice(5, 8), "-", t.slice(8)].join("");
        else if (t.length < 12) t = [t.slice(0, 3), ".", t.slice(3, 6), ".", t.slice(6, 9), "-", t.slice(9)].join("");
        else t = [t.slice(0, 2), ".", t.slice(2, 5), ".", t.slice(5, 8), "/", t.slice(8, 12), "-", t.slice(12, 14)].join("");
        return t ? t : null;
    }
    return this;
};

// only accept basic characyers, as upper and lower case alphanumerical and underslash
String.prototype.basicChar = function() { return this.replace(/\s+/g, "_").replace(/[^a-zA-Z0-9_@.]/g, ""); };

String.prototype.toDOM = function() {
    var
        x = document.createElement("div");
    x.innerHTML = this.replace(/\t+/g, "").trim();
    return x.firstChild;
};

String.prototype.ucwords = function(){
    var 
    tmp = this.split(/\s+/g);
    for(var i=0;++i<tmp.length;){
        tmp[i-1] = tmp[i-1].toLowerCase();
        tmp[i-1] = tmp[i-1][0].toUpperCase()+tmp[i-1].substring(1,tmp[i-1].length);
    }
    return tmp.join(" ");
};

Number.prototype.int = function() {
    if (!this) return 0;
    return parseInt(this);
};

Number.prototype.float = function() {
    if (this == null || this == undefined || !this) return 0.00;
    return parseFloat(this);
};

Number.prototype.money = function() { return this ? this.toString().money() : "R$ 0.00"; };

HTMLInputElement.prototype.up = function(
    name = null,
    path = null, // relative path
    func = null,
    mode = AB_REPLACE, // 5=AB_REPLACE,6=ab.mopdes.APPEND
    mini = AB_NOMINIFY,
    forc = AB_NOFORCE, // f could be ab.modes.FORCE
) {
    if (!this.value || !this.files.length || !path) return;
    ab.loading(AB_IN);
    //console.log(this.files[0].size);
    if (this.files[0].size > 1024 * 1024 * 2) {
        ab.error("Ops! Arquivo muito grande...");
        ab.loading(0);
        return;
    }
    var ctnr = this.myId();
    name = name || ab.newId(13);
    var form = new FormData(),
        counter = 0;;
    form.append("pic0", this.files[0]);
    form.append("name", name);
    form.append("path", path);
    form.append("mode", mode);
    form.append("forc", forc);
    form.append("mini", mini);
    xhr = new XMLHttpRequest();
    xhr.onprogress = function(d){
        var
        x = document.getElementsByClassName("-progress");
        if(x.length){
            for(var i=x.length;i--;){
                x[i].style.width = (d.loaded/d.total*100)+"%";
            }
        }
    }
    if (func) xhr.upload.onload = function() {
        var timer = setInterval(function() {
            if (xhr.responseText || counter++ == 10000) {
                eval(func)(JSON.parse(xhr.responseText));
                ab.loading(AB_OUT);
                clearInterval(timer);
                //__self.notify(xhr.responseText+", at "+counter);
            }
            if (counter >= 100000) {
                ab.loading(AB_OUT);
                ab.error();
                clearInterval(timer);
            }
        }, 10);
    }
    xhr.upload.onerror = function() {
        ab.error("Ops! Não foi possível subir esta imagem, tente novamente mais tarde...");
        ab.loading(AB_OUT);
    };
    xhr.open("POST", "lib/up.php");
    xhr.send(form);
};

HTMLElement.prototype.myId = function() {
    if (!this.id) {
        this.id = (new Abox()).newId(4);
    }
    return this.id;
};

HTMLElement.prototype.styleSheet = function(d, v) {
    var
        x = window.getComputedStyle(this);
    if (d) return x.getPropertyValue(d);
    return x;
};

HTMLElement.prototype.idx = function() {
    var nodes = Array.prototype.slice.call(this.parentElement.children);
    return nodes.indexOf(this);
};

HTMLElement.prototype.delete = function() { this.parentElement.removeChild(this); };

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

HTMLElement.prototype.trans = function(o = null, len = 80.0, fn = null) {
    if (o === null) return;
    //console.log(o);
    var
    pace = window.innerWidth>700?10:20;
    len /= pace;
    var
        iter = 0,
        el = this,
        cv = el.styleSheet(),
        pl = o.left != null ? (o.left.float() - cv.getPropertyValue("left").float()) / len : 0,
        pt = o.top != null ? (o.top.float() - cv.getPropertyValue("top").float()) / len : 0,
        pw = o.width != null ? (o.width.float() - cv.getPropertyValue("width").float()) / len : 0,
        ph = o.height != null ? (o.height.float() - cv.getPropertyValue("height").float()) / len : 0,
        pa = o.alpha != null ? (o.alpha.float() - cv.getPropertyValue("opacity").float()) / len : 0;
    //if(o.height) console.log((o.height-el.offsetHeight)/len,ph);
    //if(o.alpha!==null) console.log(o.alpha.float(),"-",el.style.opacity.float(),"=",o.alpha.float()-el.style.opacity.float());
    //el.style.display = "inline-block";
    //if(o.height) console.log(el,o.height,el.offsetHeigth,len,ph);
    if (el.dataset.transition) clearInterval(el.dataset.transition);
    el.dataset.transition = setInterval(function() {
        if (iter++ >= len) {
            clearInterval(el.dataset.transition);
            el.dataset.transition = "";
            
            if(pt) el.style.top     = o.top.int()    + "px";
            if(pl) el.style.left    = o.left.int()   + "px";
            if(pw) el.style.width   = o.width.int()  + "px";
            if(ph) el.style.height  = o.height.int() + "px";
            if(pa) el.style.opacity = o.alpha.float();
            
            //if(ph) console.log("final "+ph+", "+el.style.height);
            if (fn) setTimeout(fn, 10);
        } else {
            var
                x = el.styleSheet();
            if (pt) el.style.top = (x.getPropertyValue("top").float() + pt) + "px";
            if (pl) el.style.left = (x.getPropertyValue("left").float() + pl) + "px";
            if (pw) el.style.width = (x.getPropertyValue("width").float() + pw) + "px";
            if (ph) el.style.height = (x.getPropertyValue("height").float() + ph) + "px";
            if (pa) el.style.opacity = (x.getPropertyValue("opacity").float() + pa).toString();
            //if(ph) console.login(ph,el.style.height,(is_img?el.height:el.offsetHeight + ph).int());
            //if(pa) console.log(pa,x.getPropertyValue("opacity"),pa.float()+x.getPropertyValue("opacity").float());
        }
    }, pace);
};

HTMLElement.prototype.stopTrans = function() { if (this.dataset.transition) clearInterval(this.dataset.transition); return this; };

// fills the [-fill]'s select tag with given content'
HTMLElement.prototype.fill = function(c = false) {
    if (this.tagName != "SELECT" || this.classList.contains("-filled") || !this.dataset.tablefield) return;
    this.addClass("-filled");
    var
        id = this.myId(),
        tmp = this.dataset.tablefield.split(/[;:]/g),
        fn = this.dataset.callback ? this.dataset.callback.replace(/::this/g, "document.getElementById('" + this.myId() + "')") : null;
    if (tmp.length > 1) {
        ab.call(
            "../lib/ctrl/qsfill.php", {
                tabl: tmp[0],
                fild: tmp[1],
                rest: (tmp[2] ? tmp[2] : null),
                selt: (tmp[3] ? tmp[3] : null),
                fnam: (tmp[4] ? tmp[4] : null),
            },
            function(d) {
                var
                    x = document.getElementById(id),
                    t = document.createElement("option");
                if (d.status == 200) d = JSON.parse(d.data);
                else { if (fn) eval(fn); return; }
                x.innerHTML = "";
                t.value = "";
                t.textContent = "";
                t.setAttribute("selected", "selected");
                x.appendChild(t);
                if (d.length) {
                    for (var i in d) {
                        var
                            t = document.createElement("option");
                        t.value = d[i].value;
                        t.textContent = d[i].text;
                        if (d[i].selected.int()) t.setAttribute("selected", "selected");
                        x.appendChild(t);
                    }
                    if (d[i].selected.int()) x.value = d[i].value;
                }
                if (fn) eval(fn);
                if (c) x.blur();
            }, false, false
        );
    } else this.appendChild((new DOMParser()).parseFromString('<option value=""></option>', "text/xml"));
    this.remClass("-fill");
    //console.log(this.tagName,this.className,this.dataset.tablefield);
};

HTMLElement.prototype.refill = function(c = false) {
    this.addClass("-fill");
    this.remClass("-filled");
    this.fill(c);
}

// return an object eith { offset:"pixels", where:"percentual" } if the elemenet is positioned on the viewport
HTMLElement.prototype.inPage = function() {
    with($(window)) { // jshint ignore:line
        var page = {
            top: scrollTop(),
            bottom: scrollTop() + ab.h(),
            height: ab.h()
        };
    }
    var element = {
        top: $(this).offset().top,
        bottom: $(this).offset().top + $(this).height()
    };
    if (element.top <= page.bottom + 1 && element.bottom >= page.top - 1) { // jshint ignore:line
        return {
            offset: element.top - page.top, // jshint ignore:line
            where: 1 - (element.top - page.top) / page.height // jshint ignore:line
        };
    } else {
        return false;
    }
};

HTMLElement.prototype.scrollTo = function(el, t = 200) {
    if (!el) return -1;
    t /= 10;
    var
        __self = this,
        count = 0,
        pace = 0,
        length = 0;
    do {
        //console.log(length);
        length += el.offsetTop;
        el = el.parentElement;
    } while (el.myId() != this.myId());
    pace = (length - this.scrollTop) / t;
    //console.log(this.scrollTop,length,pace);
    clearInterval(this.dataset.scrolling);
    this.dataset.scrolling = setInterval(function() {
        if (++count >= t) clearInterval(__self.dataset.scrolling);
        __self.scrollTop = __self.scrollTop + pace;
    }, 4);
};

HTMLElement.prototype.stopScroll = function() { if (this.dataset.scrolling) clearInterval(this.dataset.scrolling); return this; };

HTMLElement.prototype.stop = function() {
    this.stopScroll();
    this.stopTrans();
    return this;
}

// EFFECTS
HTMLElement.prototype.appear = function(t = 180, opos = null) {
    var
    x = this;
    ot = opos || x.offsetTop;
    x.style.opacity = "0";
    x.style.display = "inline-block";
    x.style.top = (ot + (this.animationsRange || 16)).toString() + "px";
    x.trans({ top: ot, alpha: 1 }, t, this.isModal() ? function() {
        if (!x.dataset.initialposition) x.dataset.initialposition = x.offsetTop + "," + x.offsetLeft + "," + x.offsetHeight + "," + x.offsetWidth;
    } : null);
    if (x.isModal()) ab.reorder(x.myId());
    ab.organize();
};

HTMLElement.prototype.desappear = function(t = 180, r = null) {
    var
    ot = this.offsetTop;
    this.trans({ top: ot + (this.animationsRange || 16), alpha: 0 }, t);
    if (ab && this.isModal()) {
        ab.windows.set(null, ab.windows.idx(this.myId()));
        ab.reorder();
    }
    setTimeout(function(r, d, o) {
        if (d.dataset.moving) {
            clearInterval(d.dataset.moving);
            d.dataset.moving = '';
        }
        if (r) {
            if (eval("ab.tmpfs." + d.myId())) eval("delete ab.tmpfs." + d.myId());
            d.delete();
        } else {
            d.style.top = o + "px";
            d.style.display = "none";
        }
    }, t + 10, r, this, ot);
};

HTMLElement.prototype.checkout = function() {
    if (!this.classList.contains('-required') || ["input", "select", "textarea"].indexOf(this.tagName.toLowerCase()) < 0) return -1;
    var
        o = this.classList.contains('-controlled') ? this.dataset.object.split(/[;:,-]/g) : null;
    //console.log(o);
    if (!this.value) {
        if (o) eval(o[0]).value(o[1], "");
        this.style.border = "2px solid red";
        return false;
    } else {
        var
            prs = this.value ? this.value.replace(/["']/g, "&quot;") : '',
            idx = 0;
        if (prs && o && o.length == 3) {
            if (o[2] == "bool") prs = prs ? 1 : 0;
            if (o[2] == "int") prs = prs.int();
            if (o[2] == "float") prs = prs.float();
        }
        if (o && o.length == 4) idx = o[3].int();
        if (o) eval(o[0]).value(o[1].trim(), prs, idx); // jshint ignore:line
        this.style.border = "2px solid green";
        return true;
    }
    if (!this.value) { this.style.border = "2px solid red"; return false; } else { this.style.border = "2px solid green"; return true; }
};

HTMLElement.prototype.isModal = function() {
    if (!this.className) return false;
    var
        mod = ["-window", "-panel", "-dialog", "-wrapper"],
        arr = this.className.split(" "),
        i = 0,
        j = 0,
        matches = false;
    while (arr[i++] && !matches) {
        while (mod[j++] && !matches) {
            if (arr[i - 1] == mod[j - 1]) matches = { p: j - 1 };
        }
        j = 0;
    }
    return matches ? { which: mod[matches.p] } : false;
}

HTMLElement.prototype.parentModal = function() {
    var el = this,
        z = false;
    while (el && !z) {
        if (el.isModal()) z = el;
        el = el.parentElement;
    }
    return z;
};

HTMLElement.prototype.minimize = function(d = 80) {
    var
        x = this.isModal() ? this : this.parentModal();
    if (x && (!x.dataset.windowsstatus || x.dataset.windowsstatus != 0)) {
        if (!x.dataset.initialposition) x.dataset.initialposition = x.offsetTop + "," + x.offsetLeft + "," + x.offsetHeight + "," + x.offsetWidth;
        x.getElementsByClassName("-controlbox")[0].style.display = 'none';
        if (!ab.tray.has(x.myId())) ab.tray.push(x.myId());
        x.trans({ top: ab.h(87.5), left: 0, width: ab.w(10), height: ab.h(5), opacity: 1 }, 80, function() { ab.reorder(x.myId()); });
        x.dataset.windowsstatus = '0';
        $(x).draggable('disable');
    } else x.restore();
};

HTMLElement.prototype.maximize = function(d = 80) {
    var
        x = this.isModal() ? this : this.parentModal();
    if (x) {
        if (!x.dataset.windowsstatus || x.dataset.windowsstatus != 1) {
            if (!x.dataset.initialposition) x.dataset.initialposition = x.offsetTop + "," + x.offsetLeft + "," + x.offsetHeight + "," + x.offsetWidth;
            x.getElementsByClassName("-controlbox")[0].style.display = 'inline';
            if (ab.tray.has(x.myId())) ab.tray.drop(x.myId());
            x.trans({ top: 0, left: 0, width: ab.w(), height: ab.h(88), opacity: 1 }, 80, function() { ab.reorder(x.myId()); });
            x.dataset.windowsstatus = '1';
            $(x).draggable('disable');
        } else x.restore();
    }
};

HTMLElement.prototype.restore = function(d = 80) {
    var
        x = this.isModal() ? this : this.parentModal();
    if (x) {
        if (!x.dataset.initialposition) x.dataset.initialposition = x.offsetTop + "," + x.offsetLeft + "," + x.offsetHeight + "," + x.offsetWidth;
        pos = x.dataset.initialposition.split(/,/g);
        x.getElementsByClassName("-controlbox")[0].style.display = 'inline-block';
        if (ab.tray.has(x.myId())) ab.tray.drop(x.myId());
        //console.log("restoring "+x.myId());
        x.trans({ top: pos[0].int(), left: pos[1].int(), width: pos[3].int(), height: pos[2].int(), opacity: 1 }, 80, function() { ab.reorder(x.myId()); });
        x.dataset.windowsstatus = '2';
        $(x).draggable('enable');
    }
}

HTMLElement.prototype.climb = function(n = -1) {
    var
        el = this;
    if (n < 0) {
        for (var i = n * -1; i--;)
            if (el.parentElement) el = el.parentElement;
    } else if (n > 0) {
        for (var i = n; i--;)
            if (el.firstChild) el = el.firstChild;
    }
    return el;
};

/*
 * @class 
 *
 * Handle and store touples or matrixes comming from backend as an json object
 * Can interact with [handle] tag attributes to update registers on client side
 * thougth can be send to backend hole as a complete touple to be stored
 *
 */
class Response {
    constructor(s, d) {
        this.status_ = s;
        this.data_ = d;
    }
    status(s) { return (s ? s === this.status_ : this.$status_); }
    data() { return this.data_; }
}

class Auth {
    constructor(m = null) {
        this.mod = m;
        this.uri = "../lib/auth/";
        switch (this.mod) {
            case (this.AuthClient.OLX):
                this.uri += "olx";
                break;
            case (this.AuthClient.FACEBOOK):
                this.uri += "facebook";
                break;
            case (this.AuthClient.INSTAGRAM):
                this.uri += "instagram";
                break;
            case (this.AuthClient.GOOGLE):
                this.uri += "google";
                break;
        }
        this.uri += ".php";
    }

    exec(o = null, fn = null) {
        if (!o) return 0;
        return ab.exec(this.uri, o, function(d) {
            if (fn) return fn(d);
        });
    }

    authenticate(fn = null) {
        ab.loading(1);
        return ab.exec(this.uri, {
            mode: "auth"
        }, function(d) {
            var w = window.open(JSON.parse(d).auth_url, 'Autenticação oAuth2', '_blank,width=400,height=300', 'toolbar=0, location=0, menubar=0');
            var timer = setInterval(function() {
                if (w.closed) {
                    clearInterval(timer);
                    if (fn) return fn();
                }
            }, 400);
        });
    }


    handShake(fn = null) {
        return this.exec({
            mode: "hshk"
        }, fn);
    }

    getInfo(fn = null) {
        return this.exec({
            mode: "info"
        }, fn);
    }

    publish(c = null, fn = null) {
        return this.exec({
            mode: "publ",
            code: c
        }, fn);
    }

    remove(c = null, fn = null) {
        return this.exec({
            mode: "remv",
            code: c
        }, fn);
    }

    status(c = null, fn = null) {
        return this.exec({
            mode: "stts",
            code: c
        }, fn);
    }

    easyAuth(fn = null) {
        return this.authenticate(function() {
            return (new Auth(this.AuthClient.OLX)).handShake(function(d) {
                if (fn) fn(d);
            });
        });
    }
}

class Pool {
    constructor(o) {
        this.obj_ = o && Array.isArray(o) ? o : [];
        this.current = this.obj_[0];
    }
    push(el) {
        if (el) {
            if (!this.obj_.indexOf(el) > -1) {
                this.obj_.push(el);
                this.last = this.current;
                this.current = this.obj_[this.obj_.length - 1];
            } else{ this.last = this.current; this.current = this.obj_[this.obj_.indexOf(el)]; }
            return this.current;
        } else return false;
    }
    drop(el = 0, idx = false) {
        var
        i = null;
        if (idx) { if (this.obj_[el] || el === -1) i = el === -1 ? this.obj_.length - 1 : el; } else { if (this.obj_.indexOf(el) > -1) i = this.obj_.indexOf(el); }
        if (el != -1) this.current = null;
        return this.obj_.splice(i, 1);
    }
    pop() {
        this.current = this.obj_[this.obj_.length - 2];
        return this.drop(-1, true);
    }
    get(el = 0) {
        el = el == -1 ? this.obj_.length - 1 : el;
        this.last = this.current;
        this.current = this.obj_[el] ? this.obj_[el] : null;
        return this.current;
    }
    has(el) { return this.obj_.indexOf(el) > -1 ? true : false; }
    set(el, idx = 0) {
        if (el) this.obj_[idx] = el;
        this.last = this.current;
        this.current = this.obj_[idx];
        return this.current;
    }
    idx(el) {
        if (el) {
            var i = this.obj_.indexOf(el);
            this.last = this.current;
            this.current = this.obj_[i] ? this.obj_[i] : null;
        }
        return i;
    }
    length() { return this.obj_.length; }
    reset() {
        this.obj_ = [];
        this.last = this.current = null;
    }
    release(t) {
        var tmp = this.obj_[t];
        if (tmp) this.obj_[t] = null;
        this.last = this.current = null;
        return tmp;
    }
    arr(i = null) { return i ? this.obj_[i] : this.obj_; }
    setCurrent(idx = 0) { this.last = this.current; this.current = this.obj_[idx] ? this.obj_[idx] : null; return this.current; }
}

/*
 * @class 
 *
 * Handle and store touples or matrixes comming from backend as an json object
 * Can interact with [handle] tag attributes to update registers on client side
 * thougth can be send to backend hole as a complete touple to be stored
 *
 */
class Data {

    /*
     * @constructor
     *
     * table 
     * field
     * restrictions
     * ordering
     * controller
     *
     * ex.: new _Data({table:"Tabela", restrictions : "nome,idade,peso", ordering : "idade asc", controller : "ex/meu/controlador.php");
     *
     */
    constructor(o = { limit: [0, 1000], conf: null, load: null, scope: new Abox() }) {
        this.bindobj_ = {
            template: null,
            target: null,
            function: null,
            index: null
        };
        this.init(o);
    }

    /*
     * @member function
     *
     * Initialize the inner object quering information for the 'multiverse'
     * t = table 
     * f = field
     * r = restrictions
     * o = element in which the result will be Ordered
     * ex.: _Data.init("Tabela", "nome,idade,peso", "code=1", "idade asc");
     * or an empty set, ex.: _Data.init()
     *
     */
    init(o = null) {
        //console.log(o);
        this.clear();
        if (!o) return;
        this.scope(o.scope);
        this.limitRange(o.limit ? o.limit : [0, 1000]);
        if (this.configFile(o.conf ? o.conf : null) && this.loadMode(o.load ? o.load : null)) this.load(o);
        if (this.configFile_ && !this.loadMode_) o.data ? this.obj[0] = o.data : this.obj[0].code = this.scope_.newId(32);
        //console.log(this.configFile());
        //console.log(o);
    }

    /*
     * @member function
     *
     * Return the entire inner object's array given by the index ('i')
     *
     */
    innerObj(i = null) {
        if (!this.obj) return null;
        if (i === null) return this.obj;
        return (this.obj[i.int()] ? this.clone(i.int()) : 0);
    }

    scope(o) {
        this.scope_ = o ? o : ab;
        return this.scope_;
    }

    run(fn = null, idx = 0, cf = null) {
        if (cf || this.configFile_) {
            this.scope_.call("../etc/handler.php", { conf: cf ? cf : this.configFile_, data: this.obj[idx] }, fn, false, true);
            return this;
        } else return false;
    }

    /*
     * @member function
     *
     * Generic also traditional setter or getter
     * f = field into the object's touple
     * v = value to be assign
     * i = (optional) index of the touple on a matrix context, it will vary due to your query
     * GET ex.: _Data.attr("nome");
     * SET ex.: _Data.attr("nome", "Rafael");
     *
     */
    value(f, v = null, i = 0) {
        if (!this.obj.length) return -1;
        if (this.obj.length <= i) i = this.obj.length;
        if (v !== null) this.obj[i][f] = v;
        if (this.obj[i][f]) return this.obj[i][f];
        return 0;
    }

    /*
     * @member function
     *
     * Resets the inner object to an empty set
     *
     */
    clear() { this.obj = [{ code: null }]; }

    /*
     * @member function
     *
     * Checks the number of rows on a matrix context, return 1 if is a touple
     *
     */
    rows() { return this.obj.length; }

    configFile(t = null) {
        if (t) this.configFile_ = t;
        return (this.configFile_ ? this.configFile_ : null);
    }

    loadMode(q = null) {
        if (q) this.loadMode_ = q;
        return (this.loadMode_ ? this.loadMode_ : null);
    }

    limitRange(l = null) {
        if (l && Array.isArray(l)) this.limitRange_ = l;
        return (this.limitRange_ ? this.limitRange_ : [0, 100]);
    }

    load(o = null, fn = null) {
        var
        __self = this;
        if (o.conf) this.configFile(o.conf);
        if (o.load) this.loadMode(o.load);
        if (o.limit) this.limitRange(o.limit);
        if (!this.configFile_ || !this.loadMode_) return null;
        //console.log(o.data);
        this.scope_.call("../etc/loader.php", { conf: this.configFile_, data: o.data ? o.data : null, limit: this.limitRange_ }, function(d) {
            if (d.status == 200) __self.obj = JSON.parse(d.data);
            if (fn) eval(fn)(__self.obj);
            if (o.func) eval(o.func)(__self.obj);
        }, true, false);
    }
    /*
    * @member function
    *
    */
    bind(tmp, tgt, fn = null, idx = 0) {
        var
            callback;
        if (typeof tmp == 'string') this.scope_.call(tmp, null, function(result) { tmp = result.status == 200 ? result.data.toDOM() : null; }, true, true);
        this.bindobj_ = {
            template: tmp || this.bindobj_.template,
            target: tgt || this.bindobj_.target,
            function: fn || this.bindobj_.function,
            index: idx || this.bindobj_.index
        };
        tmp = this.bindobj_.template;
        tgt = this.bindobj_.target;
        fn = this.bindobj_.function;
        idx = this.bindobj_.index;
        if (!tmp) return -1;
        if (!tgt) return -2;
        else {
            tgt.style.opacity = '0';
            if (tgt.dataset.callback) callback = tgt.dataset.callback.replace(/::this/g, "document.getElementById('" + tgt.myId() + "')").trim();
            //console.log(tgt.dataset.callback);
        }
        ab.wait(80);
        tgt.innerHTML = "";
        if (this.obj.length && this.obj[0].code != null) {
            if (idx != null && this.obj[idx] && this.obj[idx].code != null) { this.obj = [this.obj[idx]]; } else if (idx != null && !this.obj[idx]) {
                tgt.appendChild("<div class='rel tct ns' style='margin:0 1vw;opacity:1;'><icon class='xpd fxx fspan disabled' style='opacity:.5'>&#xe02e;</icon></div>".toDOM());
                tgt.appear(80);
                return;
            }
            for (var i = 0; i++ < this.obj.length;) {
                //console.log(tmp);
                var
                    obj = this.obj[i - 1],
                    tile = tmp.cloneNode(true),
                    binds = tile.getElementsByClassName("-bind");
                if (!obj) break;
                tile.id = obj.code;
                for (var j = binds.length; j--;) {
                    var
                        fieldValue = eval("obj." + binds[j].dataset.field);
                    if (fieldValue) {
                        fieldValue = fieldValue.replace("/","\/");
                        if (binds[j].tagName == "INPUT") binds[j].value = fieldValue;
                        else if (binds[j].classList.contains("-switched")) binds[j].dataset.state = fieldValue.int() ? "1" : "0";
                        else if (binds[j].tagName == "IMG") {
                            if (!binds[j].classList.contains("-nd")) {
                                binds[j].style.display = fieldValue ? "inline-block" : "none";
                                if (fieldValue.split(/[\/.a-zA-Z_]/g).length) { binds[j].src = fieldValue; }
                            }
                        } else binds[j].innerText = fieldValue;
                        binds[j].dataset.bindedvalue = fieldValue;
                    }
                    binds[j].dataset.bindedobjectindex = i;
                    binds[j].addClass("-binded");
                    binds[j].remClass("-bind");
                }
                tgt.appendChild(tile);
            }
            if (fn) fn.apply();
        } else tgt.appendChild("<div class='rel tct ns' style='margin:0 1vw;opacity:1;'><icon class='xpd fxx fspan disabled' style='opacity:.5'>&#xe02e;</icon></div>".toDOM());
        if (callback) eval(callback);
        setTimeout(function() { tgt.appear(80); }, 200);
    }

    updateBind(fn = this.bindobj.function, idx = null) {
        var
            rang = idx >= 0 ? [this.obj[idx]] : this.obj;
        for (var i = 0; i++ < rang.length;) {
            var
                x = rang[i - 1],
                t = x.getElementById(rang[i - 1].code),
                b = t.getElementsByClassName("-binded");
            for (var i in b) {
                if (typeof b[i] == "object") {
                    b[i].style.opacity = "0";
                    var
                        z = eval("x." + b[i].dataset.field);
                    if (z) {
                        if (b[i].tagName.toLowerCase() == "input") b[i].value = z;
                        else if (b[i].classList.contains("-switched")) b[i].dataset.state = z.int() ? "1" : "0";
                        else if (b[i].tagName.toLowerCase() == "img") b[i].style.display = z.int() ? "inline-block" : "none";
                        else b[i].innerText = z;
                    }
                    b[i].appear();
                }
            }
        }
        if (fn) eval(fn);
    }

    remove(idx = 0) {
        if (!this.obj[idx]) return -1;
        delete this.obj[idx];
        var
            tmp = this.obj;
        this.obj = [];
        for (var i = tmp.length; i--;) {
            if (tmp[i]) this.obj.push(tmp[i]);
        }
        return this.obj;
    }

    clone(idx = 0, detach = false) {
        if (typeof idx == "string") {
            var x = this.obj.length;
            while (x-- || this.obj[x].code == idx);
            if (x == -1) return x;
            idx = x.int();
        }
        var tmp = new Data();
        tmp.configFile(this.configFile_);
        tmp.limitRange(this.limitRange_);
        tmp.scope(this.scope_);
        tmp.loadMode(this.loadMode_);
        tmp.bindobj_ = this.bindobj_;
        tmp.obj = [this.obj[idx]];
        if (detach) this.remove(idx);
        return tmp;
    }

    pop(idx = 0) { return this.clone(idx, true); }

    push(elmnt = null) {
        if (elmnt && elmnt.code) {
            this.obj.push(elmnt);
            return 1;
        }
        return 0;
    }

    find(code = null, pop = false) {
        if (code && this.obj.length) {
            for (var i = 0; i++ < this.obj.length;) {
                if (this.obj[i - 1].code == code) return pop ? this.clone(i - 1, pop) : this.obj[i - 1];
            }
            return 0;
        }
    }
}

/*
 * @class 
 *
 * Handle dates considering brazilian date format
 *
 */
class Datetime {
    /*
     * @constructor
     *
     * d = date on brazilian format
     * ex.: new _Date("12/02/1988");
     *
     */
    constructor(d) {
        return this.init(d);
    }

    /*
     * @member function
     *
     * set anew value to inner date
     * d = date value in brazilian format
     * ex.: (new _Date()).assign("12/02/1988")
     *
     */
    init(d = 0) {
            if (typeof d == 'string' && d.indexOf("-") > 1) {
                this.__date__ = d.split("-").reverse().join("/");
            } else {
                this.__date__ = (d.date ? d.date : this.today(0));
                this.__time__ = (d.time ? d.time : this.now(0));
            }
            return this.datetime();
        }
        /*
         * @member function
         *
         * returns current date as string
         *
         */
    today(c = 0) {
        var tmp = (
            ((new Date()).getDate() < 9 ? "0" + ((new Date()).getDate()) : (new Date()).getDate()) + "/" +
            ((new Date()).getMonth() < 9 ? "0" + ((new Date()).getMonth() + 1) : (new Date()).getMonth() + 1) + "/" +
            (new Date()).getFullYear()
        );
        if (c) this.__date__ = tmp;
        return tmp;
    }

    now(c = 0) {
            var a = new Date();
            var tmp = (
                (a.getHours() < 10 ? "0" : "") + a.getHours() + ":" +
                (a.getMinutes() < 10 ? "0" : "") + a.getMinutes() + ":" +
                (a.getSeconds() < 10 ? "0" : "") + a.getSeconds()
            );
            if (c) this.__time__ = tmp;
            return tmp;
        }
        /*
         * @member function
         *
         * increment count in 'n' days
         * n = number of days to increment
         * ex.: (new _Date("12/02/1988")).plus(10) // returns '22/12/1988'
         *
         */
    plus(n = 1, c = 0) {
        if (!this.__date__) this.today(true);
        var tmp = this.__date__.split("/");
        tmp = new Date(tmp[2], tmp[1] - 1, tmp[0]);
        tmp.setDate(tmp.getDate() + n);
        tmp = (
            (tmp.getDate() < 10 ? "0" + (tmp.getDate()) : tmp.getDate()) + "/" +
            (tmp.getMonth() < 10 ? "0" + (tmp.getMonth() + 1) : tmp.getMonth() + 1) + "/" +
            tmp.getFullYear()
        );
        if (c) this.date(tmp);
        return tmp;
    }

    /*
     * @member function
     *
     * reformat the inner date to international format
     * ex.: (new _Date("12/02/1988")).intl() // returns '1988-02-12'
     *
     */
    intl(d = 0, c = 0) {
        if (!d && !this.__date__) return false;
        var tmp = "";
        if (d) {
            if (c) this.__date__ = d;
            tmp = d.split("/");
        } else {
            tmp = this.__date__.split("/");
        }
        tmp = (tmp[2] + "-" + tmp[1] + "-" + tmp[0]);
        return tmp;
    }

    /*
     * @member function
     *
     * returns inner date respective epoch value
     *
     */
    days(d = 0, t = AB_FLOAT, c = 0) {
        if (!d && !this.__date__) return false;
        var tmp = "";
        if (d) {
            tmp = d.split("/");
            if (c) this.__date__ = d;
        } else {
            tmp = this.__date__.split("/");
        }
        if (tmp.length !== 3) return -1;
        var year = (new Date()).getFullYear().float();
        //console.log(tmp);
        var days = (tmp[0].float() + (30.44 * (tmp[1].int() - 1)).float() + (365.24 * year).float());
        return (t == AB_INTEGER ? days.int() : days.float());
    }

    /*
     * @member function
     *
     * returns a comparassion of given and inner dates, in number of days
     * d = date value in brazilian format
     * a = true for absolute, false to allow negative result
     * ex.: (new Date("12/02/1988")).cmp("22/02/1988") // returns 10
     *
     */
    cmp(d = 0, a = AB_ABSOLUTE) {
            if (!this.__date__) return false;
            var tmp;
            if (d) {
                tmp = new Datetime(d);
            } else {
                tmp = new Datetime(this.today());
            }
            tmp = this.days(0, AB_INTEGER) - tmp.days(0, AB_INTEGER);
            return (a == AB_ABSOLUTE ? Math.abs(tmp) : tmp);
        }
        /*
         * @member function
         *
         * returns a the inner value as a getter
         * ex.: (new Date("12/02/1988")).date() // returns "12/02/1988"
         *
         */
    date(d = 0) {
        if (d) {
            if (d == "today") this.today(1);
            else this.__date__ = d;
        }
        return this.__date__;
    }

    /*
     * @member function
     *
     * returns a the inner value as a getter
     * ex.: (new Date("12/02/1988")).date() // returns "12/02/1988"
     *
     */
    time(h = 0) {
        if (h)
            if (h == "now") this.now(1);
            else this.__time__ = h;
        else this.now(1);
        if (this.__time__) return this.__time__;
        return false;
    }

    datetime(d = 1, t = 1) {
        if (this.__date__) {
            if (this.__time__) {
                return this.__date__ + "_" + this.__time__;
            } else {
                return this.__date__ + "_" + this.now(t);
            }
        } else {
            if (this.__time__) {
                return this.today(d) + "_" + this.__time__;
            } else {
                return this.today(d) + "_" + this.now(t);
            }
        }
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
class Throttle {
    /*
     * @constructor
     *
     * f = javascript function to be applied
     * t = time betwin executions of 'f' (250ms is the default)
     * ex.: new __self.Throttle(minha_funcao,400); 
     *
     */
    constructor(f, t = 256, poff = true) {
        this.assign(f, t, poff);
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
    assign(f, t, p) {
        this.func = f;
        this.delay = t;
        this.putOff = p;
        this.timer = (new Date()).getTime();
    }

    /*
     * @member function
     *
     * execute given function assigned on constructor or assign() mmber function
     * ex.: (new __self.Throttle).fire()
     * obs.: the fire() member function will only execute the inner function if the
     * given ammount of time is passed, otherway if won't do anything
     *
     */
    fire(d) {
        var
            now = (new Date()).getTime();
        if (now - this.delay > this.timer) {
            eval(this.func)(d);
            this.timer = now;
        }
    }
}

class Abox {

    constructor() {
        var
            __self = this;
        this.schema = null;
        this.config = null;
        this.mousePos = { top: null, left: null };
        this.user = null;
        this.viewport = null;
        this.animationsRange = 16;
        this.windows = new Pool();
        this.tray = new Pool();
        this.loadPool = new Pool();
        this.tmpfs = new Pool();
        // INITIALIZERS
        var
            mousetrack = new Throttle(function(e) {
                ab.mousePos.left = e.clientX;
                ab.mousePos.top = e.clientY;
            }, 50);
        window.addEventListener("mousemove", function(e) { mousetrack.fire(e); }, { passive: true });
    }

    w(n = 100) { return (window.outerWidth / 100) * n; };

    h(n = 100) { return (window.outerHeight / 100) * n; };

    body() { return document.getElementsByTagName("body")[0]; };

    data(o) {
        if (!o) o = {};
        o.scope = this;
        return new Data(o);
    }

    date(o) { return new Datetime(o); }

    /*
     * @global functions
     *
     * Returns the "n" percentual of window measures (width and height)
     * in pixels. Usefull while working with elements positioning
     *
     */
    reorder(t) {
        var
            tmp = new Pool();
        for (var i = -1; ++i < this.windows.length();) { var el = this.windows.get(i); if (el && el != t && document.getElementById(el)) tmp.push(el); }
        this.windows = tmp;
        if (t) this.windows.push(t);
        for (var i = -1; ++i < this.windows.length();) { var el = document.getElementById(this.windows.get(i)); if (el) el.style.zIndex = 1000 + i; }
        tmp = new Pool();
        for (var i = this.tray.length(); i--;) { var el = this.tray.get(i); if (el && document.getElementById(el)) tmp.push(el); }
        for (var i = this.tray.length(); i--;) {
            var
                x = document.getElementById(this.tray.get(i));
            if (x) {
                x.style.zIndex = 1100 + i;
                x.trans({ top: ab.h(87.5), left: this.tray.length() ? i * ab.w(10) : 0, width: ab.w(10), opacity: 1 }, 80);
            }
        }
    }

    /*
     * Recalculate page's elements positions
     * Implements [_Data-map] handler switching from desktop to mobile presentation
     * Load handlers used for some measurements
     * Attach listener on window resize events to be executed
     *
     */
    organize() {
        /*
        maps = document.getElementsByClassName("-");
        if(maps.length){
           for(var i=maps.length;i--;){
                maps[i].reClass("-");
            }
        }
        */
        this.viewport = (this.w() < AB_RESPONSIVENESS_THRESHOLD) ? AB_PORTRAIT : AB_LANDSCAPE;
        
        var
        maps = document.querySelectorAll("[class*='-map'");
        if(maps.length) {
            for (var i = maps.length; i--;) {
                //console.log(maps[i].classList);
                if(maps[i].classList.contains("-mapped")) return;
                maps[i].dataset.landscape = "";
                maps[i].dataset.portrait = "";
                maps[i].dataset.classes = "";
                var
                classes = maps[i].classList,
                classnames = null;
                //console.log("classlist=",classes);
                for(var j=classes.length;j--;){
                    if(classes[j].indexOf('-map')>=0){
                        classnames = classes[j].replace("-map","").split(/[,;:=]/g);
                        //console.log("CLASSNAMES=",classnames);
                        for(var k=classnames.length;k--;){
                            if(classnames[k]){
                                if(classnames[k][0]=="@") maps[i].dataset.landscape = maps[i].dataset.landscape+" "+classnames[k].substr(1);
                                if(classnames[k][0]=="#") maps[i].dataset.portrait = maps[i].dataset.portrait+" "+classnames[k].substr(1);
                            }
                        }
                        //maps[i].remClass(classes[j]);
                    }else maps[i].dataset.classes = maps[i].dataset.classes + " " + classes[j];
                    maps[i].remClass(classes[j]);
                }
                maps[i].addClass("-mapped");
                //console.log("CLASSLIST="+maps[i].classList.value);
                //console.log("CLASSES="+maps[i].dataset.classes);
                //console.log("LANDSCAPE="+maps[i].dataset.landscape);
                //console.log("PORTRAIT="+maps[i].dataset.portrait);
            }
        }

        maps = document.getElementsByClassName("-mapped");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                maps[i].className = maps[i].dataset.classes + " " + (ab.viewport==AB_LANDSCAPE?maps[i].dataset.landscape:maps[i].dataset.portrait);
            }
        }
        
        maps = document.getElementsByClassName("-switch");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                maps[i].addClass("-switched")
                maps[i].src = "src/img/std/switch_off.svg";
                if (maps[i].dataset.state) maps[i].src = "src/img/std/switch_" + (maps[i].dataset.state.int() ? "on" : "off") + ".svg";
                else maps[i].dataset.state = "0";
                maps[i].addEventListener("click", function(e) {

                    if (this.dataset.state.int()) {
                        if (this.dataset.off) eval(this.dataset.off); // jshint ignore:line
                        this.src = "src/img/std/switch_off.svg";
                        this.dataset.state = "0";
                    } else {
                        if (this.dataset.on) eval(this.dataset.on); // jshint ignore:line
                        this.src = "src/img/std/switch_on.svg";
                        this.dataset.state = "1";
                    }
                }, { passive: true });
                maps[i].remClass("-switch");
            };
        }

        maps = document.getElementsByClassName("-imgview");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                maps[i].addEventListener("click", function(e) { ab.load("../lib/ui/img_view.php", { pic0: this.src }, 20, null, true); }, { passive: true });
                maps[i].remClass("-imgview");
            }
        }

        maps = document.getElementsByClassName("-blur");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                var
                    el = document.createElement("blur");
                maps[i].prepend(el);
                maps[i].remClass("-blur");
            }
        }

        /* requests a onfirmation for a [_Data-callback] execution
         * ex.: <div -ask="Deseja mesmo fazer isso?" _Data-callback="alert('Usuário clicou em SIM!')"></div
         */
        maps = document.getElementsByClassName("-ask");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                maps[i].addEventListener("click", function(e) { ab.load("../lib/ui/ask.php", { from: this.myId() }); }, { passive: true });
                //console.log(maps[i]);
                maps[i].addClass("-asked");
                maps[i].remClass("-ask");
            }
        }

        /* requests a onfirmation for a [_Data-callback] execution
         * ex.: <div -ask="Deseja mesmo fazer isso?" _Data-callback="alert('Usuário clicou em SIM!')"></div
         */
        maps = document.getElementsByClassName("-pswd-request");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                maps[i].addEventListener("click", function() { ab.load("../lib/ui/pswd_request.php", { from: this.myId() }); }, { passive: true });
                //console.log(maps[i]);
                maps[i].addClass("-pswd-requested");
                maps[i].remClass("-pswd-request");
            }
        }

        maps = document.querySelectorAll("[class*='-wd'");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                var x = maps[i].className.split(/\s+/g);
                for (var cls in x)
                    if (x[cls].indexOf("-wd") >= 0) {
                        maps[i].style.width = x[cls].split(/wd/g)[1];
                        maps[i].remClass(x[cls]);
                    }
            }
        }

        maps = document.querySelectorAll("[class*='-ht'");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                var x = maps[i].className.split(/\s+/g);
                for (var cls in x)
                    if (x[cls].indexOf("-ht") >= 0) {
                        maps[i].style.height = x[cls].split(/ht/g)[1];
                        maps[i].remClass(x[cls]);
                    }
            }
        }

        maps = document.querySelectorAll("[class*='-ft'");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                var x = maps[i].className.split(/\s+/g);
                for (var cls in x)
                    if (x[cls].indexOf("-ft") >= 0) {
                        maps[i].style.fontSize = x[cls].split(/ft/g)[1];
                        maps[i].remClass(x[cls]);
                    }
            }
        }

        maps = document.querySelectorAll("[class*='-lt'");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                var x = maps[i].className.split(/\s+/g);
                for (var cls in x)
                    if (x[cls].indexOf("-lt") >= 0) {
                        maps[i].style.left = x[cls].split(/lt/g)[1];
                        maps[i].remClass(x[cls]);
                    }
            }
        }

        maps = document.querySelectorAll("[class*='-tp'");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                var x = maps[i].className.split(/\s+/g);
                for (var cls in x)
                    if (x[cls].indexOf("-tp") >= 0) {
                        maps[i].style.top = x[cls].split(/tp/g)[1];
                        maps[i].remClass(x[cls]);
                    }
            }
        }

        maps = document.querySelectorAll("[class*='-zi'");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                var x = maps[i].className.split(/\s+/g);
                for (var cls in x)
                    if (x[cls].indexOf("-zi") >= 0) {
                        maps[i].style.zIndex = x[cls].split(/zi/g)[1];
                        maps[i].remClass(x[cls]);
                    }
            }
        }

        maps = document.querySelectorAll("[class*='-op'");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                var x = maps[i].className.split(/\s+/g);
                for (var cls in x)
                    if (x[cls].indexOf("-op") >= 0) {
                        maps[i].style.opacity = x[cls].split(/op/g)[1];
                        maps[i].remClass(x[cls]);
                    }
            }
        }

        // invokes a close button, no need a parameter to make it works, it will only have it's desired result if is placed under -dialog o -window classes
        maps = document.getElementsByClassName("-close");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                maps[i].addClass("-closed");
                var
                    ic = document.createElement("icon");
                ic.textContent = "M";
                ic.className = "hpd"
                maps[i].appendChild(ic);
                maps[i].onmouseenter = function() {
                    this.style.background = "red";
                    this.style.color = "white";
                };
                maps[i].onmouseleave = function() {
                    this.style.background = "initial";
                    this.style.color = "initial";
                };
                maps[i].addEventListener("click", function(e) { this.parentModal().desappear(80, true); }, { passive: true });
                maps[i].remClass("-close");
            }
        }

        // invokes a maximize button, no need a parameter to make it works, it will only have it's desired result if is placed under -dialog o -window classes
        maps = document.getElementsByClassName("-maximize");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                maps[i].addClass("-maximized");
                var
                    ic1 = document.createElement("icon"),
                    ic2 = document.createElement("icon");
                ic1.textContent = "0";
                ic2.textContent = "/";
                ic1.className = "hpd"
                ic2.className = "hpd hid"
                maps[i].appendChild(ic1);
                maps[i].appendChild(ic2);
                maps[i].onmouseenter = function() {
                    var x = this.getElementsByTagName("icon");
                    x[0].style.display = "none";
                    x[1].style.display = "initial";
                };
                maps[i].onmouseleave = function() {
                    var x = this.getElementsByTagName("icon");
                    x[1].style.display = "none";
                    x[0].style.display = "initial";
                };
                maps[i].addEventListener("click", function(e) { this.parentModal().maximize(); }, { passive: true });
                maps[i].remClass("-maximize");
            }
        }

        maps = document.getElementsByClassName("-minimize");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                maps[i].addClass("-minimized");
                var
                    ic1 = document.createElement("icon"),
                    ic2 = document.createElement("icon");
                ic1.textContent = "K";
                ic2.textContent = "3";
                ic1.className = "hpd"
                ic2.className = "hpd hid"
                maps[i].appendChild(ic1);
                maps[i].appendChild(ic2);
                maps[i].onmouseenter = function() {
                    var x = this.getElementsByTagName("icon");
                    x[0].style.display = "none";
                    x[1].style.display = "inline";
                };
                maps[i].onmouseleave = function() {
                    var x = this.getElementsByTagName("icon");
                    x[1].style.display = "none";
                    x[0].style.display = "inline";
                };
                maps[i].addEventListener("click", function(e) { this.parentModal().minimize(); }, { passive: true });
                maps[i].remClass("-minimize");
            }
        }

        maps = document.getElementsByClassName("-restore");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                maps[i].addEventListener("click", function() { this.parentModal().restore(); }, { passive: true });
                maps[i].remClass("-restore");
            }
        }

        // sets the element height as half of its width
        maps = document.getElementsByClassName("-banner");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                maps[i].style.height = maps[i].offsetWidth / 2 + "px";
            }
        }

        // sets the element height as half of its width
        maps = document.getElementsByClassName("-square");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                maps[i].style.height = maps[i].offsetWidth + "px";
            }
        }

        var inputs = document.getElementsByTagName("input");
        if (inputs.length) {
            for (var i = 0; i++ < inputs.length;) {
                if (!inputs[i - 1].getAttribute("placeholder")) inputs[i - 1].setAttribute("placeholder", " ")
            }
        };

        // sets the element height as half of its width
        maps = document.getElementsByClassName("-drag");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                maps[i].remClass("-drag");
                //maps[i].startDrag();
            }
        }

        maps = document.getElementsByClassName("-datetime");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                if (maps[i].dataset.format) {
                    if (maps[i].dataset.format == "date") maps[i].value = ab.date().date();
                    if (maps[i].dataset.format == "time") maps[i].value = ab.date().time();
                } else maps[i].value = ab.date().datetime();
            }
        }

        maps = document.getElementsByClassName("-hash");
        if (maps.length) {
            for (var i = maps.length; i--;) {
                maps[i].addEventListener("change", function(e) { if (this.value) this.value = this.value.hash(); }, { passive: true });
                maps[i].remClass("-hash");
            }
        }

        maps = document.getElementsByTagName("nocontent");
        if (maps.length)
            for (var i = maps.length; i--;) maps[i].delete();

        this.reorder();
    }


    controls() {
        var
            ctrls = document.getElementsByClassName("-control");
        if (ctrls.length) {
            for (var i = ctrls.length; i--;) {
                ctrls[i].addClass("-controlled");
                if (["input", "textarea", "select"].indexOf(ctrls[i].tagName.toLowerCase()) >= 0) {
                    ctrls[i].addEventListener("blur", function(e) {
                        var
                            hnd = this.dataset.object.split(/[;:,-]/g),
                            prs = this.value ? this.value.replace(/["']/g, "&quot;") : '',
                            idx = 0;
                        if (prs && hnd.length == 3) {
                            if (hnd[2] == "bool") prs = prs ? 1 : 0;
                            if (hnd[2] == "int") prs = prs.int();
                            if (hnd[2] == "float") prs = prs.float();
                        }
                        if (hnd.length == 4) idx = hnd[3].int();
                        eval(hnd[0]).value(hnd[1].trim(), prs, idx); // jshint ignore:line
                        //this.checkout();
                    }, { passive: true });
                } else if (ctrls[i].classList.contains("-switched")) {
                    ctrls[i].addEventListener("click", function(e) {
                        var
                            hnd = this.dataset.object.split(/[;:,-]/g);
                        eval(hnd[0].trim()).value(hnd[1].trim(), this.dataset.state.int());
                    }, { passive: true });
                }
                ctrls[i].remClass("-control");
            }
        }
    }

    // syncronize _Data values with [_Data-controller] tags
    // ex.: <input type='text' _Data-controller='Objeto;nome'> // will recieve the value of field 'nome' of
    // a _Data object named Objeto (in this case)
    updateControls(c = null) {
        if (c) {
            if (typeof c == "string") c = document.getElementById(c);
            if (!c) return;
            //console.log(c);
            var
                x = c.getElementsByClassName("-controlled");
            if (!x.length) return;
            for (var i = x.length; i--;) {
                if (typeof x !== "object") break;
                var
                    tmp = x[i].dataset.object.split(/[;:,-]/g);
                //console.log(tmp);
                if (tmp && tmp.length >= 2) {
                    var
                        t = eval(tmp[0]).value(tmp[1]); // jshint ignore:line
                    //console.log(t);
                    if (t != null) {
                        if (x[i].tagName == "INPUT") x[i].value = t ? t.replace(/&quot;/g, "'") : "";
                        else if (x[i].tagName == "SELECT") {
                            if (x[i].dataset.tablefield && (x[i].classList.contains("-fill") || x[i].classList.contains("-filled"))) {
                                var
                                    y = x[i].dataset.tablefield.split(/[#;:,-]/g);
                                //console.log(y);
                                x[i].dataset.tablefield = [
                                    y[0] // table
                                    , y[1] //field
                                    , y[2] ? y[2] : "" // restrictions
                                    , y[3] ? y[3] : ab.qcell({ // selection
                                        table: y[0],
                                        field: y[1],
                                        restrictions: "code='" + t + "'"
                                    })
                                ].join(";");
                                //console.log(x[i].dataset.tablefield);
                                x[i].refill();
                            } else x[i].value = t.replace(/&quot;/g, "'");
                        } else if (x[i].classList.contains('-switched')) {
                            x[i].dataset.state = t ? "0" : "1";
                            x[i].click();
                        } else x[i].innerText = t ? t.replace(/&quot;/g, "'") : "";
                        if (x[i].dataset.trigger) eval(x[i].dataset.trigger);
                    };
                };
            };
        } else return 0;
    }

    autofill(c = null) {
        if (c) { if (typeof c == "string") c = document.getElementById(c); } else c = document;
        var
        x = c.getElementsByClassName("-autofill");
        if (!x.length) return;
        for (var i = x.length; i--;) {
            if (typeof x[i] !== "object") break;
            var
                tmp = x[i].dataset.object.split(/[;:,-]/g);
            if (tmp && tmp.length >= 2) {
                var
                    t = eval(tmp[0]).value(tmp[1]); // jshint ignore:line
                if (t != null) {
                    //console.log(t,tmp[0],tmp[1]);
                    if (x[i].tagName == "INPUT") x[i].value = t ? t.replace(/&quot;/g, "'") : "";
                    else if (x[i].tagName == "SELECT") {
                        if (x[i].dataset.tablefield) {
                            var
                            y = x[i].dataset.tablefield.split(/[#;:,-]/g);
                            //console.log(y);
                            x[i].dataset.tablefield = [
                                y[0] // table
                                , y[1] //field
                                , y[2] ? y[2] : "" // restrictions
                                , y[3] ? y[3] : ab.qcell({ // selection
                                    table: y[0],
                                    field: y[1],
                                    restrictions: "code='" + t + "'"
                                })
                            ].join(";");
                            //console.log(x[i].dataset.tablefield);
                            x[i].refill();
                        } else if(t) x[i].value = t.replace(/&quot;/g, "'");
                    } else if (x[i].classList.contains('-switched')) {
                        x[i].dataset.state = t ? "0" : "1";
                        x[i].click();
                    } else if(t) x[i].innerText = t.replace(/&quot;/g, "'");
                    if (x[i].dataset.trigger) eval(x[i].dataset.trigger);
                };
            };
        };
    };

    /*
     * Enable/Disable the loading tag, which has the highest level of frame ordering on the page
     * s = switch (true or false)
     * ex.: __self.loading(true)
     *
     */
    loading(t = true) {
        clearInterval(this.loading_);
        var
            el = document.getElementsByClassName("-loading");
        if (!el.length) {
            var
                x = document.createElement("div"),
                y = document.createElement("img");
            y.className = "-wheel";
            y.src = "src/img/std/wheel.png";
            x.appendChild(y);
            x.className = "-loading tct";
            this.body().appendChild(x);
            el = [x];
        }
        if (t) {
            el[0].style.display = "block";
            this.loading_ = setTimeout(function(e) { e.style.display = 'none'; }, 8000, el[0]);
        } else el[0].style.display = "none";
    }

    notify(n, c = ["white", "black"], l = AB_NOLOG) {
        var
            notfys = document.getElementsByTagName("toast"),
            toast = document.createElement("toast");
        toast.style.background = c[0] ? c[0] : "white";
        toast.style.color = c[1] ? c[1] : "black";
        //toast.textContent = n?n:"Hello World!!!";
        toast.innerHTML = n ? n : "Hello World!!!";
        toast.style.left = ab.viewport==AB_LANDSCAPE?"78%":"7.5%";
        toast.style.top = "0";
        toast.onclick = function() { this.desappear(80, true); };
        ab.body().appendChild(toast);
        for (var i = notfys.length; i--;) {
            notfys[i].stop().trans({ top: notfys[i].offsetTop + ab.h(1) + toast.offsetHeight, alpha: 1 }, 40 + 10 * 1);
            //console.log({ top:notfys[i].offsetTop-ab.h(5) });
        }
        //var animationPreset = {top:this.h(88)-toast.offsetHeight, left:this.w(75), alpha:1.0 };
        //console.log(animationPreset);
        toast.stop().trans({ top: ab.h(1), alpha: 1.0 }, 40);
        toast.dataset.delay = setTimeout(function() { toast.remove(); }, 8000);
        toast.onmouseenter = function() {
            this.addClass("fbd");
            clearTimeout(this.dataset.delay);
        };
        toast.onmouseleave = function() {
            this.remClass("fbd");
            this.dataset.delay = setTimeout(function(t) { t.remove(); }, 2000, this);
        };
        if (l === AB_LOG) { this.exec("../lib/fn/log.php", { text: n }); }
        //this.organize();
    }

    /*
     * Loads a php file and interact via posting an object with arguments
     * u = url of the php file to be touched
     * o = object to be send as parameter via post
     * f = callback function to be executed
     * s = syncronization mode, true stands for syncronous
     * ex.: __self.call('welcome.php', { user:'1234', name:'Rafael Bertolini' }, function(d){ alert('hello ' + d + '!'); })
     *
     */
    call(u, o = null, f = false, s = false, l = false) {
        if (l) this.loading(AB_ON);
        //console.log(u);
        var
            id = u.uton(),
            iter = 1000,
            count = s ? new Throttle(function() {--iter; }, 1, false) : null,
            sync = null,
            xhr = new XMLHttpRequest();
        u = u.indexOf('http') > -1 ? u : ("project/" + (u.indexOf('.') > -1 ? u : u + '.php'));
        xhr.onreadystatechange = function() {
            //console.log(xhr.responseText);
            if (!s && f && xhr.readyState == 4) {
                eval(f)({ status: xhr.status, data: xhr.responseText.trim(), id: id });
            };
        }
        /*
        if (!s) {
            xhr.onload = function(data) {
                //console.log(data.currentTarget.responseText);
                if (f) {
                    eval(f)({
                        status: xhr.status,
                        data: data.currentTarget.responseText.trim(),
                        id: id
                    });
                }
            }
        }
        */
        xhr.open("POST", u, !s);
        //xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.setRequestHeader("Content-Type", "application/json");

        xhr.send(o ? JSON.stringify(o) : null);
        
        if (s) {
            var o = { status: xhr.status, data: xhr.responseText.trim(), id: id };
            return (f ? eval(f)(o) : o);
        }
    }

    qcell(o = null) { if (o) return this.call("../lib/fn/qcell.php", o, function(d) { return d.data; }, true); }

    qio(o = null, n = false) { if (o) return this.call("../lib/fn/qio.php", { data: o, n: n }, function(d) { return d.data; }, true, false); }

    /*
     * Implemets __self.call transpassing "u" and "o" arguments, but
     * "t" stands for "target", which means the content that is returned
     * from "u" (usually an entire page), is loaded inside the target element
     * "t" can be a javascript object or JQuery one
     * u = url
     * t = JQuery element as a target for the returned info
     * o = javascript object (default is null)
     * tr = transition (true = slide, false = fade)
     * ex.: __self.load('welcome.php',  { user:'Rafael' }, true)
     * or just: __self.load('welcome.php', HOME)
     *
     */
    load(u, o = null, d = 80, t = null, l = true) {
        if (l) this.loading(l);
        var
        id = u.uton(),
        uu = document.getElementById(id);
        if (uu) {
            if (uu.isModal()) uu.restore();
            else uu.appear(d);
            ab.loadPool.push(uu.myId());
            this.loading(false);
            return;
        }
        this.call(u, o, function(d) {
            if (d.status != 200) ab.error(d.data);
            else {
                d.data = d.data.split(/{{/g);
                if (d.data.length > 1) {
                    for (var i = d.data.length; --i;) {
                        d.data[i] = d.data[i].split(/}}/g);
                        if (d.data[i].length > 1) {
                            d.data[i][0] = d.data[i][0]
                                .replace(/[%]/g, "ab.tmpfs.") // temporary workspace
                                .replace(/[#]/g, d.id) // ID
                                .replace(/[!]/g, ".data"); // refer to an ab's Data object
                            d.data[i] = d.data[i].join("");
                        }
                    }
                }
                d.data = d.data.join("");
                var
                uu,
                x = d.data.toDOM();
                uu = document.getElementById(x.id);
                if (uu) {
                    if (uu.isModal()) uu.restore();
                    else uu.appear(d);
                    ab.loadPool.push(uu.myId());
                    ab.loading(AB_OUT);
                    return;
                }
                if (!x || !(typeof x == "object") || ["SCRIPT", "STYLE"].indexOf(x.tagName) >= 0){  
                    if(x.tagName=="SCRIPT") eval(x.textContent);
                    return;
                } else {
                    if (!x.id) x.id = id;
                    if (t) t.appendChild(x);
                    else ab.body().appendChild(x);
                    if (x.isModal()) ab.windows.push(x.id);
                    ab.loadPool.push(x.id);
                    x.appear(80);
                    var
                    scr = x.getElementsByTagName("script");
                    if (scr.length) for (var i = 0; i++ < scr.length;) eval(scr[i - 1].innerText.trim());
                }
            }
            ab.loading(AB_OUT);
        }, false, l);
    }

    /*
     * Get result from a remote page passing parameters (or no)
     * and can fire a callback if it is passed
     * u = url to be processed
     * o = javascript object, null is the default
     * f = function callback to be applied
     * ex.: __self.exec('login.php', { pswd:'1234', user:'rafsb' }, function(){ document.location.reload(); })
     *
     */
    exec(u, o = null, f = null) { this.call(u, o, f, false, false); }

    conf_file_handler(f, v = null, fn = null) {
        this.exec("../lib/ctrl/conf_parameters.php", { fild: f, val0: v }, function(d) {
            if (d.status == "success") ab.success();
            else ab.error();
        }); // jshint ignore:line
    }

    schema_file_handler(f, v = null, fn = null) {
        this.exec("../lib/ctrl/schema_parameters.php", { fild: f, val0: v }, function(d) {
            if (d.status == "success") ab.success();
            else ab.error();
        });
    }

    hashIt(h) { return this.call("../lib/ctrl/hash_it.php", { hash: h }, function(d) { return d.data; }, true, false); } // encrypt _Data 'h'

    pswdCheck(u, p) {
        // check user's ('u') password ('p')
        return this.call("../lib/ctrl/pswd_check.php", { user: u, pswd: p }, function(d) { return d.data; }, true, false);
    }

    pswdChange(c) { this.load("../lib/ui/pswd_change.php"); }

    pswdReset(c) { this.call("../lib/ui/pswd_reset.php", null, function(d) { ab.working("TODO: fn/pswd_reset.php"); }); }

    // returns a string with 'n' length of random characteres
    newId(n = 8, p = "", t = AB_STRING) {
        var
            s = (t == AB_STRING ? "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ" : "") + (t == AB_STRING || t == AB_INTEGER ? "23456789" : "") + "01",
            tmp = (p || "i").split('');
        if (tmp.length >= n) return tmp.join('');
        else n -= tmp.length;
        while (n--) tmp.push(s.charAt(Math.floor(Math.random() * s.length)));
        return tmp.join('');
    }

    /*
     * Returns a json object comming from the server's _Database, setted by:
     * t = table
     * f = field
     * r = restrictions
     * o = field into which the result will be ordered
     *
     */
    jout(o = null) {
        if (!o) return;
        return this.call("../lib/ctrl/jout_sql.php", o, function(d) {
            if (d) return d;
            else return null;
        }, true, false)
    }

    /*
     * Returns '1' if user and pswd is ok, otherwise returns '0'
     * u = user's user to pass
     * p = user's password on system
     * k = true for keep signed over sessions (uses cookies)
     * f = callback function to fire
     * ex.: __self.signin('rafsb', '1234', true, function(d){ if(parseInt(d)){ alert('logged In!'); } });
     *
     */
    signin(u = null, p = null, k = 1, f = null) {
        if (!u || !p) return -1;
        this.call("../lib/ctrl/signin.php", { user: u, pswd: p, keep: k }, (f ? f : function(d) {
            switch (d.data.int()) {
                case (1):
                    ab.success("Login confirmado...");
                    setTimeout(function() {
                        var
                            x = location.href.split("?");
                        if (x.length > 1) {
                            x[0] += "?";
                            x[1] = x[1].split("&");
                            for (let i = x[1].length; i--;)
                                if (x[1][i].indexOf('uuid') >= 0) x[0] += x[1][i];
                        }
                        location.href = x[0];
                    }, 200);
                    break;
                case (0):
                    ab.error('Ops! Algo deu errado, tente novamente mais tarde...');
                    break;
                case (-1):
                    ab.error('Faltando Usuário ou senha (-1)');
                    break;
                case (-2):
                    ab.error('Usuário ou senha inválidos (-2)');
                    break;
                case (-3):
                    ab.error('Ops! Talvez esta não seja sua página (-3)');
                    break;
                case (-4):
                    ab.error('Seu e-mail ainda não foi confirmado (-4)');
                    break;
                case (-5):
                    ab.error('Por algum motivo seu usuário está desabilitado (-5)');
                    break;
                case (-6):
                    ab.error('Erro na validação da senha (-6)');
                    break;
                default:
                    ab.error();
                    break;
            }
            ab.loading(AB_OUT);
        }), false, true);
    }

    // detach user's credentials from the actual session
    logoff() {
        this.exec("../lib/fn/logoff.php", null, function() {
            setTimeout(function() {
                var
                    x = location.href.split("?");
                if (x.length > 1) {
                    x[0] += "?";
                    x[1] = x[1].split("&");
                    for (let i = x[1].length; i--;)
                        if (x[1][i].indexOf('uuid') >= 0) x[0] += x[1][i];
                }
                location.href = x[0];
            }, 200);
        }, true, true);
    }

    // calls framework's inner login modal window
    login() { this.load("../lib/ui/login.php"); }

    currentUser() { return this.call("../lib/fn/currentuser.php", null, function(d) { return JSON.parse(d.data); }, true, false); };

    // calls framework's inner message modal window, m = message to be displayed
    advise(m = "Hello world!", setup = null) { this.load("../lib/ui/advise.php", { advs: m, setu: setup }); }

    // displays a standard error message
    error(m = null) { this.notify(m ? m : "Ops! Something went wrong...", ["#F32F29", "white"]); }

    // displays a standard error message
    working(m = null) { this.notify(m ? m : "Ops! We're working on that...", ["#FC4522", "white"]); }

    success(m) { this.notify(m ? m : "Yup! Succeed!", ["#2BA892", "white"]); }

    // enable tooltips for [-tooltip] tags
    tooltips() {
        if (this.viewport === AB_PORTRAIT) return;
        else {
            if (!document.getElementsByTagName("tooltip").length) {
                var
                    tooltip = document.createElement("tooltip");
                tooltip.className = "pin bdark fwhite hpd hr";
                ab.body().appendChild(tooltip);
            }
            var
                x = document.getElementsByClassName("-tooltip");
            for (var i = x.length; i--;) {
                x[i].addEventListener("mouseenter", function() {
                    if (this.dataset.tooltipshow) clearInterval(this.dataset.tooltipshow);
                    if (this.dataset.tooltiphide) clearInterval(this.dataset.tooltiphide);
                    var
                        pos = ab.mousePos,
                        ttips = document.getElementsByTagName("tooltip")[0];
                    ttips.style.top = (pos.top + 32) + "px";
                    ttips.style.left = (pos.left - 6) + "px";
                    if (this.dataset.message && this.dataset.message.indexOf("::text") > -1) this.dataset.message.replace("::text", this.innerText);
                    //console.log(this.dataset.message,this.innerText);
                    ttips.innerHTML = this.dataset.message;
                    ttips.dataset.tooltipshow = setTimeout(function() {
                        ttips.appear(80, pos.top + 12);
                        ttips.dataset.tooltiphide = setTimeout(function() { ttips.desappear(80, false); }, 4000);
                    }, 1000);
                    ab.organize();
                }, { passive: true });
                x[i].addEventListener("mouseleave", function() {
                    var
                        ttips = document.getElementsByTagName("tooltip")[0];
                    clearInterval(ttips.dataset.tooltipshow);
                    clearInterval(ttips.dataset.tooltiphide);
                    ttips.style.display = "none";
                }, { passive: true });
            }
        }
    }


    fills() {
        var
            x = document.getElementsByClassName("-fill");
        if (x.length)
            for (var i = 0; i++ < x.length;) setTimeout(function(x) { x.fill(); }, 40, x[i - 1]);
    }

    scrolls() {
        /*
         * Nedded nodes: name(quantity)
         * .-sscope(1)
         * \____ .-hook(n)
         * \____ .-navy(1)
         *       \____ .-anchor(==n)
         */
        var
        who = document.getElementsByClassName("-sscope");
        //console.log(who);
        if (who && who.length) {
            for (var i = who.length; i--;) {
                var
                    hooks = who[i].getElementsByClassName("-hook"),
                    navy = who[i].getElementsByClassName("-navy")[0],
                    anchors = navy.getElementsByClassName("-anchor"),
                    scrollEffect = new Throttle(function() {
                        var
                        head = 0,
                        iter = 1000000;
                        for (var i = anchors.length; i--;){
                            if (Math.abs(navy.scrollTop - anchors[i].offsetTop) < iter) {
                                head = i;
                                iter = Math.abs(navy.scrollTop - anchors[i].offsetTop);
                            }
                        }
                        for (var i = hooks.length; i--;){
                            hooks[i].style.fontWeight = (i == head ? "bolder" : "lighter");
                            hooks[i].style.opacity = (i == head ? "1" : ".7");
                        }
                    }, 40);
                for (var j = hooks.length; j--;) {
                    hooks[j].addEventListener("click", function() {
                        navy.scrollTo(anchors[this.idx()], 400);
                    }, { passive: true });
                }
                navy.addEventListener("scroll", function() { scrollEffect.fire(); }, { passive: true });
                who[i].addClass("-scoped");
                who[i].remClass("-sscope");
            }
        }
    }

    /* enable dropdowns
     * it may follow some rules
     * see its tree:
     * [-dropdown] // has optional [_Data-rows] that contains classes to be placed on rows
     * \___: $.children(":eq(0)") // wich is what you see on screen before the mouse positioning above it
     * \___: $.children(":eq(1)") // it is what is toggled
     *     \___: N number of <row></row>, wich one as a block element
     */
    dropdowns() {
        var
            drops = document.getElementsByClassName("-dropdown");
        if (drops.length) {
            for (var i = drops.length; i--;) {
                var
                    x = drops[i],
                    bt = x.children[0],
                    nv = x.children[1],
                    cl = x.dataset.paint ? x.dataset.paint.split(/[,:;-\s+]/g) : [this.schema.bdialog, this.schema.fmain];
                nv.style.top = bt.offsetHeight - 4 + "px";
                nv.style.background = cl[0];
                nv.style.color = cl[1];
                if(!nv.classList.contains("-nd")&&!nv.classList.contains("--no-default")){
                    nv.style.minWidth = (2 * bt.offsetWidth) + "px";
                    nv.style.paddingTop = "1rem";
                    nv.style.paddingBottom = "1rem";
                    nv.addClass("ns");
                }
                x.onmouseenter = function() {
                    this.children[0].style.background = cl[0];
                    this.children[0].style.color = cl[1];
                    this.children[0].style.opacity = cl[2] ? cl[2] : 1;
                    this.children[1].style.opacity = cl[2] ? cl[2] : 1;
                    this.children[1].style.display = 'inline-block';
                };
                x.onmouseleave = function() {
                    this.children[0].style.background = "initial";
                    this.children[0].style.color = null;
                    this.children[1].style.display = 'none';
                };
            }
        }
    }

    // enables all masks used on a document */
    masks() {
        var
            masks = document.querySelectorAll("[class*=-mask]");
        if (masks.length) {
            for (var i = masks.length; i--;) {
                var
                    x = masks[i];
                if (x.classList.contains("-mask-doc")) {
                    x.addEventListener("keyup", function(e) { this.value = this.value.document(); }, { passive: true });
                    x.remClass("-mask-doc");
                    this.value = this.value ? this.value.document() : "";
                }
                if (x.classList.contains("-mask-money")) {
                    x.addEventListener("keyup", function(e) { this.value = this.value.money(); }, { passive: true });
                    x.remClass("-mask-money");
                    this.value = this.value ? this.value.money() : "";
                }
                if (x.classList.contains("-mask-date")) {
                    x.addEventListener("keyup", function(e) { this.value = this.value.date(); }, { passive: true });
                    x.remClass("-mask-date");
                    this.value = this.value ? this.value.date() : "";
                }
                if (x.classList.contains("-mask-char")) {
                    x.addEventListener("keyup", function(e) { this.value = this.value.basicChar(); }, { passive: true });
                    x.remClass("-mask-char");
                    this.value = this.value ? this.value.basicChar().toLowerCase() : "";
                }
                if (x.classList.contains("-mask-hour")) {
                    x.addEventListener("keyup", function(e) { this.value = this.value.hour(); }, { passive: true });
                    x.remClass("-mask-hour");
                    this.value = this.value ? this.value.hour() : "";
                }
                if (x.classList.contains("-mask-phone")) {
                    x.addEventListener("keyup", function(e) { this.value = this.value.phone(); }, { passive: true });
                    x.remClass("-mask-phone");
                    this.value = this.value ? this.value.phone() : "";
                }
                if (x.classList.contains("-mask-int")) {
                    x.addEventListener("keyup", function(e) {
                        if (this.value.length < 3) return;
                        this.value = this.value.replace(/[^0-9]/g, "").replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
                    }, { passive: true });
                    x.remClass("-mask-int");
                    this.value ? this.value = this.value.replace(/[^0-9]/g, "").replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.") : "";
                }
                if (x.classList.contains("-mask-float")) {
                    x.addEventListener("keyup", function(e) {
                        if (this.value.length < 3) return;
                        this.value = this.value.money().split("").splice(3).join('');
                    }, { passive: true });
                    x.remClass("-mask-float");
                    this.value ? this.value = this.value.money().split("").splice(3).join('') : "";
                }
            }
        }
    };

    apply(f = null, o = null) { return (f ? eval(f)(o) : null); } // jshint ignore:line

    checkout(c) {
        if (!c) return false;
        var
            ret = true,
            els = c.getElementsByClassName("-required");
        for (var i = els.length; i--;)
            if (els[i].checkout() === false) ret = false;
        return ret;
    }

    env_vars(o) { return ab.call('../lib/ctrl/env_vars.php', o, function(d) { return d.data; }, true, false); }

    object_fetch(field, value, obj = this) {
        var
            idx = String(field).indexOf('.');
        if (idx > -1) return this.object_fetch(field.substring(idx + 1), value, obj[field.substring(0, idx)]);
        if (value) obj[field] = value;
        return obj[field];
    }

    wait(time = 80) {
        var
            start = new Date().getTime();
        while ((new Date().getTime()) - start < time);
    }

    mail(u,o,fn){
        if(!u){
            ab.error("Favor indicar o template para envio...");
            return 0;
        }
        ab.call("../etc/mailer.php",{conf:u,data:o},function(data){ if(fn) eval(fn)(data); });
    }
}

let ab = new Abox();