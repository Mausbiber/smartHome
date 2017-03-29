// JavaScript Document

var socketServer = new WebSocket('ws://10.0.0.61:5505');
var offline = false;

socketServer.onerror = function(error) {
	console.log('WebSocket Error : ' + error);
	};

socketServer.onopen = function(event) {
	};

socketServer.onmessage = function(event) {};

socketServer.onclose = function(event) {};

function TimerNew (timer) {
	var send_string = JSON.stringify(["timerswitch_new",,,timer])
	this.send(send_string);
	window.setTimeout(function(){window.location = "index.php"},500);
};
function TimerUpdate (timer) {
	var send_string = JSON.stringify(["timerswitch_update",,,timer])
	this.send(send_string);
	window.setTimeout(function(){window.location = "index.php"},500);
};
function TimerDelete (timer) {
	var send_string = JSON.stringify(["timerswitch_delete",,,timer])
	this.send(send_string);
	window.setTimeout(function(){window.location = "index.php"},500);
};

this.send = function (message, callback) {
	this.waitForConnection(function () {
		socketServer.send(message);
		if (typeof callback !== 'undefined') {
		  callback();
		}
	}, 500);
};

this.waitForConnection = function (callback, interval) {
	if (socketServer.readyState === 1) {
		callback();
	} else {
		var that = this;
		setTimeout(function () {that.waitForConnection(callback, interval);}, interval);
	}
};
