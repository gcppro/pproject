<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {

        case "signUp":

            $pw = $req->pw;
            $id = $req->id;
            $nickName = $req->nickName;
//            $name = $req->name;
            $age = $req->age;
            $height = $req->height;
            $weight = $req->weight;
//            $check_email = filter_var($id, FILTER_VALIDATE_EMAIL);
            $check_pw = '/^.*(?=^.{8,15}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[!*@#$%^&+=]).*$/';
            $check_id = '/^[0-9a-z]{4,9}$/';

            if (empty($pw) || empty($id) || empty($nickName) || empty($age)
                || empty($height) || empty($weight)) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "공백이 입력됐습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else
                if (isValidUser($id) == 1) {
                    $res->isSucces = FALSE;
                    $res->code = 100;
                    $res->message = "이미 가입된 id 입니다.";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                } else if (!preg_match($check_pw, "$pw")) {
                    $res->isSucces = FALSE;
                    $res->code = 100;
                    $res->message = "영어 소문자, 숫자, 특수문자를 포함하여 8-15자로 비밀번호를 생성하세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                } else {
                    $hash = password_hash($pw, PASSWORD_DEFAULT);
                    signUp($id, $hash, $age, $nickName, $height, $weight);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "회원 가입 성공!";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
            $res->isSucces = FALSE;
            $res->code = 100;
            $res->message = "회원가입에 실패하였습니다. 다시 시도해주세요";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            return;


        case "login":
            $id = $req->id;
            $pw = $req->pw;
            $conn = mysqli_connect('pproject.ccpq5m4joejt.ap-northeast-2.rds.amazonaws.com', 'admin', 'ppro2020', 'pproject');
            mysqli_set_charset($conn, "utf8");
            $sql = "select password from member where account_id = '$id'";
            $resp = mysqli_query($conn, $sql);
            $row = mysqli_fetch_array($resp);
            $hash = $row['password'];

            if (empty($id) || empty($pw)) {
                $res->isSucces = FALSE;
                $res->code = 00;
                $res->message = "공백이 입력됐습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                if (!isValidUser($id)) {
                    $res->isSuccess = FALSE;
                    $res->code = 100;
                    $res->message = "유효하지 않은 아이디 입니다.";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                } else {
                    if (password_verify($pw, $hash)) {
                        $jwt = getJWToken($id, $hash, JWT_SECRET_KEY);
                        $res->result["jwt"] = $jwt;
                        $res->isSuccess = TRUE;
                        $res->code = 100;
                        $res->message = "로그인 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        break;
                    } else {
                        $res->isSuccess = FALSE;
                        $res->code = 100;
                        $res->message = "비밀번호가 일치하지 않습니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        break;
                    }
                }
            }

        case "selectUsers":
            http_response_code(200);
            $res->result = selectUsers();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;




    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
