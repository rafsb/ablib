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
        , color: [ clr.WHITE, clr.PASTEL ]
        , flags: [ "hoje", tmp.angle_factor + "x" ]
    } 
    , lines: {
        type: "wave"
        , fill:      [ clr.WHITE, clr.PASTEL ]
        , bullets:   [ true, false ]
        , dasharray: [ false, [ 4,2,1,2 ] ]
        , axis:      [ 2, clr.LIGHT4 ]
        , guide:     [ 1, clr.LIGHT2 ]
    }
})
*/
class Graph {

    __makex__(){
        let
        s = this.o.series
        , lines = this.o.lines
        , final = this.o.xlabels || []
        ;
        
        if(!final.length && s.length){
            let
            max = 0
            , min = 0
            ;
            s.each(serie => max = Math.max(max, serie.length))
            final = [ min, max/2, max ];
        }
        if(!final.length) final = [ 0, .5, 1 ];
        this.xaxis = final;

        return final
    }

    __makey__(){
        var
        final = [0]
        , lines = this.o.lines
        , max = 0
        , min = lines.nozero ? Number.MAX_VALUE : 0
        , s = this.o.series
        ;

        if(s.length){
            s.each(serie => {
                serie.each(fragment => {
                    max = Math.max(max, fragment);
                    min = Math.min(min, fragment);
                })
            })

            if(this.o.log){
                let 
                i=1
                while(i<max){
                    i*=10;
                    final.push(i)
                }
            } else {
                max = Math.ceil(max*1.1/10)*10;
                min = this.o.nonegatives ? Math.max(0, Math.floor(min*.9/10)*10) : Math.floor(min*.9/10)*10;
                final = [ min, (max+min)/2, max ];
            }
        } else final = [0,.5,1]

        if(lines.nozero&&final.length>2) while(final[1]<min) final = final.slice(1)

        if(final.last()==final.first()) final = [0,.5,1]
        
        this.yaxis = final;

        return this.yaxis
    }

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

            if(n*1===0) return h;
            if(!n) return null;

            var
            x = h
            , yiter = 0
            , pace = h/(yaxis.last()-yaxis.first())
            ;

            if(log){
                if(n){
                    let
                    logmax = 1;
                    while(logmax<max) logmax*=10;
                    n = h*Math.log(n,10)/Math.log(logmax,10)
                }
            }
            else x = h-(n-yaxis.first())*pace;

