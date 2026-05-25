<?php

require_once 'auth_check.php';
require_role('company');

require_once 'header.php';
require_once 'db.php';

$company_id = $_SESSION['company_id'];


$stmt = $pdo -> prepare("SELECT * FROM Trips WHERE company_id = ? ORDER BY departure_time DESC");
$stmt -> execute([$company_id]);
$trips = $stmt -> fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>Firma Sefer Yönetimi</h1>
    <a href="/add_trip.php" class="button" style="display: inline-block; margin-bottom: 2rem;">Yeni Sefer Ekle</a>

    <?php if (count($trips) > 0): ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 8px; border: 1px solid #ddd;">Kalkış</th>
                    <th style="padding: 8px; border: 1px solid #ddd;">Varış</th>
                    <th style="padding: 8px; border: 1px solid #ddd;">Zaman</th>
                    <th style="padding: 8px; border: 1px solid #ddd;">Fiyat</th>
                    <th style="padding: 8px; border: 1px solid #ddd;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trips as $trip): ?>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($trip['departure_city']); ?></td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($trip['destination_city']); ?></td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><?php echo date('d M Y, H:i', strtotime($trip['departure_time'])); ?></td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($trip['price']); ?> TL</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">
                            <a href="/edit_trip.php?id=<?php echo $trip['id']; ?>">Düzenle</a> |
                            <a href="/delete_trip.php?id=<?php echo $trip['id']; ?>" onclick="return confirm('Bu seferi silmek istediğinize emin misiniz?');">Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Henüz hiç sefer oluşturmadınız.</p>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>