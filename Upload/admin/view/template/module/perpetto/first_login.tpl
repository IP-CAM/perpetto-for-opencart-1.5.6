<?php echo $header; ?>
<div id="content">
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i>&nbsp;<span style="vertical-align:middle;font-weight:bold;">Perpetto for OpenCart</span></h3>
            <div class="storeSwitcherWidget">
                <div class="form-group">
                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><?php echo $store['name']; if($store['store_id'] == 0) echo " <strong>(".$text_default.")</strong>"; ?>&nbsp;<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
                    <ul class="dropdown-menu" role="menu">
                        <?php foreach ($stores  as $st) { ?>
                            <li><a href="index.php?route=module/perpetto&store_id=<?php echo $st['store_id'];?>&token=<?php echo $token; ?>"><?php echo $st['name']; ?></a></li>
                        <?php } ?> 
                    </ul>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <form action="<?php echo $login_action; ?>" method="post" enctype="multipart/form-data" id="perpetto-login" class="col-xs-12"> 
                <input type="hidden" name="store_id" value="<?php echo $store['store_id']; ?>" />
                <input type="hidden" name="perpetto_status" value="1" />
                <div class="tab-navigation form-inline" style="margin-bottom: 50px;">
                    <div class="tab-buttons">
                        <button type="submit" class="btn btn-success save-changes"><i class="fa fa-check"></i>&nbsp;<?php echo $save_changes?></button>
                        <a onclick="location = '<?php echo $cancel; ?>'" class="btn btn-warning"><i class="fa fa-times"></i>&nbsp;<?php echo $button_cancel?></a>
                    </div> 
                </div><!-- /.tab-navigation --> 
                <div class="row" style="margin-right: -30px;">
                    <div class="perpetto-header col-xs-12">
                        <div class="logo col-xs-5 col-sm-2">
                            <img class="img-responsive" src="view/image/perpetto/perpetto_logo.png" />
                        </div>
                        <div class="notifications col-xs-7 col-sm-10">
                            <?php if ($error_warning) { ?>
                                <div class="alert alert-warning">
                                    <span class="fa-stack fa-lg">
                                      <i class="fa fa-circle fa-stack-2x"></i>
                                      <i class="fa fa-question fa-stack-1x fa-inverse"></i>
                                    </span>
                                    <span><?php echo $error_warning; ?></span>
                                </div>
                            <?php } ?>
                            <?php if ($success) { ?>
                                <div class="alert alert-success">
                                    <span class="fa-stack fa-lg">
                                      <i class="fa fa-circle fa-stack-2x"></i>
                                      <i class="fa fa-check fa-stack-1x fa-inverse"></i>
                                    </span>
                                    <span><?php echo $success; ?></span>
                                    <i class="fa fa-times"></i>
                                </div>
                            <?php } ?>
                            
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="perpetto-body">
                        <div class="col-xs-12 col-sm-6">
                            <div class="sign-in">
                                <h3>Get your Account ID and Secret</h3>
                                <span>Sign in here and go to the Store section to copy your Account ID and Secret.</span>
                                <a href="https://admin.perpetto.com/#/sign_in" target="_blank" class="btn btn-default">Sign In</a>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="sign-up">
                                <h3>Don't have a Perpetto account?</h3>
                                <span>Sign up free here to get started, it only takes a few seconds.</span>
                                <a href="https://admin.perpetto.com/#/sign_up/start/opencart_ocplugin" target="_blank" class="btn btn-default">Sign Up Free Here</a>
                            </div>
                        </div>
                    </div>
                </div>
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
                                <input name="<?php echo $moduleNameSmall; ?>[account_id]" class="form-control" type="text" />
                            </div>
                    </div>
                    <br />
                    <div class="row">
                            <div class="col-xs-4 col-sm-2">
                                <h5>Secret</h5>
                            </div>
                            <div class="col-xs-8 col-sm-4">
                                <input name="<?php echo $moduleNameSmall; ?>[secret]" class="form-control" type="text" />
                            </div>
                    </div>
            </form>
            <hr />
        </div> 
    </div>
</div>
<script>
    $('.perpetto-header .fa.fa-times').on('click', function() {
        $(this).parent().slideUp(300);
    });
</script>
<?php echo $footer; ?>
