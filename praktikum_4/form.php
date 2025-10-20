<?php
include 'koneksi_2.php'; 
$nameErr = $emailErr = $genderErr = "";
$name = $email = $website = $comment = $gender = "";
$output_data = "";

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $all_valid = true;


    if (empty($_POST["name"])) {
        $nameErr = "Nama wajib diisi";
        $all_valid = false;
    } else {
        $name = test_input($_POST["name"]);

        if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
            $nameErr = "Hanya huruf dan spasi yang diizinkan";
            $all_valid = false;
        }
    }


    if (empty($_POST["email"])) {
        $emailErr = "Email wajib diisi";
        $all_valid = false;
    } else {
        $email = test_input($_POST["email"]);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Format email tidak valid";
            $all_valid = false;
        }
    }


    if (!empty($_POST["website"])) {
        $website = test_input($_POST["website"]);

        if (!filter_var($website, FILTER_VALIDATE_URL)) {
            $website = ""; 
        }
    }

    
    if (empty($_POST["gender"])) {
        $genderErr = "Gender wajib dipilih";
        $all_valid = false;
    } else {
        $gender = test_input($_POST["gender"]);
    }

    
    $comment = test_input($_POST["comment"]);


    
    if ($all_valid) {
    
        $stmt = $conn->prepare("INSERT INTO users (name, email, website, comment, gender) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $website, $comment, $gender);

        if ($stmt->execute()) {
            $output_data = "Data berhasil disimpan ke database!";
        } else {
            $output_data = "Error: " . $stmt->error;
        }
        
        $stmt->close();
        
        $output_data .= "<br><br><h3>Hasil Input Anda:</h3>";
        $output_data .= "<b>Name:</b> " . $name . "<br>";
        $output_data .= "<b>E-mail:</b> " . $email . "<br>";
        $output_data .= "<b>Website:</b> " . ($website ? $website : "-") . "<br>";
        $output_data .= "<b>Comment:</b> " . ($comment ? $comment : "-") . "<br>";
        $output_data .= "<b>Gender:</b> " . $gender . "<br>";

        $name = $email = $website = $comment = $gender = "";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>PHP Form Validation Example</title>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>

<h2>PHP Form Validation Example</h2>
<p><span class="error">* required field</span></p>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
  Name: <input type="text" name="name" value="<?php echo $name;?>">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>
  E-mail: <input type="text" name="email" value="<?php echo $email;?>">
  <span class="error">* <?php echo $emailErr;?></span>
  <br><br>
  Website: <input type="text" name="website" value="<?php echo $website;?>">
  <br><br>
  Comment: <textarea name="comment" rows="5" cols="40"><?php echo $comment;?></textarea>
  <br><br>
  Gender:
  <input type="radio" name="gender" <?php if (isset($gender) && $gender=="Female") echo "checked";?> value="Female">Female
  <input type="radio" name="gender" <?php if (isset($gender) && $gender=="Male") echo "checked";?> value="Male">Male
  <input type="radio" name="gender" <?php if (isset($gender) && $gender=="Other") echo "checked";?> value="Other">Other
  <span class="error">* <?php echo $genderErr;?></span>
  <br><br>
  <input type="submit" name="submit" value="Submit">  
</form>

<hr>

<h3>Your Input:</h3>
<p>
<?php 

echo $output_data;
?>
</p>

</body>
</html>

<?php

$conn->close();
?>