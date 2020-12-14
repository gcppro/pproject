<?php
function isExistExercise($exercise_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM pproject.exercise WHERE id = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$exercise_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["exist"];
}
function getExercises()
{
    $pdo = pdoSqlConnect();
    $query = "SELECT id, name, thumbnail
FROM exercise;";
    $st = $pdo->prepare($query);
    $st->execute([]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function goalKind($challenge_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM pproject.challenge_exercise_goal WHERE challenge_id = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$challenge_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["exist"];
}

function getChallengeExerciseGoal($challenge_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT CONCAT(e.name, ' ' ,challenge_exercise_goal.count, '회') AS goal
FROM challenge_exercise_goal
INNER JOIN exercise e on challenge_exercise_goal.exercise_id = e.id
WHERE challenge_id = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$challenge_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getChallengeDietGoal($challenge_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT CONCAT('목표 칼로리: ', cal, ' kcal') AS goal
FROM challenge_diet_goal
WHERE challenge_id = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$challenge_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getExercisesDetail($exercise_id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT *
FROM exercise
WHERE id = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$exercise_id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function getUserInfo($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT nick_name, money
FROM member
WHERE id = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}