<?php 
session_start();
error_reporting(0);
include('includes/config.php');
if(isset($_GET['action']) && $_GET['action']=="add"){
	$id=intval($_GET['id']);
	if(isset($_SESSION['cart'][$id])){
		$_SESSION['cart'][$id]['quantity']++;
	}else{
		$sql_p="SELECT * FROM products WHERE id={$id}";
		$query_p=mysqli_query($con,$sql_p);
		if(mysqli_num_rows($query_p)!=0){
			$row_p=mysqli_fetch_array($query_p);
			$_SESSION['cart'][$row_p['id']]=array("quantity" => 1, "price" => $row_p['productPrice']);
			echo "<script>alert('Producto agregado al carrito')</script>";
			echo "<script type='text/javascript'> document.location ='my-cart.php'; </script>";
		}else{
			$message="ID de producto no válido";
		}
	}
}
$pid=intval($_GET['pid']);
if(isset($_GET['pid']) && $_GET['action']=="wishlist" ){
	if(strlen($_SESSION['login'])==0)
    {   
		header('location:login.php');
	}
	else
	{
		mysqli_query($con,"INSERT INTO wishlist(userId,productId) VALUES('".$_SESSION['id']."','$pid')");
		echo "<script>alert('Producto agregado a la lista de deseos');</script>";
		header('location:my-wishlist.php');
	}
}
if(isset($_POST['submit'])){
	$qty=$_POST['quality'];
	$price=$_POST['price'];
	$value=$_POST['value'];
	$name=$_POST['name'];
	$summary=$_POST['summary'];
	$review=$_POST['review'];
	mysqli_query($con,"INSERT INTO productreviews(productId,quality,price,value,name,summary,review) VALUES('$pid','$qty','$price','$value','$name','$summary','$review')");
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<meta name="description" content="">
		<meta name="author" content="">
	    <meta name="keywords" content="MediaCenter, Template, eCommerce">
	    <meta name="robots" content="all">
	    <title>Detalles del producto</title>
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="assets/css/main.css">
	    <link rel="stylesheet" href="assets/css/green.css">
	    <link rel="stylesheet" href="assets/css/owl.carousel.css">
		<link rel="stylesheet" href="assets/css/owl.transitions.css">
		<link href="assets/css/lightbox.css" rel="stylesheet">
		<link rel="stylesheet" href="assets/css/animate.min.css">
		<link rel="stylesheet" href="assets/css/rateit.css">
		<link rel="stylesheet" href="assets/css/bootstrap-select.min.css">
		<link rel="stylesheet" href="assets/css/config.css">

		<link href="assets/css/green.css" rel="alternate stylesheet" title="Green color">
		<link href="assets/css/blue.css" rel="alternate stylesheet" title="Blue color">
		<link href="assets/css/red.css" rel="alternate stylesheet" title="Red color">
		<link href="assets/css/orange.css" rel="alternate stylesheet" title="Orange color">
		<link href="assets/css/dark-green.css" rel="alternate stylesheet" title="Darkgreen color">
		<link rel="stylesheet" href="assets/css/font-awesome.min.css">

        <!-- Fonts --> 
		<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
		<link rel="shortcut icon" href="assets/images/favicon.ico">
	</head>
    <body class="cnt-home">
	
<header class="header-style-1">
<?php include('includes/top-header.php');?>
<?php include('includes/main-header.php');?>
<?php include('includes/menu-bar.php');?>
</header>

<div class="breadcrumb">
	<div class="container">
		<div class="breadcrumb-inner">
<?php
$ret=mysqli_query($con,"SELECT category.categoryName AS catname,subCategory.subcategory AS subcatname,products.productName AS pname FROM products JOIN category ON category.id=products.category JOIN subcategory ON subcategory.id=products.subCategory WHERE products.id='$pid'");
while ($rw=mysqli_fetch_array($ret)) {
?>
			<ul class="list-inline list-unstyled">
				<li><a href="index.php">Inicio</a></li>
				<li><?php echo htmlentities($rw['catname']);?></a></li>
				<li><?php echo htmlentities($rw['subcatname']);?></li>
				<li class='active'><?php echo htmlentities($rw['pname']);?></li>
			</ul>
<?php }?>
		</div>
	</div>
</div>

<div class="body-content outer-top-xs">
	<div class='container'>
		<div class='row single-product outer-bottom-sm '>
			<div class='col-md-3 sidebar'>
				<div class="sidebar-module-container">
					<?php include('includes/sidebar.php');?>
				</div>
			</div>

<?php 
$ret=mysqli_query($con,"SELECT * FROM products WHERE id='$pid'");
while($row=mysqli_fetch_array($ret)) {
?>
			<div class='col-md-9'>
				<div class="row wow fadeInUp">
					<div class="col-sm-6 col-md-5 gallery-holder">
						<!-- Aquí el código para la galería de imágenes del producto -->
					</div>
					<div class='col-sm-6 col-md-7 product-info-block'>
						<div class="product-info">
							<h1 class="name"><?php echo htmlentities($row['productName']);?></h1>

							<!-- Mostrando el stock disponible -->
							<div class="stock-container info-container m-t-10">
								<div class="row">
									<div class="col-sm-3">
										<div class="stock-box">
											<span class="label">Stock disponible :</span>
										</div>	
									</div>
									<div class="col-sm-9">
										<div class="stock-box">
											<span class="value"><?php echo htmlentities($row['stock']);?> unidades</span>
										</div>	
									</div>
								</div>
							</div>

							<!-- Resto de la información del producto -->
							<div class="price-container info-container m-t-20">
								<div class="row">
									<div class="col-sm-6">
										<div class="price-box">
											<span class="price">CLP. <?php echo htmlentities($row['productPrice']);?></span>
											<span class="price-strike">CLP. <?php echo htmlentities($row['productPriceBeforeDiscount']);?></span>
										</div>
									</div>

									<div class="col-sm-6">
										<div class="favorite-button m-t-10">
											<a class="btn btn-primary" data-toggle="tooltip" data-placement="right" title="Wishlist" href="product-details.php?pid=<?php echo htmlentities($row['id'])?>&&action=wishlist">
											    <i class="fa fa-heart"></i>
											</a>
										</div>
									</div>
								</div>
							</div>

							<div class="quantity-container info-container">
								<div class="row">
									<div class="col-sm-2">
										<span class="label">Cantidad :</span>
									</div>
									<div class="col-sm-2">
										<div class="cart-quantity">
											<div class="quant-input">
												<div class="arrows">
													<div class="arrow plus gradient"><span class="ir"><i class="icon fa fa-sort-asc"></i></span></div>
													<div class="arrow minus gradient"><span class="ir"><i class="icon fa fa-sort-desc"></i></span></div>
												</div>
												<input type="text" value="1">
											</div>
										</div>
									</div>

									<div class="col-sm-7">
										<?php if($row['productAvailability']=='En Stock'){?>
											<a href="product-details.php?page=product&action=add&id=<?php echo $row['id']; ?>" class="btn btn-primary"><i class="fa fa-shopping-cart inner-right-vs"></i> Agregar al Carrito</a>
										<?php } else {?>
											<div class="action" style="color:red">Agotado</div>
										<?php } ?>
									</div>
								</div>
							</div>

							<div class="product-social-link m-t-20 text-right">
								<span class="social-label">Compartir :</span>
								<div class="social-icons">
									<ul class="list-inline">
										<li><a class="fa fa-facebook" href="http://facebook.com/transvelo"></a></li>
										<li><a class="fa fa-twitter" href="#"></a></li>
										<li><a class="fa fa-linkedin" href="#"></a></li>
										<li><a class="fa fa-rss" href="#"></a></li>
										<li><a class="fa fa-pinterest" href="#"></a></li>
									</ul>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</div>
<?php } ?>

		</div>
	</div>
</div>
<?php include('includes/footer.php');?>
<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/scripts.js"></script>
</body>
</html>
