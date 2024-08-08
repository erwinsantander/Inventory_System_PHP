<?php
$page_title = 'Add Sale';
require_once('includes/load.php');
page_require_level(3);

$msg = [];
$errors = [];
$products = [];

if (isset($_POST['add_sale'])) {
    $req_fields = array('s_id', 'quantity', 'price', 'total', 'date');
    validate_fields($req_fields);
    
    if (empty($errors)) {
        $p_id = $db->escape((int)$_POST['s_id']);
        $s_qty = $db->escape((int)$_POST['quantity']);
        $s_total = $db->escape($_POST['total']);
        $date = $db->escape($_POST['date']);
        $s_date = make_date();

        $sql = "INSERT INTO sales (product_id, qty, price, date) VALUES ('{$p_id}', '{$s_qty}', '{$s_total}', '{$s_date}')";

        if ($db->query($sql)) {
            update_product_qty($s_qty, $p_id);
            $session->msg('s', "Sale added.");
            redirect('add_sale.php', false);
        } else {
            $session->msg('d', 'Sorry, failed to add!');
            redirect('add_sale.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('add_sale.php', false);
    }
}

$sales_query = "
    SELECT sales.id, sales.product_id, sales.price, sales.qty, sales.date, name AS product_name
    FROM sales
    JOIN products ON sales.product_id = products.id
";
$sales_result = $db->query($sales_query);

$products_query = "SELECT id, name FROM products";
$products_result = $db->query($products_query);

include_once('layouts/header.php');
?>

<div class="row">
  <div class="col-md-6">
    <form method="post" action="add_sale.php">
        <div class="form-group">
            <label for="s_id">Product:</label>
            <select class="form-control" name="s_id" id="s_id" required>
                <option value="">Select Product</option>
                <?php while ($product = $products_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($product['id']); ?>">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" class="form-control" name="quantity" id="quantity" required>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="text" class="form-control" name="price" id="price" readonly>
        </div>
        <div class="form-group">
            <label for="total">Total:</label>
            <input type="text" class="form-control" name="total" id="total" readonly>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" class="form-control" name="date" id="date" required>
        </div>
        <button type="submit" name="add_sale" class="btn btn-primary">Add Sale</button>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#s_id').change(function() {
        var productId = $(this).val();
        if (productId) {
            $.ajax({
                url: 'get_product_price.php',
                type: 'GET',
                data: {product_id: productId},
                dataType: 'json',
                success: function(data) {
                    if (data.price) {
                        $('#price').val(data.price);
                        calculateTotal();
                    }
                }
            });
        }
    });

    $('#quantity').on('input', function() {
        calculateTotal();
    });

    function calculateTotal() {
        var price = parseFloat($('#price').val()) || 0;
        var quantity = parseInt($('#quantity').val()) || 0;
        var total = price * quantity;
        $('#total').val(total.toFixed(2));
    }
});
</script>

<?php if (isset($msg)): ?>
<script>
    Swal.fire({
        icon: '<?php echo htmlspecialchars($msg['type']); ?>',
        title: '<?php echo htmlspecialchars($msg['message']); ?>',
        position: 'center',
        showConfirmButton: true
    });
</script>
<?php endif; ?>

<?php include_once('layouts/footer.php'); ?>
