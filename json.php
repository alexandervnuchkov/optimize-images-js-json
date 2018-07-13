<?php
    ini_set('max_execution_time', 0);
    error_reporting(E_ERROR);
    ini_set("display_errors", 1);
    set_time_limit(0);
?>
<html>
<head>
    <title>Optimize JSON</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:900,800,700,600,500,400,300&amp;subset=latin,cyrillic-ext,cyrillic,latin-ext" rel="stylesheet" type="text/css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <p id="back-top" style="display: none">
        <a title="Scroll up" href="#top"></a>
    </p>
    <h1>Optimize JSON</h1>
<?php

    $i = 0;
    $red_all = 0;
    $perc_all = 0;
    $in_all = 0;
    $out_all = 0;
    $src_file_list1 = array();
    $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('files/json/src'), RecursiveIteratorIterator::SELF_FIRST);
    foreach($objects as $name => $object){
        if(is_dir($name)==1){
            $dest_name = str_replace('src', 'out', $name);
            mkdir($dest_name);
        }
        if(pathinfo($name, PATHINFO_EXTENSION) == 'json'){
            array_push($src_file_list1, $name);
        }
    }
    echo '<table><thead><tr><th>#</th><th>File name</th><th>Source size (KB)</th><th>Output size (KB)</th><th>Reduction (KB)</th><th>Reduction (%)</th></tr></thead><tbody>';
    foreach($src_file_list1 as $file_path){
        $data = file_get_contents($file_path);
        $json = json_decode($data, true);
        file_put_contents(str_replace('src', 'out', $file_path), json_encode($json));
        $in_file_size = round(filesize($file_path) / 1024, 2);
        $out_file_size = round(filesize(str_replace('src', 'out', $file_path)) / 1024, 2);
        $percent = 1 - round($out_file_size/$in_file_size, 2);
        $reduction = $out_file_size - $in_file_size;
        $number = $i+1;
        echo '<tr><td>' . $number . '</td><td>' . basename($file_path) . '</td><td>' . number_format($in_file_size, 2, '.', '\'') . '</td><td>' . number_format($out_file_size, 2, '.', '\'') . '</td><td>' . number_format($reduction, 2, '.', '\'') . '</td><td>' . number_format($percent, 2, '.', '\'') . '</td></tr>';
        $i+=1;
        $red_all += $reduction;
        $perc_all += $percent;
        $in_all += $in_file_size;
        $out_all += $out_file_size;
    }
    $red_average = round($red_all/$i, 2);
    $perc_average = round($perc_all/$i, 2);
    echo '<tr><td>Total: </td><td></td><td>' . number_format($in_all, 2, '.', '\'') . '</td><td>' . number_format($out_all, 2, '.', '\'') . '</td><td>' . number_format($red_all, 2, '.', '\'') . '</td><td>' . number_format($perc_average, 2, '.', '\'') . '</td></tr></tbody></table>';
?>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="js/arrowup.min.js"></script>
</body>
</html>