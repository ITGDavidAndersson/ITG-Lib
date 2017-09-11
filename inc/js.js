function el(name) {
	return document.getElementById(name);
}
function b_ajax_get(callback, doc, method, data) {
	method = typeof method !== 'undefined' ? method : "GET";
	data = typeof data !== 'undefined' ? data : null;
	if(doc === null) {
		return false;
	} else if(callback === null) {
		return false;
	} else {
		var xhr = new XMLHttpRequest();
		xhr.open(method, doc, true);
		if(data != null) {
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		}
		xhr.onreadystatechange = function () {
		    var DONE = 4;
		    var OK = 200;
		    if(xhr.readyState === DONE) {
				if(xhr.status === OK) {
					callback(xhr.response);
				} else {
					callback("Error: "+xhr.status);
				}
		    }
		};
		xhr.send(data);
	}
}
var tid = 0;
var timer = null;
var timerMode = "";
window.onscroll = function() {
	if(document.getElementById("header").getBoundingClientRect().top < -150) {
		document.getElementById("menu").style.position = "fixed";
		document.getElementById("menu").style.top = "0pt";
	} else {
		document.getElementById("menu").style.position = "absolute";
		document.getElementById("menu").style.top = "";
	}
}
if(typeof(scanUrl) === "undefined") {
	var scanUrl = "";
}
function warningTimer(timeLeft) {
	document.getElementById("warning").style.display = "block";
	tid = timeLeft;
	warningUpdater(true);
	if(timeLeft !== undefined) {
		if(timeLeft >= 180) {
			timer = setInterval(warningUpdater, 1000*60);
			timerMode = "slow";
		} else if(timeLeft >= 120) {
			timer = setInterval(warningUpdater, 1000*60);
			timerMode = "slow";
		} else if(timeLeft => 60) {
			timer = setInterval(warningUpdater, 1000);
			timerMode = "fast";
		} else {
			timer = setInterval(warningUpdater, 1000);
			timerMode = "fast";
		}
	}
}
function warningUpdater(blink) {
	var blink = false;
	if(timerMode == "slow") {
		tid -= 59;
		if(tid <= 120) {
			clearInterval(timer);
			timer = setInterval(warningUpdater, 1000);
			timerMode = "fast";
		}
	}
	if(tid >= 60) {
		tout = Math.floor(tid/60);
		var tt = "minut(er)";
		document.getElementById("warning").classList.add("blink");
	} else {
		tout = tid;
		var tt = "sekund(er)";
		if((tid == 60) || (tid == 50) || (tid == 40) || (tid == 30) || (tid == 20)) {
			blink = true;
		} else if(tid <= 10) {
			blink = "fast";
		}
	}
	if(document.getElementById("warning").classList.contains("blink") == true) {
		document.getElementById("warning").classList.remove("blink");
	}
	if(blink == true) {
		document.getElementById("warning").classList.add("blink");
		var timeout = setTimeout(function() {
			document.getElementById("warning").classList.remove("blink");
		}, 50);
	} else if(blink == "fast") {
		document.getElementById("warning").classList.add("blinkfast");
		var timeout = setTimeout(function() {
			document.getElementById("warning").classList.remove("blinkfast");
		}, 50);
	}
	document.getElementById("warning").children[0].innerHTML = "<b>"+tout+"</b> "+tt+" innan en bok går ut.";
	if(tid == 0) {
		location.reload(true);
		document.getElementById("warning").children[0].innerHTML = "<b>Tiden för boken har gått ut. Lämna tillbaka den genast för att inte bli ersättningsskyldig!</b>";
		document.getElementById("warning").classList.add("blink");
		clearInterval(timer);
	}
	tid -= 1;
}
var timer = null;
function popup(txt) {
	clearTimeout(timer);
	timer = setTimeout(function() {
		document.getElementById("popup").style.top = "-200pt";
	}, 2000+(txt.length*50));
	document.getElementById("popup").innerHTML = txt;
	document.getElementById("popup").style.top = "5pt";
}
function tooltip(caller, txt) {
	var found = false;
	for(var c = 0; c < caller.children.length; c++) {
		if(caller.children[c].classList.contains("tooltip")) {
			found = true;
		}
	}
	if(found == false) {
		var div = document.createElement("DIV");
		var txtn = document.createTextNode(txt);
		div.onclick = function() {
			this.style.opacity = 0;
			caller.style.color = "#f00";
			setTimeout(function() {
				caller.removeChild(div);
			}, 500);
		};
		div.classList.add("tooltip");
		div.appendChild(txtn);
		div.style.opacity = "0";
		caller.style.color = "#a00";
		setTimeout(function() {
			div.style.opacity = 1;
		}, 50);
		caller.appendChild(div);
	}
}
function adminLogin() {
	if(document.getElementById("adminLoginBox").style.display === "block") {
		document.getElementById("adminLoginBox").style.display = "none";
	} else {
		document.getElementById("adminLoginBox").style.display = "block";
	}
}
function bookScan() {
	if(scanUrl != null) {
		var ok = window.open("http://zxing.appspot.com/scan?ret=http%3A%2F%2F"+scanUrl+"%3Fc%3D%7BCODE%7D&SCAN_FORMATS=CODE_128", "_self");
		if(ok === null) {
			return false;
		} else {
			return true;
		}
	} else{
		popup("Finns ingen scanUrl definierad. Kontakta David");
		return false;
	}
}
function scan() {
	if(scanUrl != null) {
		var ok = window.open("http://zxing.appspot.com/scan?ret=http%3A%2F%2F"+scanUrl+"%3Fc%3D%7BCODE%7D&SCAN_FORMATS=EAN_13", "_self");
		if(ok === null) {
			return false;
		} else {
			return true;
		}
	} else{
		popup("Finns ingen scanUrl definierad. Kontakta David");
		return false;
	}
}
function loaded(todo) {
	if(document.readyState == "complete") {
		todo();
	} else {
		setTimeout(function(){
			loaded(todo);
		}, 50);
	}
}
var lang = {
	"list": {
		"sv": "Svenska",
		"da": "Danska",
		"en": "Engelska",
		"af": "Afrikaans",
		"sq": "Albanian",
		"ar-sa": "Arabic (Saudi Arabia)",
		"ar-iq": "Arabic (Iraq)",
		"ar-eg": "Arabic (Egypt)",
		"ar-ly": "Arabic (Libya)",
		"ar-dz": "Arabic (Algeria)",
		"ar-ma": "Arabic (Morocco)",
		"ar-tn": "Arabic (Tunisia)",
		"ar-om": "Arabic (Oman)",
		"ar-ye": "Arabic (Yemen)",
		"ar-sy": "Arabic (Syria)",
		"ar-jo": "Arabic (Jordan)",
		"ar-lb": "Arabic (Lebanon)",
		"ar-kw": "Arabic (Kuwait)",
		"ar-ae": "Arabic (U.A.E.)",
		"ar-bh": "Arabic (Bahrain)",
		"ar-qa": "Arabic (Qatar)",
		"eu": "Basque (Basque)",
		"bg": "Bulgariska",
		"be": "Belarusian",
		"ca": "Catalan",
		"zh-tw": "Chinese (Taiwan)",
		"zh-cn": "Chinese (PRC)",
		"zh-hk": "Chinese (Hong Kong SAR)",
		"zh-sg": "Chinese (Singapore)",
		"hr": "Kroatiska",
		"cs": "Tjeckiska",
		"nl": "Holländska",
		"en-us": "Engelska (US)",
		"en-gb": "Engelska (UK)",
		"en-au": "Engelska (Australien)",
		"et": "Estonian",
		"fo": "Faeroese",
		"fa": "Farsi",
		"fi": "Finnish",
		"fr": "Franska",
		"gd": "Gaelic (Scotland)",
		"ga": "Irländska",
		"de": "Tyska",
		"el": "Greek",
		"he": "Hebrew",
		"hi": "Hindi",
		"hu": "Hungarian",
		"is": "Icelandic",
		"id": "Indonesian",
		"it": "Italienska",
		"ja": "Japanese",
		"ko": "Korean",
		"lv": "Latvian",
		"lt": "Lithuanian",
		"mk": "Macedonian (FYROM)",
		"ms": "Malaysian",
		"mt": "Maltese",
		"no": "Norska",
		"pl": "Polska",
		"pt": "Portugisiska",
		"rm": "Rhaeto-Romanic",
		"ro": "Romänska",
		"ru": "Ryska",
		"sz": "Samiska",
		"sr": "Serbian (Cyrillic)",
		"sr": "Serbian (Latin)",
		"sk": "Slovak",
		"sl": "Slovenian",
		"sb": "Sorbian",
		"es": "Spanska",
		"sx": "Sutu",
		"th": "Thai",
		"ts": "Tsonga",
		"tn": "Tswana",
		"tr": "Turkiska",
		"uk": "Ukrainska",
		"ur": "Urdu",
		"ve": "Venda",
		"vi": "Vietnamese",
		"xh": "Xhosa",
		"ji": "Yiddish",
		"zu": "Zulu"
	},
	"lookup": function(inp) {
		if(typeof(lang.list[inp]) === "undefined") {
			return inp;
		} else {
			return lang.list[inp];
		}
	}
}








