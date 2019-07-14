<?php
require_once 'lib/vendor/autoload.php';
use Krugozor\Database\Mysql\Mysql as Mysql;

CONST DB_LOGIN = 'haton1';
CONST DB_NAME = 'haton1';
CONST DB_PASS = 'Mj9k76nQYqvuZeh9j7CO';

CONST ERROR_MSGS = ['login' => 'логин',
                    'mail' => 'E-mail',
                    'phone' => 'телефон'];

header('Access-Control-Expose-Headers: Access-Control-Allow-Origin', false);
header('Access-Control-Allow-Origin: *', false);
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept', false);

$input_params = !empty($_POST) ? $_POST : !empty($_GET) ? $_GET : exit('Error1');

try {
    $db = Mysql::create('localhost', DB_LOGIN, DB_PASS)->setCharset('utf8')->setDatabaseName(DB_NAME);
} catch (Exception $e) {
    exit(json_encode(["status" => "error",
        "error" => "error_db"]));
}
$result = [];
$result['status'] = 'ok';

if (!isset($input_params['module']) or $input_params['module'] != 'auth') {
    session_start(['name' => 'SESSION']);
    if (!isset($_SESSION['username'])) {
        $result['auth'] = ['status' => 'fail'];
        if (isset($input_params['module']) and $input_params['module'] == 'get' and isset($input_params['mode']) and $input_params['mode'] == 'points')
            $result['auth'] = [
                'status' => 'fail',
                'username' => 'guest',
                'is_moder' => 0
            ];
        else
            unset($input_params['module']);
//        session_destroy();
        header_remove("set-cookie");
        setcookie("SESSION","");
//        $result['auth'] = [
//            'status' => 'ok',
//            'username' => 'Иванов И.И.'
//        ];
    } else {
        $result['auth'] = [
            'status' => 'ok',
            'username' => $_SESSION['username'],
            'is_moder' => $_SESSION['is_moder']
        ];
    }
}

issetParams(['module']);

