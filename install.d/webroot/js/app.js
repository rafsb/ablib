var
__come = new Event('come')
, __go = new Event('go')
, bootstrap = {
	screens : {
		continuing: false 
	}
	, pace : function(){
		let
		count = 0;
		for(var i in this.screens) count++;
		return 100/count;
	}
	, percent: function(){
		let
		count = 0;
		for(var i in this.screens) if(this.screens[i]) count++;
		return count*this.pace();
	}
	, status: function(scr){
		return this.screens[scr]
	}
	, ready: function(scr){
		if(scr){
			// set screen to true
			this.screens[scr] = true;

			let
			perc = this.percent();
			// init only on 100%
			if(perc>=99&&!this.alreadyLoaded){ 
				this.onFinishLoading.fire(()=>{ app.pragma = app.initial_pragma; }, ANIMATION_LENGTH);
				this.alreadyLoaded=true; 
			}
		}
		return this.alreadyLoaded || false
	}
	, loadComponents : new Pool()
	, onFinishLoading : new Pool()
}
, app = {
	debug: false
	, allLikes: 0
	, allShares: 0
	, initial_pragma: 0
	, current: 0
	, last : 0
	, fw: _
	, body: document.getElementsByTagName("body")[0]
	, onPragmaChange: new Pool()
	, get: function(e,w){ return faau.get(e,w||document).nodearray; }
	, screens: function(){ return document.getElementsByClassName("--screen"); }
    , declare: function(obj){ Object.keys(obj).each(function(){ window[this+""] = obj[this+""] }); }
	, initialize: function(){

		/* SPLASH SCREEN LOAD */
		app.fw.load("views/splash.htm", null, $(".--screen.--splash")[0], function(){ bootstrap.ready("start") });

		let
		args = location.href.split("?")[1];
		if(args){
			let
			o={};
		    args = args.split("&").extract(function(){ let t = {}; o[this.split("=")[0]] = this.split("=")[1]; });
			if(o.pager*1) this.initial_pragma = o.pager*1;
			this.args = o;
		}


		 // console.log("INITIAL_PRAGMA = "+ this.initial_pragma);
		
		bootstrap.onFinishLoading.add(function(){
			$(".--screen").each(function(){
				this.on("come",function(){ 
					if(this.dataset.hideposx) this.anime({translateX:0},ANIMATION_LENGTH/2);
					else if(this.dataset.hideposy) this.anime({translateY:0},ANIMATION_LENGTH/2);
				});
				this.on("go",function(){
					let
					x = [0,"-100vw","100vw"][["left","right"].indexOf(this.dataset.hideposx)+1]
					, y = [0,"-100vh","100vh"][["top","bottom"].indexOf(this.dataset.hideposy)+1];

					if(x) this.anime({ translateX:x }, ANIMATION_LENGTH, 0, function(){ if(this.dataset.role=="dismiss") this.remove() });
					else if(y) this.anime({ translateY:y }, ANIMATION_LENGTH, 0, function(){ if(this.dataset.role=="dismiss") this.remove() });
					else setTimeout(function(x){ x.desappear(ANIMATION_LENGTH,x.dataset.role=="dismiss"?true:false) },ANIMATION_LENGTH,this);
				});
			});
		});

		bootstrap.onFinishLoading.add(()=>{ 
			this.device_id = location.href.split("?device_id=")[1] ? location.href.split("?device_id=")[1].split("&")[0] : null;
		 	if(this.device_id) this.fw.call("http://api.spumedata.com/push/register",hashit({hash:this.device_id, charts:app.fw.storage("charts") ? app.fw.storage("charts").json().extract(function(){ return this.id }) : null }));
		 });
	}
};

app.spy("pragma",function(x){
	app.last = app.current;
	app.current = x;
	if(app.debug) console.log("SCREEN '"+(Object.keys(bootstrap.screens).length>=x ? Object.keys(bootstrap.screens)[x].toUpperCase() : "RETAIL")+"("+x+")' TRYING TO BE LOADED");
	if(!bootstrap.ready()) return setTimeout((x)=>{ app.pragma = x }, ANIMATION_LENGTH, x);
	
	$(".--screen").each(function(){ if(this.has("--"+Object.keys(bootstrap.screens)[x])) this.dispatchEvent(__come); else this.dispatchEvent(__go) });

	if(app.hash) return this.onPragmaChange.fire(x);
	else $(".--screen.--login")[0].dispatchEvent(__come);
});