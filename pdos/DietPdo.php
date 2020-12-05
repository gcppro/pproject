<?php

function setDiet($foodName, $type, $gram, $id)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO diet(food_list, breakfast_lunch_dinner, gram, account_id) values (?,?,?,?);";

    $st = $pdo->prepare($query);
    $st->execute([$foodName, $type, $gram, $id]);

    $st = null;
    $pdo = null;

}

function selectBreakfast($id, $date)
{
    $pdo = pdoSqlConnect();
    $query = "select food_list, breakfast_lunch_dinner, cal * resultGram as cal, carb * resultGram as carb, fat * resultGram as fat, created_at from
(select food_list, breakfast_lunch_dinner, date_format(diet.created_at, '%Y-%m-%d') as created_at, n.cal, n.carb, n.fat, n.gram/diet.gram as resultGram, account_id from diet
inner join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and breakfast_lunch_dinner = 'B' and created_at = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $date]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function selectLunch($id, $type)
{
    $pdo = pdoSqlConnect();
    $query = "select food_list, breakfast_lunch_dinner, cal * resultGram as cal, carb * resultGram as carb, fat * resultGram as fat, created_at from
(select food_list, breakfast_lunch_dinner, created_at, n.cal, n.carb, n.fat, n.gram/diet.gram as resultGram, account_id from diet
inner join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and breakfast_lunch_dinner = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $type]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function selectDinner($id, $type)
{
    $pdo = pdoSqlConnect();
    $query = "select food_list, breakfast_lunch_dinner, cal * resultGram as cal, carb * resultGram as carb, fat * resultGram as fat, created_at from
(select food_list, breakfast_lunch_dinner, created_at, n.cal, n.carb, n.fat, n.gram/diet.gram as resultGram, account_id from diet
inner join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and breakfast_lunch_dinner = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $type]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}
