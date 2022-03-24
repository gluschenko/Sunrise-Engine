<?php

OnLoad(function(){
	$path = dirname(__FILE__);
	AddAdminSection("guidelines", "Гайдлайны", $path."/guidelines_markup.php");
	AddAdminLink("/admin?act=guidelines", "Гайдлайны", 0);
});

?>