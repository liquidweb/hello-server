# Hello Server by Liquid Web
Contributors: Liquid Web, liquidwebdan
Tags: server info, hostname, cluster, clustering
Requires at least: 3.0
Tested up to: 4.6.1
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This is a simple plugin that tells you what server your WordPress is on. It's super useful for when you run WordPress in a clustered environment.

## Description

Do you run your WordPress in a Cluster setup?
Need to know what server your request is served from?
Don't want to have to dig in access logs to do it?

Look no further. This is just the plugin for you.

Hello Server adds a node to the Toolbar that tells you what server your request was sent from! In the new Hello Server menu you'll find the current servers hostname and IP!

## Screenshots

1. Basic WordPress toolbar with Hello Server menu
2. Hello Server menu expanded showing server details
3. 'Close up' of just the Hello Server menu

## Frequently Asked Questions

1. So what's this do then?

    Basically this plugin will tell you the servers hostname that your WordPress
    is executing on. This means if you're in a shared environment it will report
    the shared servers hostname. If you're in a clustered environment then it 
    will report the hostname that handled the request.
2. Is this only useful for Clustered setups?

    No, but they benefit the most from it. It saves users the time and effort it
     takes to SSH into servers and tail log files. This plugin could also be 
    useful for WordPress users hosted via a Platform as a Service (PaaS).

## Installation

Automated Install:

*   Go to plugins page in WordPress admin
*   Search for 'Hello Server'
*   Click 'Add to WordPress'
*   Activate plugin

Manual Install:

*   Download zip to WordPress plugins folder.
*   Extract zip contents
*   Activate plugin
