<?php

require_once 'header.php';

?>

<style>
    .search-form {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .form-group label::before {
        content: "📍";
        font-size: 1.1rem;
    }

    .form-group:last-of-type label::before {
        content: "🎯";
    }
</style>

<div class="container" style="max-width: 650px;">
    <h1>Otobüs Arama</h1>
    <p class="subtitle" style="text-align: center; color: var(--text-secondary); font-size: 1rem; margin-bottom: 2.5rem;">Hayalinizdeki yolculuğa bir adım daha yaklaşın</p>
    <form class="search-form" action="/trips.php" method="GET">
        <div class="form-group">
            <label for="departure">Kalkış Yeri</label>
            <input type="text" id="departure" name="departure_city" placeholder="Örn: Ankara" required>
        </div>
        <div class="form-group">
            <label for="destination">Varış Yeri</label>
            <input type="text" id="destination" name="destination_city" placeholder="Örn: Giresun" required>
        </div>
        <button type="submit">Seferleri Ara</button>
    </form>
</div>


<?php

require_once 'footer.php';

?>