<?php
require __DIR__ . '/Models/Csv.php';

try {
    $file = __DIR__ . '/csv/weather_statistics.csv';

    $csv = new Csv($file);

    $data = [
        $statByDays = $csv->getStatByDays(),
        $statByWeeks = $csv->getStatByWeeks(),
        $getStatByMonth = $csv->getStatByMonth(),
    ];

    for ($i = 0; $i < count($data); $i++) {
        $csv->showStat($data[$i], Csv::CAPTIONS[$i], Csv::TITLES[$i]);
    }

} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
