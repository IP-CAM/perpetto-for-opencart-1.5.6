<?php if(!empty($slots)) { ?>
<?php foreach($slots as $slot) { ?>
<div class="ptto-rec-slot-token" data-ptto-token="<?php if(!empty($slot['token'])) echo $slot['token']; else echo ''; ?>"></div>
<?php } ?>
<?php } ?>