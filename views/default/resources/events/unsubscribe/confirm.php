<?php

$guid = (int) elgg_extract('guid', $vars);
$code = elgg_extract('code', $vars);

if (empty($guid) || empty($code)) {
	forward(REFERER);
}

$registration = get_entity($guid);
if (!($registration instanceof EventRegistration)) {
	register_error(elgg_echo('ClassException:ClassnameNotClass', [$guid, elgg_echo('item:object:' . EventRegistration::SUBTYPE)]));
	forward(REFERER);
}

$event = $registration->getOwnerEntity();
$verify_code = event_manager_create_unsubscribe_code($registration, $event);

if ($code !== $verify_code) {
	register_error(elgg_echo('event_manager:unsubscribe_confirm:error:code'));
	forward(REFERER);
}

// set page owner
elgg_set_page_owner_guid($event->getContainerGUID());

// make breadcrumb
elgg_push_breadcrumb($event->title, $event->getURL());

// make page elements
$title_text = elgg_echo('event_manager:unsubscribe_confirm:title', [$event->title]);

$body_vars = [
	'entity' => $event,
	'registration' => $registration,
	'code' => $code,
];
$body = elgg_view_form('event_manager/event/unsubscribe_confirm', [], $body_vars);

// make page
$page_data = elgg_view_layout('content', [
	'title' => $title_text,
	'content' => $body,
	'filter' => '',
]);

// draw page
echo elgg_view_page($title_text, $page_data, 'default');
