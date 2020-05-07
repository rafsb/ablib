var
_num = function(n){
    return n >= 1000000 ? ((n/1000000).toFixed(1)*1)+"mi" : (n >= 1000 ? ((n/1000).toFixed(1)*1)+"k" : Math.ceil(n)+"")
}
/*
 SPLIT_TYPE(obj = {
    target: graph.target
    , serie: serie
    , log: false
    , xserie: [ -7, 0, 7 ]
    , graph: {
        type:    "split"
        , color: [ clr.black, clr.PASTEL ]
        , flags: [ "hoje", tmp.angle_factor + "x" ]
    } 
    , lines: {
        type: "wave"
        , fill:      [ clr.black, clr.PASTEL ]
        , bullets:   [ true, false ]
        , dasharray: [ false, [ 4,2,1,2 ] ]
        , axis:      [ 2, clr.LIGHT4 ]
        , guide:     [ 1, clr.LIGHT2 ]
    }
})
*/
class Graph {
    
    draw(){        
        if(this.o.target) this.o.target.empty().app(this.svg);
        tooltips();
        return this.svg
    }
    dseries(){
        var
        t = this.o.type
        , fsize = this.o.fsize || 10
        , w = this.o.width-fsize*2
        , h = this.o.height-fsize*2
        , svg = this.svg
        , log = this.o.log
        , series = this.o.series
        , series_length = this.o.series.extract(function(){ return this.length }).calc(MAX)
        , lines = this.o.lines || {}
        , yaxis = this.yaxis
        , names = this.o.names || []
        , length = this.o.length
        , max = yaxis.last()
        , min = yaxis.first()
        , _xpos = function(n){

            if(!(n*1)) return fsize;

            var 
            pace = w/(series_length-2)
            , x = n*pace;

            return Math.min(w, Math.max(0,x))+fsize
        }
        , _ypos = function(n){

            if(n*1===0) return h+fsize;
            if(!n) return null;

            var
            x = h
            , yiter = 0
            , pace = h/(yaxis.last() - yaxis.first())
            ;

            if(log){
                if(n){
                    let
                    logmax = 1;
                    while(logmax<max) logmax*=10;
                    x = h-h*Math.log(n*1,10)/Math.log(logmax,10)
                }
            }
            else x = h-(n-yaxis.first())*pace;

            return Math.min(h, Math.max(0,x))+fsize
        }
        , stop = series.extract(function(){ return this.length }).calc(MAX)
        ;

        lines = bind({series: [], stroke: "black 1 1 1" }, lines);        

        series.each((serie, iter) =>{
            var
            clsname = (this.o.names&&this.o.names[iter]?this.o.names[iter]:"noname").replace(/[^0-9a-zA-Z]/g,"")
            , path = [ "M", [ _xpos(0), _ypos(serie[0]) ], "L" ]
            , bullets = lines.series[iter]&&lines.series[iter].bullets ? lines.series[iter].bullets.split(/\s+/g) : (lines.bullets ? lines.bullets.split(/\s+/g) : [ "transparent", 2, "transparent", 1 ])
            , head = lines.series[iter]&&lines.series[iter].head ? lines.series[iter].head.split(/\s+/g) : [ "transparent", 4, "transparent", 2 ]
            , tail = lines.series[iter]&&lines.series[iter].tail ? lines.series[iter].tail.split(/\s+/g) : [ "transparent", 4, "transparent", 2 ]
            , linepath = [ 
                lines.series[iter]&&lines.series[iter].stroke ? lines.series[iter].stroke.split(/\s+/g)[0] : (lines.stroke.split(/\s+/g)[0] || "black")
                , lines.series[iter]&&lines.series[iter].stroke ? lines.series[iter].stroke.split(/\s+/g)[1] : (lines.stroke.split(/\s+/g)[1] || 1)
                , lines.series[iter]&&lines.series[iter].stroke ? lines.series[iter].stroke.split(/\s+/g)[2] : (lines.stroke.split(/\s+/g)[2] || 1)
                , lines.series[iter]&&lines.series[iter].stroke ? lines.series[iter].stroke.split(/\s+/g)[3] : (lines.stroke.split(/\s+/g)[3] || null)
            ]
            , last  = false
            , first = true
            , wv
            ;

            switch(t){
            
                case "split": {

                    var
                    split   = this.o.splitmark || Math.ceil(serie.length/2)
                    , path2 = [ "M", [ _xpos(split-1), _ypos(serie[split]) ], "L"]
                    , wv, wv2
                    , s_bullets  = lines.series[iter+1]&&lines.series[iter+1].bullets ? lines.series[iter+1].bullets.split(/\s+/g) : (lines.bullets ? lines.bullets.split(/\s+/g) : [ "black", 2, "black", 1 ])
                    , s_head     = lines.series[iter+1]&&lines.series[iter+1].head ? lines.series[iter+1].head.split(/\s+/g) : [ "transparent", 4, "transparent", 2 ]
                    , s_tail     = lines.series[iter+1]&&lines.series[iter+1].tail ? lines.series[iter+1].tail.split(/\s+/g) : [ "transparent", 4, "transparent", 2 ]
                    , s_linepath = [ 
                        lines.series[iter+1]&&lines.series[iter+1].stroke ? lines.series[iter+1].stroke.split(/\s+/g)[0] : (lines.stroke.split(/\s+/g)[0] || "black")
                        , lines.series[iter+1]&&lines.series[iter+1].stroke ? lines.series[iter+1].stroke.split(/\s+/g)[1] : (lines.stroke.split(/\s+/g)[1] || 1)
                        , lines.series[iter+1]&&lines.series[iter+1].stroke ? lines.series[iter+1].stroke.split(/\s+/g)[2] : (lines.stroke.split(/\s+/g)[2] || 1)
                        , lines.series[iter+1]&&lines.series[iter+1].stroke ? lines.series[iter+1].stroke.split(/\s+/g)[3] : (lines.stroke.split(/\s+/g)[3] || null)
                    ]
                    ;
                    

                    for(var i=0; i<stop;i++){

                        if(i==stop-1 || i==split-1) last = true;
                        else last = false;

                        if(!i || i==split) first = true;
                        else first = false;

                        // if(last) console.log("last", i, head, s_head)
                        // if(first) console.log("first", i, tail, s_tail)

                        let
                        left = _xpos(i<split ? i : i-1)
                        , top = _ypos(serie[i])
                        , rad = i<split ? (first&&tail[1] ? tail[1] : (last&&head[1] ? head[1] : (bullets[1] || 2))) : (first&&s_tail[1] ? s_tail[1] : (last&&s_head[1] ? s_head[1] : (s_bullets[1] || 2)))
                        , fill = i<split ? (first&&tail[0] ? tail[0] : (last&&head[0] ? head[0] : (bullets[0] || "black"))) : (first&&s_tail[0] ? s_tail[0] : (last&&s_head[0] ? s_head[0] : (s_bullets[0] || "black")))
                        , stroke = i<split ? (first&&tail[2] ? tail[2] : (last&&head[2] ? head[2] : (bullets[2] || "black"))) : (first&&s_tail[2] ? s_tail[2] : (last&&s_head[2] ? s_head[2] : (s_bullets[2] || "black")))
                        , stroke_width = i<split ? (first&&tail[3] ? tail[3] : (last&&head[3] ? head[3] : (bullets[3] || 1))) : (first&&s_tail[3] ? s_tail[3] : (last&&s_head[3] ? s_head[3] : (s_bullets[3] || 1)))
                        ;

                        if(i<split) path.push([ left, top ]);
                        else path2.push([ left, top ]);

                        /* TOOLTIPS */
                        svg.app(
                            __svg("circle", "--bullet -pointer --tooltip" + (first?" --tail":"") + (last?" --head":""), { 
                                cx: left
                                , cy: top
                                , r: rad*2
                                , "data-tip": (names.length&&names[iter]?names[iter]+" ":"") + _num(serie[i])
                            }, { 
                                fill: fill
                                , stroke: stroke
                                , "stroke-width": stroke_width
                            })
                        )
                    }

                    path2[1] = path2[3] = path.last();


                    /* SEQUENCE 2 */
                    svg.app(__svg("path", "--default-sequence --serie --s"+iter, { d: path2.join(' ') }, bind({ 
                        stroke: s_linepath[0], "stroke-width": s_linepath[1], fill:"none", opacity: s_linepath[2]}, s_linepath[3] ? { "stroke-dasharray": s_linepath[3] } : {})));

                    /* SEQUENCE 1 */
                    svg.app(__svg("path", "--default-sequence --serie --s"+iter, { d: path.join(' ') }, bind({ 
                        stroke: linepath[0], "stroke-width": linepath[1], fill:"none", opacity: linepath[2]}, linepath[3] ? { "stroke-dasharray": linepath[3] } : {})));
                    
                    if(lines.type=="wave"){
                        let
                        gd1 = "fagd"+app.nuid()
                        , gd2 = "fagd"+app.nuid()
                        ;
                        
                        /* WAVE 2 */
                        svg.get("defs").at().app(
                            __svg("linearGradient", "--graph-gradient --gd1", { id:gd2, x1:"50%", y1:"0%", x2:"50%", y2:"100%", gradientUnits:"userSpaceOnUse" })
                            .app(__svg("stop", "--gradient-stop --stop1", { offset:"0%" }, { "stop-color":s_linepath[0], "stop-opacity":.24 }))
                            .app(__svg("stop", "--gradient-stop --stop2", { offset:"95%" }, { "stop-color":s_linepath[0], "stop-opacity":0 }))
                        )
                        wv2 = path2.concat([ [ w+fsize, h ], [ _xpos(split-1), h ], "z" ]);
                        svg.pre(__svg("path", "--split-wave0 --serie", { d: wv2.join(' '), fill:"url(#"+gd2+")" }, { stroke: "none" }))

                        /* WAVE 1 */
                        svg.get("defs").at().app(
                            __svg("linearGradient", "--graph-gradient --gd1", { id:gd1, x1:"50%", y1:"0%", x2:"50%", y2:"100%", gradientUnits:"userSpaceOnUse" })
                            .app(__svg("stop", "--gradient-stop --stop1", { offset:"0%" }, { "stop-color":linepath[0], "stop-opacity":.24 }))
                            .app(__svg("stop", "--gradient-stop --stop2", { offset:"95%" }, { "stop-color":linepath[0], "stop-opacity":0 }))
                        )
                        wv = path.concat([ [ _xpos(split-1), h ], [ _xpos(0), h ], "z" ]);
                        svg.pre(__svg("path", "--split-wave0 --serie", { d: wv.join(' '), fill:"url(#"+gd1+")" }, { stroke: "none" }))
                    }
                } break;

                case "bars":{
                    var
                    clr = lines.series&&lines.series[iter]&&lines.series[iter].color ? lines.series[iter].color: (lines.color ? lines.color : "black")
                    , rects = [ ]
                    ;

                    //w = w-fsize*2;

                    /* SEQUENCE */
                    for(var i=0; i<stop; i++){
                        var
                        left = Math.min(w,w*i/(stop+1)+fsize*1.5)
                        , top = _ypos(serie[i]*1)
                        ;                        
                        svg.app(__svg("rect", "--bar -pointer --tooltip", { 
                            y: top
                            , x: left + fsize/2
                            , width: fsize
                            , height: h
                            , "data-tip": (names.length&&names[iter]?names[iter]+" ":"")+_num(serie[i])
                        }, { fill:clr, stroke:"none" }))
                    }

                } break;
            
                default: {
                    var
                    path = [ "M", [ fsize*1.5, _ypos(serie.first()) ], "L" ]
                    , clr = lines.colors&&lines.colors[iter]? lines.colors[iter] : "black"
                    , wv
                    , first = true
                    , last = false
                    ;

                    for(var i=1; i<stop;i++){
                        
                        if(i==stop-1) last = true;

                        var
                        left = Math.max(fsize, Math.ceil(w*i/(stop-1)-(i?0:fsize)))
                        , top = _ypos(serie[i])
                        ;

                        if(top!==null){
                            path.push([ left, top ]);

                            /* TOOLTIPS */
                            svg.app(
                                __svg("circle", "--bullet -pointer --tooltip --"+clsname, {
                                    cx: Math.ceil(left + (!i?4:(i==stop-1?-4:0)))
                                    , cy: top
                                    , r:4
                                    , "data-tip": (names.length&&names[iter]?names[iter]+" ":"")+_num(serie[i])+"\n"
                                }, { 
                                    opacity: lines.bullets&&lines.bullets[iter] ? 1 : (!i&&lines.tails&&lines.tails[iter] ? 1: (i==stop-1&&lines.heads&&lines.heads[iter] ? 1 : 0)) 
                                    , stroke:clr
                                    , "stroke-width": first || last ? 2 : (lines.width || 1)
                                    , fill: (i==stop-1&&lines.heads ? lines.heads[iter] || clr : (!i&&lines.tails ? lines.tails[iter] || clr : (lines.bullets ? lines.bullets[iter] || clr : clr)))
                                })
                            )
                        }

                        if(first&&i) first=false;
                    }

                    svg
                    /* SEQUENCE 1 */
                    .app(__svg("path", "--default-sequence --serie --s"+iter+" --"+clsname, { d: path.join(' ') }, bind({ 
                        stroke: clr
                        , "stroke-width": lines.width
                        , fill:"none" 
                        , opacity: this.o.lines.nopath&&this.o.lines.nopath[iter] ? .05 : 1
                    }, lines.dasharray&&lines.dasharray[iter] ? { "stroke-dasharray": lines.dasharray[iter] } : {})));
                    
                    if(lines.type=="wave"){
                        let
                        gd = "fagd"+app.nuid();

                        svg.get("defs").at().app(
                            __svg("linearGradient", "--graph-gradient --gd1", { id:gd, x1:"50%", y1:"0%", x2:"50%", y2:"100%", gradientUnits:"userSpaceOnUse" })
                            .app(__svg("stop", "--gradient-stop --stop1", { offset:"0%" }, { "stop-color":clr, "stop-opacity":.24 }))
                            .app(__svg("stop", "--gradient-stop --stop2", { offset:"100%" }, { "stop-color":clr, "stop-opacity":0 }))
                        )

                        wv = path.concat([ [ w, h+fsize ], [ fsize, h+fsize ], "z" ]);

                        /* WAVE */
                        svg.pre(__svg("path", "--split-wave1 --serie", { d: wv.join(' '), fill:"url(#"+gd+")" }, { stroke: "none" }))
                    }
                } break;
            }
        })
        
        return this
    }
    














