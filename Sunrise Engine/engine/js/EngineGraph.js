EngineGraph = {
	Canvas: null,
	Context: null,
	Width: 0,
	Height: 0,
	Nodes: {},
	Zoom: 1,
	OffsetX: 0,
	OffsetY: 0,
	mousePosition: { x: 0, y: 0 },
	FPSCount: 0,
	FPSCountView: 0,

	Init: function(obj, struct){
		var e = EngineGraph;

		e.ApplyCanvas(obj);
		e.CreateStructure(struct);
		e.Draw();

		Find(obj).addEventListener("mousemove", function (event) {
            e.mousePosition = e.getMousePosition(event);
        }, false);

		setInterval(function(){
			e.FPSCountView = e.FPSCount * 5;
			e.FPSCount = 0;
		}, 200);

		/*Find(obj).addEventListener("click", function (event) {
            e.Zoom *= 1.05;
			e.OffsetX += (e.Width/2 - e.mousePosition.x) < 0 ? -10 : 10;
			e.OffsetY += (e.Height/2 - e.mousePosition.y) < 0 ? -10 : 10;
			e.Draw();
			event.preventDefault();
        }, false);

		Find(obj).addEventListener("contextmenu", function (event) {
            e.Zoom *= 0.95;
			e.OffsetX += (e.Width/2 - e.mousePosition.x) < 0 ? -10 : 10;
			e.OffsetY += (e.Height/2 - e.mousePosition.y) < 0 ? -10 : 10;
			e.Draw();
			event.preventDefault();
        }, false);*/
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
	Draw: function(){
		EngineGraph.ApplyCanvas(EngineGraph.Canvas.id);
		//
		var ctx = EngineGraph.Context;

		var w = EngineGraph.Width;
		var h = EngineGraph.Height;
		var e = EngineGraph;

		var calcLen = function(node){
			var count = 0;
			for(var n in node)count++;
			return count;
		};

		var calcPoint = function(point){
			return { x: (point.x + e.OffsetX) * e.Zoom, y: (point.y + e.OffsetY) * e.Zoom };
		};

		if(e.Lines.length == 0 && e.Texts.length == 0)
		{
			var curNode = EngineGraph.Nodes;
			
			var process = function(cur, curPoint, space, startAngle, scale){
				var len = calcLen(cur);
				var sector = 360/len;
				var count = 0;
				if(len % 2 == 0)startAngle -= sector/2;
				
				for(var n in cur)
				{
					var Angle = startAngle + count * sector;
					var rotPoint = e.RotatePoint({ x: 0, y: space }, Angle);
					rotPoint = { x: curPoint.x + rotPoint.x, y: curPoint.y + rotPoint.y };
					
					e.Lines.push([curPoint, rotPoint]);
					e.Texts.push([rotPoint, n, scale]);
					
					if(typeof(cur[n]) != "string")
					{
						process(cur[n], rotPoint, space/2, Angle, scale * 0.8);
					}
					
					count++;
				}
			};
			
			process(curNode, { x: 0, y: 0 }, 300, 0, 1);
		}
		
		//
		
		ctx.clearRect(0, 0, w, h);
		ctx.fillStyle = "#222";
		ctx.fillRect(0, 0, w, h);
		
		var DrawingOffsetX = w/2;// - (w/2 - e.mousePosition.x);
		var DrawingOffsetY = h/2;// - (h/2 - e.mousePosition.y);

		ctx.translate(DrawingOffsetX, DrawingOffsetY);

		//
		for(var i = 0; i < e.Lines.length; i++)
		{
			var A = calcPoint(e.Lines[i][0]);
			var B = calcPoint(e.Lines[i][1]);
			
			e.DrawLine(A, B);
		}
		//
		for(var i = 0; i < e.Texts.length; i++)
		{
			var Point = calcPoint(e.Texts[i][0]);

			e.DrawCircle(Point, 2);
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

			var dx = Point.x - MouseX;
			var dy = Point.y - MouseY;
			var Dist = Math.sqrt(dx * dx + dy * dy);

			var scaleRate = 0.5;
			if(Dist < MaxDist)
			{
				scaleRate += Math.pow((MaxDist - Dist)/MaxDist, 3);
			}

			e.DrawText(Point, Text, Scale * scaleRate);
		}
		//
		ctx.translate(-DrawingOffsetX, -DrawingOffsetY);
		//
		e.FPSCount++;
		
		setTimeout(function(){
			e.Draw();
		}, 50);
	},
	RotatePoint: function(point, degAngle){
		var Angle = degAngle * (Math.PI/180); // 0.0174532925;
		var x = point.x;
		var y = point.y;
		var rx = Math.round((x * Math.cos(Angle)) - (y * Math.sin(Angle)));
		var ry = Math.round((x * Math.sin(Angle)) + (y * Math.cos(Angle)));
		
		return { x: rx, y: ry };
	},
	GetAngle: function(A, B){
		var X1 = A.x, Y1 = A.y;
		var X2 = B.x, Y2 = B.y;
		var Angle = Math.atan2(Y2 - Y1, X2 - X1) * (180/Math.PI);
		Angle = (Angle < 0) ? Angle + 360 : Angle;

		return Math.round(Angle);
	},
	DrawLine: function(A, B)
	{
		var ctx = EngineGraph.Context;
		
		ctx.beginPath();
		ctx.moveTo(A.x, A.y);
		ctx.lineTo(B.x, B.y);
		ctx.lineWidth = 1;
		ctx.strokeStyle = "#0f0";
		ctx.stroke();
	},
	DrawCircle: function(pos, radius){
		var ctx = EngineGraph.Context;

		ctx.beginPath();
		ctx.arc(pos.x, pos.y, radius, 0, 2 * Math.PI, false);
		ctx.fillStyle = "#0f0";
		ctx.fill();
	},
	DrawText: function(pos, text, scale){
		if(scale > 0)
		{
			var ctx = EngineGraph.Context;
			ctx.font = Math.round(scale * 14) + "px arial";
			
			pos.x += 1;
			pos.y -= 1;

			ctx.strokeStyle = "#000";
			ctx.strokeText(text, pos.x + 1, pos.y + 1);
			ctx.fillStyle = "#fff";
			ctx.fillText(text, pos.x, pos.y);
		}
	},
	getMousePosition: function(moveEvent) {
        var rect = EngineGraph.Canvas.getBoundingClientRect();

        var x = moveEvent.clientX - rect.left;
        var y = moveEvent.clientY - rect.top;

        return { x: x, y: y} ;
    },

};