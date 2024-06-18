<?php
include_once("../../inc/autoload.php");

$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);

$wine = new wine($cleanUID);

?>

<div class="card mb-3">
	<div class="card-body">
		<h5 class="card-title"><?php echo $wine->name; ?></h5>
		<p class="card-text">
			<div class="row">
				<div class="col">
					<label for="qty" class="form-label">Qty.</label>
					<input type="number" class="form-control" id="qty" name="qty[]" placeholder="Qty" value="1">
					<div id="qtyHelpBlock" class="form-text">
					  Available: <?php echo $wine->qty; ?>
					</div>
				</div>
				<div class="col">
					<label for="price" class="form-label">Price/each</label>
					<input type="number" class="form-control" id="price" name="price[]" placeholder="Price/each" value="<?php echo $wine->price_external; ?>">
					<div id="priceHelpBlock" class="form-text">
					  Internal / External Price: <?php echo currencyDisplay($wine->price_internal) . " / " . currencyDisplay($wine->price_external); ?>
					</div>
				</div>
			</div>
		</p>
	</div>
	<input type="hidden" id="uid" name="uid[]" value="<?php echo $wine->uid; ?>">
</div>