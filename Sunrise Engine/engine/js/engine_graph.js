EngineGraph = {
	Canvas: null,
	Context: null,
	Width: 0,
	Height: 0,
	Nodes: {},
	Zoom: 1,
	OffsetX: 0,
	OffsetY: 0,
	mousePosition: {},
	FPSCount: 0,
	FPSCountView: 0,
	LastTime: 0,

	clickToggle: false,
	clickPoint: {},

	targetZoom: 1,
	targetPosition: {},

	Init: function(obj, struct){
		var e = EngineGraph;

		e.ApplyCanvas(obj);
		e.CreateStructure(struct);
		e.Draw(0);

		e.mousePosition = e.Point(0, 0);
		e.clickPoint = e.Point(0, 0);
		e.targetPosition = e.Point(0, 0);

		Find(obj).addEventListener("mousemove", function (event) {
            e.mousePosition = e.getMousePosition(event);
        }, false);

		setInterval(function(){
			e.FPSCountView = e.FPSCount * 5;
			e.FPSCount = 0;
		}, 200);

		Find(obj).addEventListener("click", function (event) {

			e.clickToggle = !e.clickToggle;
			e.targetPosition = e.mousePosition;

			if(e.targetZoom == 1) e.targetZoom = 4;
			else e.targetZoom = 1;

			event.preventDefault();
        }, false);
	},
	ApplyCanvas: function(obj){
		var C = Find(obj);
		EngineGraph.Canvas = C;
		EngineGraph.Context = C.getContext("2d");
		EngineGraph.Width = C.width;
		EngineGraph.Height = C.height;
		C.width = C.width;
	},
	CreateStructure: function(struct){
		EngineGraph.Nodes = {};

		for(var i = 0; i < struct.length; i++)
		{
			var signature = struct[i]['signature'];
			var file_path = struct[i]['file_path'];
			
			var signatureArray = file_path.split("/");
			
			var currentNode = EngineGraph.Nodes;
			for(var n = 0; n < signatureArray.length; n++)
			{
				if(signatureArray[n] != "")
				{
					var name = signatureArray[n];
					
					if(currentNode[name] == null)
					{
						currentNode[name] = {};
					}
					
					currentNode = currentNode[name];
				}
			}
			
			currentNode[signature] = "";
		}
		
		//console.log(EngineGraph.Nodes);
	},
	Texts: [],
	Lines: [],
	Draw: function(time){
		EngineGraph.ApplyCanvas(EngineGraph.Canvas.id);
		//
		var e = EngineGraph;
		//
		var deltaTime = (time - e.LastTime) / 1000;
	    e.LastTime = time;
		//
		var ctx = e.Context;
		var w = e.Width;
		var h = e.Height;
		var z = e.Zoom; // == 1 ? e.Zoom : e.Zoom * (2 / e.Zoom);

		e.Zoom = e.Lerp(e.Zoom, e.targetZoom, deltaTime * 3);
		e.clickPoint = e.PointLerp(e.clickPoint, e.targetPosition, deltaTime * 3);

		var calcLen = function(node){
			var count = 0;
			for(var n in node) count++;
			return count;
		};

		var calcPoint = function(point){
			return e.Point((point.x + e.OffsetX) * z, (point.y + e.OffsetY) * z);
		};

		if(e.Lines.length == 0 && e.Texts.length == 0)
		{
			var colors = ["#ffff00", "#00ffff", "#0000ff", "#ff0000", "#ff00ff",
							"#76EE00", "#0147FA", "#CDC5BF", "#24D330",  
							"#5DFC0A", "#F8F8FF", "#FFA500", "#7D9EC0"];

			var shuffle = function(a) {
				var j, x, i;
				for (i = a.length; i; i--) {
					j = Math.floor(Math.random() * i);
					x = a[i - 1];
					a[i - 1] = a[j];
					a[j] = x;
				}
			}

			shuffle(colors);

			var process = function(cur, curIndex, curPoint, space, startAngle, scale){
				var len = calcLen(cur);
				var sector = 360/len;
				var count = 0;
				if(len % 2 == 0) startAngle -= sector/2;

				var seed = (curIndex.length) % colors.length;
				console.log(seed + " -> " + len + ", " + curIndex.length);

				var color = colors[seed % colors.length];
				
				for(var n in cur)
				{
					var Angle = startAngle + count * sector;
					var rotPoint = e.RotatePoint(e.Point(0, space), Angle);
					rotPoint = e.Point(curPoint.x + rotPoint.x, curPoint.y + rotPoint.y);

					e.Lines.push([curPoint, rotPoint, color]);
					e.Texts.push([rotPoint, n, scale, color]);
					
					if(typeof(cur[n]) != "string")
					{
						process(cur[n], n, rotPoint, space/2, Angle, scale * 0.8);
					}
					
					count++;
				}
			};
			
			var curNode = EngineGraph.Nodes;
			process(curNode, "", e.Point(0, 0), 300, 0, 1);
		}
		
		//
		
		ctx.clearRect(0, 0, w, h);
		ctx.fillStyle = "#222";
		ctx.fillRect(0, 0, w, h);
		
		var offsetX = w/2;
		var offsetY = h/2;
		if(e.clickToggle)
		{
			var ox = (w - e.clickPoint.x) - w/2;
			var oy = (h - e.clickPoint.y) - h/2;
			offsetX = w/2 + ox * e.Zoom;
			offsetY = h/2 + oy * e.Zoom;
		}

		var DrawingOffsetX = offsetX;
		var DrawingOffsetY = offsetY;

		ctx.translate(DrawingOffsetX, DrawingOffsetY);

		//
		for(var i = 0; i < e.Lines.length; i++)
		{
			var A = calcPoint(e.Lines[i][0]);
			var B = calcPoint(e.Lines[i][1]);
			var color = e.Lines[i][2];
			e.DrawLine(A, B, color);
		}
		//
		for(var i = 0; i < e.Texts.length; i++)
		{
			var point = calcPoint(e.Texts[i][0]);
			var color = e.Texts[i][3];
			e.DrawCircle(point, 1.5, color);
		}
		//
		var MouseX = e.mousePosition.x - w/2;
		var MouseY = e.mousePosition.y - h/2;
		var MaxDist = 100;

		for(var i = 0; i < e.Texts.length; i++)
		{
			var Point = calcPoint(e.Texts[i][0]);
			var Text = e.Texts[i][1];
			var Scale = e.Texts[i][2];
			e.DrawText(Point, Text, Scale * e.Zoom);
		}
		//
		ctx.translate(-DrawingOffsetX, -DrawingOffsetY);
		//
		e.FPSCount++;

		//e.DrawText(e.Point(10, 40), e.clickPoint.x + ", " + e.clickPoint.y + " | " + e.mousePosition.x + ", " + e.mousePosition.y, 1);
		//e.DrawCircle(e.clickPoint, 10);
		//e.DrawCircle(e.mousePosition, 10);

		window.requestAnimationFrame(e.Draw);
		/*setTimeout(function(){
			e.Draw();
		}, 50);*/
	},
	Point: function(x, y){
		return { x: x, y: y };
	},
	PointLerp: function(a, b, r){ 
		var e = EngineGraph;
		return e.Point(e.Lerp(a.x, b.x, r), e.Lerp(a.y, b.y, r));
	},
	Lerp: function(a, b, r){ return a + (b - a) * r; },
	RotatePoint: function(point, degAngle){
		var e = EngineGraph;
		var Angle = degAngle * (Math.PI/180); // 0.0174532925;
		var x = point.x;
		var y = point.y;
		var rx = Math.round((x * Math.cos(Angle)) - (y * Math.sin(Angle)));
		var ry = Math.round((x * Math.sin(Angle)) + (y * Math.cos(Angle)));
		
		return e.Point(rx, ry);
	},
	GetAngle: function(A, B){
		var X1 = A.x, Y1 = A.y;
		var X2 = B.x, Y2 = B.y;
		var Angle = Math.atan2(Y2 - Y1, X2 - X1) * (180/Math.PI);
		Angle = (Angle < 0) ? Angle + 360 : Angle;
		return Math.round(Angle);
	},
	DrawLine: function(A, B, color)
	{
		var ctx = EngineGraph.Context;
		ctx.beginPath();
		ctx.moveTo(A.x, A.y);
		ctx.lineTo(B.x, B.y);
		ctx.lineWidth = 1;
		ctx.strokeStyle = color;
		ctx.stroke();
	},
	DrawCircle: function(pos, radius, color){
		var ctx = EngineGraph.Context;
		ctx.beginPath();
		ctx.arc(pos.x, pos.y, radius, 0, 2 * Math.PI, false);
		ctx.fillStyle = color;
		ctx.fill();
	},
	DrawText: function(pos, text, scale){
		if(scale > 0)
		{
			var ctx = EngineGraph.Context;
			ctx.font = Math.round(scale * 5) + "px arial";
			
			pos.x += 1;
			pos.y -= 1;

			ctx.strokeStyle = "#000";
			ctx.strokeText(text, pos.x + 1, pos.y + 1);
			ctx.fillStyle = "#fff";
			ctx.fillText(text, pos.x, pos.y);
		}
	},
	getMousePosition: function(moveEvent) {
		var e = EngineGraph;
        var rect = e.Canvas.getBoundingClientRect();
        var x = moveEvent.clientX - rect.left;
        var y = moveEvent.clientY - rect.top;
        return e.Point(x, y);
    },

};