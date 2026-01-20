@extends('layouts.adminnew')




@section('content')
@php 
   $permission = Auth::user();
   $account_id = Auth::user()->account_id;
   $reg_count = $permission->noOfReg($account_id);
   $permission = $permission->check_permission(7,$permission->role_id); 
 

@endphp
 <style>
         .monthbg{    background: #fff;
         margin-top: 20px;
         border-left: 15px solid #e9e9ea;     border-right: 15px solid #e9e9ea;}
         .monthbg h2 {     font-weight: 600;
         font-size: 16px;
         margin-top: 16px;
         text-align: center;
         }
       
      </style>
<div class="status">
  <h1>manage user lists</h1>
</div>

  <div class="">
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
  @endif
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li @if(!request()->has('view')) class="activeul" @endif><a href="{{url('/opslogin/user')}}">Dashboard</a></li>
                     <li @if(request()->has('view') && request()->view=='summary') class="activeul" @endif><a href="{{url('/opslogin/user?view=summary')}}">Summary</a></li>
                     @if(isset($permission) && $permission->edit==1 )
                        <li><a href="{{url('/opslogin/user/access')}}">User Access</a></li>
                     @endif
                     @if(isset($permission) && $permission->create==1 )
                        <li><a href="{{url('/opslogin/user/create')}}">Create new user</a></li>
                     @endif
                     @if(isset($permission) && $permission->create==1 )
                        <li><a href="#myModalcnf"  data-toggle="modal" >Import from CSV</a></li>
                     @endif
                     @if(isset($permission) && $permission->edit==1 )
                        <li><a href="{{url('/opslogin/unitlist')}}">Unit information</a></li>
                     @endif
                     @if(isset($permission) && $permission->edit==1 )
                        <li><a href="{{url('/opslogin/registrations')}}">Registrations  @if(isset($reg_count) && $reg_count >0 )
                  <span class="notification17">{{$reg_count}}</span>
                  @endif</a> </li>
                     @endif
                  </ul>
               </div>
   </div>
               <div id="myModalcnf" class="modal fade">
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header flex-column">
								
				<h4 class="modal-title w-100">Message</h4>	
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<p>Building and Unit should be created before bulk upload of User.<br/><br />Are you sure want to continue?</p>
			</div>
			<div class="modal-footer justify-content-center">
         <a href="{{url("/opslogin/user/uploadcsv")}}" class="btn btn-secondary">Confim</a>
				<a type="button" class="btn btn-danger" data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div> 
@if(!request()->has('view'))
<div>
    <div class="row">
        <div class="col-lg-6 monthbg pb-3">
            <h2 class="text-center">Users by Role</h2>
            <div id="chartContainer01" style="height: 370px; width: 100%;"></div>
            <div class="removewatermark"></div>
        </div>
       <div class="col-lg-6 countp monthbg pb-3">
          <h2 class="text-center">Application Usage Status</h2>
          <div id="chartContainer02" style="height: 370px; width: 100%;"></div>
       </div>
       <div class="col-lg-6 countp monthbg pb-3">
          <h2 class="text-center">Application Platform Usage Status</h2>
          <div id="chartContainer03" style="height: 370px; width: 100%;"></div>
       </div>
       <div class="col-lg-6 countp monthbg pb-3">
          <h2 class="text-center">Car Usage Status</h2>
          <div id="chartContainer04" style="height: 370px; width: 100%;"></div>
       </div>
    </div>
</div>
@endif
@if(request()->has('view') && request()->view=='summary')
  <form action="{{url('/opslogin/user/search')}}" method="get" role="search" class="forunit forbottom">
           
                     <div class="row asignbg">
					    <input name="view" type="hidden" value="summary"/>
						 <div class="col-lg-2">
                           <div class="form-group">
						    <input  type="text" class="form-control" name="name" id="name" value="<?php echo(isset($name)?$name:'');?>" placeholder="Enter First Name">
						    </div>
                        </div>
						 <div class="col-lg-2">
                           <div class="form-group">
						     <input  type="text" class="form-control" name="last_name" id="last_name" value="<?php echo(isset($last_name)?$last_name:'');?>" placeholder="Enter Last Name">
						    </div>
                        </div>
						 <div class="col-lg-3">
                     {{ Form::select('building', ['' => '--Block--'] + $buildings, $building, ['class'=>'form-control','id'=>'building']) }}
						 </div>
						 <div class="col-lg-2">
                           <div class="form-group">
                       <input  type="text" name="unit" class="form-control" value="<?php echo(isset($unit)?$unit:'');?>" id="unit_list" placeholder=" Unit Number">
                           </div>
                        </div>
						 <div class="col-lg-3">
                           <div class="form-group">
						    {{ Form::select('role', ['' => '--User Role--'] + $roles, $role, ['class'=>'form-control','id'=>'role']) }}
						    </div>
                        </div>
                     <div class="col-lg-4">
                        <div class="form-group mt0-3">
						         <input  type="text" class="form-control" name="email" id="email" value="<?php echo(isset($email)?$email:'');?>" placeholder="Enter email">
						      </div>
                     </div>
                     <div class="col-lg-3">
                        <div class="form-group mt0-3">
						         {{ Form::select('login_from', ['' => '--All--',1=>'IOS App','2'=>"Android App"], $login_from, ['class'=>'form-control','id'=>'login_from']) }}
						      </div>
                     </div>
						 <div class="col-lg-5">
                     <div class="form-group mt0-3">
                        @if(isset($permission) && $permission->edit==1 )
                           <a href="{{url("/opslogin/user/export")}}"  class="submit float-right ml-2" style="width:auto;">Export to CSV</a>
                        @endif
						   <a href="{{url("/opslogin/user?view=summary")}}"  class="submit  float-right ml-2">clear</a>
						    <button type="submit" class="submit float-right">search</button>
						    </div>
                        </div>
                     

                     </div>
                  </form>
<style>
.mb-112{    padding-bottom: 125px;
    margin-bottom: -120px;}
</style>
                  <div class="overflowscroll2 mb-112">
                        <table class="gap fillscreen">
                        <tr>
                        @if(Auth::user()->role_id ==1)
                           <th>PROPERTY</th>
                        @endif
                           <th width="10%" style="    padding-left: 15px!important;">block</th>
                           <th width="5%">unit</th>
                           <th width="5%">photo</th>
                           <th width="10%">first name</th>
                           <th width="10%">last name</th>
                           <th width="12%">assigned role</th>
                           <th width="10%">password</th>
                           <th width="5%">app</th>
                           <th width="10%">contact</th>
                           <th width="8%">start date</th>
                           <th width="8%">end date</th>
                           <th width="7%">actions</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($users)
                        @foreach($users as $k => $user)
                        @php
                        $role_id =  isset($user->getuser->role_id)?$user->getuser->role_id:'';
                        $building_name = '';
                        $unit_name = '';
                        if(isset($unit) && $unit >0){
                           //echo "User :".$user->id." Property :".$user->account_id." Unit :".$unit;
                           $unitObj = new \App\Models\v7\Unit();
                           //echo $unit;
                           $moreinfo = new \App\Models\v7\UserMoreInfo();
                           $purchaseUnitInfo = $moreinfo->moreunitinfo($user->id,$user->account_id,$unit);
                           $role_id = isset($purchaseUnitInfo->role_id)?$purchaseUnitInfo->role_id:'';
                           //echo $role_id;
                           $roleInfo = $moreinfo->roleInfo($role_id);
                           $unitinfo = $unitObj->unit_info($user->user_id,$unit,$building,$user->account_id,$user->id);
                           $building_name = isset($unitinfo->addubuildinginfo)?$unitinfo->addubuildinginfo->building:'';
                           $unit_name = isset($unitinfo->addunitinfo)?"#".Crypt::decryptString($unitinfo->addunitinfo->unit):'';
                        }
                        else if(!empty($user->getuser->role_id) &&in_array($user->getuser->role_id,$app_user_lists)){
                           //echo "User :".$user->id." Property :".$user->account_id;
                           $moreinfo = new \App\Models\v7\UserMoreInfo();
                           $purchaseUnitInfo = $moreinfo->moreunitinfo($user->id,$user->account_id);
                           $role_id = isset($purchaseUnitInfo->role_id)?$purchaseUnitInfo->role_id:'';

                           $roleInfo = $moreinfo->roleInfo($role_id);
                           $unitInfo = $moreinfo->unitInfo(isset($purchaseUnitInfo->unit_id)?$purchaseUnitInfo->unit_id:'');
                           if(isset($unitInfo))
                              $buildingInfo = $moreinfo->buildinginfo($unitInfo->building_id);
                           $building_name = isset($buildingInfo)?$buildingInfo->building:'';
                           $unit_name = isset($unitInfo)?"#".Crypt::decryptString($unitInfo->unit):'';
                           //exit;
                        } 
                        
                        @endphp
                        <tr class='{{($user->status !=1)?"textdisabled":""}}'>
                        @if(Auth::user()->role_id ==1)
                        <td>{{isset($user->company_name)?$user->company_name:''}}</td>
                        @endif
                           <td  class='roundleft' style="    padding-left: 15px!important;">{{$building_name}}</td>
                           <td class='spacer'>{{$unit_name}}</td>
                           <td class='spacer'>
                              @if(isset($user->profile_picture) && $user->profile_picture !='')
                                 <a href="{{$file_path}}/{{$user->profile_picture}}" target="_blank">
                                    <img src="{{$file_path}}/{{$user->profile_picture}}" class="viewimg phvert">
                                 </a>
                              @endif
                           </td>
                           <td class='spacer'>{{Crypt::decryptString($user->first_name)}} </td>
                           <td class='spacer'><a href="#" alt="{{isset($user->last_name)?Crypt::decryptString($user->last_name):''}}" title="{{isset($user->last_name)?Crypt::decryptString($user->last_name):''}}" style="color:#5D5D5D" >{{isset($user->last_name)?Str::limit(Crypt::decryptString($user->last_name),20):''}}</a></td>
                           <td class='spacer'>
                              @if($role_id >0 && !in_array($role_id,$app_user_lists))
                                 <a href="#" alt="{{isset($user->getuser->role->name)?$user->getuser->role->name:''}}" title="{{isset($user->getuser->role->name)?$user->getuser->role->name:''}}" style="color:#5D5D5D">{{isset($user->getuser->role->name)?Str::limit($user->getuser->role->name,20):''}} </a>
                                 
                              @else
                                 <a href="#" alt="{{isset($roleInfo->name)?$roleInfo->name:''}}" title="{{isset($roleInfo->name)?$roleInfo->name:''}}" style="color:#5D5D5D">{{isset($roleInfo->name)?Str::limit($roleInfo->name,20):''}}</a>
                              @endif
                          
                           </td>
                           <td class='spacer'>{{(!empty($user->getuser->password) && $user->getuser->password!='')?'Yes':'No'}}</td>
                           <td class='spacer'>
                           @php
                           /*if(isset($user->getuser->getos))
                           {
                              if($user->getuser->getos->login_from==1)
                                 echo "IOS";
                              else  if($user->getuser->getos->login_from==2)
                                 echo "Android";
                           }*/
                           echo isset($user->getuser->app_version)?$user->getuser->app_version:'';
                           
                           @endphp
                           </td>
                           <td class='spacer'>{{isset($user->phone)?Crypt::decryptString($user->phone):''}}</td>
                           <td class='spacer'>{{date('d/m/y',strtotime($user->created_at))}}</td>
                           <td class='spacer'>{{($user->deactivated_date != '0000-00-00' && $user->deactivated_date != '')?date('d/m/y',strtotime($user->deactivated_date)):''}}</td>
                           <td class='roundright'>
						    <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                             
                              
                              @if(isset($permission) && $permission->edit==1 )
                                 <a class="dropdown-item" href="{{url("opslogin/user/info/$user->id")}}">Summary</a>
                                 <a class="dropdown-item" href="{{url("opslogin/user/$user->id/edit")}}">Edit</a>
                              
                                 @if($role_id =='' || in_array($role_id,$app_user_lists))
                                    <a class="dropdown-item" href="{{url("opslogin/user/userunits/$user->id")}}" >Assign Units</a>
                                    <a class="dropdown-item" href="{{url("opslogin/user/usercards/$user->id")}}" >Assign Cards</a>
                                 @endif
                                    <a class="dropdown-item" href="{{url("opslogin/user/userdevices/$user->id")}}"> Assign Devices</a>
                                 @if($role_id ==''  || in_array($role_id,$app_user_lists))
                                    <a class="dropdown-item" href="{{url("opslogin/user/useraccess/$user->id")}}">System Access</a>
                                 @endif
                                 @if($user->status ==0)
                                 <a class="dropdown-item" href="#" onclick="activate_record('{{url("opslogin/user/activate/$user->id")}}');">Activate</a>
                                 @else
                                 <a class="dropdown-item" href="#"   onclick="deactivate_record('{{url("opslogin/user/deactivate/$user->id")}}');" data-toggle="tooltip" data-placement="top" title="De-Activate">De-Activate</a>
                                 @endif
                                 <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/user/delete/$user->id")}}');" >Delete</a>
                              @endif
							  
                                    </div>
                                 </div>
                            <!--<a href="{{url("admin/user/rights/$user->id")}}"><img src="{{url('assets/admin/img/confirm.png')}}" alt=""></a>-->
                           
                            </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                     </table>
					  </div>
					                 <div class="col-lg-12">
						@if ($users->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($users->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $users->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($users->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $users->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($users->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $users->lastPage()) as $i)
									@if($i >= $users->currentPage() - 2 && $i <= $users->currentPage() + 2)
										@if ($i == $users->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $users->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($users->currentPage() < $users->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($users->currentPage() < $users->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $users->appends($_GET)->url($users->lastPage()) }}">{{ $users->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($users->hasMorePages())
									<li><a href="{{ $users->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
				</div>	
                 @endif
  

               </div>
@endsection

@section('customJS')
<script>
window.onload = function () {
    CanvasJS.addColorSet("greenShades",
                         [//colorSet Array
         
                        "#f2cfee",
                         "#caeefb",              
                         "#c2f1c8",              
                         "#fbe3d6",              
                         "#c1e5f5",              
                         "#dceaf7",             
                         "#e59edd",             
                         "#f6c6ad",             
                         "#d5d5b8",             
                         "#d9f2d0",             
                         ]);
    let chartOneData = [];
    @if(isset($chartOne))
    @foreach($chartOne as $d)
    chartOneData.push({
        y : parseInt("{{ $d['y'] }}"),
        name : "{{ $d['name'] }}",
        exploded : "{{ $d['exploded'] }}"
    });
    @endforeach
    @endif
    
    var chart = new CanvasJS.Chart("chartContainer01", {
		 colorSet: "greenShades",
    	animationEnabled: true,
    	legend:{
    		cursor: "pointer",
    		itemclick: explodePie
    	},
    	data: [{
    		type: "pie",
    		showInLegend: true,
    		toolTipContent: "{name}: <strong>{y}</strong>",
    		indexLabel: "{name} - {y}",
    		dataPoints: chartOneData
    	}]
    });
    chart.render();

    var chartTwo = new CanvasJS.Chart("chartContainer02", {
    	animationEnabled: true,
    	theme: "light2",
    	data: [{
    		type: "column",
    		yValueFormatString: "#,##0.00'%'",
    		toolTipContent:
            "<b>{label}</b><br/>Percentage: {y}<br/>Users: {users}",
    		dataPoints: [
    		    {y: parseFloat("{{ $chartTwo['app_using']['percentage'] ?? 0 }}"), label:"Using Application", users : "{{ $chartTwo['app_using']['numbers'] ?? 0 }}"},
    		    {y: parseFloat("{{ $chartTwo['app_not_using']['percentage'] ?? 0 }}"), label: "Not using Application", users : "{{ $chartTwo['app_not_using']['numbers'] ?? 0 }}"},
    		   ]
    	}]
    });
    chartTwo.render();
    
    var chartThree = new CanvasJS.Chart("chartContainer03", {
    	animationEnabled: true,
    	theme: "light2",
    	data: [{
    		type: "column",
    		yValueFormatString: "#,##0.00'%'",
    		toolTipContent:
            "<b>{label}</b><br/>Percentage: {y}<br/>Users: {users}",
    		dataPoints: [
    		    {y: parseFloat("{{ $chartThree['android_usage']['percentage'] ?? 0 }}"), label:"Android Users", users : "{{ $chartThree['android_usage']['numbers'] ?? 0 }}", color : '#f2cfee'},
    		    {y: parseFloat("{{ $chartThree['ios_usage']['percentage'] ?? 0 }}"), label: "Ios Users", users : "{{ $chartThree['ios_usage']['numbers'] ?? 0 }}",color:'#c1e5f5'},
    		   ]
    	}]
    });
    chartThree.render();
    
    var chartFour = new CanvasJS.Chart("chartContainer04", {
    	animationEnabled: true,
    	theme: "light2",
    	data: [{
    		type: "column",
    		yValueFormatString: "#,##0.00'%'",
    		toolTipContent:
            "<b>{label}</b><br/>Percentage: {y}<br/>Cars: {users}",
    		dataPoints: [
    		    {y: parseFloat("{{ $chartFour['one_car_usage']['percentage'] ?? 0 }}"), label:"One Car", users : "{{ $chartFour['one_car_usage']['numbers'] ?? 0 }}"},
    		    {y: parseFloat("{{ $chartFour['two_car_usage']['percentage'] ?? 0 }}"), label: "Two Cars", users : "{{ $chartFour['two_car_usage']['numbers'] ?? 0 }}"},
    		    {y: parseFloat("{{ $chartFour['no_car_usage']['percentage'] ?? 0 }}"), label: "No Cars", users : "{{ $chartFour['no_car_usage']['numbers'] ?? 0 }}"},
    		   ]
    	}]
    });
    chartFour.render();
}

function explodePie (e) {
	if(typeof (e.dataSeries.dataPoints[e.dataPointIndex].exploded) === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
		e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
	} else {
		e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
	}
	e.chart.render();

}

	$(document).ready(function(e){
		$('#exportusers').click(function() {
			var unit = $('#unit_list').val();
			var name = $('#name').val();
			var role = $('#role').val();
			var option = '';
			
			if($("#option").is(":checked")) {
				option = 'unit';
			} else if($("#option1").is(":checked")) {
				option = 'name';
			} else if($("#option2").is(":checked")) {
				option = 'role';
			}
			
			var url = "{{ url('/opslogin/exportusers') }}?unit="+unit+"&name="+name+"&role="+role+"&option="+option;
			window.open(url, '_blank');
		});		    
	});
</script>
@endsection




