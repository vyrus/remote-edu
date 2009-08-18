<?php
    
    $result = array('query' => $this->query);
    
    foreach ($this->regions as $region) {
        $result['suggestions'][] = $region['name'];
        $result['data'][] = $region['id'];
    }
    
    echo json_encode($result);

?>