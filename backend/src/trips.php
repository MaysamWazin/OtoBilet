<?php

require_once 'header.php';
require_once 'db.php';


$departure_city = $_GET['departure_city'] ?? '';
$destination_city = $_GET['destination_city'] ?? '';


$sql = "SELECT T.*, B.name as company_name FROM Trips T JOIN Bus_Company B ON T.company_id = B.id WHERE 1=1";
$params = [];


if (!empty($departure_city)) {
    $sql .= " AND T.departure_city LIKE ?";
    $params[] = '%' . $departure_city . '%';
}
if (!empty($destination_city)) {
    $sql .= " AND T.destination_city LIKE ?";
    $params[] = '%' . $destination_city . '%';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$trips = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<style>
    .trips-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .trip-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .trip-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 2rem;
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        border: 1px solid rgba(255, 255, 255, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: fadeInUp 0.6s ease-out;
    }

    .trip-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-xl);
    }

    .trip-info {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        flex: 1;
    }

    .trip-route {
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .trip-details {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .trip-company {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .trip-price {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--secondary-color), var(--secondary-dark));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-align: right;
        min-width: 120px;
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

    @media (max-width: 768px) {
        .trip-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 1.5rem;
        }

        .trip-price {
            text-align: left;
            width: 100%;
        }
    }
</style>

<div class="container">
    <h1>Sefer Arama Sonuçları</h1>
    
    <?php if (count($trips) > 0): ?>
        <div class="trips-container">
            <?php foreach ($trips as $trip): ?>
                <a href="/trip_details.php?id=<?php echo $trip['id']; ?>" class="trip-link">
                    <div class="trip-card">
                        <div class="trip-info">
                            <div class="trip-route">
                                <?php echo htmlspecialchars($trip['departure_city']); ?> &rarr; <?php echo htmlspecialchars($trip['destination_city']); ?>
                            </div>
                            <div class="trip-details">
                                <span>🕐</span>
                                <span>Kalkış: <?php echo date('d M Y, H:i', strtotime($trip['departure_time'])); ?></span>
                            </div>
                            <div class="trip-company">
                                <span>🏢</span>
                                <span>Firma: <?php echo htmlspecialchars($trip['company_name']); ?></span>
                            </div>
                        </div>
                        <div class="trip-price">
                            <?php echo htmlspecialchars($trip['price']); ?> TL
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-results">
            <p>Aradığınız kriterlere uygun sefer bulunamadı.</p>
        </div>
    <?php endif; ?>
</div>

<?php

require_once 'footer.php'; 
?>