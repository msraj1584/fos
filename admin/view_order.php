<div class="container-fluid">
	
	<table class="table table-bordered">
		<thead>
			<tr>				
				<th>Name</th>
				<th>Qty</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$total = 0;
			include 'db_connect.php';
			$qry = $conn->query("SELECT * FROM order_list o inner join product_list p on o.product_id = p.id  where order_id =".$_GET['id']);
			while($row=$qry->fetch_assoc()):
				$total += $row['qty'] * $row['price'];
			?>
			<tr>
			<td><?php echo $row['name'] ?></td>
				<td class="text-right"><?php echo $row['qty'] ?></td>
				
				<td><?php echo number_format($row['qty'] * $row['price'],2) ?></td>
			</tr>
		<?php endwhile; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="2" class="text-right">TOTAL</th>
				<th ><?php echo number_format($total,2) ?></th>
			</tr>

		</tfoot>
	</table>
	<div class="text-center">
		 <!-- Dropdown for status type -->
		 <select id="status_type" class="form-control">
            <?php
 // Fetch the current order status from the 'orders' table based on the provided 'id'
 $order_id = $_GET['id']; // Assuming 'id' is passed as a query parameter
 $order_qry = $conn->query("SELECT status FROM orders WHERE id = $order_id");
 $order = $order_qry->fetch_assoc();
 $current_status_id = $order['status']; // Fetch the current status of the order

            // Fetch status types from the database
            $status_qry = $conn->query("SELECT * FROM status_type");
            while($status = $status_qry->fetch_assoc()):
				
            ?>
                 <option value="<?php echo $status['status_id']; ?>" 
                <?php echo ($status['status_id'] == $current_status_id) ? 'selected' : ''; ?>>
                <?php echo $status['status_name']; ?>
            </option>
            <?php endwhile; ?>
        </select>
        <br>
		<button class="btn btn-primary" id="confirm" type="button" onclick="confirm_order()">Confirm</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

	</div>
</div>
<style>
	#uni_modal .modal-footer{
		display: none
	}
</style>
<script>
	function confirm_order(){
		let status_id = $('#status_type').val();
        if(status_id === '') {
            alert("Please select a status.");
            return;
        }
		start_load()
		$.ajax({
			url:'ajax.php?action=confirm_order',
			method:'POST',
			data: {id: '<?php echo $_GET['id'] ?>', status_id: status_id},
			success:function(resp){
				if(resp == 1){
					alert_toast("Order confirmed.")
                        setTimeout(function(){
                            location.reload()
                        },1500)
				}
			}
		})
	}
</script>