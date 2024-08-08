<?php
  $page_title = 'Admin Home Page';
  require_once('includes/load.php');
  page_require_level(1);

  $c_categorie     = count_by_id('categories');
  $c_product       = count_by_id('products');
  $c_sale          = count_by_id('sales');
  $c_user          = count_by_id('users');
  $products_sold   = find_higest_saleing_product('10');
  $recent_products = find_recent_product_added('5');
  $recent_sales    = find_recent_sale_added('5');

  // Fetch total sales and quantity sold
  $total_sales = get_total_sales(); // Implement this function as needed
  $total_quantity_sold = get_total_quantity_sold(); // Implement this function as needed
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
   <div class="col-md-6">
   </div>
</div>
<div class="row">
    <a href="users.php" style="color:black;">
        <div class="col-md-3">
           <div class="panel clearfix">
             <div class="panel-icon pull-left bg-secondary1">
              <i class="glyphicon glyphicon-user"></i>
            </div>
            <div class="panel-value pull-right">
              <h2 class="margin-top">&nbsp; <?php echo $c_user['total']; ?> </h2>
              <p class="text-muted"> &nbsp;Users</p>
            </div>
           </div>
        </div>
    </a>
    <a href="categorie.php" style="color:black;">
        <div class="col-md-3">
           <div class="panel clearfix">
             <div class="panel-icon pull-left bg-red">
              <i class="glyphicon glyphicon-th-large"></i>
            </div>
            <div class="panel-value pull-right">
              <h2 class="margin-top">&nbsp; <?php echo $c_categorie['total']; ?> </h2>
              <p class="text-muted"> &nbsp;Categories</p>
            </div>
           </div>
        </div>
    </a>
    <a href="product.php" style="color:black;">
        <div class="col-md-3">
           <div class="panel clearfix">
             <div class="panel-icon pull-left bg-blue2">
              <i class="glyphicon glyphicon-shopping-cart"></i>
            </div>
            <div class="panel-value pull-right">
              <h2 class="margin-top">&nbsp; <?php echo $c_product['total']; ?> </h2>
              <p class="text-muted"> &nbsp;Products</p>
            </div>
           </div>
        </div>
    </a>
    <a href="sales.php" style="color:black;">
        <div class="col-md-3">
           <div class="panel clearfix">
             <div class="panel-icon pull-left bg-green">
              <i class="fas fa-money-bill-alt"></i>
            </div>
            <div class="panel-value pull-right">
              <h2 class="margin-top">&nbsp; <?php echo number_format($total_sales, 2); ?></h2>
              <p class="text-muted"> &nbsp;Sales</p>
              <p class="text-muted"> &nbsp;Quantity Sold: <?php echo (int)$total_quantity_sold; ?></p>
            </div>
           </div>
        </div>
    </a>
</div>

<!-- Monthly Sales Chart -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Monthly Sales from 2021 to 2024</span>
        </strong>
      </div>
      <div class="panel-body">
        <canvas id="monthlySalesChart" width="400" height="200"></canvas>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  <?php
  $years = range(2021, 2024);
  $months = [
    '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun',
    '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
  ];
  
  $labels = [];
  $data = [];
  
  foreach ($months as $num => $name) {
    foreach ($years as $year) {
      $labels[] = "$name $year";
      $data[$year][$num] = 0;
    }
  }
  
  foreach ($years as $year) {
    $monthly_sales = get_monthly_sales($year); // Implement this function as needed
    while ($row = $monthly_sales->fetch_assoc()) {
      $month = str_pad($row['month'], 2, '0', STR_PAD_LEFT);
      $data[$year][$month] = floatval($row['total_sales']);
    }
  }
  
  // Flatten data for chart
  $chartData = [];
  foreach ($labels as $index => $label) {
    list($month, $year) = explode(' ', $label);
    $monthNum = array_search($month, $months);
    $chartData[$index] = $data[$year][$monthNum] ?? 0;
  }
  ?>

  var ctx = document.getElementById('monthlySalesChart').getContext('2d');
  var chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($labels); ?>,
      datasets: [{
        label: 'Monthly Sales',
        data: <?php echo json_encode($chartData); ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.5)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Sales (₱)'
          }
        }
      },
      plugins: {
        title: {
          display: true,
          text: 'Monthly Sales from 2021 to 2024'
        }
      }
    }
  });
