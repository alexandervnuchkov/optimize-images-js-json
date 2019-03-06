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

    if(isset($_POST['submitOO'])) {
        $helpVar = 'onlyoffice';
        buildJS($helpVar);
    } else if(isset($_POST['submitR7'])) {
        $helpVar = 'r7';
        buildJS($helpVar);
    }


    function buildJS($helpVar){
        $i = 0;
        $red_all = 0;
        $perc_all = 0;
        $in_all = 0;
        $out_all = 0;
        $src_file_list1 = array();
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('files/js/' . $helpVar . '/src'), RecursiveIteratorIterator::SELF_FIRST);
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

        if($helpVar == 'onlyoffice') {
            $uglify = new Uglify(array(
                'files/js/onlyoffice/out/jquery/jquery.min.js',
                'files/js/onlyoffice/out/jquery/jquery-migrate.min.js',
                'files/js/onlyoffice/out/core/main.js',
                'files/js/onlyoffice/out/help/menuselector.js',
                'files/js/onlyoffice/out/help/toggler.js',
                'files/js/onlyoffice/out/highlight/highlight.pack.js',
                'files/js/onlyoffice/out/jquery/jquery.actual.min.js',
                'files/js/onlyoffice/out/jquery/jquery.mousewheel.js',
                'files/js/onlyoffice/out/jquery/mwheelIntent.js',
                'files/js/onlyoffice/out/jquery/jquery.jscrollpane.min.js',
                'files/js/onlyoffice/out/help/sitemap.sort.js',
                'files/js/onlyoffice/out/jquery/jquery.blockUI.js',
                'files/js/onlyoffice/out/jquery/jquery.tmpl.js',
                'files/js/onlyoffice/out/core/menu.js',
                'files/js/onlyoffice/out/core/language-selector.js',
                'files/js/onlyoffice/out/jquery/jquery.watermarkinput.js',
                'files/js/onlyoffice/out/core/basemaster.init.js',
                'files/js/onlyoffice/out/core/navigation-menu.js',
                'files/js/onlyoffice/out/core/modalscontroller.js',
                'files/js/onlyoffice/out/core/jquery.dropdownToggle.js',
                'files/js/onlyoffice/out/jquery/pushy.min.js',
                'files/js/onlyoffice/out/help/search.enter.js',
                'files/js/onlyoffice/out/builder/prettify/pre.prettyprint.js',
                'files/js/onlyoffice/out/builder/prettify/prettify.js',
                'files/js/onlyoffice/out/help/toggle.version.js',
                'files/js/onlyoffice/out/help/editor.tables.js',
                'files/js/onlyoffice/out/help/language_table.js',
                'files/js/onlyoffice/out/help/table.sorter.js',
                'files/js/onlyoffice/out/help/language_table_builder.js',
                'files/js/onlyoffice/out/help/outofdate.notice.js',
                'files/js/onlyoffice/out/help/expand.menu.js',
                //'files/js/onlyoffice/out/help/holiday_greetings.js',
                'files/js/onlyoffice/out/help/cookie_notice.js',
                //'files/js/onlyoffice/out/help/server_commands.js'
            ));
            $uglify->write('files/js/onlyoffice/scripts.js');
        } else if ($helpVar == 'r7') {
            $uglify = new Uglify(array(
                'files/js/r7/out/jquery/jquery.min.js',
                'files/js/r7/out/jquery/jquery-migrate.min.js',
                'files/js/r7/out/core/main.js',
                'files/js/r7/out/help/menuselector.js',
                'files/js/r7/out/help/toggler.js',
                'files/js/r7/out/highlight/highlight.pack.js',
                'files/js/r7/out/jquery/jquery.actual.min.js',
                'files/js/r7/out/jquery/jquery.mousewheel.js',
                'files/js/r7/out/jquery/mwheelIntent.js',
                'files/js/r7/out/jquery/jquery.jscrollpane.min.js',
                'files/js/r7/out/jquery/jquery.blockUI.js',
                'files/js/r7/out/jquery/jquery.tmpl.js',
                'files/js/r7/out/core/menu.js',
                'files/js/r7/out/jquery/jquery.watermarkinput.js',
                'files/js/r7/out/core/navigation-menu.js',
                'files/js/r7/out/core/modalscontroller.js',
                'files/js/r7/out/core/jquery.dropdownToggle.js',
                'files/js/r7/out/jquery/pushy.min.js',
                'files/js/r7/out/builder/prettify/pre.prettyprint.js',
                'files/js/r7/out/builder/prettify/prettify.js',
                'files/js/r7/out/help/toggle.version.js',
                'files/js/r7/out/help/editor.tables.js',
                'files/js/r7/out/help/expand.menu.js',
                'files/js/r7/out/help/cookie_notice.js',
                'files/js/r7/out/help/scrollanchor.js',
            ));
            $uglify->write('files/js/r7/scripts.js');
        }
        
        echo '<p>The resulting \'scripts.js\' file size: ' . number_format(round(filesize('files/js/' . $helpVar . '/scripts.js') / 1024, 2), 2, '.', '\'') . ' KB.</p>';
    }

    ?>
    <?php
    echo '<div class="formDiv">
        <form method="post" name="helpSelector">
            <input name="submitOO" type="submit" value="ONLYOFFICE">
            <input name="submitR7" type="submit" value="R7-Office">
        </form>
        </div>';
    ?>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="js/arrowup.min.js"></script>
</body>
</html>