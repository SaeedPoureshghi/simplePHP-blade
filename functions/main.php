<?php


function Index() {
    page::renderBlade("index");
}

function signoutGet() {
  session_destroy();
  header('Location: /');
}

function AboutGet() {
  page::renderBlade('about');
}

function ContactGet() {
  page::renderBlade('contact');
} 