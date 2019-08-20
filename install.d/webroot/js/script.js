const 
START 	  	  	= 0
, CLIPPINGS 	= 1
, FEEDS 	  	= 2
, MENU 		  	= 3
, LOGIN 	  	= 4
, WHOWEARE 	  	= 5
, ABOUT 	  	= 6
, CHARTCHANGE 	= 7
, NOCHART 	 	= 8
, POLICY 	 	= 9
, NOTIFICATIONS = 10
, FEED_LENGTH 	= 100
, FEED_LOADS    = 5;
;

app.spheres = {};
app.components = {};
app.notifications = null;

app.fw.color_pallete = {
    wet_asphalt     : "#444444"
    , midnight_blue : "#2d2d2d"
    , concrete      : "#8d8d8d"
    , amethyst      : "#9C56B8"
    , wisteria      : "#8F44AD"
    , clouds        : "#ECF0F1"
    , peter_river   : "#2C97DD"
    , belize_hole   : "#2A80B9"
    , alizarin      : "#E84C3D"
    // , emerald       : "#53D78B"
    , nephiritis    : "#27AE61"
    // , carrot        : "#E67D21"
    // , turquoise     : "#00BE9C"
    // , green_sea     : "#169F85"
    , sunflower     : "#F2C60F"
    , alpha_k1 		: "rgba(0,0,0,.18)"
    , alpha_k2 		: "rgba(0,0,0,.32)"
    , alpha_k3 		: "rgba(0,0,0,.64)"
    , alpha_w1 		: "rgba(255,255,255,.16)"
    , alpha_w2 		: "rgba(255,255,255,.32)"
    , alpha_w3 		: "rgba(255,255,255,.64)"
};

bootstrap.screens = {
	// secreens
	start    	   			: false
	, clippings 			: false
	, feeds     			: false
	, menu    				: false
	, login   	  			: false
	, whoweare 	  			: false
	, about   	  			: false
	, chartchange  			: false
	, nochart 	  			: true
	, policy 	  			: false
	, notifications 		: false
	// componentes
	, header  	   			: false
	, footer 				: false
	, feeds_tile 			: false
	, clipping_tile 		: false
	, notification_tile 	: false
	, children_tile 		: true//false
	, clipping_tile_status 	: false
	, moods_tile 			: false
	, grandchildren_tile 	: false
	// jsons
	, mrc_json     	   		: false
	, feed_json  			: false
	, notification_json		: false
	, charts_json 			: false
	// scripts
	, init_script 			: false
};

