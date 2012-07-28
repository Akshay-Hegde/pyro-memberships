<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * This module allows for the base management of sports teams for a club. The
 * structure is sufficiently generic to allow any sport. It also adds a similar
 * base support for league identification more as a thoughtful design than a
 * smart feature (makes keeping teams in the same leagues easier).
 * I highly recommend you fetch memberships as part of your addon installation.
 * It will allow you to have team rosters with anything from basic players and
 * coaches to whatever your heart desires (I'm having cake bakers).
 * @author Johannes L. Borresen
 * @website http://the.homestead.dk
 * @package teams
 **/

$lang['teams:teams']		=	'Teams';
$lang['teams:team']			=	'Team';
$lang['teams:edit']			=	'Edit team';
$lang['teams:create']		=	'Add team';
$lang['teams:delete']		=	'Delete team';
$lang['teams:id']			=	'Id';
$lang['teams:name']			=	'Name';
$lang['teams:slug']			=	'Slug';
$lang['teams:description']	=	'Description';

$lang['teams:create_not_allowed'] = 'You don\' have the permmissions to create a team.';
$lang['teams:delete_not_allowed'] = 'You don\' have the permmissions to create a team.';
$lang['teams:edit_not_allowed'] = 'You don\' have the permmissions to create a team.';
