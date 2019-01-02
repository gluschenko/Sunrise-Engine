<script type="text/javascript">

function Matrix() {
	
	for (s = window.screen,
		w = q.width = s.width,
		h = q.height = s.height, 
		m = Math.random,
		p = [],
		
		i = 0; 
		i < 256;
		p[i++] = 1);
		
	var Loop = setInterval(function(){
	
		q.getContext('2d').fillStyle='rgba(0,0,0,0.05)';
		q.getContext('2d').fillRect(0,0,w,h);
		
		q.getContext('2d').fillStyle='#22FF22';
		p.map(function(v,i){
			randomNum = m() * 33;
			randomAsianChar = String.fromCharCode(30000 + randomNum);
			
			q.getContext('2d').fillText(randomAsianChar, i*10, v);
			
			minimumHeight = 758;
			num = minimumHeight+m()*10000;
			p[i] = (v > num) ? 0 : v + 10;
		});
	}, 33);
}

var bgTrack = new Audio("/engine/assets/navras.mp3");
bgTrack.play();

</script>

<div style='position: fixed;'><canvas id=q></canvas></div>

<script>
Matrix();
</script>