bootstrap.loadComponents.add(function(){
	app.fw.load("views/login.htm", null, $(".--screen.--login")[0], ()=>{ bootstrap.ready("login") });

	if(app.hash){
		// load components
		app.fw.call("views/components/header.htm", null, function(){ app.components.header = this.data.prepare(app.fw.colors()); bootstrap.ready("header") });
		app.fw.call("views/components/footer.htm", null, function(){ app.components.footer = this.data.prepare(app.fw.colors()); bootstrap.ready("footer") });

		// load screens
		app.fw.call("views/clippings.htm"	 , null, function(){ app.components.clippings 	  = this.data.prepare(app.fw.colors()); bootstrap.ready("clippings")     });
		app.fw.call("views/feeds.htm"   	 , null, function(){ app.components.feeds 		  = this.data.prepare(app.fw.colors()); bootstrap.ready("feeds")         });
		app.fw.call("views/notifications.htm", null, function(){ app.components.notifications = this.data.prepare(app.fw.colors()); bootstrap.ready("notifications") });

		app.fw.load("views/menu.htm"  	 	 , null, $(".--screen.--menu")[0]		, ()=>{ bootstrap.ready("menu") 	   });
		app.fw.load("views/whoweare.htm"     , null, $(".--screen.--whoweare")[0]	, ()=>{ bootstrap.ready("whoweare")    });
		app.fw.load("views/about.htm"        , null, $(".--screen.--about")[0]	 	, ()=>{ bootstrap.ready("about") 	   });
		app.fw.load("views/chartchange.htm"  , null, $(".--screen.--chartchange")[0], ()=>{ bootstrap.ready("chartchange") });
		app.fw.load("views/policy.htm"       , null, $(".--screen.--policy")[0]	 	, ()=>{ bootstrap.ready("policy") 	   });

		// load tiles
		app.fw.call("views/tiles/feed.htm"			 , null, function(){ app.components.feed_tile 			 = this.data.prepare(app.fw.colors()); bootstrap.ready("feeds_tile") 		});
		app.fw.call("views/tiles/clipping.htm"		 , null, function(){ app.components.clipping_tile 		 = this.data.prepare(app.fw.colors()); bootstrap.ready("clipping_tile") 		});
		app.fw.call("views/tiles/children.htm"		 , null, function(){ app.components.children_tile 		 = this.data.prepare(app.fw.colors()); bootstrap.ready("children_tile") 		});
		app.fw.call("views/tiles/notification.htm"	 , null, function(){ app.components.notification_tile	 = this.data.prepare(app.fw.colors()); bootstrap.ready("notification_tile") 	});
		app.fw.call("views/tiles/grandchildren.htm"  , null, function(){ app.components.grandchildren_tile 	 = this.data.prepare(app.fw.colors()); bootstrap.ready("grandchildren_tile") });
		app.fw.call("views/tiles/mood.htm"			 , null, function(){ app.components.moods_tile			 = this.data.prepare(app.fw.colors()); bootstrap.ready("moods_tile") 		});
		app.fw.call("views/tiles/clipping-status.htm", null, function(){ app.components.clipping_tile_status = this.data.prepare(app.fw.colors()); bootstrap.ready("clipping_tile_status")});

		app.fw.call("http://api.spumedata.com/charts/list", { hash: app.hash }, function() {
			this.data = this.data.json();
			if(this.status==200 && this.data.length){
				app.fw.storage("charts",this.data.stringify());

				let
				config = app.fw.hashit({
					hash: app.hash||app.fw.storage("hash")
					, cuid: app.fw.storage("current")||this.data[0].id
				});
				
				app.fw.call("http://api.spumedata.com/charts/retrieve", config, function(){
					if(this.status!=200||this.data.trim()=="[]") return app.fw.error("não foi possível carregar este gráfico...");
					app.spheres.content = this.data.json();
					bootstrap.ready("mrc_json");        
				});

				app.fw.call("http://api.spumedata.com/charts/feed", config, function(){ 
					app.feed = this.data.json();

					/***************************************************
								           _     _                      
					  __ _  __ _ _ __ ___ | |__ (_) __ _ _ __ _ __ __ _ 
					 / _` |/ _` | '_ ` _ \| '_ \| |/ _` | '__| '__/ _` |
					| (_| | (_| | | | | | | |_) | | (_| | |  | | | (_| |
					 \__, |\__,_|_| |_| |_|_.__/|_|\__,_|_|  |_|  \__,_|
					 |___/**********************************************/
						
						app.feed.feeds_by_parent_id = app.feed.feeds_by_parent_id.extract(function(){ return this.length ? this : null })

					/***************************************************/

					bootstrap.ready('feed_json');
				});

				app.fw.call("http://api.spumedata.com/push/latest", config, function(){ 
					// console.log(this.data.json());
					if(this.status==200&&this.data!="0"){
						app.notifications = this.data.json();
					}
					else{
						app.notifications = null;
						// bootstrap.onFinishLoading.add(function(){ 
						// 	$("header .--tab>div")[2].remClass("--pulse").anime({background: "#363636"}, ANIMATION_LENGTH);
						// });
					}
					$(".--notifications")[0].app(app.components.notifications.prepare(app.fw.colors()).morph()).evalute();
					bootstrap.ready('notification_json');
				});

				bootstrap.ready("charts_json");
			} else app.fw.error("Não foi possível carregar a lista de gráficos disponíveis...");
		});

		bootstrap.ready("init_script");
	} else{
		bootstrap.screens = {login:true};
		bootstrap.ready();
	}
})

