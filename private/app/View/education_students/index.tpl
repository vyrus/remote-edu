<h2>Список студентов</h2>
<ul>
<?php
/*    echo '<pre>';
    print_r($this->student_list);
    echo '</pre>';*/
    foreach ($this->student_list as $student) {
        echo '<li>' . $student['login'];
    } 
?>
</ul>