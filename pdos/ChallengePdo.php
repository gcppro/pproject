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

function getChallenge()
{
    $pdo = pdoSqlConnect();
    $query = "SELECT id, goal, CONCAT('모집마감일: ', MONTH(recruitment_end_date), '월 ', DAY(recruitment_end_date), '일') AS end_date, image_url, member_id, CONCAT('챌린지기간: ', period, '일') AS period
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

function setChallenge($member_id, $goal, $recruitment_start_date, $recruitment_end_date, $period, $amount, $image_url, $description)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO pproject.challenge(member_id, goal, recruitment_start_date, recruitment_end_date, period, amount, image_url, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$member_id, $goal, $recruitment_start_date, $recruitment_end_date, $period, $amount, $image_url, $description]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    $st = null;
    $pdo = null;
}

function getChallengeDetail($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT challenge.id,
       challenge.goal, CONCAT('모집시작일: ', YEAR(challenge.recruitment_start_date), '년 ', MONTH(challenge.recruitment_start_date), '월 ', DAY(challenge.recruitment_start_date), '일') AS start_date,
       challenge.goal, CONCAT('모집마감일: ', YEAR(challenge.recruitment_end_date), '년 ', MONTH(challenge.recruitment_end_date), '월 ', DAY(challenge.recruitment_end_date), '일') AS end_date,
       CONCAT('챌린지기간: ', challenge.period, '일') AS period,
       CONCAT('금액: ', challenge.amount, '원') AS amount,
       CONCAT('주최자: ', m.nick_name) AS nick_name,
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

function d()
{

}