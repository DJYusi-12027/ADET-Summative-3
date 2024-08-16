<?php
	session_start();
	$todoList = array();
	$doneList = array(); // data structure for finished tasks

	if (isset($_SESSION["todoList"])) $todoList = $_SESSION["todoList"];
	if (isset($_SESSION["doneList"])) $doneList = $_SESSION["doneList"];

	function appendData($data)
	{
		return $data;
	}
	
	function deleteData($toDelete, $todoList) //edited to have more operators
	{
    if (isset($_GET['confirm']) && $_GET['confirm'] === 'true' && $_GET['task'] === $toDelete)
	{
      foreach ($todoList as $index => $taskName)
	  {
        if ($taskName === $toDelete)
		{
          unset($todoList[$index]);
          return $todoList;
        }
      }
    } else if (isset($_GET['task']) && $_GET['task'] === $toDelete)
	{
      echo '<div class="alert alert-warning"><b>Are you sure you want to delete </b>"' . $toDelete . '"<b>?</b>';
      echo '<a href="index.php?confirm=true&task=' . $toDelete . '" class="btn btn-danger ml-2">Yes</a>';
      echo '<a href="index.php" class="btn btn-secondary ml-2">No</a></div>';
    }
    return $todoList;
  }

// added function
	function markDone($taskToMark, &$todoList, &$doneList)
	{
		$taskIndex = array_search($taskToMark, $todoList);
		{
			// predefined functions (date and unset)
			$currentTime = date("Y-m-d H:i:s");
			$doneList[] = "<b>DONE </b>- " . $currentTime . ": " . $todoList[$taskIndex];
			unset($todoList[$taskIndex]);
		}
	}
	
	if($_SERVER["REQUEST_METHOD"] =="POST")
	{
		if (empty( $_POST["task"] ))
		{
			echo '<script>alert("Error: there is no data to add in array")</script>';
			exit;
		}
 
		array_push($todoList, appendData($_POST["task"]));
		$_SESSION["todoList"] = $todoList;
	}

	if (isset($_GET['task']) && isset($_GET['markDone']))
	{
		if ($_GET['markDone'] === "true")
		{
			markDone($_GET['task'], $todoList, $doneList);
			$_SESSION["todoList"] = $todoList;
			$_SESSION["doneList"] = $doneList;
			echo '<div class="alert alert-success"><b>Task successfully marked as done!</b></div>'; // task done message
		}
	}
	
	if (isset($_GET['task']))
	{
		$todoList = deleteData($_GET['task'], $todoList);
		$_SESSION["todoList"] = $todoList; // deletes task when finished or deleted by choice
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Simple To-Do List</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #FF9800;  /* Light orange background */
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    h1 {
      color: #B80000;  /* Dark red heading */
    }

    .card {
      border-radius: 8px;
      background-color: #B80000;  /* Dark red card background */
      border-color: #FF9800;  /* Light orange card border to create a subtle outline */
    }

	.alert-container {		
		position: relative;
		min-height: 100vh;
		padding-top: 70px; 
	}

    .alert-warning,
	.alert-success{
		background-color: #B80000;  /* Dark red card background */
		border-color: #FF9800;
		color: #ffc300;
		position: fixed;
		top: 10px;
		left: 50%;
		transform: translateX(-50%);
		z-index: 1000; 
	}
	
    .card-header {
      font-family: 'Helvetica';
      color: #FF9800;  /* Light orange text for headers */
      background-color: #5F8670;  /* Dark blue header background */
    }

    .btn-primary,
    .btn-success,
    .btn-danger,
	.btn-secondary{
      background-color: #5F8670;  /* Dark blue buttons */
      border-color: #5F8670;
      color: #ffc300;  /* Light orange button text */
    }

    .btn-primary:hover {
      background-color: #820300; /* Darker red on button hover */
    }

    .form-control {
      background-color: #FF9800; 
      border-color: #B80000;
      color: #B80000; 
    }

    .form-control::placeholder {
      color: #B80000;
    }

    .form-label {
      position: absolute;
      top: 50%;
      left: 10px;
      transform: translateY(-50%);
      color: #B80000;  /* Dark red color for label text */
      pointer-events: none;  /* Prevent clicks on the label */
      transition: opacity 0.5s ease-in-out;  /* Smooth fade-out animation */
    }

    /* Hide the label when the user starts typing */
    .form-control:focus + .form-label {
      opacity: 0;
    }

    /* Apply styles to list items within the cards */
    .list-group-item {
		font-family: 'Helvetica';
		background-color: #FF9800;  /* Light orange background for list items */
		color: #B80000;  /* Dark red text for list items */
		border-color: #B80000;  /* Dark red border for list items */
    }
  </style>
</head>
<body>

  <div class="container mt-5">
    <h1 class="text-center">To-Do List</h1>
    <div class="card">
      <div class="card-header"><b>Add a New Task</b></div>
      <div class="card-body">
        <form method="post" action="">
          <div class="form-group">
            <input type="text" class="form-control" name="task" placeholder="Enter your task here">
          </div>
          <button type="submit" class="btn btn-primary"><b>Add Task</b></button>
        </form>
      </div>
    </div>

    <div class="card mt-4">
      <div class="card-header"><b>Tasks</b></div>
      <ul class="list-group list-group-flush">
        <?php
          foreach ($todoList as $task) {
            echo '<div class="d-flex p-2 bd-highlight w-100 justify-content-between"> <li class="list-group-item w-100">' . $task . '
                </li><a href="index.php?delete=true&task=' . $task . '" class="btn btn-danger">Delete</a><a href="index.php?markDone=true&task=' . $task . '" class="btn btn-success">Mark Done</a></div>';
          }
        ?>
      </ul>
    </div>

    <div class="card mt-5">
      <div class="card-header"><b>Completed Tasks</b></div>
      <ul class="list-group list-group-flush">
        <?php
        foreach ($doneList as $doneTask) {
          echo '<div class="d-flex p-2 bd-highlight w-100 justify-content-between"> <li class="list-group-item w-100">' . $doneTask . '</li></div>';
        }
        ?>
      </ul>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
