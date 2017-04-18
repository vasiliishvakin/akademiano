<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('get index page');
$I->amOnPage("/");
$I->seeResponseCodeIs(200);
$I->see("Akademiano Default All Site Template", "h1");
