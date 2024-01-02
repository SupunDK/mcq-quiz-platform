<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location: login.php");
}

function sortById($record_1, $record_2){
    if( $record_1['id'] < $record_2['id']){
        return 1;
    }
    else{
        return 0;
    }
}

$get_all_data_query = "SELECT * FROM resps";

include ('connect_db.php');
$data_object = mysqli_query($db, $get_all_data_query);
mysqli_close($db);

$data = mysqli_fetch_all($data_object, MYSQLI_BOTH);

usort($data, 'sortById');

?>
  
<html>
<head>
    <link href='https://fonts.googleapis.com/css?family=Cairo' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Teko' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Bebas+Neue' rel='stylesheet' type='text/css'>
    <link href="bootstrap\bootstrap-toggle-master\css\bootstrap-toggle.css" rel="stylesheet">
    <link rel="stylesheet" href="bootstrap\bootstrap-4.5.3-dist\css\bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    
    <title>Edit Quizes</title>
   
    <script src="js\jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="bootstrap\bootstrap-toggle-master\js\bootstrap-toggle.js"></script>
    <script src="js/jquery-3.5.1.js"></script>
    <script type="text/javascript">
        function generateIframeLeaderboard(quiz_id_name){
            alert('please copy the following embeding link and paste in the forumAC post.  <iframe src="https://forumac.org/quiz/leaderboard.php?quiz_name='+quiz_id_name+'" width="100%" height="500px" frameborder="0" style="margin: auto; display: block; width: 100%; height: 700px; border-left: 1px solid lightsteelblue; border-right: 1px solid lightsteelblue;">Loading....</iframe>');
        }

        function generateIframeQuiz(quiz_id_name){
            alert('please copy the following embeding link and paste in the forumAC post.  <iframe src="https://forumac.org/quiz/quizes.php?quiz_name='+quiz_id_name+'" width="100%" height="500px" frameborder="0" style="margin: auto; display: block; width: 100%; height: 700px; border-left: 1px solid lightsteelblue; border-right: 1px solid lightsteelblue;">Loading....</iframe>');
        }

        function deleteQuiz(quiz_id_name){    
            var password = window.prompt("Enter your password to confirm the deletion.");

            $.post("server.php",{
                delete_quiz_id_name: quiz_id_name,
                user_password:password
            },function(data,status){
                result = jQuery.parseJSON(data);
                if (result.action == 'deleted'){
                    location.reload();
                }
            });
            
        }

        function toggleStatus(quiz_status, returned_quiz_id_name){
            if (quiz_status == true){
                quiz_status = '1';
            }else{
                quiz_status = '0';
            }

            $.post("server.php",{
                open_close_quiz:quiz_status,
                quiz_id_name:returned_quiz_id_name
            },function(data,status){

            });
            
        }

    </script>
    <style>
        .container-head{
            text-align: center;
            padding: 20px;
        }

        .container{
            padding-bottom: 20px;
        }

        .table-head{
            background-color: rgb(134, 0, 0);
            color: white;
        }
        
        h1{
            font-family: "Bebas Neue", serif;
            font-size: 50px;
        }

        h3{
            font-family: "Teko", serif;
        }

        th{
            text-align: center;
            font-family: "Cairo", serif;
        }

        .toggle{
            border-radius: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="container-head">
            <h1>Admin Panel</h1>
            <h3>- Quiz Info -</h3>
        </div>
        <br>
        <table class="table">
            <thead class="table-head">
                <tr>
                    <th>Status</th>                        
                    <th>Quiz Name</th>
                    <th>Quiz Id</th>
                    <th>Quiz</th>
                    <th>Leaderboard</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tr>
                <th><input type="checkbox" checked data-toggle="toggle" data-on="Enabled" data-off="Enabled" data-onstyle="success" data-offstyle="success"></th>
                <th>Overall Rankings</th>
                <th>quizOverall</th>
                <th>None</th>
                <th><button class="btn btn-primary" onclick = "generateIframeLeaderboard('quizOverall')">Generate</button></th>
                <th><button class="btn btn-danger" onclick = "deleteQuiz('quizOverall')">Delete All</button></th>
            </tr>
            <?php foreach($data as $cell) : ?>
            <tr>
                <th>
                    <?php if($cell['status'] == 1) : ?>
                        <input type="checkbox" checked data-toggle="toggle" data-on="Enabled" data-off="Disabled" data-onstyle="success" data-offstyle="danger" onchange="toggleStatus(checked, '<?php echo $cell['quiz_id_name'] ?>')">
                    <?php endif ?>

                    <?php if($cell['status'] == 0) : ?>
                        <input type="checkbox" data-toggle="toggle" data-on="Enabled" data-off="Disabled" data-onstyle="success" data-offstyle="danger" onchange="toggleStatus(checked, '<?php echo $cell['quiz_id_name'] ?>')">
                    <?php endif ?>
            
                </th>
                <th><?php echo $cell['quiz_name'] ?></th>
                <th><?php echo $cell['quiz_id_name']?></th>
                <th><button class="btn btn-primary" onclick="generateIframeQuiz('<?php echo $cell['quiz_id_name']?>')">Generate</button></th>
                <th><button class="btn btn-primary" onclick="generateIframeLeaderboard('<?php echo $cell['quiz_id_name']?>')">Generate</button></th>
                <th><button class="btn btn-danger" style="width:90px" onclick="deleteQuiz('<?php echo $cell['quiz_id_name']?>')">Delete</button></th>
            </tr>
            <?php endforeach ?>
        </table>
    <br>
    <br>
    <center><button class="btn btn-outline-primary" onclick="location.href = 'index.php'">Back to Main Page</button></center>
    </div>
</body>
</html>