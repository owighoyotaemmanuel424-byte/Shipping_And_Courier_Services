<?php
require_once __DIR__ . '/session.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['submit']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string)($_POST['csrf_token'] ?? ''))) {
    http_response_code(403); exit('Invalid request. Please return to the shipment form and try again.');
}
require_once __DIR__ . '/../database/connection.php';
$redirect = 'add-tracking.php';
$fields = ['firstname'=>190,'lastname'=>190,'tracknum'=>100,'shipmentType'=>100,'content'=>2000,'sdate'=>30,'ddate'=>30,'scity'=>190,'sstate'=>190,'szip'=>40,'ccity'=>190,'cstate'=>190,'czip'=>40,'dcity'=>190,'dstate'=>190,'dzip'=>40,'contactnumber'=>60,'status'=>100];
$data=[];
foreach ($fields as $key=>$max) {
    $value=trim((string)($_POST[$key] ?? ''));
    // Tracking IDs are generated server-side, never accepted from the browser.
    if ($key === 'tracknum') { $value = 'PENDING'; }
    $optional=in_array($key,['content','szip','czip','dzip'],true);
    if ((!$optional && $value==='') || strlen($value)>$max) {
        $_SESSION['ERRMSG_ARR']=['Please complete all shipment fields using valid values.']; header('Location: '.$redirect); exit();
    }
    $data[]=$value;
}
$dateValid=static function(string $date): bool {
    return preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$date)===1 && checkdate((int)substr($date,5,2),(int)substr($date,8,2),(int)substr($date,0,4));
};
if (!in_array($data[3],['Air Freight','Marine Freight','Land Freight'],true) || !in_array($data[17],['Shipped','Delivered','Transit','Packaging','Missing Parcel'],true)) {
    $_SESSION['ERRMSG_ARR']=['Choose a valid shipment type and parcel status.']; header('Location: '.$redirect); exit();
}
if (!$dateValid($data[5]) || !$dateValid($data[6]) || strcmp($data[6],$data[5])<0) {
    $_SESSION['ERRMSG_ARR']=['Enter valid dates. Delivery date cannot be earlier than shipment date.']; header('Location: '.$redirect); exit();
}
if (!preg_match('/^[A-Za-z0-9_-]+$/',$data[2])) {
    $_SESSION['ERRMSG_ARR']=['Tracking numbers may contain only letters, numbers, hyphens, and underscores.']; header('Location: '.$redirect); exit();
}
try {
    $data[2] = 'NG' . date('ymd') . strtoupper(bin2hex(random_bytes(5)));
    $sql='INSERT INTO tracking (first_name,last_name,tracking_number,shipment_type,content,shipment_date,delivery_date,source_city,source_state,source_zip,current_city,current_state,current_zip,destination_city,destination_state,destination_zip,contact_number,parcel_status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
    app_db()->prepare($sql)->execute($data);
} catch (PDOException $exception) {
    error_log('Create shipment failed: '.$exception->getMessage());
    $_SESSION['ERRMSG_ARR']=['Unable to generate or save a unique tracking number right now. Please try again.'];
    header('Location: '.$redirect); exit();
} catch (Throwable $exception) {
    error_log('Create shipment failed: '.$exception->getMessage());
    $_SESSION['ERRMSG_ARR']=['Unable to save shipment right now. Please try again.']; header('Location: '.$redirect); exit();
}
header('Location: dashboard.php'); exit();
