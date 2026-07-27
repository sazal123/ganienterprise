
<?php $__env->startSection('title'); ?>
<title>Customer Register</title>
<?php $__env->startSection('content'); ?>
<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-5">
                <div class="form-content">
                    <p class="auth-title"> কাস্টমার রেজিস্ট্রেশন </p>
                    <form action="<?php echo e(route('customer.store')); ?>" method="POST"  data-parsley-validate="">
                        <?php echo csrf_field(); ?>
                        <div class="form-group mb-3">
                            <label for="name">নাম</label>
                            <input type="text" id="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="name" value="<?php echo e(old('name')); ?>" placeholder=" আপনার নাম " required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <!-- col-end -->
                        <div class="form-group mb-3">
                            <label for="phone"> মোবাইল নাম্বার </label>
                            <input type="number" id="phone" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="phone" value="<?php echo e(old('phone')); ?>" placeholder="মোবাইল নাম্বার" required>
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <!-- col-end -->
                        <!--<div class="form-group mb-3">-->
                        <!--    <label for="email"> ইমেইল </label>-->
                        <!--    <input type="email" id="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e(old('email')); ?>" placeholder="ইমেইল">-->
                        <!--    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>-->
                        <!--        <span class="invalid-feedback" role="alert">-->
                        <!--            <strong><?php echo e($message); ?></strong>-->
                        <!--        </span>-->
                        <!--    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>-->
                        <!--</div>-->
                        <!-- col-end -->
                        <div class="form-group mb-3">
                            <label for="password"> পাসওয়ার্ড </label>
                            <input type="password" id="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="পাসওয়ার্ড " name="password" value="<?php echo e(old('password')); ?>" required>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <!-- col-end -->
                        <button class="submit-btn">রেজিস্ট্রেশন</button>
                         <div class="register-now no-account">
                        <p><i class="fa-solid fa-user"></i> রেজিস্ট্রেশন করা থাকলে?</p>
                        <a href="<?php echo e(route('customer.login')); ?>"><i data-feather="edit-3"></i> লগিন করুন </a>
                    </div>
                        </div>
                     <!-- col-end -->
                     
                    
                     </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('script'); ?>
<script src="<?php echo e(asset('frontEnd/')); ?>/js/parsley.min.js"></script>
<script src="<?php echo e(asset('frontEnd/')); ?>/js/form-validation.init.js"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('frontEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/smgiyasuddin/ganienterprise/resources/views/frontEnd/layouts/customer/register.blade.php ENDPATH**/ ?>