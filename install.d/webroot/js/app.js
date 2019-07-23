var
app = {
	debug: false
	, fw: faau
	, body: document.getElementsByTagName("body")[0]
	, get: function(e,w){ return faau.get(e,w||document).nodearray }
    , onDeviceReady: function(){ this.receivedEvent('deviceready') }
    , receivedEvent: function(id) {
        // var parentElement = document.getElementById(id);
        // var listeningElement = parentElement.querySelector('.listening');
        // var receivedElement = parentElement.querySelector('.received');
        // listeningElement.setAttribute('style', 'display:none;');
        // receivedElement.setAttribute('style', 'display:block;');
        // console.log('Received Event: ' + id);
    }
	, initialize: function(){
		document.addEventListener('deviceready', this.onDeviceReady.bind(this), false);
		document.addEventListener("backbutton", ()=>{ faau.error(app.last) }, false);
	}
};