switch ($input_params['module']) {
    case "auth":  // Авторизация
        {
            issetParams(['pass', 'login']);

            $user_data = $db->query("SELECT first_name, last_name, second_name, id_user, is_moder FROM users WHERE
                        (login = '?s' or mail = '?s' or phone = '?s') AND pass = '?s'",
                $input_params['login'], $input_params['login'], $input_params['login'], $input_params['pass'])->fetch_assoc();

            if (!isset($user_data['first_name'])) {
                $result['status'] = 'error';
                $result['error'] = 'invalid_auth';
                break;
            }

            $username = $user_data['last_name']." ".mb_substr($user_data['first_name'], 0, 1).". ".mb_substr($user_data['second_name'], 0, 1).".";

            session_start(['name' => 'SESSION']); // Авторизация прошла успешно
            $_SESSION['id_user'] = $user_data['id_user'];
            $_SESSION['username'] = $username;
            $_SESSION['is_moder'] = $user_data['is_moder'];
            $result['auth'] = [
                'status' => 'ok',
                'username' => $username,
                'is_moder' => $user_data['is_moder']
            ];
            break;
        }
    case "logout": // Выход из сисемы
        {
            if (isset($_SESSION['id']))
                session_destroy();
            setcookie("SESSION", "");
            $result['logout'] = "ok";
            break;
        }
    case "register":
        {
            issetParams(['pass', 'first_name', 'last_name', 'second_name', 'series_passport', 'number_passport', 'login']);
            $check_valid = $db->query("SELECT 1 FROM users WHERE login = '?s'", $input_params['login'])->fetch_assoc()['1'];

            if (isset($check_valid)) {
                $result['status'] = 'error';
                $result['error'] = 'login';
                $result['error_msg'] = "Такой логин уже зарегистрирован";
                break;
            }
            $check_repeat = $db->query("SELECT 1 FROM users WHERE series_passport = '?s' AND number_passport = '?s'", $input_params['series_passport'], $input_params['number_passport'])->fetch_assoc()['1'];
            if (isset($check_repeat)) {
                $result['status'] = 'error';
                $result['error'] = 'passport';
                $result['error_msg'] = 'Пользователь с таким паспортом уже существует';
                break;
            }
            if (checkPassport($input_params['number_passport'], $input_params['series_passport']) != 1) {
                $result['status'] = 'error';
                $result['error'] = 'passport';
                $result['error_msg'] = "Неверные данные паспорта";
                break;
            }
            $db->query("INSERT INTO users (pass, first_name, last_name, second_name, series_passport, number_passport, login)
                        VALUE ('?s', '?s', '?s', '?s', ?i, ?i, '?s')", $input_params['pass'], $input_params['first_name'],
                            $input_params['last_name'], $input_params['second_name'], $input_params['series_passport'], $input_params['number_passport'], $input_params['login']);
            break;
        }
    case "get":
        {
            issetParams(['mode']);
            switch ($input_params['mode']) {
                case "profile":
                    {
                        $result['result'] = $db->query("SELECT login, mail, first_name, last_name, 
                                                        second_name, phone, series_passport, number_passport FROM users
                                                        WHERE id_user = ?i", $_SESSION['id_user'])->fetch_assoc();
                        break;
                    }
                case "points":
                    {
                        if (!isset($input_params['sort'])) {
                            $result['result'] = $db->query("SELECT cord_x,
                                                                   cord_y,
                                                                   solution.id_solution,
                                                                   solution.title,
                                                                   dascription,
                                                                   src_image,
                                                                   id_creator,
                                                                   progress,
                                                                   type,
                                                                   district,
                                                                   time,
                                                                   stage.title                            as 'status',
                                                                   ROUND(100 / SUM(CASE WHEN vote.id_solution = solution.id_solution THEN vote.priority + 1 ELSE 0 END) /
                                                                         COUNT(DISTINCT vote.id_user)) as 'votes'
                                                            FROM points,
                                                                 solution,
                                                                 stage,
                                                                 vote
                                                            WHERE points.id_solution = solution.id_solution
                                                              AND solution.id_stage = stage.id_stage
                                                              AND is_show = 1
                                                            GROUP BY cord_x, cord_y, solution.id_solution, solution.title, dascription, src_image, id_creator, progress, type,
                                                                     district, time, status
                                                             ORDER BY votes DESC")->fetch_assoc_array();
                            break;
                        }
                        if ($input_params['sort'] == 'times')
                            $result['result'] = $db->query("SELECT cord_x,
                                                                   cord_y,
                                                                   solution.id_solution,
                                                                   solution.title,
                                                                   dascription,
                                                                   src_image,
                                                                   id_creator,
                                                                   progress,
                                                                   type,
                                                                   district,
                                                                   time,
                                                                   stage.title                            as 'status',
                                                                   ROUND(100 / SUM(CASE WHEN vote.id_solution = solution.id_solution THEN vote.priority + 1 ELSE 0 END) /
                                                                         COUNT(DISTINCT vote.id_user)) as 'votes'
                                                            FROM points,
                                                                 solution,
                                                                 stage,
                                                                 vote
                                                            WHERE points.id_solution = solution.id_solution
                                                              AND solution.id_stage = stage.id_stage
                                                              AND is_show = 1
                                                            GROUP BY cord_x, cord_y, solution.id_solution, solution.title, dascription, src_image, id_creator, progress, type,
                                                                     district, time, status
                                                            ORDER BY time")->fetch_assoc_array();
                        else if ($input_params['sort'] == 'coords') {
                            issetParams(['coord_x', 'coord_y']);
                            $result['result'] = $db->query("SELECT cord_x,
                                                                   cord_y,
                                                                   solution.id_solution,
                                                                   solution.title,
                                                                   dascription,
                                                                   src_image,
                                                                   id_creator,
                                                                   progress,
                                                                   type,
                                                                   district,
                                                                   time,
                                                                   stage.title                            as 'status',
                                                                   ROUND(100 / SUM(CASE WHEN vote.id_solution = solution.id_solution THEN vote.priority + 1 ELSE 0 END) /
                                                                         COUNT(DISTINCT vote.id_user)) as 'votes',
                                                                   (SQRT(POW((cord_x - ?i), 2) + POW((cord_y - ?i), 2))) as 'len'
                                                            FROM points,
                                                                 solution,
                                                                 stage,
                                                                 vote
                                                            WHERE points.id_solution = solution.id_solution
                                                              AND solution.id_stage = stage.id_stage
                                                              AND is_show = 1
                                                            GROUP BY cord_x, cord_y, solution.id_solution, solution.title, dascription, src_image, id_creator, progress, type,
                                                                     district, time, statusORDER BY len",
                                                    $input_params['coord_x'], $input_params['coord_y'])->fetch_assoc_array();
                        } else {
                            $result['status'] = 'error';
                            $result['error'] = 'invalid_auth';
                            break;
                        }

                        break;
                    }
                case "solutions":
                    {
                        issetParams(['id']);
                        $result['result'] = [
                            'solution' => $db->query("SELECT * FROM points, solution WHERE id_solution = ?i", $input_params['id'])->fetch_assoc(),
                            'point' => $db->query("SELECT points.* FROM points, solution WHERE points.id_solution = solution.id_solution
                                                            AND solution.id_solution = ?i", $input_params['id'])->fetch_assoc()
                        ];
                        break;
                    }
                case "vote":
                    {
                        $result['result'] = $db->query("SELECT * FROM solution, vote WHERE solution.id_solution = vote.id_solution
                                                        AND vote.id_user = ?i ORDER BY priority", $_SESSION['id_user'])->fetch_assoc_array();
                        break;
                    }
                default:
                    $result['status'] = 'error';
                    $result['error'] = 'invalid_params';
            }
            break;
        }
    case 'update':
        {
            issetParams(['mode', 'value']);

            if (!in_array($input_params['mode'], ['login', 'pass', 'mail', 'first_name', 'last_name', 'second_name', 'phone'])) {
                $result['status'] = 'error';
                $result['error'] = 'invalid_params';
                break;
            }

            if (in_array($input_params['mode'], ['login', 'mail', 'phone'])) {
                $check_valid = $db->query("SELECT 1 FROM users WHERE ?f = '?s'", $input_params['mode'], $input_params['value'])->fetch_assoc()['1'];

                if (isset($check_valid)) {
                    $result['status'] = 'error';
                    $result['error'] = "invalid_{$input_params['mode']}";
                    $result['error_msg'] = "Такой ".ERROR_MSGS[$input_params['mode']]." уже зарегистрирован";
                    break;
                }
            }

            $db->query("UPDATE users SET ?f = '?s' WHERE id_user = ?i", $input_params['mode'], $input_params['value'], $_SESSION['id_user']);

            break;
        }
    case 'create':
        {
            issetParams(['title', 'description', 'src_image', 'coord_x', 'coord_y', 'street']);
            $db->query("INSERT INTO solution (title, dascription, src_image, id_creator, time, street)
                        VALUE ('?s', '?s', '?s', ?i, ?i)", $input_params['title'], $input_params['description'], $input_params['src_image'],
                                                $_SESSION['id_user'], time(), $input_params['street']);
            $id_solution = $db->query("select last_insert_id() as id")->fetch_assoc()['id'];
            $db->query("INSERT INTO points (cord_x, cord_y, id_solution) VALUE (?d, ?d, ?i)",
                        $input_params['coord_x'], $input_params['coord_y'], $id_solution);
            break;
        }
    case "vote":
        {
            issetParams(['mode', 'id']);
            if ($input_params['mode'] == 'add') {
                $check = $db->query("SELECT 1 FROM vote WHERE id_solution = ?i AND id_user = ?i", $input_params['id'], $_SESSION['id_user'])->fetch_assoc()['1'];
                if (isset($check)) {
                    $result['status'] = 'error';
                    $result['error'] = 'id_vote';
                    $result['error_msg'] = 'Вы уже подписались на эту петицию';
                    break;
                }
                $check = $db->query("SELECT 1 FROM solution WHERE id_solution = ?i", $input_params['id'])->fetch_assoc()['1'];
                if (!isset($check)) {
                    $result['status'] = 'error';
                    $result['error'] = 'id_solution';
                    break;
                }
                $priority = $db->query("SELECT MAX(priority) as 'count' FROM vote WHERE id_user = ?i", $_SESSION['id_user'])->fetch_assoc()['count'] ?? -1;
                $db->query("UPDATE solution SET votes = votes + 1 WHERE id_solution = ?i", $input_params['id']);
                $db->query("INSERT INTO vote (id_solution, id_user, priority) VALUE (?i, ?i, ?i)", $input_params['id'], $_SESSION['id_user'], ($priority + 1));
            } else if ($input_params['mode'] == 'modify_position') {
                issetParams(['priority']);
                $this_priority = $db->query("SELECT priority FROM vote WHERE id_solution = ?i AND id_user = ?i",
                    $input_params['id'], $_SESSION['id_user'])->fetch_assoc()['priority'];
                $db->query("UPDATE vote SET priority = priority + 1 WHERE priority >= ?i AND id_user = ?i 
                        AND priority < ?i",
                    $input_params['priority'], $_SESSION['id_user'], $this_priority);
                $db->query("UPDATE vote SET priority = ?i WHERE id_solution = ?i AND id_user = ?i",
                    $input_params['priority'], $input_params['id'], $_SESSION['id_user']);
            }
            break;
        }
    case 'moder':
        {
//            $_SESSION['is_moder'] = 1;
            if (!isset($_SESSION['is_moder']) or $_SESSION['is_moder'] != 1) {
                $result['status'] = 'error';
                $result['error'] = 'module';
                break;
            }
            issetParams(['mode']);
            switch ($input_params['mode']) {
                case "get":
                    {
                        issetParams(['is_show']);
                        $result['result'] = $db->query("SELECT * FROM solution WHERE is_show = ?i", $input_params['is_show'])->fetch_assoc_array();
                        break;
                    }
                case "update":
                    {
                        issetParams(['container', 'value', 'id']);
                        if (!in_array($input_params['container'], ['progress', 'type', 'district', 'id_stage', 'is_show'])) {
                            $result['status'] = 'error';
                            $result['error'] = 'permission_denied';
                            break;
                        }
                        $check_publish = $db->query("SELECT is_show FROM solution WHERE id_solution = ?i", $input_params['id'])->fetch_assoc()['is_show'];
                        if ($check_publish and in_array($input_params['container'], ['type', 'district', 'is_show'])) {
                            $result['status'] = 'error';
                            $result['error'] = 'permission_denied';
                            break;
                        }
                        $db->query("UPDATE solution SET ?f = '?s' WHERE id_solution = ?i", $input_params['container'], $input_params['value'], $input_params['id']);
                        break;
                    }
                default:
                    {
                        $result['status'] = 'error';
                        $result['error'] = 'module';
                    }
            }
            break;
        }
    default:
        $result['status'] = 'error';
        $result['error'] = 'module';
}

echo json_encode($result);

function issetParams($param) {
    global $input_params, $result;
    foreach ($param as $var) {
        if (!isset($input_params[$var])) {
            $result['status'] = 'error';
            $result['error'] = "not_params_$var";
            exit(json_encode($result));
        }
    }
    return True;
}

function checkPassport($number, $series) {
    $result_apis = json_decode(
        file_get_contents("http://passport.bg-c.ru/verification.php?ser=$series&nom=$number"), true);
    if ($result_apis['result'] != 'success' or !isset($result_apis['data']))
        return false;
    return $result_apis['data']['passport'];
}

function uploadImage() {
    global $result;
    $path_download_image = __DIR__."/img/";
    $type = 'image';
    if (isset($_FILES[$type]['name']) and !is_array($_FILES[$type]['name']))
        foreach ($_FILES[$type] as $key => $val)
            $_FILES[$type][$key] = [$val];
    if(isset($_FILES[$type]) and $_FILES[$type]['name'][0] != null) {
        // Максимальный размер файла
        $size = 5242880;
        $total = count($_FILES[$type]['name']);
        $filenames = [];
        // Обработка запроса
        for($i = 0; $i < $total; $i++){

            // Проверяем тип файла
            if ($_FILES[$type]['type'][$i] == 'image/jpeg')
                $new_name_file = time()."_".rand().".jpg";
            else {
                exit("Неверный формат файла {$_FILES[$type]['name'][$i]}");
            }

            // Проверяем размер файла
            if ($_FILES[$type]['size'][$i] > $size) {
                exit("Размер файла {$_FILES[$type]['name'][$i]} слишком велик");
            }


            if (!move_uploaded_file($_FILES[$type]['tmp_name'][$i], $path_download_image . $new_name_file)) {
                exit("Не удалось загрузить {$_FILES[$type]['name'][$i]}");
            }

            $filenames[] = $path_download_image . $new_name_file;
        }
        return $filenames;
    } else {
        $result['status'] = 'error';
        $result['error'] = 'msg';
        exit(json_encode($result));
    }
}