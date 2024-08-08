<?php
$page_title = 'Sales Report';
$results = '';
require_once('includes/load.php');
// Check what level user has permission to view this page
page_require_level(3);

if (isset($_POST['submit'])) {
    $req_dates = array('start-date', 'end-date');
    validate_fields($req_dates);

    if (empty($errors)) {
        $start_date = remove_junk($db->escape($_POST['start-date']));
        $end_date = remove_junk($db->escape($_POST['end-date']));
        $results = find_sale_by_dates($start_date, $end_date);
    } else {
        $session->msg("d", $errors);
        redirect('sales_report.php', false);
    }
} else {
    $session->msg("d", "Select dates");
    redirect('sales_report.php', false);
}
?>
<!doctype html>
<html lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .page-break {
            page-break-before: always;
        }
        .sale-head {
            text-align: center;
            margin-bottom: 30px;
        }
        .sale-head h1 {
            margin: 0 0 10px;
            padding: 10px 0;
            border-bottom: 2px solid #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        tfoot {
            font-weight: bold;
        }
    </style>
</head>
<body>
<?php if ($results): ?>
    <div class="page-break">
        <div class="sale-head">
            <h1>Inventory Management System - Sales Report</h1>
            <strong><?php if (isset($start_date)) { echo $start_date; } ?> TO <?php if (isset($end_date)) { echo $end_date; } ?></strong>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product Title</th>
                    <th>Buying Price</th>
                    <th>Selling Price</th>
                    <th>Total Qty</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $total_qty = 0;
            $total_sales = 0;
            $total_buy_price = 0;
            foreach ($results as $result):
                $qty = (int)$result['total_sales']; // Assuming this is the deducted quantity
                $buy_price = (float)$result['buy_price'];
                $sale_price = (float)$result['sale_price'];
                $total_saleing_price = (float)$result['total_saleing_price'];
                $total_qty += $qty;
                $total_sales += $total_saleing_price;
                $total_buy_price += $qty * $buy_price;
                ?>
                <tr>
                    <td><?php echo remove_junk($result['date']); ?></td>
                    <td><?php echo remove_junk(ucfirst($result['name'])); ?></td>
                    <td class="text-right"><?php echo number_format($buy_price, 2); ?></td>
                    <td class="text-right"><?php echo number_format($sale_price, 2); ?></td>
                    <td class="text-right"><?php echo number_format($qty); ?></td>
                    <td class="text-right"><?php echo number_format($total_saleing_price, 2); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4"></td>
                <td>Total Qty Deducted</td>
                <td class="text-right"><?php echo number_format($total_qty); ?></td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td>Grand Total Sales</td>
                <td class="text-right">₱ <?php echo number_format($total_sales, 2); ?></td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td>Profit</td>
                <td class="text-right">₱ <?php echo number_format($total_sales - $total_buy_price, 2); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php
else:
    $session->msg("d", "Sorry, no sales have been found.");
    redirect('sales_report.php', false);
endif;
?>
<script>
    window.onload = function() {
        window.print();
    }
</script>
</body>
</html>
<?php if (isset($db)) { $db->db_disconnect(); } ?>
