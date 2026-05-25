<?php

require_once 'auth_check.php';
require_role('admin');

require_once 'header.php';
require_once 'db.php';

$stmt_companies = $pdo ->query("SELECT * FROM Bus_Company ORDER BY name");
$companies = $stmt_companies -> fetchAll(PDO::FETCH_ASSOC);

$stmt_admins = $pdo->query(
    "SELECT U.id, U.full_name, U.email, B.name as company_name 
     FROM User U 
     LEFT JOIN Bus_Company B ON U.company_id = B.id 
     WHERE U.role = 'company' 
     ORDER BY B.name, U.full_name"
);
$company_admins = $stmt_admins->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container">
    <h1>Admin Paneli</h1>

    <section id="company-management">
        <h2>Firma Yönetimi</h2>
        <a href="/add_company.php" class="button" style="display: inline-block; margin-bottom: 1rem;">Yeni Firma Ekle</a>
        <?php if (count($companies) > 0): ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>...</thead> <tbody>
                    <?php foreach ($companies as $company): ?>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($company['name']); ?></td>
                            <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">
                                <a href="/edit_company.php?id=<?php echo $company['id']; ?>">Düzenle</a> |
                                <a href="/delete_company.php?id=<?php echo $company['id']; ?>" onclick="return confirm('Bu firmayı silmek istediğinize emin misiniz? Bu firmaya ait tüm seferler ve firma adminleri de silinecektir!');">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Sistemde kayıtlı otobüs firması bulunmuyor.</p>
        <?php endif; ?>
    </section>

    <hr style="margin: 2rem 0;">

    <section id="company-admin-management">
        <h2>Firma Admini Yönetimi</h2>
        <a href="/add_company_admin.php" class="button" style="display: inline-block; margin-bottom: 1rem;">Yeni Firma Admini Ekle</a>
        
        <?php if (count($company_admins) > 0): ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Ad Soyad</th>
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">E-posta</th>
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Atandığı Firma</th>
                        <th style="padding: 8px; border: 1px solid #ddd;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($company_admins as $admin): ?>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($admin['full_name']); ?></td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($admin['email']); ?></td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($admin['company_name'] ?? 'Atanmamış'); ?></td>
                            <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">
                                <a href="/edit_user.php?id=<?php echo $admin['id']; ?>">Düzenle</a> |
                                <a href="/delete_user.php?id=<?php echo $admin['id']; ?>" onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
             <p>Sistemde kayıtlı firma admini bulunmuyor.</p>
        <?php endif; ?>
    </section>
</div>

<?php require_once 'footer.php'; ?>