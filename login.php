<?php
    require_once "pdo.php";
    require_once "head.php";
    session_start();
    /*
    Construct
    */
    $host = $_SERVER['HTTP_HOST'];
    $ruta = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $url = "http://$host$ruta"; 
    
if (isset($_POST["cancel"])) {
    header("Location: $url/index.php");
    die();
}

    /*
    verification
    */
if (isset($_POST["email"]) && isset($_POST["pass"])) {
    unset($SESSION["name"]);
    unset($SESSION["user_id"]);
        
    $salt = 'XyZzy12*_';

    $check = hash("md5", $salt . $_POST["pass"]);

    $stmt = $pdo->prepare(
        'SELECT user_id, name
        FROM users
        WHERE
        email = :em AND
        password = :pw'
    );
    $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row !== false) {
        error_log("Login success " . $_POST['email'] . "\n", 3, "logs.log");
        $_SESSION["user_id"] = $row["user_id"];
        $_SESSION["name"] = $row["name"];
        header("Location: $url/index.php");
        die();
    } else {
        $_SESSION["error"] = "Incorrect email or password";
        error_log("Login fail " .$_POST['email']. " $check" . "\n", 3, "logs.log");
        header("Location: $url/login.php");
        die();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Pierre Demian</title>
<?php require_once "head.php"; ?>
</head>
<body>
<div class="container">
<h1><?= $profile_id ? "Editing" : "Adding" ?>
 Profile for <?= htmlentities($_SESSION['name']); ?></h1>
<?php
flashMessages();

echo('<form method="post" action="form.php">');
echo('<div id="profile"><img src="spinner.gif"></div>');
if ( $profile_id ) {
    echo('<input type="hidden" name="profile_id" value="'.htmlentities($profile_id).'" />');
}

echo('<p>Education: <input type="submit" id="addEdu" value="+" style="display:none;">'."\n");
echo('<div id="edu_fields">'."\n");
echo('<img src="spinner.gif">');
echo("</div></p>\n");
echo('<p>Position: <input type="submit" id="addPos" value="+" style="display:none;">'."\n");
echo('<div id="position_fields">'."\n");
echo('<img src="spinner.gif">');
echo("</div></p>\n");
?>
    <form method="post">
        <label>Email</label>
        <input type="text" name="email" autocomplete="off" id="id_email">
        <br>
        <label>Password</label>
        <input type="password" name="pass" id="id_1723">
        <br>
        <input type="submit" onclick="return doValidate();" value="Log In">
        <input type="submit" name="cancel" value="Cancel">
    </form>
    <p>
        If you don't know which password to use, check the code comments
        <!-- Email is 'umsi@umich.edu' and the password is 'php123' -->
    </p>
    <script>
        function doValidate()
        {
            console.log("Validating...");
            try
            {
                email = document.getElementById("id_email").value;
                pw = document.getElementById("id_1723").value;
                console.log("Validating email="+email);
                console.log("Validating pw="+pw);
                if (pw == null || pw == "" || email == null || email == "")
                {
                    alert("Both fields must be filled out");
                    return false;
                }
                if(email.search("@") === -1)
                {
                    alert("Email address must contain @");
                    return false;
                }
                return true;
            }
            catch(e)
            {
                return false;
            }
            return false;
        }
    </script>
</body>
</html>
