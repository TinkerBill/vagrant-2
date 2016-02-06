<?php

// Experimental devlog.php for wf_widgets - a single place where all the site changes are logged.
// ...means we don't have to go searching for latest version number

/*
v6.80	3/10/15	wf_widgets.php: function get_catposts($args,$params) moved from a function to a method in abstract class Wf_Widget because 
				(a) it's used by both List and Vscroller widgets and (b) overloading a function (as opposed to a method) doesn't 
				allow as much control over what gets overwritten. (Eg: extending Lists widget to ToDo widget causes all lists to 
				have ToDo junk added!

*/
