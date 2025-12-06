<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") { http_response_code(405); exit; }

function clean($v){ return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8'); }

$name   = clean($_POST["name"] ?? "");
$email  = clean($_POST["email"] ?? "");
$start  = clean($_POST["start_date"] ?? "");
$end    = clean($_POST["end_date"] ?? "");
$msg    = clean($_POST["message"] ?? "");

if (!$name || !$email || !$start || !$end) {
  http_response_code(400);
  echo "Missing required fields.";
  exit;
}

$OWNER_EMAIL = "ignace_opsommer@yahoo.com";      // <-- set this
$FROM_EMAIL  = "ignace_opsommer@yahoo.com"; // <-- set a valid sender if possible

$subjectOwner = "Nieuwe reservatie-aanvraag: $name ($start → $end)";
$bodyOwner = "Nieuwe reservatie-aanvraag via de website:\n\n"
           . "Naam: $name\n"
           . "E-mail: $email\n"
           . "Start: $start\n"
           . "Einde: $end\n\n"
           . "Bericht:\n$msg\n";

$headers = "From: $FROM_EMAIL\r\n"
         . "Reply-To: $email\r\n"
         . "Content-Type: text/plain; charset=UTF-8\r\n";

@mail($OWNER_EMAIL, $subjectOwner, $bodyOwner, $headers);

// Guest confirmation (E)
$subjectGuest = "Bevestiging aanvraag – Vakantiewoning Nieuwpoort-Stad";
$bodyGuest = "Dag $name,\n\n"
           . "Bedankt voor je aanvraag voor de periode $start → $end.\n"
           . "Ik neem zo snel mogelijk contact met je op om alles te bevestigen.\n\n"
           . "Met vriendelijke groeten,\n"
           . "Vakantiewoning Nieuwpoort-Stad\n";

@mail($email, $subjectGuest, $bodyGuest, "From: $FROM_EMAIL\r\nContent-Type: text/plain; charset=UTF-8\r\n");

// Simple log (optional)
@file_put_contents("requests.log", date("Y-m-d H:i:s") . " | $name | $email | $start-$end\n", FILE_APPEND);

echo "OK";
?>