<head>
<title>Alysii's PI Scheme</title>
<style type="text/css" media="screen">
	body { background-color: #000; }
	body,td { font: .8em Gotham, Helvetica Neue, Helvetica, Arial Narrow, Arial; font-weight: 500; color: #666; padding: 0; margin: 0; }
	ul { list-style: none none; margin: 0; padding: 0; display: block; }
	
	a,a:link { color: #ffcc00; text-decoration: none; }
	
	td { padding: 4px 4px; text-align: center; border: 1px solid #111; border-width: 1px 0; }
	tr:last-child td { border-bottom-width: 0; }
	tr.header td { padding: 10px 4px; font-size: 1.5em; font-weight: 200; color: #aaa; }
	tr.header td i { font-size: .4em; font-weight: 200; font-style: normal; }

	.items li { margin: 5px 0px; }
	.items li.current span { text-shadow: 0px 0px 8px #79cef4; }
	.arrows li { margin: 5px 0px; color: #333; }
	.items li span { white-space: nowrap; cursor: pointer; background-color: rgba(0,0,0,0.75); border-radius: 3px; padding: 1px 4px; }
	
	tr.footer { display: none; }
/*
	tr.footer td { color: #111; }
	.p1 td,.p2 td,.p3 td,.p4 td,
	.p1,.p2,.p3,.p4 { font-weight: 900 !important; }
	.p1.current,.p2.current,.p3.current,.p4.current,
	.p1.current td,.p2.current td,.p3.current td,.p4.current td { color: red; }
*/
	#canvas { z-index: 1; display: block; position: absolute; left: 0; top: 0; background-color: transparent; }
	#pi { z-index: 2; border-collapse: collapse; position: relative; left: 0; top: 0; }

</style>
</head>
<body>
<table id="pi" border="0" cellpadding="0" cellspacing="0" width="100%" class="">
<tbody><tr class="header">
<td valign="top">Planets</td>
<td valign="top" colspan="3" style="text-align: right;">Resources</td>
<td valign="top"></td>
<td valign="top" colspan="3" style="text-align: left;">Basic<br> <i>P1 - 1800s</i></td>
<td valign="top"></td>
<td valign="top" colspan="3">Refined<br> <i>P2 - 3600s</i></td>
<td valign="top"></td>
<td valign="top" colspan="3">Specialized<br> <i>P3 - 3600s</i></td>
<td valign="top"></td>
<td valign="top" colspan="3">Advanced<br> <i>P4 - 3600s</i></td>
</tr>
<tr>
<td class="items">
<ul id="planets">
<li id="barren" style="color: rgb(51, 51, 51);"><span class="aqueous-liquids base-metals carbon-compounds micro-organisms noble-metals">Barren</span></li>
<li id="gas" style="color: rgb(51, 51, 51);"><span class="aqueous-liquids base-metals ionic-solutions noble-gas reactive-gas">Gas</span></li>
<li id="ice" style="color: rgb(51, 51, 51);"><span class="aqueous-liquids heavy-metals micro-organisms noble-gas planktic-colonies">Ice</span></li>
<li id="lava" style="color: rgb(51, 51, 51);"><span class="base-metals felsic-magma heavy-metals non-cs-crystals suspended-plasma">Lava</span></li>
<li id="oceanic" style="color: rgb(51, 51, 51);"><span class="aqueous-liquids carbon-compounds complex-organisms micro-organisms planktic-colonies">Oceanic</span></li>
<li id="plasma" style="color: rgb(51, 51, 51);"><span class="base-metals heavy-metals noble-metals non-cs-crystals suspended-plasma">Plasma</span></li>
<li id="storm" style="color: rgb(51, 51, 51);"><span class="aqueous-liquids base-metals ionic-solutions noble-gas suspended-plasma">Storm</span></li>
<li id="temperate" style="color: rgb(51, 51, 51);"><span class="aqueous-liquids autotrophs carbon-compounds complex-organisms micro-organisms">Temperate</span></li>
</ul>
</td>
<td colspan="3" class="items" style="text-align: right;">
<ul id="resources">
<li class="barren gas ice oceanic storm temperate" id="aqueous-liquids" style="color: rgb(51, 51, 51);"><span class="water">Aqueous Liquids</span></li>
<li class="temperate" id="autotrophs" style="color: rgb(51, 51, 51);"><span class="industrial-fibers">Autotrophs</span></li>
<li class="barren gas lava plasma storm" id="base-metals" style="color: rgb(51, 51, 51);"><span class="reactive-metals">Base Metals</span></li>
<li class="barren oceanic temperate" id="carbon-compounds" style="color: rgb(51, 51, 51);"><span class="biofuels">Carbon Compounds</span></li>
<li class="oceanic temperate" id="complex-organisms" style="color: rgb(51, 51, 51);"><span class="proteins">Complex Organisms</span></li>
<li class="lava" id="felsic-magma" style="color: rgb(51, 51, 51);"><span class="silicon">Felsic Magma</span></li>
<li class="ice lava plasma" id="heavy-metals" style="color: rgb(51, 51, 51);"><span class="toxic-metals">Heavy Metals</span></li>
<li class="gas storm" id="ionic-solutions" style="color: rgb(51, 51, 51);"><span class="electrolytes">Ionic Solutions</span></li>
<li class="barren ice oceanic temperate" id="micro-organisms" style="color: rgb(51, 51, 51);"><span class="bacteria">Micro Organisms</span></li>
<li class="gas ice storm" id="noble-gas" style="color: rgb(51, 51, 51);"><span class="oxygen">Noble Gas</span></li>
<li class="barren plasma" id="noble-metals" style="color: rgb(51, 51, 51);"><span class="precious-metals">Noble Metals</span></li>
<li class="lava plasma" id="non-cs-crystals" style="color: rgb(51, 51, 51);"><span class="chiral-structures">Non-CS Crystals</span></li>
<li class="ice oceanic" id="planktic-colonies" style="color: rgb(51, 51, 51);"><span class="biomass">Planktic Colonies</span></li>
<li class="gas" id="reactive-gas" style="color: rgb(51, 51, 51);"><span class="oxidizing-compound">Reactive Gas</span></li>
<li class="lava plasma storm" id="suspended-plasma" style="color: rgb(51, 51, 51);"><span class="plasmoids">Suspended Plasma</span></li>
</ul>
</td>
<td>
<ul class="arrows">
<li>→</li>
<li>→</li>
<li>→</li>
<li>→</li>
<li>→</li>
<li>→</li>
<li>→</li>
<li>→</li>
<li>→</li>
<li>→</li>
<li>→</li>
<li>→</li>
<li>→</li>
<li>→</li>
<li>→</li>
</ul>
</td>
<td colspan="3" class="items" style="text-align: left;">
<ul id="basic">
<li class="aqueous-liquids" id="water" style="color: rgb(51, 51, 51);"><span class="coolant superconductors test-cultures water-cooled-cpu sterile-conduits">Water</span></li>
<li class="autotrophs" id="industrial-fibers" style="color: rgb(51, 51, 51);"><span class="microfiber-shielding polyaramids polytextiles">Industrial Fibers</span></li>
<li class="base-metals" id="reactive-metals" style="color: rgb(51, 51, 51);"><span class="construction-blocks mechanical-parts nanites water-cooled-cpu nano-factory">Reactive Metals</span></li>
<li class="carbon-compounds" id="biofuels" style="color: rgb(51, 51, 51);"><span class="biocells livestock polytextiles">Biofuels</span></li>
<li class="complex-organisms" id="proteins" style="color: rgb(51, 51, 51);"><span class="fertilizer gen-enhanced-livestock livestock">Proteins</span></li>
<li class="felsic-magma" id="silicon" style="color: rgb(51, 51, 51);"><span class="microfiber-shielding miniature-electronics silicate-glass">Silicon</span></li>
<li class="heavy-metals" id="toxic-metals" style="color: rgb(51, 51, 51);"><span class="construction-blocks consumer-electronics enriched-uranium">Toxic Metals</span></li>
<li class="ionic-solutions" id="electrolytes" style="color: rgb(51, 51, 51);"><span class="coolant rocket-fuel synthetic-oil">Electrolytes</span></li>
<li class="micro-organisms" id="bacteria" style="color: rgb(51, 51, 51);"><span class="fertilizer nanites test-cultures viral-agent organic-mortar-applicators">Bacteria</span></li>
<li class="noble-gas" id="oxygen" style="color: rgb(51, 51, 51);"><span class="oxides supertensile-plastics synthetic-oil">Oxygen</span></li>
 <li class="noble-metals" id="precious-metals" style="color: rgb(51, 51, 51);"><span class="biocells enriched-uranium mechanical-parts">Precious Metals</span></li>
<li class="non-cs-crystals" id="chiral-structures" style="color: rgb(51, 51, 51);"><span class="consumer-electronics miniature-electronics transmitter">Chiral Structures</span></li>
<li class="planktic-colonies" id="biomass" style="color: rgb(51, 51, 51);"><span class="gen-enhanced-livestock supertensile-plastics viral-agent">Biomass</span></li>
<li class="reactive-gas" id="oxidizing-compound" style="color: rgb(51, 51, 51);"><span class="oxides polyaramids silicate-glass">Oxidizing Compound</span></li>
<li class="suspended-plasma" id="plasmoids" style="color: rgb(51, 51, 51);"><span class="rocket-fuel superconductors transmitter">Plasmoids</span></li>
</ul>
</td>
<td class="items"></td>
<td colspan="3" class="items">
<ul id="refined">
<li class="biofuels precious-metals" id="biocells" style="color: rgb(51, 51, 51);"><span class="gel-matrix-biopaste neocoms transcranial-microcontrollers">Biocells</span></li>
<li class="reactive-metals toxic-metals" id="construction-blocks" style="color: rgb(51, 51, 51);"><span class="biotech-research-reports smartfab-units">Construction Blocks</span></li>
<li class="toxic-metals chiral-structures pos" id="consumer-electronics" style="color: rgb(51, 51, 51);"><span class="robotics supercomputers">Consumer Electronics</span></li>
<li class="electrolytes water pos" id="coolant" style="color: rgb(51, 51, 51);"><span class="condensates supercomputers">Coolant</span></li>
<li class="precious-metals toxic-metals pos" id="enriched-uranium" style="color: rgb(51, 51, 51);"><span class="nuclear-reactors">Enriched Uranium</span></li>
<li class="bacteria proteins" id="fertilizer" style="color: rgb(51, 51, 51);"><span class="cryoprotectant-solution industrial-explosives">Fertilizer</span></li>
<li class="proteins biomass" id="gen-enhanced-livestock" style="color: rgb(51, 51, 51);"><span class="hermetic-membranes">Gen. Enhanced Livestock</span></li>
<li class="proteins biofuels" id="livestock" style="color: rgb(51, 51, 51);"><span class="biotech-research-reports vaccines">Livestock</span></li>
<li class="reactive-metals precious-metals pos" id="mechanical-parts" style="color: rgb(51, 51, 51);"><span class="planetary-vehicles robotics">Mechanical Parts</span></li>
<li class="industrial-fibers silicon" id="microfiber-shielding" style="color: rgb(51, 51, 51);"><span class="data-chips nuclear-reactors">Microfiber Shielding</span></li>
<li class="chiral-structures silicon" id="miniature-electronics" style="color: rgb(51, 51, 51);"><span class="planetary-vehicles smartfab-units">Miniature Electronics</span></li>
<li class="bacteria reactive-metals" id="nanites" style="color: rgb(51, 51, 51);"><span class="biotech-research-reports transcranial-microcontrollers">Nanites</span></li>
<li class="oxidizing-compound oxygen" id="oxides" style="color: rgb(51, 51, 51);"><span class="condensates gel-matrix-biopaste">Oxides</span></li>
<li class="oxidizing-compound industrial-fibers" id="polyaramids" style="color: rgb(51, 51, 51);"><span class="hermetic-membranes high-tech-transmitters">Polyaramids</span></li>
<li class="biofuels industrial-fibers" id="polytextiles" style="color: rgb(51, 51, 51);"><span class="hazmat-detection-systems industrial-explosives">Polytextiles</span></li>
<li class="plasmoids electrolytes" id="rocket-fuel" style="color: rgb(51, 51, 51);"><span class="camera-drones">Rocket Fuel</span></li>
<li class="oxidizing-compound silicon" id="silicate-glass" style="color: rgb(51, 51, 51);"><span class="camera-drones neocoms">Silicate Glass</span></li>
<li class="plasmoids water" id="superconductors" style="color: rgb(51, 51, 51);"><span class="gel-matrix-biopaste ukomi-super-conductors">Superconductors</span></li>
<li class="oxygen biomass" id="supertensile-plastics" style="color: rgb(51, 51, 51);"><span class="data-chips planetary-vehicles synthetic-synapses">Supertensile Plastics</span></li>
<li class="electrolytes oxygen" id="synthetic-oil" style="color: rgb(51, 51, 51);"><span class="cryoprotectant-solution ukomi-super-conductors">Synthetic Oil</span></li>
<li class="bacteria water" id="test-cultures" style="color: rgb(51, 51, 51);"><span class="cryoprotectant-solution synthetic-synapses">Test Cultures</span></li>
<li class="plasmoids chiral-structures" id="transmitter" style="color: rgb(51, 51, 51);"><span class="guidance-systems hazmat-detection-systems high-tech-transmitters">Transmitter</span></li>
<li class="bacteria biomass" id="viral-agent" style="color: rgb(51, 51, 51);"><span class="hazmat-detection-systems vaccines">Viral Agent</span></li>
<li class="reactive-metals water" id="water-cooled-cpu" style="color: rgb(51, 51, 51);"><span class="guidance-systems supercomputers">Water-Cooled CPU</span></li>
</ul>
</td>
<td class="items"></td>
<td colspan="3" class="items">
<ul id="specialized">
<li class="nanites livestock construction-blocks" id="biotech-research-reports" style="color: rgb(51, 51, 51);"><span class="wetware-mainframe">Biotech Research Reports</span></li>
<li class="silicate-glass rocket-fuel" id="camera-drones" style="color: rgb(51, 51, 51);"><span class="self-harmonizing-power-core">Camera Drones</span></li>
<li class="oxides coolant" id="condensates" style="color: rgb(51, 51, 51);"><span class="organic-mortar-applicators">Condensates</span></li>
<li class="test-cultures synthetic-oil fertilizer" id="cryoprotectant-solution" style="color: rgb(51, 51, 51);"><span class="wetware-mainframe">Cryoprotectant Solution</span></li>
<li class="supertensile-plastics microfiber-shielding" id="data-chips" style="color: rgb(51, 51, 51);"><span class="broadcast-node">Data Chips</span></li>
<li class="biocells oxides superconductors" id="gel-matrix-biopaste" style="color: rgb(51, 51, 51);"><span class="integrity-response-drones">Gel-Matrix Biopaste</span></li>
<li class="water-cooled-cpu transmitter" id="guidance-systems" style="color: rgb(51, 51, 51);"><span class="recursive-computing-module">Guidance Systems</span></li>
<li class="polytextiles viral-agent transmitter" id="hazmat-detection-systems" style="color: rgb(51, 51, 51);"><span class="integrity-response-drones">Hazmat Detection Systems</span></li>
<li class="polyaramids gen-enhanced-livestock" id="hermetic-membranes" style="color: rgb(51, 51, 51);"><span class="self-harmonizing-power-core">Hermetic Membranes</span></li>
<li class="polyaramids transmitter" id="high-tech-transmitters" style="color: rgb(51, 51, 51);"><span class="broadcast-node">High-Tech Transmitters</span></li>
<li class="fertilizer polytextiles" id="industrial-explosives" style="color: rgb(51, 51, 51);"><span class="nano-factory">Industrial Explosives</span></li>
<li class="biocells silicate-glass" id="neocoms" style="color: rgb(51, 51, 51);"><span class="broadcast-node">Neocoms</span></li>
<li class="microfiber-shielding enriched-uranium" id="nuclear-reactors" style="color: rgb(51, 51, 51);"><span class="self-harmonizing-power-core">Nuclear Reactors</span></li>
<li class="supertensile-plastics mechanical-parts miniature-electronics" id="planetary-vehicles" style="color: rgb(51, 51, 51);"><span class="integrity-response-drones">Planetary Vehicles</span></li>
<li class="mechanical-parts consumer-electronics pos" id="robotics" style="color: rgb(51, 51, 51);"><span class="organic-mortar-applicators">Robotics</span></li>
<li class="construction-blocks miniature-electronics" id="smartfab-units" style="color: rgb(51, 51, 51);"><span class="sterile-conduits">Smartfab Units</span></li>
<li class="water-cooled-cpu coolant consumer-electronics" id="supercomputers" style="color: rgb(51, 51, 51);"><span class="wetware-mainframe">Supercomputers</span></li>
<li class="supertensile-plastics test-cultures" id="synthetic-synapses" style="color: rgb(51, 51, 51);"><span class="recursive-computing-module">Synthetic Synapses</span></li>
<li class="biocells nanites" id="transcranial-microcontrollers" style="color: rgb(51, 51, 51);"><span class="recursive-computing-module">Transcranial Microcontrollers</span></li>
<li class="synthetic-oil superconductors" id="ukomi-super-conductors" style="color: rgb(51, 51, 51);"><span class="nano-factory">Ukomi Super Conductors</span></li>
<li class="livestock viral-agent" id="vaccines" style="color: rgb(51, 51, 51);"><span class="sterile-conduits">Vaccines</span></li>
</ul>
</td>
<td class="items" style=""></td>
<td colspan="3" class="items" style="">
<ul id="advanced">
<li class="neocoms data-chips high-tech-transmitters" id="broadcast-node" style="color: rgb(51, 51, 51);"><span>Broadcast Node</span></li>
<li class="gel-matrix-biopaste hazmat-detection-systems planetary-vehicles" id="integrity-response-drones" style="color: rgb(51, 51, 51);"><span>Integrity Response Drones</span></li>
<li class="industrial-explosives ukomi-super-conductors reactive-metals" id="nano-factory" style="color: rgb(51, 51, 51);"><span>Nano-Factory</span></li>
<li class="condensates robotics bacteria" id="organic-mortar-applicators" style="color: rgb(51, 51, 51);"><span>Organic Mortar Applicators</span></li>
<li class="synthetic-synapses guidance-systems transcranial-microcontrollers" id="recursive-computing-module" style="color: rgb(51, 51, 51);"><span>Recursive Computing Module</span></li>
<li class="camera-drones nuclear-reactors hermetic-membranes" id="self-harmonizing-power-core" style="color: rgb(51, 51, 51);"><span>Self-Harmonizing Power Core</span></li>
<li class="smartfab-units vaccines water" id="sterile-conduits" style="color: rgb(51, 51, 51);"><span>Sterile Conduits</span></li>
<li class="supercomputers biotech-research-reports cryoprotectant-solution" id="wetware-mainframe" style="color: rgb(51, 51, 51);"><span>Wetware Mainframe</span></li>
</ul>
</td>
</tr>
<tr class="p1 footer">
<td></td>
<td></td>
<td>3000<i>u</i></td>
<td></td>
<td>→</td>
<td></td>
<td>20<i>u</i></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr class="p2 footer">
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td>40<i>u</i></td>
<td></td>
<td>→</td>
<td></td>
<td>5<i>u</i></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr class="p3 footer">
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td>10<i>u</i></td>
<td></td>
<td>→</td>
<td></td>
<td>3<i>u</i></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr class="p4 footer">
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td>40<i>u</i></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td>6<i>u</i></td>
<td></td>
<td>→</td>
<td></td>
<td>1<i>u</i></td>
<td></td>
</tr>
<tr>
<td style="width: 8%;">&nbsp;</td>
<td style="width: 12%;">&nbsp;</td>
<td style="width: 4%;">&nbsp;</td>
<td style="width: 1%;">&nbsp;</td>
<td style="width: 1%;">&nbsp;</td>
<td style="width: 1%;">&nbsp;</td>
<td style="width: 4%;">&nbsp;</td>
<td style="width: 7%;">&nbsp;</td>
<td style="width: 1%;">&nbsp;</td>
<td style="width: 7%;">&nbsp;</td>
<td style="width: 7%;">&nbsp;</td>
<td style="width: 7%;">&nbsp;</td>
<td style="width: 1%;">&nbsp;</td>
<td style="width: 7%;">&nbsp;</td>
<td style="width: 7%;">&nbsp;</td>
<td style="width: 7%;">&nbsp;</td>
<td style="width: 1%;">&nbsp;</td>
<td style="width: 7%;">&nbsp;</td>
<td style="width: 4%;">&nbsp;</td>
<td style="width: 7%;">&nbsp;</td>
</tr>
</tbody></table>
<canvas id="canvas" height="578" width="1579"></canvas>
<div style="position: fixed; bottom: 0; text-align: center; width: 100%;">
<h2 style="margin: 0px;">News</h2>
- Added sticky on click / click sticky again to unsticky -<br>
- I've plans to move this to github and add calculations, hold off on feature request -<br><br>
© 2004 alysii.com © 2004-2018 hanns.io All Rights Reserved.<br>
Material related to EVE-Online is used with limited permission of CCP Games hf.<br>
No official affiliation or endorsement by CCP Games hf is stated or implied.<br><br>
<small style="font-size: 0.6em">EVE Online and the EVE logo are the registered trademarks of CCP hf. All rights are reserved worldwide. All other trademarks are the property of their respective owners. EVE Online, the EVE logo, EVE and all associated logos and designs are the intellectual property of CCP hf. All artwork, screenshots, characters, vehicles, storylines, world facts or other recognizable features of the intellectual property relating to these trademarks are likewise the intellectual property of CCP hf. CCP hf. has granted permission to [insert your name or site name] to use EVE Online and all associated logos and designs for promotional and information purposes on its website but does not endorse, and is not in any way affiliated with, [insert name or site name]. CCP is in no way responsible for the content on or functioning of this website, nor can it be liable for any damage arising from the use of this website.</small>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script>
$(document).ready(function() {
	
	var colors = ["#333","#492f1f","#763c19","#f7941d","yellow","#79cef4","#00aeef","lime","green","#304b15"];
	$(".items li").css({color:colors[0]});
	
	canvas();
	$(window).resize(function() { // todo: get event, only repaint on mouse release
		canvas();
	});

	// distribute before list to span
	$(".items ul li")
		.wrapInner($('<span />'))
		.each(function() {

			item = lower_case($(this).children("span").html());		// turn item name css compliant
			$(this).attr({ id: item });								// set li id as item name
			
			if ($(this).parents("ul").attr("id") != "planets") {		// planets has no before
				list = $(this).attr("class").split(/\s+/);			// get before items from li class
				for (i in list) {
					$("#"+list[i]+" span").addClass(item);			// find id with class, inject itself into span as class
				}
			}
		})
	$('.items')
		.on('mouseenter','ul li',function() {
			if (! $('#pi').hasClass('sticky')) {
				$(this).addClass("current");
				$(this).css({color:colors[5]});
				pi_link($(this));
			}
		})
		.on('mouseleave','ul li',function() {
			if (! $('#pi').hasClass('sticky')) {
				$(this).removeClass("current");
				$(".items li").css({color:colors[0]});
				canvas();
			}
		})
		.on('click','ul li',function() {
			
			if (!$(this).hasClass('current')) {
				$('#pi').removeClass('sticky')
			}
			
			if ($('#pi').hasClass('sticky')) {
				$('#pi').removeClass('sticky')
				
				$('.items li').removeClass('current').css({color:colors[0]});
			} else {
				$('#pi').addClass('sticky')
				
				$('.items li').removeClass('current').css({color:colors[0]});
				canvas();

				$(this).addClass("current");
				$(this).css({color:colors[5]});
				pi_link($(this));
			}
		})
		
		// $("ul#basic").hover(function() { $(".p1").toggleClass("current"); });
		// $("ul#refined").hover(function() { $(".p2").toggleClass("current"); });
		// $("ul#specialized").hover(function() { $(".p3").toggleClass("current"); });
		// $("ul#advanced").hover(function() { $(".p4").toggleClass("current"); });
	
	function lower_case(s) { return s.toLowerCase().replace(/ /g, '-').replace(/\./g, ''); }
	function pi_link(item) {
		pi_link_before(item,-1);
		pi_link_after(item,1);
	}
	function pi_link_before(item, degree, depth) {
		type   = item.parent("ul").attr("id");
		degree = degree + ((type == "basic") ? 1 : 0);

		class_prefex = ".items li span.";
		$(".items li span."+item.attr("id"))
			.each(function() {
				$(this).parent("li").css({color: colors[5+degree]});
				if (type != "basic" && degree > -4) {
					line(item.children("span"),$(this),colors[5+degree],colors[5+degree+1]);
				}
				pi_link_before($(this).parent("li"),degree-1);
			});
	}
	function pi_link_after(item, degree, depth) {
		type   = item.parent("ul").attr("id");
		degree = degree + ((type == "resources") ? -1 : 0);
		
		$(".items li."+item.attr("id"))
			.each(function() {
				$(this).css({color: colors[5+degree]});
				if (type != "resources" && degree < 3) {
					line($(this).children("span"),item.children("span"),colors[5+degree-1],colors[5+degree]);
				}
				pi_link_after($(this),degree+1);
			});
	}
	
	$("#disco").toggle(
		function() {
			$(this).text("Disco!");
		}
	,
		function() {
			$(this).text("Planets");
		}
	);
});
function canvas() {
	$("#canvas").attr({ 
		"height": $("#pi").outerHeight(),
		"width": $("#pi").outerWidth() 
	});
}
function line(a,b,c1,c2) {
	pad = 0;
	fx  = a.position().left + 4;
	fy  = a.position().top + a.height()/2 + 1;
	tx  = b.position().left + b.width() + 2;
	ty  = b.position().top + b.height()/2 + 1;

	var cvs = document.getElementById('canvas');

	var ctx = cvs.getContext('2d');
	ctx.lineWidth = 1;
	ctx.beginPath();
	ctx.moveTo(tx,ty);
	ctx.lineTo(fx,fy);
	ctx.globalAlpha = 0.5;

	var gdt = ctx.createLinearGradient( tx,ty, fx,fy );
	gdt.addColorStop(0, c1);
	gdt.addColorStop(1, c2);

	ctx.strokeStyle = gdt;
	ctx.stroke();
}


pi = {
	planets: ['Barren','Gas','Ice','Lava','Oceanic','Plasma','Storm','Temperate'],
	resources: ['Aqueous Liquids','Autotrophs','Base Metals','Carbon Compounds','Complex Organisms','Felsic Magma','Heavy Metals','Ionic Solutions','Micro Organisms','Noble Gas','Noble Metals','Non-CS Crystals','Planktic Colonies','Reactive Gas','Suspended Plasma'],
	productions: {
		basic: ['Water','Industrial Fibers','Reactive Metals','Biofuels','Proteins','Silicon','Toxic Metals','Electrolytes','Bacteria','Oxygen','Precious Metals','Chiral Structures','Biomass','Oxidizing Compound','Plasmoids'],
		refined: ['Biocells','Construction Blocks','Consumer Electronics','Coolant','Enriched Uranium','Fertilizer','Gen. Enhanced Livestock','Livestock','Mechanical Parts','Microfiber Shielding','Miniature Electronics','Nanites','Oxides','Polyaramids','Polytextiles','Rocket Fuel','Silicate Glass','Superconductors','Supertensile Plastics','Synthetic Oil','Test Cultures','Transmitter','Viral Agent','Water-Cooled CPU'],
		specialized: ['Biotech Research Reports','Camera Drones','Condensates','Cryoprotectant Solution','Data Chips','Gel-Matrix Biopaste','Guidance Systems','Hazmat Detection Systems','Hermetic Membranes','High-Tech Transmitters','Industrial Explosives','Neocoms','Nuclear Reactors','Planetary Vehicles','Robotics','Smartfab Units','Supercomputers','Synthetic Synapses','Transcranial Microcontrollers','Ukomi Super Conductors','Vaccines'],
		advanced: ['Broadcast Node','Integrity Response Drones','Nano-Factory','Organic Mortar Applicators','Recursive Computing Module','Self-Harmonizing Power Core','Sterile Conduits','Wetware Mainframe']
	} 
}
</script>
</body>