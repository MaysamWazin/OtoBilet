<?php 


require_once 'header.php';
require_once 'db.php';

$trip_id = $_GET['id'] ?? null;

if(!$trip_id) {
    die("Geçersiz id değeri");
}


$stmt = $pdo -> prepare("SELECT T.*, B.name as company_name FROM Trips T JOIN Bus_Company B ON  T.company_id = B.id WHERE T.id = ?");
$stmt -> execute([$trip_id]);
$trip = $stmt -> fetch(PDO::FETCH_ASSOC);

if (!$trip) {
    die("Sefer bulunamadı!");
}

$stmt_seats = $pdo -> prepare("SELECT BS.seat_number FROM Booked_Seats BS 
     JOIN Tickets T ON BS.ticket_id = T.id 
     WHERE T.trip_id = ? AND T.status = 'active'");

$stmt_seats -> execute([$trip_id]);
$booked_seats = $stmt_seats -> fetchAll(PDO::FETCH_COLUMN);
?>

<style>
    .trip-detail-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 2rem;
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        border: 1px solid rgba(255, 255, 255, 0.3);
        margin-bottom: 2rem;
        animation: fadeInUp 0.6s ease-out;
    }

    .trip-info {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .trip-route {
        font-size: 1.75rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }

    .trip-details {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-secondary);
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }

    .trip-details strong {
        color: var(--text-primary);
        font-weight: 600;
    }

    .bus-container {
        max-width: 600px;
        margin: 2rem auto;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 2rem;
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .bus-header {
        text-align: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--border-color);
    }

    .bus-header::before {
        content: "🚌";
        font-size: 2rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .bus-layout {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        margin: 1rem 0;
        position: relative;
        z-index: 1;
        touch-action: manipulation;
    }

    .seat-row {
        display: contents;
    }

    .seat {
        width: 60px;
        height: 60px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: var(--bg-primary);
        border: 2px solid var(--border-color);
        border-radius: 12px;
        cursor: pointer;
        user-select: none;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: var(--text-primary);
        position: relative;
        pointer-events: auto !important;
        z-index: 10;
        -webkit-tap-highlight-color: transparent;
    }

    .seat * {
        pointer-events: none;
    }

    .seat-number {
        font-size: 1rem;
        font-weight: 700;
        pointer-events: none;
    }

    .seat-label {
        font-size: 0.7rem;
        color: var(--text-secondary);
        margin-top: 2px;
        pointer-events: none;
    }

    .seat:hover:not(.disabled):not(.selected) {
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        background: rgba(37, 99, 235, 0.05);
    }

    .seat.disabled {
        background: #e5e7eb;
        color: #9ca3af;
        cursor: not-allowed;
        opacity: 0.6;
        border-color: #d1d5db;
        pointer-events: none;
    }

    .seat.disabled .seat-label {
        color: #9ca3af;
    }

    .seat.selected {
        background: linear-gradient(135deg, var(--secondary-color), var(--secondary-dark));
        color: white;
        border-color: var(--secondary-dark);
        transform: scale(1.05);
        box-shadow: var(--shadow-md);
    }

    .seat.selected .seat-label {
        color: rgba(255, 255, 255, 0.9);
    }

    .aisle {
        grid-column: 3;
        pointer-events: none;
    }

    .bus-legend {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 2px solid var(--border-color);
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .legend-seat {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: 2px solid var(--border-color);
    }

    .legend-seat.available {
        background: var(--bg-primary);
    }

    .legend-seat.selected {
        background: linear-gradient(135deg, var(--secondary-color), var(--secondary-dark));
        border-color: var(--secondary-dark);
    }

    .legend-seat.disabled {
        background: #e5e7eb;
        opacity: 0.6;
    }

    .selected-seats-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 1.5rem;
        border-radius: 16px;
        box-shadow: var(--shadow-md);
        border: 1px solid rgba(255, 255, 255, 0.3);
        margin: 1.5rem auto 2rem;
        max-width: 800px;
        animation: fadeInUp 0.4s ease-out;
    }

    .selected-seats-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--border-color);
    }

    .selected-seats-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        background: linear-gradient(135deg, var(--secondary-color), var(--secondary-dark));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .selected-seats-header span {
        background: linear-gradient(135deg, var(--secondary-color), var(--secondary-dark));
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 1rem;
    }

    .selected-seats-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        min-height: 50px;
    }

    .selected-seat-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, var(--secondary-color), var(--secondary-dark));
        color: white;
        padding: 0.75rem 1.25rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        box-shadow: var(--shadow-sm);
        animation: fadeInUp 0.3s ease-out;
        transition: all 0.3s ease;
    }

    .selected-seat-badge:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .selected-seat-badge .seat-icon {
        font-size: 1.2rem;
    }

    .selected-seat-badge .remove-seat {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 700;
        transition: all 0.2s ease;
        margin-left: 0.25rem;
    }

    .selected-seat-badge .remove-seat:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .purchase-section {
        text-align: center;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 2rem;
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        border: 1px solid rgba(255, 255, 255, 0.3);
        margin-top: 2rem;
    }

    .price-summary {
        background: rgba(249, 250, 251, 0.8);
        padding: 1.5rem;
        border-radius: 12px;
        margin: 1.5rem 0;
        border: 1px solid var(--border-color);
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        font-size: 1rem;
        color: var(--text-secondary);
    }

    .price-row.total {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 2px solid var(--border-color);
    }

    .price-row.discount {
        color: var(--secondary-color);
        font-weight: 600;
    }

    .price-display {
        font-size: 2rem;
        font-weight: 700;
        margin: 1.5rem 0;
        background: linear-gradient(135deg, var(--secondary-color), var(--secondary-dark));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .purchase-section .form-group {
        max-width: 400px;
        margin: 1.5rem auto;
    }

    .coupon-section {
        display: flex;
        gap: 0.5rem;
        align-items: flex-end;
        max-width: 400px;
        margin: 0 auto;
    }

    .coupon-section .form-group {
        flex: 1;
        margin: 0;
    }

    .coupon-section button {
        padding: 1rem 1.5rem;
        white-space: nowrap;
    }

    .coupon-message {
        margin-top: 0.75rem;
        padding: 0.75rem;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        animation: fadeInUp 0.3s ease-out;
    }

    .coupon-message.success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--secondary-dark);
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .coupon-message.error {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .coupon-applied {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 0.75rem;
        padding: 0.75rem;
        background: rgba(16, 185, 129, 0.1);
        border-radius: 8px;
        color: var(--secondary-dark);
        font-weight: 600;
    }

    .coupon-applied .remove-coupon {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.3);
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .coupon-applied .remove-coupon:hover {
        background: rgba(239, 68, 68, 0.2);
    }

    .purchase-section input[type="text"] {
        text-align: center;
    }

    @media (max-width: 768px) {
        .bus-container {
            padding: 1.5rem;
        }

        .bus-layout {
            gap: 8px;
        }

        .seat {
            width: 50px;
            height: 50px;
        }

        .seat-number {
            font-size: 0.9rem;
        }

        .seat-label {
            font-size: 0.65rem;
        }

        .selected-seats-container {
            padding: 1.25rem;
            margin: 1rem auto 1.5rem;
        }

        .selected-seats-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .selected-seats-header h3 {
            font-size: 1.1rem;
        }

        .selected-seats-list {
            gap: 0.5rem;
        }

        .selected-seat-badge {
            padding: 0.625rem 1rem;
            font-size: 0.9rem;
        }

        .purchase-section {
            padding: 1.5rem;
        }

        .price-summary {
            padding: 1rem;
        }

        .price-row {
            font-size: 0.9rem;
        }

        .price-row.total {
            font-size: 1.25rem;
        }

        .coupon-section {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }

        .coupon-section .form-group {
            width: 100%;
        }

        .coupon-section input[type="text"] {
            width: 100%;
            padding: 0.875rem;
            font-size: 1rem;
        }

        .coupon-section button {
            width: 100%;
            margin-top: 0;
            padding: 0.875rem 1.25rem;
            font-size: 1rem;
            white-space: normal;
        }

        #buy-button {
            width: 100%;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
            margin-top: 1rem;
        }

        .coupon-applied {
            flex-direction: column;
            gap: 0.5rem;
            text-align: center;
        }

        .coupon-applied .remove-coupon {
            width: 100%;
            padding: 0.5rem;
        }
    }

    @media (max-width: 480px) {
        .purchase-section {
            padding: 1rem;
        }

        .price-summary {
            padding: 0.875rem;
        }

        .price-row {
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .price-row.total {
            font-size: 1.1rem;
        }

        .coupon-section input[type="text"] {
            padding: 0.75rem;
            font-size: 0.95rem;
        }

        .coupon-section button {
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
        }

        #buy-button {
            padding: 0.875rem 1.25rem;
            font-size: 1rem;
        }
    }
