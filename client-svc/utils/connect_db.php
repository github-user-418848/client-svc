<?php

	require_once 'config.php';

	$hostname = DB_HOST;
	$dbname = DB_DATABASE;
	$username = DB_USERNAME;
	$passwd = DB_PASSWORD;

	$hostname2 = DB_HOST_TWO;
	$dbname2 = DB_DATABASE_TWO;
	$username2 = DB_USERNAME_TWO;
	$passwd2 = DB_PASSWORD_TWO;

	// Are PDO prepared statements sufficient to prevent SQL injection?
	// The short answer is NO, PDO prepares will not defend you from all possible SQL-Injection attacks. For certain obscure edge-cases.
	// The long answer isn't so easy. It's based off an attack demonstrated here: http://shiflett.org/blog/2006/jan/addslashes-versus-mysql-real-escape-string

	// Some safe example:
	// $pdo = new PDO('mysql:host=localhost;dbname=testdb;charset=utf8', $user, $password);
	// $stmt = $pdo->prepare('SELECT * FROM test WHERE name = ? LIMIT 1');
	// $stmt->execute(array("\xbf\x27 OR 1=1 /*"));

	try {

		$con = new PDO("mysql:host=$hostname;dbname=$dbname;charset=utf8", $username, $passwd);
		$con -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$con -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$con2 = new PDO("sqlsrv:Server=$hostname2;Database=$dbname2", $username2, $passwd2);
		$con2 -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$con2 -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		// $remote_data = query::msSqlSelectQuery("SELECT * FROM [dbo].[TBL_QUEUING_NUMBERS] WHERE [QUEUE_DATE] = '".date("Y-m-d")."'", $con2);
		// $queue = $remote_data["SERVICE_PRIORITY"];
		// query::msSqlInsertQuery("UPDATE [dbo].[TBL_QUEUING_NUMBERS] SET [SERVICE_PRIORITY]=$queue+1 WHERE [QUEUE_DATE] = '".date("Y-m-d")."'", $con2);
		// echo $queue;
		// Queue num generate

		// $stmt = $con2 -> prepare("DELETE FROM [dbo].[TBL_QUEUING_NUMBERS] WHERE [QUEUE_DATE] = '".date("Y-m-d")."'");
		// $stmt -> execute();
		
		// // priority client
		// $queue = $data["SERVICE_PRIORITY"] += 1;
		// $stmt = $con2 -> prepare("UPDATE [dbo].[TBL_QUEUING_NUMBERS] SET [SERVICE_PRIORITY]=$queue WHERE [QUEUE_DATE] = '".date("Y-m-d")."'");
		// // regular client
		// $queue = $data["SERVICE_REGULAR"] += 1;
		// $stmt = $con2 -> prepare("UPDATE [dbo].[TBL_QUEUING_NUMBERS] SET [SERVICE_REGULAR]=$queue WHERE [QUEUE_DATE]='".date("Y-m-d")."'");
		// $stmt -> execute();

		// while ($data = $stmt ->fetch()) {
		// 	echo $data[0] . $data[1] ."<br>";
		// }
	}
	catch(PDOException $e) {
		echo "Connection failed: " . $e->getMessage();
	}

?>