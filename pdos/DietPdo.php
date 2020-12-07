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

function selectBMealList($id, $date)
{
    $pdo = pdoSqlConnect();
    $query = "select id, food_list, cal * resultGram as cal from
(select diet.id, food_list, breakfast_lunch_dinner, date_format(diet.created_at, '%Y-%m-%d') as created_at, n.cal,n.gram/diet.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and breakfast_lunch_dinner = 'B' and created_at = ?;";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $date]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
//    $query = "select round(sum(cal * resultGram)) as cal, round(sum(carb * resultGram), 2) as carb, round(sum(fat * resultGram), 2) as fat,
//round(sum(protein * resultGram), 2) as protein, round(sum(vitamin * resultGram),2) as vitamin from
//(select food_list, breakfast_lunch_dinner, date_format(diet.created_at, '%Y-%m-%d') as created_at,
//        n.cal, n.carb, n.fat, n.protein, n.vitamin, n.gram/diet.gram as resultGram, account_id from diet
//left outer join nutrient n on diet.food_list = n.food_name) total
//where account_id = ? and breakfast_lunch_dinner = 'B' and created_at = ?;";
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$id, $date]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res1 = $st->fetchAll();
//    $res["totalCalorie"] = $res1;
    $st = null;
    $pdo = null;
    return $res;
}


function selectLMealList($id, $date)
{
    $pdo = pdoSqlConnect();
    $query = "select id, food_list, cal * resultGram as cal from
(select diet.id, food_list, breakfast_lunch_dinner, date_format(diet.created_at, '%Y-%m-%d') as created_at, n.cal,n.gram/diet.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and breakfast_lunch_dinner = 'L' and created_at = ?;";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $date]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function selectDMealList($id, $date)
{
    $pdo = pdoSqlConnect();
    $query = "select id, food_list, cal * resultGram as cal from
(select diet.id, food_list, breakfast_lunch_dinner, date_format(diet.created_at, '%Y-%m-%d') as created_at, n.cal,n.gram/diet.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and breakfast_lunch_dinner = 'D' and created_at = ?;";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $date]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function selectBMealNut($id, $date)
{
    $pdo = pdoSqlConnect();
    $query = "select round(sum(cal * resultGram)) as cal, round(sum(carb * resultGram), 2) as carb, round(sum(fat * resultGram), 2) as fat,
round(sum(protein * resultGram), 2) as protein, round(sum(vitamin * resultGram),2) as vitamin from
(select food_list, breakfast_lunch_dinner, date_format(diet.created_at, '%Y-%m-%d') as created_at,
        n.cal, n.carb, n.fat, n.protein, n.vitamin, n.gram/diet.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
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

function selectLMealNut($id, $date)
{
    $pdo = pdoSqlConnect();
    $query = "select round(sum(cal * resultGram)) as cal, round(sum(carb * resultGram), 2) as carb, round(sum(fat * resultGram), 2) as fat,
round(sum(protein * resultGram), 2) as protein, round(sum(vitamin * resultGram),2) as vitamin from
(select food_list, breakfast_lunch_dinner, date_format(diet.created_at, '%Y-%m-%d') as created_at,
        n.cal, n.carb, n.fat, n.protein, n.vitamin, n.gram/diet.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and breakfast_lunch_dinner = 'L' and created_at = ?;";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $date]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function selectDMealNut($id, $date)
{
    $pdo = pdoSqlConnect();
    $query = "select round(sum(cal * resultGram)) as cal, round(sum(carb * resultGram), 2) as carb, round(sum(fat * resultGram), 2) as fat,
round(sum(protein * resultGram), 2) as protein, round(sum(vitamin * resultGram),2) as vitamin from
(select food_list, breakfast_lunch_dinner, date_format(diet.created_at, '%Y-%m-%d') as created_at,
        n.cal, n.carb, n.fat, n.protein, n.vitamin, n.gram/diet.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and breakfast_lunch_dinner = 'D' and created_at = ?;";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $date]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function selectTotalMealNut($id, $date)
{
    $pdo = pdoSqlConnect();
    $query = "select round(sum(cal * resultGram)) as cal, round(sum(carb * resultGram), 2) as carb, round(sum(fat * resultGram), 2) as fat,
round(sum(protein * resultGram), 2) as protein, round(sum(vitamin * resultGram),2) as vitamin from
(select food_list, breakfast_lunch_dinner, date_format(diet.created_at, '%Y-%m-%d') as created_at,
        n.cal, n.carb, n.fat, n.protein, n.vitamin, n.gram/diet.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and created_at = ?;";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $date]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}
