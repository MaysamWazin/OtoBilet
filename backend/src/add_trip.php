<?php
require_once 'auth_check.php';
require_role('company'); 

require_once 'header.php';
?>

<div class="container">
    <h1>Yeni Sefer Ekle</h1>
    <form action="/handle_add_trip.php" method="POST">
        <div class="form-group">
            <label for="departure_city">Kalkış Şehri</label>
            <input type="text" id="departure_city" name="departure_city" required>
        </div>
        <div class="form-group">
            <label for="destination_city">Varış Şehri</label>
            <input type="text" id="destination_city" name="destination_city" required>
        </div>
        <div class="form-group">
            <label for="departure_time">Kalkış Zamanı</label>
            <input type="datetime-local" id="departure_time" name="departure_time" required>
        </div>
        <div class="form-group">
            <label for="arrival_time">Varış Zamanı</label>
            <input type="datetime-local" id="arrival_time" name="arrival_time" required>
        </div>
        <div class="form-group">
            <label for="price">Fiyat</label>
            <input type="number" id="price" name="price" required>
        </div>
        <div class="form-group">
            <label for="capacity">Kapasite (Koltuk Sayısı)</label>
            <input type="number" id="capacity" name="capacity" required>
        </div>
        <button type="submit">Seferi Ekle</button>
    </form>
</div>

<?php require_once 'footer.php'; ?>