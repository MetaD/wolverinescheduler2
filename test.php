<!DOCTYPE html>
<html>
<body>

<h1> PHP page for algorithm </h1>

<?php echo 'hello world';
echo "<br>"; //new line
?>

<?php

class Lecture{
    public $coursename;
    public $start_time;
    public $end_time;
    public $days;
    
    function __construct() 
    { 
        $a = func_get_args(); 
        $i = func_num_args(); 
        if (method_exists($this,$f='__construct'.$i)) { 
            call_user_func_array(array($this,$f),$a); 
        } 
    } 
    
    function __construct3($a1,$a2,$a3){
        // echo 'new Lecture created ';
        $this->coursename = $a1;
        $this->start_time = $a2;
        $this->end_time = $a3;
    }
}

class Course{
    public $coursename;
    public $credit;
    public $lectures = array();
    
    function __construct() 
    { 
        $a = func_get_args(); 
        $i = func_num_args(); 
        if (method_exists($this,$f='__construct'.$i)) { 
            call_user_func_array(array($this,$f),$a); 
        } 
    } 
    
    function __construct2($a1,$a2){
        // echo 'new Course created ';
        $this->coursename = $a1;
        $this->credit = $a2;
    }
    
    public function add($L){
        array_push($this->lectures, $L);
        // echo "ADD"; var_dump($this->lectures); echo "<br>";
    }
}

// GLOBAL VARIABLES
$solutions = array(); //2D array
$schedule; //2D array

function absolute_val($x){
    if($x > 0) return $x;
    $zero = 0;
    return $zero - $x;
}

function find_course_combination(&$potential_courses, $curr_index, $credits, &$selected) {
    global $solutions;
    global $schedule;
    
    // echo "find_course_comb inputs are: "; var_dump($potential_courses); 
    //       echo "<br>"; //new line
    // echo "input credit is: "; echo $credits;     echo " abs: "; echo absolute_val($credits); echo "<br>";
    // echo "selected array is: "; var_dump($selected); echo "<br>";
    

    if( absolute_val($credits) < 2){
      array_push($solutions, $selected);
    //   echo "I FIND A SOLUTION!  "; var_dump($selected);
    //   echo "<br>"; //new line
      return;
    }
    
    if($credits < 0) return;
    
    for ($i = $curr_index + 1 ; $i < count($potential_courses); $i++) {
      $course = $potential_courses[$i];

    //   echo "now pushing: "; var_dump($course); echo "<br>"; //new line
      array_push($selected, $course);
      
      $remain_credit = $credits - $course->credit;
    //   echo "Remaining Credit! Fuck: "; echo $remain_credit; echo " Course->Credit: "; echo $course->credit; 
    //       echo "input credit is: "; echo $credits; echo "<br>";

      
      find_course_combination($potential_courses, $i, $remain_credit, $selected);
      array_pop($selected);
    }  //end for
}

function isOnSameDay($L1, $L2) {
    $L1_arr = str_split($L1);
    $L2_arr = str_split($L2);
    foreach ($L1_arr as &$c1) { // TODO horrible efficiency
        foreach $L1_arr as &$c2 {
            if ($c1 == $c2)
                return True;
        }
    }
}

function hasConflict($L1, $L2) {
    if(isOnSameDay($L1, $L2) and $L1->start_time >= $L2->start_time and $L1->start_time < $L2->end_time)
        return True;
    if(isOnSameDay($L1, $L2) and $L2->start_time >= $L1->start_time and $L2->start_time < $L1->end_time)
        return True;
  return False;
}



function hasConflictWithList($L, &$V){
  for ($i = 0 ; $i <= count($V); $i++) {
    if(hasConflict($L, $V[$i])) return True;
  }    
  return False;
}



function find_time(&$course_list, $curr_index, &$curr_result){
    global $solutions;
    global $schedule;
    
  if(count($course_list) == count($curr_result)) {
    array_push($schedule, $curr_result);  
    return;
  }
  
  for ($i = $curr_index+1 ; $i <= count($course_list); $i++) {
    $temp_course = $course_list[$i];
    for ($j = 0 ; $j <= count($temp_course->lectures); $j++) {
      if( hasConflictWithList($temp_course->lectures[$j], $curr_result) ) 
        continue;
      
      array_push($curr_result, $temp_course->lectures[$j]);
      find_time($course_list, $i, $curr_result);
      array_pop($curr_result);
    }
  }
}

function debug_solutions()
{
    echo "<br>";
    global $solutions;
    global $schedule;
    
    for($i=0; $i < count($solutions); $i++){
        $total_credit = 0;
        for($j=0; $j < count($solutions[$i]); $j++){
            $course = $solutions[$i][$j];
            echo $course->coursename; echo " ";
            $total_credit += $course->credit;
        }
        echo "total: "; echo $total_credit ; echo "<br>"; //new line
    }
}


//begin main
$c1 = new Course("EECS 280", 4);
$c2 = new Course("EECS 370", 4);
$c3 = new Course("MATH 412", 3);
$c4 = new Course("MATH 500", 3);

$L1 = new Lecture("EECS 280", 8, 9);
$L2 = new Lecture("EECS 280", 9, 10);

$c1->add($L1);
$c1->add($L2);
// var_dump($c1);
// var_dump($c1->lectures);

$L3 = new Lecture("EECS 370", 10, 11);
$L4 = new Lecture("EECS 370", 12, 15);
$c2->add($L3);
$c2->add($L4);

$L5 = new Lecture("MATH 412", 13, 15);
$L6 = new Lecture("MATH 412", 8, 11);
$c3->add($L5);
$c3->add($L6);

$L6 = new Lecture("MATH 500", 15, 16);
$L7 = new Lecture("MATH 500", 10, 12);
$L8 = new Lecture("MATH 500", 11, 14);
$c4->add($L6);
$c4->add($L7);
$c4->add($L8);


$allCourse = array($c1, $c2, $c3, $c4);
$temp_sol = array();


echo "update63"; echo "<br>"; //new line

find_course_combination($allCourse, -1, 12, $temp_sol);
// echo "THE SOLUTION IS: "; var_dump($solutions);
debug_solutions();

?>

</body>
</html>
