<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '-1');

class Search_Results
{
        public          $found = false;
        public          $found_match; // Item

        public          $closest_match = false;
        public          $closest_match_found; // Item

	public		$closest_match_alt = false;
	public		$closest_match_found_alt; // Item

        public          $extra_match = false;
        public          $extra_match_found = array(); // ARR
}

class Item
{
        public          $name; // STR
        public          $id; // INT
        public          $url; // STR
        public          $price; // INT
}

class YoworldItems
{
        private                 $data;
        private                 $lines;
        private                 $items = array();
        private                 $search;

        function __construct(string $q)
        {
                $this->search = $q;
                $this->data = file_get_contents("items.txt");
                $this->lines = explode("\n", $this->data);
                $this->fetch_all_items();
        }

        function _item(array $arr): Item
        {
                $i = new Item();
                $i->name = $arr[0];
                $i->id = $arr[1];
                $i->url = $arr[2];
                $i->price = $arr[3];
                return $i;
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
                        array_push($this->items, $this->_item($info));
                }
        }

        public function search_item_by_name(): Search_Results
        {
                $s = new Search_Results();
                foreach($this->items as $item)
                {
                        $no_case_sen = strtolower($this->search);
                        // Case Senitive Search
                        if($item->name == $this->search && $s->found == false)
                        {
                                $s->found = true;
                            	$s->found_match = $item;
                        } 
                        if((str_contains($item->name, $this->search) || str_contains($item->name, $no_case_sen)) && $s->closest_match_alt == false)
			{
				$s->closest_match_alt = true;
				$s->closest_match_found_alt = $item;
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
                return $this->_item(array("", "", "", ""));
        }
}

?>