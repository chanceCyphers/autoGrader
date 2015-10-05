<!---Page2 
	Created by Anjana Chiluka (07-18-2014)--->
	<style>
	#leftNav
	{left: 0;
    position: absolute;
    top: 120px;
    width: 150px;}
	#content{margin-left: 170px; margin-top: 20px; background-color:white overflow:scroll}

	</style>
	<head>
	<script type="text/javascript" src="mktree.js"></script>
	<script type="text/javascript">
	function setupPage(){
		  var myvar = "<?php echo $_GET['id'];?>";
		  var myvar2 = ""
		  if(myvar == myvar2)
		  {
		  
		  	  	collapseTree('tree1');
		  }
		  else
		  {
		 	  expandToItem('tree1',myvar);
		   
		  }
		 myvar = "";
	}
	</script>
	<link rel="stylesheet" href="mktree.css" type="text/css">
	</head>
<body style="background-color: #F8F8F8 ;" onload="setupPage()">
<div id = "header" style="border-bottom:12px solid #009966;">
	<?php include("header.php");?>
	</div>

<div id="leftNav" style="float:left;">

<?php include("nav.php");?>

</div>

<div id="content" style="float:left;" >

<?php 
$selId = $_GET["id"];
echo "$selId";

switch($selId)
{
	case "os": echo"Operating System";
				break;
				
	case "nw":echo "Netwroking";
				break;
	
	case "dbs": echo "DBS";
				break;
	default:
			
	echo"Lorem ipsum dolor sit amet, nec id dolor tantas facilisis. Sonet facilisis neglegentur eu cum. Sea eu error nostro, legimus scaevola et per. His quaeque legimus ad, quo no dissentias scribentur.

Ad ius nostrud pertinacia, no mollis iisque quaerendum mea. Iuvaret commune intellegebat nam id. Omnis nulla tantas ius eu, ad vim sonet delenit, ne ius graeco platonem. Mea ludus dolorum appetere ne, quo labores percipit mediocritatem ei. Accusata evertitur ad pro. Ad autem viderer eos, modo suas an ius, assum labore vix ei.

Mei choro dolores eu, vim labores platonem te. Discere contentiones vix ut, ei omittam voluptatibus nec. Urbanitas voluptatum vis at, ea qui oratio eleifend, cetero deleniti definitionem ius cu. At suas philosophia est, an numquam fabulas conclusionemque mel. No duo doctus salutatus.

No sed soleat iriure voluptatum. Pro possim oblique utroque an, congue placerat facilisi vim ex. At debet phaedrum iracundia mei. Brute harum iudicabit eos at, vel virtute elaboraret reformidans ad. Timeam laboramus pri no, eu pri meis graeci nonumes. Erant dictas eleifend mei id, cum ex nibh aeterno theophrastus.

Putent lucilius tractatos et sea, sit nusquam legendos efficiantur te, et vel graece ponderum mnesarchum. Recusabo disputando duo ad. Qui legere eligendi probatus te, primis laboramus cum ei. Ea omnesque lucilius pri, ne dicant pertinacia sit, duo errem iisque et. Ius postea vocibus quaerendum id, officiis mandamus vituperatoribus usu in. Mea an mundi suavitate posidonium. Sonet labores ei vis.";
$selId  = "";
break;
}
?>

<div style="clear:both;"></div>
<div id="main">
	<div id="leftNavDiv">
	
	</div>
	<div id = "mainContentDiv"></div>
</div>

<div id="footer" padding: 200px; align="center">
<?php include("footer.php");?>
</div>
<div style="border-bottom:12px solid #009966;">
</body>


