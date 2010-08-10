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

    $nor = process_dir(ROOT, $_process_exts);

    header('Content-Type: text/plain; charset=utf-8');
    print_r($nor);

    function process_dir($path, array $exts) {
        global $_skip_entries;

        $nor = array();

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
                $tmp_nor = process_dir($full_path, $exts);

                if (1 == sizeof($tmp_nor) && 0 == $tmp_nor['total']) {
                    continue;
                }

                $key = strip_base_dir($full_path);
                $nor[$key] = $tmp_nor;
                $nor['total'] += $tmp_nor['total'];

                continue;
            }

            $parts = explode('.', $entry);
            $ext = array_pop($parts);

            if (in_array($ext, $exts)) {
                $nr_replacements = convert_tabs($full_path);

                if (null !== $nr_replacements) {
                    $key = strip_base_dir($full_path);
                    $nor[$key] = $nr_replacements;
                }
            }
        }

        $nor['total'] = array_sum($nor);

        return $nor;
    }

    function convert_tabs($file_path) {
        $contents = file_get_contents($file_path);;

        if (!strstr($contents, "\t")) {
            return null;
        }

        $contents = preg_replace("/\t/", str_repeat(' ', 2),
                                 $contents, -1, $count);
        file_put_contents($file_path, $contents);

        return $count;
    }

    function strip_base_dir($path) {
        $base_len = strlen(ROOT);
        $path = substr($path, $base_len);

        return $path;
    }

?>