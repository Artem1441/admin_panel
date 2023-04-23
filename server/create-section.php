<?php
include('db.php');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$req = file_get_contents("php://input");
$req = json_decode($req, true);

// CreateSection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isExistTable = IsExistTable("sections");
    if (!$isExistTable) CreateTable("sections", ["page_name_en" => "varchar(255) NOT NULL", "section_name" => "varchar(255) NOT NULL", "section_name_en" => "varchar(255) NOT NULL", "type" => "varchar(255) NOT NULL", "inputs" => "JSON", "data" => "JSON"]);

    $page_name_en = $req["page_name_en"];
    $section_name = $req["section_name"];
    $section_name_en = $req["section_name_en"];
    $type = $req["type"];
    $inputs = $req["inputs"];

    if ($section_name == "" || $section_name_en == "") {
        echo json_encode(["status" => false, "message" => "Название раздела не может быть пустым", "result" => null], JSON_UNESCAPED_UNICODE);
        return;
    }

    $matchingResultsName = SearchOne("sections", ["page_name_en" => $page_name_en, "section_name" => $section_name]);
    $matchingResultsNameEn = SearchOne("sections", ["page_name_en" => $page_name_en, "section_name_en" => $section_name_en]);
    if ($matchingResultsName) {
        echo json_encode(["status" => false, "message" => "Такой раздел уже существует. '" . $section_name . "' использовать нельзя", "result" => null], JSON_UNESCAPED_UNICODE);
        return;
    };
    if ($matchingResultsNameEn) {
        echo json_encode(["status" => false, "message" => "Такой раздел уже существует. '" . $section_name_en . "' использовать нельзя", "result" => null], JSON_UNESCAPED_UNICODE);
        return;
    }

    $data;
    if ($type == "array") {
        $data = array((object) []);
        foreach ($inputs as $input) {
            $value;
            if ($input["type"] == "text") $value = "";
            if ($input["type"] == "number") $value = "";
            if ($input["type"] == "img") $value = ["src" => "", "alt" => ""];
            $data[0]->{$input["titleEn"]} = $value;
        }
    } else { // object only
        $data = (object) [];
        foreach ($inputs as $input) {
            $value;
            if ($input["type"] == "text") $value = "";
            if ($input["type"] == "number") $value = "";
            if ($input["type"] == "img") $value = ["src" => "", "alt" => ""];
            $data->{$input["titleEn"]} = $value;
        }
    }
    Insert("sections", ["page_name_en" => $page_name_en, "section_name" => $section_name, "section_name_en" => $section_name_en, "type" => $type, "inputs" => json_encode($inputs, JSON_UNESCAPED_UNICODE), "data" => json_encode($data, JSON_UNESCAPED_UNICODE)]);

    echo json_encode(["status" => true, "message" => null, "result" => null], JSON_UNESCAPED_UNICODE);
}
