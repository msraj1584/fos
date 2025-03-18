 <!-- Masthead-->
 <header class="masthead">
            <div class="container h-100">
                <div class="row h-100 align-items-center justify-content-center text-center">
                    <div class="col-lg-10 align-self-center mb-4 page-title">
                    	<h1 class="text-white">Your Orders</h1>
                        <hr class="divider my-4 bg-dark" />
                    </div>
                    
                </div>
            </div>
        </header>
		<section class="page-section" id="orders">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="sticky">
                    <div class="card">
                        <div class="card-body bg-primary">
                            <div class="row">
                                <div class="col-md-12"><b>Orders</b></div>                                
                            </div>
                        </div>
                    </div>
                </div>

                <?php 
                if(isset($_SESSION['login_user_id'])){
                    $user_id = $_SESSION['login_user_id'];    
                    $orders = $conn->query("SELECT * FROM orders oo left join status_type st on oo.status=st.status_id WHERE user_id = '$user_id' order by oo.id desc");
                    while($order = $orders->fetch_assoc()):
                        $order_id = $order['id'];
                        $total_order = 0;
                        $status = $order['status']; // Order status
                ?>
                
                <div class="card mt-3 bg-warning" >
                    <div class="card-body">
                    <div class="col-md-12">
                                <b>Order ID #<?php echo $order_id; ?></b><br>
                                <small>Date: <?php echo $order['order_date']; ?></small><br>
                                <b>Total Amount: <?php echo number_format($order['total_amount'], 2); ?></b><br/>
                                <small>Status: 
                                    <span class="badge 
                                        <?php 
                                        if($status == '0') echo 'badge-danger';
                                        elseif($status == '1') echo 'badge-warning';
                                        elseif($status == '2') echo 'badge-info';
                                        elseif($status == '3') echo 'badge-secondary';
                                        elseif($status == '4') echo 'badge-success';
                                        elseif($status == '10') echo 'badge-primary';
                                        ?>">
                                        <?php echo $order['status_name']; ?>
                                    </span>
                                </small>
                            </div>
                        <div class="mt-3">
                            <b>Items:</b>
                            <?php 
                            $items = $conn->query("SELECT oi.*, p.name, p.img_path, p.description 
                                                   FROM order_list oi 
                                                   INNER JOIN product_list p ON p.id = oi.product_id 
                                                   WHERE oi.order_id = '$order_id'");
                            while($item = $items->fetch_assoc()):
                                $total_order += ($item['qty'] * $item['price']);
                            ?>

                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 d-flex align-items-center" style="text-align: -webkit-center">
                                            <div class="col-auto">    
                                                <img src="assets/img/<?php echo $item['img_path'] ?>" alt="" width="50" height="50">
                                            </div>
                                            <div class="col-auto flex-shrink-1 flex-grow-1 text-center">
                                                <p><b><?php echo $item['name'] ?></b></p>
                                            </div>  
                                        </div>
                                        <div class="col-md-4">
                                            <p><b>Quantity:</b> <?php echo $item['qty'] ?></p>
                                            <p><b>Unit Price:</b> <?php echo number_format($item['price'], 2) ?></p>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <b><?php echo number_format($item['qty'] * $item['price'], 2) ?></b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>

                        
                    </div>
                </div>

                <?php 
                    endwhile;
                }
                ?>
            </div>
        </div>
    </div>
</section>

    <style>
    	.card p {
    		margin: unset
    	}
    	.card img{
		    max-width: calc(100%);
		    max-height: calc(59%);
    	}
    	div.sticky {
		  position: -webkit-sticky; /* Safari */
		  position: sticky;
		  top: 4.7em;
		  z-index: 10;
		  background: white
		}
		.rem_cart{
		   position: absolute;
    	   left: 0;
		}
    </style>
    <script>
        
        $('.view_prod').click(function(){
            uni_modal_right('Product','view_prod.php?id='+$(this).attr('data-id'))
        })
        $('.qty-minus').click(function(){
		var qty = $(this).parent().siblings('input[name="qty"]').val();
		update_qty(parseInt(qty) -1,$(this).attr('data-id'))
		if(qty == 1){
			return false;
		}else{
			 $(this).parent().siblings('input[name="qty"]').val(parseInt(qty) -1);
		}
		})
		$('.qty-plus').click(function(){
			var qty =  $(this).parent().siblings('input[name="qty"]').val();
				 $(this).parent().siblings('input[name="qty"]').val(parseInt(qty) +1);
		update_qty(parseInt(qty) +1,$(this).attr('data-id'))
		})
		function update_qty(qty,id){
			start_load()
			$.ajax({
				url:'admin/ajax.php?action=update_cart_qty',
				method:"POST",
				data:{id:id,qty},
				success:function(resp){
					if(resp == 1){
						load_cart()
						end_load()
					}
				}
			})

		}
		$('#checkout').click(function(){
			if('<?php echo isset($_SESSION['login_user_id']) ?>' == 1){
				location.replace("index.php?page=checkout")
			}else{
				uni_modal("Checkout","login.php?page=checkout")
			}
		})
    </script>
	
