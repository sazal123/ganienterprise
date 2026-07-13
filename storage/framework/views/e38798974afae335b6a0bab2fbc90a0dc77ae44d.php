
<?php $__env->startSection('title',$order_status->name.' Order'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?php echo e(route('admin.order.create')); ?>" class="btn btn-danger rounded-pill"><i class="fe-shopping-cart"></i> Add New</a>
                </div>
                <h4 class="page-title"><?php echo e($order_status->name); ?> Order (<?php echo e($order_status->orders_count); ?>)</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row order_page">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-8">
                        <ul class="action2-btn">
                            <li><a data-bs-toggle="modal" data-bs-target="#asignUser" class="btn rounded-pill btn-success"><i class="fe-plus"></i> Assign User</a></li>
                            <li><a data-bs-toggle="modal" data-bs-target="#changeStatus" class="btn rounded-pill btn-primary"><i class="fe-plus"></i> Change Status</a></li>
                            <li><a href="<?php echo e(route('admin.order.bulk_destroy')); ?>" class="btn rounded-pill btn-danger order_delete"><i class="fe-plus"></i> Delete All</a></li>
                            <li><a href="<?php echo e(route('admin.order.order_print')); ?>" class="btn rounded-pill btn-info multi_order_print"><i class="fe-printer"></i> Print</a></li>
                            <?php if($steadfast): ?>
                            <li><a href="<?php echo e(route('admin.bulk_courier', 'steadfast')); ?>?status=5" class="btn rounded-pill btn-info multi_order_courier"><i class="fe-truck"></i> Steadfast</a></li>
                            <?php endif; ?>
                            <li><a data-bs-toggle="modal" data-bs-target="#pathao" class="btn rounded-pill btn-info"><i class="fe-truck"></i> pathao</a></li>
                            
                        </ul>
                    </div>
                    <div class="col-sm-4">
                        <form class="custom_form">
                            <div class="form-group">
                                <input type="text" name="keyword" placeholder="Search">
                                <button class="btn  rounded-pill btn-info">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive ">
                <table id="datatable-buttons" class="table table-striped   w-100">
                    <thead>
                        <tr>
                            <th style="width:2%"><div class="form-check"><label class="form-check-label"><input type="checkbox" class="form-check-input checkall" value=""></label>
                            <th style="width:2%">SL</th>
                                    </div></th>
                            <th style="width:8%">Action</th>
                            <th style="width:8%">Invoice</th>
                            <th style="width:10%">Date</th>
                            <th style="width:10%">Name</th>
                            <th style="width:10%">Phone</th>
                            <th style="width:10%">Check</th>
                            <th style="width:10%">Amount</th>
                            <th style="width:10%">Status</th>
                        </tr>
                    </thead>
                
                
                    <tbody>
                        <?php $__currentLoopData = $show_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><input type="checkbox" class="checkbox" value="<?php echo e($value->id); ?>"></td>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td>
                                <div class="button-list custom-btn-list">   
                                    <a href="<?php echo e(route('admin.order.invoice',['invoice_id'=>$value->invoice_id])); ?>" title="Invoice"><i class="fe-eye"></i></a>
                                    <a href="<?php echo e(route('admin.order.process',['invoice_id'=>$value->invoice_id])); ?>" title="Process"><i class="fe-settings"></i></a>
                                    <a href="<?php echo e(route('admin.order.edit',['invoice_id'=>$value->invoice_id])); ?>" title="Edit"><i class="fe-edit"></i></a>
                                    <form method="post" action="<?php echo e(route('admin.order.destroy')); ?>" class="d-inline">        
                                        <?php echo csrf_field(); ?>
                                    <input type="hidden" value="<?php echo e($value->id); ?>" name="id">
                                    <button type="submit" title="Delete" class="delete-confirm"><i class="fe-trash-2"></i></button>

                                    
                                    </form>
                                </div>
                            </td>
                            <td><?php echo e($value->invoice_id); ?></td>
                            <td><?php echo e(date('d-m-Y', strtotime($value->updated_at))); ?><br> <?php echo e(date('h:i:s a', strtotime($value->updated_at))); ?></td>
                            <td><strong><?php echo e($value->shipping?$value->shipping->name:''); ?></strong><p><?php echo e($value->shipping?$value->shipping->address:''); ?></p></td>
                            <td><?php echo e($value->shipping?$value->shipping->phone:''); ?></td>
                            <td> <a target="_blank" style="text-decoration: underline" href="https://greenviewit.com/check-fraud-customer" >Fraud Customer Check</a></td>
                            <td>৳<?php echo e($value->amount); ?></td>
                            <td><?php echo e($value->status?$value->status->name:''); ?></td>
                            
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                </div>
                <div class="custom-paginate">
                    <?php echo e($show_data->links('pagination::bootstrap-4')); ?>

                </div>
            </div> <!-- end card body-->
           
        </div> <!-- end card -->
    </div><!-- end col-->
   </div>
</div>
<!-- Assign User End -->
<div class="modal fade" id="asignUser" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Assign User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?php echo e(route('admin.order.assign')); ?>" id="order_assign">
      <div class="modal-body">
        <div class="form-group">
            <select name="user_id" id="user_id" class="form-control">
                <option value="">Select..</option>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($value->id); ?>"><?php echo e($value->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- Assign User End-->

<!-- Assign User End -->
<div class="modal fade" id="changeStatus" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Assign User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?php echo e(route('admin.order.status')); ?>" id="order_status_form">
      <div class="modal-body">
        <div class="form-group">
            <select name="order_status" id="order_status" class="form-control">
                <option value="">Select..</option>
                <?php $__currentLoopData = $orderstatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($value->id); ?>"><?php echo e($value->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- Assign User End-->
<!-- pathao coureir start -->
<div class="modal fade" id="pathao" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pathao Courier</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?php echo e(route('admin.order.pathao')); ?>" id="order_sendto_pathao">

      <div class="modal-body">
        <div class="form-group">
            <label for="pathaostore" class="form-label">Store</label>
           <select name="pathaostore" id="pathaostore" class="pathaostore form-control" >
             <option value="">Select Store...</option>
             <?php if(isset($pathaostore['data']['data'])): ?>
             <?php $__currentLoopData = $pathaostore['data']['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
             <option value="<?php echo e($store['store_id']); ?>"><?php echo e($store['store_name']); ?></option>
             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
             <?php else: ?>
             <?php endif; ?>
           </select>
            <?php if($errors->has('pathaostore')): ?>
              <span class="invalid-feedback" role="alert">
                <strong><?php echo e($errors->first('pathaostore')); ?></strong>
              </span>
              <?php endif; ?>
        </div>
        <!-- form group end -->
        <div class="form-group mt-3">
          <label for="pathaocity" class="form-label">City</label>
           <select name="pathaocity" id="pathaocity" class="chosen-select pathaocity form-control" style="width:100%" >
             <option value="">Select City...</option>
             <?php if(isset($pathaocities['data']['data'])): ?>
             <?php $__currentLoopData = $pathaocities['data']['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
             <option value="<?php echo e($city['city_id']); ?>"><?php echo e($city['city_name']); ?></option>
             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
             <?php else: ?>
             <?php endif; ?>
           </select>
            <?php if($errors->has('pathaocity')): ?>
              <span class="invalid-feedback" role="alert">
                <strong><?php echo e($errors->first('pathaocity')); ?></strong>
              </span>
              <?php endif; ?>
        </div>
        <!-- form group end -->
        <div class="form-group mt-3">
          <label for="" class="form-label">Zone</label>
             <select name="pathaozone" id="pathaozone" class="pathaozone chosen-select form-control  <?php echo e($errors->has('pathaozone') ? ' is-invalid' : ''); ?>" value="<?php echo e(old('pathaozone')); ?>"  style="width:100%">
            </select>
             <?php if($errors->has('pathaozone')): ?>
              <span class="invalid-feedback" role="alert">
                <strong><?php echo e($errors->first('pathaozone')); ?></strong>
              </span>
              <?php endif; ?>
        </div>
        <!-- form group end -->
        <div class="form-group mt-3">
          <label for="" class="form-label">Area</label>
             <select name="pathaoarea" id="pathaoarea" class="pathaoarea chosen-select form-control  <?php echo e($errors->has('pathaoarea') ? ' is-invalid' : ''); ?>" value="<?php echo e(old('pathaoarea')); ?>"  style="width:100%">
            </select>
             <?php if($errors->has('pathaoarea')): ?>
              <span class="invalid-feedback" role="alert">
                <strong><?php echo e($errors->first('pathaoarea')); ?></strong>
              </span>
              <?php endif; ?>
        </div>
        <!-- form group end -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- pathao courier  End-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $(".checkall").on('change',function(){
      $(".checkbox").prop('checked',$(this).is(":checked"));
    });

    // order assign
    $(document).on('submit', 'form#order_assign', function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var method = $(this).attr('method');
        let user_id=$(document).find('select#user_id').val();
    
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids=order.get();
        
        if(order_ids.length ==0){
            toastr.error('Please Select An Order First !');
            return ;
        }
        
        $.ajax({
           type:'GET',
           url:url,
           data:{user_id,order_ids},
           success:function(res){
               if(res.status=='success'){
                toastr.success(res.message);
                window.location.reload();
                
            }else{
                toastr.error('Failed something wrong');
            }
           }
        });
    
    });

    // order status change 
    $(document).on('submit', 'form#order_status_form', function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var method = $(this).attr('method');
        let order_status=$(document).find('select#order_status').val();
    
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids=order.get();
        
        if(order_ids.length ==0){
            toastr.error('Please Select An Order First !');
            return ;
        }
        
        $.ajax({
           type:'GET',
           url:url,
           data:{order_status,order_ids},
           success:function(res){
               if(res.status=='success'){
                toastr.success(res.message);
                window.location.reload();
                
            }else{
                toastr.error('Failed something wrong');
            }
           }
        });
    
    });
    // order delete
    $(document).on('click', '.order_delete', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids=order.get();
        
        if(order_ids.length ==0){
            toastr.error('Please Select An Order First !');
            return ;
        }
        
        $.ajax({
           type:'GET',
           url:url,
           data:{order_ids},
           success:function(res){
               if(res.status=='success'){
                toastr.success(res.message);
                window.location.reload();
                
            }else{
                toastr.error('Failed something wrong');
            }
           }
        });
    
    });
    
    // multiple print
    $(document).on('click', '.multi_order_print', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids=order.get();
        
        if(order_ids.length ==0){
            toastr.error('Please Select Atleast One Order!');
            return ;
        }
        $.ajax({
           type:'GET',
           url,
           data:{order_ids},
           success:function(res){
               if(res.status=='success'){
                   console.log(res.items, res.info);                          
                   var myWindow = window.open("", "_blank");                   
                   myWindow.document.write(res.view);
            }else{
                toastr.error('Failed something wrong');
            }
           }
        });
    });
    // multiple courier
    $(document).on('click', '.multi_order_courier', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        console.log(url);
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids=order.get();
        
        if(order_ids.length ==0){
            toastr.error('Please Select An Order First !');
            return ;
        }
        
        $.ajax({
           type:'GET',
           url:url,
           data:{order_ids},
           success:function(res){
               if(res.status=='success'){
                   console.log(res.message);
                   console.log(res.success);
                   console.log(res.failed);
                    toastr.success(res.message);
                    toastr.success(res.success);
                    toastr.error(res.failed);
                    window.location.reload();
                
            }else{
                console.log(res.message);
                toastr.error('Failed something wrong');
            }
           }
        });
    
    });
})
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ecommerce/ganienterprise/resources/views/backEnd/order/index.blade.php ENDPATH**/ ?>