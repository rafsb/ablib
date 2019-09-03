bootstrap = {
	screens 	: { continue: false }
	, pace 		: function(){ let count = 0; for(var i in Object.keys(this.screens)) count++; return 100/count; }
	, percent	: function(){ let count = 0; for(var i in this.screens) if(this.screens[i]) count++; return count*this.pace(); }
	, status 	: function(scr){ return this.screens[scr]; }
	, loadComponents 		: new Pool()
	, onLoadingStatusChange : new Pool()
	, onFinishLoading 		: new Pool()
	, ready 	: function(scr){
		if(scr){
			this.screens[scr] = true;
			let perc = this.percent();
			/* 100% */
			if(perc>=99&&!this.alreadyLoaded){ this.onFinishLoading.fire(); this.alreadyLoaded=true; }
		}
		return this.alreadyLoaded || false;
	}
};