    lines(lines={}){
        lines = lines || {};
        
        lines.class = (lines.class||"--auto")+" --serie --line";
        lines.type  = lines.type||"std";
        lines.color = lines.color||app.colors().FONT
        lines.gradient = lines.gradient || [ lines.color, "transparent" ]
        
        lines.css = bind({
            "stroke-width":2
            , stroke:app.colors().BLACK
            , fill: "none"
            , "stroke-dasharray": "none"
            , "stroke-linecap":"round"
        },lines.css||{})
        
        [ "heads", "tails", "bullets" ].each(key => {
            lines[key] = lines[key] || { css:{}, attr:{} }
            lines[key] ={
                css: bind({
                    "stroke-width":1
                    , stroke:app.colors().BLACK
                    , fill: app.colors().BACKGROUND
                }, lines[key].css || {})
                , attr: bind({
                    r: 2
                }, lines[key].attr || {})
            }
        })

        this.lines_ = lines;

        return this
    }

    __makex__(o){
        let
        l = o.length || 0
        , x = o.x || []
        ;
        if(Array.isArray(x)&&x.length) this.x_ = x;
        else if(l){
            x = array(this.length_)
            x.each((el,i) => x[i]=i)
        }
        return this;
    }

     __makey__(o){
        var
        y = o.y || []
        , max = o.max
        ;
        if(Array.isArray(x)&&y.length) this.y_ = y;
        else if(max){
            if(o.log){
                let 
                i=1
                while(i<=max){
                    i*=10;
                    y.push(i)
                }
            }else{
                max = Math.ceil(max*1.1/10)*10;
                y = [ 0, max/2, max ]
            }
        }
        this.y_ = y;
        return this
    }

