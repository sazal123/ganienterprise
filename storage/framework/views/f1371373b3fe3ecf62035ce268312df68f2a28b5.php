<?php
    $subtotal = Cart::instance('pos_shopping')->subtotal();
    $subtotal = str_replace(',','',$subtotal);
    $subtotal = str_replace('.00', '',$subtotal);
    $shipping = Session::get('pos_shipping');
    $total_discount = Session::get('pos_discount')+Session::get('product_discount');
?>
<tr>
    <td>Sub Total</td>
    <td><?php echo e($subtotal); ?></td>
</tr>
<tr>
    <td>Shipping Fee</td>
    <td><?php echo e($shipping); ?></td>
</tr>
<tr>
    <td>Discount</td>
    <td><?php echo e($total_discount); ?></td>
</tr>
<tr>
    <td>Total</td>
    <td><?php echo e(($subtotal + $shipping)- $total_discount); ?></td>
</tr><?php /**PATH /Users/smgiyasuddin/ecommerce/ganienterprise/resources/views/backEnd/order/cart_details.blade.php ENDPATH**/ ?>