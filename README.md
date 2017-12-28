# CakePHP Application Skeleton

[![Build Status](https://api.travis-ci.org/cakephp/app.png)](https://travis-ci.org/cakephp/app)
[![License](https://poser.pugx.org/cakephp/app/license.svg)](https://packagist.org/packages/cakephp/app)

A skeleton for creating applications with [CakePHP](http://cakephp.org) 3.x.

The framework source code can be found here: [cakephp/cakephp](https://github.com/cakephp/cakephp).

## Installation

Install cakephp 3, then unpack the files from this project.

## Configuration

Read and edit `config/app.php` and setup the 'Datasources' and any other
configuration relevant for your application.

# simplicity
Simplicity - Editable web-pages with tinymce rich-text editor.

Simplicity consist of one Controller, a Helper and a Model, which must be copied into their respective location in your existing Cake-installation.

Once inserted, you should be able to create new pages on the fly, like this: 

yourfancyurl.com/pages/inventanamehere

Visiting that page will create an empty page for you, with an edit button. 

Simple? Yep. :)

CREATE CUSTOM VIEW FILES

Since Simplicity is based on the genially simple PagesController, you can add your own .ctp view files for any page you have created. 
Just name it 'inventanamehere.ctp' and put it in the Template/EditablePages folder.

I assume you are a bit into using Cake, so I leave you with this information, and urge you to contact me if you have any questions or ideas for improvements!
