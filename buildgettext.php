<?php

include 'lang/English/language.php';

function listdir($dir='.') {
    if (!is_dir($dir)) {
        return false;
    }
   
    $files = array();
    listdiraux($dir, $files);

    return $files;
}

function listdiraux($dir, &$files) {
    $handle = opendir($dir);
    while (($file = readdir($handle)) !== false) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        $filepath = $dir == '.' ? $file : $dir . '/' . $file;
        if (is_link($filepath))
            continue;
        if (is_file($filepath) && false !== strpos( $file, '.php' ))
            $files[] = $filepath;
        else if (is_dir($filepath))
            listdiraux($filepath, $files);
    }
    closedir($handle);
}

$files = listdir('.');
sort($files, SORT_LOCALE_STRING);

foreach ($files as $f) {
	$file = file_get_contents( $f );
	preg_match_all( '/\$lang\[\'(.*?)\'\]/i', $file, $matches );
	foreach ( $matches[1] as $i => $slug ) {
		$file = str_replace( $matches[0][ $i ], "__('{$lang[ $slug ]}', 'luna')", $file );
	}
	file_put_contents( $f, $file );
} 