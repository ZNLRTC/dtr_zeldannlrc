<?php
/*
 * Page Name: Universal
 * Author: Jushua FF
 * Date: 01.17.2023
 */
$account_session = $this->session->userdata('account_session');
?>

<form class="d-none">
    <?php
        $timezone = new DateTimeZone('Asia/Manila');
        $now = new DateTime('now', $timezone);
    ?>
    <input type="hidden" id="current-date" value="<?= $now->format('F d, Y') ?>">
    <input type="hidden" id="current-time" value="<?= $now->format('H:i:s') ?>">
</form>

<div class="w-100 head-row">

	<button class="sidebar-toggle" title="Hide sidebar">
		<i class="fas fa-angle-left"></i>
	</button>

	<div class="ml-auto d-flex align-items-center">
		<div class="dropdown">
		  <button class="fas fa-bell text-white notification-icon pointer dropdown-toggle" id="account-setting" title="account setting" data-bs-toggle="dropdown"><span class="notification-count">3</span></button>
		  <ul class="dropdown-menu" aria-labelledby="account-setting">
		    <li class="dropdown-item"><small class="lh-12px">The 'DTR Request Update' button has been removed.</small></li>
		    <li class="dropdown-item"><small class="lh-12px">The break time is set to a minimum of 30 minutes.</small></li>
		    <li class="dropdown-item"><small class="lh-12px">Only a maximum of 15 minutes of lateness can be compensated.</small></li>
		    <li class="dropdown-item pointer text-center coming-soon" title="Coming Soon!"><a href="#"><small class="lh-12px">View All Updates</small></a></li>
		  </ul>
		</div>

		<div class="dropdown px-1">
		  <button class="dropdown-toggle" id="account-setting" title="account setting" data-bs-toggle="dropdown"><i class="fas fa-cog"></i></button>
		  <ul class="dropdown-menu" aria-labelledby="account-setting">
		    <li><a class="edit-profile dropdown-item own-profile"  href="<?= base_url('employee') ?>/view_profile?id=<?= $account_session['id'] ?>" user-type="n/a">Profile</a></li>
		    <li><button class="update-password dropdown-item own-password" user-id="<?= $account_session['id'] ?>" data-toggle="modal" data-target="#update-password-modal">Password</button></li>
		  </ul>
		</div>
		<a href="#log-out-modal" data-target="#log-out-modal" data-toggle="modal"><i title="Logout" class="fas fa-power-off text-white pointer"></i></a>
	</div>
</div>