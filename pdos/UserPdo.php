<?php

function signUp($id, $hash, $age, $nickName, $height, $weight)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO member(account_id, password, birth, nick_name, height, weight) values (?,?,?,?,?,?)";

    $st = $pdo->prepare($query);
    $st->execute([$id, $hash, $age, $nickName, $height, $weight]);

    $st = null;
    $pdo = null;

}

function isValidUser($id){

    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM member WHERE account_id = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $res = $st->fetchAll();

    return intval($res[0]["exist"]);
}

function selectPw($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT password from member WHERE account_id = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

