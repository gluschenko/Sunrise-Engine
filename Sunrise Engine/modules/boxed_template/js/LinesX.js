LinesX = {
	Canvas: null,
	Context: null,
	Width: 0,
	Height: 0,
	
	Points: [],
	
	Counter: 0,
	
	FPSCount: 0,
	FPSCountView: 0,
	LastTime: 0,
	Parent: null,

	Init: function(obj, objParent){
		var e = LinesX;

		e.ApplyCanvas(obj, objParent);
		e.Draw(0);

		/*Find(obj).addEventListener("mousemove", function (event) {
            e.mousePosition = e.getMousePosition(event);
        }, false);

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
		
		setInterval(function(){
			e.FPSCountView = e.FPSCount * 2;
			e.FPSCount = 0;
		}, 500);
	},
	ApplyCanvas: function(obj, objParent){
		var C = document.getElementById(obj);
		LinesX.Canvas = C;

		if(objParent == "")
		{
			C.width = window.innerWidth;//LinesX.Parent.clientWidth;
			C.height = window.innerHeight;//LinesX.Parent.clientHeight;
		}
		else
		{
			LinesX.Parent = document.getElementById(objParent);
			C.width = LinesX.Parent.clientWidth;
			C.height = LinesX.Parent.clientHeight;
		}
		
		LinesX.Width = C.width;
		LinesX.Height = C.height;
		LinesX.Context = C.getContext("2d");
		//
		//C.width = C.width;
	},
	Draw: function(time){
	    var e = LinesX;
		e.ApplyCanvas(e.Canvas.id, LinesX.Parent.id);
		//
		var ctx = e.Context;

		var w = e.Width;
		var h = e.Height;
		
	    var deltaTime = (time - e.LastTime) / 1000;
	    e.LastTime = time;
		
		var dotColor = "#333";
		var backColor = "#fff";
		var A = "32, 32, 32";
		var B = "64, 64, 64";
		var C = "96, 96, 96";
		
		ctx.fillStyle = backColor;
		ctx.fillRect(0, 0, w, h);
		//
		if(e.Counter == 0)
		{
			for(var i = 0; i < 30; i++)
			{
				var x = Math.round(Math.random() * w);
				var y = Math.round(Math.random() * h);
				e.Points.push(e.Point(x, y));
			}
		}
		//
		
		for(var i = 0; i < e.Points.length; i++)
		{
			var N = e.Counter + i;
			var range = 10 + i;
			var speed = (i % 2 == 0 ? -1 : 1);
			/*e.Points[i].x += Math.sin(N * speed) * range;
			e.Points[i].y += Math.cos(N * speed) * range;*/
			
			e.Points[i].x += Math.sin(N / i * speed) * range / 100;
			e.Points[i].y += Math.cos(N / i * speed) * range / 100;
			
			e.Points[i].x -= deltaTime * 60;
			if(e.Points[i].x < 0) e.Points[i].x += w;
			
			e.DrawCircle(e.Points[i], 0.5, dotColor);
		}
		
		var LinesIndex = [];
		
		for(var i = 0; i < e.Points.length; i++)
		{
			var PointA = e.Points[i];
			
			for(var j = 0; j < e.Points.length; j++)
			{
				if(i != j)
				{
					var PointB = e.Points[j];
					var Dist = e.Distance(PointA, PointB);
					
					if(!in_array(Dist, LinesIndex))
					{
						var N = (i * e.Points.length + j) % 3;
						
						var color = "";
						if(N == 0) color = A;
						if(N == 1) color = B;
						if(N == 2) color = C;
						
						var alpha = Dist / 300;
						if(alpha > 1)alpha = 1;
						alpha = 1 - alpha;
						alpha = alpha * alpha;
						
						if(alpha > 0)
						{
							var rgba = "rgba(" + color + "," + alpha + ")";
							
							e.DrawLine(PointA, PointB, rgba);
							LinesIndex.push(Dist);
						}
					}
				}
			}
			//break;
		}
		
		e.Counter++;
		//
		e.DrawText(e.Point(10, 30), e.FPSCountView + " FPS", 1);
		e.FPSCount++;
		//
		/*setTimeout(function(){
			e.Draw();
		}, 30);*/
		
		window.requestAnimationFrame(e.Draw);
	},
	Point: function(x, y){
		return { x: x, y: y };
	},
	RotatePoint: function(point, degAngle){
		var Angle = degAngle * (Math.PI/180); // 0.0174532925;
		var x = point.x;
		var y = point.y;
		var rx = Math.round((x * Math.cos(Angle)) - (y * Math.sin(Angle)));
		var ry = Math.round((x * Math.sin(Angle)) + (y * Math.cos(Angle)));
		
		return LinesX.Point(rx, ry);
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
		var ctx = LinesX.Context;
		
		ctx.beginPath();
		ctx.moveTo(A.x, A.y);
		ctx.lineTo(B.x, B.y);
		ctx.lineWidth = 1;
		ctx.strokeStyle = color;
		ctx.stroke();
	},
	DrawCircle: function(pos, radius, color){
		var ctx = LinesX.Context;

		ctx.beginPath();
		ctx.arc(pos.x, pos.y, radius, 0, 2 * Math.PI, false);
		ctx.fillStyle = color;
		ctx.fill();
	},
	DrawText: function(pos, text, scale){
		if(scale > 0)
		{
			var ctx = LinesX.Context;
			ctx.font = Math.round(scale * 14) + "px arial";
			
			pos.x += 1;
			pos.y -= 1;

			ctx.strokeStyle = "#000";
			ctx.strokeText(text, pos.x + 1, pos.y + 1);
			ctx.fillStyle = "#fff";
			ctx.fillText(text, pos.x, pos.y);
		}
	},
	Distance: function(A, B){
		var dx = Math.abs(A.x - B.x);
		var dy = Math.abs(A.y - B.y);
		return Math.sqrt(dx * dx + dy * dy);
	},
	getMousePosition: function(moveEvent) {
        var rect = LinesX.Canvas.getBoundingClientRect();

        var x = moveEvent.clientX - rect.left;
        var y = moveEvent.clientY - rect.top;

        return LinesX.Point(x, y);
    },

};

function in_array(what, where) {
    for(var i = 0; i < where.length; i++)
	{
		if(what == where[i])
		{
			return true;
		}
	}  
    return false;
}
