<!doctype html>
<html lang="en">
<head>
	<title>Powers List: <?php echo $url_path;?></title>
	<?php include("header.html");?>
	<style><?php include '/home/joe/huldufolk/styles.css'; ?></style>
	<script>
		function display() {
			// this.classList.remove("foo");
			this.classList.add("anotherclass");
		}
	</script>
</head>
<body>
	<h1 class="page-title">Character Powers Summary</h1>
	<p style="text-align: center;">thehuldufolk.com/powers/<?php echo $url_path;?></p>
	<div class="container container--results">

<?php

//print_r($spheres);
foreach($spheres as $sphere){
	echo "<h1 class=\"sphere-title\">Sphere: $sphere[name]</h1>\n";
	echo "<p class=\"sphere-description\">".str_replace("\n","<br/>\n",$sphere['description'])."</p>\n";
	foreach($sphere['paths'] as $path){
		$shortpath=str_replace(' ','_',$path['name']);
		echo "<div id='accordion$shortpath' class='d-print-inline'>\n";
		echo "<h2 class=\"path-title\">Path: $path[name]<span class='d-none d-md-inline'> — ";
		echo "$path[levelnum] Level";
		if($path['levelnum'] > 1) echo "s";
		echo "</span></h2>\n";
		if($path['flat']) echo "<h3>+$path[levelnum] Bonus</h3>\n";
		
		echo "<p class='path-description'>".str_replace("\n","<br/>\n",$path['description'])."</p>\n";
		if(!$path['flat']){
			echo "<div class='levels-list'>";
			foreach($path['levels'] as $level){
				$idSuffix=$shortpath.'Level'.$level['level'];
				// echo "<div class='d-none d-sm-block'><h3>Level $level[level]: $level[name]</h3></div>"; //regular header
				echo "<article class='card'><header class='card__header-container d-block d-sm-none' data-toggle='collapse' data-target='#collapse{$idSuffix}' aria-expanded='false' aria-controls='collapse{$idSuffix}' id='heading{$idSuffix}'>"; //mobile header
				echo "<h3 class='card__header'>Level $level[level]: $level[name]</h3></header>\n";
				
				echo "<div id='collapse{$idSuffix}' class='collapse dont-collapse-sm d-print-block' aria-labelledby='heading{$idSuffix}' data-parent='#accordion$path[name]'>";
				echo "<div class='card-body d-print-block'><p>".str_replace("\n","<br/>\n",$level['description'])."</p></div></div></article>\n";
			}
			echo "</div>";
		}
		echo "</div>";
	}
}
?>
</div>
</body>
</html>
