<?php

define('MAX_WEIGHT', 30); //Max weight of knapsack

//Initialising the weight and values of the objects to some random numbers.
$object_weight = array(2,7,6,4,8,9,1,9,6,5);
$object_value = array(1,73,36,89,9,19,50,62,38,99);

// We  generate a population of four individuals first (knapsacks)
$knapsacks = array(
	create_napsack($object_weight),
	create_napsack($object_weight),
	create_napsack($object_weight),
	create_napsack($object_weight));


$optimal_knapsack = genetic_algorithm($knapsacks,$object_value,$object_weight);

printf("Optimized Napsack -> weight: %d value: %d\n", total_weight($optimal_knapsack, $object_weight), total_value($optimal_knapsack,$object_value)); 


function genetic_algorithm($population,$object_value,$object_weight){
	$max_tries = 10000;

	//find fitness of population and which couple to mate
	$fitness = array(
		0 => total_value($population[0],$object_value), 
		1 => total_value($population[1],$object_value),
		2 => total_value($population[2],$object_value),
		3 => total_value($population[3],$object_value));

	arsort($fitness); // sort descending according to the total value of each individual
	$keys = array_keys($fitness); // get the key positions


	do {
		
		//create new offsprings by performing two-point crossover on them

		$offspring1 = $population[$keys[0]];
		$offspring2 = $population[$keys[1]];
		
		//two random numbers for crossover
		$index1 = mt_rand(0, 9);
		do{
			$index2 = mt_rand(0, 9);

		}while($index1 == $index2);


		for($i = min($index1,$index2); $i <= max($index1,$index2); $i++){
			$temp = $offspring1[$i];
			$offspring1[$i] = $offspring2[$i];
			$offspring2[$i] = $temp;
		}


		//let the algorithm choose wheter to mutate DNA off one off the offsprings or not
		if(mt_rand(0, 1) == 1){
			
			$index = mt_rand(0, 9);

			if(mt_rand(0, 1) ){
				$offspring1[$index] = mt_rand(0, 1);
			}

			else{
				$offspring2[$index] = mt_rand(0, 1);
			}
		}


		//make sure the weight off the newly created offsprings are a feasable solution, otherwise adjust the wight

		while(total_weight($offspring1, $object_weight) > MAX_WEIGHT) {
			$offspring1[array_search(1, $offspring1) ] = 0; 
		}

		while(total_weight($offspring2, $object_weight) > MAX_WEIGHT) {
			$offspring2[array_search(1, $offspring2) ] = 0; 
		}		

		//add offsprings to population		
		array_push($population, $offspring1, $offspring2);


		//find and remove weakest individuals in population
		$fitness = array(
			0 => total_value($population[0],$object_value), 
			1 => total_value($population[1],$object_value),
			2 => total_value($population[2],$object_value),
			3 => total_value($population[3],$object_value),
			4 => total_value($population[4],$object_value),
			5 => total_value($population[5],$object_value));
		

		arsort($fitness); // sort descending according to the total value of each individual
		$keys = array_keys($fitness);

		unset($population[$keys[5]]);
		unset($population[$keys[4]]);

		//arrange according to populations fittness again
		$population = array_values($population); //reindex array after unset

			$fitness = array(
			0 => total_value($population[0],$object_value), 
			1 => total_value($population[1],$object_value),
			2 => total_value($population[2],$object_value),
			3 => total_value($population[3],$object_value));

		arsort($fitness); // sort descending according to the total value of each individual
		$keys = array_keys($fitness); // get the key positions
		
		$max_tries--;

	}while( $max_tries > 0);


	return $population[$keys[0]]; //return the best solution found

}


function create_napsack($object_weight){
	$knapsack = array(0,0,0,0,0,0,0,0,0,0); //0's means item is not selected, and 1's mean selected
	
	$weight = 0;

		for($i = 0; $i < count($knapsack); $i++){
			if(mt_rand(0, 1) == 1){
				$weight += $object_weight[$i];

				if($weight < MAX_WEIGHT){
					$knapsack[$i] = 1;
				}
			}
		}
	
	return $knapsack;
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