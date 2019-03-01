<?php
class Index extends Page {
	protected function onload(){
		$this -> view("home/page");
	}
}