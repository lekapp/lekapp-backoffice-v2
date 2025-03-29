<header class="header">
	<nav class="navbar">
		<!-- Search Box-->
		<div class="search-box">
			<button class="dismiss"><i class="icon-close"></i></button>
			<form id="searchForm" action="#" role="search">
				<input type="search" placeholder="What are you looking for..." class="form-control">
			</form>
		</div>
		<div class="container-fluid">
			<div class="navbar-holder d-flex align-items-center justify-content-between">
				<!-- Navbar Header-->
				<div class="navbar-header">
					<a href="<?= base_url() ?>" class="navbar-brand">
						<div class="brand-text brand-big">
							<span><?= SYSNAME ?></span>
						</div>
					</a>
					<a id="toggle-btn" href="#" class="menu-btn active">
						<span></span>
						<span></span>
						<span></span>
					</a>
				</div>
				<!-- Navbar Menu -->
				<ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
					<li class="nav-item"><a href="<?= base_url('login') ?>" class="nav-link logout">Cerrar Sesión <i class="fa fa-sign-out"></i></a></li>
				</ul>
			</div>
		</div>
	</nav>
</header>