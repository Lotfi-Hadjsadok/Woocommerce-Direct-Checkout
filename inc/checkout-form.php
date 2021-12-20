<div class="rc-flex__container">
    <div class="first-content">
        <?php echo esc_attr(get_option('rc_first_content')) ?> </div>
    <div class="second-content">
        <?php echo esc_attr(get_option('rc_second_content')) ?>
    </div>
    <?php

    $inputs = array(
        'name' => array(
            'label' => __('Name', 'raysel-checkout'),
            'type' => 'text',
            'value' => '',
            'name' => 'rc_name'
        ),

        'phone' => array(
            'label' => __('Phone Number', 'raysel-checkout'),
            'type' => 'number',
            'value' => '',
            'name' => 'rc_number',
            'class' => 'rc_number'
        ),

        'state' => array(
            'label' => __('State', 'raysel-checkout'),
            'type' => 'select',
            'name' => 'rc_state'
        ),
        'city' => array(
            'label' => __('City', 'raysel-checkout'),
            'type' => 'text',
            'value' => '',
            'name' => 'rc_city'
        ),
        'address' => array(
            'label' => __('address', 'raysel-checkout'),
            'type' => 'text',
            'value' => '',
            'name' => 'rc_address'
        ),
        'quantity' => array(
            'label' => __('Quantity', 'raysel-checkout'),
            'type' => 'number',
            'value' => '',
            'name' => 'rc_quantity'
        )
    );
    $productMeta = get_post_meta(get_the_ID(), '_product_attributes');
    $productAttributes = $productMeta[0];

    $product = wc_get_product(get_the_id());
    if ($product->get_attributes()) {
        $pos = 0;
        foreach ($productAttributes as  $key => $attribute) {
            $values = explode('|', $attribute['value']);
            if (get_locale() == 'ar') {
                if ($key == 'color' || $key == 'couleur' || $key == 'couleure') {
                    $attribute['name'] = 'اللون';
                }
            }
            if (get_locale() == 'ar') {
                if ($key == 'taille' || $key == 'size' || $key == 'tail') {
                    $attribute['name'] = 'القياس';
                }
            }
            $inputs = array_merge(array_slice($inputs, 0, 2 + $pos), array(
                __($key, 'raysel-checkout')  => array(
                    'label' => __($attribute['name'], 'raysel-checkout'),
                    'type' => 'select',
                    'value' => $values,
                    'name' => 'rc_' . $attribute['name']
                )
            ), array_slice($inputs, 2));
            $pos++;
        }
    }





    ?>

    <form method="post">
        <input type="hidden" name="rc_product_id" value="<?php echo esc_html(get_the_ID()); ?>">
        <?php

        foreach ($inputs as $key => $input) {
            if ($key == 'quantity') {
        ?>
                <label for="<?php echo $input['name'] ?>"><?php echo $input['label'] ?></label>
                <input class="<?php if ($input['class']) echo $input['class']; ?>" value="1" min="1" max="50" onpaste="return false;" id="<?php echo $input['name'] ?>" name="<?php echo $input['name'] ?>" type="<?php echo $input['type'] ?>">
            <?php
            }



            if ($input['type'] != 'select' && $key != 'quantity') {
            ?>
                <label for="<?php echo $input['name'] ?>"><?php echo $input['label'] ?></label>
                <input required class="<?php if ($input['class']) echo $input['class']; ?>" onpaste="return false;" id="<?php echo $input['name'] ?>" name="<?php echo $input['name'] ?>" type="<?php echo $input['type'] ?>">
            <?php
            }
            if ($key == 'state') {
            ?>
                <label for="<?php echo $input['name'] ?>"><?php echo $input['label'] ?></label>
                <select required name="<?php echo $input['name'] ?>" id="<?php echo $input['name'] ?>">
                    <?php require_once(plugin_dir_path(__FILE__) . '/states.php') ?>
                </select>
            <?php
            }
            if ($key != 'state' && $input['type'] == 'select') {
            ?>
                <label for="<?php echo $input['name'] ?>"><?php echo $input['label'] ?></label>
                <select name="<?php echo $input['name'] ?>" id="<?php echo $input['name'] ?>">
                    <?php
                    foreach ($input['value'] as $value) {
                    ?>
                        <option value="<?php echo esc_html($value) ?>"><?php echo esc_html($value) ?></option>
                    <?php
                    }
                    ?>
                </select>
        <?php
            }
        }
        ?>

        <input class="rc_submit" type="submit" name="rc_order" value="<?php echo esc_attr(get_option('rc_submit_btn')) ?>">
    </form>
</div>

<style>
    :root {
        --rc-primary-color: <?php echo esc_attr(get_option('rc_first_color')); ?>;
        --rc-secondary-color: <?php echo esc_attr(get_option('rc_second_color')); ?>;
    }
</style>