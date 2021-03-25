class Graph {

    preload(o){
        this.target = this.target ? this.target : o.target || $("#app")[0];
        this.width  = o.w = this.target.getBoundingClientRect().width;
        this.height = o.h = this.target.getBoundingClientRect().height;
        if(!this.node) this.node = _S("svg", "-absolute -zero -wrapper --graph --type-"+(o.type || "std"), null, o.css || { background:"transparent" }).app(_S("defs")).attr({ width: o.w, height: o.h });
    }


    axis(o){
        if(this.node&&!o.noaxis){

            this.preload(o);
            
            let
            color = o.axis&&o.axis.css&&o.axis.css.color ? o.axis.css.color : o.css.color || app.colors("FONT")
            , fsize = o.axis&&o.axis.css&&o.axis.css.fontSize ? o.axis.css.fontSize.replace(/[^0-9]/g,'')*1 : 12
            , dy = [
                "M"
                , [ 0, o.h ]
                , "L"
                , [ fsize*3+8, o.h-fsize-12 ]
                , [ fsize*3+8, 0]
            ].join(" ")
            , dx = [
                "M"
                , [ 0, o.h ]
                , "L"
                , [ fsize*3+12, o.h-fsize-8 ]
                , [ o.w, o.h-fsize-8]
            ].join(" ")

            this.node.get("defs").at().app(
                _S("linearGradient", "--graph-axis-gradient --gag", { id:"xaxis-gradient", x1:"100%", y1:"0%", x2:"0%", y2:"100%", gradientUnits:"userSpaceOnUse" })
                /**/ .app(_S("stop", "--gradient-stop --stop1", { offset:"0%" }, { "stop-color":color, "stop-opacity":.64 }))
                /**/ .app(_S("stop", "--gradient-stop --stop2", { offset:"100%" }, { "stop-color":color, "stop-opacity":.04 }))
            ).app(
                _S("linearGradient", "--graph-axis-gradient --gag", { id:"yaxis-gradient", x1:"0%", y1:"0%", x2:"0%", y2:"100%", gradientUnits:"userSpaceOnUse" })
                /**/ .app(_S("stop", "--gradient-stop --stop1", { offset:"0%" }, { "stop-color":color, "stop-opacity":.64 }))
                /**/ .app(_S("stop", "--gradient-stop --stop2", { offset:"100%" }, { "stop-color":color, "stop-opacity":.04 }))
            );

            if(!o.noxaxis) this.node.app(_S("path", "--axis --x", { d: dx }, { "stroke-width":1, fill:"none", stroke: "url(#xaxis-gradient)" }))
            if(!o.noyaxis) this.node.app(_S("path", "--axis --y", { d: dy }, { "stroke-width":1, fill:"none", stroke: "url(#yaxis-gradient)" }))
        }
    }

    guides(o){

        if(this.node&&!o.noguides){

            this.preload(o);

            let
            xdots = o.xdots || 4
            , ydots = o.ydots || 2
            , fsize = o.guides&&o.guides.css&&o.guides.css.fontSize ? o.guides.css.fontSize.replace(/[^0-9]/g,'')*1 : 12
            , color = o.guides&&o.guides.css&&o.guides.css.color ? o.guides.css.color : o.css.color || app.colors("FONT")
            , xpace = Math.ceil((o.w - fsize - 8) / xdots)
            , ypace = Math.ceil((o.h - fsize - 8) / ydots)
            , node = this.node
            ;

            node.get("defs").at().app(
                _S("linearGradient", "--graph-guides-gradient --x --ggg", { id:"xguide-gradient", x1:"0%", y1:"0%", x2:"0%", y2:"100%", gradientUnits:"userSpaceOnUse" })
                /**/.app(_S("stop", "--gradient-stop --stop1", { offset:"0%"   }, { "stop-color":color, "stop-opacity": 0.00 }))
                /**/.app(_S("stop", "--gradient-stop --stop2", { offset:"50%"  }, { "stop-color":color, "stop-opacity": 0.08 }))
                /**/.app(_S("stop", "--gradient-stop --stop3", { offset:"100%" }, { "stop-color":color, "stop-opacity": 0.00 }))
            ).app(
                _S("linearGradient", "--graph-guides-gradient --y --ggg", { id:"yguide-gradient", x1:"0%", y1:"0%", x2:"100%", y2:"0%", gradientUnits:"userSpaceOnUse" })
                /**/.app(_S("stop", "--gradient-stop --stop1", { offset:  "0%" }, { "stop-color":color, "stop-opacity": 0.00 }))
                /**/.app(_S("stop", "--gradient-stop --stop2", { offset: "50%" }, { "stop-color":color, "stop-opacity": 0.08 }))
                /**/.app(_S("stop", "--gradient-stop --stop3", { offset:"100%" }, { "stop-color":color, "stop-opacity": 0.00 }))
            );

            !o.noxguides&&app.iterate(0, xdots+1, i => {
                node.app(_S("path", "--guide --x", { d: ["M", [ i*xpace, 0 ], "L", [ i*xpace, o.h - fsize - 8 ] ].join(" ") }, { stroke: "url(#xguide-gradient)", "stroke-width":1 }))
            });

            !o.noyguides&&app.iterate(0, ydots, i => {
                i&&node.app(_S("path", "--guide --y", { d: [ "M", [ fsize + 8, i*ypace + 2 ], "L", [ o.w, i*ypace+2] ].join(" ") }, { stroke: "url(#yguide-gradient)", "stroke-width":1 }))
            });
        }

    }

    labels(o){
        if(this.node&&!o.nolabels){

            this.preload(o);

            if(o.log){
                
            }else{

            }

            let
            xdots   = o.xdots || 4
            , ydots = o.ydots || 2
            , ymax  = o.series.extract(s => s.calc(MAX)||1).calc(MAX) * 1.1
            , xlen  = o.series.extract(s => s.length||1).calc(MAX) / xdots
            , ylen  = ymax / ydots
            , fsize = o.labels&&o.labels.css&&o.labels.css.fontSize ? o.labels.css.fontSize.replace(/[^0-9]/g,'')*1 : 12
            , color = o.labels&&o.labels.css&&o.labels.css.color ? o.labels.css.color : o.css.color || app.colors("FONT")
            , xpace = Math.ceil((o.w - fsize - 8) / xdots)
            , ypace = Math.floor((o.h - fsize - 8) / ydots)
            , node  = this.node
            ;            

            !o.noxlabels&&app.iterate(0, xdots, i => node.app(
                _S("text", "--label --x", { y: o.h - 4, x: (i+1)*xpace }, { "font-size":fsize, stroke: color, "text-anchor" : "end", opacity:.32 })
                /**/.text(o.labels&&o.labels[Math.ceil((i+1)*xlen)] ? o.labels[Math.ceil((i+1)*xlen)] : (o.labels&&o.labels[Math.ceil((i+1)*xlen-1)] ? o.labels[Math.ceil((i+1)*xlen-1)] : i))
            ));

            !o.noylabels&&app.iterate(0, ydots , i => {
                if(i<ydots) node.app(
                    _S("text", "--label --y", { y: i * ypace+16, x: 4 }, { "font-size":fsize, stroke: color, "text-anchor" : "start", opacity:.32 }).text(Math.ceil(ymax/(i+1)).nerdify())
                )
            })
            ;
        }
    }

    draw(o){
        if(this.node){

            // this.preload(o);

            let
            h = o.h
            , xmax = o.series.extract(s => s.length).calc(MAX)
            , ymax = Math.max(1, o.series.extract(s => s.calc(MAX)).calc(MAX)*1.2)
            , css  =__bind__({
                fontSize:12
                , "stroke-width": [2]
                , color: [ "#000" ]
            }, o.lines&&o.lines.css ? o.lines.css : o.css || { color: app.colors("FONT") })
            , fsize = (css.fontSize+"").replace(/[^0-9]/g,'')*1
            , color = o.colors || (css.color || app.colors("FONT"))
            , strokeWidth = css["stroke-width"] || 2
            , strokeDasharray = css["stroke-dasharray"] 
            , xpace = (o.w - fsize*3 - 8) / xmax
            , node = this.node
            , type = o.type || "line"
            ;

            color = Array.isArray(color) ? color : [ color ];
            strokeWidth = Array.isArray(strokeWidth) ? strokeWidth : [ strokeWidth ];
            strokeDasharray = Array.isArray(strokeDasharray) ? strokeDasharray : [ strokeDasharray ];

            // delete css.color;
            // delete css["stroke-width"];

            switch(type.toLowerCase()){
                case "line"     : type = "L"    ; break;
                case "default"  : type = "L"    ; break;
                case "smooth"   : type = "S"    ; break;
                case "curve"    : type = "C"    ; break;
                default         : type = o.type || "L" ; break;
            }

            // GRADIENT
            node.get("defs").at().app(
                _S("linearGradient", "--graph-lines-gradient --x --ggg", { id:"xguide-gradient", x1:"0%", y1:"0%", x2:"0%", y2:"100%", gradientUnits:"userSpaceOnUse" })
                /**/.app(_S("stop", "--gradient-stop --stop1", { offset:"0%"   }, { "stop-color":"#000", "stop-opacity": 0.16 }))
                /**/.app(_S("stop", "--gradient-stop --stop3", { offset:"100%" }, { "stop-color":"#000", "stop-opacity": 1.00 }))
            );            

            o.series.each((s, idx) => {

                let
                clr = color[idx] ? color[idx] : color[0]
                , strw = strokeWidth[idx] ? strokeWidth[idx] : strokeWidth[0]
                , strda = strokeDasharray[idx]!==null ? strokeDasharray[idx] : strokeDasharray[0]
                ;

                let
                d = [ "M", [ fsize*3 + 8, h - fsize - 8 - (h- fsize - 8) * s[0] / ymax ], type ];
                s.each((n,i) => {
                    let
                    x = i * xpace + fsize*3 + 8
                    , y =  h - fsize - 8 - (h- fsize - 8) * n / ymax
                    , rect = type == "bars" ? [] : $("#"+ node.uid() +" .-hint-plate.--iter"+i)
                    ;

                    if(!rect.length) {
                        rect = 
                        _S("rect", "-hint-plate -pointer --tooltip --iter"+i, { 
                            width: xpace - (type == "bars" ? 4 : 0)
                            , height: h - fsize - 8 - (type == "bars" ? y : 0)
                            , x: x + (type== "bars" ? 2 : -xpace/2)
                            , y: type== "bars" ? y : 0
                            , "data-tip": n!=null ? (o.labels&&o.labels[i] ? o.labels[i] : "") +"<br>-<br>"+ (o.names&&o.names[idx] ? o.names[idx] + " " : "") + n.nerdify() + "<br>" : ""
                        }, { fill: clr, opacity:type == "bars" ? .64 : 0 });

                        rect.on("mouseenter", function(){ 
                            $("#"+node.uid()+" .-hint-plate").not(this).stop().anime({ opacity:type == "bars" ? .32 : 0 });
                            this.css({ opacity: type == "bars" ? 1 : .16 })
                        }).on("mouseleave", function(){ 
                            $("#"+node.uid()+" .-hint-plate").stop().anime({ opacity:type == "bars" ? .64 : 0 }) 
                        });
                        node.app(rect)
                    }else rect[0].dataset.tip = rect[0].dataset.tip + (o.names&&o.names[idx] ? o.names[idx] + " " : "") + n.nerdify() + "<br>";

                    if(i&&type!="bars"){
                        if(type=="C"){
                            d.push([ x - xpace / 2, y ]);
                            d.push([ x - xpace / 4, y ]);
                        }
                        d.push([ x, y ])
                    }
                });

                if(type=="S" && s.length%2==0) d.push([ o.w, h - fsize - 8 - (h- fsize - 8) * s.last() / ymax ]);
                if(type!="bars") node.app(_S("path", "--line -avoid-pointer", { d:d.join(" ") }, { fill:"none", stroke: clr, "stroke-width": strw, "stroke-dasharray": strda }))
            })
            tooltips();
        }
    }

    load(o){

        this.preload(o);
        
        if(Array.isArray(o.series)&&o.series.length){ if(!Array.isArray(o.series[0])) o.series = [ o.series ] }
        else o.series = [];

        o.css = o.css || { color: app.colors("FONT") };
        o.css.color = o.css.color || app.colors("FONT");

        this.target.app(this.node);
        this.axis(o);
        this.guides(o);
        this.labels(o);
        if(o.series.extract(s => s.length).calc()) this.draw(o);

        /* BASE SERIES CREATION */
        
        // if(o.series.length){

        //     this.length_ = 0;
        //     o.series.each(serie => this.length_ = Math.max(this.length_, serie.length));

        //     this.max_ = 0;
        //     o.series.each(serie => this.max_ = Math.max(this.max_, serie.calc(MAX)));

        //     this.axis(o);
        //     this.guides(o);

        //     this.series_ = o.series || []; 
        // }
        return this
    }

    constructor(o){
        if(o) this.load(o)
    }
}