<?php

function getExercises()
{
    $pdo = pdoSqlConnect();
    $query = "SELECT *
FROM exercise;";
    $st = $pdo->prepare($query);
    $st->execute([]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}