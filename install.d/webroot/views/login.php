<div class='-wrapper'>
	<div class='-row -content-center --splash'>
		<br/>
		<section class='--panel' style="width:60%;max-width:800px;"></section>
	</div>
	<form class='-row -content-center -comfortaa --login' action="javascript:void(0)" onsubmit="__login(this.json())">
		<h4>LOGIN</h4><br/>
		<div>
			<div class='-left' style="font-size:.5em">USUÁRIO</div><br/>
			<input type="user" autocomplete="user" class='-left'name="user"/>
		</div>
		<br/>
		<div>
			<div class='-left' style="font-size:.5em">SENHA</div><br/>
			<input type="password" autocomplete="password" class='-left -hash' name="pswd"/>
		</div>
		<br/>
		<input type="submit" class='-bt -skip' style="padding:.5em 2em" value="LOGIN"/><br/>
	</form>
	<style type="text/css">
		.--progresspath {
			animation: progress-splash 4s ease-in-out infinite alternate-reverse;
		}
		@keyframes progress-splash {
			from { stroke-dashoffset:  0; fill-opacity:0;stroke-opacity:1;}
			to { stroke-dashoffset:  attr(data-len); fill-opacity: 1;stroke-opacity:0;}
		}
		.--login input:not(.-bt){
			border: 1.5px solid black;
			border-radius: 3px;
		}
		.--login input {
			margin-bottom:1em;
			margin-top: -.25em;
			padding: .5em;
		}
		.--login .-bt {
			background: black;
			color: white;
			margin-top: 2em;
		}
	</style>
	<script type="text/javascript">
		
		app.declare({
			__login: function(json){
				if(json.user&&json.pswd){
					app.fw.call("user/signin",{hash:btoa(json.stringify())},function(){
						if(this.data=="1") location.reload();
					})
				} else app.fw.error("Please fill user and password")
			}
		});

		app.fw.call("src/img/splash.svg",null,function(){
			let
			svg = this.data.morph().setStyle({
				margin: "10vh auto"
				, width: "20%"
				, maxWidth: "800px"
			});
			svg.get("path").each(function(){
				let
				len = this.getTotalLength();
				this.addClass("--progresspath").setData({len:len*2}).setStyle({"stroke-dasharray":len+","+len, "stroke-dashoffset":len});
			});
			$(".--splash").at().pre(svg);
		});
	</script>
</div>