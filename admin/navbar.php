
<style>
</style>
<nav id="sidebar" class='mx-lt-5 bg-dark' >
		
		<div class="sidebar-list">

				<a href="index.php?page=home" class="nav-item nav-home"><span class='icon-field'><i style="color:orange;"class="fa fa-home"></i></span> Home</a>
				<a href="index.php?page=appointments" class="nav-item nav-appointments"><span class='icon-field'><i style="color:orange;"class="fa fa-calendar"></i></span> Appointments</a>
				<a href="index.php?page=priest" class="nav-item nav-doctors"><span class='icon-field'><i style="color:orange;"class="fa fa-id-card"></i></span> Priest List</a>
				<a href="index.php?page=categories" class="nav-item nav-categories"><span class='icon-field'><i style="color:orange;"class="fa fa-book-medical"></i></span> Church & Events</a>			
				<?php if($_SESSION['login_type'] == 1): ?>
				<a href="index.php?page=users" class="nav-item nav-users"><span class='icon-field'><i style="color:orange;"class="fa fa-users"></i></span> Users</a>
				<a href="index.php?page=site_settings" class="nav-item nav-site_settings"><span class='icon-field'><i style="color:orange;"class="fa fa-cog"></i></span> Site Settings</a>
				<a href="https://mail.google.com/mail/u/0/#inbox?compose=new" class="nav-item nav-site_settings"><span class='icon-field'><i style="color:orange;"class="fa fa-envelope"></i></span> Gmail</a>
			<?php endif; ?>
		</div>

</nav>
<script>
	$('.nav-<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>').addClass('active')
</script>
<?php if($_SESSION['login_type'] == 2): ?>
	<style>
		.nav-sales ,.nav-users,.nav-doctors,.nav-categories{
			display: none!important;
		}
	</style>
<?php endif ?>