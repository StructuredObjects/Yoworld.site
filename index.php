<?php

class Search_Results
{
	public 		$found = false;
	public 		$found_match; // ARR

	public 		$closest_match = false;
	public 		$closest_match_found; // ARR

	public 		$extra_match = false;
	public 		$extra_match_found; // ARR
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
	private 		$items;
	private 		$search;

	function __construct(string $q)
	{
		$this->search = $q;
		$this->data = file_get_contents("items.txt");
		$this->lines = explode("\n", $this->data);
	}

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
			} else if($no_case_sen == strtolower($item->name))
			{
				$s->closest_match = true;
				$s->closest_match_found = $item;
			}

			$words = explode(" ", $no_case_sen);
			$match = 0;
			foreach($words as $word)
			{
				if(str_contains($word, strtolower($item->name)))
					$match++;

				if($match > 1) {
					$extra_match = true;
					array_push($extra_match_found, $item);
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