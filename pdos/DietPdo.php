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
    $query = "select id as foodIdx, food_list, cal * resultGram as cal from
(select diet.id, food_list, breakfast_lunch_dinner, is_deleted, date_format(diet.created_at, '%Y-%m-%d') as created_at, n.cal,diet.gram/n.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and breakfast_lunch_dinner = 'B' and created_at = ? and is_deleted = '0';";
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
    $query = "select id as foodIdx, food_list, cal * resultGram as cal from
(select diet.id, food_list, is_deleted, breakfast_lunch_dinner, date_format(diet.created_at, '%Y-%m-%d') as created_at, n.cal,diet.gram/n.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and breakfast_lunch_dinner = 'L' and created_at = ? and is_deleted = '0';";
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
    $query = "select id as foodIdx, food_list, cal * resultGram as cal from
(select diet.id, food_list, is_deleted, breakfast_lunch_dinner, date_format(diet.created_at, '%Y-%m-%d') as created_at, n.cal,diet.gram/n.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and breakfast_lunch_dinner = 'D' and created_at = ? and is_deleted = '0';";
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
(select food_list, breakfast_lunch_dinner, is_deleted, date_format(diet.created_at, '%Y-%m-%d') as created_at,
        n.cal, n.carb, n.fat, n.protein, n.vitamin, diet.gram/n.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and breakfast_lunch_dinner = 'B' and created_at = ? and is_deleted = '0';";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $date]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];
}

function selectLMealNut($id, $date)
{
    $pdo = pdoSqlConnect();
    $query = "select round(sum(cal * resultGram)) as cal, round(sum(carb * resultGram), 2) as carb, round(sum(fat * resultGram), 2) as fat,
round(sum(protein * resultGram), 2) as protein, round(sum(vitamin * resultGram),2) as vitamin from
(select food_list, breakfast_lunch_dinner, is_deleted, date_format(diet.created_at, '%Y-%m-%d') as created_at,
        n.cal, n.carb, n.fat, n.protein, n.vitamin, diet.gram/n.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and breakfast_lunch_dinner = 'L' and created_at = ? and is_deleted = '0';";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $date]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];
}

function selectDMealNut($id, $date)
{
    $pdo = pdoSqlConnect();
    $query = "select round(sum(cal * resultGram)) as cal, round(sum(carb * resultGram), 2) as carb, round(sum(fat * resultGram), 2) as fat,
round(sum(protein * resultGram), 2) as protein, round(sum(vitamin * resultGram),2) as vitamin from
(select food_list, breakfast_lunch_dinner, is_deleted, date_format(diet.created_at, '%Y-%m-%d') as created_at,
        n.cal, n.carb, n.fat, n.protein, n.vitamin, diet.gram/n.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and breakfast_lunch_dinner = 'D' and created_at = ? and is_deleted = '0';";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $date]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];
}

function selectTotalMealNut($id, $date)
{
    $pdo = pdoSqlConnect();
    $query = "select round(sum(cal * resultGram)) as cal, round(sum(carb * resultGram), 2) as carb, round(sum(fat * resultGram), 2) as fat,
round(sum(protein * resultGram), 2) as protein, round(sum(vitamin * resultGram),2) as vitamin from
(select food_list, breakfast_lunch_dinner, is_deleted, date_format(diet.created_at, '%Y-%m-%d') as created_at,
        n.cal, n.carb, n.fat, n.protein, n.vitamin, diet.gram/n.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and created_at = ? and is_deleted = '0';";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $date]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];
}

function selectFoodDetail($id, $foodNo)
{
    $pdo = pdoSqlConnect();
    $query = "select total.id as foodIdx, food_list, round((cal * resultGram)) as cal, round((carb * resultGram), 2) as carb, round((fat * resultGram), 2) as fat,
round((protein * resultGram), 2) as protein, round((vitamin * resultGram), 2) as vitamin from
(select diet.id, food_list, is_deleted,
        n.cal, n.carb, n.fat, n.protein, n.vitamin, diet.gram/n.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and total.id = ? and is_deleted = '0';";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $foodNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];
}

function isValidFoodNo($id, $foodNo){

    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM diet WHERE account_id = ? and id = ? and is_deleted = '0') AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $foodNo]);
    $res = $st->fetchAll();

    return intval($res[0]["exist"]);
}


function isDeletedFoodNo($id, $foodNo){

    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM diet WHERE account_id = ? and id = ? and is_deleted = '1') AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $foodNo]);
    $res = $st->fetchAll();

    return intval($res[0]["exist"]);
}



function deleteDiet($foodNo, $id){
    $pdo = pdoSqlConnect();
    $query = "UPDATE diet SET is_deleted = '1' WHERE id = ? and account_id = ?";
    $st = $pdo->prepare($query);
    $st->execute([$foodNo, $id]);
    $st = null;
    $pdo = null;
}


function dietChallengeCertification($id, $date)
{
    $pdo = pdoSqlConnect();
    $query = "select round(sum(cal * resultGram)) as cal, round(sum(carb * resultGram), 2) as carb, round(sum(fat * resultGram), 2) as fat,
round(sum(protein * resultGram), 2) as protein, round(sum(vitamin * resultGram),2) as vitamin from
(select food_list, breakfast_lunch_dinner, is_deleted, date_format(diet.created_at, '%Y-%m-%d') as created_at,
        n.cal, n.carb, n.fat, n.protein, n.vitamin, diet.gram/n.gram as resultGram, account_id from diet
left outer join nutrient n on diet.food_list = n.food_name) total
where account_id = ? and breakfast_lunch_dinner = 'L' and created_at = ? and is_deleted = '0';";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $date]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res[0];
}

function ongoingChallenge($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT challenge_id, recruitment_start_date, recruitment_end_date, image_url, goal
FROM challenge
INNER JOIN challenge_participation cp
    on challenge.id = cp.challenge_id
INNER JOIN member m
    on cp.member_id = m.id
WHERE challenge.is_deleted = '0' and account_id = ?
group by challenge_id;";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}


function getDietGoal($chIdx)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT challenge.id, recruitment_start_date, recruitment_end_date, cal, period, goal
FROM challenge
INNER JOIN challenge_participation cp
    on challenge.id = cp.challenge_id
INNER JOIN member m
    on cp.member_id = m.id
INNER JOIN challenge_diet_goal cdg
    on cdg.challenge_id = challenge.id
WHERE challenge.is_deleted = '0'
    AND challenge.id = ?
group by challenge.id;";
    $st = $pdo->prepare($query);
    $st->execute([$chIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}



function ongoingChallengeCTF($id, $startDate, $endDate, $cal)
{
    $pdo = pdoSqlConnect();
    $query = "select count(date) as successDate
from (select DATE (created_at) as date , round(sum(cal * resultGram)) as totalCal
from
(select date_format(diet.created_at, '%Y-%m-%d') as created_at,
        n.cal, diet.gram/n.gram as resultGram, account_id 
from diet
left outer join nutrient n 
    on diet.food_list = n.food_name) total
where account_id = ? 
  and created_at between ? and ?
group by date having totalCal < ?)challenge_result;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $startDate, $endDate, $cal]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["successDate"];
}