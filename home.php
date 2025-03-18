
<div class="mt-5"></div>


<section class="home" id="home">
    <div class="container">
        <div class="row d-flex align-items-center justify-content-center">
            <div class="col-md-6 col-sm-12 ">
                <h1 class="main-head"><?php echo $_SESSION['setting_name'] ?></h1>
                <p>We believe that every meal tells a story. Our mission is to bring the warmth and comfort of homemade meals straight to your table. Whether you’re a busy professional, a student, or a family on the go, we offer a delicious variety of dishes crafted with love and care, just like mom used to make.</p>
                <a href="#menu" class="btn btn-primary">Order Now</a>
            </div>
            <div class="col-md-6 col-sm-12 mt-5">
                <img data-tilt src="assets/img/<?php echo $_SESSION['setting_home_img'] ?>" alt="Home image" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<section class="page-section" id="menu">
    <div style="text-align:center;">
        <h2 class="menu-head">MENU</h2>
    </div>
    <div id="menu-field" class="row card-deck">
        <?php 
            include 'admin/db_connect.php';
            $qry = $conn->query("SELECT * FROM product_list WHERE status=1 ORDER BY RAND()");
            while ($row = $qry->fetch_assoc()):
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6" style="margin-bottom: 20px;">
            <div class="card menu-item mt-2">
                <img src="assets/img/<?php echo $row['img_path'] ?>" class="card-img-top" alt="..." style="height:250px; width:100%;">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $row['name'] ?></h5>
                    <p class="card-text truncate" style="height:50px;"><?php echo $row['description'] ?></p>
                    <h6 class="card-title">Price: ₹<?php echo $row['price'] ?></h6>
                    <div class="text-center">
                        <button class="btn btn-sm btn-outline-primary view_prod btn-block" data-id="<?php echo $row['id'] ?>">
                            <i class="fa fa-eye"></i> View
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<script>
    $('.view_prod').click(function() {
        uni_modal_right('Product', 'view_prod.php?id=' + $(this).attr('data-id'));
    });
</script>

<style>

.main-head{
    color:red;
    font-weight:700;
}

.menu-head{
    color:red;
    font-weight:700;
}
    /* Custom styles for responsiveness */
    @media (max-width: 768px) {
        .home-wrapper {
            flex-direction: column;
        }
    }
</style>
