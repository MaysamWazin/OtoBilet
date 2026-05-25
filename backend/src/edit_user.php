<?php
require_once 'auth_check.php';
require_role('admin');

require_once 'header.php';
require_once 'db.php';

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    die('Kullanıcı ID\'si belirtilmedi.');
}

// 1. Düzenlenecek kullanıcının bilgilerini çek
$stmt_user = $pdo->prepare("SELECT * FROM User WHERE id = ? AND role = 'company'");
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('Firma admini bulunamadı.');
}

// 2. Dropdown menüsü için tüm firmaların listesini çek
$stmt_companies = $pdo->query("SELECT id, name FROM Bus_Company ORDER BY name");
$companies = $stmt_companies->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>Firma Admini Düzenle</h1>
    <form action="/handle_edit_user.php" method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
        
        <div class="form-group">
            <label for="full_name">Ad Soyad</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">E-posta</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Yeni Şifre (Değiştirmek istemiyorsanız boş bırakın)</label>
            <input type="password" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="company_id">Atanacak Firma</label>
            <select id="company_id" name="company_id" required>
                <option value="">-- Firma Seçin --</option>
                <?php foreach ($companies as $company): ?>
                    <option value="<?php echo htmlspecialchars($company['id']); ?>" <?php if($user['company_id'] == $company['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($company['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit">Değişiklikleri Kaydet</button>
    </form>
</div>

<?php require_once 'footer.php'; ?>