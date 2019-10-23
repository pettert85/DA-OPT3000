<?php

define('MAX_WEIGHT', 11); //Max weight of knapsack
define('POPULATION_SIZE', 8);
define('ARRAY_SIZE', 10);
define('MUTATION_PROBABILITY', '0.05'); 

//Initialising the weight and values of the objects to some random numbers.
$object_weight = array(2,4,6,2,8,9,1,9,6,5);
$object_value = array(23,55,36,89,32,19,6,54,38,99);
//11.5 - 13.75 - 6 - 44.5 -  4 - 2.1 - 6 - 6 - 6.33 - 19.8
//  0      1     0     1     0   0     0   0     0     1
//vekt 2 5 4
//value 55 + 89 + 99 = 243

printf("Maximum weight of knapsack: %d\n",MAX_WEIGHT);
printf("Probability of mutation: %.2f\n",MUTATION_PROBABILITY);
printf("Number of objects (array): %d\n",ARRAY_SIZE);
printf("Population size: %d\n\n",POPULATION_SIZE);

printf("%-22s","Weights: ");
for($i = 0; $i < count($object_weight); $i++){
	printf(" %d ",$object_weight[$i]);
}

printf("\n");

printf("%-22s","Values: ");
for($i = 0; $i < count($object_value); $i++){
	printf(" %d ",$object_value[$i]);
}

printf("\n\n");

//run algorithm
genetic_algorithm($object_value,$object_weight);


function genetic_algorithm($object_value,$object_weight){
	$total_best_weight = 0;
	$iteration_number = 0;
	$population = [];
	$fitness = [];

	//We  generate a population of some random knapsacks first
	for($i = 0; $i < POPULATION_SIZE; $i++){
		array_push($population,create_knapsack($object_weight));
	}

	//print out first generation of knapsacks
	printf("%-30s %-35s %-25s %-2s\n", "Name", "Array", "Weight", "Value");
	for($i = 0; $i < count($population); $i++){
		printf("%-8s %-13d","Initial knapsack",$i);

	 	for($j = 0; $j < count($population[$i]); $j++){
			printf(" %d ", $population[$i][$j]);
		}

	 	printf("%9d %26d\n",total_weight($population[$i],$object_weight),total_value($population[$i],$object_value));
	}
	
	do {
		//first clone the parents before we alter the genes
		$offsprings = $population;

		$index1 = mt_rand(0, 9);
		do{
			$index2 = mt_rand(0, 9);
		}while($index1 == $index2);
		
		//alter offsprings by performing two-point crossover on them		
		while(count($offsprings) > 1) {
			
			$offspring1 = array_pop($offsprings);
			$offspring2 = array_pop($offsprings);


			for($j = min($index1,$index2); $j <= max($index1,$index2); $j++){

				$temp = $offspring1[$j];
				$offspring1[$j] = $offspring2[$j];
				$offspring2[$j] = $temp;
			}


			//mutate according to the probability of mutation
			if ( (mt_rand(0, 100) / 100) <= MUTATION_PROBABILITY ){
				$index = mt_rand(0, ARRAY_SIZE-1);
				
				/*$index1 = mt_rand(0, ARRAY_SIZE-1);
				do{
					$index2 = mt_rand(0, ARRAY_SIZE-1);
				}while($index1 == $index2);*/
			
				if(mt_rand(0, 1) == 0){
					
					//Single flip mutation
					if( $offspring1[$index] == 0 ){
						$offspring1[$index] = 1;
					}

					else{
						$offspring1[$index] = 0;
					}
				
				/*	$temp = $offspring1[$index1];
					$offspring1[$index1] = $offspring1[$index2];
					$offspring1[$index2] = $temp;
					*/
				
				}

				else{
					
					//Single flip mutation
					if( $offspring2[$index] == 0 ){
						$offspring2[$index] = 1;
					}

					else{
						$offspring2[$index] = 0;
					}
			/*
					$temp = $offspring2[$index1];
					$offspring2[$index1] = $offspring2[$index2];
					$offspring2[$index2] = $temp;*/
				}
				
			} //mutation

			//make sure the weight off the newly created offsprings are a feasable solution, otherwise adjust their wight accordingly				
			while(total_weight($offspring1, $object_weight) > MAX_WEIGHT) {
				$offspring1[array_search(1, $offspring1) ] = 0; 
			}

			while(total_weight($offspring2, $object_weight) > MAX_WEIGHT) {
				$offspring2[array_search(1, $offspring2) ] = 0; 
			}

			//add the offsprings to the population
			array_push($population, $offspring1, $offspring2);	
		}

		//find fitness of increased population
		for($i = 0; $i < count($population); $i++){
			$fitness[$i] = total_value($population[$i],$object_value);
		}

		arsort($fitness); // sort descending according to the total value of each individual

		$keys = array_keys($fitness); // get the key positions
		
		//remove from population according to Darwins Law :)
		for($i = count($keys) -1; $i >= POPULATION_SIZE; $i-- ){			
			unset($population[$keys[$i]]);	
		}
		
		//arrange according to populations fittness again
		$fitness = []; //empty old fitness array:
		$population = array_values($population); //reindex array after unset

		for($i = 0; $i < count($population); $i++){
			$fitness[$i] = total_value($population[$i],$object_value);
		}

		arsort($fitness); // sort descending according to the total value of each individual

		$keys = array_keys($fitness); // get the key positions
		

		//check to see if we got a new best, and if so, print it to the console
		if(total_weight($population[$keys[0]],$object_weight) > $total_best_weight){

			$total_best_weight = total_weight($population[$keys[0]],$object_weight);

			//print out the optimized knapsack
			printf("%-30s","New Best Knapsack");

			for($i = 0; $i < count($population[$keys[0]]); $i++){
				printf(" %d ", $population[$keys[0]][$i]);
			}

			printf("%9d %26d",total_weight($population[$keys[0]],$object_weight),total_value($population[$keys[0]],$object_value));
			printf(" - Number of iterations when found: %d\n", $iteration_number);
		}

		$iteration_number++;
	}while(true);


	//return $population[$keys[0]]; //return the best solution found

}


function create_knapsack($object_weight){
	$knapsack = []; //0's means item is not selected, and 1's mean selected
	for($i = 0; $i < ARRAY_SIZE; $i++){
		array_push($knapsack, 0);
	}

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
	//returns the total weight of the objects in the knapsack given as argument one.
	
	$weight = 0;

	for($i = 0; $i < count($knapsack); $i++){
		if($knapsack[$i] == 1 ){
			$weight += $object_weight[$i];
		}
	}

	return $weight;
}

function total_value($knapsack, $object_value){
	//returns the total value of the objects in the knapsack given as argument one
	
	$value = 0;

	for($i = 0; $i < count($knapsack); $i++){
		if($knapsack[$i] == 1 ){
			$value += $object_value[$i];
		}
	}

	return $value;
}

?>