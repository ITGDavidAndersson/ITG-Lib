function draw(el) {
	this.canvas = el.getContext("2d");
	this.el = el;
	var self = this;
	this.r2d = function(rad) {
		return rad*180/Math.PI;
	};
	this.d2r = function(deg) {
		return deg*Math.PI/180;
	};
	this.r2coord = function(d, r) {
		return {
			x: Math.sin(d2r(-r))*d,
			y: Math.cos(d2r(-r))*d
		};
	};
	this.drawFRect = function(x, y, w, h, color) {
		self.canvas.beginPath();
		self.canvas.fillStyle = color;
		self.canvas.fillRect(x, y, w, h);
	};
	this.drawLine = function(x1, y1, x2, y2, color) {
		self.canvas.beginPath();
		self.canvas.strokeStyle = color;
		self.canvas.moveTo(x1, y1);
		self.canvas.lineTo(x2, y2);
		self.canvas.stroke();
	};
	this.drawLineWidth = function(x1, y1, x2, y2, width, color) {
		self.canvas.beginPath();
		self.canvas.strokeStyle = color;
		self.canvas.strokeWidth = width;
		self.canvas.moveTo(x1, y1);
		self.canvas.lineTo(x2, y2);
		self.canvas.stroke();
	};
	this.drawRect = function(x, y, w, h, color) {
		self.canvas.beginPath();
		self.canvas.strokeStyle = color;
		self.canvas.moveTo(x, y);
		self.canvas.lineTo(x, y);
		self.canvas.lineTo(x+w, y);
		self.canvas.lineTo(x+w, y+h);
		self.canvas.lineTo(x, y+h);
		self.canvas.lineTo(x, y);
		self.canvas.stroke();
	};
	this.drawCircle = function(x, y, radie, borderSize, color) {
		self.canvas.beginPath();
		self.canvas.lineStyle = color;
		self.canvas.arc(x, y, radie, 0, 2*Math.PI);
		self.canvas.stroke();
	};
	this.drawFilledCircle = function(x, y, radie, color, start, stop) {
		self.canvas.beginPath();
		self.canvas.arc(x, y, radie, start, stop);
		self.canvas.fillStyle = color;
		self.canvas.fill();
		self.canvas.closePath();
	};
	this.text = function(x, y, text) {
		self.canvas.font = "20px Verdana";
		self.canvas.textAlign = "left";
		self.canvas.textBaseline = "top";
		self.canvas.fillStyle = "#000";
		self.canvas.fillText(text, x, y);
	};
	this.smallText = function(x, y, text) {
		self.canvas.font = "10px Verdana";
		self.canvas.textAlign = "right";
		self.canvas.textBaseline = "middle";
		self.canvas.fillStyle = "#000";
		self.canvas.fillText(text, x, y);
	};
	this.drawImage = function(x, y, width, height, src) {
		self.canvas.drawImage(src, x, y, width, height);
	};
	this.clear = function() {
		self.canvas.clearRect(0, 0, self.el.width, self.el.height);
	};
}
var m = {
	dist: function(x1, y1, x2, y2) {
		return Math.sqrt(Math.pow(x1-x2, 2)+Math.pow(y1-y2, 2));
	},
	aim: function(x1, y1, x2, y2) {
		return Math.atan2(x1-x2, y1-y2)/Math.PI*180;
	}
};