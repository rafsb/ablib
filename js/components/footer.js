let
    footer = {
        ready: null
        , count: 5
        , node: $("body>footer").at().css({ opacity: 0 }, me => me.anime({ opacity: 1, background: "@FOREGROUND" }))
    }
    , icon_tile = _("nav", "-only-pointer -col-3 -tile --footer-icon-tile");

footer.spy("ready", function () { if (!--this.count) bootstrap.ready("footer_icons") })

async function loadicon(name, pragma) {
    return app.call("src/img/icons/" + name + ".svg").then(svg => {
        svg = svg.data.morph().css({ padding: "1em", height: "4em", filter:"invert(1)" })[0];
        svg.get("path").css({ fill: "@WET_ASPHALT" });
        footer.node.app(icon_tile.cloneNode(true).data({ pragma: pragma }).app(svg));
        footer.ready = true;
    })
}

loadicon("rss", 0).then(_ => {
    loadicon("network", 1).then(_ => {
        loadicon("search", 2).then(_ => {
            loadicon("bell", 4).then(_ => {
                loadicon("menu", 5).then(_ => {
                    $(".--footer-icon-tile").on("click", function () { app.pragma = this.dataset.pragma }) && bootstrap.ready("footer")
                })
            })
        })
    })
})