<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('get index page');
$I->amOnPage("/");
$I->see(" Akademiano Default");
