/*
 * Skrypt wyświetlający okienko z informacją o wykorzystaniu ciasteczek (cookies)
 * 
 * Więcej informacji: http://webhelp.pl/artykuly/okienko-z-informacja-o-ciasteczkach-cookies/
 * 
 */

function WHCreateCookie(name, value, days) {
    var date = new Date();
    date.setTime(date.getTime() + (days*24*60*60*1000));
    var expires = "; expires=" + date.toGMTString();
	document.cookie = name+"="+value+expires+"; path=/";
}
function WHReadCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	}
	return null;
}

window.onload = WHCheckCookies;

function WHCheckCookies() {
    if(WHReadCookie('cookies_accepted') != 'T') {
        var message_container = document.createElement('div');
        message_container.id = 'cookies-message-container';
        var html_code = '<div id="cookies-message" style="color: white; padding: 5px 0px; font-size: 11px; line-height: 14px; text-align: center; position: fixed; top: -40px; left:0; right:0; margin: auto; background-color: rgba(0,0,0,0.6); border: solid 1px white; border-top:0; width: 100%; max-width: 630px; z-index: 100000; box-shadow: 0 0 5px rgba(0,0,0,0.4);">Ta strona używa ciasteczek (cookies), dzięki którym nasz serwis może działać lepiej. <a href="http://wszystkoociasteczkach.pl" target="_blank" style="color: white;">Dowiedz się więcej</a><a href="javascript:WHCloseCookiesWindow();" id="accept-cookies-checkbox" name="accept-cookies" style="transition: all 0.5s; padding: 2px 6px; border-radius: 4px; display: inline-block; margin-left: 10px; text-decoration: none; cursor: pointer;">Rozumiem</a></div><style>#accept-cookies-checkbox{background-color: #00AFBF; color: #FFF; border: solid 1px #00AFBF;}#accept-cookies-checkbox:hover{background-color: transparent; border-color: white}</style>';
        message_container.innerHTML = html_code;
        document.body.appendChild(message_container);
		
		function moveCookie(){
			var cookies_message = document.getElementById('cookies-message')
			cookies_message.style.top = parseInt(cookies_message.style.top) + 1 + 'px';
			if(parseInt(cookies_message.style.top)<0){
				animate = setTimeout(moveCookie,30); // call moveRight in 20msec
			}
		}
		animate = setTimeout(moveCookie,1000);
    }
}

function WHCloseCookiesWindow() {
    WHCreateCookie('cookies_accepted', 'T', 365);
    document.getElementById('cookies-message-container').removeChild(document.getElementById('cookies-message'));
}