            return Math.min(h, Math.max(0,x))+fsize
        }
        , stop = series.extract(function(){ return this.length }).calc(MAX)
        ;

        lines = bind({series: [], stroke: "white 1 1 1" }, lines);        

        series.each((serie, iter) =>{
            var
            clsname = (this.o.names&&this.o.names[iter]?this.o.names[iter]:"noname").replace(/[^0-9a-zA-Z]/g,"")
            , path = [ "M", [ _xpos(0), _ypos(serie[0]) ], "L" ]
            , bullets = lines.series[iter]&&lines.series[iter].bullets ? lines.series[iter].bullets.split(/\s+/g) : (lines.bullets ? lines.bullets.split(/\s+/g) : [ "transparent", 2, "transparent", 1 ])
            , head = lines.series[iter]&&lines.series[iter].head ? lines.series[iter].head.split(/\s+/g) : [ "transparent", 4, "transparent", 2 ]
            , tail = lines.series[iter]&&lines.series[iter].tail ? lines.series[iter].tail.split(/\s+/g) : [ "transparent", 4, "transparent", 2 ]
            , linepath = [ 
                lines.series[iter]&&lines.series[iter].stroke ? lines.series[iter].stroke.split(/\s+/g)[0] : (lines.stroke.split(/\s+/g)[0] || "white")
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
                    , s_bullets  = lines.series[iter+1]&&lines.series[iter+1].bullets ? lines.series[iter+1].bullets.split(/\s+/g) : (lines.bullets ? lines.bullets.split(/\s+/g) : [ "white", 2, "white", 1 ])
                    , s_head     = lines.series[iter+1]&&lines.series[iter+1].head ? lines.series[iter+1].head.split(/\s+/g) : [ "transparent", 4, "transparent", 2 ]
                    , s_tail     = lines.series[iter+1]&&lines.series[iter+1].tail ? lines.series[iter+1].tail.split(/\s+/g) : [ "transparent", 4, "transparent", 2 ]
                    , s_linepath = [ 
                        lines.series[iter+1]&&lines.series[iter+1].stroke ? lines.series[iter+1].stroke.split(/\s+/g)[0] : (lines.stroke.split(/\s+/g)[0] || "white")
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
                        , fill = i<split ? (first&&tail[0] ? tail[0] : (last&&head[0] ? head[0] : (bullets[0] || "white"))) : (first&&s_tail[0] ? s_tail[0] : (last&&s_head[0] ? s_head[0] : (s_bullets[0] || "white")))
                        , stroke = i<split ? (first&&tail[2] ? tail[2] : (last&&head[2] ? head[2] : (bullets[2] || "white"))) : (first&&s_tail[2] ? s_tail[2] : (last&&s_head[2] ? s_head[2] : (s_bullets[2] || "white")))
                        , stroke_width = i<split ? (first&&tail[3] ? tail[3] : (last&&head[3] ? head[3] : (bullets[3] || 1))) : (first&&s_tail[3] ? s_tail[3] : (last&&s_head[3] ? s_head[3] : (s_bullets[3] || 1)))
                        ;

                        if(i<split) path.push([ left, top ]);
                        else path2.push([ left, top ]);

                        /* TOOLTIPS */
                        svg.app(
                            _S("circle", "--bullet -pointer --tooltip" + (first?" --tail":"") + (last?" --head":""), { 
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
                        if(this.o.please_that && last && i<split){
                            svg.app(
                                _S("path", null, { d: [ "M", [ left, 0 ], "L", [ left, h ] ].join(" ") }, { stroke:"white", "stroke-dasharray":"4,4", opacity:.64, "stroke-width":1 })
                            ).app(
                                _S("text", null, { x:left-24, y:top-36 }, { "text-anchor":"middle", stroke:"white", opacity:.64, "transform-origin":(left-24)+"px "+(top-36)+"px", transform:"rotate(-90deg)"}).text("today")
                            )
                        }

                    }

                    path2[1] = path2[3] = path.last();


                    /* SEQUENCE 2 */
                    svg.app(_S("path", "--default-sequence --serie --s"+iter, { d: path2.join(' ') }, bind({ 
                        stroke: s_linepath[0], "stroke-width": s_linepath[1], fill:"none", opacity: s_linepath[2]}, s_linepath[3] ? { "stroke-dasharray": s_linepath[3] } : {})));

                    /* SEQUENCE 1 */
                    svg.app(_S("path", "--default-sequence --serie --s"+iter, { d: path.join(' ') }, bind({ 
                        stroke: linepath[0], "stroke-width": linepath[1], fill:"none", opacity: linepath[2]}, linepath[3] ? { "stroke-dasharray": linepath[3] } : {})));
                    
                    if(lines.type=="wave"){
                        let
                        gd1 = "fagd"+app.nuid()
                        , gd2 = "fagd"+app.nuid()
                        ;
                        
                        /* WAVE 2 */
                        svg.get("defs").at().app(
                            _S("linearGradient", "--graph-gradient --gd1", { id:gd2, x1:"50%", y1:"0%", x2:"50%", y2:"100%", gradientUnits:"userSpaceOnUse" })
                            .app(_S("stop", "--gradient-stop --stop1", { offset:"0%" }, { "stop-color":s_linepath[0], "stop-opacity":.24 }))
                            .app(_S("stop", "--gradient-stop --stop2", { offset:"95%" }, { "stop-color":s_linepath[0], "stop-opacity":0 }))
                        )
                        wv2 = path2.concat([ [ w+fsize, h ], [ _xpos(split-1), h ], "z" ]);
                        svg.pre(_S("path", "--split-wave0 --serie", { d: wv2.join(' '), fill:"url(#"+gd2+")" }, { stroke: "none" }))

                        /* WAVE 1 */
                        svg.get("defs").at().app(
                            _S("linearGradient", "--graph-gradient --gd1", { id:gd1, x1:"50%", y1:"0%", x2:"50%", y2:"100%", gradientUnits:"userSpaceOnUse" })
                            .app(_S("stop", "--gradient-stop --stop1", { offset:"0%" }, { "stop-color":linepath[0], "stop-opacity":.24 }))
                            .app(_S("stop", "--gradient-stop --stop2", { offset:"95%" }, { "stop-color":linepath[0], "stop-opacity":0 }))
                        )
                        wv = path.concat([ [ _xpos(split-1), h ], [ _xpos(0), h ], "z" ]);
                        svg.pre(_S("path", "--split-wave0 --serie", { d: wv.join(' '), fill:"url(#"+gd1+")" }, { stroke: "none" }))
                    }
                } break;

                case "bars":{
                    var
                    clr = lines.series&&lines.series[iter]&&lines.series[iter].color ? lines.series[iter].color: (lines.color ? lines.color : "white")
                    , rects = [ ]
                    ;

                    //w = w-fsize*2;

                    /* SEQUENCE */
                    for(var i=0; i<stop; i++){
                        var
                        left = Math.min(w,w*i/(stop+1)+fsize*1.5)
                        , top = _ypos(serie[i]*1)+(serie[i]*1 ? 0 : fsize)
                        ;                        
                        svg.app(_S("rect", "--bar -pointer --tooltip", { 
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
                    , clr = lines.colors&&lines.colors[iter]? lines.colors[iter] : "white"
                    , wv
                    , first = true
                    , last = false
                    ;

                    for(var i=0; i<stop;i++){
                        
                        if(i==stop-1) last = true;

                        var
                        left = Math.max(fsize, Math.ceil(w*i/(stop-1)-(i?0:fsize)))
                        , top = _ypos(serie[i])
                        ;

                        if(top!==null){
                            path.push([ left, top ]);

                            /* TOOLTIPS */
                            svg.app(
                                _S("circle", "--bullet -pointer --tooltip --"+clsname, {
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
                    .app(_S("path", "--default-sequence --serie --s"+iter+" --"+clsname, { d: path.join(' ') }, bind({ 
                        stroke: clr
                        , "stroke-width": lines.width
                        , fill:"none" 
                        , opacity: this.o.lines.nopath&&this.o.lines.nopath[iter] ? .05 : 1
                    }, lines.dasharray&&lines.dasharray[iter] ? { "stroke-dasharray": lines.dasharray[iter] } : {})));
                    
                    if(lines.type=="wave"){
                        let
                        gd = "fagd"+app.nuid();

                        svg.get("defs").at().app(
                            _S("linearGradient", "--graph-gradient --gd1", { id:gd, x1:"50%", y1:"0%", x2:"50%", y2:"100%", gradientUnits:"userSpaceOnUse" })
                            .app(_S("stop", "--gradient-stop --stop1", { offset:"0%" }, { "stop-color":clr, "stop-opacity":.64 }))
                            .app(_S("stop", "--gradient-stop --stop2", { offset:"100%" }, { "stop-color":clr, "stop-opacity":.32 }))
                        )

                        wv = path.concat([ [ w, h+fsize ], [ fsize, h+fsize ], "z" ]);

                        /* WAVE */
                        svg.pre(_S("path", "--split-wave1 --serie", { d: wv.join(' '), fill:"url(#"+gd+")" }, { stroke: "none" }))
                    }
                } break;
            }
        })
        
        return this
    }
    daxis(){
        if(this.o){
            let
            gdx = "fagd"+app.nuid()
            , gdy = "fagd"+app.nuid()
            , gdxy = "fagd"+app.nuid()
            , lines = this.o.lines
            , clr = lines.color || "white"
            ;

            this.svg.get("defs").at().app(
                _S("linearGradient", "--graph-gradient --gd1", { id:gdx, x1:"0%", y1:"0%", x2:"0%", y2:"100%", gradientUnits:"userSpaceOnUse" })
                .app(_S("stop", "--gradient-stop --stop1", { offset:"10%" }, { "stop-color":clr, "stop-opacity":.08 }))
                .app(_S("stop", "--gradient-stop --stop2", { offset:"50%" }, { "stop-color":clr, "stop-opacity":.32 }))
                .app(_S("stop", "--gradient-stop --stop2", { offset:"90%" }, { "stop-color":clr, "stop-opacity":.08 }))
            ).app(
                _S("linearGradient", "--graph-gradient --gd1", { id:gdy, x1:"0%", y1:"0%", x2:"100%", y2:"0%", gradientUnits:"userSpaceOnUse" })
                .app(_S("stop", "--gradient-stop --stop1", { offset:"10%" }, { "stop-color":clr, "stop-opacity":.08 }))
                .app(_S("stop", "--gradient-stop --stop2", { offset:"50%" }, { "stop-color":clr, "stop-opacity":.32 }))
                .app(_S("stop", "--gradient-stop --stop2", { offset:"90%" }, { "stop-color":clr, "stop-opacity":.08 }))
            ).app(
                _S("linearGradient", "--graph-gradient --gd1", { id:gdxy, x1:"0%", y1:"100%", x2:"100%", y2:"50%", gradientUnits:"userSpaceOnUse" })
                .app(_S("stop", "--gradient-stop --stop1", { offset:"10%" }, { "stop-color":clr, "stop-opacity":.08 }))
                .app(_S("stop", "--gradient-stop --stop2", { offset:"50%" }, { "stop-color":clr, "stop-opacity":.32 }))
                .app(_S("stop", "--gradient-stop --stop2", { offset:"90%" }, { "stop-color":clr, "stop-opacity":.08 }))
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
            , fcolor = this.o.fcolor || "white"
            , maxx = 0
            ;

            this.yaxis = ya;
            this.xaxis = xa;

            this.o.series.each(serie => maxx = Math.max(maxx, serie.length));
            --maxx;

            /* AXIS */
            if(!this.o.noaxis) svg.app(_S("path", "--axis --y --x", { d: ["M", [ 0, 0 ], "L", [ 0, h ], [ w,h ] ].join(" ") }, axiscss))

            /* GUIDES Y */
            if(!this.o.noyguides&&!this.o.noguides){
                var
                z=0;
                while(z<=ya.length){
                    let l = z/(ya.length-1)*h;
                    svg.app(_S("path", "--guide --y", { d: ["M", [ 0, l ] , "L", [ w, l ] ].join(" ") }, bind(guidecss, { stroke: "url(#"+gdy+")" })));
                    z++
                }
            }

            /* GUIDES X */
            if(!this.o.noxguides&&!this.o.noguides){
                let
                i = 0;
                while(i<=w){
                    svg.pre(_S("path", "--guide --x", { d: ["M", [ Math.ceil(i-fsize*2), 0 ], "L", [ Math.ceil(i-fsize*2), h ] ].join(" ") }, bind(guidecss, { stroke: "url(#"+gdx+")" })));
                    i+=w/maxx;
                }
            }

            /* LABELS Y */
            if(!this.o.nolabels&&!this.o.noylabels) ya.each((y, i) => {
                let
                ylapse = !i ? -fsize*.5 : (i==ya.length-1 ? fsize*1.5 : 0)
                ;
                y = _num(y)
                svg.app(_S("text", "--label --y", { x: Math.ceil(fsize*.5), y: h-i/(ya.length-1)*h+ylapse }, { stroke: fcolor, opacity:.32, fontSize:fsize }).text(y))
            })

            /* LABELS X */
            if(!this.o.nolabels&&!this.o.noxlabels) xa.each((x, i) => {
                svg.app(_S("text", "--label --x", { x: Math.ceil(i/(xa.length-1)*w - fsize*2), y: h - fsize*1.5 }, { stroke: fcolor, opacity: .32, "text-anchor":"middle" }).text(x+""))
            })
        }
        return this
    }
    dbase(){
        if(this.o) this.svg = app.svg("svg", "--fa-graph --fa-type-"+this.o.type, this.o.css).app(app.svg("defs")).attr({ width: this.o.width, height: this.o.height })
        return this
    }
    preload(o){
        o.width = o.target ? o.target.getBoundingClientRect().width : window.innerWidth;
        o.height = o.target ? o.target.getBoundingClientRect().height : window.innerHeight;
        o.series = o.series || [];
        o.graph = o.graph || {};
        o.lines = o.lines || {};
        o.length = 0;
        if(!o.type) o.type = "regular";

        o.series.each(serie => o.length = Math.max(o.length, serie.length))

        this.o = o;
        return this
    }
    constructor(o){
        if(o){
            this
            .preload(o)
            .dbase()
            .daxis()
            .dseries()
        }
    }
}