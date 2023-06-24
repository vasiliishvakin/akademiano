<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Test index page');
$I->amOnPage("/");
$I->seeResponseCodeIs(200);
$I->see("Akademiano Default Site Template", "h1");
$I->validateMarkup();
