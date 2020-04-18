let
tile = app.components.tile.mimic().css({fontSize:".75em", background:"@LIGHT4", color:"@CONCRETE", width:"20vw"})

tile.get(".--title")[0].text("LOGIN");
tile.get(".--icon")[0].attr({src:"src/img/icons/fingerprint.svg"})
tile.get(".--content")[0].addClass("-keep").app(
    _("div", "-row -keep", { color: "@WET_ASPHALT", padding:"2em" }).app(
        _("input", "-row -input -keep -content-center", { marginTop: ".5em" }).attr({
            type: "user"
            , name: "user"
            , placeholder: "USUÁRIO"
        })
    ).app(
        _("input", "-row -input -hash -keep -content-center", { marginTop: ".5em" }).attr({
            type: "password"
            , name: "passwd"
            , placeholder: "SENHA"
        })
    )
)
tile.get(".--footer")[0].addClass("-keep").app(
    _("input", "-row -bt -skip -keep", { margin: ".5em 0", background: "@WET_ASPHALT", color: "@CLOUDS" }).attr({
        type: "submit"
        , value: "ENTRAR"
    })
)

$(".--screen.--login")[0].css({ background: "@CLOUDS" }).app(
    _("form", "-content-center -row", { padding:"2em" }).attr({ action: "javascript:void(0)" }).app(tile).pre(
        _("img", "-col-1", { padding:"1em" }).attr({ src: "src/img/logos/logo.png"})
    ).on("submit",function(){
        let
        f = this.json();
        if(f.user && f.passwd){
            app.working("enviando dados...")
            app.call("https://auth.faau.me/check", f, "POST").then(key => {
                if(key.data&&key.data.length > 2){
                    app.storage("hash", key.data);
                    app.success("Login efetuado com sucesso!");
                    setTimeout(() => { location.reload() }, ANIMATION_LENGTH);
                }else app.error("tente novamente...")
            })
        }else{
            if(!f.user) app.advise("Faltando usuário...");
            if(!f.passwd) app.advise("Faltando senha...");
        }
    })
)

$(".--screen.--login input")[0].focus()

bootstrap.ready("login")
