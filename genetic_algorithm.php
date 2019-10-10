<?php

define('MAX_WEIGHT', 25); //Max weight of knapsack

$object_weight = array(2,7,6,4,8,9,1,9,6,5);
$object_value = array(1,73,36,89,9,19,50,62,38,99);

//create two inital knapsacks 
$knapsack1 = create_napsack($object_weight);
$knapsack2 = create_napsack($object_weight);

$optimal_knapsack = genetic_algorithm($knapsack1,$knapsack2,$object_value,$object_weight);

printf("Knapsack 1 -> weight: %d value: %d \n", total_weight($knapsack1, $object_weight), total_value($knapsack1,$object_value) ); 
printf("Knapsack 2 -> weight: %d value: %d\n", total_weight($knapsack2, $object_weight), total_value($knapsack2,$object_value)); 
printf("Optimal Napsack -> weight: %d value: %d\n", total_weight($optimal_knapsack, $object_weight), total_value($optimal_knapsack,$object_value)); 
	
function create_napsack($object_weight){
	$knapsack = array(0,0,0,0,0,0,0,0,0,0);
	$weight = 0;

	do{
		for($i = 0; $i < count($knapsack); $i++){
			if(mt_rand(0, 1) == 1){
				$weight += $object_weight[$i];

				if($weight < MAX_WEIGHT){
					$knapsack[$i] = 1;
				}
			}
		}
	}while ($weight < MAX_WEIGHT);
	
	return $knapsack;
}

function genetic_algorithm($parent1,$parent2,$object_value,$object_weight){
/*	uses a genetic approach with parents and offspring in order to improve the result
	of the napsack. The two family members with highest total values are kept each round.
	The algorithm runs in a loop until the two family members with highest values are eqaual and
	the result is returned back to the caller. */

	do{

		$index1 = mt_rand(0, 9);
		do{
			$index2 = mt_rand(0, 9);
		}while($index1 == $index2);

		//generate two offsprings
		$offspring1 = $parent1;
		$offspring2 = $parent2;

		//let offsprings inherit DNA from the parents in the two random positions we found
		$offspring1[$index1] = $parent2[$index1];
		$offspring1[$index2] = $parent2[$index2];

		$offspring2[$index1] = $parent1[$index1];
		$offspring2[$index2] = $parent1[$index2];

		//let algorithm choose to mutate (flip one position) the offspring or not
		if(mt_rand(0, 1) == 1){
			$index = mt_rand(0, 9);

			if(mt_rand(0, 1) ){
				
				if($offspring1[$index1] == 0){
					$offspring1[$index1] = 1;
				}  

				else $offspring1[$index1] = 0;
			}

			else{
				if($offspring2[$index1] == 0){
					$offspring2[$index1] = 1;
				}  

				else $offspring2[$index1] = 0;
			}
		}

		//checks to make sure the total weight of the knapsack does not go over MAX_WEIGHT
		while(total_weight($offspring1, $object_weight) > MAX_WEIGHT) {
			$offspring1[array_search(1, $offspring1) ] = 0; //reduce weight until solution is feasible
		}

		while(total_weight($offspring2, $object_weight) > MAX_WEIGHT) {
			$offspring2[array_search(1, $offspring2) ] = 0; //reduce weight until solution is feasible
		}		


		$family = array($parent1,$parent2,$offspring1,$offspring2);

		$values = array(
			0 => total_value($parent1,$object_value), 
			1 => total_value($parent2,$object_value),
			2 => total_value($offspring1,$object_value),
			3 => total_value($offspring2,$object_value));

		arsort($values); // sort descending according to the total values
		$keys = array_keys($values); // get the keys
		
		//update the parents to the family members with the highest value
		$parent1 = $family[$keys[0]];
		$parent2 = $family[$keys[1]];
	}while($parent1 != $parent2);
	

	return $parent1; //return the best result
}

function total_weight($knapsack, $object_weight){
	//returns the total weight of the objects in the napsack given as argument one.
	$weight = 0;

	for($i = 0; $i < count($knapsack); $i++){
		if($knapsack[$i] == 1 ){
			$weight += $object_weight[$i];
		}
	}

	return $weight;
}

function total_value($knapsack, $object_value){
	//returns the total value of the objects in the napsack given as argument one
	$value = 0;

	for($i = 0; $i < count($knapsack); $i++){
		if($knapsack[$i] == 1 ){
			$value += $object_value[$i];
		}
	}

	return $value;
}
?>