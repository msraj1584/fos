<div class="container-fluid">
	<div class="card">
		<div class="card-body">
			<table class="table table-bordered">
		<thead>
			 <tr>

			<th>Slno</th>
			<th>Order ID</th>
			<th>Name</th>
			<th>Address</th>
			<!-- <th>Email</th> -->
			<th>Mobile</th>
			<th>Date Time</th>
			<th>Amount</th>
			<th>Status</th>
			<th></th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$i = 1;
			include 'db_connect.php';
			$qry = $conn->query("SELECT * FROM orders oo left join status_type st on oo.status=st.status_id order by oo.id desc");
			while($row=$qry->fetch_assoc()):
			 ?>
			 <tr>
			 		<td><?php echo $i++ ?></td>
					 <td class="text-right"><b><?php echo $row['id'] ?></b></td>
			 		<td><?php echo $row['name'] ?></td>
			 		<td><?php echo $row['address'] ?></td>
			 		<!-- <td><?php echo $row['email'] ?></td> -->

			 		<td><?php echo $row['mobile'] ?></td>
					 <td><?php echo $row['order_date'] ?></td>
					 <td class="text-right"><?php echo $row['total_amount'] ?></td>
			 		<?php if($row['status_id'] == 0): ?>
			 			<td class="text-center"><span class="badge badge-danger"><?php echo $row['status_name'] ?></span></td>
					<?php elseif($row['status_id'] == 1): ?>
					<td class="text-center"><span class="badge badge-warning"><?php echo $row['status_name'] ?></span></td>
					<?php elseif($row['status_id'] == 2): ?>
						<td class="text-center"><span class="badge badge-info"><?php echo $row['status_name'] ?></span></td>
					<?php elseif($row['status_id'] == 3): ?>
						<td class="text-center"><span class="badge badge-secondary"><?php echo $row['status_name'] ?></span></td>
					<?php elseif($row['status_id'] == 4): ?>
						<td class="text-center"><span class="badge badge-success"><?php echo $row['status_name'] ?></span></td>
					<?php elseif($row['status_id'] == 10): ?>
						<td class="text-center"><span class="badge badge-primary"><?php echo $row['status_name'] ?></span></td>
			 		<?php else: ?>
			 			<td class="text-center"><span class="badge badge-secondary"></span></td>
			 		<?php endif; ?>
			 		<td>
			 			<button class="btn btn-sm btn-dark view_order" data-id="<?php echo $row['id'] ?>" >View Order</button>
			 		</td>
			 </tr>
			<?php endwhile; ?>
		</tbody>
	</table>
		</div>
	</div>
	
</div>
<script>
	$('.view_order').click(function(){
		uni_modal('Order','view_order.php?id='+$(this).attr('data-id'))
	})
</script>