<?php

class Search_Results
{
	public $found = false;
	public $found_match;

	public $closest_match = false;
	public $closest_match_found;

	public $extra_match = false;
	public $extra_match_found;
}

class Item 
{
	public $name;
	public $id;
	public $url;
	public $price;
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
	private $data;
	private $lines;
	private $items;
	private $search;
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
}