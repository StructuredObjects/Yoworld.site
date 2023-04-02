<?php

class Search_Results
{
	public 		$found = false;
	public 		$found_match; // ARR

	public 		$closest_match = false;
	public 		$closest_match_found; // ARR

	public 		$extra_match = false;
	public 		$extra_match_found = array(); // ARR
}

class Item 
{
	public 		$name; // STR 
	public 		$id; // INT
	public 		$url; // STR
	public 		$price; // INT
}

function set_item(array $arr): Item 
{
	$i = new Item();
	$i->name = $arr[0];
	$i->id = $arr[1];
	$i->url = $arr[2];
	$i->price = $arr[3];
	return $i;
}

class YoworldItems
{
	private 		$data;
	private 		$lines;
	private 		$items = array();
	private 		$search;

	function __construct(string $q)
	{
		$this->search = $q;
		$this->data = file_get_contents("items.txt");
		$this->lines = explode("\n", $this->data);
		$this->fetch_all_items();
	}

	public function items(): array { return $this->items; }

	public function parse(string $line): array
	{
		$fix = str_replace("(", "", $line);
		$fix = str_replace(")", "", $fix);
		$fix = str_replace("'", "", $fix);
		return explode(",", $fix);
	}

	public function fetch_all_items()
	{
		foreach($this->lines as $line)
		{
			if(strlen($line) == 0 || strlen($line) < 5) continue;
			$info = $this->parse($line);
			if(count($info) < 3) continue;
			array_push($this->items, set_item($info));
		}
	}

	public function search_item_by_name(): Search_Results
	{
		$s = new Search_Results();
		foreach($this->items as $item)
		{
			$no_case_sen = strtolower($this->search);
			// Case Senitive Search
			if($item->name == $this->search) 
			{
				$s->found = true;
				$s->found_match = $item;
			} else if(str_contains($item->name, $no_case_sen))
			{
				$s->closest_match = true;
				$s->closest_match_found = $item;
			}

			$words = explode(" ", $no_case_sen);
			$match = 0;
			foreach($words as $word)
			{
				if(str_contains(strtolower($item->name), $word))
					$match++;

				if($match > 1) {
					$s->extra_match = true;
					array_push($s->extra_match_found, $item);
				}
			}
		}

		return $s;
	}

	public function search_by_id(): Item
	{
		foreach($this->items as $item)
		{ if($item->id == $this->search) return $item; }
		return set_item(array("", "", "", ""));
	}
}

$eng = new YoworldItems("26295");
$result = $eng->search_by_id();
echo $result->name. " | ". $result->price. "\r\n";


$e = new YoworldItems("cupids bow");
$results = $e->search_item_by_name();
echo count($results->extra_match_found). "\r\n";
foreach($results->extra_match_found as $i)
{
	echo $i->name. " | ". $i->price. "\r\n";
}