@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $rm =  $permission->check_menu_permission(61,$permission->role_id,1);
   $batch =  $permission->check_menu_permission(71,$permission->role_id,1);
   $individual =  $permission->check_menu_permission(72,$permission->role_id,1);

   $permission = $permission->check_permission(61,$permission->role_id); 
   //print_r($permission);
@endphp

<div class="status">
  <h1>resident management overview</h1>
</div>
  <div class="row">
               <div class="col-lg-12">
			   <ul class="summarytab">
                    
			   		@if(isset($rm) && $rm->view==1 && $admin_id !=1)
                    <li class="activeul"><a href="{{url('/opslogin/paymentoverview#vm')}}">Summary</a></li>
					@endif
                    @if(isset($rm) && $rm->create==1 && $admin_id !=1)
                    <li><a href="{{url('/opslogin/invoice/create')}}">Create MF/SF Invoice</a></li>
                    @endif
                    @if(isset($batch) && $batch->view==1 && $admin_id !=1)
                    <li    ><a href="{{url('/opslogin/invoice#vm')}}">Manage Batch Invoice</a></li>
                    @endif
                    @if(isset($individual) && $individual->view==1 && $admin_id !=1)
                    <li><a href="{{url('/opslogin/invoice_report#vm')}}">Manage Individual Invoices</a></li>
                    @endif
                    @if(isset($permission) && $permission->view==1 && $admin_id !=1)
                    <li><a href="{{url('/opslogin/paidlists#vm')}}">Filter Payments</a></li>
                    @endif
                    @if(isset($permission) && $permission->create==1 && $admin_id !=1)
                    <li><a href="{{url('/opslogin/invoice/uploadcsv#vm')}}">Import Invoices</a></li>
                    @endif
                  </ul>
				 </ul>
                  
               </div>
               </div>
  <div class="">
      @if (session('status'))
         <div class="alert alert-info">
         {{ session('status') }}
         </div>
      @endif
      <div class="row">
				         <div class="col-lg-6 coubg">
                     <h2 class="text-center">Monthly Fee Breakdown</h2>
                     <div class="wrapper">
		<ul class="parent">
			<li>
			<strong>Monthly Fee Collected</strong>
				<span class="circle01 color1">$@php echo number_format($monthly_fee,2) @endphp</span>
				<ul class="children first-child">
					<li>
					<p>Management Fund</p>
						<span class="circle color2">$@php echo number_format($mf_amounts,2) @endphp</span>
						
					</li>
					<li>
					<p>Sinking Fund</p>
						<span class="circle color3">$@php echo number_format($sf_amounts,2) @endphp</span>
					
					</li>
					<li>
					<p>Interest Fee</p>
						<span class="circle color4">$@php echo number_format($int_amounts,2) @endphp</span>
					
					</li>
					<li>
					<p>Tax</p>
						<span class="circle color5">$@php echo number_format($tax_amounts,2) @endphp</span>
					
					</li>
					
				</ul>
			</li>
		</ul>
	</div>
                     </div>
                   
                     <div class="col-lg-6 coubg">
                       <h2 class="text-center">Payment status</h2>
                           <p> Invoices Created: <b>{{$total_invoices}} </b></p>
                           <p> Payment Pending: <b>{{$pending_invoices}}</b> </p>
                           <p> Partial Payment: <b>{{$partial_invoices}} </b></p>
                           <p> Full Payment: <b>{{$paid_invoices}}</b></p>
                     </div>
                     <div class="col-lg-6 countp coubg pb-3">
                        <h2 class="text-center">Management / Sinking Fund Graph</h2>
                        <div id="chartContainer01" style="height: 370px; width: 100%;"></div>
                        <div class="removewatermark01"></div>
                     </div>
                
                     <div class="col-lg-6 coubg pb-3">
                     
                        <h2 class="text-center">Total Collected Till Date</h2>
                        <div id="chartContainer02" style="height: 370px; width: 100%;"></div>
                        <div class="removewatermark"></div>
                     </div>
                  </div>
   </div>

   
   @endsection
   <script>

