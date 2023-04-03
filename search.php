<html>

<form method="post">
	<input type="text" name="item_name" id="item_name" placeholder="Enter Item Name Or ID"/>
	<input type="submit" name="search_item" id="search_item" value="Search"/>
</form>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '-1');
include 'yoworldlib.php';
if(array_key_exists("search_item", $_POST))
{
	$search = $_POST['item_name'];
	$eng = new YoworldItems($search);
	$result = (is_numeric($search) ? $eng->search_by_id() : $eng->search_item_by_name());

	if(is_numeric($search))  {
		echo "<b>Item Name: </b>". $result->name. "<br/><b>Price: </b>". $result->price. "<br/><img src=\"". $result->url. "\"/><br/>";
	} else if($result->found) {
		echo "<b>Exact Match Found....!</b><br />";
		echo "<b>Item Name: </b>". $result->found_match->name. "<br/><b>ID: </b>". $result->found_match->id. "<br/><b>Price: </b>". $result->found_match->price. "<br/><img src=\"". $result->found_match->url. "\"/><br/><br />";
	}

	if($result->closest_match_alt)
	{
		echo "<br/></br/>Closest Match Found...!<br />";
		echo "<b>Item Name: </b>". $result->closest_match_found_alt->name. "<br/><b>ID: </b>". $result->closest_match_found_alt->id. "<br/><b>Price: </b>". $result->closest_match_found_alt->price. "<br/><img src=\"". $result->closest_match_found_alt->url. "\"/><br/><br />";
	}


	echo "<br /> Results: ". count($result->extra_match_found). " matches found!<br/>";
	if(count($result->extra_match_found) > 0) {
		$c = 0;
		foreach($result->extra_match_found as $i)
		{
			if($c > 20) break;
			echo "<b>Item ID: </b>". $i->id. " | <b>Item Name: </b>". $i->name. "<br/><b>Price: </b>". $i->price. "<br/><img src=\"". $i->url. "\"/><br/>";
			$c++;
		}
	}
}

?>
