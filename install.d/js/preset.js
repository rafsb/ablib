app.hash = app.storage("hash") || null;
app.theme = app.storage("theme") || DEFAULT_THEME;
app.body = $("body")[0];
app.initial_pragma = START;
app.components = { /**/ };

bootloader.loaders = { 
	pass: 1
};

bootloader.loadComponents.add(function(){ /**/ });

bootloader.onFinishLoading.add(function(){ 
	/**/
	tileClickEffectSelector(".-tile") 
});

app.onPragmaChange.add(x => {
	switch (x) {
		case START: 		/**/ 
		;
		break;
	}
});

__scroll.up(()=>{ 	 /**/ });
__scroll.down(()=>{  /**/ });
__scroll.right(()=>{ /**/ });
__scroll.left(()=>{  /**/ });
__scroll.fire();