<?php
$time_start = microtime(true);

$memcache = new Memcache;
$memcache->connect('localhost', 11211) or die ("Could not connect");
$empployees = $memcache->get('empployees');

$msg = 'mencache';
if (!$empployees) {
  // New Connection
  $link = new mysqli("localhost", "root", "admin", "thegeekstuff");

  // Check for errors
  if ($link->connect_errno) {
      echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
  }

  $empployees = array();
  $result = $link->query("SELECT * FROM employee");
  
  // Cycle through results
  while($empployee = $result->fetch_object()){ 
      array_push($empployees, $empployee);
  } 
  
  $result->close(); // Free result set
  $link->close(); // Close connection

  $memcache->set('empployees', $empployees, false, 30) or die ("Failed to save data at the server");
  $msg = 'database';
} 

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Memcached</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container" >
      <!-- Header -->
      <div class="page-header">
        <blockquote><h1>PHP com Memcahced<small>otimização de aplicações web usando servidor de cache.</small></h1></blockquote>
      </div>      
      
      <!-- Default panel table -->
      <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading">Table employees</div>
        <!-- Table -->
        <table class="table">
          <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Dept</th>
            <th>Salary</th>
          </tr>
          <?php foreach ($empployees as $empployee) { ?>
          <tr>
            <td><?= $empployee->id ?></td>
            <td><?= $empployee->name ?></td> 
            <td><?= $empployee->dept ?></td>
            <td><?= $empployee->salary ?></td>
          </tr>
          <?php } ?>
        </table>
      </div>

      <?php 
        $time_end = microtime(true);
        $time = $time_end - $time_start;
      ?>

      <!-- Alert -->
      <div class="alert <?= ($msg == 'database') ? 'alert-danger' : 'alert-success' ?>" role="alert">
        <a href="#" class="alert-link"><?= 'Tempo de carregamento usando '.$msg.' '.$time.' segundos' ?></a>
      </div>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="assets/js/bootstrap.min.js"></script>
  </body>
</html>


