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
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form"> 
                <input type="hidden" name="store_id" value="<?php echo $store['store_id']; ?>" />
                <input type="hidden" name="perpetto_status" value="1" />
                <div class="tabbable">
                    <div class="tab-navigation form-inline">
                        <ul class="nav nav-tabs mainMenuTabs" id="mainTabs">
                            <li><a href="#recommendations" data-toggle="tab">Recommendations</a></li>
                            <li id="mainSettingsTab"><a href="#account" data-toggle="tab">Account</a></li>
                        </ul>
                        <div class="tab-buttons">
                            <button type="submit" class="btn btn-success save-changes"><i class="fa fa-check"></i>&nbsp;<?php echo $save_changes?></button>
                            <a onclick="location = '<?php echo $cancel; ?>'" class="btn btn-warning"><i class="fa fa-times"></i>&nbsp;<?php echo $button_cancel?></a>
                        </div> 
                    </div><!-- /.tab-navigation --> 
                    <div class="tab-content">
                    <?php
                    if (!function_exists('modification_vqmod')) {
                    	function modification_vqmod($file) {
                    		if (class_exists('VQMod')) {
                   				return VQMod::modCheck(modification($file), $file);
                    		} else {
                    			return modification($file);
                   			}
                    	}
                    }
					?>
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
                        <div id="recommendations" class="tab-pane fade"><?php require_once (DIR_APPLICATION.'view/template/module/perpetto/tab_recommendations.php'); ?></div>
                        <div id="account" class="tab-pane fade"><?php require_once (DIR_APPLICATION.'view/template/module/perpetto/tab_account.php'); ?></div>
                    </div> <!-- /.tab-content --> 
                </div><!-- /.tabbable -->
            </form>
        </div> 
    </div>
</div>
<?php echo $footer; ?>
<script type="text/javascript">
	$('#mainTabs a:first').tab('show'); // Select first tab
	if (window.localStorage && window.localStorage['currentTab']) {
		$('.mainMenuTabs a[href="'+window.localStorage['currentTab']+'"]').tab('show');
	}
	if (window.localStorage && window.localStorage['currentSubTab']) {
		$('a[href="'+window.localStorage['currentSubTab']+'"]').tab('show');
	}
	$('.fadeInOnLoad').css('visibility','visible');
	$('.mainMenuTabs a[data-toggle="tab"]').click(function() {
		if (window.localStorage) {
			window.localStorage['currentTab'] = $(this).attr('href');
		}
	});
	$('a[data-toggle="tab"]:not(.mainMenuTabs a[data-toggle="tab"], .review_tabs a[data-toggle="tab"])').click(function() {
		if (window.localStorage) {
			window.localStorage['currentSubTab'] = $(this).attr('href');
		}
	});
</script>
<script>
    $('.perpetto-header .fa.fa-times').on('click', function() {
        $(this).parent().slideUp(300);
    });
</script>