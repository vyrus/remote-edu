<?php
    
    $result = array('query'       => $this->query,
                    'suggestions' => array());
    
    foreach ($this->cities as $city) {
        $name = Model_Locality::expandName($city['name'], $city['type']);
        
        $result['suggestions'][] = $name;
        $result['data'][] = $city['id'];
    }
    
    echo json_encode($result);

?>