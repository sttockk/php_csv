<?php

require_once __DIR__ . "/../Services/DataBase.php";

class Csv
{
    const CSV = "csv";
    const CAPTIONS = ["по дням", "по неделям", "по месяцам"];
    const TITLES = ["Дата", "Неделя", "Месяц"];
    public function __construct(private ?string $csvFile = null)
    {
        if (!file_exists($csvFile)) {
            throw new Exception("Файл {$csvFile} не найден");
        }
        $this->db = DataBase::getInstance();
        $this->insertRows($this->getCsv());
    }

    public function getCsv()
    {
        $handle = fopen($this->csvFile, "r");
        $arr = [];

        fgetcsv($handle, 0, ';');

        while (($row = fgetcsv($handle, 0, ";")) !== FALSE) {
            $arr[] = [
                "date" => date("Y-m-d", strtotime(strstr($row[0], ' ', true))),
                "temperature" => (float)$row[1]
            ];
        }
        fclose($handle);

        return $arr;
    }

    public function insertRow($params): array
    {
        $query = "INSERT INTO `" . self::CSV . "` (`date`,`temperature`) VALUES (:date,:temperature)";

        return $this->db->query($query, $params);
    }


    public function insertRows($csv): void
    {
        foreach ($csv as $item) {
            $params = [
                ':date' => $item["date"],
                ':temperature' => $item["temperature"],
            ];
            $this->insertRow($params);
        }
    }

    public function showStat($stat, $caption, $date): void
    {
        echo "<table border='1' style='float: left;display: inline-block;margin-left: 10px'>
           <caption>$caption</caption>
           <tr>
            <th>$date</th>
            <th>Средняя температура</th>
           </tr>";
        foreach ($stat as $item) {
            echo "<tr><td>{$item['date']}</td><td>{$item['avg_temp']}</td></tr>";
        }
        echo "</table>";
    }

    public function getStatByDays(): array
    {
        $query = "SELECT date, ROUND(AVG(temperature), 1) as avg_temp FROM  `" . self::CSV . "` GROUP BY date";

        return $this->db->query($query);
    }

    public function getStatByWeeks(): array
    {
        $query = "SELECT WEEK(date, 7) as date, ROUND(AVG(temperature), 1) as avg_temp FROM `" . self::CSV . "` GROUP BY WEEK(date, 7)";

        return $this->db->query($query);
    }

    public function getStatByMonth(): array
    {
        $query = "SELECT MONTH(date) as date, ROUND(AVG(temperature), 1) as avg_temp FROM `" . self::CSV . "` GROUP BY MONTH(date)";

        return $this->db->query($query);
    }
}

