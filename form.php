<!doctype html>
<html lang="en">
<head>
	<title>Powers List Creator</title>
	<?php include("header.html"); ?>
	<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.6.0/bootstrap-slider.min.js"></script>
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.6.0/css/bootstrap-slider.min.css">
	<style><?php include('styles.css'); ?></style>
</head>
<body><div class="container">
	<header>Huldufolk Sheet Helper</header>
	<form method='post'>
	
<?php
foreach($spheres as $sphere){
	echo '<div class="form-group" style="">';
	echo "<h3>$sphere[name]</h3>\n";
	if(!empty($sphere['paths'])){
		foreach($sphere['paths'] as $path){
			echo "<div class='form-row'>\n";
			echo "\t<div class='col col-xs-12 col-sm-12 col-md-4 offset-md-1'><h4>$path[name]</h4></div>\n";
			echo "\t<div class='input input--slider'>";
			echo "<input id='path_$path[id]' name='path[".$path['id']."]' type='range' min='0' max='5' step='1' value='0' list='slider'>";
			?>
			<div class="ticks">
				<span class="tick">0</span>
				<span class="tick">1</span>
				<span class="tick">2</span>
				<span class="tick">3</span>
				<span class="tick">4</span>
				<span class="tick">5</span>
			</div>
		          <!--data-provide="slider"-->
		          <!--data-slider-ticks="[0, 1, 2, 3, 4, 5]"-->
		          <!--data-slider-ticks-labels='["0", "1", "2", "3", "4", "5"]'-->
		          <!--data-slider-min="0"-->
		          <!--data-slider-max="5"-->
		          <!--data-slider-step="1"-->
		          <!--data-slider-value="0"-->
		          <!--data-slider-tooltip="hide" />-->
			
			<?php
			echo "\t</div></div>\n";
		}
	}
	echo "</div>\n";
}

?>
<div class="button-container">
	<button type="submit" class="button">Submit</button>
</div>
</form></div>

</body>
</html>