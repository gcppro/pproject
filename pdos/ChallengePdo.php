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

function setChallenge($member_id, $goal, $recruitment_start_date, $recruitment_end_date, $period, $amount, $image_url)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO pproject.challenge(member_id, goal, recruitment_start_date, recruitment_end_date, period, amount, image_url) VALUES (?, ?, ?, ?, ?, ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$member_id, $goal, $recruitment_start_date, $recruitment_end_date, $period, $amount, $image_url]);
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
       challenge.image_url
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