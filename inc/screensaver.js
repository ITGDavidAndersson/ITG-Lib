function Screensaver(obj, theme, sizeMod) {
	var self = this;
	this.canvas = obj;
	this.theme = theme;
	
	var body = document.body, html = document.documentElement;
	var height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
	var width = Math.max( body.scrollWidth, body.offsetWidth, html.clientWidth, html.scrollWidth, html.offsetWidth);
	this.width = width/sizeMod;
	this.height = height/sizeMod;
	this.mod = 1/sizeMod;
	document.getElementById(this.canvas).children[0].width = this.width;
	document.getElementById(this.canvas).children[0].height = this.height;
	this.timer = false;
	this.enabled = false;
	this.c = new draw(document.getElementById(this.canvas).children[0]);
	this.themes = [
		{
			vars: {
				dots: []
			}
		},
		{
			vars: {
				dots: []
			}
		}
	];
	this.themes[0].init = function() {
		self.themes[0].vars.specs = 5;
		self.themes[0].vars.NPCCount = Math.round((self.width*self.height)/10000)*self.themes[0].vars.specs;
		self.themes[0].vars.updateMovementCounter = self.themes[0].vars.NPCCount*0.005;
		self.themes[0].vars.speed = 2*self.mod;
		var tx = Math.random()*self.width;
		var ty = Math.random()*self.height;
		self.themes[0].vars.dots[0] = {
			id: 0,
			sx: tx,
			sy: ty,
			x: tx,
			y: ty,
			prot: true,
			mx: (Math.random()*(self.themes[0].vars.speed*2))-self.themes[0].vars.speed,
			my: (Math.random()*(self.themes[0].vars.speed*2))-self.themes[0].vars.speed,
			type: "i",
			col: "#0c0",
			str: 20*self.mod,
			timer: -1,
			targets: []
		};
		for(var c = 1; c < 2; c++) {
			tx = Math.random()*self.width;
			ty = Math.random()*self.height;
			self.themes[0].vars.dots[c] = {
				id: c,
				sx: tx,
				sy: ty,
				x: tx,
				y: ty,
				prot: true,
				mx: (Math.random()*(self.themes[0].vars.speed*2))-self.themes[0].vars.speed,
				my: (Math.random()*(self.themes[0].vars.speed*2))-self.themes[0].vars.speed,
				type: "c",
				col: "#00f",
				str: 15*self.mod,
				timer: -1,
				targets: []
			};
		}
		for(var c = 2; c < self.themes[0].vars.NPCCount; c++) {
			tx = Math.random()*self.width;
			ty = Math.random()*self.height;
			self.themes[0].vars.dots[c] = {
				id: c,
				sx: tx,
				sy: ty,
				x: tx,
				y: ty,
				prot: false,
				mx: (Math.random()*(self.themes[0].vars.speed*2))-self.themes[0].vars.speed,
				my: (Math.random()*(self.themes[0].vars.speed*2))-self.themes[0].vars.speed,
				type: "n",
				col: "#aaa",
				str: 10*self.mod,
				timer: 0,
				targets: []
			};
		}
	};
	this.themes[1].init = function() {
		self.themes[1].vars.specs = 5;
		self.themes[1].vars.NPCCount = Math.round((self.width*self.height)/10000)*self.themes[1].vars.specs;
		self.themes[1].vars.updateMovementCounter = self.themes[1].vars.NPCCount*0.005;
		for(var c = 0; c < Math.ceil(self.themes[1].vars.NPCCount*0.02); c++) {
			var tx = Math.random()*self.width;
			var ty = Math.random()*self.height;
			self.themes[1].vars.dots[c] = {
				id: c,
				sx: tx,
				sy: ty,
				x: tx,
				y: ty,
				mx: (Math.random()*(self.themes[0].vars.speed*2))-self.themes[0].vars.speed,
				my: (Math.random()*(self.themes[0].vars.speed*2))-self.themes[0].vars.speed,
				type: "c",
				str: 10*self.mod,
				col: "#ff0",
				targets: []
			};
		}
		for(var c = Math.ceil(self.themes[1].vars.NPCCount*0.02); c < self.themes[1].vars.NPCCount; c++) {
			var tx = Math.random()*self.width;
			var ty = Math.random()*self.height;
			self.themes[1].vars.dots[c] = {
				id: c,
				sx: tx,
				sy: ty,
				x: tx,
				y: ty,
				mx: (Math.random()*(self.themes[0].vars.speed*2))-self.themes[0].vars.speed,
				my: (Math.random()*(self.themes[0].vars.speed*2))-self.themes[0].vars.speed,
				type: "n",
				str: 2*self.mod,
				col: "#ff0",
				targets: []
			};
		}
	};
	this.themes[0].moveNPC = function(npc) {
		if(npc.x < npc.str) {
			npc.mx = -npc.mx;
		}
		if(npc.y < npc.str) {
			npc.my = -npc.my;
		}
		if(npc.x > self.width-npc.str) {
			npc.mx = -npc.mx;
		}
		if(npc.y > self.height-npc.str) {
			npc.my = -npc.my;
		}
		var fail = 0;
		while(npc.x < npc.str) {
			npc.x += 0.5;
			if(fail > 100) {
				break;
			} else {
				fail++;
			}
		}
		fail = 0;
		while(npc.y < npc.str) {
			npc.y += 0.5;
			if(fail > 100) {
				break;
			} else {
				fail++;
			}
		}
		fail = 0;
		while(npc.x > self.width-npc.str) {
			npc.x -= 0.5;
			if(fail > 100) {
				break;
			} else {
				fail++;
			}
		}
		fail = 0;
		while(npc.y > self.height-npc.str) {
			npc.y -= 0.5;
			if(fail > 100) {
				break;
			} else {
				fail++;
			}
		}
		npc.x += npc.mx;
		npc.y += npc.my
	};
	this.themes[0].checkDistance = function() {
		var ts = 0;
		for(var c in self.themes[0].vars.dots) {
			var dot = self.themes[0].vars.dots[c];
			if(dot.type !== "n") {
				if(dot.type === "c") {
					if(dot.timer === 0) {
						dot.type = "n";
						dot.col = "#aaa";
						dot.str = 10;
						dot.targets = [];
					} else {
						dot.timer--;
					}
				}else if(dot.type === "i") {
					if(dot.prot === false) {
						dot.str += 0.02;
					}
				}
				if(dot.targets.length > 0) {
					for(var q in dot.targets) {
						/*if(dot.type === "i") {
							if(dot.targets[q].type !== "n") {
								dot.targets.splice(q, 1);
								break;
							}
						} else if(dot.type === "c") {
							if(dot.targets[q].type === "n") {
								dot.targets.splice(q, 1);
								break;
							}
						}*/
						if(m.dist(dot.x, dot.y, dot.targets[q].x, dot.targets[q].y) > 50+dot.str) {
							dot.targets.splice(q, 1);
						} else if(m.dist(dot.x, dot.y, dot.targets[q].x, dot.targets[q].y) < (dot.str+dot.targets[q].str)) {
							if((dot.type === "i") && (dot.targets[q].type === "n")) {
								self.themes[0].vars.dots[dot.targets[q].id].type = "i";
								self.themes[0].vars.dots[dot.targets[q].id].col = dot.col;
								self.themes[0].vars.dots[dot.targets[q].id].str = dot.str*0.5;
							} else if(dot.type === "c") {
								if(self.themes[0].vars.dots[dot.targets[q].id].type == "i") {
									self.themes[0].vars.dots[dot.targets[q].id].type = "c";
									self.themes[0].vars.dots[dot.targets[q].id].col = "#00f";
									self.themes[0].vars.dots[dot.targets[q].id].timer = 150;
								}
							}
						}
					}
				}
				for(var c2 = 0; c2 < Math.ceil(self.themes[0].vars.dots.length*0.4); c2++) {
					var target = self.themes[0].vars.dots[Math.floor(Math.random()*self.themes[0].vars.dots.length)];
					if(target.prot === false) {
						if(dot.type === "i") {
							if(target.type === "n") {
								if(m.dist(dot.x, dot.y, target.x, target.y) < 50+dot.str) {
									dot.targets.push(target);
								}
							}
						} else if(dot.type === "c") {
							if(target.type === "i") {
								if(m.dist(dot.x, dot.y, target.x, target.y) < 50+dot.str) {
									dot.targets.push(target);
								}
							}
						}
					}
				}
			}
			ts += dot.targets.length;
		}
		console.log(ts);
	};
	this.themes[1].checkDistance = function() {
		for(var c in self.themes[1].vars.dots) {
			var dot = self.themes[1].vars.dots[c];
			if(dot.type === "c") {
				if(dot.targets.length > 0) {
					for(var q in dot.targets) {
						if(m.dist(dot.x, dot.y, dot.targets[q].x, dot.targets[q].y) > 100) {
							self.themes[1].vars.dots[c].targets.splice(q, 1);
						}
					}
					for(var q in dot.targets) {
						if(m.dist(dot.x, dot.y, dot.targets[q].x, dot.targets[q].y) < (dot.str+dot.targets[q].str)) {
							if(dot.targets[q] === "c") {
								self.themes[1].vars.dots[dot.targets[q].id].col = "#ff0";
								self.themes[1].vars.dots[dot.targets[q].id].col = "#ff0";
							} else if(dot.type === "n") {
								self.themes[1].vars.dots[dot.targets[q].id].col = "#ff0";
							}
						}
					}
				}
				for(var c2 = 0; c2 < Math.ceil(self.themes[1].vars.dots.length*0.1); c2++) {
					var target = self.themes[1].vars.dots[Math.floor(Math.random()*self.themes[1].vars.dots.length)];
					if(dot.type === "c") {
						if(target.type === "n") {
							if(m.dist(dot.x, dot.y, target.x, target.y) < 50+dot.str) {
								dot.targets.push(target);
							}
						}
					} else if(dot.type === "c") {
						if(m.dist(dot.x, dot.y, target.x, target.y) < 50+dot.str) {
							dot.targets.push(target);
						}
					}
				}
			}
		}
	};
	this.themes[0].draw = function() {
		self.c.clear();
		self.themes[0].checkDistance();
		for(var c = 0; c < self.themes[0].vars.updateMovementCounter; c++) {
			self.themes[0].vars.dots[Math.floor(Math.random()*self.themes[0].vars.dots.length)].mx = (Math.random()*(self.themes[0].vars.speed*2))-self.themes[0].vars.speed;
			self.themes[0].vars.dots[Math.floor(Math.random()*self.themes[0].vars.dots.length)].my = (Math.random()*(self.themes[0].vars.speed*2))-self.themes[0].vars.speed;
		}
		for(var c in self.themes[self.theme].vars.dots) {
			var npc = self.themes[0].vars.dots[c];
			var x = self.themes[self.theme].vars.dots[c].x;
			var y = self.themes[self.theme].vars.dots[c].y;
			self.themes[0].moveNPC(npc);
			for(var c in npc.targets) {
				self.c.drawLine(npc.x, npc.y, npc.targets[c].x, npc.targets[c].y, "#aaa");
			}
		}
		for(var c in self.themes[self.theme].vars.dots) {
			var npc = self.themes[0].vars.dots[c];
			self.c.drawFilledCircle(npc.x, npc.y, npc.str, npc.col, 0, Math.PI*180*2);
		}
	};
	this.themes[1].draw = function() {
		self.c.clear();
		self.themes[1].checkDistance();
		for(var c = 0; c < self.themes[1].vars.updateMovementCounter; c++) {
			self.themes[1].vars.dots[Math.floor(Math.random()*self.themes[1].vars.dots.length)].mx = (Math.random()*(self.themes[0].vars.speed*2))-self.themes[0].vars.speed;
			self.themes[1].vars.dots[Math.floor(Math.random()*self.themes[1].vars.dots.length)].my = (Math.random()*(self.themes[0].vars.speed*2))-self.themes[0].vars.speed;
		}
		for(var c in self.themes[self.theme].vars.dots) {
			var npc = self.themes[1].vars.dots[c];
			var x = self.themes[self.theme].vars.dots[c].x;
			var y = self.themes[self.theme].vars.dots[c].y;
			self.themes[0].moveNPC(npc);
			for(var c2 in npc.targets) {
				if(npc.targets[c2].type === "c") {
					self.c.drawLineWidth(npc.x, npc.y, npc.targets[c2].x, npc.targets[c2].y, 10, "#ff0");
				} else {
					self.c.drawLine(npc.x, npc.y, npc.targets[c2].x, npc.targets[c2].y, "#ff0");
				}
			}
		}
		var targets = 0;
		for(var c in self.themes[self.theme].vars.dots) {
			var npc = self.themes[1].vars.dots[c];
			targets += npc.targets.length;
			self.c.drawFilledCircle(npc.x, npc.y, npc.str, npc.col, 0, Math.PI*180*2);
		}
	};
	this.themes[theme].init();
	this.restart = function() {
		clearTimeout(self.timer);
		setTimeout(self.start(), 1000*10);
	};
	this.start = function() {
		document.getElementById(self.canvas).style.display = "block";
		setTimeout(function() {
			document.getElementById(self.canvas).style.opacity = "1";
		}, 5);
		self.enabled = true;
		self.timer = setInterval(self.draw, 1000/(30*self.themes[0].vars.specs));
	};
	this.stop = function() {
		document.getElementById(self.canvas).style.opacity = "0";
		setTimeout(function() {
			if(!self.enabled) {
				document.getElementById(self.canvas).style.display = "none";
			}
			clearInterval(self.timer);
		}, 2005);
		self.enabled = false;
	};
	this.draw = function() {
		self.themes[theme].draw();
	};
};