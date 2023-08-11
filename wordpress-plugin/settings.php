<?php

global $uipro_options, $UIPRO_msg;

if(isset($_POST['action'])){
    
    UIPRO_update_options();
    
    UIPRO_get_options();
    
}

?>

<form method="POST" id="uiprosettings">
    
    <input type="hidden" name="action" value="update" />
    
    <h1 style="margin-bottom: 30px;">Ui-Pro Settings</h1>
    
    <?php
    
    if($UIPRO_msg){
        
        echo '<span class="uipro_alert">' . $UIPRO_msg . '</span>';
        
    }
    
    ?>
    
    <label>Left menu
        <select name="left">
            <option value="off"<?php if($uipro_options['left'] == 'off'){echo ' SELECTED';}?>>Off</option>
            <option value="on"<?php if($uipro_options['left'] == 'on'){echo ' SELECTED';}?>>On</option>
        </select>
    </label>
    <br><br>
    
    <label>Right menu
        <select name="right">
            <option value="off"<?php if($uipro_options['right'] == 'off'){echo ' SELECTED';}?>>Off</option>
            <option value="on"<?php if($uipro_options['right'] == 'on'){echo ' SELECTED';}?>>On</option>
        </select>
    </label>
    <br><br>
    
    <label>Threshold
        <input type="text" value="<?php echo $uipro_options['threshold'];?>" name="threshold" />
    </label><br>
    <span style="font-size: 11px; color: #999;">The threshold of the area on the left/right side where the menu gets activated. Positive number in pixels. Example: 100</span>
    
    <br><br>
    <button type="submit">Save</button>
    <!--<div class="">
        
    </div>-->
    
</form>