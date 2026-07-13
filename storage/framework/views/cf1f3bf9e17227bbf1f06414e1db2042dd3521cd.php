<?php $__env->startSection('title','Customer Manage'); ?>

<?php $__env->startSection('css'); ?>
<link href="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<style>
  .customer-table { font-size: 13px; }
  .customer-table th { white-space: nowrap; background: #f8f9fa; }
  .customer-table td { vertical-align: middle; }
  .search-box { background: #fff; border: 1px solid #dee2e6; border-radius: 6px; padding: 3px; }
  .search-box .form-control { border: none; box-shadow: none; }
  .search-box .btn { border-radius: 4px; }
  .feedback-text { max-width: 150px; white-space: normal; word-wrap: break-word; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?php echo e(route('customers.create')); ?>" class="btn btn-success rounded-pill"><i class="fe-plus-circle"></i> Add New</a>
                </div>
                <h4 class="page-title">Customer Manage</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- Search & Filter -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body py-2">
                    <form action="<?php echo e(route('customers.index')); ?>" method="GET" class="row align-items-end">
                        <div class="col-md-5">
                            <div class="input-group search-box">
                                <input type="text" class="form-control" name="keyword" value="<?php echo e(request()->get('keyword')); ?>" placeholder="Search by name, phone, email, address, customer code, whatsapp, feedback...">
                                <button class="btn btn-info" type="submit"><i class="fe-search"></i> Search</button>
                                <?php if(request()->get('keyword') || request()->get('status')): ?>
                                <a href="<?php echo e(route('customers.index')); ?>" class="btn btn-secondary">Clear</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="Premium" <?php echo e(request()->get('status') == 'Premium' ? 'selected' : ''); ?>>Premium</option>
                                <option value="General" <?php echo e(request()->get('status') == 'General' ? 'selected' : ''); ?>>General</option>
                                <option value="Inactive" <?php echo e(request()->get('status') == 'Inactive' ? 'selected' : ''); ?>>Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100 customer-table">
                            <thead>
                                <tr>
                                    <th>SL.</th>
                                    <th>Customer ID</th>
                                    <th>Customer's Name</th>
                                    <th>Address</th>
                                    <th>Thana</th>
                                    <th>District</th>
                                    <th>Contact</th>
                                    <th>WhatsApp No.</th>
                                    <th>Customer's Status</th>
                                    <th>No. of Deal</th>
                                    <th>Ordered Product Category</th>
                                    <th>Total Order Value (tk)</th>
                                    <th>Last Order Date</th>
                                    <th>Feedback (Customer)</th>
                                    <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th class="month-col"><?php echo e($month); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $show_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $orders = $value->orders;
                                    $orderCount = $orders->count();
                                    $totalOrderValue = $orders->sum('amount');
                                    $lastOrder = $orders->sortByDesc('created_at')->first();

                                    // Get categories from ordered products
                                    $categoryNames = \App\Models\OrderDetails::whereIn('order_id', $orders->pluck('id'))
                                        ->join('products', 'order_details.product_id', '=', 'products.id')
                                        ->join('categories', 'products.category_id', '=', 'categories.id')
                                        ->distinct()
                                        ->pluck('categories.name')
                                        ->toArray();
                                    $productCategories = implode(', ', array_unique($categoryNames));

                                    // Get monthly order data for this customer
                                    $customerMonthly = isset($monthlyOrders[$value->id]) ? $monthlyOrders[$value->id]->keyBy('month_key') : collect();
                                ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td>
                                        <strong><?php echo e($value->customer_code ?? 'GE-OR' . date('y') . '-' . str_pad($value->id, 2, '0', STR_PAD_LEFT)); ?></strong>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('customers.profile',['id'=>$value->id])); ?>" class="text-primary fw-semibold">
                                            <?php echo e($value->name); ?>

                                        </a>
                                    </td>
                                    <td><?php echo e($value->address ?? 'N/A'); ?></td>
                                    <td><?php echo e($value->cust_area ? $value->cust_area->area_name : ($value->area ?? 'N/A')); ?></td>
                                    <td>
                                        <?php if($value->district): ?>
                                            <?php $district = \App\Models\District::find($value->district); ?>
                                            <?php echo e($district ? $district->district : 'N/A'); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($value->phone ?? 'N/A'); ?></td>
                                    <td><?php echo e($value->whatsapp ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if($value->status == 'Premium'): ?>
                                            <span class="badge bg-soft-warning text-warning">Premium</span>
                                        <?php elseif($value->status == 'Inactive'): ?>
                                            <span class="badge bg-soft-danger text-danger">Inactive</span>
                                        <?php else: ?>
                                            <span class="badge bg-soft-success text-success">General</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?php echo e($orderCount); ?></td>
                                    <td>
                                        <?php if($productCategories): ?>
                                            <span class="badge bg-soft-info text-info"><?php echo e(Str::limit($productCategories, 30)); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">৳<?php echo e(number_format($totalOrderValue, 2)); ?></td>
                                    <td>
                                        <?php if($lastOrder): ?>
                                            <?php echo e(date('d-m-Y', strtotime($lastOrder->created_at))); ?>

                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($value->feedback): ?>
                                            <span class="feedback-text"><?php echo e($value->feedback); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td class="text-end month-col">
                                        <?php if(isset($customerMonthly[$month])): ?>
                                            ৳<?php echo e(number_format($customerMonthly[$month]->total, 2)); ?>

                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <td>
                                        <div class="button-list" style="display:flex; gap:3px; flex-wrap:nowrap;">
                                            <?php if($value->status != 'Inactive'): ?>
                                            <form method="post" action="<?php echo e(route('customers.inactive')); ?>" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" value="<?php echo e($value->id); ?>" name="hidden_id">
                                                <button type="button" class="btn btn-xs btn-secondary waves-effect waves-light change-confirm" title="Set Inactive"><i class="fe-thumbs-down"></i></button>
                                            </form>
                                            <?php else: ?>
                                            <form method="post" action="<?php echo e(route('customers.active')); ?>" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" value="<?php echo e($value->id); ?>" name="hidden_id">
                                                <button type="button" class="btn btn-xs btn-success waves-effect waves-light change-confirm" title="Set General"><i class="fe-thumbs-up"></i></button>
                                            </form>
                                            <?php endif; ?>
                                            <a href="<?php echo e(route('customers.edit',$value->id)); ?>" class="btn btn-xs btn-primary waves-effect waves-light" title="Edit"><i class="fe-edit-1"></i></a>
                                            <a href="<?php echo e(route('customers.profile',['id'=>$value->id])); ?>" class="btn btn-xs btn-blue waves-effect waves-light" title="View Profile"><i class="fe-eye"></i></a>
                                            <form method="post" action="<?php echo e(route('customers.adminlog')); ?>" class="d-inline" target="_blank">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" value="<?php echo e($value->id); ?>" name="hidden_id">
                                                <button type="button" class="btn btn-xs btn-pink waves-effect waves-light change-confirm" title="Login as customer"><i class="fe-log-in"></i></button>
                                            </form>
                                            <form method="post" action="<?php echo e(route('customers.destroy')); ?>" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" value="<?php echo e($value->id); ?>" name="hidden_id">
                                                <button type="button" class="btn btn-xs btn-danger waves-effect waves-light delete-confirm" title="Delete"><i class="fe-trash-2"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($show_data) == 0): ?>
                                <tr>
                                    <td colspan="<?php echo e(15 + count($months)); ?>" class="text-center text-muted py-4">No customers found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="custom-paginate">
                        <?php echo e($show_data->appends(request()->query())->links('pagination::bootstrap-4')); ?>

                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
</div>

<!-- Delete Modal -->
<form action="<?php echo e(route('customers.destroy')); ?>" method="post">
    <?php echo csrf_field(); ?>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <input type="hidden" name="hidden_id" id="delete_id">
                    <h5 class="text-danger">Are you sure?</h5>
                    <p>This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<!-- third party js -->
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/datatables.net-select/js/dataTables.select.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/pdfmake/build/pdfmake.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/pdfmake/build/vfs_fonts.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/js/pages/datatables.init.js"></script>
<!-- third party js ends -->
<script>
    $(document).ready(function() {
        // Delete confirmation
        $(document).on('click', '.delete-confirm', function() {
            var id = $(this).closest('form').find('input[name="hidden_id"]').val();
            $('#delete_id').val(id);
            $('#deleteModal').modal('show');
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ecommerce/ganienterprise/resources/views/backEnd/customer/index.blade.php ENDPATH**/ ?>