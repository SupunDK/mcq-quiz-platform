<?php if(isset($_GET['quiz_name'])) : ?>

<?php

if($_GET['quiz_name'] != "quizOverall"){

    $quiz_id_name = $_GET['quiz_name'];

    $quiz_data_get_query = "SELECT * FROM resps WHERE quiz_id_name = '$quiz_id_name' ";

    include('connect_db.php');
    $quiz_data_object = mysqli_query($db, $quiz_data_get_query);
    mysqli_close($db);

    if (mysqli_num_rows($quiz_data_object) == 0){
        $quiz_deleted = 1;
    }else{
        $quiz_deleted = 0;
        

        $quiz_data = mysqli_fetch_assoc($quiz_data_object);

        
        if($quiz_data != NULL){

            unset($quiz_data['id']);
            unset($quiz_data['quiz_id_name']);
            unset($quiz_data['quiz_name']);
            unset($quiz_data['status']);

            $num_quiz_takers = count($quiz_data);

            $time_taken = array();
            $full_names = array();
            $profile_pics = array();
            $usernames = array();
            $scores = array();
            
            $atmptd_num_quiz_takers = 0;

            foreach(array_keys($quiz_data) as $key){
                if($quiz_data[$key] != NULL && strpos($quiz_data[$key], ',') != false){
                    $quiz_data[$key] = explode(',',$quiz_data[$key]);
                    array_push($time_taken, (int)$quiz_data[$key][1]);
                    array_push($full_names, $quiz_data[$key][4]);
                    array_push($profile_pics, $quiz_data[$key][5]);
                    array_push($usernames, $key);
                    array_push($scores, (int)$quiz_data[$key][2]);
                    $quiz_data[$key] = (int)$quiz_data[$key][2];
                    $atmptd_num_quiz_takers += 1;
                }
                else{
                    $quiz_data[$key] = 0;
                    array_push($time_taken, 0);
                    array_push($full_names, "");
                    array_push($profile_pics, "");
                    array_push($scores, 0);
                    array_push($usernames, "");
                }
            }

            array_multisort($quiz_data, SORT_DESC, $time_taken, SORT_ASC, $full_names, $profile_pics,$usernames, $scores);

            $count_row = 0;
            $rank = 0;
            $prev_score = NULL;
            $prev_time = NULL;

        }
        else{
            $num_quiz_takers = 0;
            $time_taken = array();
        }
    }
}else{ 

    $all_data_get_query = "SELECT * FROM resps";
    include('connect_db.php');
    $all_data_object = mysqli_query($db, $all_data_get_query);
    mysqli_close($db);

    if (mysqli_num_rows($all_data_object) == 0){
        $quiz_deleted = 1;
    }else{
        $quiz_deleted = 0;

        $all_data = mysqli_fetch_all($all_data_object, MYSQLI_ASSOC);

        if($all_data == NULL){
            $quiz_data = NULL;
            $time_taken = array();
            $num_quiz_takers = 0;
        }
        else{
            $table_keys = array_keys($all_data[0]);
            array_splice($table_keys, 0, 4);

            $num_quiz_takers = count($table_keys);

            $quiz_data = array();
            $time_taken = array();
            $full_names = array();
            $profile_pics = array();

            foreach($table_keys as $key){
                $quiz_data[$key] = 0;
                array_push($time_taken, 0);
                array_push($full_names, NULL);
                array_push($profile_pics, NULL);
            }

            foreach($all_data as $cell){
                $count = 0;
                foreach($table_keys as $key){
                    if($cell[$key] != NULL && strpos($cell[$key], ',') != false){
                        $quiz_data[$key] += (int)explode(',',$cell[$key])[2];
                        $time_taken[$count] += (int)explode(',',$cell[$key])[1];

                        if($full_names[$count] == NULL){
                            $full_names[$count] = explode(',',$cell[$key])[4];
                            $profile_pics[$count] = explode(',',$cell[$key])[5];   
                        }
                    }
                    $count += 1;
                }
            }
            array_multisort($quiz_data, SORT_DESC, $time_taken, SORT_ASC, $full_names, $profile_pics);

            $count_row = 0;
            $rank = 0;
            $prev_score = NULL;
            $prev_time = NULL;
        }
    }
}


if (isset($time_taken)){
    $time_taken_str = array();

    foreach(array_keys($time_taken) as $index){
        $seconds = $time_taken[$index];
        
        $hours = (floor($seconds/3600));

        if ($hours<10){
            $hours = '0'.(string)$hours;
        }
        else{
            $hours = (string)$hours;
        }

        $minutes = (floor(($seconds%3600)/60));

        if ($minutes<10){
            $minutes = '0'.(string)$minutes;
        }
        else{
            $minutes = (string)$minutes;
        }

        $seconds = (($seconds%3600)%60);

        if ($seconds<10){
            $seconds = '0'.(string)$seconds;
        }
        else{
            $seconds = (string)$seconds;
        }

        array_push($time_taken_str, $hours.':'.$minutes.':'.$seconds);

    }
}
?>

