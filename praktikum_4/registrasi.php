<?php
$host = "localhost:3307"; 
$user = "root";      
$pass = "";          
$db = "latihan_4"; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

$message = ""; 
$form_data = []; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil dan sanitasi data input
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $address = $conn->real_escape_string($_POST['address']);
    $postal_code = $conn->real_escape_string($_POST['postal_code']);
    $telephone_number = $conn->real_escape_string($_POST['telephone_number']);
    
    // Asumsi Place of Birth dan Date of Birth di-split, lalu di-combine
    $place_of_birth = $conn->real_escape_string($_POST['place_of_birth']);
    $date_of_birth = $conn->real_escape_string($_POST['date_of_birth']);
    
    $gender = $conn->real_escape_string(isset($_POST['gender']) ? $_POST['gender'] : '');
    
    // Logika untuk Agama (Religion)
    $religion_temp = isset($_POST['religion']) ? $_POST['religion'] : '';
    $others_religion = $conn->real_escape_string($_POST['others_religion']);
    $religion = ($religion_temp == 'Others') ? 'Others: ' . $others_religion : $religion_temp;
    
    $attended_school = $conn->real_escape_string($_POST['attended_school']);

    // 2. Query INSERT data ke database (Menggunakan Prepared Statements)
    $sql = "INSERT INTO pendaftar (full_name, address, postal_code, telephone_number, place_of_birth, date_of_birth, gender, religion, attended_school)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $full_name, $address, $postal_code, $telephone_number, $place_of_birth, $date_of_birth, $gender, $religion, $attended_school);

    if ($stmt->execute()) {
        $message = "✅ **Registrasi Berhasil!** Data Anda telah tersimpan ke database.";
        
        // Simpan data untuk ditampilkan di hasil form
        $form_data = $_POST;
        $form_data['religion'] = $religion; // Gunakan nilai agama yang sudah diproses

    } else {
        $message = "❌ **Error:** Terjadi kesalahan saat menyimpan data: " . $stmt->error;
    }

    $stmt->close();
}

// Tutup koneksi setelah selesai berinteraksi dengan database
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Registrasi English Course</title>
    <style>
        /* CSS dasar untuk merapikan tampilan */
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { 
            width: 650px; 
            margin: 50px auto; 
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 30px; 
            border-radius: 8px;
        }
        h2, h3 { text-align: center; margin: 5px 0; }
        h3 { color: #555; }
        hr { margin-bottom: 20px; border: 0; border-top: 1px solid #ccc; }
        .form-row { 
            display: flex; 
            align-items: center; 
            margin-bottom: 12px;
        }
        .form-row label { 
            width: 220px; /* Lebar label untuk keseragaman */
            text-align: left; 
            font-weight: bold;
        }
        .form-row input[type="text"], 
        .form-row input[type="date"] { 
            padding: 8px; 
            border: 1px solid #ddd; 
            border-radius: 4px;
            box-sizing: border-box;
            flex: 1;
        }
        .form-row input[type="radio"] { 
            margin-right: 5px; 
        }
        .radio-group label {
            font-weight: normal;
            width: auto;
            margin-right: 15px;
        }
        .submit-container { 
            text-align: right; 
            margin-top: 25px; 
        }
        .submit-container button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .submit-container button:hover {
            background-color: #0056b3;
        }
        .result-box { 
            border: 1px solid #28a745; 
            padding: 15px; 
            background-color: #e9f7ee; 
            margin-top: 20px; 
            border-radius: 5px;
            color: #155724;
        }
        .result-box ul { list-style: none; padding-left: 0; }
        .result-box li { margin-bottom: 5px; }
        .result-box strong { display: inline-block; width: 180px; }
    </style>
</head>
<body>

<div class="container">
    <h2>REGISTRATION FORM</h2>
    <h3>ENGLISH COURSE</h3>
    <hr>

    <?php 
    // Bagian untuk menampilkan PESAN dan HASIL form setelah submit
    if ($message) {
        echo "<div class='result-box'>$message";
        
        if (!empty($form_data)) {
            echo "<h4>Detail Data Pendaftaran:</h4>";
            echo "<ul>";
            echo "<li><strong>1. Full Name:</strong> " . htmlspecialchars($form_data['full_name']) . "</li>";
            echo "<li><strong>2. Address:</strong> " . htmlspecialchars($form_data['address']) . "</li>";
            echo "<li><strong>Postal Code:</strong> " . htmlspecialchars($form_data['postal_code']) . "</li>";
            echo "<li><strong>3. Telephone Number:</strong> " . htmlspecialchars($form_data['telephone_number']) . "</li>";
            echo "<li><strong>4. Place/Date of Birth:</strong> " . htmlspecialchars($form_data['place_of_birth']) . " / " . htmlspecialchars($form_data['date_of_birth']) . "</li>";
            echo "<li><strong>5. Gender:</strong> " . htmlspecialchars($form_data['gender']) . "</li>";
            echo "<li><strong>6. Religion:</strong> " . htmlspecialchars($form_data['religion']) . "</li>";
            echo "<li><strong>7. Attended School at:</strong> " . htmlspecialchars($form_data['attended_school']) . "</li>";
            echo "</ul>";
        }
        echo "</div>";
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        
        <div class="form-row">
            <label for="full_name">1. Full Name</label>
            <input type="text" id="full_name" name="full_name" required>
        </div>

        <div class="form-row">
            <label for="address">2. Address</label>
            <input type="text" id="address" name="address" required>
            <span style="width: 100px; margin-left: 10px; font-weight: bold;">Postal Code:</span>
            <input type="text" name="postal_code" style="width: 100px; flex: unset;" required>
        </div>

        <div class="form-row">
            <label for="telephone_number">3. Telephone Number</label>
            <input type="text" id="telephone_number" name="telephone_number" required>
        </div>
        
        <div class="form-row">
            <label>4. Place/Date of Birth</label>
            <input type="text" name="place_of_birth" placeholder="Place of Birth" required style="width: 150px;">
            <span style="margin: 0 5px;">/</span>
            <input type="date" name="date_of_birth" required style="width: 150px;">
        </div>

        <div class="form-row radio-group">
            <label>5. Gender</label>
            <input type="radio" id="male" name="gender" value="Male" required>
            <label for="male">Male</label>
            <input type="radio" id="female" name="gender" value="Female">
            <label for="female">Female</label>
        </div>

        <div class="form-row radio-group" style="flex-wrap: wrap;">
            <label>6. Religion</label>
            
            <input type="radio" id="muslim" name="religion" value="Muslim" required>
            <label for="muslim">Muslim</label>
            
            <input type="radio" id="christian" name="religion" value="Christian">
            <label for="christian">Christian</label>

            <input type="radio" id="hinduism" name="religion" value="Hinduism">
            <label for="hinduism">Hinduism</label>

            <input type="radio" id="buddhism" name="religion" value="Buddhism">
            <label for="buddhism">Buddhism</label>

            <input type="radio" id="others" name="religion" value="Others">
            <label for="others" style="margin-left: 5px;">Others:</label>
            <input type="text" name="others_religion" style="width: 100px; flex: unset;">
        </div>

        <div class="form-row">
            <label for="attended_school">7. Attended School at</label>
            <input type="text" id="attended_school" name="attended_school" required>
        </div>

        <div class="submit-container">
            <button type="submit">Submit</button>
        </div>
    </form>
</div>

</body>
</html>