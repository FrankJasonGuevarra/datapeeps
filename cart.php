<?php
session_start();
$product_ids = array();
//session_destroy();
//check if submit
if(filter_input(INPUT_POST,'add_to_cart')){
	if(isset($_SESSION['shopping_cart'])){
		$count = count($_SESSION['shopping_cart']);
		$product_ids = array_column($_SESSION['shopping_cart'], 'id');
		pre_r($product_ids);
		if(!in_array(filter_input(INPUT_GET, 'id'), $product_ids)){
			$_SESSION['shopping_cart'][$count] = array(
				'id' => filter_input(INPUT_GET,'id'),
				'name' => filter_input(INPUT_POST,'name'),
				'price' => filter_input(INPUT_POST,'price'),
				'quantity' => filter_input(INPUT_POST,'quantity')
			);
		}else{
			//if product exists just increase the quantity of existing item
			for($i=0; $i < count($product_ids); $i++){
				if($product_ids[$i] == filter_input(INPUT_GET,'id') ){
					$_SESSION['shopping_cart'][$i]['quantity'] += filter_input(INPUT_POST,'quantity');
				}
			}
		}
		
	}else{
		//if shopping cart not exist
		$_SESSION['shopping_cart'][0] = array(
			'id' => filter_input(INPUT_GET,'id'),
			'name' => filter_input(INPUT_POST,'name'),
			'price' => filter_input(INPUT_POST,'price'),
			'quantity' => filter_input(INPUT_POST,'quantity')
		);
	}
}

pre_r($_SESSION);

function pre_r($array){
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Shopping Cart</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
		<link rel="stylesheet" href="cart.css" />
	</head>
	<body>
		<div class="container">
		<?php
			$mysqli = new mysqli('localhost', 'root', '123456', 'cart');

			if ($mysqli->connect_error) {
				die('Connect Error (' . $mysqli->connect_errno . ') '
						. $mysqli->connect_error);
			}
			//echo '<p>Connection OK '. $mysqli->host_info.'</p>';
			//echo '<p>Server '.$mysqli->server_info.'</p>';

			$query = 'SELECT * FROM products ORDER BY id';
			$result = mysqli_query($mysqli,$query);
			if ($result):
				if(mysqli_num_rows($result)>0):
					while($product = mysqli_fetch_assoc($result)):
					?>
					<div class="col-sm-4 col-md-3" >
						<form method="post" action="cart.php?action=add&id=<?php echo $product['id'];?>">
							<div class="products">
								<img style="height: 150px" src="<?php echo $product['image'];?>" class="img-responsive" />
								<h4 class="text-info"><?php echo $product['name'];?></h4>
								<h4>PHP <?php echo $product['price']; ?></h4>
								<input type="text" name="quantity" class="form-control" value="1" />
								<input type="hidden" name="name" value="<?php echo $product['name']; ?>" />
								<input type="hidden" name="price" value="<?php echo $product['price']; ?>" />
								<input type="submit" name="add_to_cart" class="btn btn-info" style="margin-top: 5px" value="Add to Cart" />
							</div>
						</form>
					</div>
					<?php
					endwhile;
				endif;	
			endif;	
			$mysqli->close();
		?>
		</div>
	</body>
</html>
