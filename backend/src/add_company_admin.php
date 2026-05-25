<?php

require_once 'auth_check.php';
require_role('admin');

require_once 'header.php';
require_once 'db.php';

$stmt = $pdo -> query("SELECT id, name FROM Bus_Company ORDER BY name");
$companies = $stmt -> fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>Yeni Firma Admini Ekle</h1>
    <form action="/handle_add_company_admin.php" method="POST">
        <div class="form-group">
            <label for="full_name">Ad Soyad</label>
            <input type="text" id="full_name" name="full_name" required>
        </div>
        <div class="form-group">
            <label for="email">E-posta</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Şifre</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="company_id">Atanacak Firma</label>
            <select id="company_id" name="company_id" required>
                <option value="">-- Firma Seçin --</option>
                <?php foreach ($companies as $company): ?>
                    <option value="<?php echo htmlspecialchars($company['id']); ?>">
                        <?php echo htmlspecialchars($company['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit">Firma Admini Oluştur</button>
    </form>
</div>

<?php require_once 'footer.php'; ?>