    guides(o){
        let
        x = o.x
        , y = o.y;
         if(x){
            let
            id = app.nuid()
            ;
            this.svg_.get("defs")[0].app(
                __svg("linearGradient", "--graph-gradient --gd1", { id:gdx, x1:"0%", y1:"0%", x2:"0%", y2:"100%", gradientUnits:"userSpaceOnUse" })
                    .app(__svg("stop", "--gradient-stop --stop1", { offset:"10%" }, { "stop-color":clr, "stop-opacity":.08 }))
                    .app(__svg("stop", "--gradient-stop --stop2", { offset:"50%" }, { "stop-color":clr, "stop-opacity":.32 }))
                    .app(__svg("stop", "--gradient-stop --stop2", { offset:"90%" }, { "stop-color":clr, "stop-opacity":.08 }))
                );

            

         }
        if(o){
            let
            gdx = "fagd"+app.nuid()
            , gdy = "fagd"+app.nuid()
            , gdxy = "fagd"+app.nuid()
            , lines = this.o.lines
            , clr = lines.color || "black"
            ;

            this.svg.get("defs").at().app(
                
            ).app(
                __svg("linearGradient", "--graph-gradient --gd1", { id:gdy, x1:"0%", y1:"0%", x2:"100%", y2:"0%", gradientUnits:"userSpaceOnUse" })
                .app(__svg("stop", "--gradient-stop --stop1", { offset:"10%" }, { "stop-color":clr, "stop-opacity":.08 }))
                .app(__svg("stop", "--gradient-stop --stop2", { offset:"50%" }, { "stop-color":clr, "stop-opacity":.32 }))
                .app(__svg("stop", "--gradient-stop --stop2", { offset:"90%" }, { "stop-color":clr, "stop-opacity":.08 }))
            ).app(
                __svg("linearGradient", "--graph-gradient --gd1", { id:gdxy, x1:"0%", y1:"100%", x2:"100%", y2:"50%", gradientUnits:"userSpaceOnUse" })
                .app(__svg("stop", "--gradient-stop --stop1", { offset:"10%" }, { "stop-color":clr, "stop-opacity":.08 }))
                .app(__svg("stop", "--gradient-stop --stop2", { offset:"50%" }, { "stop-color":clr, "stop-opacity":.32 }))
                .app(__svg("stop", "--gradient-stop --stop2", { offset:"90%" }, { "stop-color":clr, "stop-opacity":.08 }))
            )

            let
            t = this.o.type
            , w = this.o.width
            , h = this.o.height
            , svg = this.svg
            , ya = this.o.yaxis || this.__makey__()
            , xa = this.o.xaxis || this.__makex__()
            , axiscss = { stroke: "url(#"+gdxy+")", fill: "none", "stroke-width": lines.width || 1 }
            , guidecss = { 
                fill: "none"
                , "stroke-width": lines.width || 1
                , opacity:.32
                , "stroke-dasharray":1 
            }
            , fsize = this.o.fsize || 10
            , fcolor = this.o.fcolor || "black"
            , maxx = 0
            ;

            this.yaxis = ya;
            this.xaxis = xa;

            this.o.series.each(serie => maxx = Math.max(maxx, serie.length));
            --maxx;

            /* AXIS */
            if(!this.o.noaxis) svg.app(__svg("path", "--axis --y --x", { d: ["M", [ 0, 0 ], "L", [ 0, h ], [ w,h ] ].join(" ") }, axiscss))

            /* GUIDES Y */
            if(!this.o.noyguides&&!this.o.noguides){
                var
                z=0;
                while(z<=ya.length){
                    let l = z/(ya.length-1)*h;
                    svg.app(__svg("path", "--guide --y", { d: ["M", [ 0, l ] , "L", [ w, l ] ].join(" ") }, bind(guidecss, { stroke: "url(#"+gdy+")" })));
                    z++
                }
            }

            /* GUIDES X */
            if(!this.o.noxguides&&!this.o.noguides){
                let
                i = 0;
                while(i<=w){
                    svg.pre(__svg("path", "--guide --x", { d: ["M", [ Math.ceil(i-fsize*2), 0 ], "L", [ Math.ceil(i-fsize*2), h ] ].join(" ") }, bind(guidecss, { stroke: "url(#"+gdx+")" })));
                    i+=w/maxx;
                }
            }

            /* LABELS Y */
            if(!this.o.nolabels&&!this.o.noylabels) ya.each((y, i) => {
                let
                ylapse = !i ? -fsize*.5 : (i==ya.length-1 ? fsize*1.5 : 0)
                ;
                y = _num(y)
                svg.app(__svg("text", "--label --y", { x: Math.ceil(fsize*.5), y: h-i/(ya.length-1)*h+ylapse }, { stroke: fcolor, opacity:.32, fontSize:fsize }).text(y))
            })

            /* LABELS X */
            if(!this.o.nolabels&&!this.o.noxlabels) xa.each((x, i) => {
                svg.app(__svg("text", "--label --x", { x: Math.ceil(i/(xa.length-1)*w - fsize*2), y: h - fsize*1.5 }, { stroke: fcolor, opacity: .32, "text-anchor":"middle" }).text(x+""))
            })
        }
        return this
    }

