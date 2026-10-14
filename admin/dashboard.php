<?php
include('session.php');
require_once __DIR__ . '/../database/connection.php';
function get_track_details(): array {
    try {
        return app_db()->query('SELECT * FROM tracking ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $exception) {
        error_log('Dashboard shipment query failed: '.$exception->getMessage());
        return [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="assets/ico/favicon.png"><link href="../css/cssreset.css" rel="stylesheet"><link href="../css/bootstrap.css" rel="stylesheet"><link href="../css/main.css" rel="stylesheet">
<script src="https://kit.fontawesome.com/cbdf2b0f0e.js" crossorigin="anonymous"></script><title>NEXTGEN - Logistics</title>
</head>
<body>
<?php include_once('header.php'); ?>
<div class="container border my-5"><div class="row">
<?php include_once('./admin-sidebar.php'); ?>
<div class="col-12 col-md-9">
<h3>Admin Dashboard</h3>
<p>Welcome <?php echo htmlspecialchars((string)$_SESSION['username'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>. <a href="logout.php" title="logout">Logout</a></p>
<?php if (!empty($_SESSION['ERRMSG_ARR'])): ?><div class="alert alert-danger"><?php foreach ($_SESSION['ERRMSG_ARR'] as $msg) echo htmlspecialchars((string)$msg, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'<br>'; unset($_SESSION['ERRMSG_ARR']); ?></div><?php endif; ?>
<div class="container mb-5"><div class="row"><div class="col-12">
<h3 class="my-1">Parcel Information</h3><div class="table-responsive border"><table class="table table-hover">
<thead><tr><th>Tracking Number</th><th>Receiver Name</th><th>Content</th><th>Contact Number</th><th>Status</th><th>Edit</th><th>Delete</th></tr></thead><tbody>
<?php foreach (get_track_details() as $ap): $id=(int)$ap['id']; ?>
<tr>
<td><?php echo htmlspecialchars((string)$ap['tracking_number'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars(trim((string)$ap['first_name'].' '.(string)$ap['last_name']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars((string)$ap['content'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars((string)$ap['contact_number'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars((string)$ap['parcel_status'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></td>
<td><a href="./edit.php?id=<?php echo $id; ?>">Edit</a></td>
<td><form method="POST" action="./delete.php" onsubmit="return confirm('Delete this shipment? This cannot be undone.');"><input type="hidden" name="id" value="<?php echo $id; ?>"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>"><button type="submit" class="btn btn-danger btn-sm">Delete</button></form></td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div></div></div>
</div></div></div>
<?php include_once('../views/footer.php'); ?>
</body></html>