<?php if ($quiz_deleted==0): ?>
<html>
<head>
    <title>Leaderboard</title>
    <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="bootstrap\bootstrap-4.5.3-dist\css\bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="js/jquery-3.5.1.js"></script>
    <style>
        .table-head{
            background-color: rgb(134, 0, 0);
            color: white;
        }

        th{
            font-family: 'Abel', serif; 
        }

        .propic{
            width: 40px;
            height: 40px;
            border-radius: 50px;
            margin-top: -7px;
        }
    </style>

    <script>
        //function to reduce the seconds into the hh:mm:ss format
        function format_time(seconds){
            hours = (Math.floor(seconds/3600));
            if (hours<10){hours = '0'+hours;}
            minutes = (Math.floor((seconds%3600)/60));
            if (minutes<10){minutes = '0'+minutes;}
            seconds = ((seconds%3600)%60);
            if (seconds<10){seconds = '0'+seconds;}

            return hours+':'+minutes+':'+seconds;

        }

        const usernames = <?php echo json_encode($usernames); ?>;
        const scores = <?php echo json_encode($scores); ?>;
        const taken_times = <?php echo json_encode($time_taken); ?>;

        const loggedIn = parent.Discourse.User.current();
        var loggedIn_username = loggedIn.username;
        var loggedIn_name = loggedIn.name;
        var loggedIn_pic ='https://forumac.org' + loggedIn.avatar_template.replace('{size}','240');

        var rank_val = "?";
        var score_val = "?";
        var time_taken_val = "Attemt now!";

        var rank = 1;

        for (let i = 0; i < usernames.length; i++) {

            if (usernames[i]!=""){
                
                if (usernames[i]==loggedIn_username){
                    rank_val = rank.toString();
                    score_val = scores[i].toString();
                    time_taken_val = format_time(taken_times[i]).toString();
                    break;
                }else{
                    rank++;
                }
            }
        }

        $(document).ready(function(){

            document.getElementById('username').innerHTML = "Your results:";
            document.getElementById('username_row').innerHTML = loggedIn_name;
            document.getElementById('propic_row').src = loggedIn_pic;
            

            document.getElementById('rank_row').innerHTML = rank_val;
            document.getElementById('score_row').innerHTML = score_val;
            document.getElementById('time_taken_row').innerHTML = time_taken_val;
            
            document.getElementById('username').hidden = false;
        });
    </script>
</head>
<body>
    <h3 style="text-align: center;">LEADERBOARD</h3>
    <table class="table">
        <thead class="table-head">
            <tr>
                <th scope="col">#</th>
                <th></th>
                <th scope="col">User</th>
                <th scope="col">Score</th>
                <th scope="col">Time</th>
            </tr>
        </thead>

        <tbody>
            <?php if($quiz_data != NULL) : ?>
            <?php foreach($quiz_data as $key => $value) : ?>
                <?php if($time_taken[$count_row] != 0) : ?>
                <?php if($value != $prev_score || $prev_time != $time_taken[$count_row]){
                    $rank += 1;
                    $prev_score = $value;
                    $prev_time = $time_taken[$count_row];
                }
                ?>
                <tr>
                    <th scope="row"><?php echo $rank ?></th>
                    <th><img src="<?php echo $profile_pics[$count_row] ?>" alt="profile picture" class="propic"></th>
                    <th><?php echo $full_names[$count_row] ?></th>
                    <th><?php echo $value ?><?php if($_GET['quiz_name'] != 'quizOverall'): ?>% <?php endif ?></th>
                    <th><?php echo $time_taken_str[$count_row] ?></th>
                </tr>
                <?php endif ?>
                <?php 
                    $count_row += 1; 
                        
                    if( $rank > 10){
                        break;
                    }
                ?>
            <?php endforeach ?>
            <?php endif ?>
        
        </tbody>
    </table>
    <div>
        <p>Total number of attempts: <?php echo $atmptd_num_quiz_takers; ?></p>
        <div id = "username" hidden></div>
        <table class="table">
            <tbody>
                <tr>
                    <th scope="col" id = "rank_row"></th>
                    <th scope="col"><img  id = "propic_row" src="propic" alt="profile picture" class="propic"></th>
                    <th scope="col" id = "username_row"></th>
                    <th scope="col" id = "score_row"></th>
                    <th scope="col" id = "time_taken_row"></th>
                </tr>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php else : ?>
<html>
    <body>
        <p>The quiz has been deleted by the admins</p>
    </body>
</html>
<?php endif ?>

<?php endif ?>