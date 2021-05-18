const
PRISM = {
    ALIZARIN:"#E84C3D"
    , PETER_RIVER:"#2C97DD"
    , ICE_PINK: "#CA179E"
    , EMERLAND:"#53D78B"
    , SUN_FLOWER:"#F2C60F"
    , AMETHYST:"#9C56B8"
    , TURQUOISE:"#00BE9C"
    , PURPLE_PINK:"#8628B8"
    , PASTEL: "#FEC200"
    , CLOUDS:"#ECF0F1"
    , CARROT:"#E67D21"
    , BURRO_QNDO_FOJE: "#8C887B"
};

class FGraph {

    axis(o){
        if(this.node){

            const
            rects = this.rects
            , fsize = app.em2px()
            ;;
            // Y axis
            !o.noyaxis&&this.node.app(
                SVG("path", "--axis --y", { d: [
                    "M"
                    , [ fsize * 2, 0 ]
                    , "L"
                    , [ fsize * 2  , rects.height - fsize * 2 ]
                    , [ 0          , rects.height             ]
                ].join(" ") }, { "stroke-width": o.strokeWidth || 1, fill: "none", stroke: o.strokeColor || app.colors("FONT") })
            );
            // X axis
            !o.noxaxis&&this.node.app(
                SVG("path", "--axis --x", { d: [
                    "M"
                    , [ 0          , rects.height             ]
                    , "L"
                    , [ fsize * 2  , rects.height - fsize * 2 ]
                    , [ rects.width, rects.height - fsize * 2 ]
                ].join(" ") }, { "stroke-width": o.strokeWidth || 1, fill: "none", stroke: o.strokeColor || app.colors("FONT") })
            )            
        }
    }

    guides(o){
        if(this.node){

            const
            rects   = this.rects
            , fsize = app.em2px()
            , ydots = 2 + (o.ydots || 2)
            , xdots = 2 + (o.xdots || 4)
            , ypace = (rects.height - fsize * 2) / ydots
            , xpace = (rects.width  - fsize * 2) / xdots
            , node  = this.node
            ;;
            // Y Guides
            !o.noxguides&&app.iterate(0, xdots, i => {
                node.app(
                    SVG("path", "--guide --x", { d: [
                        "M"
                        , [ i * xpace, 0            ]
                        , "L"
                        , [ i * xpace, rects.height - fsize * 2 ]
                    ].join(" ") }, { stroke: o.strokeColor || app.colors("FONT") + "22", "stroke-width": o.strokeWidth || 1 })
                )
            });
            // X Guides
            !o.noyguides&&app.iterate(0, ydots, i => {
                node.app(
                    SVG("path", "--guide --y", { d: [ 
                        "M"
                        , [ fsize * 2  , i * ypace ]
                        , "L"
                        , [ rects.width, i * ypace ] 
                    ].join(" ") }, { stroke: o.strokeColor || app.colors("FONT") + "22", "stroke-width": o.strokeWidth || 1 })
                )
            });
        }
    }