</script>

<!-- Bottom -->
<div class="row">
   <div class="col-md-4">
     <div class="panel panel-default">
       <div class="panel-heading">
         <strong>
           <span class="glyphicon glyphicon-th"></span>
           <span>Highest Selling Products</span>
         </strong>
       </div>
       <div class="panel-body">
         <table class="table table-striped table-bordered table-condensed">
          <thead>
           <tr>
             <th>Title</th>
             <th>Total Sold</th>
             <th>Total Quantity</th>
           <tr>
          </thead>
          <tbody>
            <?php foreach ($products_sold as $product_sold): ?>
              <tr>
                <td><?php echo remove_junk(first_character($product_sold['name'])); ?></td>
                <td><?php echo (int)$product_sold['totalSold']; ?></td>
                <td><?php echo (int)$product_sold['totalQty']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
         </table>
       </div>
     </div>
   </div>
   <div class="col-md-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>LATEST SALES</span>
          </strong>
        </div>
        <div class="panel-body">
          <table class="table table-striped table-bordered table-condensed">
           <thead>
             <tr>
               <th class="text-center" style="width: 50px;">#</th>
               <th>Product Name</th>
               <th>Date</th>
               <th>Total Sale</th>
               <th>Quantity Deducted</th>
             </tr>
           </thead>
           <tbody>
             <?php foreach ($recent_sales as $recent_sale): ?>
             <tr>
               <td class="text-center"><?php echo count_id();?></td>
               <td>
                <a href="edit_sale.php?id=<?php echo (int)$recent_sale['id']; ?>">
                 <?php echo remove_junk(first_character($recent_sale['name'])); ?>
               </a>
               </td>
               <td><?php echo remove_junk(ucfirst($recent_sale['date'])); ?></td>
               <td>₱<?php echo remove_junk(first_character($recent_sale['price'])); ?></td>
               <td><?php echo (int)$recent_sale['quantity']; ?></td>
            </tr>
           <?php endforeach; ?>
           </tbody>
         </table>
        </div>
       </div>
    </div>
   <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Recently Added Products</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="list-group">
      <?php foreach ($recent_products as $recent_product): ?>
            <a class="list-group-item clearfix" href="edit_product.php?id=<?php echo (int)$recent_product['id'];?>">
                <h4 class="list-group-item-heading">
                 <?php if($recent_product['media_id'] === '0'): ?>
                    <img class="img-avatar img-circle" src="uploads/products/no_image.png" alt="">
                  <?php else: ?>
                  <img class="img-avatar img-circle" src="uploads/products/<?php echo $recent_product['image'];?>" alt="" />
                <?php endif;?>
                <?php echo remove_junk(first_character($recent_product['name']));?>
                  <span class="label label-warning pull-right">
                  ₱<?php echo (int)$recent_product['sale_price']; ?>
                  </span>
                </h4>
                <span class="list-group-item-text pull-right">
                <?php echo remove_junk(first_character($recent_product['categorie'])); ?>
              </span>
          </a>
      <?php endforeach; ?>
    </div>
  </div>
 </div>
</div>
 </div>

<?php if ($msg): ?>
<script>
    Swal.fire({
        icon: '<?php echo $msg['type']; ?>',
        title: '<?php echo $msg['message']; ?>',
        position: 'center',
        showConfirmButton: true
    });
</script>
<?php endif; ?>

<?php include_once('layouts/footer.php'); ?>
