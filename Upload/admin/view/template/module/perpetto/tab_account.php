<hr />
<div class="row">
        <div class="col-xs-4 col-sm-2">
            <h4><strong>Account ID and Secret</strong></h4>
        </div>
        <div class="col-xs-8 col-sm-4">
            <span style="font-size: 13px;">Insert the Account ID and Secret of your Perpetto account to get started.  
You can find them in the Store section after you sign in.</span>
        </div>
</div>
<br />
<div class="row">
        <div class="col-xs-4 col-sm-2">
            <h5>Account ID</h5>
        </div>
        <div class="col-xs-8 col-sm-4">
            <input name="<?php echo $moduleNameSmall; ?>[account_id]" class="form-control" type="text" value="<?php echo (!empty($moduleData['account_id'])) ? $moduleData['account_id'] : '' ?>" />
        </div>
</div>
<br />
<div class="row">
        <div class="col-xs-4 col-sm-2">
            <h5>Secret</h5>
        </div>
        <div class="col-xs-8 col-sm-4">
            <input name="<?php echo $moduleNameSmall; ?>[secret]" class="form-control" type="text" value="<?php echo (!empty($moduleData['secret'])) ? $moduleData['secret'] : '' ?>" />
        </div>
</div>