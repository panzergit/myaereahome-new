@extends('layouts.adminnew')



@section('content')


    <!-- Main content -->
              <div class="status">
               <h1>visitor management - new walk - in</h1>
			   </div>
			     <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                      <li ><a href="{{url('/opslogin/visitor-summary?view=dashboard')}}">Dashboard</a></li>
                     <li ><a href="{{url('/opslogin/visitor-summary?view=summary')}}">Summary</a></li>
                     <li    class="activeul"><a href="{{url('/opslogin/visitor-summary/create')}}">Add New Walk In</a></li>
                     <li><a href="{{url('/opslogin/visitor-summary/new#vm')}}">New Visitors</a></li>
                  </ul>
               </div>
               </div>
                <div class="">
                {!! Form::open(['method' => 'POST','class'=>'forunitvisit', 'id'=>"reg_form", 'url' => url('opslogin/visitor-summary'), 'files' => true]) !!}
                     <div class="">
                        <div class="">
                           <div class="row asignbg forunit">
                              <div class="col-lg-3">
                                 <div class="form-group ">
                                    <label> unit no*:</label>
                                    {{ Form::select('unit_no', ['' => '--Choose Unit--'] + $unites, null, ['class'=>'form-control','required' => true]) }}
                                   
                                 </div>
                              </div>
                               <div class="col-lg-3">
                                 <div class="form-group ">
                                    <label> purpose:</label>
                                    {{ Form::select('visiting_purpose', ['' => '--Choose Purpose--'] + $types, null, ['class'=>'form-control','required' => true,'id'=>'purpose','onchange'=>'getpurpose()']) }}
                                   
                                 </div>
                              </div>
                              <div class="col-lg-3" id="limit">
                                 <div class="form-group">
                                    <label> check availability:</label>
                                       <div id="sandbox">
                                          <input id="visiting_date" name="visiting_date" type="text" class="form-control" value="" OnChange="check_availability()" >
                                    </div>
                                 </div>
                              </div>
                           </div>
                           @foreach($visiting_types as $k =>$type)
                          
                           @php
                           
                              $display_style = "display:none";
                           @endphp
                              <div class="row asignbg forunit" id="type_{{$type->id}}" style="{{$display_style}}">
                              @if(isset($type->compinfo_required )&& $type->compinfo_required==1)
                                 <div class="col-lg-3" id="comp_info">
                                    <div class="form-group ">
                                       <label> company*:</label>
                                          <input type="text" class="form-control" name="company_info_{{$type->id}}" id="" value="" >
                                       
                                    </div>
                                 </div>
                                 @endif
                                 @if(isset($type->subcategory) && $type->cat_dropdown==1)
                                 <div class="col-lg-3" id="sub_cat_div">
                                    <div class="form-group">
                                       <label> {{$type->sub_category}}*:</label>
                                          <select class="form-control" id="sub_cat" name="sub_cat_{{$type->id}}">
                                             <option value="a">Choose item</option>
                                             @foreach($type->subcategory as $s =>$subcat)
                                             <option value="{{$subcat->id}}">{{$subcat->sub_category}}</option>
                                             @endforeach
                                          </select>
                                    </div>
                                 </div>
                                 @endif
                              </div>
                           @endforeach
                           <div id="current_id" value="">
                        </div>
                        @php
                          $limit = ($property->visitor_limit ==1)? $property->visitors_allowed:5;
                        @endphp
                        @for($i=1;$i<=$limit;$i++)
                        @php
                          if($i ==1)
                            $display_style = "";
                          else
                            $display_style = "display:none";
                        @endphp
                        <div class="row" id="add_field{{$i}}" style="{{$display_style}}">
                           <div class="col-lg-12 forunit " id="tbody">
                              <div class="clbord ">
                                 <h3 class="vhead vistres">Visitor {{$i}} Details</h3>
                                 <div class="form-group row vistres">
                                    <div class="col-lg-4">
                                       <div class="form-group row">
                                          <label class="col-sm-4 col-4 col-form-label">
                                          <label>Name{{($i ==1)?"* ":""}} : </label>
                                          </label>
                                          <div class="col-sm-8 col-8  ">
                                             <label class="col-form-label">
                                             <input type="text" name="name_{{$i}}" id="name_{{$i}}" class="form-control" placeholder="">
                                             </label>
                                          </div>
                                       </div>
                                       <div class="form-group row">
                                          <label class="col-sm-4 col-4 col-form-label">
                                          <label>Vechicle No : </label>
                                          </label>
                                          <div class="col-sm-8 col-8  ">
                                             <label class="col-form-label">
                                             <input type="text" name ="vehicle_no_{{$i}}" id="vehicle_no_{{$i}}" class="form-control" placeholder="">
                                             </label>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-lg-4">
                                       <div class="form-group row">
                                          <label class="col-sm-4 col-4 col-form-label">
                                          <label>Mobile{{($i ==1)?"* ":""}} : </label>
                                          </label>
                                          <div class="col-sm-8 col-8  ">
                                             <label class="col-form-label">
                                             <input type="text" name ="mobile_{{$i}}" id="mobile_{{$i}}" class="form-control" placeholder="">
                                             </label>
                                          </div>
                                       </div>

                                       <div class="form-group row idclass" style="display:none">
                                          <label class="col-sm-4 col-4 col-form-label">
                                          <label>ID No{{($i ==1)?"* ":""}} : </label>
                                          </label>
                                          <div class="col-sm-8 col-8  ">
                                             <label class="col-form-label">
                                             <input type="text" name ="id_number_{{$i}}" id="id_number_{{$i}}" class="form-control" placeholder="">
                                             </label>
                                          </div>
                                       </div>

                                    </div>
                                    <div class="col-lg-4">
                                       <div class="form-group row">
                                          <label class="col-sm-4 col-4 ">
                                          <label>  Entry Date : </label>
                                          </label>
                                          <div class="col-sm-8 col-8  labelb col-form-label">
                                       <b> {{date('d/m/y')}}</b>
                                          </div>
                                       </div>
                                       <div class="form-group row">
                                          <label class="col-sm-4 col-4 col-form-label">
                                          <label> Entry Time : </label>
                                          </label>
                                          <div class="col-sm-8 col-8  labelb col-form-label">
                                           <b> {{date('g:i a')}}</b>
                                           
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endfor

                        <div class="row">
                           <div class="col-lg-12" id="buttonsection">
                              <a class="addrow" id="addBtn03" type="button" onclick="showmore()">
							                         <img src="{{url('assets/img/plus.png')}}" class="upimg"/><br>
                              Add Another
                              </a>
                              <input type="hidden" id="rowcount" value="1">
                              <input type="hidden" id="maxcount" value="{{$limit}}">
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-lg-12" id="submit_btn_div">
                        <input type="submit" class="submit mt-3 ml-3 float-right " value="Submit">

                        <!--<button type="submit" class="submit2 mt-3 ml-3 float-right mlres">SUBMIT</button>-->
                        </div>
                     </div>
                  </form>
               </div>
              </div>

@stop