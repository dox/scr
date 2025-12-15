<?php
$user->pageCheck('wine');

$wines = new Wines();
$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);


// Detect whether this is a new wine
$isNew = empty($cleanUID);

if ($isNew) {
	$formURL = 'index.php?page=meals';
} else {
	$formURL = htmlspecialchars($_SERVER['REQUEST_URI']);
}

// Load existing or empty object
$wine = $isNew ? new Wine() : new Wine($cleanUID);
$bin = new Bin($wine->bin_uid);
$cellar = new Cellar($bin->cellar_uid);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Save or update
	if ($isNew) {
		$newUID = $wine->create($_POST);        // Create new record
		//header("Location: index.php?page=meal&uid={$newUID}");
		exit;
	} else {
		$wine->update($_POST);                // Update existing
		$wine = new Wine($cleanUID);          // Reload object
	}
}

// Title and action buttons
echo pageTitle(
	$isNew ? "Add New Wine" : $wine->clean_name(),
	$isNew ? "" : ""
);
?>

<form class="needs-validation" id="wine_addEdit" novalidate="">

<div class="card mb-3">
	<div class="card-body">
		<div class="row">
			<div class="col-4 mb-3">
				<label for="cellar_uid" class="form-label">Cellar</label>
				<select class="form-select" name="cellar_uid" id="cellar_uid" disabled required>
					<?php
					foreach ($wines->cellars() as $cellarChoice) {
						$title = trim($cellarChoice->name);
						$selected = ($title === $bin->cellar_uid) ? ' selected' : '';
						echo "<option value=\"{$cellarChoice->uid}\"{$selected}>{$title}</option>";
					}
					?>
				</select>
			</div>
			<div class="col-4 mb-3">
				<label for="cellar_uid" class="form-label">Bin</label>
				<select class="form-select" id="bin_uid" name="bin_uid" required="">
					
					<?php
					foreach ($cellar->sections() AS $section) {
						$output = "<optgroup label=\"" . $section . "\">";
						
						$filter = ['key' => 'category', 'value' => $section];
						foreach ($cellar->bins($filter) AS $binOption) {
							if ($binOption->uid == $wine->bin_uid) {
								//$output .= "<option value=\"" . $binOption->uid . "\" selected>" . $binOption->name . "</option>";
							} else {
								//$output .= "<option value=\"" . $binOption->uid . "\">" . $binOption->name . "</option>";
							}
						}
						
						$output .= "</optgroup>";
						
						
						echo $output;
					}
					?>
				</select>
			</div>
			<div class="col-4 mb-3">
				<label for="status" class="form-label">Status</label>
				<select class="form-select" id="status" name="status" required="">
					<option selected="">In Use</option><option>In-Bond</option><option>Awaiting Delivery</option><option>Closed</option>				</select>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<div class="row">
					<div class="col-4 mb-3">
						<label for="category" class="form-label">Category</label>
						<select class="form-select" id="category" name="category" required="">
							<option>White</option><option>Red</option><option>Sparkling</option><option>Port</option><option>Sherry</option><option>Other</option><option selected="">White Bin-End</option><option>Red Bin-End</option>						</select>
					</div>
					<div class="col-8 mb-3">
						<label for="name" class="form-label">Wine Name</label>
						<input type="text" class="form-control" id="name" name="name" value="Paarl Heights Sauvignon Blanc" required="">
					</div>
				</div>
				<div class="row">
					<div class="col mb-3">
						<label for="name" class="form-label">Supplier</label>
						<input type="text" class="form-control" id="supplier" name="supplier" value="TJ Wines" list="suppliers">
						<datalist id="suppliers">
							<option id="" value=""></option><option id="Anthony Byrne" value="Anthony Byrne"></option><option id="Bancroft Wines" value="Bancroft Wines"></option><option id="Berry Bros" value="Berry Bros"></option><option id="Bibendum" value="Bibendum"></option><option id="Cambridge Wine Merchants" value="Cambridge Wine Merchants"></option><option id="Charles" value="Charles"></option><option id="Charles Taylor Wines" value="Charles Taylor Wines"></option><option id="Clarion Wines" value="Clarion Wines"></option><option id="Clark Foyster Wines" value="Clark Foyster Wines"></option><option id="Corney &amp; Barrow" value="Corney &amp; Barrow"></option><option id="Danebury Vineyards" value="Danebury Vineyards"></option><option id="Decorum Vintners" value="Decorum Vintners"></option><option id="Direct Wine Supplier" value="Direct Wine Supplier"></option><option id="Edward Sheldon" value="Edward Sheldon"></option><option id="Ellis of Richmond Ltd" value="Ellis of Richmond Ltd"></option><option id="First Class Products" value="First Class Products"></option><option id="Flint Wines" value="Flint Wines"></option><option id="Geodhuis" value="Geodhuis"></option><option id="GOEDHUIS" value="GOEDHUIS"></option><option id="Guantleys" value="Guantleys"></option><option id="Haynes Hanson Clark" value="Haynes Hanson Clark"></option><option id="Hayward Bros" value="Hayward Bros"></option><option id="HS Fine Wines" value="HS Fine Wines"></option><option id="Imbibros" value="Imbibros"></option><option id="Imibros" value="Imibros"></option><option id="John Armit Wines" value="John Armit Wines"></option><option id="Justerini &amp; Brooks" value="Justerini &amp; Brooks"></option><option id="Lay &amp; Wheeler" value="Lay &amp; Wheeler"></option><option id="Lee &amp; Sandeman" value="Lee &amp; Sandeman"></option><option id="LOEB" value="LOEB"></option><option id="Majestic" value="Majestic"></option><option id="Manley Wines UK" value="Manley Wines UK"></option><option id="Miscellaneous" value="Miscellaneous"></option><option id="Morris &amp; Verdin" value="Morris &amp; Verdin"></option><option id="Nethergate" value="Nethergate"></option><option id="Nicholsons" value="Nicholsons"></option><option id="Oddbins" value="Oddbins"></option><option id="Oxford Wine Company" value="Oxford Wine Company"></option><option id="Private Cellar" value="Private Cellar"></option><option id="S H Jones" value="S H Jones"></option><option id="Seckford Agencies" value="Seckford Agencies"></option><option id="Sheldon's Wine Cellar" value="Sheldon's Wine Cellar"></option><option id="Stevens Garnier" value="Stevens Garnier"></option><option id="Summerlee" value="Summerlee"></option><option id="T J Wines" value="T J Wines"></option><option id="The Wine Barn Ltd" value="The Wine Barn Ltd"></option><option id="TJ Wines" value="TJ Wines"></option><option id="Veritas &amp; Co" value="Veritas &amp; Co"></option><option id="Vine Partners Limited" value="Vine Partners Limited"></option><option id="Waters Wine Merchant" value="Waters Wine Merchant"></option><option id="Wine Barn" value="Wine Barn"></option><option id="Wine Traders" value="Wine Traders"></option><option id="Yapp Brothers" value="Yapp Brothers"></option>						</datalist>
					</div>
					<div class="col mb-3">
						<label for="name" class="form-label">Supplier Order Reference</label>
						<input type="text" class="form-control" id="supplier_ref" name="supplier_ref" value="">
					</div>
				</div>
				<div class="row">
					<div class="col-4 mb-3">
						<label for="name" class="form-label">Country of Origin</label>
						<input type="text" class="form-control" id="country_of_origin" name="country_of_origin" list="codes-countries" value="South Africa">
						<datalist id="codes-countries">
							<option id="" value=""></option><option id="Argentina" value="Argentina"></option><option id="Australia" value="Australia"></option><option id="Chile" value="Chile"></option><option id="China" value="China"></option><option id="England" value="England"></option><option id="France" value="France"></option><option id="Germany" value="Germany"></option><option id="Greece" value="Greece"></option><option id="Israel" value="Israel"></option><option id="Italy" value="Italy"></option><option id="New Zealand" value="New Zealand"></option><option id="Portugal" value="Portugal"></option><option id="South Africa" value="South Africa"></option><option id="Spain" value="Spain"></option><option id="United Kingdom" value="United Kingdom"></option><option id="USA" value="USA"></option>						</datalist>
					</div>
					<div class="col-4 mb-3">
						<label for="name" class="form-label">Region of Origin</label>
						<input type="text" class="form-control" id="region_of_origin" name="region_of_origin" list="codes-regions" value="">
						<datalist id="codes-regions">
							<option id="" value=""></option><option id="Alsace" value="Alsace"></option><option id="Barossa Valley" value="Barossa Valley"></option><option id="Beaujolais" value="Beaujolais"></option><option id="Bordeaux" value="Bordeaux"></option><option id="Burgundy" value="Burgundy"></option><option id="California" value="California"></option><option id="Casablanca Valley" value="Casablanca Valley"></option><option id="Catalonia" value="Catalonia"></option><option id="Catalunya" value="Catalunya"></option><option id="Central Coast" value="Central Coast"></option><option id="Central Valley" value="Central Valley"></option><option id="Champagne" value="Champagne"></option><option id="Hampshire" value="Hampshire"></option><option id="Jerez" value="Jerez"></option><option id="Languedoc" value="Languedoc"></option><option id="Languedoc Roussillon" value="Languedoc Roussillon"></option><option id="Loire" value="Loire"></option><option id="Madeira" value="Madeira"></option><option id="Mantinia" value="Mantinia"></option><option id="Marlborough" value="Marlborough"></option><option id="Medoc" value="Medoc"></option><option id="Mendoza" value="Mendoza"></option><option id="Nahe" value="Nahe"></option><option id="Pfalz" value="Pfalz"></option><option id="Pomerol" value="Pomerol"></option><option id="Provence" value="Provence"></option><option id="Puglia" value="Puglia"></option><option id="Rhone" value="Rhone"></option><option id="Rioja" value="Rioja"></option><option id="Riverina" value="Riverina"></option><option id="Sancerre" value="Sancerre"></option><option id="Sicilia" value="Sicilia"></option><option id="South West" value="South West"></option><option id="South West France" value="South West France"></option><option id="Southern Australia" value="Southern Australia"></option><option id="Southern France" value="Southern France"></option><option id="Tuscany" value="Tuscany"></option><option id="Veneto" value="Veneto"></option><option id="Venezia" value="Venezia"></option><option id="Verona" value="Verona"></option><option id="Yarra Valley" value="Yarra Valley"></option>						</datalist>
					</div>
					<div class="col-4 mb-3">
						<label for="name" class="form-label">Grape</label>
						<input type="text" class="form-control" id="grape" name="grape" list="codes-grapes" value="">
						<datalist id="codes-grapes">
							<option id="" value=""></option><option id="60% Merlot 40% Cabernet Sauvignon" value="60% Merlot 40% Cabernet Sauvignon"></option><option id="App" value="App"></option><option id="Barbera" value="Barbera"></option><option id="Cabernet Franc" value="Cabernet Franc"></option><option id="Cabernet Sauvignon" value="Cabernet Sauvignon"></option><option id="Chardonnay" value="Chardonnay"></option><option id="Chewing Blanc" value="Chewing Blanc"></option><option id="Cinsault" value="Cinsault"></option><option id="Colombard-Sauvignon" value="Colombard-Sauvignon"></option><option id="Gamay" value="Gamay"></option><option id="Garganega" value="Garganega"></option><option id="Garnacha / Samso" value="Garnacha / Samso"></option><option id="Glera" value="Glera"></option><option id="Grenache" value="Grenache"></option><option id="Grenache, Chardonnay" value="Grenache, Chardonnay"></option><option id="GSM" value="GSM"></option><option id="Malbec" value="Malbec"></option><option id="Med" value="Med"></option><option id="Melon Blanc" value="Melon Blanc"></option><option id="Merlot" value="Merlot"></option><option id="Moschofilero" value="Moschofilero"></option><option id="Nebbiolo" value="Nebbiolo"></option><option id="Palomino" value="Palomino"></option><option id="Pinot Grigio" value="Pinot Grigio"></option><option id="Pinot Gris" value="Pinot Gris"></option><option id="Pinot Noir" value="Pinot Noir"></option><option id="Piquepoul" value="Piquepoul"></option><option id="Primitivo" value="Primitivo"></option><option id="Riesling" value="Riesling"></option><option id="Sangiovese" value="Sangiovese"></option><option id="Sauvignon Blanc" value="Sauvignon Blanc"></option><option id="Sauvignon Blanc &amp; Semillon" value="Sauvignon Blanc &amp; Semillon"></option><option id="Semillon" value="Semillon"></option><option id="Sercial" value="Sercial"></option><option id="Shiraz" value="Shiraz"></option><option id="Syrah" value="Syrah"></option><option id="Tempranillo" value="Tempranillo"></option><option id="Viognier" value="Viognier"></option>						</datalist>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
								<input type="text" class="form-control" id="qty" name="qty" value="4" disabled="" required="" pattern="[0-9]*">
				<label for="qty" class="form-label">Bottles Qty.</label>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<input type="text" class="form-control" id="price_purchase" name="price_purchase" value="4.99" pattern="[0-9]+([\.,][0-9]+)?" required="">
				<label for="price_purchase" class="form-label">Purchase Price <a href="#" data-bs-toggle="tooltip" data-bs-title="All prices are ex VAT"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#info-circle"></use></svg></a></label>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<div class="row">
					<div class="col-sm">
						<input type="text" class="form-control" id="price_internal" name="price_internal" value="6.89" required="" pattern="[0-9]+([\.,][0-9]+)?">
					</div>
					<div class="col-sm">
						<input type="text" class="form-control" id="price_external" name="price_external" value="8.98" required="" pattern="[0-9]+([\.,][0-9]+)?">
					</div>
				</div>
				<label for="price_internal" class="form-label">Internal/External Price <a href="#" data-bs-toggle="tooltip" data-bs-title="All prices are ex VAT"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#info-circle"></use></svg></a></label>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<input type="text" class="form-control" id="vintage" name="vintage" value="2021" pattern="[0-9]*">
				<label for="price_purchvintagease" class="form-label">Vintage</label>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<input type="text" class="form-control" id="code" name="code" list="codes-list" value="MW710" required="">
				<label for="code" class="form-label">Wine Code</label>
				<datalist id="codes-list">
					<option id="0" value="0"></option><option id="AL119" value="AL119"></option><option id="AL121" value="AL121"></option><option id="AL122" value="AL122"></option><option id="AL89" value="AL89"></option><option id="AL90" value="AL90"></option><option id="BE20" value="BE20"></option><option id="BE58" value="BE58"></option><option id="BE80" value="BE80"></option><option id="BE82" value="BE82"></option><option id="BE84" value="BE84"></option><option id="BE85" value="BE85"></option><option id="BE86" value="BE86"></option><option id="BE87" value="BE87"></option><option id="BR232" value="BR232"></option><option id="BR241" value="BR241"></option><option id="BR242" value="BR242"></option><option id="BR268" value="BR268"></option><option id="BR272" value="BR272"></option><option id="BR283" value="BR283"></option><option id="BR286" value="BR286"></option><option id="BR287" value="BR287"></option><option id="BR289" value="BR289"></option><option id="BR290" value="BR290"></option><option id="BR298" value="BR298"></option><option id="BR301" value="BR301"></option><option id="BR323" value="BR323"></option><option id="BR324" value="BR324"></option><option id="BR327" value="BR327"></option><option id="BR331" value="BR331"></option><option id="BR334" value="BR334"></option><option id="BR335" value="BR335"></option><option id="BR343" value="BR343"></option><option id="BR344" value="BR344"></option><option id="BR345" value="BR345"></option><option id="BR346" value="BR346"></option><option id="BR347" value="BR347"></option><option id="BR348" value="BR348"></option><option id="BR351" value="BR351"></option><option id="BR352" value="BR352"></option><option id="BR353" value="BR353"></option><option id="BR354" value="BR354"></option><option id="BR355" value="BR355"></option><option id="BW337" value="BW337"></option><option id="BW401" value="BW401"></option><option id="BW408" value="BW408"></option><option id="BW447" value="BW447"></option><option id="BW452" value="BW452"></option><option id="BW455" value="BW455"></option><option id="BW458" value="BW458"></option><option id="BW463" value="BW463"></option><option id="BW464" value="BW464"></option><option id="BW465" value="BW465"></option><option id="BW468" value="BW468"></option><option id="BW469" value="BW469"></option><option id="BW478" value="BW478"></option><option id="BW482" value="BW482"></option><option id="BW488" value="BW488"></option><option id="BW491" value="BW491"></option><option id="BW496" value="BW496"></option><option id="BW506" value="BW506"></option><option id="BW507" value="BW507"></option><option id="BW510" value="BW510"></option><option id="BW514" value="BW514"></option><option id="BW519" value="BW519"></option><option id="BW520" value="BW520"></option><option id="BW521" value="BW521"></option><option id="BW527" value="BW527"></option><option id="BW529" value="BW529"></option><option id="BW531" value="BW531"></option><option id="BW532" value="BW532"></option><option id="BW534" value="BW534"></option><option id="BW535" value="BW535"></option><option id="BW536" value="BW536"></option><option id="BW537" value="BW537"></option><option id="BW538" value="BW538"></option><option id="BW539" value="BW539"></option><option id="BW541" value="BW541"></option><option id="BW542" value="BW542"></option><option id="BW543" value="BW543"></option><option id="BW544" value="BW544"></option><option id="BW545" value="BW545"></option><option id="BW546" value="BW546"></option><option id="BW547" value="BW547"></option><option id="BW550" value="BW550"></option><option id="BW555" value="BW555"></option><option id="BW556" value="BW556"></option><option id="BW557" value="BW557"></option><option id="BW558" value="BW558"></option><option id="BW559" value="BW559"></option><option id="BW562" value="BW562"></option><option id="BW563" value="BW563"></option><option id="BW564" value="BW564"></option><option id="BW566" value="BW566"></option><option id="BW567" value="BW567"></option><option id="BW568" value="BW568"></option><option id="CH120" value="CH120"></option><option id="CH156" value="CH156"></option><option id="CH168" value="CH168"></option><option id="CH183" value="CH183"></option><option id="CH196" value="CH196"></option><option id="CH205" value="CH205"></option><option id="CH208" value="CH208"></option><option id="CH209" value="CH209"></option><option id="CH211" value="CH211"></option><option id="CH212" value="CH212"></option><option id="CH213" value="CH213"></option><option id="CH215" value="CH215"></option><option id="CH219" value="CH219"></option><option id="CH225" value="CH225"></option><option id="CH227" value="CH227"></option><option id="CH342" value="CH342"></option><option id="CH354" value="CH354"></option><option id="CL109" value="CL109"></option><option id="CL428" value="CL428"></option><option id="CL431" value="CL431"></option><option id="CL434" value="CL434"></option><option id="CL435" value="CL435"></option><option id="CL436" value="CL436"></option><option id="CL437" value="CL437"></option><option id="CL438" value="CL438"></option><option id="CL441" value="CL441"></option><option id="CL450" value="CL450"></option><option id="CL451" value="CL451"></option><option id="CL452" value="CL452"></option><option id="CL453" value="CL453"></option><option id="CL454" value="CL454"></option><option id="CL458" value="CL458"></option><option id="CL465" value="CL465"></option><option id="CL466" value="CL466"></option><option id="CL468" value="CL468"></option><option id="CL469" value="CL469"></option><option id="CL470" value="CL470"></option><option id="CL472" value="CL472"></option><option id="CL473" value="CL473"></option><option id="CL479" value="CL479"></option><option id="CL481" value="CL481"></option><option id="CL482" value="CL482"></option><option id="CL483" value="CL483"></option><option id="CL484" value="CL484"></option><option id="CL485" value="CL485"></option><option id="CL486" value="CL486"></option><option id="CL488" value="CL488"></option><option id="CL490" value="CL490"></option><option id="CL491" value="CL491"></option><option id="CL492" value="CL492"></option><option id="CL493" value="CL493"></option><option id="CL494" value="CL494"></option><option id="CL499" value="CL499"></option><option id="CL500" value="CL500"></option><option id="CL501" value="CL501"></option><option id="CL502" value="CL502"></option><option id="CL503" value="CL503"></option><option id="CL504" value="CL504"></option><option id="CL506" value="CL506"></option><option id="CL507" value="CL507"></option><option id="CL508" value="CL508"></option><option id="CL509" value="CL509"></option><option id="CL510" value="CL510"></option><option id="CL511" value="CL511"></option><option id="CL512" value="CL512"></option><option id="CL513" value="CL513"></option><option id="CL514" value="CL514"></option><option id="CL516" value="CL516"></option><option id="CL517" value="CL517"></option><option id="CL519" value="CL519"></option><option id="CL520" value="CL520"></option><option id="CL521" value="CL521"></option><option id="CL522" value="CL522"></option><option id="CR10" value="CR10"></option><option id="CR11" value="CR11"></option><option id="CR13" value="CR13"></option><option id="CR14" value="CR14"></option><option id="CR2" value="CR2"></option><option id="CR9" value="CR9"></option><option id="CW1" value="CW1"></option><option id="CW10" value="CW10"></option><option id="CW12" value="CW12"></option><option id="CW17" value="CW17"></option><option id="CW5" value="CW5"></option><option id="CW6" value="CW6"></option><option id="CW8" value="CW8"></option><option id="DE155" value="DE155"></option><option id="DE157" value="DE157"></option><option id="GW109" value="GW109"></option><option id="GW117" value="GW117"></option><option id="GW121" value="GW121"></option><option id="GW127" value="GW127"></option><option id="GW128" value="GW128"></option><option id="GW79" value="GW79"></option><option id="GW80" value="GW80"></option><option id="GW96" value="GW96"></option><option id="IR106" value="IR106"></option><option id="IR115" value="IR115"></option><option id="IR117" value="IR117"></option><option id="IR119" value="IR119"></option><option id="IR120" value="IR120"></option><option id="IR121" value="IR121"></option><option id="IR122" value="IR122"></option><option id="IR127" value="IR127"></option><option id="IR129" value="IR129"></option><option id="IR130" value="IR130"></option><option id="IR131" value="IR131"></option><option id="IR132" value="IR132"></option><option id="IR134" value="IR134"></option><option id="IR135" value="IR135"></option><option id="IR138" value="IR138"></option><option id="LW229" value="LW229"></option><option id="LW232" value="LW232"></option><option id="LW235" value="LW235"></option><option id="LW237" value="LW237"></option><option id="LW241" value="LW241"></option><option id="LW242" value="LW242"></option><option id="LW253" value="LW253"></option><option id="LW254" value="LW254"></option><option id="LW255" value="LW255"></option><option id="LW257" value="LW257"></option><option id="LW272" value="LW272"></option><option id="LW275" value="LW275"></option><option id="LW277" value="LW277"></option><option id="LW279" value="LW279"></option><option id="MA22" value="MA22"></option><option id="MA37" value="MA37"></option><option id="MA40" value="MA40"></option><option id="MA42" value="MA42"></option><option id="MA69" value="MA69"></option><option id="MR235" value="MR235"></option><option id="MR267" value="MR267"></option><option id="MR300" value="MR300"></option><option id="MR315" value="MR315"></option><option id="MR334" value="MR334"></option><option id="MR379" value="MR379"></option><option id="MR410" value="MR410"></option><option id="MR413" value="MR413"></option><option id="MR423" value="MR423"></option><option id="MR424" value="MR424"></option><option id="MR426" value="MR426"></option><option id="MR427" value="MR427"></option><option id="MR431" value="MR431"></option><option id="MR433" value="MR433"></option><option id="MR435" value="MR435"></option><option id="MR436" value="MR436"></option><option id="MR437" value="MR437"></option><option id="MR439" value="MR439"></option><option id="MR445" value="MR445"></option><option id="MR449" value="MR449"></option><option id="MR451" value="MR451"></option><option id="MR452" value="MR452"></option><option id="MR454" value="MR454"></option><option id="MR458" value="MR458"></option><option id="MR460" value="MR460"></option><option id="MR462" value="MR462"></option><option id="MR463" value="MR463"></option><option id="MR466" value="MR466"></option><option id="MR467" value="MR467"></option><option id="MR469" value="MR469"></option><option id="MR471" value="MR471"></option><option id="MR472" value="MR472"></option><option id="MR475" value="MR475"></option><option id="MR477" value="MR477"></option><option id="MR479" value="MR479"></option><option id="MR480" value="MR480"></option><option id="MR481" value="MR481"></option><option id="MR563" value="MR563"></option><option id="MW324" value="MW324"></option><option id="MW411" value="MW411"></option><option id="MW451" value="MW451"></option><option id="MW517" value="MW517"></option><option id="MW520" value="MW520"></option><option id="MW533" value="MW533"></option><option id="MW536" value="MW536"></option><option id="MW539" value="MW539"></option><option id="MW544" value="MW544"></option><option id="MW546" value="MW546"></option><option id="MW556" value="MW556"></option><option id="MW558" value="MW558"></option><option id="MW560" value="MW560"></option><option id="MW561" value="MW561"></option><option id="MW565" value="MW565"></option><option id="MW569" value="MW569"></option><option id="MW575" value="MW575"></option><option id="MW577" value="MW577"></option><option id="MW581" value="MW581"></option><option id="MW582" value="MW582"></option><option id="MW585" value="MW585"></option><option id="MW593" value="MW593"></option><option id="MW597" value="MW597"></option><option id="MW598" value="MW598"></option><option id="MW599" value="MW599"></option><option id="MW605" value="MW605"></option><option id="MW612" value="MW612"></option><option id="MW614" value="MW614"></option><option id="MW619" value="MW619"></option><option id="MW620" value="MW620"></option><option id="MW621" value="MW621"></option><option id="MW622" value="MW622"></option><option id="MW627" value="MW627"></option><option id="MW633" value="MW633"></option><option id="MW636" value="MW636"></option><option id="MW637" value="MW637"></option><option id="MW638" value="MW638"></option><option id="MW643" value="MW643"></option><option id="MW644" value="MW644"></option><option id="MW649" value="MW649"></option><option id="MW653" value="MW653"></option><option id="MW654" value="MW654"></option><option id="MW655" value="MW655"></option><option id="MW656" value="MW656"></option><option id="MW662" value="MW662"></option><option id="MW663" value="MW663"></option><option id="MW664" value="MW664"></option><option id="MW665" value="MW665"></option><option id="MW666" value="MW666"></option><option id="MW668" value="MW668"></option><option id="MW669" value="MW669"></option><option id="MW671" value="MW671"></option><option id="MW672" value="MW672"></option><option id="MW674" value="MW674"></option><option id="MW678" value="MW678"></option><option id="MW680" value="MW680"></option><option id="MW681" value="MW681"></option><option id="MW682" value="MW682"></option><option id="MW684" value="MW684"></option><option id="MW686" value="MW686"></option><option id="MW687" value="MW687"></option><option id="MW690" value="MW690"></option><option id="MW693" value="MW693"></option><option id="MW694" value="MW694"></option><option id="MW707" value="MW707"></option><option id="MW710" value="MW710"></option><option id="MW714" value="MW714"></option><option id="MW716" value="MW716"></option><option id="MW717" value="MW717"></option><option id="MW718" value="MW718"></option><option id="MW719" value="MW719"></option><option id="MW721" value="MW721"></option><option id="MW723" value="MW723"></option><option id="PO103" value="PO103"></option><option id="PO107" value="PO107"></option><option id="PO111" value="PO111"></option><option id="PO114" value="PO114"></option><option id="PO115" value="PO115"></option><option id="PO116" value="PO116"></option><option id="PO69" value="PO69"></option><option id="PO72" value="PO72"></option><option id="PO73" value="PO73"></option><option id="PO74" value="PO74"></option><option id="PO75" value="PO75"></option><option id="PO76" value="PO76"></option><option id="PO81" value="PO81"></option><option id="PO82" value="PO82"></option><option id="PO83" value="PO83"></option><option id="PO86" value="PO86"></option><option id="PO93" value="PO93"></option><option id="PO96" value="PO96"></option><option id="PO97" value="PO97"></option><option id="RO354" value="RO354"></option><option id="RR127" value="RR127"></option><option id="RR131" value="RR131"></option><option id="RR176" value="RR176"></option><option id="RR198" value="RR198"></option><option id="RR251" value="RR251"></option><option id="RR259" value="RR259"></option><option id="RR261" value="RR261"></option><option id="RR292" value="RR292"></option><option id="RR297" value="RR297"></option><option id="RR309" value="RR309"></option><option id="RR315" value="RR315"></option><option id="RR324" value="RR324"></option><option id="RR325" value="RR325"></option><option id="RR326" value="RR326"></option><option id="RR3321" value="RR3321"></option><option id="RR333" value="RR333"></option><option id="RR335" value="RR335"></option><option id="RR341" value="RR341"></option><option id="RR345" value="RR345"></option><option id="RR347" value="RR347"></option><option id="RR348" value="RR348"></option><option id="RR350" value="RR350"></option><option id="RR353" value="RR353"></option><option id="RR354" value="RR354"></option><option id="RR356" value="RR356"></option><option id="RR357" value="RR357"></option><option id="RR362" value="RR362"></option><option id="RR365" value="RR365"></option><option id="RR366" value="RR366"></option><option id="RR372" value="RR372"></option><option id="RR375" value="RR375"></option><option id="RR376" value="RR376"></option><option id="RR379" value="RR379"></option><option id="RR380" value="RR380"></option><option id="RR381" value="RR381"></option><option id="RR382" value="RR382"></option><option id="RR383" value="RR383"></option><option id="RR384" value="RR384"></option><option id="RR385" value="RR385"></option><option id="RR388" value="RR388"></option><option id="RR389" value="RR389"></option><option id="RR390" value="RR390"></option><option id="RR391" value="RR391"></option><option id="RR392" value="RR392"></option><option id="RR393" value="RR393"></option><option id="RR395" value="RR395"></option><option id="RR397" value="RR397"></option><option id="RR398" value="RR398"></option><option id="RR399" value="RR399"></option><option id="RR401" value="RR401"></option><option id="RR402" value="RR402"></option><option id="RR403" value="RR403"></option><option id="RR404" value="RR404"></option><option id="RR405" value="RR405"></option><option id="RR406" value="RR406"></option><option id="RR407" value="RR407"></option><option id="RR408" value="RR408"></option><option id="RR409" value="RR409"></option><option id="RR410" value="RR410"></option><option id="RR412" value="RR412"></option><option id="RR413" value="RR413"></option><option id="RR414" value="RR414"></option><option id="RR415" value="RR415"></option><option id="RR418" value="RR418"></option><option id="RR419" value="RR419"></option><option id="RR420" value="RR420"></option><option id="RR421" value="RR421"></option><option id="RR422" value="RR422"></option><option id="RR423" value="RR423"></option><option id="RR424" value="RR424"></option><option id="RR425" value="RR425"></option><option id="RR426" value="RR426"></option><option id="RR427" value="RR427"></option><option id="RR428" value="RR428"></option><option id="RR429" value="RR429"></option><option id="RR430" value="RR430"></option><option id="RR431" value="RR431"></option><option id="RR432" value="RR432"></option><option id="RR433" value="RR433"></option><option id="RR434" value="RR434"></option><option id="RR435" value="RR435"></option><option id="RR436" value="RR436"></option><option id="RR439" value="RR439"></option><option id="RR441" value="RR441"></option><option id="RR442" value="RR442"></option><option id="RR443" value="RR443"></option><option id="RR444" value="RR444"></option><option id="RR446" value="RR446"></option><option id="RR447" value="RR447"></option><option id="RR448" value="RR448"></option><option id="RR449" value="RR449"></option><option id="SH002" value="SH002"></option><option id="SH100" value="SH100"></option><option id="SH63" value="SH63"></option><option id="SR73" value="SR73"></option><option id="SR79" value="SR79"></option><option id="SR81" value="SR81"></option><option id="SR82" value="SR82"></option>				</datalist>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xl-8">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title">Tasting Notes</h5>
				<textarea class="form-control" id="tasting" name="tasting" rows="3"></textarea>
			</div>
		</div>
		
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title">Private Notes</h5>
				<textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
			</div>
		</div>
	</div>
	<div class="col-xl-4">
		<div class="card mb-3">
			<img src="img/wines/wine_543.jpg" class="card-img-top" alt="...">
			<div class="card-body">
				<input class="form-control" type="file" id="photograph" name="photograph">
			</div>
		</div>
	</div>
	
	<button type="button" class="btn btn-lg btn-primary" data-wineuid="543" onclick="submitWine(this)">Save</button>
	
	<input type="hidden" id="uid" name="uid" value="543"></div>
</form>

