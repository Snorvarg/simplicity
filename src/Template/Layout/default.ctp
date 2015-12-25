<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = 'CakePHP: the rapid development php framework';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('cake.css') ?>
    <?= $this->Html->css('zurb-foundation-6/foundation.css') ?>    
    <?= $this->Html->css('zurb-foundation-6/app.css') ?>
    <?= $this->Html->css('simplicity.css') ?>

		<?= $this->Html->script('zurb-foundation-6/vendor/jquery.min.js') ?>
		<?= $this->Html->script('zurb-foundation-6/vendor/what-input.min.js') ?>
		<?= $this->Html->script('zurb-foundation-6/foundation.js') ?>
		<?= $this->Html->script('zurb-foundation-6/app.js') ?>
		
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <nav class="top-bar expanded" data-topbar role="navigation">
        <ul class="title-area large-3 medium-4 columns">
            <li class="name">
                <h1><a href=""><?= $this->fetch('title') ?></a></h1>
            </li>
        </ul>
        <section class="top-bar-section">
            <ul class="right">
                <li><a target="_blank" href="http://book.cakephp.org/3.0/">Documentation</a></li>
                <li><a target="_blank" href="http://api.cakephp.org/3.0/">API</a></li>
            </ul>
        </section>
    </nav>
    <?= $this->Flash->render() ?>
    <section class="container clearfix">
        <?= $this->fetch('content') ?>
    </section>
    
    <div class="row">
    	<div class="large-12 columns">
    		<h1>Hej svej!</h1>
    	</div>
    </div>
    <div class="row">
    	<div class="large-12 columns">
    		<p>troll och bananer</p>
    		<div class="row">
    			<div class="large-4 medium-4 columns">
    				<p>Korvören</p>
    			</div>
    			<div class="large-4 medium-4 columns">
    				<p>Korvören</p>
    			</div>
    			<div class="large-4 medium-4 columns">
    				<p>Korvören</p>
    			</div>
    		</div>
    	</div>
    </div>
    <div style="margin-top: 50px;"></div>
    
    <footer>
    </footer>
</body>
</html>