    preload(o){
        
        this.target_  = o.target || $("#app")[0]
        this.type_    = o.type || "std";
        this.width_   = this.target.getBoundingClientRect().width;
        this.height_  = this.target.getBoundingClientRect().height;
        this.css_     = o.css || {};
        this.svg_     = app.svg("svg", "--graph --type-"+this.type_, this.css).app(app.svg("defs")).attr({ width: this.width_, height: this.height_ })
        
        this.lines(o.lines);

        this.series = o.series || []; /* BASE SERIES CREATION */ {
            if(Array.isArray(o.series)&&o.series.length){ if(!Array.isArray(o.series[0])) o.series = [ o.series ] }
            else o.series = [];
        }

        if(o.series.length){

            this.length_ = 0;
            o.series.each(serie => this.length_ = Math.max(this.length_, serie.length));

            this.max_ = 0;
            o.series.each(serie => this.max_ = Math.max(this.max_, serie.calc(MAX)));

            this.axis({ series: o.series, x: o.xaxis, y: o.yaxis })
            if(!o.noguides) this.guides({ x: o.noxguide ? null : this.x_, y: o.noyguide ? null : this.y_ })


        }

        return this
    }
    constructor(o){
        if(o){
            this
            .preload(o)
            //.dbase()
            //.daxis()
            //.dseries();
            //if(o.draw) this.draw();
        }
    }
}