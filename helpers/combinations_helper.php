<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
Functions for combinations and permuatations


*/

	//this is a cartesian product algorithm taken from http://stackoverflow.com/questions/6311779/finding-cartesian-product-with-php-associative-arrays
	//This simply returns an array of all possible combinations
	function cartesian_product($input) {
    	$result = array();

		foreach ($input as $key => $values) {
			// If a sub-array is empty, it doesn't affect the cartesian product
			if (empty($values)) {
				continue;
			}
	
			// Seeding the product array with the values from the first sub-array
			if (empty($result)) {
				foreach($values as $value) {
					$result[] = array($key => $value);
				}
			}
			else {
				// Second and subsequent input sub-arrays work like this:
				//   1. In each existing array inside $product, add an item with
				//      key == $key and value == first item in input sub-array
				//   2. Then, for each remaining item in current input sub-array,
				//      add a copy of each existing array inside $product with
				//      key == $key and value == first item of input sub-array
	
				// Store all items to be added to $product here; adding them
				// inside the foreach will result in an infinite loop
				$append = array();
	
				foreach($result as &$product) {
					// Do step 1 above. array_shift is not the most efficient, but
					// it allows us to iterate over the rest of the items with a
					// simple foreach, making the code short and easy to read.
					$product[$key] = array_shift($values);
	
					// $product is by reference (that's why the key we added above
					// will appear in the end result), so make a copy of it here
					$copy = $product;
	
					// Do step 2 above.
					foreach($values as $pos_key=>$item) {
						$copy[$key] = $item;
						$append[] = $copy;
					}
					
					// Undo the side effecst of array_shift
					array_unshift($values, $product[$key]);			
				}
	
				// Out of the foreach, we can add to $results now
				$result = array_merge($result, $append);
							
			}
		}
		
		return $result;
	}//end cartesian product algorithm
	
	
	//This finds all permutations of an array
	function permutations($items, $perms = array( )) {
		if (empty($items)) {
			$return = array($perms);
		}  else {
			$return = array();
			for ($i = count($items) - 1; $i >= 0; --$i) {
				 $newitems = $items;
				 $newperms = $perms;
			 list($foo) = array_splice($newitems, $i, 1);
				 array_unshift($newperms, $foo);
				 $return = array_merge($return, permutations($newitems, $newperms));
			 }
		}
		return $return;
	}