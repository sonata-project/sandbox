// Globalize jquery
import jQuery from 'jquery';
global.$ = jQuery;
global.jQuery = jQuery;

// Styles
import '../scss/app.scss'

// Vendors
import 'adminlte'
import "bootstrap"

import "jquery-form"
import "jquery-ui"
import "jquery.scrollto"
import "jquery-slimscroll"

import "x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min"

import "icheck"
import "waypoints/lib/jquery.waypoints"
import "waypoints/lib/shortcuts/sticky.min"
import "select2"

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

// Custom
import "./Admin"
import "./sidebar"
import "./jquery.confirmExit"
import "./treeview"