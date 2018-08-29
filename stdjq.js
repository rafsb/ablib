// masks an field with a brazilian date format


$.fn.myId = function() {
    if (!this[0]) return;
    //console.log(this);
    return this[0].myId();
};



$.fn.fill = function() {
    this[0].fill();
}
$.fn.refill = function() { this[0].refill(); }

$.fn.appear = function(t = 220) { this[0].appear(t); };

$.fn.desappear = function(t = 220, r = false) {
    //console.log(this,this[0],$(this));
    if(this) $(this)[0].desappear(t, r);
};

$.fn.raise = function() {
    ab_reorder($(this).myId());
};

$.fn.checkout = function(fn = null) { return this[0].checkout(); };

$.fn.isModal = function(){
    return this[0].isModal();
}

$.fn.minimize = function() {
    var x = $(this);
    if (!x.find(".ab-minimize").length || !(x.hasClass("ab-wrapper") || x.hasClass("ab-window") || x.hasClass("ab-panel") || x.hasClass("ab-dialog"))) return -1;
    if (!x.attr("__MINIMIZED__").int()) {
        x.attr("__MINIMIZED__", 1);
        if (!abox.VIEWPORT) {
            $(this).hide();
            return;
        }
        x.animate({
            width: ab_w(10),
            height: "5vh",
            top: "95vh",
            left: ab_w(10) * abox.Tray.length,
            "margin-top": 0,
            "margin-left": 0
        }, 160);
        //x.prepend("<div class='abs zero stretch bgray fdark'>"+x.find("title").text().trim()+"</div>");
        //var tmp = x.find(".ab-minimize").parent();
        //tmp.find(".ab-maximize, .ab-close, .bt, button, [type=button], [type=submit], [type=reset]").css("display","none");
        x.children(":not(.ab-restore)").not(".ab-title").css("display", "none");
        //x.find(".ab-restore").show();
        x.draggable();
        x.draggable("disable");
        abox.Tray.push(x.myId());
        ab_reorder(x.myId());
    } else x.restore();
};

$.fn.maximize = function() {
    var x = $(this);
    //console.log(x);
    if (!(x.hasClass("ab-wrapper") || x.hasClass("ab-window") || x.hasClass("ab-panel"))) return -1;
    if (!x.attr("__MAXIMIZED__").int()) {
        x.attr("__MAXIMIZED__", 1);
        if (!abox.VIEWPORT) {
            $(this).hide();
            return;
        }
        x.animate({
            width: ab_w() - (BODY.height() > ab_h() ? 15 : 0),
            height: ab_h(100),
            top: 0,
            left: 0
        }, 160);
        x.draggable();
        x.draggable("disable");
        ab_reorder(x.myId());
    } else x.restore();
};

$.fn.restore = function() {
    var x = $(this);
    if (!(x.hasClass("ab-wrapper") || x.hasClass("ab-window") || x.hasClass("ab-panel") || x.hasClass("ab-dialog"))) return -1;
    x.draggable();
    x.draggable("enable");
    x.attr("__MINIMIZED__", 0);
    x.attr("__MAXIMIZED__", 0);
    x.children(":not(script)").not("style").css("display", "inline-block");
    x.stop().animate({
        width: x.attr("__W__"),
        height: x.attr("__H__"),
        top: x.attr("__T__"),
        left: x.attr("__L__")
    }, 160);
    var tmp = [];
    for (var i = 0; i++ < abox.Tray.length;) {
        if (abox.Tray[i - 1] != x.myId()) tmp.push(abox.Tray[i - 1]);
    }
    abox.Tray = tmp;
    ab_reorder(x.myId());
};

$.fn.lockTab = function(l = true) {
    $(this).off("keydown");
    if (!l) return;

    $(this).keydown(function(e) {
        var k = (e.keyCode ? e.keyCode : e.which);
        if (k == 0 || k == 9) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
};


$.fn.parentModal = function() {
    return $(this[0].parentModal());
};

    /*
     * @global listener
     *
     * Tracks mouse cardinal position (x and y) on viewport, in pixels and assign
     * tooltip tag X and Y cordinates
     *
     */
    $(document).mousemove(function(e) {
        $("tooltip").offset({
            left: +e.pageX + 24,
            top: +e.pageY
        });
    });
