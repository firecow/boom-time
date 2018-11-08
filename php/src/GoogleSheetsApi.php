<?php

namespace App;

use App\DAL\SQL;

class GoogleSheetsApi {

    public static function UpdateFromSheet(SQL $sql, string $sheetData, string $charName) {

        foreach(explode("\n", $sheetData) as $line) {
            $column = explode(",", $line);
            if ($column[3] === "$charName" && !strpos($line, "Placeholder")) {
                $signAttendance = $column[10] === "Inactive" ? -1 : (int)str_replace("%", "", $column[10]);
                $attendance = $column[11] === "Inactive" ? -1 : (int)str_replace("%", "", $column[11]);
                $raidsAttended = $column[12];
                $sql->raw("UPDATE characters SET attendance = $attendance, signAttendance = $signAttendance, raidsAttended=$raidsAttended WHERE charName = '$charName'");
            }
        }
    }

}