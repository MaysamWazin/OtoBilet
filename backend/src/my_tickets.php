<?php 

require_once 'auth_check.php';
require_role('user');

require_once 'header.php';
require_once 'db.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo -> prepare("SELECT
        T.id as ticket_id,
        T.total_price,
        T.created_at as purchase_date,
        Tr.departure_city,
        Tr.destination_city,
        Tr.departure_time,
        BC.name as company_name,
        GROUP_CONCAT(BS.seat_number) as seat_numbers
     FROM Tickets T
     JOIN Trips Tr ON T.trip_id = Tr.id
     JOIN Bus_Company BC ON Tr.company_id = BC.id
     JOIN Booked_Seats BS ON T.id = BS.ticket_id
     WHERE T.user_id = ? AND T.status = 'active'
     GROUP BY T.id
     ORDER BY Tr.departure_time DESC");
$stmt -> execute([$user_id]);
$tickets = $stmt -> fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .tickets-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .ticket-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 2rem;
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: fadeInUp 0.6s ease-out;
    }

    .ticket-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-xl);
    }

    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid var(--border-color);
    }

    .ticket-route {
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }

    .ticket-info {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        flex: 1;
    }

    .ticket-detail {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .ticket-detail strong {
        color: var(--text-primary);
        font-weight: 600;
    }

    .ticket-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        min-width: 150px;
    }

    .ticket-actions a {
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        display: inline-block;
    }

    .ticket-actions .btn-secondary {
        background: rgba(255, 255, 255, 0.9);
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
    }

    .ticket-actions .btn-secondary:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .ticket-actions .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        border: none;
    }

    .ticket-actions .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .no-results {
        text-align: center;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 3rem 2rem;
        border-radius: 16px;
        box-shadow: var(--shadow-md);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .no-results p {
        font-size: 1.1rem;
        color: var(--text-secondary);
    }
</style>

<div class="container">
    <h1>Aktif Biletlerim</h1>

    <?php if (count($tickets) > 0): ?>
        <div class="tickets-container">
            <?php foreach ($tickets as $ticket): ?>
                <div class="ticket-card">
                    <div class="ticket-header">
                        <div class="ticket-info">
                            <div class="ticket-route">
                                <?php echo htmlspecialchars($ticket['departure_city']); ?> &rarr; <?php echo htmlspecialchars($ticket['destination_city']); ?>
                            </div>
                            <div class="ticket-detail">
                                <span>🏢</span>
                                <span>Firma: <strong><?php echo htmlspecialchars($ticket['company_name']); ?></strong></span>
                            </div>
                            <div class="ticket-detail">
                                <span>🕐</span>
                                <span>Kalkış: <strong><?php echo date('d M Y, H:i', strtotime($ticket['departure_time'])); ?></strong></span>
                            </div>
                            <div class="ticket-detail">
                                <span>💺</span>
                                <span>Koltuk No: <strong><?php echo htmlspecialchars($ticket['seat_numbers']); ?></strong></span>
                            </div>
                            <div class="ticket-detail">
                                <span>💰</span>
                                <span>Ödenen Tutar: <strong><?php echo htmlspecialchars($ticket['total_price']); ?> TL</strong></span>
                            </div>
                        </div>
                        <div class="ticket-actions">
                            <a href="/download_pdf.php?id=<?php echo $ticket['ticket_id']; ?>" class="btn-secondary">📄 PDF İndir</a>
                            <?php
                                $departure_time = new DateTime($ticket['departure_time']);
                                $now = new DateTime();
                               
                                if ($departure_time > $now) {
                                    $interval = $now->diff($departure_time);
                                    $hours_until_departure = ($interval->days * 24) + $interval->h;

                                    if ($hours_until_departure >= 1) {
                                        echo '<a href="/cancel_ticket.php?id='.$ticket['ticket_id'].'" class="btn-danger" onclick="return confirm(\'Bu bileti iptal etmek istediğinize emin misiniz? Ücret iadesi yapılacaktır.\');">❌ İptal Et</a>';
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-results">
            <p>Hiç aktif biletiniz bulunmamaktadır.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>