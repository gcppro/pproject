<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case 'getChallenge':
            $res->result = getChallenge();
            $res->is_success = TRUE;
            $res->code = 200;
            $res->message = "챌린지 리스트 조회 성공했습니다.";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case 'setChallenge':
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            elseif(mb_strlen($req->goal) < 5 OR empty($req->goal)){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "목표를 더 길게 작성해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            elseif(!preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $req->recruitment_start_date)){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "모집 시작일 날짜 형식이 잘못 됐습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            elseif(!preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $req->recruitment_end_date)){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "모집 마감일 날짜 형식이 잘못 됐습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            elseif($req->period < 1 OR $req->period > 31){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "챌린지 기간은 1일부터 30일까지만 가능합니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            elseif($req->amount < 1000 OR $req->amount > 100001){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "금액의 최소한도는 천원 최대한도는 십만원입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            elseif(empty($req->image_url)){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "사진이 없습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            elseif(mb_strlen($req->description) < 10 OR empty($req->description)){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "설명이 너무 짧습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                $account_id = $userInfo->id;
                $member_id = getMemberId($account_id);
                $res->result = setChallenge($member_id, $req->goal, $req->recruitment_start_date, $req->recruitment_end_date, $req->period, $req->amount, $req->image_url, $req->description);
                $res->is_success = TRUE;
                $res->code = 200;
                $res->message = "챌린지 생성 성공했습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;

        case 'getChallengeDetail':
            if(true){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "존재하지 않는 게시판입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                $res->result = getPosts($_GET["bulletin_board_id"]);
                $res->is_success = TRUE;
                $res->code = 200;
                $res->message = "게시글 리스트 조회 성공했습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;

        case 'setChallengeParticipation':
            if(true){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "존재하지 않는 게시판입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                $res->result = getPosts($_GET["bulletin_board_id"]);
                $res->is_success = TRUE;
                $res->code = 200;
                $res->message = "게시글 리스트 조회 성공했습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
