<?php
/*
Learning to write some simple algorithms in php


*/


//variable definitions
$init_route;

//change to 500 nodes later
$city_matrix = array
(
    array(0,15,10,5),
    array(15,0,12,7),
    array(10,12,0,8),
    array(5,7,8,0)
);

$visited = [count($city_matrix[0])]; //is city visited
$num_cities = array_sum($visited); //num of cities


//No cities are visited yet(Boool false)
for($i=0; $i < $num_cities + 10; $i++){
    $visited[$i] = FALSE;
}

printf("
1: Random
2: Iterative Random
3: Greedy Algorithm\n");

printf("Choose method for the initial solution:");
$choice = fgets(STDIN);

switch($choice){
    case(1):
        $init_route = random_initial($num_cities,$visited);
        printf("Sum: %d\n", distance_calc($init_route,$city_matrix));
        break;
    case(2):
     printf("choice 2");
     break;
    case(3):
        printf("choise 3");
        break;
    default:
}

function random_initial($num_cities,$visited){
    $rand_max = $num_cities - 1;
    $route_index = 0;

    $route = [$num_cities]; // Travel route array
    
    while($num_cities != 0){
        //find a new random city which is not already visited
        do {
            $next_city = mt_rand(0,$rand_max);
        }while($visited[$next_city]);

        //update new travel route and citys that are visited
        $visited[$next_city] = TRUE;
        $route[$route_index] = $next_city;
        
        
        //increase iterations
        $num_cities--;
        $route_index++;
    }
    //return the complete random route back to caller
    return $route;

}

function distance_calc($route, $city_matrix){
    $sum = 0; //initial sum 0

    //sum all travels from values in the city_matrix
    for($i = 0; $i < count($route) -1; $i++){
        $sum += $city_matrix[$route[$i]][$route[$i + 1]];
   }

    //add the route from last node back to first
    //printf("rundesum: %d",$city_matrix[count($route) -1][$route[0]]);
    $sum += $city_matrix[$route[ (count($route)-1)]][$route[0]];
    return $sum;
}


?>