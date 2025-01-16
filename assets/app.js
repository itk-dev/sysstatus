/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

import jquery from 'jquery';
const $ = jquery;
window.$ = $;

import once from 'jquery-once';

import "select2/dist/css/select2.min.css";
import select2 from 'select2';
select2($);
