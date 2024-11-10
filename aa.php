<?php 
session_start();
error_reporting(0);
include('includes/config.php');

if(isset($_GET['action']) && $_GET['action']=="add") {
    $id = intval($_GET['id']);
    $quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;

    $sql_p = "SELECT * FROM products WHERE id={$id}";
    $query_p = mysqli_query($con, $sql_p);
    if(mysqli_num_rows($query_p) != 0) {
        $row_p = mysqli_fetch_array($query_p);

        // Verificar que la cantidad solicitada no supere el stock disponible
        if ($quantity <= $row_p['stock']) {
            if(isset($_SESSION['cart'][$id])) {
                // Actualizar la cantidad en el carrito, pero limitando al stock disponible
                $_SESSION['cart'][$id]['quantity'] = min($_SESSION['cart'][$id]['quantity'] + $quantity, $row_p['stock']);
            } else {
                // Agregar el producto al carrito con la cantidad solicitada, limitado al stock
                $_SESSION['cart'][$row_p['id']] = array("quantity" => $quantity, "price" => $row_p['productPrice']);
            }
            echo "<script>alert('Producto agregado al carrito');</script>";
            echo "<script type='text/javascript'> document.location ='my-cart.php'; </script>";
        } else {
            echo "<script>alert('La cantidad solicitada supera el stock disponible.');</script>";
            echo "<script type='text/javascript'> document.location ='product-details.php?pid={$id}'; </script>";
        }
    } else {
        echo "<script>alert('ID de producto no válido');</script>";
    }
}

// Código existente para agregar productos a la lista de deseos, manejo de reseñas, etc.

$pid = intval($_GET['pid']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta y enlaces de estilos existentes -->
</head>
<body class="cnt-home">

<header class="header-style-1">
    <?php include('includes/top-header.php'); ?>
    <?php include('includes/main-header.php'); ?>
    <?php include('includes/menu-bar.php'); ?>
</header>

<div class="breadcrumb">
    <!-- Código del breadcrumb -->
</div>

<div class="body-content outer-top-xs">
    <div class='container'>
        <div class='row single-product outer-bottom-sm '>
            <div class='col-md-3 sidebar'>
                <?php include('includes/sidebar.php'); ?>
            </div>
            
            <?php 
            $ret = mysqli_query($con, "SELECT * FROM products WHERE id='$pid'");
            while ($row = mysqli_fetch_array($ret)) {
            ?>

            <div class='col-md-9'>
                <div class="row wow fadeInUp">
                    <div class="col-xs-12 col-sm-6 col-md-5 gallery-holder">
                        <!-- Galería de productos -->
                    </div>
                    
                    <div class='col-sm-6 col-md-7 product-info-block'>
                        <div class="product-info">
                            <h1 class="name"><?php echo htmlentities($row['productName']); ?></h1>
                            <div class="stock-container info-container m-t-10">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="stock-box">
                                            <span class="label">Stock disponible :</span>
                                        </div>    
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="stock-box">
                                            <span class="value"><?php echo htmlentities($row['stock']); ?> unidades</span>
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
                                                <!-- Limitar el input al stock disponible -->
                                                <input type="number" value="1" name="quantity" min="1" max="<?php echo htmlentities($row['stock']); ?>" id="product-quantity">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-7">
                                        <?php if($row['productAvailability']=='En Stock'){ ?>
                                            <a href="product-details.php?page=product&action=add&id=<?php echo $row['id']; ?>&quantity=" class="btn btn-primary add-to-cart-button"><i class="fa fa-shopping-cart inner-right-vs"></i> Agregar al Carrito</a>
                                        <?php } else { ?>
                                            <div class="action" style="color:red">Agotado</div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección de precios, etc. -->

                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<!-- Otros scripts -->

<!-- Validación con JavaScript para evitar exceder el stock -->
<script>
document.getElementById('product-quantity').addEventListener('input', function () {
    const maxStock = <?php echo $row['stock']; ?>;
    if (parseInt(this.value) > maxStock) {
        alert('La cantidad seleccionada supera el stock disponible.');
        this.value = maxStock;
    }
});

// Actualizar el enlace del botón "Agregar al Carrito" con la cantidad seleccionada
document.getElementById('product-quantity').addEventListener('change', function () {
    const quantity = this.value;
    const addToCartButton = document.querySelector('.add-to-cart-button');
    addToCartButton.setAttribute('href', addToCartButton.getAttribute('href') + quantity);
});
</script>

</body>
</html>
