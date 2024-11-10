<?php 
session_start();
error_reporting(0);
include('includes/config.php');

if(isset($_POST['submit'])){
    if(!empty($_SESSION['cart'])){
        foreach($_POST['quantity'] as $key => $val){
            // Obtener el stock disponible de la base de datos
            $sql = "SELECT stock FROM products WHERE id='$key'";
            $query = mysqli_query($con, $sql);
            $row = mysqli_fetch_array($query);
            $availableStock = $row['stock'];

            // Validar la cantidad ingresada contra el stock disponible
            if ($val > $availableStock) {
                $val = $availableStock; // Ajustar la cantidad al máximo disponible
                echo "<script>alert('La cantidad solicitada para el producto ID $key supera el stock disponible. La cantidad ha sido ajustada al máximo permitido ($availableStock).');</script>";
            }

            if($val == 0){
                unset($_SESSION['cart'][$key]);
            } else {
                $_SESSION['cart'][$key]['quantity'] = $val;
            }
        }
        echo "<script>alert('Su carrito ha sido actualizado');</script>";
    }
}

// Código para eliminar un producto del carrito
if(isset($_POST['remove_code'])) {
    if(!empty($_SESSION['cart'])){
        foreach($_POST['remove_code'] as $key){
            unset($_SESSION['cart'][$key]);
        }
        echo "<script>alert('Su carrito ha sido actualizado');</script>";
    }
}

// Código para procesar el pedido
if(isset($_POST['ordersubmit'])) {
    if(strlen($_SESSION['login']) == 0) {   
        header('location:login.php');
    } else {
        $cartItems = [];
        $totalAmount = 0;

        foreach($_SESSION['cart'] as $id => $item) {
            $query = mysqli_query($con, "SELECT * FROM products WHERE id='$id'");
            $product = mysqli_fetch_array($query);

            $subtotal = $item['quantity'] * $product['productPrice'] + $product['shippingCharge'];
            $totalAmount += $subtotal;

            $cartItems[] = [
                'productId' => $id,
                'quantity' => $item['quantity']
            ];
        }
        
        $_SESSION['totalAmount'] = $totalAmount;
        $_SESSION['orderDetails'] = [
            'items' => $cartItems,
            'totalAmount' => $totalAmount
        ];

        header('location:payment-method.php');
        exit();
    }
}

// Código para actualizar la dirección de facturación y de envío...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Mi Carrito</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Otros enlaces de estilos -->
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
    <div class="container">
        <div class="row inner-bottom-sm">
            <div class="shopping-cart">
                <div class="col-md-12 col-sm-12 shopping-cart-table">
                    <div class="table-responsive">
                        <form name="cart" method="post" onsubmit="return validateCartQuantities();">
                            <?php if(!empty($_SESSION['cart'])) { ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Eliminar</th>
                                            <th>Imagen</th>
                                            <th>Nombre del Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio por unidad</th>
                                            <th>Gastos de envío</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $sql = "SELECT * FROM products WHERE id IN(";
                                    foreach($_SESSION['cart'] as $id => $value) {
                                        $sql .= $id . ",";
                                    }
                                    $sql = substr($sql, 0, -1) . ") ORDER BY id ASC";
                                    $query = mysqli_query($con, $sql);
                                    $totalprice = 0;

                                    if(!empty($query)) {
                                        while($row = mysqli_fetch_array($query)) {
                                            $quantity = $_SESSION['cart'][$row['id']]['quantity'];
                                            $subtotal = $quantity * $row['productPrice'] + $row['shippingCharge'];
                                            $totalprice += $subtotal;

                                            // Asignar el stock máximo a un atributo en el input de cantidad
                                            echo "<tr>";
                                            echo "<td><input type='checkbox' name='remove_code[]' value='" . htmlentities($row['id']) . "' /></td>";
                                            echo "<td><img src='admin/productimages/" . $row['id'] . "/" . $row['productImage1'] . "' width='114' height='146'></td>";
                                            echo "<td><h4>" . $row['productName'] . "</h4></td>";
                                            echo "<td><input type='number' name='quantity[" . $row['id'] . "]' value='" . $quantity . "' min='1' max='" . $row['stock'] . "' class='cart-quantity' data-max='" . $row['stock'] . "'></td>";
                                            echo "<td>" . "CLP " . $row['productPrice'] . ".00</td>";
                                            echo "<td>" . "CLP " . $row['shippingCharge'] . ".00</td>";
                                            echo "<td>" . "CLP " . $subtotal . ".00</td>";
                                            echo "</tr>";
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            <?php } else { echo "Su carrito de compras está vacío"; } ?>
                            <input type="submit" name="submit" value="Actualizar Carrito" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>

<script>
function validateCartQuantities() {
    let valid = true;
    document.querySelectorAll('.cart-quantity').forEach(function(input) {
        const max = parseInt(input.getAttribute('data-max'));
        const quantity = parseInt(input.value);

        if (quantity > max) {
            alert(`La cantidad para el producto ID ${input.name.replace('quantity[', '').replace(']', '')} supera el stock disponible de ${max}.`);
            input.value = max;  // Ajustar al máximo disponible
            valid = false;
        }
    });
    return valid;
}
</script>

</body>
</html>
