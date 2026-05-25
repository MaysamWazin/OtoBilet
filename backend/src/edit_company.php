<?php

require_once 'auth_check.php';
require_role('admin');

require_once 'header.php';
require_once 'db.php';

$id = $_GET['id'] ?? '';

if(!$id){
    die("id değeri belirtilmedi");
}

$stmt = $pdo -> prepare("SELECT * FROM Bus_Company WHERE id = ?");
$stmt -> execute([$id]);
$company = $stmt -> fetch(PDO::FETCH_ASSOC);

if(!$company) {
    die("Firma bulunamadı");
}

?>

<div class= "container">
    <h1>Firma Düzenle</h1>
    <form action="/handle_edit_company.php" method = "POST">
        <input type="hidden" name = "id" value = "<?php echo htmlspecialchars($company['id']); ?>">
        
        <div class = "form-group">
            <label for ="company_name">Firma Adı</label>
            <input type = "text" id = "company_name" name = "company_name" value = "<?php echo htmlspecialchars($company['name']); ?>" required>
</div>

<button type="submit" >Kaydet</button>
</form>
</div>

<?php require_once 'footer.php'; ?>