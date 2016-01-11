<?php 

use App\Controller\AppController;
/* Simplicity default layout.
 * 
 */

?>
<!DOCTYPE html>
<html>
	<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
      <?= AppController::$simplicity_site_title.': '.$this->fetch('simplicity_page_name') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('cake.css') ?>
    <?= $this->Html->css('zurb-foundation-6/foundation.css') ?>    
    <?= $this->Html->css('zurb-foundation-6/app.css') ?>
    <?= $this->Html->css('simplicity.css') ?>
		
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
	</head>
<body>
	<div id="simplicity-wrapper">
		<nav class="top-bar" data-topbar role="navigation">
			<div class="top-bar-left">
				<div class="">
					<?php //$this->Html->image(); ?>
				</div>
				<ul class="menu">
					<li class="menu-text"><?= AppController::$simplicity_site_title; ?></li>
				</ul>
			</div>
			<div class="top-bar-right">
				<?= $this->fetch('simplicity_top_menu') ?>
			</div>
		</nav>
		<?= $this->fetch('simplicity_breadcrumbs') ?>
		
		<!--div class="callout large primary">
			<div class="row column text-center">
				<h1>Screaming out loud</h1>
			</div>
		</div-->
				
		<?= $this->Flash->render() ?>
		
		<div id="simplicity-content" class="row">
			<div class="medium-9 columns">
				<?= $this->fetch('content') ?>
			</div>
			<div class="medium-3 columns" data-sticky-container>
				<div class="sticky" data-sticky data-anchor="content">
					<?= $this->fetch('simplicity_side_menu') ?>
					
					<!-- EXAMPLES -->
					<!--h4>Overview</h4>
					<ul class="vertical menu" data-accordion-menu>
					  <li>
					    <a href="#">Item 1</a>
					    <ul class="menu vertical nested is-active">
					      <li>
					        <a href="#">Item 1A</a>
					        <ul class="menu vertical nested">
					          <li><a href="#">Item 1Ai</a></li>
					          <li><a href="#">Item 1Aii</a></li>
					          <li><a href="#">Item 1Aiii</a></li>
					        </ul>
					      </li>
					      <li><a href="#">Item 1B</a></li>
					      <li><a href="#">Item 1C</a></li>
					    </ul>
					  </li>
					  <li>
					    <a href="#">Item 2</a>
					    <ul class="menu vertical nested">
					      <li><a href="#">Item 2A</a></li>
					      <li><a href="#">Item 2B</a></li>
					    </ul>
					  </li>
					  <li><a href="#">Item 3</a></li>
					</ul-->
				</div>
			</div>	
		</div>
		    
	<!-- EXAMPLES -->
	<!-- button class="button" type="button" data-toggle="example-dropdown">Toggle Dropdown</button>
	<div class="dropdown-pane" id="example-dropdown" data-dropdown data-auto-focus="true">
	  Example form in a dropdown.
	  <form>
	    <div class="row">
	      <div class="medium-6 columns">
	        <label>Name
	          <input type="text" placeholder="Kirk, James T.">
	        </label>
	      </div>
	      <div class="medium-6 columns">
	        <label>Rank
	          <input type="text" placeholder="Captain">
	        </label>
	      </div>
	    </div>
	  </form>
	</div-->    
	<footer>
		a fancy footer
	</footer>
</div> <!-- simplicity-wrapper -->    
    
    
<?php // Zurb Foundation js really have to be at the bottom of the html file, otherwise it wont initialize correctly. ?>
<?= $this->Html->script('zurb-foundation-6/vendor/jquery.min.js') ?>
<?= $this->Html->script('zurb-foundation-6/vendor/what-input.min.js') ?>
<?= $this->Html->script('zurb-foundation-6/foundation.js') ?>
<?= $this->Html->script('zurb-foundation-6/app.js') ?>    
</body>
</html>
