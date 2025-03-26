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
					<h2 class="no-margin-bottom">Reportes diarios ></h2>
				</div>
			</header>
			<?php
				$this->load->view( SPATH . 'building_site_report_list_content',  array( 'data' => $data ) );
			?>
		
			<?php 
				$this->load->view( CPATH . 'footer' ) 
			?>
			</div>
		</div>
		
	</div>
</div>