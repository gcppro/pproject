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

function selectUsers()
{
    $pdo = pdoSqlConnect();
    $query = "select id, account_id, nick_name, created_at, is_deleted from member;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

//$id = user idx
function selectUserDietStatus($id)
{
    $pdo = pdoSqlConnect();
    $query = "select m.id, DATE (created_at) as date , round(sum(cal * resultGram)) as cal, round(sum(carb * resultGram), 2) as carb, round(sum(fat * resultGram), 2) as fat,
round(sum(protein * resultGram), 2) as protein, round(sum(vitamin * resultGram),2) as vitamin from
(select date_format(diet.created_at, '%Y-%m-%d') as created_at,
        n.cal, n.carb, n.fat, n.protein, n.vitamin, n.gram/diet.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
inner join (SELECT id, account_id from member) m on total.account_id = m.account_id
where m.id = ?
group by date;
";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function selectUserInfo($id)
{
    $pdo = pdoSqlConnect();
    $query = "select id, account_id, birth, nick_name, height, weight, gender
from member
where id = ?;
";
    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}