</style>

<div class="container">
    <h1>Sefer Detayları ve Koltuk Seçimi</h1>
    
    <div class="trip-detail-card">
        <div class="trip-info">
            <div class="trip-route"><?php echo htmlspecialchars($trip['departure_city']); ?> &rarr; <?php echo htmlspecialchars($trip['destination_city']); ?></div>
            <div class="trip-details">
                <span>🏢</span>
                <span>Firma: <strong><?php echo htmlspecialchars($trip['company_name']); ?></strong></span>
            </div>
            <div class="trip-details">
                <span>🕐</span>
                <span>Kalkış: <strong><?php echo date('d M Y, H:i', strtotime($trip['departure_time'])); ?></strong></span>
            </div>
            <div class="trip-details">
                <span>💰</span>
                <span>Tek Koltuk Fiyatı: <strong><?php echo htmlspecialchars($trip['price']); ?> TL</strong></span>
            </div>
        </div>
    </div>

    <h2 style="text-align: center; margin-bottom: 1rem;">Lütfen Koltuk Seçiniz</h2>

    <!-- Seçili Koltuklar Bölümü -->
    <div id="selected-seats-display" class="selected-seats-container" style="display: none;">
        <div class="selected-seats-header">
            <h3>Seçili Koltuklar</h3>
            <span id="selected-seats-count">0</span>
        </div>
        <div id="selected-seats-list" class="selected-seats-list">
            <!-- Seçili koltuklar buraya eklenecek -->
        </div>
    </div>

    <div class="bus-container">
        <div class="bus-header">
            <div style="font-weight: 600; color: var(--text-primary);">Otobüs Koltuk Düzeni</div>
        </div>
        
        <div class="bus-layout">
            <?php 
            $total_seats = $trip['capacity'];
            $seats_per_row = 4; // 2-2 düzen (sol 2, koridor, sağ 2)
            $rows = ceil($total_seats / $seats_per_row);
            
            for ($row = 0; $row < $rows; $row++): 
                // Sol taraftaki 2 koltuk
                for ($col = 0; $col < 2; $col++):
                    $seat_num = ($row * $seats_per_row) + $col + 1;
                    
                    if ($seat_num > $total_seats) {
                        echo '<div style="width: 60px; height: 60px;"></div>';
                        continue;
                    }
                    
                    $seat_class = 'seat';
                    if (in_array($seat_num, $booked_seats)) {
                        $seat_class .= ' disabled'; 
                    }
            ?>
                <div class="<?php echo $seat_class; ?>" data-seat-number="<?php echo $seat_num; ?>">
                    <span class="seat-number"><?php echo $seat_num; ?></span>
                    <span class="seat-label">Sol</span>
                </div>
            <?php 
                endfor;
                
                // Koridor
                echo '<div class="aisle"></div>';
                
                // Sağ taraftaki 2 koltuk
                for ($col = 2; $col < 4; $col++):
                    $seat_num = ($row * $seats_per_row) + $col + 1;
                    
                    if ($seat_num > $total_seats) {
                        echo '<div style="width: 60px; height: 60px;"></div>';
                        continue;
                    }
                    
                    $seat_class = 'seat';
                    if (in_array($seat_num, $booked_seats)) {
                        $seat_class .= ' disabled'; 
                    }
            ?>
                <div class="<?php echo $seat_class; ?>" data-seat-number="<?php echo $seat_num; ?>">
                    <span class="seat-number"><?php echo $seat_num; ?></span>
                    <span class="seat-label">Sağ</span>
                </div>
            <?php 
                endfor;
            endfor; 
            ?>
        </div>
        
        <div class="bus-legend">
            <div class="legend-item">
                <div class="legend-seat available"></div>
                <span>Müsait</span>
            </div>
            <div class="legend-item">
                <div class="legend-seat selected"></div>
                <span>Seçili</span>
            </div>
            <div class="legend-item">
                <div class="legend-seat disabled"></div>
                <span>Dolu</span>
            </div>
        </div>
    </div>

    <div class="purchase-section">
        <form action="/handle_purchase.php" method="POST" id="purchase-form">
            <input type="hidden" name="trip_id" value="<?php echo htmlspecialchars($trip['id']); ?>">
            <input type="hidden" name="selected_seats" id="selected_seats_input" value="">
            <input type="hidden" name="coupon_code" id="coupon_code_input" value="">
            
            <div class="price-summary">
                <div class="price-row">
                    <span>Koltuk Sayısı:</span>
                    <span id="seat-count">0</span>
                </div>
                <div class="price-row">
                    <span>Birim Fiyat:</span>
                    <span><?php echo htmlspecialchars($trip['price']); ?> TL</span>
                </div>
                <div class="price-row" id="subtotal-row" style="display: none;">
                    <span>Ara Toplam:</span>
                    <span id="subtotal-price">0</span> TL
                </div>
                <div class="price-row discount" id="discount-row" style="display: none;">
                    <span>🎟️ Kupon İndirimi (<span id="discount-percent">0</span>%):</span>
                    <span>-<span id="discount-amount">0</span> TL</span>
                </div>
                <div class="price-row total">
                    <span>Toplam Tutar:</span>
                    <span id="total-price">0</span> TL
                </div>
            </div>

            <div class="form-group">
                <label for="coupon_code">İndirim Kuponu (varsa):</label>
                <div class="coupon-section">
                    <div class="form-group" style="flex: 1; margin: 0;">
                        <input type="text" id="coupon_code" placeholder="Kupon Kodunu Girin" style="margin: 0;">
                    </div>
                    <button type="button" id="apply-coupon-btn" style="margin: 0;">Kupon Uygula</button>
                </div>
                <div id="coupon-message"></div>
                <div id="coupon-applied" style="display: none;"></div>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <button type="submit" id="buy-button" disabled>🎫 Satın Al</button>
            <?php else: ?>
                <p style="color: var(--text-secondary); margin-top: 1rem;">Bilet satın almak için lütfen <a href="/login.html" style="color: var(--primary-color); font-weight: 600;">giriş yapın</a>.</p>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const seatsContainer = document.querySelector('.bus-layout');
    const selectedSeatsInput = document.getElementById('selected_seats_input');
    const couponCodeInput = document.getElementById('coupon_code_input');
    const totalPriceDisplay = document.getElementById('total-price');
    const subtotalPriceDisplay = document.getElementById('subtotal-price');
    const seatCountDisplay = document.getElementById('seat-count');
    const discountRow = document.getElementById('discount-row');
    const discountAmountDisplay = document.getElementById('discount-amount');
    const discountPercentDisplay = document.getElementById('discount-percent');
    const subtotalRow = document.getElementById('subtotal-row');
    const buyButton = document.getElementById('buy-button');
    const applyCouponBtn = document.getElementById('apply-coupon-btn');
    const couponCodeField = document.getElementById('coupon_code');
    const couponMessage = document.getElementById('coupon-message');
    const couponApplied = document.getElementById('coupon-applied');
    const selectedSeatsDisplay = document.getElementById('selected-seats-display');
    const selectedSeatsList = document.getElementById('selected-seats-list');
    const selectedSeatsCount = document.getElementById('selected-seats-count');
    const tripId = "<?php echo $trip['id']; ?>";
    const seatPrice = parseFloat("<?php echo $trip['price']; ?>");
    
    let selectedSeats = [];
    let appliedCoupon = null;
    let discountPercent = 0;

    // Koltuk seçimi için event listener
    let seatSelectionInitialized = false;
    
    function initializeSeatSelection() {
        // Zaten başlatıldıysa tekrar başlatma
        if (seatSelectionInitialized) {
            return;
        }
        
        if (!seatsContainer) {
            console.error('Koltuk konteyneri bulunamadı');
            return;
        }
        
        // Tüm koltukları bul (hem disabled hem de aktif)
        const allSeats = seatsContainer.querySelectorAll('.seat');
        console.log('Toplam koltuk sayısı:', allSeats.length);
        
        if (allSeats.length === 0) {
            console.warn('Hiç koltuk bulunamadı, tekrar denenecek...');
            return;
        }
        
        // Her koltuk için event listener ekle
        allSeats.forEach(function(seat) {
            // Disabled koltukları atla
            if (seat.classList.contains('disabled')) {
                return;
            }
            
            // Koltuk seçme fonksiyonu
            function handleSeatSelection(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const seatNumber = seat.getAttribute('data-seat-number');
                
                if (!seatNumber) {
                    console.warn('Koltuk numarası bulunamadı');
                    return;
                }
                
                console.log('Koltuk tıklandı:', seatNumber);
                
                // Seçili mi kontrol et
                const isSelected = seat.classList.contains('selected');
                
                if (isSelected) {
                    // Seçimi kaldır
                    console.log('Koltuk seçimi kaldırılıyor:', seatNumber);
                    seat.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(s => String(s) !== String(seatNumber));
                } else {
                    // Seç
                    console.log('Koltuk seçiliyor:', seatNumber);
                    seat.classList.add('selected');
                    const seatNumStr = String(seatNumber);
                    if (!selectedSeats.some(s => String(s) === seatNumStr)) {
                        selectedSeats.push(seatNumber);
                    }
                }
                
                // Fiyat ve görünümü güncelle
                updateSelection();
            }
            
            // Click event listener (masaüstü için)
            seat.addEventListener('click', handleSeatSelection);
            
            // Touch event listener (mobil için)
            seat.addEventListener('touchend', function(e) {
                handleSeatSelection(e);
            });
            
            // Hover efekti için cursor kontrolü
            seat.style.cursor = 'pointer';
        });
        
        seatSelectionInitialized = true;
        console.log('Koltuk seçimi başlatıldı. Müsait koltuk sayısı:', seatsContainer.querySelectorAll('.seat:not(.disabled)').length);
    }
    
    // Sayfa yüklendiğinde koltuk seçimini başlat
    // Kısa bir gecikme ile DOM'un tam yüklendiğinden emin ol
    setTimeout(function() {
        initializeSeatSelection();
    }, 100);
    
    // Ayrıca window load event'inde de başlat (yedek)
    window.addEventListener('load', function() {
        setTimeout(function() {
            initializeSeatSelection();
        }, 50);
    });

    function updateSelection() {
        // Seçili koltukları sırala ve birleştir (sayısal sıralama)
        const sortedSeats = selectedSeats.map(s => String(s)).sort((a, b) => {
            const numA = parseInt(a);
            const numB = parseInt(b);
            return numA - numB;
        });
        
        // Hidden input'a seçili koltukları kaydet
        if (selectedSeatsInput) {
            selectedSeatsInput.value = sortedSeats.join(',');
        }
        
        // Koltuk sayısını güncelle
        if (seatCountDisplay) {
            seatCountDisplay.textContent = selectedSeats.length;
        }
        
        // Seçili koltuklar listesini güncelle
        updateSelectedSeatsDisplay(sortedSeats);
        
        // Ara toplamı hesapla
        const subtotal = selectedSeats.length * seatPrice;
        if (subtotalPriceDisplay) {
            subtotalPriceDisplay.textContent = subtotal.toFixed(2);
        }
        
        // Ara toplam satırını göster/gizle
        if (subtotalRow) {
            if (selectedSeats.length > 0) {
                subtotalRow.style.display = 'flex';
            } else {
                subtotalRow.style.display = 'none';
            }
        }
        
        // Toplam fiyatı hesapla
        calculateTotal();

        // Satın al butonunu aktif/pasif yap
        if (buyButton) {
            buyButton.disabled = selectedSeats.length === 0;
            if (selectedSeats.length > 0) {
                buyButton.style.opacity = '1';
                buyButton.style.cursor = 'pointer';
            } else {
                buyButton.style.opacity = '0.6';
                buyButton.style.cursor = 'not-allowed';
            }
        }
    }

    function updateSelectedSeatsDisplay(sortedSeats) {
        // Seçili koltuklar container'ını göster/gizle
        if (selectedSeatsDisplay) {
            if (sortedSeats.length > 0) {
                selectedSeatsDisplay.style.display = 'block';
            } else {
                selectedSeatsDisplay.style.display = 'none';
            }
        }
        
        // Koltuk sayısını güncelle
        if (selectedSeatsCount) {
            selectedSeatsCount.textContent = sortedSeats.length;
        }
        
        // Seçili koltuklar listesini oluştur
        if (selectedSeatsList) {
            selectedSeatsList.innerHTML = '';
            
            sortedSeats.forEach(function(seatNum) {
                const seatBadge = document.createElement('div');
                seatBadge.className = 'selected-seat-badge';
                seatBadge.innerHTML = `
                    <span class="seat-icon">🪑</span>
                    <span>Koltuk ${seatNum}</span>
                    <button type="button" class="remove-seat" data-seat="${seatNum}" title="Koltuk seçimini kaldır">×</button>
                `;
                
                // Kaldır butonuna event listener ekle
                const removeBtn = seatBadge.querySelector('.remove-seat');
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Koltuk seçimini kaldır
                    const seatElement = seatsContainer.querySelector(`.seat[data-seat-number="${seatNum}"]`);
                    if (seatElement) {
                        seatElement.classList.remove('selected');
                    }
                    
                    selectedSeats = selectedSeats.filter(s => String(s) !== String(seatNum));
                    updateSelection();
                });
                
                selectedSeatsList.appendChild(seatBadge);
            });
        }
    }

    function calculateTotal() {
        const subtotal = selectedSeats.length * seatPrice;
        let discount = 0;
        
        // Kupon uygulanmışsa indirimi hesapla
        if (appliedCoupon && selectedSeats.length > 0 && discountPercent > 0) {
            discount = subtotal * (discountPercent / 100);
            if (discountRow) {
                discountRow.style.display = 'flex';
            }
            if (discountAmountDisplay) {
                discountAmountDisplay.textContent = discount.toFixed(2);
            }
            if (discountPercentDisplay) {
                discountPercentDisplay.textContent = discountPercent;
            }
        } else {
            if (discountRow) {
                discountRow.style.display = 'none';
            }
        }
        
        // Toplam tutarı hesapla
        const total = Math.max(0, subtotal - discount);
        if (totalPriceDisplay) {
            totalPriceDisplay.textContent = total.toFixed(2);
        }
    }

    if (applyCouponBtn) {
        applyCouponBtn.addEventListener('click', function() {
            const couponCode = couponCodeField ? couponCodeField.value.trim() : '';
            
            if (!couponCode) {
                showCouponMessage('Lütfen bir kupon kodu girin.', 'error');
                return;
            }

            if (selectedSeats.length === 0) {
                showCouponMessage('Önce koltuk seçmelisiniz.', 'error');
                return;
            }

            // Butonu devre dışı bırak (çift tıklamayı önle)
            applyCouponBtn.disabled = true;
            applyCouponBtn.textContent = 'Kontrol ediliyor...';

            // Kuponu kontrol et
            fetch(`/check_coupon.php?code=${encodeURIComponent(couponCode)}&trip_id=${tripId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.valid) {
                        appliedCoupon = couponCode;
                        discountPercent = parseFloat(data.discount);
                        if (couponCodeInput) {
                            couponCodeInput.value = couponCode;
                        }
                        
                        showCouponApplied(couponCode, discountPercent);
                        showCouponMessage(`Kupon başarıyla uygulandı! %${discountPercent} indirim.`, 'success');
                        if (couponCodeField) {
                            couponCodeField.value = '';
                        }
                        calculateTotal();
                    } else {
                        showCouponMessage(data.message || 'Geçersiz kupon kodu.', 'error');
                        appliedCoupon = null;
                        discountPercent = 0;
                        if (couponCodeInput) {
                            couponCodeInput.value = '';
                        }
                        calculateTotal();
                    }
                })
                .catch(error => {
                    showCouponMessage('Kupon kontrol edilirken bir hata oluştu. Lütfen tekrar deneyin.', 'error');
                    console.error('Kupon kontrol hatası:', error);
                })
                .finally(() => {
                    // Butonu tekrar aktif et
                    if (applyCouponBtn) {
                        applyCouponBtn.disabled = false;
                        applyCouponBtn.textContent = 'Kupon Uygula';
                    }
                });
        });
    }

    function showCouponMessage(message, type) {
        if (couponMessage) {
            couponMessage.innerHTML = `<div class="coupon-message ${type}">${message}</div>`;
            setTimeout(() => {
                if (couponMessage) {
                    couponMessage.innerHTML = '';
                }
            }, 5000);
        }
    }

    function showCouponApplied(code, discount) {
        if (couponApplied) {
            couponApplied.innerHTML = `
                <div class="coupon-applied">
                    <span>✅ Kupon uygulandı: <strong>${code}</strong> (%${discount} indirim)</span>
                    <button type="button" class="remove-coupon" onclick="removeCoupon()">Kaldır</button>
                </div>
            `;
            couponApplied.style.display = 'block';
        }
    }

    window.removeCoupon = function() {
        appliedCoupon = null;
        discountPercent = 0;
        if (couponCodeInput) {
            couponCodeInput.value = '';
        }
        if (couponApplied) {
            couponApplied.style.display = 'none';
            couponApplied.innerHTML = '';
        }
        if (couponMessage) {
            couponMessage.innerHTML = '';
        }
        calculateTotal();
    };

    // Enter tuşu ile kupon uygula
    if (couponCodeField) {
        couponCodeField.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (applyCouponBtn) {
                    applyCouponBtn.click();
                }
            }
        });
    }

    // Sayfa yüklendiğinde başlangıç durumunu ayarla
    updateSelection();
});
</script>

<?php require_once 'footer.php'; ?>