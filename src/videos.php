<html>
<head>
</head>
<body>

<h1>Movie Library</h1><br/>
	
	<form method = "POST" action="">
		Title: <input type="text" name="title" required><br/>
		Category: <input type="text" name="category"><br/>
		Length: <input type="text" name="length" min="1"><br/>
		<input type="submit" name="addMovie" value="Add Movie"><br/>
	</form>
<br/><br/><h2>Movie List</h2><br/><br/>
<form action = "" method="POST">
	<input type="submit" name="deleteAll" value="Delete All Inventory">
</form>

<?php

error_reporting(E_ALL);
  ini_set('display_errors','On');
$host = "oniddb.cws.oregonstate.edu";
$db = "hengs-db";
$dbuser = "hengs-db";
$pw = "DXB2D0DXn9smG486";
//include "login.php";

$con = new mysqli($host, $db, $pw, $dbuser);
if (!$con|| $con->connect_errno) {
    echo "Connection Failure: (" . $con->connect_errno . ") " . $con->connect_error;
    exit(1);
}
if(isset($_POST['addMovie'])){
	//$_POST['available']=1;
$AddQuery = $con->prepare ("INSERT INTO MyVideos (Title, Category, Length) VALUES ('$_POST[title]','$_POST[category]','$_POST[length]')");         
//mysql_query($AddQuery, $con);

$AddQuery->execute();
$AddQuery->close();
};

if(isset($_POST['deleteAll'])){
$DeleteAllQuery = $con->prepare("TRUNCATE TABLE MyVideos");          
//mysql_query($DeleteALLQuery, $con);
$DeleteAllQuery->execute();
$DeleteAllQuery->close();
echo "All videos deleted!!!";
};

if(isset($_POST['checkout'])){
	$UpdateQuery = $con->prepare("UPDATE MyVideos SET Available = 1  WHERE id ='$_POST[checkout]'");               
	$UpdateQuery->execute();
	$UpdateQuery->close();
	echo "Movie has now been checked out.<br/>";
};

if(isset($_POST['checkin'])){
	$UpdateQuery1 = $con->prepare("UPDATE MyVideos SET Available = 0  WHERE id ='$_POST[checkin]'");               
	$UpdateQuery1->execute();
	$UpdateQuery1->close();
	echo "Movie has now been checked in.<br/>";
};

if(isset($_POST['delete'])) {
	$DeleteQuery = $con->prepare("DELETE FROM MyVideos WHERE id = '$_POST[delete]'");
	$DeleteQuery->execute();
	$DeleteQuery->close();
	echo "You have deleted your video <br/>";
	}


$stmt = $con->prepare("SELECT DISTINCT category FROM MyVideos");
$stmt->execute();
	
	$catList= NULL;
	$stmt->bind_result($catList);

	echo '<form method="POST">
	<select name="lists">
		<option value="allMovies" name="allMovies">All Movies</option>';
	
		while($stmt->fetch()){
			$list = $catList;
			if ($list != NULL) {
				echo '<option value="'.$list.'">'.$list.'</option>';
			}
		}
	echo '</select><input type="submit" value="filter"></form>';
	
	$stmt->close();

	

if (isset($_POST["lists"])) {
      if($_POST["lists"] == "allMovies"){
      	$catFilter = "SELECT id, Title, Category, Length, Available FROM MyVideos";
    }
   	 else {
     
      $catFilter = "SELECT id, Title, Category, Length, Available FROM MyVideos WHERE Category = '".$_POST["lists"]."'";
    }}
 else{$catFilter ="SELECT id, Title, Category, Length, Available FROM MyVideos";}


 $CatStmt = $con->prepare($catFilter);
 $CatStmt->execute();
 
$uid = NULL;
$utitle = NULL;
$ucategory = NULL;
$ulength = NULL;
$uavailable = NULL;

$CatStmt->bind_result($uid, $utitle, $ucategory, $ulength, $uavailable);


	echo '<table border = 1>Movie List<br/>';
	echo '<th>Title</th><th>Category</th><th>Length(min)</th><th>Availablity</th><th>Delete</th><th>Check In/Out</th>';
	while($CatStmt->fetch()){
		if($uavailable===NULL || $uavailable===0){

		echo "<tr><td>".$utitle."</td><td>".$ucategory."</td><td>".$ulength."</td><td>Available</td>";
		echo '<form action = "videos.php" method="POST">';
		echo "<td><input type='hidden' name='delete' value=".$uid."><input type='submit' value='Deletes' name='delete1'></td></form>";
		echo '<form action = "videos.php" method="POST">';
		echo "<td><input type='hidden' name='checkout' value=".$uid."><input type='submit' value='Check Out' name='checkout1'></td></tr></form>";

		}
		else{

		echo "<tr><td>".$utitle."</td><td>".$ucategory."</td><td>".$ulength."</td><td>Unavailable</td>";
		echo '<form action = "videos.php" method="POST">';
		echo "<td><input type='hidden' name='delete' value=".$uid."><input type='submit' value='Deletes' name='delete1'></td></form>";
		echo '<form action = "videos.php" method="POST">';
		echo "<td><input type='hidden' name='checkin' value=".$uid."><input type='submit' value='Check In' name='checkin1'></td></tr></form>";


	}}
echo "</table>";

$CatStmt->close();

?>


</body>
</html>