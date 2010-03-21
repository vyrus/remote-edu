<?php
    
    define('DS',   DIRECTORY_SEPARATOR);
    define('ROOT', dirname(__FILE__));

    $_skip_entries = array(
        '.', '..',
        
        ROOT . DS . '.git',
        ROOT . DS . 'tests',
        ROOT . DS . 'lines-of-code.php',
        
        ROOT . DS . 'private' . DS . 'third_party'
    );
    
    $_process_exts = array('php', 'tpl');
    
    $loc = process_dir(ROOT, $_process_exts);
    
    header('Content-Type: text/plain; charset=utf-8');
    print_r($loc);
    
    function process_dir($path, array $exts) {
        global $_skip_entries;
        
        $loc = array('total' => 0);
        
        $dir = dir($path);
        
        while (false !== ($entry = $dir->read()))
        {
            if (in_array($entry, $_skip_entries)) {
                continue;
            }
            
            $full_path = $path . DS . $entry;
            
            if (in_array($full_path, $_skip_entries)) {
                continue;
            }
                
            if (is_dir($full_path)) {
                $tmp_loc = process_dir($full_path, $exts);
                
                $key = strip_base_dir($full_path);
                $loc[$key] = $tmp_loc;
                $loc['total'] += $tmp_loc['total'];
                
                continue;
            }
                
            $parts = explode('.', $entry);
            $ext = array_pop($parts);
            
            if (in_array($ext, $exts)) {
                $nr_lines = get_nr_lines($full_path);
                
                $key = strip_base_dir($full_path);
                $loc[$key] = $nr_lines;
            }
        }
        
        $loc['total'] = array_sum($loc);
        
        return $loc;
    }
    
    function get_nr_lines($file_path) {
        $nr_lines = 0;
        
        $fp = fopen($file_path, 'r');
        
        while (!feof($fp))
        {
            if (fgets($fp)) {
                $nr_lines++;
            }
        }
        
        return $nr_lines;
    }
    
    function strip_base_dir($path) {
        $base_len = strlen(ROOT);
        $path = substr($path, $base_len);
        
        return $path;
    }
    
?>