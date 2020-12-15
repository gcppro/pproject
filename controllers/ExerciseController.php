<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case 'getExercises':
            $res->result = getExercises();
            $res->is_success = TRUE;
            $res->code = 200;
            $res->message = "운동 리스트 조회 성공했습니다.";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        case 'getExercisesDetail':
            if(!isExistExercise($vars["id"])){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "존재하지 않는 운동자세입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                $res->result = getExercisesDetail($vars["id"]);
                $res->is_success = TRUE;
                $res->code = 200;
                $res->message = "운동 상세 조회 성공했습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;


        case 'getMypageUser':
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                $account_id = $userInfo->id;
                $member_id = getMemberId($account_id);
                $res->result = getUserInfo($member_id);
                $res->is_success = TRUE;
                $res->code = 200;
                $res->message = "유저 조회 성공했습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;

        case 'setExerciseCount':
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                $account_id = $userInfo->id;
                $member_id = getMemberId($account_id);
                setExerciseCount($req->exercise_id, $member_id, $req->count);
                $res->is_success = TRUE;
                $res->code = 200;
                $res->message = "운동 카운트 저장되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
