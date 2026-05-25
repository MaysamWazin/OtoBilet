<?php 

require_once 'auth_check.php';
require_role('admin');

require_once 'header.php';

?>

<div class = "container">
    <h1>Yeni Otobüs Firması Ekle</h1>
    <form action = "/handle_add_company.php" method="POST">
        <div class = "form-group">
            <label for = "company_name">Firma Adı</label>
            <input type= "text" id = "company_name" name = "company_name" required>
</div>
<button type = "submit">Firmayı Ekle</button>
</form>
</div>

<?php require_once 'footer.php'; ?>