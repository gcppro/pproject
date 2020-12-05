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
            // 모집 시작일이 모집 마감일보다 날짜가 늦으면 안됨. 그거에 대한 유효성 검사 해야됨.
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
                $challenge_id = setChallenge($member_id, $req->goal, $req->recruitment_start_date, $req->recruitment_end_date, $req->period, $req->amount, $req->image_url, $req->description);
                setChallengeParticipation($member_id, $challenge_id);
                updateMemberMoney(getChallengeAmount($challenge_id), $member_id);
                $res->is_success = TRUE;
                $res->code = 200;
                $res->message = "챌린지 생성 성공했습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;

        case 'getChallengeDetail':
            if(!isExistChallenge($vars["id"])){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "존재하지 않는 챌린지입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                $res->result->id = getChallengeDetail($vars["id"])[0]["id"];
                $res->result->goal = getChallengeDetail($vars["id"])[0]["goal"];
                $res->result->start_date = getChallengeDetail($vars["id"])[0]["start_date"];
                $res->result->end_date = getChallengeDetail($vars["id"])[0]["end_date"];
                $res->result->period = getChallengeDetail($vars["id"])[0]["period"];
                $res->result->amount = getChallengeDetail($vars["id"])[0]["amount"];
                $res->result->nick_name = getChallengeDetail($vars["id"])[0]["nick_name"];
                $res->result->image_url = getChallengeDetail($vars["id"])[0]["image_url"];
                $res->result->members = getChallengeMembers($vars["id"]);
                $res->is_success = TRUE;
                $res->code = 200;
                $res->message = "챌린지 세부 정보 조회 성공했습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;

        case 'setChallengeParticipation':
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            else if(!isExistChallenge($req->challenge_id)){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "존재하지 않는 챌린지입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                 $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                $account_id = $userInfo->id;
                $member_id = getMemberId($account_id);
                if(getUserMoney($member_id) < getChallengeAmount($req->challenge_id)){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "돈이 부족합니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
                }
                else{
                setChallengeParticipation($member_id, $req->challenge_id);
                updateMemberMoney(getChallengeAmount($req->challenge_id), $member_id);
                $res->is_success = TRUE;
                $res->code = 200;
                $res->message = "챌린지 참여 되었습니다!";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
                }
             }
                break;

            case 'getChallengeCertification':
                if(!isExistChallenge($_GET["challenge_id"])){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "존재하지 않는 챌린지입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
                }

            else{
                $res->result = getChallengeCertification($_GET["challenge_id"], "11월 29일"); // 여기 날짜부분 입력값 수정. 정규식 써서 유효성 검사
                $res->is_success = TRUE;
                $res->code = 200;
                $res->message = "챌린지 인증하기 창 조회 성공했습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;

        case 'setChallengeCertification':
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            elseif(!isExistChallenge($req->challenge_id)){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "존재하지 않는 챌린지입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            elseif (empty($req->image_url)) {
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "사진이 없습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            elseif(mb_strlen($req->description) < 5){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "설명이 너무 부족합니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                $account_id = $userInfo->id;
                $member_id = getMemberId($account_id);
                $challenge_participation_id = getChallengeParticipationId($req->challenge_id, $member_id);

                $res->result = setChallengeCertification($challenge_participation_id, $req->image_url, $req->description);
                $res->is_success = TRUE;
                $res->code = 200;
                $res->message = "챌린지 인증 성공했습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;

        case 'getChallengeCertificationDetail':
            if(!isExistChallengeCertification($vars["id"])){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "존재하지 않는 인증글입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                $res->result = getChallengeCertificationDetail($vars["id"]);
                $res->is_success = TRUE;
                $res->code = 200;
                $res->message = "인증글 세부정보 조회 성공했습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
                break;
            }

        case 'updateChallengeCertification':
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "유효하지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                $res->result = TRUE;
                $res->is_success = TRUE;
                $res->code = 200;
                $res->message = "";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            break;

        case 'deleteChallengeCertification':
            if(true){
                $res->is_success = FALSE;
                $res->code = 400;
                $res->message = "";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                $res->result = TRUE;
                $res->is_success = TRUE;
                $res->code = 200;
                $res->message = "";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
