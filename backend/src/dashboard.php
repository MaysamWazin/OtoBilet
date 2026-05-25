<?php 


require_once 'auth_check.php';

?>
<!DOCTYPE html>
<html lang= "tr">
<head>
<meta charset="UTF-8">
<title>Panel</title>

</head>

<body>
    <h1>Hoş Geldin, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>

    <p> Kullanıcı Rolün: <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong></p>

    <a href="/logout.php">Çıkış Yap</a>

</body>
</html>