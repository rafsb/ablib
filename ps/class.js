/* jshint esversion:6 */
/* jshint -W034 */

const PS_TRUE    = true;
const PS_FALSE   = false;

class PS_BASIC{
    constructor(obj=''){
        if(typeof obj=='object') this.obj_ = obj;
        this.init();
    };
    init(){ this.obj_=this.obj_?this.obj_:{}; };
    obj(){ return this.obj_; };
    dataset(f=null,v=null){
        if(!f) return;
        if(v) this.obj_[f]=v;
        return this.obj_[f];
    }
};

// Item [code,desc,val0,qtty,wght]
class PS_ITEM extends PS_BASIC{
    init(){
        if(this.obj_) return;
        this.obj_ = {code:ab.newId(8),desc:'',val0:35.00,qtty:1,wght:0}; 
    }
    id(d='')          { if(d!=='') this.obj_.code=d; return this.obj_.code; }
    description(d='') { if(d!=='') this.obj_.desc=d; return this.obj_.desc; }
    value(d='')       { if(d!=='') this.obj_.val0=d; return this.obj_.val0; }
    quantity(d='')    { if(d!=='') this.obj_.qtty=d; return this.obj_.qtty; }
    weight(d='')      { if(d!=='') this.obj_.wght=d; return this.obj_.wght; }
};

// Client [name,tel0,mail,adrs,nadr,ngbr,city,zipc,uf00]
class PS_CLIENT extends PS_BASIC {
    init() {        
        if(this.obj_) return;
        this.obj_ = {name:"",tel0:"",mail:"",adrs:"",nadr:"",ngbr:"",city:"",zipc:"",uf00:""};
    };
    mail(d=''){ if(d!='') this.obj_.name=d; return this.obj_.name; }
    mail(d=''){ if(d!='') this.obj_.mail=d; return this.obj_.mail; }
    phone(d=''){ if(d!='') this.obj_.tel0=d; return this.obj_.tel0; }
    address(ad='',na=0,ng='',ct='',zc='12200000',uf="SP"){
        if(ad!==null) this.obj_.adrs=ad;
        if(na!==null) this.obj_.nadr=na;
        if(ng!==null) this.obj_.ngbr=ng;
        if(ct!==null) this.obj_.city=ct;
        if(zc!==null) this.obj_.zipc=zc;
        if(uf!==null) this.obj_.uf00=uf;
        return {
            address_line:this.obj_.adrs,
            number      :this.obj_.nadr,
            neighborhood:this.obj_.ngbr,
            city        :this.obj_.city,
            zip_code    :this.obj_.zipc,
            uf          :this.obj_.uf00
        };
    }
};

class PS_MOVE{
    constructor(item=(new PS_ITEM),cli=(new PS_CLIENT),run=false){
        this.item = item;
        this.client = cli;
        if(run) this.send();
    };
    item(f=null,v=null){
        if(!f) return this.item;
        if(v) this.item.obj_[f]=v;
        return this.item.obj_[f];
    }
    item(f=null,v=null){
        if(!f) return this.client;
        if(v) this.client.obj_[f]=v;
        return this.client.obj_[f];
    }
    send(){
        var
        xhr = new XMLHttpRequest(),
        obj = {item:this.item.obj_,client:this.client.obj_};
        //console.log(string);
        xhr.upload.onload = function(){ console.log(xhr.responseText); }
        xhr.upload.onerror = function(){ console.log("deu ruim: "+xhr.responseText) };
        xhr.open("POST","lib/ps/sell.php");
        //xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('Content-type', 'application/json');
        xhr.send(obj);
    }
};
