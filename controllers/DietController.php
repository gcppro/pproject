<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
$array = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
//$json = file_get_contents("php://input");
//$arrayData = (array)json_decode($json, true); //두번째 인수를 true 로 반환하면 배열이 아닌 객체로.
$arrayData = json_decode(file_get_contents("php://input"), true);

try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {

        case "setDiet":


            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];//사용자가 가지고 있는 토큰이 유효한지 확인하고
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $id = $data->id;

//            if($arrayData['type'] == null){
//                $res->isSuccess = FALSE;
//                $res->code = 201;
//                $res->message = "공백이 입력되었습니다";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                return;
//            }
            foreach ($arrayData as $foodList){
                $type = $foodList['type'];
                $gram = $foodList['gram'];
                $foodName = $foodList['foodName'];
                setDiet($foodName, $type, $gram, $id);
            }
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 200;
            $res->message = "식단 추가 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getDiet":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];//사용자가 가지고 있는 토큰이 유효한지 확인하고
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $id = $data->id;
            $date = $_GET["date"];

            if(empty($date)){
                $res->isSucces = FALSE;
                $res->code = 100;
                $res->message = "date 를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            }
            else {
                http_response_code(200);
                $res->result["totalNutrient"] = selectTotalMealNut($id, $date);
                $res->result["BreakfastResult"] = selectBMealList($id, $date);
                $res->result["BreakfastTotalNutrient"] = selectBMealNut($id, $date);
                $res->result["LunchResult"] = selectLMealList($id, $date);
                $res->result["LunchTotalNutrient"] = selectLMealNut($id, $date);
                $res->result["DinnerResult"] = selectDMealList($id, $date);
                $res->result["DinnerTotalNutrient"] = selectDMealNut($id, $date);
                $res->isSuccess = TRUE;
                $res->code = 200;
                $res->message = "식단 리스트 조회 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

        case "foodNutrientDetail":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];//사용자가 가지고 있는 토큰이 유효한지 확인하고
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->messag = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $id = $data->id;
            $foodNo = $vars["foodIdx"];

            if(empty($foodNo)){
                $res->isSucces = FALSE;
                $res->code = 100;
                $res->message = "foodIdx 를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (isValidFoodNo($id, $foodNo) == 0) {
                $res->isSucces = FALSE;
                $res->code = 101;
                $res->message = "존재하지 않는 식단 번호 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else {
                http_response_code(200);
                $res->result = selectFoodDetail($id, $foodNo);
                $res->isSuccess = TRUE;
                $res->code = 200;
                $res->message = "음식 별 영양소 상세 조회 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

        case "deleteDiet":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];//사용자가 가지고 있는 토큰이 유효한지 확인하고
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->messag = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $id = $data->id;
            $foodNo = $_GET["foodIdx"];

            if(empty($foodNo)){
                $res->isSucces = FALSE;
                $res->code = 100;
                $res->message = "foodIdx 를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (isValidFoodNo($id, $foodNo) == 0) {
                $res->isSucces = FALSE;
                $res->code = 101;
                $res->message = "존재하지 않는 번호입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (isDeletedFoodNo($id, $foodNo)) {
                $res->isSucces = FALSE;
                $res->code = 102;
                $res->message = "이미 삭제된 식단 번호입니다.";
               echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else {
                http_response_code(200);
                deleteDiet($foodNo, $id);
                $res->isSuccess = TRUE;
                $res->code = 200;
                $res->message = "식단 삭제 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }


        case "ongoingChallenge":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];//사용자가 가지고 있는 토큰이 유효한지 확인하고
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->messag = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $id = $data->id;

            if(ongoingChallenge($id) == null){
                http_response_code(200);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "진행중인 챌린지가 없습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            http_response_code(200);
            $res->result = ongoingChallenge($id);
            $res->isSuccess = TRUE;
            $res->code = 200;
            $res->message = "진행중인 챌린지 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        case "dietChallengeCertification":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];//사용자가 가지고 있는 토큰이 유효한지 확인하고
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->messag = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $id = $data->id;
//            $id = getMemberId($accountId);
            $chIdx = $vars["chIdx"];


            $array = getDietGoal($chIdx, $id);
            $cal = $array["cal"];
            $startDate = $array["challenge_start_date"];
            $endDate = $array["challenge_end_date"];
            $period = $array["period"];
            $goal = $array["goal"];
            $totalSuccess = ongoingChallengeCTF($id, $startDate, $endDate, $cal);
            $percent = round((100 / $period) * $totalSuccess ,2);

//            echo $startDate;
//            echo $goal;
//            echo $id;

            if(empty($chIdx)){
                http_response_code(200);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "챌린지 번호를 적어주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            if (!isExistChallenge($chIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 101;
                $res->message = "존재하지 않는 챌린지 번호 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            if(empty($totalSuccess)){
                http_response_code(200);
                $res->isSuccess = TRUE;
                $res->code = 102;
                $res->message = "챌린지 상세 데이터가 없습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            http_response_code(200);
            $res->result["challenge_id"] = $chIdx;
            $res->result["successDate"] = ongoingChallengeCTF($id, $startDate, $endDate, $cal);
            $res->result["period"] = $period;
            $res->result["successPercent"] = $percent;
            $res->result["goal"] = $goal;
            $res->isSuccess = TRUE;
            $res->code = 200;
            $res->message = "챌린지 별 성공 현황 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        case "successCertification":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];//사용자가 가지고 있는 토큰이 유효한지 확인하고
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->messag = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $id = $data->id;

            $chIdx = $req->chIdx;

            $array = getDietGoal($chIdx, $id);
            $cal = $array["cal"];
            $amount = $array["amount"];
            $startDate = $array["challenge_start_date"];
            $endDate = $array["challenge_end_date"];
            $period = $array["period"];
            $goal = $array["goal"];
            $totalSuccess = ongoingChallengeCTF($id, $startDate, $endDate, $cal);
            $percent = round((100 / $period) * $totalSuccess ,2);

            if(empty($chIdx)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 100;
                $res->message = "챌린지 번호를 적어주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if(!(checkBreakfast($id, $endDate)||checkLunch($id, $endDate)||checkDinner($id, $endDate))){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 300;
                $res->message = "아침, 점심, 저녁 값이 모두 입력되지 않았습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if(alreadyReturnMoney($id, $chIdx)){
                http_response_code(200);
                $res->isSuccess = FALSE;
                $res->code = 301;
                $res->message = "이미 금액이 반환됐습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
           }
            
            if($percent == '100') {
                http_response_code(200);
                updateIsSuccess($chIdx, $id);
                returnMoney($amount, $id);
                $res->isSuccess = TRUE;
                $res->code = 200;
                $res->message = "성공 챌린지 금액 리턴 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}

