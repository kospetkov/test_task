<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require_once 'connect.php';

    define('PATH_TO_MYDOMEN', 'http://mydomen/');
    define('MYDOMEN', 'mydomen');
    define('LENGTH', '15');
    define('LIMIT_TIME_FOR_IP', '3600');
    define('LIMIT_TIME_FOR_LINCK', '15768000');
    define('INDEX_FOR_ARRAY', '2');
    define('LIMIT_CREATE_LINCK', '10');

    if (isset($_POST['linck'])) {
        $linck = $_POST['linck'];
        $linck_to_array = explode('/', $linck);
        $ip = file_get_contents('https://api.ipify.org');

        if ($linck_to_array[INDEX_FOR_ARRAY] !== MYDOMEN) {
            $sql = "SELECT * FROM `linck_tabl` WHERE linck LIKE '$linck'";
            $result = mysqli_query($connect, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $data_array[] = [
                        'new_linck' => $row['new_linck'],
                        'ip' => 'select',
                        'linck' => ''
                    ];
                }
                echo json_encode($data_array);
            }

            else {
                $new_linck = create_new_linck($linck);
                $using_linck = 0;
                $limit_time = limit_time(LIMIT_TIME_FOR_IP);
                $sql = "SELECT *  FROM `linck_tabl` WHERE `ip_user` LIKE '$ip' AND `time_create` > '$limit_time'";
                $result = mysqli_query($connect, $sql);
                $count_for_rows = mysqli_num_rows($result);

                if (($count_for_rows < LIMIT_CREATE_LINCK) && ($count_for_rows >= 0)) {
                    $limit_time = limit_time(LIMIT_TIME_FOR_LINCK);
                    $sql = "SELECT *  FROM `linck_tabl` WHERE `time_create` < '$limit_time'";
                    $result = mysqli_query($connect, $sql);
                    $id = 0;

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $data_array[] = [
                                'id' => $row['id']
                            ];
                        }
                        $id = $data_array[0]['id'];
                        $create_time = limit_time(0);
                        $sql = "UPDATE `linck_tabl` SET `linck`='$linck', `new_linck`='$new_linck', `using_linck`=0, `ip_user`='$ip', time_create='$create_time' WHERE id='$id'";

                        if (!mysqli_query($connect, $sql)) {
                            error_message($res, mysqli_error($connect));
                        }

                        else {
                            $data_linck[] = [
                                'new_linck' => $new_linck,
                                'ip' => 'update',
                                'linck' => ''
                            ];
                            echo json_encode($data_linck);
                        }
                    }

                    else {
                        $sql = "INSERT INTO `linck_tabl` (`id`, `linck`, `new_linck`, `using_linck`, ip_user)"
                            . " VALUES (NULL, '$linck', '$new_linck', '0', '$ip')";

                        if (!mysqli_query($connect, $sql)) {
                            error_message($res, mysqli_error($connect));
                        }

                        else {
                            $data_array[] = [
                                'new_linck' => $new_linck,
                                'ip' => 'insert',
                                'linck' => ''
                            ];
                            echo json_encode($data_array);
                        }
                    }
                }

                else {
                    $data_array[] = [
                        'new_linck' => 'limit create linck',
                        'ip' => $ip,
                        'linck' => ''
                    ];
                    echo json_encode($data_array);
                }
            }
        }

        else {
            $sql = "SELECT * FROM `linck_tabl` WHERE `new_linck` LIKE '$linck'";
            $result = mysqli_query($connect, $sql);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $data_array[] = [
                    'id' => $row['id'],
                    'linck' => $row['linck'],
                    'new_linck' => '',
                    'using_linck' => $row['using_linck']
                ];
                $using_linck = $data_array[0]['using_linck'] + 1;
                $id_linck = $data_array[0]['id'];
                echo json_encode($data_array);

                $sql = "UPDATE `linck_tabl` SET `using_linck`=$using_linck WHERE id=$id_linck";

                if (!mysqli_query($connect, $sql)) {
                    error_message($res, mysqli_error($connect));
                }
            }

        }

    }
}

mysqli_close($connect);

function create_new_linck($string) {
    $new_linck = explode('/', $string);
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRSTUVWXYZ0123456789';
    $str_length = strlen($chars);
    $string = '';
    for ($i = 0; $i < LENGTH; $i ++) {
        $string .= substr($chars, mt_rand(1, $str_length) - 1, 1);
    }
    $new_linck = PATH_TO_MYDOMEN . $string;
    return $new_linck;
}

function error_message($res, $error_message) {
    $res['error'] = $error_message;
    $res['status'] = '';
    echo json_encode($res);
}

function limit_time($limit) {
   $timer = time() - $limit;
   $date_limit = date("Y-m-d H:i:s", $timer);
   return $date_limit;
}