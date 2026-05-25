<?php
require_once 'auth_check.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

require_once 'db.php';
require('lib/fpdf.php');

$ticket_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$ticket_id) {
    die('Bilet ID\'si belirtilmedi.');
}

$stmt = $pdo->prepare(
    "SELECT
        T.id as ticket_id, T.total_price, T.created_at as purchase_date,
        U.full_name as passenger_name,
        Tr.departure_city, Tr.destination_city, Tr.departure_time, Tr.arrival_time,
        BC.name as company_name,
        GROUP_CONCAT(BS.seat_number) as seat_numbers
     FROM Tickets T
     JOIN User U ON T.user_id = U.id
     JOIN Trips Tr ON T.trip_id = Tr.id
     JOIN Bus_Company BC ON Tr.company_id = BC.id
     JOIN Booked_Seats BS ON T.id = BS.ticket_id
     WHERE T.id = ? AND T.user_id = ?
     GROUP BY T.id"
);
$stmt->execute([$ticket_id, $user_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die('Bilet bulunamadı veya bu bileti görüntüleme yetkiniz yok.');
}

function to_turkish_iso($text) {
    return iconv('UTF-8', 'ISO-8859-9', $text);
}


$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);


$pdf->Cell(0, 10, to_turkish_iso('Elektronik Otobüs Biletiniz'), 0, 1, 'C');
$pdf->Ln(10);


$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, to_turkish_iso('Yolcu Bilgileri'), 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 8, to_turkish_iso('Ad Soyad:'), 0, 0);
$pdf->Cell(0, 8, to_turkish_iso($ticket['passenger_name']), 0, 1);


$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, to_turkish_iso('Sefer Bilgileri'), 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 8, to_turkish_iso('Firma:'), 0, 0);
$pdf->Cell(0, 8, to_turkish_iso($ticket['company_name']), 0, 1);
$pdf->Cell(40, 8, to_turkish_iso('Güzergah:'), 0, 0);
$pdf->Cell(0, 8, to_turkish_iso($ticket['departure_city'] . ' -> ' . $ticket['destination_city']), 0, 1);
$pdf->Cell(40, 8, to_turkish_iso('Kalkış Zamanı:'), 0, 0);
$pdf->Cell(0, 8, date('d.m.Y H:i', strtotime($ticket['departure_time'])), 0, 1);


$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, to_turkish_iso('Bilet Detayları'), 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 8, to_turkish_iso('Koltuk No:'), 0, 0);
$pdf->Cell(0, 8, $ticket['seat_numbers'], 0, 1);
$pdf->Cell(40, 8, to_turkish_iso('Toplam Tutar:'), 0, 0);
$pdf->Cell(0, 8, number_format($ticket['total_price'], 2, ',', '.') . ' TL', 0, 1);
$pdf->Cell(40, 8, to_turkish_iso('Satın Alma Tarihi:'), 0, 0);
$pdf->Cell(0, 8, date('d.m.Y H:i', strtotime($ticket['purchase_date'])), 0, 1);


$pdf->Output('D', 'bilet-'. $ticket_id . '.pdf');
exit();