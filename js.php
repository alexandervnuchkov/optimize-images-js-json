<?php
    ini_set('max_execution_time', 0);
    error_reporting(E_ERROR);
    ini_set("display_errors", 1);
    set_time_limit(0);
    require __DIR__ . '/vendor/autoload.php';
    
    use Patchwork\JSqueeze;
    use NodejsPhpFallback\Uglify;
?>
<html>
<head>
    <title>Optimize JS</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:900,800,700,600,500,400,300&amp;subset=latin,cyrillic-ext,cyrillic,latin-ext" rel="stylesheet" type="text/css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <p id="back-top" style="display: none">
        <a title="Scroll up" href="#top"></a>
    </p>
    <h1>Optimize JS</h1>
<?php

    $i = 0;
    $red_all = 0;
    $perc_all = 0;
    $in_all = 0;
    $out_all = 0;
    $src_file_list1 = array();
    $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('files/js/src'), RecursiveIteratorIterator::SELF_FIRST);
    foreach($objects as $name => $object){
        if(is_dir($name)==1){
            $dest_name = str_replace('src', 'out', $name);
            mkdir($dest_name);
        }
        if(pathinfo($name, PATHINFO_EXTENSION) == 'js'){
            array_push($src_file_list1, $name);
        }
    }

    $jz = new JSqueeze();
    
    echo '<table><thead><tr><th>#</th><th>File name</th><th>Source size (KB)</th><th>Output size (KB)</th><th>Reduction (KB)</th><th>Reduction (%)</th></tr></thead><tbody>';

    foreach($src_file_list1 as $file_path){
        $data = file_get_contents($file_path);
        
        $minifiedJs = $jz->squeeze($data,
            true,   // $singleLine
            true,   // $keepImportantComments
            false   // $specialVarRx
        );

        file_put_contents(str_replace('src', 'out', $file_path), $minifiedJs);
        
        $in_file_size = round(filesize($file_path) / 1024, 2);
        $out_file_size = round(filesize(str_replace('src', 'out', $file_path)) / 1024, 2);
        $percent = (1 - round($out_file_size/$in_file_size, 2))*100;
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

    $uglify = new Uglify(array(
        'files/js/out/jquery/jquery.min.js',
        'files/js/out/jquery/jquery-migrate.min.js',
        'files/js/out/core/main.js',
        'files/js/out/help/menuselector.js',
        'files/js/out/help/toggler.js',
        'files/js/out/highlight/highlight.pack.js',
        'files/js/out/jquery/jquery.actual.min.js',
        'files/js/out/jquery/jquery.mousewheel.js',
        'files/js/out/jquery/mwheelIntent.js',
        'files/js/out/jquery/jquery.jscrollpane.min.js',
        'files/js/out/help/sitemap.sort.js',
        'files/js/out/jquery/jquery.blockUI.js',
        'files/js/out/jquery/jquery.tmpl.js',
        'files/js/out/core/menu.js',
        'files/js/out/core/language-selector.js',
        'files/js/out/jquery/jquery.watermarkinput.js',
        'files/js/out/core/basemaster.init.js',
        'files/js/out/core/navigation-menu.js',
        'files/js/out/core/modalscontroller.js',
        'files/js/out/core/jquery.dropdownToggle.js',
        'files/js/out/jquery/pushy.min.js',
        'files/js/out/help/search.enter.js',
        'files/js/out/builder/prettify/pre.prettyprint.js',
        'files/js/out/builder/prettify/prettify.js',
        'files/js/out/help/toggle.version.js',
        'files/js/out/help/editor.tables.js',
        'files/js/out/help/language_table.js',
        'files/js/out/help/table.sorter.js',
        'files/js/out/help/language_table_builder.js',
        'files/js/out/help/outofdate.notice.js',
        'files/js/out/help/expand.menu.js',
        'files/js/out/help/holiday_greetings.js',
        'files/js/out/help/cookie_notice.js',
        //'files/js/out/help/server_commands.js'
    ));
    $uglify->write('files/js/scripts.js');
    
    echo '<p class="formDiv">The resulting \'scripts.js\' file size: ' . number_format(round(filesize('files/js/scripts.js') / 1024, 2), 2, '.', '\'') . ' KB.</p>';
    
    ?>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="js/arrowup.min.js"></script>
</body>
</html>