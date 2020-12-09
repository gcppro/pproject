<?php
require './pdos/DatabasePdo.php';
require './pdos/IndexPdo.php';
require './pdos/ChallengePdo.php';
require './pdos/ExercisePdo.php';
require './pdos/UserPdo.php';
require './pdos/DietPdo.php';
require './vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

date_default_timezone_set('Asia/Seoul');
ini_set('default_charset', 'utf8mb4');

//에러출력하게 하는 코드
//error_reporting(E_ALL); ini_set("display_errors", 1);

//Main Server API
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    /* ******************   Test   ****************** */
    $r->addRoute('GET', '/', ['IndexController', 'index']);
    $r->addRoute('GET', '/test', ['IndexController', 'test']);
    $r->addRoute('GET', '/test/{testNo}', ['IndexController', 'testDetail']);
    $r->addRoute('POST', '/test', ['IndexController', 'testPost']);
    $r->addRoute('GET', '/jwt', ['MainController', 'validateJwt']);
    $r->addRoute('POST', '/jwt', ['MainController', 'createJwt']);

    $r->addRoute('GET', '/challenge', ['ChallengeController', 'getChallenge']); // 1. 챌린지 첫 페이지
    $r->addRoute('POST', '/challenge', ['ChallengeController', 'setChallenge']); // 2. 챌린지 추가
    $r->addRoute('GET', '/challenge/{id}', ['ChallengeController', 'getChallengeDetail']); // 3. 모집 중인 챌린지 창
    $r->addRoute('POST', '/challenge-participation', ['ChallengeController', 'setChallengeParticipation']); // 4. 챌린지 참여
    $r->addRoute('GET', '/challenge-certification', ['ChallengeController', 'getChallengeCertification']); // 5. 챌린지 인증하기 창
    $r->addRoute('POST', '/challenge-certification', ['ChallengeController', 'setChallengeCertification']); // 6. 챌린지 인증
    $r->addRoute('GET', '/challenge-certification/{id}', ['ChallengeController', 'getChallengeCertificationDetail']); // 7. 인증 모여모여 창
    $r->addRoute('UPDATE', '/challenge-certification/{id}', ['ChallengeController', 'updateChallengeCertification']); // 8. 인증 수정
    $r->addRoute('DELETE', '/challenge-certification/{id}', ['ChallengeController', 'deleteChallengeCertification']); // 9. 인증 삭제
    $r->addRoute('GET', '/exercises', ['ExerciseController', 'getExercises']);

    $r->addRoute('POST', '/user', ['UserController', 'signUp']); 
    $r->addRoute('POST', '/token', ['UserController', 'login']);

    $r->addRoute('POST', '/diet', ['DietController', 'setDiet']);
    $r->addRoute('GET', '/diet', ['DietController', 'getDiet']);
    $r->addRoute('GET', '/diet/{foodIdx}', ['DietController', 'foodNutrientDetail']);
    $r->addRoute('DELETE', '/diet', ['DietController', 'deleteDiet']);

    $r->addRoute('GET', '/web-admin/users', ['UserController', 'selectUsers']);


//    $r->addRoute('GET', '/users', 'get_all_users_handler');
//    // {id} must be a number (\d+)
//    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//    // The /{title} suffix is optional
//    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 로거 채널 생성
$accessLogs = new Logger('ACCESS_LOGS');
$errorLogs = new Logger('ERROR_LOGS');
// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$accessLogs->pushHandler(new StreamHandler('logs/access.log', Logger::INFO));
$errorLogs->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));
// add records to the log
//$log->addInfo('Info log');
// Debug 는 Info 레벨보다 낮으므로 아래 로그는 출력되지 않음
//$log->addDebug('Debug log');
//$log->addError('Error log');

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "404 Not Found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        switch ($routeInfo[1][0]) {
            case 'IndexController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/IndexController.php';
                break;
            case 'MainController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/MainController.php';
                break;
            case 'ChallengeController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/ChallengeController.php';
                break;
            case 'DietController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/DietController.php';
                break;
            case 'UserController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/UserController.php';
                break;
            case 'ExerciseController':
                $handler = $routeInfo[1][1];
                require './controllers/ExerciseController.php';
                break;
            /*case 'SearchController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/SearchController.php';
                break;
            case 'ReviewController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ReviewController.php';
                break;
            case 'ElementController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ElementController.php';
                break;
            case 'AskFAQController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/AskFAQController.php';
                break;*/
        }

        break;
}
