<div class="container-fluid">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Qty</th>
                <th>Order</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            include 'db_connect.php';
            $qry = $conn->query("SELECT * FROM order_list o INNER JOIN product_list p ON o.product_id = p.id WHERE order_id = ".$_GET['id']);
            while($row=$qry->fetch_assoc()):
                $total += $row['qty'] * $row['price'];
            ?>
            <tr>
                <td><?php echo $row['qty'] ?></td>
                <td><?php echo $row['name'] ?></td>
                <td><?php echo number_format($row['qty'] * $row['price'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">TOTAL</th>
                <th><?php echo number_format($total, 2) ?></th>
            </tr>
        </tfoot>
    </table>
    <div class="text-center">
        <button class="btn btn-primary" id="confirm" type="button" onclick="confirm_order()">Confirm</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button class="btn btn-success" type="button" onclick="print_receipt()">Print Receipt</button>
    </div>
</div>

<style>
    #uni_modal .modal-footer {
        display: none;
    }
</style>

<script>
    function confirm_order() {
        Swal.fire({
            title: 'Confirm Order',
            text: 'Are you sure you want to confirm this order?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, confirm!'
        }).then((result) => {
            if (result.isConfirmed) {
                start_load();
                $.ajax({
                    url: 'ajax.php?action=confirm_order',
                    method: 'POST',
                    data: { id: '<?php echo $_GET['id'] ?>' },
                    success: function(resp) {
                        if (resp == 1) {
                            Swal.fire(
                                'Confirmed!',
                                'Order has been successfully confirmed.',
                                'success'
                            ).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                'Error confirming order: ' + resp,
                                'error'
                            );
                        }
                        end_load();
                    },
                    error: function() {
                        end_load();
                        Swal.fire(
                            'Error!',
                            'AJAX request failed.',
                            'error'
                        );
                    }
                });
            }
        });
    }

   
    function print_receipt() {
        var printContents = document.querySelector('.container-fluid').innerHTML;
        var receiptWindow = window.open('', '', 'height=600,width=800,location=no');
       
        receiptWindow.document.write('<style>');
        receiptWindow.document.write('table { border-collapse: collapse; width: 100%; }');
        receiptWindow.document.write('th, td { border: 1px solid black; padding: 8px; text-align: left; font-size: 14px; }');
        receiptWindow.document.write('th { background-color: #f2f2f2; }');
        receiptWindow.document.write('body { font-size: 12px; }');
        receiptWindow.document.write('@media print { body { font-size: 10px; } }');
        receiptWindow.document.write('</style>');
        receiptWindow.document.write('</head><body>');
        receiptWindow.document.write('<h1 style="font-size: 20px; text-align: center;">Receipt</h1>');
        receiptWindow.document.write('<div class="container-fluid">');
        receiptWindow.document.write('<table class="table table-bordered">' + 
                                      '<thead>' + 
                                      '<tr>' + 
                                      '<th>Qty</th>' + 
                                      '<th>Order</th>' + 
                                      '<th>Amount</th>' + 
                                      '</tr>' + 
                                      '</thead>' + 
                                      '<tbody>' + 
                                      '<?php 
                                      $total = 0;
                                      include 'db_connect.php';
                                      $qry = $conn->query("SELECT * FROM order_list o INNER JOIN product_list p ON o.product_id = p.id WHERE order_id = ".$_GET['id']);
                                      while($row=$qry->fetch_assoc()):
                                          $total += $row['qty'] * $row['price'];
                                      ?>
                                      <tr>' + 
                                      '<td><?php echo $row['qty'] ?></td>' + 
                                      '<td><?php echo $row['name'] ?></td>' + 
                                      '<td><?php echo number_format($row['qty'] * $row['price'], 2) ?></td>' + 
                                      '</tr>' + 
                                      '<?php endwhile; ?>' + 
                                      '</tbody>' + 
                                      '<tfoot>' + 
                                      '<tr>' + 
                                      '<th colspan="2" class="text-right">TOTAL</th>' + 
                                      '<th><?php echo number_format($total, 2) ?></th>' + 
                                      '</tr>' + 
                                      '</tfoot>' + 
                                      '</table>' + 
                                      '<div class="text-center">' + 
                                     '</div>' + 
                                      '</div>');
        receiptWindow.document.write('</body></html>');
        receiptWindow.document.close();
        receiptWindow.print();
    }
</script>
