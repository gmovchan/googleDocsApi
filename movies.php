<?php
/*
if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}
*/
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/app/models/googleApiConnect.php';

// Prints the names and majors of students in a sample spreadsheet:
// https://docs.google.com/spreadsheets/d/1n2Y_m3dMtga_MSbrD9mTj6RQHwbCzEPr44Q91EHnF0Y/edit
$spreadsheetId = '1n2Y_m3dMtga_MSbrD9mTj6RQHwbCzEPr44Q91EHnF0Y';

$clientSecretPath = __DIR__ . '/client_secret.json';
$credentialsPath = __DIR__ . '/credentials/sheets.googleapis.com-php-movies.json';

$googleApiConnect = new googleApiConnect('movie', $spreadsheetId, $clientSecretPath, $credentialsPath);

$movies = $googleApiConnect->getRows('Watched movies!B5:K');

$moviesForViews = array();

if (count($movies) == 0) {
    print "No data found.\n";
} else {
    foreach ($movies as $row) {
        // оценка фильма
        if (!empty($row[4])) {
            $stars = (int) $row[4];
        } else {
            $stars = 0;
        }

        $name = $row[0];
        if (isset($row[5])) {
            $episode = $row[5];
        }
        
        // добавляет эпизод к названию
        if (!empty($episode)) {
            $name = $name . " ($episode)";
        }
        
        $moviesForViews[] = array(
            'name' => $name,
            'stars' => $stars,
            );
    }
}

require_once __DIR__ . '/app/views/moviesView.php';