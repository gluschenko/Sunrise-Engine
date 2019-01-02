<!--
<div class='button back1' onclick='ApiMethod("engine.sections.get", { url: "/main" }, function(data){ console.log(data); });'>API</div>
<div class='space'></div>
<div class='button back2' onclick='NavigateAsync("/dev");'>Dev (async)</div>
<div class='space'></div>
<div class='button back0' onclick='NavigateAsync("/test");'>Test</div>
<div class='space'></div>
<div class='button back4' onclick='NavigateAsync("/");'>Main</div>
<div class='space'></div>
<div class='button back5' onclick='NavigateAsync("/fhfhhfhf");'>Error</div>
<div class='space'></div>
-->

/main

<pre>
<?php print_r(GetSection($section['url'], GetSiteSettings()));?>
</pre>