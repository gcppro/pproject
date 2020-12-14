<?php

function getMemberId($account_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT id FROM member WHERE account_id = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$account_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["id"];
}

function isExistChallenge($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM pproject.challenge WHERE id = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["exist"];
}

function isExistChallengeCertification($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM pproject.challenge_certification WHERE id = ? AND is_deleted = 0) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["exist"];
}

function getChallenge()
{
    $pdo = pdoSqlConnect();
    $query = "SELECT id, goal, CONCAT('모집마감일: ', MONTH(recruitment_end_date), '월 ', DAY(recruitment_end_date), '일') AS end_date,
image_url, member_id, CONCAT('챌린지기간: ', period, '일') AS period
FROM challenge
ORDER BY end_date DESC;";
    $st = $pdo->prepare($query);
    $st->execute([]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getChallengeMemberNumber($challenge_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT COUNT(*) AS count
FROM challenge_participation
WHERE challenge_id = ? AND is_deleted = 0;";
    $st = $pdo->prepare($query);
    $st->execute([$challenge_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}


function getChallengeMembers($challenge_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT challenge_participation.id, member_id, challenge_start_date, is_success, m.nick_name,
       (SELECT COUNT(*) AS num FROM challenge_certification WHERE challenge_participation_id = challenge_participation.id AND challenge_certification.is_deleted = 0) AS certification_num
FROM challenge_participation
INNER JOIN member m on challenge_participation.member_id = m.id
WHERE challenge_id = ? AND challenge_participation.is_deleted = 0;";
    $st = $pdo->prepare($query);
    $st->execute([$challenge_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function setChallenge($member_id, $goal, $recruitment_start_date, $recruitment_end_date, $period, $amount, $image_url, $kinds)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO pproject.challenge(member_id, goal, recruitment_start_date, recruitment_end_date, period, amount, image_url, kinds) VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$member_id, $goal, $recruitment_start_date, $recruitment_end_date, $period, $amount, $image_url, $kinds]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    $last_id = $pdo->lastInsertId();
    $st = null;
    $pdo = null;
    return $last_id;
}


function getChallengeDetail($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT challenge.id,
       challenge.goal, CONCAT(YEAR(challenge.recruitment_start_date), '년 ', MONTH(challenge.recruitment_start_date), '월 ', DAY(challenge.recruitment_start_date), '일') AS start_date,
       CONCAT(YEAR(challenge.recruitment_end_date), '년 ', MONTH(challenge.recruitment_end_date), '월 ', DAY(challenge.recruitment_end_date), '일') AS end_date,
       CONCAT(challenge.period, '일') AS period,
       CONCAT(challenge.amount, '원') AS amount,
       CONCAT(m.nick_name) AS nick_name,
       challenge.image_url, challenge.kinds
FROM challenge
INNER JOIN member m on challenge.member_id = m.id
WHERE challenge.id = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getChallengeAmount($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT challenge.amount
FROM challenge
INNER JOIN member m on challenge.member_id = m.id
WHERE challenge.id = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["amount"];
}


function getUserMoney($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT member.money
FROM member
WHERE id = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["money"];
}

function setChallengeParticipation($member_id, $challenge_id)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO pproject.challenge_participation(challenge_id, member_id) VALUES (?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$challenge_id, $member_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    $st = null;
    $pdo = null;
}

function updateMemberMoney($money, $member_id)
{
    $pdo = pdoSqlConnect();
    $query = "UPDATE pproject.member SET money = money - ? WHERE id = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$money, $member_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    $st = null;
    $pdo = null;
}



function getChallengeCertification($id, $date)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT challenge_certification.id, challenge_certification.image_url, cp.member_id, CONCAT(m.nick_name, ' 님') AS nick_name
FROM challenge_certification
INNER JOIN challenge_participation cp on challenge_certification.challenge_participation_id = cp.id
INNER JOIN member m on cp.member_id = m.id
INNER JOIN challenge c on cp.challenge_id = c.id
WHERE c.id = ? AND CONCAT(MONTH(challenge_certification.date), '월 ', DAY(challenge_certification.date), '일') = ? AND challenge_certification.is_deleted = 0;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $date]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function setChallengeCertification($challenge_participation_id, $image_url, $description)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO pproject.challenge_certification(challenge_participation_id, image_url, description) VALUES (?, ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$challenge_participation_id, $image_url, $description]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    $st = null;
    $pdo = null;
}


function getChallengeParticipationId($challenge_id, $member_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT id
FROM challenge_participation
WHERE challenge_id = ? AND member_id = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$challenge_id, $member_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["id"];
}

function isChallengeParticipation($challenge_id, $member_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM pproject.challenge_participation WHERE challenge_id = ? AND member_id = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$challenge_id, $member_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["exist"];
}



function getChallengeCertificationDetail($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT image_url, description, CONCAT(MONTH(date), '월 ', DAY(date), '일') AS date, cp.member_id
FROM challenge_certification
INNER JOIN challenge_participation cp on challenge_certification.challenge_participation_id = cp.id
WHERE challenge_certification.id = ? AND challenge_certification.is_deleted = 0;
";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function setChallengeExerciseGoal($challenge_id, $exercise_id, $count)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO pproject.challenge_exercise_goal(challenge_id, exercise_id, count) VALUES (?, ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$challenge_id, $exercise_id, $count]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    $st = null;
    $pdo = null;
}

function setChallengeDietGoal($challenge_id, $cal)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO pproject.challenge_diet_goal(challenge_id, cal) VALUES (?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$challenge_id, $cal]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    $st = null;
    $pdo = null;
}

# 해당 유저가 챌린지 몇개를 참여했는지 확인
function countUserChallengeNumber($member_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT COUNT(a.id) as count
FROM (
    SELECT c.id FROM challenge_participation
    INNER JOIN challenge c on challenge_participation.challenge_id = c.id
    WHERE challenge_participation.member_id = ?
    AND DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d') < DATE_ADD(DATE_FORMAT(challenge_participation.challenge_start_date, '%Y-%m-%d'), INTERVAL c.period DAY)) as a;
";
    $st = $pdo->prepare($query);
    $st->execute([$member_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["count"];
}

# 해당 유저가 어떤 챌린지를 참여했는지 보여줌(어떤 챌린지며 챌린지 종류가 뭔지(식단, 운동))
function getUserChallengeKinds($member_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT c.id, c.kinds FROM challenge_participation
    INNER JOIN challenge c on challenge_participation.challenge_id = c.id
    WHERE challenge_participation.member_id = ? AND DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d') < DATE_ADD(DATE_FORMAT(challenge_participation.challenge_start_date, '%Y-%m-%d'), INTERVAL c.period DAY)
    ORDER BY id;
";
    $st = $pdo->prepare($query);
    $st->execute([$member_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

# 아침, 점심, 저녁이 다 있나 (3이 되어야 이제 칼로리를 비교)
function getBdiet($account_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM diet WHERE breakfast_lunch_dinner = 'B' AND account_id = ? AND
                                       DATE_FORMAT(created_at, '%Y-%m-%d') = DATE_FORMAT(current_timestamp, '%Y-%m-%d')) AS exist;
";
    $st = $pdo->prepare($query);
    $st->execute([$account_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["exist"];
}

function getLdiet($account_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM diet WHERE breakfast_lunch_dinner = 'L' AND account_id = ? AND
                                       DATE_FORMAT(created_at, '%Y-%m-%d') = DATE_FORMAT(current_timestamp, '%Y-%m-%d')) AS exist;
";
    $st = $pdo->prepare($query);
    $st->execute([$account_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["exist"];
}

function getDdiet($account_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM diet WHERE breakfast_lunch_dinner = 'D' AND account_id = ? AND
                                       DATE_FORMAT(created_at, '%Y-%m-%d') = DATE_FORMAT(current_timestamp, '%Y-%m-%d')) AS exist;
";
    $st = $pdo->prepare($query);
    $st->execute([$account_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["exist"];
}

# 여기 결과 값이 밑에 값보다 작으면 challenge_daily_success INSERT
function getUserDietCal($account_id)
{
    $pdo = pdoSqlConnect();
    $query = "select round(sum(cal * resultGram)) as cal from
(select food_list, breakfast_lunch_dinner, is_deleted, date_format(diet.created_at, '%Y-%m-%d') as created_at,
        n.cal, diet.gram/n.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and DATE_FORMAT(created_at, '%Y-%m-%d') = DATE_FORMAT(current_timestamp, '%Y-%m-%d') and is_deleted = '0';
";
    $st = $pdo->prepare($query);
    $st->execute([$account_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["cal"];
}

# 챌린지 칼로리 값
function getChallengeCal($challenge_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT cal
FROM challenge_diet_goal
WHERE challenge_id =?;
";
    $st = $pdo->prepare($query);
    $st->execute([$challenge_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["cal"];
}

# 챌린지 운동 종류의 갯수
function getChallengeExerciseKindCount($challenge_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT COUNT(a.id) AS count
FROM (SELECT challenge_exercise_goal.id
    FROM challenge_exercise_goal WHERE challenge_id = ?) as a;
";
    $st = $pdo->prepare($query);
    $st->execute([$challenge_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["count"];
}

# 챌린지 운동 개수 값
function getChallengeExerciseCount($challenge_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT exercise_id, count
FROM challenge_exercise_goal
WHERE challenge_id = ?
ORDER BY exercise_id;
";
    $st = $pdo->prepare($query);
    $st->execute([$challenge_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

# 유저가 진행한 운동(위의 값과 비교해서 exercise_id가 똑같고, count가 위의 값보다 크면 challenge_daily_success INSERT)
function getUserChallengeExerciseKind($exercise_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT exercise_id, count
FROM exercise_count
WHERE member_id = ? AND DATE_FORMAT(created_at, '%Y-%m-%d') = DATE_FORMAT(current_timestamp, '%Y-%m-%d')
ORDER BY exercise_id;
";
    $st = $pdo->prepare($query);
    $st->execute([$exercise_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

# challenge_daily_success 등록
function setUserChallengeDailySuccess($challenge_id, $member_id)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO pproject.challenge_daily_success(challenge_id, member_id) VALUES (?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$challenge_id, $member_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    $st = null;
    $pdo = null;
}

# challenge_daily_success 개수
function getChallengeDailySuccessCount($member_id, $challenge_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT COUNT(a.id) as count FROM (SELECT challenge_daily_success.id
FROM challenge_daily_success
WHERE member_id = ? AND challenge_id = ?) as a;
";
    $st = $pdo->prepare($query);
    $st->execute([$member_id, $challenge_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["count"];
}

# 챌린지 기간 조회 (위의 값과 똑같다면 challenge_participation의 is_success 컬럼 1로 변경)
function getChallengePeriod($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT period
FROM challenge
WHERE id = ?;
";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["period"];
}

# challenge_daily_success 존재 여부 (이것으로 추가 INSERT를 방지)
function isExistChallengeDailySuccess($challenge_id, $member_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM challenge_daily_success
WHERE challenge_id = ? AND member_id = ? AND DATE_FORMAT(created_at, '%Y-%m-%d') = DATE_FORMAT(current_timestamp, '%Y-%m-%d')) as exist;
";
    $st = $pdo->prepare($query);
    $st->execute([$challenge_id, $member_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["exist"];
}

#challenge 성공 여부
function isSuccessChallenge($member_id, $challenge_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT is_success
FROM challenge_participation
WHERE member_id = ? AND challenge_id = ?;
";
    $st = $pdo->prepare($query);
    $st->execute([$member_id, $challenge_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["is_success"];
}