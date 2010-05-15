<?php 

    /* Вместо списка действий контроллера пользователей выводим "Моё меню" */
    include ELEMENTS . DS . 
            'menus'  . DS . 
            'my_menu.' . Mvc_View::DEFAULT_TPL_EXTENSION;
    
?>