bootstrap.onFinishLoading.add(function(){
	// hide every screen elements
	// $(".--screen.--splash")[0].dispatchEvent(__go);

	let
	scheme = app.fw.colors();

	if(app.hash){
		// lopad components
		$("body>footer")[0].app(app.components.footer.prepare(scheme).morph());
		
		scheme.main_title = app.spheres.content.context_title.toUpperCase();
		$("body>header")[0].app(app.components.header.prepare(scheme).morph());

		scheme.n_articles = app.feed.feeds_ammount;
		scheme.feeds_offset = 0;
		scheme.feeds_length = FEED_LENGTH;
		$(".--home.--clippings")[0].app(app.components.clippings.prepare(scheme).morph()).evalute();

		scheme.n_articles = app.spheres.content.articles_ammount;
		scheme.parent_limit = 5;
		$(".--home.--feeds")[0].app(app.components.feeds.prepare(scheme).morph()).evalute();

		var
		sugar = 0;

		setTimeout(()=>{
			app.body.app(
				app.fw.new()
					.addClass("-fixed -pointer --collapsebutton")
					.setStyle({
						bottom:"5em"
						, right:"2em"
						, borderRadius:"50%"
						, background: "rgba(255,255,255,.80)"
						, border: "1px solid " + app.fw.colors().concrete
						, boxShadow: "0 0 24px rgba(0,0,0,.64)"
						, opacity: 0
						, display: "none"
					})
					.app(
						app.fw.new("img")
							.setStyle({
								padding:".5em"
								, height: "2.75em"
								, width: "3em"
								, filter: "invert(1)"
								, transform: "scaleY(-1)"
							})
							.setAttr({
								src:"src/img/icons/nav-down.svg"
							})
					)
					.on("click",function(){
						switch(app.current){
							
							case CLIPPINGS: {
								$('.--clippingtile').each(function(){ if(this.dataset.state=='1') this.click(); });
								$('.--clippings .--stage')[0].scroll({top:0,behavior:'smooth'});
							} break;
							
							case FEEDS: {
								$('.--feedtile').each(function(){ if(this.dataset.state=='1') $('.--toggler',this)[0].click(); });
								// $('.--feedclosetabs')[0].anime({opacity:0}, ANIMATION_LENGTH, 1, function(){ $('.--feedtile').anime({opacity:1}); });
								$('.--feeds .--stage')[0].scroll({top:0,behavior:'smooth'});
								$('.--closechildren').each(function(){ if(this.dataset.state=='1') this.click(); });
							} break;

							case NOTIFICATIONS: {
								$('.--notificationtile').anime({maxHeight:'4em'}).setData({state:"0"})
							} break;

						}

						return this.desappear();

					})
			)
		}, ANIMATION_LENGTH);

	} else setTimeout(()=>{ app.pragma = LOGIN; }, ANIMATION_LENGTH);

})

app.onPragmaChange.add(function(x){

	if(!app.hash) return;
	// $("header .--tab").remClass("--active");
	$(".--overlay").setStyle({display:"none"});
	$("footer .--footer>div").anime({opacity:1});
	// $(".--home.--feeds")[0].anime({paddingTop:"4em"});
	let
	hideCollapseButton = true;

	switch(x){
		case START:
		 	$(".--backbutton").anime({opacity:.25},ANIMATION_LENGTH/4);
			// $('.--clippingtile .--fadebar').anime({opacity:1});
			$(".--home.--feeds")[0].stop().anime({translateY:"calc(50vh - 3em)"},ANIMATION_LENGTH/2);
			$(".--overlay").setStyle({display:"block"});
		 	// $(".--feedclosetabs, .--clippingclosetabs").each(function(){ this.click(); });

		 	
		 	// CP
		 	$('.--clippingtile').each(function(){ if(this.dataset.state=='1') this.click(); });
			$('.--clippings .--stage')[0].scroll({top:0,behavior:'smooth'});
			// FEED
			$('.--feedtile').each(function(){ if(this.dataset.state=='1') $('.--toggler',this)[0].click(); });
			// $('.--feedclosetabs')[0].anime({opacity:0}, ANIMATION_LENGTH, 1, function(){ $('.--feedtile').anime({opacity:1}); });
			$('.--feeds .--stage')[0].scroll({top:0,behavior:'smooth'});
			$('.--closechildren').each(function(){ if(this.dataset.state=='1') this.click(); });
			// NOT
			$('.--notificationtile').anime({maxHeight:'4em'}).setData({state:"0"})
		break;
		
		case CLIPPINGS:
			$("footer .--footer>div").anime({opacity:.4});
			$(".--backbutton").anime({opacity:1},ANIMATION_LENGTH);
			$("footer .--footer>div")[0].anime({opacity:1});
			// $(".--feedclosetabs").each(function(){ this.click(); });
			$(".--clippingtile").each(function(){ if(this.dataset.state=="1") hideCollapseButton=false; });
		break;

		case FEEDS:
			$("footer .--footer>div").anime({opacity:.4});
			$(".--backbutton").anime({opacity:1},ANIMATION_LENGTH);
			$("footer .--footer>div")[1].anime({opacity:1});
			// $(".--clippingclosetabs").each(function(){ this.click(); });
			$(".--feedtile").each(function(){ if(this.dataset.state=="1") hideCollapseButton=false; });

		break;
		
		case MENU:
			$(".--backbutton").anime({opacity:1},ANIMATION_LENGTH);
		break;

		case CHARTCHANGE:
			$(".--backbutton").anime({opacity:1},ANIMATION_LENGTH);
		break;

		case LOGIN:
			
		break;

		case WHOWEARE:
			
		break;

		case ABOUT:
			
		break;

		case POLICY:
			
		break;

		case NOTIFICATIONS:
			$("footer .--footer>div").anime({opacity:.25});
			$(".--backbutton").anime({opacity:1},ANIMATION_LENGTH);
			$(".--notificationtile").each(function(){ if(this.dataset.state=="1") hideCollapseButton = false; });
			$("footer .--footer>div")[2].anime({opacity:1});
		break;
	}

	if(hideCollapseButton) $(".--collapsebutton").desappear(); else $(".--collapsebutton").appear();
})