    draw(o){
        if(this.target&&this.node){
            app.series_ = o.series;
            const
            target     = this.target
            , node     = this.node
            , rects    = this.rects
            , entities = o.series.extract(serie => serie.extract(s => s.key)[0])
            , series   = o.series.extract(serie => serie.extract(s => s.content.array())[0])
            , labels   = o.series.extract(serie => serie.extract(s => s.content.keys())[0])[0]
            , xmax     = series.extract(s => s.length).calc(MAX)
            , ymax     = Math.max(1, series.extract(s => s.calc(MAX)).calc(MAX)*1.1)
            , fsize    = app.em2px()             
            , xpace    = (rects.width - fsize*2) / xmax
            , colors   = this.colors
            , labelbar = DIV("-absolute -zero-bottom-left", { paddingLeft: (fsize*2)+"px" })
            , entitybar= DIV("-absolute -zero-top-right")
            ;;

            var type;;
            switch(this.type.toLowerCase()){
                case "line"     : type = "L"    ; break;
                case "default"  : type = "L"    ; break;
                case "smooth"   : type = "S"    ; break;
                case "curve"    : type = "C"    ; break;
            }

            entities.each((name, idx) => {

                const
                serie   = series[idx]
                , color = colors[idx] ? colors[idx] : PRISM.array().rand()
                , h = rects.height - fsize * 4
                , d = [ "M" ]
                ;;

                !o.noentities&&entitybar.app(
                    DIV("-row -content-right", { padding:".25em" }).app(
                        DIV("-right -circle", { height:"1em", width: "1em", marginLeft: "1em", background:color })
                    ).app(
                        SPAN(name, "-right")
                    )
                )
                
                serie.each((n, i) => {

                    n = n ? n : 0.0001;

                    const
                    x = parseInt(i * xpace + fsize * 2)
                    , y =  parseInt(h - (h * n / ymax) + fsize * 2)
                    ;

                    !o.nolabels                                                      &&
                    (!i || i==serie.length-1 || serie==Math.ceil(serie.length/2))    &&
                    labelbar.app(
                        SPAN(labels[idx] || idx, "-absolute -zero-bottom-left", { left: x + "px" })
                    )

                    if(!$("#"+ node.uid() +" .-hint-plate.--iter"+i)[i]) node.app(
                        SVG("rect", "-hint-plate -pointer --tooltip --iter"+i, { 
                            width: type == "bars" ? fsize : xpace
                            , height: type == "bars" ? h -y  : h 
                            , x: x
                            , y: (type == "bars" ? h - y : 0) + fsize * 2
                            , "data-tip": name +": " + (n*1.0).nerdify() + "<br/>"
                        }, { 
                            fill: type == "bars" ? color : app.colors("FONT")+44
                            , opacity:type == "bars" ? 1 : 0 
                        }).on("mouseenter", function(){ 
                            $("#"+node.uid()+" .-hint-plate").not(this).stop().anime({ opacity: type == "bars" ? .32 : 0 });
                            this.css({ opacity: type == "bars" ? 1 : .16 })
                        }).on("mouseleave", function(){ 
                            $("#"+node.uid()+" .-hint-plate").stop().anime({ opacity: type == "bars" ? .64 : 0 }) 
                        })
                    ); else $("#"+ node.uid() +" .-hint-plate.--iter"+i)[i].dataset.tip = $("#"+ node.uid() +" .-hint-plate.--iter"+i)[i].dataset.tip + name +": " + (n*1.0).nerdify() + "<br/>";

                    if(type=="C") d.push([ parseInt(x - xpace / 2), y ]);
                    d.push([ x, y ]);
                    if(!i) d.push(type)
                    if(type=="C") d.push([ parseInt(x + xpace / 2), y ]);
                });

                if(type == "S" && serie.length % 2 == 0) d.push([ rects.width, (h * serie.last() / ymax) + fsize * 2 ]);
                if(type != "bars") node.app(SPATH(d.join(" "), "--line -avoid-pointer", { fill:"none", stroke: color, "stroke-width": 2 }))

                target.app(entitybar)//.app(labelbar)
            });
            app.tooltips();
        }
    }

    constructor(o){
        this.target = (o.target || (this.target || $("#app")[0])).css({ overflow: 'hidden' }).empty();
        this.rects  = this.target.getBoundingClientRect();
        this.type   = o.type || "line";
        this.colors = o.colors || PRISM;
        
        if(!this.node){
            const
            cls    = "-absolute -zero"
            , attr = binds({ height: this.rects.height, width: this.rects.width, "viewBox": "0 0 " + this.rects.width + " " + this.rects.height }, o.attr || {})
            , css  =  binds({ background:"transparent" }, o.css|| {})
            ;;
            this.node = SVG("svg", cls, attr, css)
        }
        this.target.app(this.node);

        const pallete = app.colors(), base = { color: pallete.FONT+"AA", strokeColor: pallete.FONT+"22"};
        if(!o.noaxis) this.axis(binds(base, o.axis||{}));
        if(!o.noguides) this.guides(binds(base, o.guides || {}));
        if(o.series&&o.series.length) this.draw(o);
    }
}