window.onload = function () {
    CanvasJS.addColorSet("greenShades",
                [//colorSet Array

                "#bdd7ee",
                "#f8cbad",              
                ]);
var chart01 = new CanvasJS.Chart("chartContainer01", {
	animationEnabled: true,
	 colorSet: "greenShades",
	axisY: {
		titleFontColor: "#000",
		lineColor: "#fff",
		labelFontColor: "#000",
		tickColor: "#000"
	},
	axisY2: {
		titleFontColor: "#000",
		lineColor: "#fff",
		labelFontColor: "#000",
		tickColor: "#000"
	},	
	toolTip: {
		shared: true
	},
	legend: {
		cursor:"pointer",
		itemclick: toggleDataSeries
	},
	data: [{
		name:'MF',
		type: "column",
		legendText: "Management",
		showInLegend: true, 
		dataPoints:[ 
			@php echo $mf_y_axis @endphp
		 ]
	},
	{
		name:'SF',
		type: "column",	
		legendText: "Sinking",
		//axisYType: "secondary",
		showInLegend: true,
		dataPoints:[ 
			@php echo $sf_y_axis @endphp
		 ]
	}]
});
chart01.render();

function toggleDataSeries(e) {
	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
		e.dataSeries.visible = false;
	}
	else {
		e.dataSeries.visible = true;
	}
	chart01.render();
}

CanvasJS.addColorSet("greenShades",
                [//colorSet Array

                "#bdd7ee",
                "#f8cbad",              
                "#fbe397",              
                "#c3ddb1",              
                ]);
var chart = new CanvasJS.Chart("chartContainer02", {
	colorSet: "greenShades",
	//exportEnabled: true,
	animationEnabled: true,
	title:{
		//text: "State Operating Funds"
	},
	legend:{
		cursor: "pointer",
		itemclick: explodePie
	},
	data: [{
		type: "pie",
		showInLegend: true,
		toolTipContent: "{name}: <strong>S${y}</strong>",
		indexLabel: "{name} - S${y}",
		dataPoints: [
			//{ y: 26, name: "School Aid", exploded: true },
			//{ y: 20, name: "Medical Aid" },
			//{ y: 5, name: "Debt/Capital" },
			//{ y: 3, name: "Elected Officials" }

			{ y: @php echo $tot_mf_amounts @endphp, name: "Management Fund", exploded: true },
			{ y: @php echo $tot_sf_amounts @endphp, name: "Sinking Fund" },
			{ y: @php echo $tot_int_amounts @endphp, name: "Interest" },
			{ y: @php echo $tot_tax_amounts @endphp, name: "Tax" }
		]
	}]
});
chart.render();

function explodePie (e) {
	if(typeof (e.dataSeries.dataPoints[e.dataPointIndex].exploded) === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
		e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
	} else {
		e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
	}
	e.chart.render();

}
 }
      </script>

   <!--script>

window.onload = function () {
    CanvasJS.addColorSet("greenShades",
                [//colorSet Array

                "#bdd7ee",
                "#f8cbad",              
                ]);
var chart01 = new CanvasJS.Chart("chartContainer01", {
	animationEnabled: true,
	 colorSet: "greenShades",
	axisY: {
		//title:"Amount S$",
		titleFontColor: "#000",
		lineColor: "#fff",
		labelFontColor: "#000",
		tickColor: "#000"
	},
	axisY2: {
		titleFontColor: "#000",
		lineColor: "#fff",
		labelFontColor: "#000",
		tickColor: "#000"
	},	
	toolTip: {
		shared: true
	},
	legend: {
		cursor:"pointer",
		itemclick: toggleDataSeries
	},
	data: [{
		name:"MF",
		type: "column",
		legendText: "Management",
		showInLegend: true, 
		dataPoints:[ 
			@php echo $mf_y_axis @endphp
		 ]
	},
	{
		name:"SF",
		type: "column",	
		legendText: "Sinking",
		//axisYType: "secondary",
		showInLegend: true,
		dataPoints:[ 
			@php echo $sf_y_axis @endphp
		 ]
	}]
});
chart01.render();

function toggleDataSeries(e) {
	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
		e.dataSeries.visible = false;
	}
	else {
		e.dataSeries.visible = true;
	}
	chart01.render();
}
 
	  
	   CanvasJS.addColorSet("greenShades",
                [//colorSet Array

                "#bdd7ee",
                "#f8cbad",              
                "#fbe397",              
                "#c3ddb1",              
                ]);
         var chart02 = new CanvasJS.Chart("chartContainer02",
             {
			  colorSet: "greenShades",
               //  animationEnabled: true,
                 axisX: {
                     interval: 2,
					 
                 },
         		toolTip: {
         			    	enabled: true,
         			        shared: true,
fontWeight: "lighter",							},
               	data: [{
		type: "pie",
		//showInLegend: true,
		toolTipContent: "{name}: <strong>${y}</strong>",
		indexLabel: "{name} - ${y}",
		dataPoints: [
			
			{ y: @php echo $tot_mf_amounts @endphp, name: "MF", exploded: true },
			{ y: @php echo $tot_sf_amounts @endphp, name: "SF" },
			{ y: @php echo $tot_int_amounts @endphp, name: "INT" },
			{ y: @php echo $tot_tax_amounts @endphp, name: "GST" }
		]
	}]
             });
         chart02.render();

        }

	 
      </script-->
  