<div class="page">

	<?php 
		$this->load->view( CPATH . 'navbar', array( 'user' => $user ) ); 
	?>

	<div class="page-content d-flex align-items-stretch"> 
		<!-- Side Navbar -->
		<nav class="side-navbar">
			<?php
				$this->load->view( CPATH . 'sidebar' ) 
			?>
		</nav>

		<div class="content-inner">
			<!-- Page Header-->
			<header class="page-header">
				<div class="container-fluid">
					<h2 class="no-margin-bottom">Especialidades ></h2>
				</div>
			</header>

			<?php
				$this->load->view( SPATH . 'bs_speciality_add_content', array( 'building_sites' => $building_sites ) );
			?>
		
			<?php 
				$this->load->view( CPATH . 'footer' ) 
			?>

			</div>
		</div>
		
	</div>

</div>