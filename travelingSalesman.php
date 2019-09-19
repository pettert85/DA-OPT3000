<?php
/*
Learning to write some simple algorithms in php
From laptop
*/

printf("Choose size of matrix (random numbers) or type fixed(default)
    1: 50 x 50
    2: 100 x 100
    3: 250 x 250
    4: 500 x 500
    default(fixed) == enter:
    Enter your choice: ");
    

$choice = fgets(STDIN); //read from user
switch ($choice){
    case 1:
        $city_matrix = matrix_generator(50);
        break;

    case 2:
        $city_matrix = matrix_generator(100);
        break;

    case 3:
        $city_matrix = matrix_generator(250);
        break;

    case 4:
        $city_matrix = matrix_generator(500);
        break;

    default:    
        $city_matrix = array
        (
        array(0,15,10,5),
        array(15,0,12,7),
        array(10,12,0,8),
        array(5,7,8,0)
        );  
        break;
}



//variable definitions
$init_route;
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

printf("Choose method for the initial solution: ");
$choice = fgets(STDIN);


switch($choice){
    case(1):
        $init_route = random_initial($num_cities,$visited);
        $inital_sum = distance_calc($init_route,$city_matrix);
        printf("Random Inital sum: %d\n",$inital_sum);
        break;
    case(2):
        printf("choice 2\n");
        $init_route = interative_random($num_cities,$visited,$city_matrix);
        $inital_sum = distance_calc($init_route,$city_matrix);
        printf("Iterative Random Initial Sum: %d\n",$init);
        break;
    case(3):
        printf("choice 3\n");
         $init_route = greedy_algorithm($num_cities,$visited,$city_matrix);
         $inital_sum = distance_calc($init_route,$city_matrix);
        printf("Greedy Algorithm Inital sum: %d\n",$inital_sum);
        break;
    default:
}

printf("Improve your result using the following options:
    1: Greedy Huristic
    2: something else
    default - no improvements\n Method for improvement: " );
$choice = fgets(STDIN);
switch ($choice) {
    case 1:
        $improved_route = greedy_improvement($init_route,$city_matrix);
        $improved_sum = distance_calc($improved_route,$city_matrix);
        printf("Sum with Greey Huristic Algorithm: %s\n",$improved_sum);
        break;
    case 2:
        # code...
        break;
    
    default:
        printf("Goodbye\n");
        break;
}

$percentage = (($inital_sum - $improved_sum) / $inital_sum) * 100;
printf("RESULTS:
    Inital: %d
    Improved: %d
    Improved Percentage: %d%%\n",$inital_sum,$improved_sum,$percentage);

/*A random function that return only a random route*/

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

/*
Creates several random routes and return the best found through n iterations
*/
function interative_random($num_cities,$visited,$city_matrix){
    $stop = 100;
    $best_route = random_initial($num_cities,$visited); //initial route
    $best_sum = distance_calc($best_route,$city_matrix);

    while($stop != 0){
        $route = random_initial($num_cities,$visited); 
        $sum = distance_calc($route,$city_matrix);
        if($sum < $best_sum){
            $best_sum = $sum;
            $best_route = $route;
        }
        
        $stop--;
    }

    return $best_route;
}

/*
Greedy alghorithm always choose the next node with the lowest cost (travel distance)
*/

function greedy_algorithm($num_cities,$visited,$city_matrix){
    $best = max($city_matrix); //Set to highest number in matrix
    $route_index = 0;

    //generate random starting point
    $visited_index = mt_rand(0, $num_cities - 1); //Start in random city
    $visited[$visited_index] = TRUE; //Starting city is visited
    $route[$route_index] = $visited_index; //update route with the first city
    $route_index++;

    for($i = 0; $i < $num_cities -1; $i++){
        for($j = 0; $j < $num_cities; $j++){
            if($city_matrix[$visited_index][$j] < $best && !$visited[$j]){
                $best = $city_matrix[$visited_index][$j];
                $best_index = $j; //Next city to choose
            }
        }

        $visited_index = $best_index;
        $visited[$visited_index] = TRUE;
        $route[$route_index] =$visited_index;
        $route_index++;
        $best = max($city_matrix);
    }
    
    return $route;


}


function greedy_improvement($init_route,$city_matrix){
    for($i = 0; $i < 1000; $i++){
        $inital_sum = distance_calc($init_route,$city_matrix); //sum of our initial route

        //get two new positions in matrix to swap
        do {
            $position1 = mt_rand(0, count($init_route)-1);
            $position2 = mt_rand(0, count($init_route)-1);
        }while($position1 == $position2);
        
        $temp = $init_route[$position1]; //keep city number
        
        //swap city locations in an attempt to improve the result
        $init_route[$position1] = $init_route[$position2];
        $init_route[$position2] = $temp;
        

        $new_sum = distance_calc($init_route,$city_matrix);

        //let the algorithm choose wther equal matrices should be reverted or not(no human interactions please :)) 

        $make_change =(boolean)mt_rand(0, 1);

        //revert change if the sum was not improved or if it was equal and the algorithm wants to revert it.
        if($new_sum > $inital_sum || ($new_sum == $inital_sum && $make_change)){

            $temp = $init_route[$position1]; //keep city number
            
            //swap city locations in an attempt to improve the result
            $init_route[$position1] = $init_route[$position2];
            $init_route[$position2] = $temp;
        
        }
    }

    return $init_route;
}

/*
Calculates and return the total distance of the route provided
*/

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

function matrix_generator($size){
    $city_matrix =[$size][$size];


    for($i = 0; $i < $size; $i++){
        for($j = 0; $j < $size; $j++){
                $city_matrix[$i][$j] = NULL;
        }
    }

    for($i = 0; $i < $size; $i++){
        for($j = 0; $j < $size; $j++){
            if($i == $j){
                $city_matrix[$i][$j] = 0;
            }

            else if(is_null($city_matrix[$i][$j])){
                $city_matrix[$i][$j] = mt_rand(1, 100);
                $city_matrix[$j][$i] = $city_matrix[$i][$j]; //also the mirrored coordinates must be the same
            }
        }
    }

    return $city_matrix;
}
?>
