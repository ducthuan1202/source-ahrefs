<?php
// Gâu Gâu
$data = get_ahrefs_export($user_id);
$ahrefs_export = $data['ahrefs_export'];
if($ahrefs_export == 0)
	unset($_SESSION['user_credits']['ahrefs_export']);
// End Gâu Gâu