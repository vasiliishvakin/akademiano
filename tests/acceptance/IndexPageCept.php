<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('get index page');
$I->amOnPage("/");
$I->seeResponseCodeIs(404);
//$I->see("Akademiano Default All Site Template", "h1");