// controlling scroll usage
app.declare({
	__swipe : new Swipe(app.body)
});

__swipe.up(()=>{ 
	// console.log("upping")
	if(app.current==LOGIN) return;
	if(app.current==START) app.pragma = FEEDS;
	if(app.current==CLIPPINGS) $(".--collapsebutton").appear();
	// 	let
	// 	el = $(".--home.--clippings .--stage .--clippingbytime")[0];
	// 	console.log(el);
	// 	if(!el.dataset.offset||el.dataset.offset*1<FEED_LOADS){
	// 		el.dataset.offset = (el.dataset.offset||1)*1+1;
	// 		let
	// 		tile = app.components.clipping_tile
	// 		, feeds = app.feed.feeds.slice(el.dataset.offset*FEED_LENGTH, el.dataset.offset*FEED_LENGTH+FEED_LENGTH);

	// 		for(let i=0;i<FEED_LENGTH;i++){
	// 			console.log(i);
	// 			if(feeds[i]){
	// 				let
	// 				feed = feeds[i]
	// 				, date = Math.floor(Math.abs( (new Date()) - (new Date(feed.timestamp)))/1000/60)
	// 				, date_text = "min"
	// 				, el = tile;

	// 				if(date>60){ 
	// 					date = Math.floor(date/60);
	// 					date_text = "hrs";
	// 				}

	// 				el = el.prepare({
	// 					date_value	: date
	// 					, date_text	: date_text
	// 					, title		: feed.title
	// 					, source	: feed.source || ""
	// 					, link		: (feed.link.indexOf("http")+1) ? feed.link : "https://twitter.com/"+feed.link
	// 					, summary	: feed.content
	// 				}).morph();

	// 				$(".--clippings .--stage")[0].app(el);
	// 			}
	// 		}
	// 	}
	// }

});
__swipe.down(()=>{
	if(app.current==LOGIN) return;
	if(app.current==START||(app.current==FEEDS&&$(".--home.--feeds .--stage")[0].scrollTop==0)) app.pragma = CLIPPINGS;
});
__swipe.right(()=>{
	if(app.current==LOGIN) return;

	// PUXAR PARA ESQUERDA DESABILITADO
	// else  app.pragma = START;
	
	// else app.pragma = NOTIFICATIONS;
});
__swipe.left(()=>{ 
	if(app.current==LOGIN) return;
	// else if(app.current == NOTIFICATIONS) app.pragma = START;
	
	// PUXAR PARA DIREITA DESABILITADO
	// else app.pragma = NOTIFICATIONS;
});
__swipe.fire();