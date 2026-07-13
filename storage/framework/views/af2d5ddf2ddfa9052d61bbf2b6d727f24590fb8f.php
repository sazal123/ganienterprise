<div class="customer-auth">
    <div class="customer-img">
        <img src="<?php echo e(asset(Auth::guard('customer')->user()->image)); ?>" alt="">
    </div>
    <div class="customer-name">
        <p><small>Hello</small></p>
        <p><?php echo e(Auth::guard('customer')->user()->name); ?></p>
    </div>
</div>
<div class="sidebar-menu">
    <ul>
        <li><a href="<?php echo e(route('customer.account')); ?>" class="<?php echo e(request()->is('customer/account')?'active':''); ?>"><i data-feather="user"></i> My Account</a></li>
        <li><a href="<?php echo e(route('customer.orders')); ?>" class="<?php echo e(request()->is('customer/orders')?'active':''); ?>"><i data-feather="database"></i> My Order</a></li>
        <li><a href="<?php echo e(route('customer.profile_edit')); ?>" class="<?php echo e(request()->is('customer/profile-edit')?'active':''); ?>"><i data-feather="edit"></i> Profile Edit</a></li>
        <li><a href="<?php echo e(route('customer.change_pass')); ?>" class="<?php echo e(request()->is('customer/change-password')?'active':''); ?>"><i data-feather="lock"></i> Change Password</a></li>
        <li><a href="<?php echo e(route('customer.logout')); ?>"
            onclick="event.preventDefault();
            document.getElementById('logout-form').submit();"><i data-feather="log-out"></i> Logout</a></li>
        <form id="logout-form" action="<?php echo e(route('customer.logout')); ?>" method="POST" style="display: none;">
            <?php echo csrf_field(); ?>
        </form>
    </ul>
</div><?php /**PATH /Users/smgiyasuddin/ecommerce/ganienterprise/resources/views/frontEnd/layouts/customer/sidebar.blade.php ENDPATH**/ ?>