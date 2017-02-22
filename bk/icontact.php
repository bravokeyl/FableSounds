<?php

// Test (sandbox) data:
// --> API URLs for this app start with this prefix:
// https://app.sandbox.icontact.com/icp/a/415506/c/126934/

// After a user registers, we add him/her to iContact by calling add_user_to_icontact.
// When they finish registering their product, we add them to the appropriate list,
// by calling add_user_to_list. The decision on which list to add the user to is based on the
// registration DB. From the products table we get the 'icontact_list_id' column, which contains the
// numeric ID of the list for the product in question, and then call the function
// add_user_to_list() and pass in the user ID and list ID.
// For initial testing, see below for the IDs for each product's list, which
// I got by calling GET on the /lists URL (same as method dump_all_lists()) using the
// Postman application.

// Each of the functions here ends up calling callResource(), which sets up all the
// HTTP headers for the request authentication.



$GLOBALS['config'] = array(
 'apiUrl'   => 'https://app.sandbox.icontact.com/icp',
 'username' => 'fablesounds-wc',
 'password' => 't1K1beta',
 'appId'    => 's19UXbn8Sfc4AmTCOzcikj3NKcTiOhn7',
 'accountId' => 415511,
 'clientFolderId' => 126939,
);

$GLOBALS['icontact_lists'] = array(
	'BKFDR' => 268236,
	'BLDR' => 268237,
	'BGDR' => 268238,
);



function add_user_to_icontact($email, $firstName, $lastName, $userName) {
  $bk_wclogger = new WC_Logger();
  $bk_wclogger->add('info','adding user to icontact...');
  $bk_wclogger->add('info','email = '.$email.', user_id = '.$userName.', firstName = '.$firstName.', lastName = '.$lastName);
	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];
  // wp_die($userName);
	$response = callResource("/a/{$acc}/c/{$clf}/contacts",
		'POST', array(
			array(
				'firstName' => $firstName,
				'lastName'  => $lastName,
				'email'     => $email,
				'fablesoundsuserid' => '',
        'fableusername' => $userName
			)
		));

  return $response;
}

function mark_as_new_lites_user($fableUserId, $dump_response) {
	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];

	$contactId = get_contact_id($fableUserId);
	if ($contactId > 0)
	{

		$response = callResource("/a/{$acc}/c/{$clf}/contacts/{$contactId}",
			'POST', array(
				array(
					'contactId' => $contactId,
					'city' => 'honolulu'
					//'newlitesuser' => 1
				)
			));
		if ($dump_response == true)
		{
			dump($response);
		}

	}

}

function dump_all_lists() {
	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];

	$response = callResource("/a/{$acc}/c/{$clf}/lists", 'GET');
	dump($response);
}

function dump_list($listName) {
	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];

	$response = callResource("/a/{$acc}/c/{$clf}/lists?name={$listName}", 'GET');
	dump($response);
}

function dump_contact($fableSoundsUserId) {
	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];

	$response = callResource("/a/{$acc}/c/{$clf}/contacts?fablesoundsuserid={$fableSoundsUserId}", 'GET');
	dump($response);
}

function dump_all_contacts() {
	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];

	$response = callResource("/a/{$acc}/c/{$clf}/contacts", 'GET');
	dump($response);
}
/*
function add_to_list($contactId, $listId)
{
	$response = callResource("/a/{$acc}/c/{$clf}/subscriptions",
		'POST', array(
			array(
				'contactId' => $contactId,
				'listId'  => $listId,
				'status' => 'normal'
			)
		));
	dump($response);
}
*/

function add_user_to_list($contactId, $listId) {
	error_log('adding user '.$userName.' to mailing list: '.$listId);

	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];
		if ($listId > 0){
			error_log('listId = '.$listId);
			$response = callResource("/a/{$acc}/c/{$clf}/subscriptions",
				'POST', array(
					array(
						'contactId' => $contactId,
						'listId'  => $listId,
						'status' => 'normal'
					)
				));

      // wp_die(print_r($response));
		}
}

function delete_user($userId, $dump_response) {
	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];

	$contactId = get_contact_id($userId);
	if ($contactId > 0)
	{
			$response = callResource("/a/{$acc}/c/{$clf}/contacts/{$contactId}",
				'DELETE', array(
					array(
						'contactId' => $contactId
					)
				));

			if ($dump_response == true)
			{
				dump($response);
			}

	}

}

function create_segment($userId, $contactId, $listId, $dump_response) {
	error_log("Creating segment for user {$userId}, in listId {$listId}");

	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];

	if ($contactId > 0)
	{
			$response = callResource("/a/{$acc}/c/{$clf}/segments",
				'POST', array(
					array(
						'name' => 'new user - '.$userId,
						'listId'  => $listId,
					)
				));
			if ($dump_response == true)
			{
				dump($response);
			}
			if ($response['code'] == STATUS_CODE_SUCCESS)
			{
				$segmentId = $response['data']['segments'][0]['segmentId'];
			}

			$response = callResource("/a/{$acc}/c/{$clf}/segments/{$segmentId}/criteria",
				'POST', array(
					array(
						'fieldName' => 'fablesoundsuserid',
						'operator'  => 'eq',
						'values'    => array(
							'0' => $userId
						)
					)
				));
			if ($dump_response == true)
			{
				dump($response);
			}
	}
}

function send_message($messageId, $listId, $segmentId, $debug) {
	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];

	$response = callResource("/a/{$acc}/c/{$clf}/sends",
		'POST', array(
			array(
				'messageId' => $messageId,
				'includeSegmentIds' => $segmentId,
			)
		));
	if ($debug)
	{
		dump($response);
	}
}

function create_message($campaign, $subject, $htmlBody, $textBody, $debug) {
	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];

	$response = callResource("/a/{$acc}/c/{$clf}/messages",
		'POST', array(
			array(
				'campaignId' => $campaign,
				'subject' => $subject,
				'messageType' => 'normal',
				'htmlBody' => $htmlBody,
				'textBody' => $textBody
			)
		));
	if ($debug)
	{
		dump($response);
	}
}

function reuse_message($messageId, $debug) {
	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];

	$response = callResource("/a/{$acc}/c/{$clf}/messages/{$messageId}", 'GET');
	if ($debug)
	{
		dump($response);
	}
}

function dump_segment_criteria($segmentId) {
	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];

	$response = callResource("/a/{$acc}/c/{$clf}/segments/{$segmentId}/criteria",	'GET');
	dump($response);
}

function update_criteria($segmentId, $fableUserId, $dump_response) {
	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];

	$response = callResource("/a/{$acc}/c/{$clf}/segments/{$segmentId}/criteria/1",
		'POST', array(
			array(
				'fieldName' => 'fablesoundsuserid',
				'operator' => 'eq',
				'values' => array (
					'0' => $fableUserId
				)
			)
		));

	if ($dump_response == true)
	{
		dump($response);
	}
}

function dump_all_segments() {
	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];

	$response = callResource("/a/{$acc}/c/{$clf}/segments",	'GET');
	dump($response);
}

function dump_all_messages() {
	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];

	//$response = callResource("/a/{$acc}/c/{$clf}/messages/?fields=messageId,campaignId,subject,messageType,createDate",	'GET');
	$response = callResource("/a/{$acc}/c/{$clf}/messages/?messageType=autoresponder",	'GET');
	dump($response);
}

function get_list_id($listName) {
	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];

	$listId = 0;
	$response = callResource("/a/{$acc}/c/{$clf}/lists?name={$listName}", 'GET');
	if ($response['code'] == STATUS_CODE_SUCCESS)
	{
		$listId = $response['data']['lists'][0]['listId'];
	}
	return $listId;
}


function get_contact_id($userId) {
	$acc = $GLOBALS['config']['accountId'];
	$clf = $GLOBALS['config']['clientFolderId'];

	$contactId = 0;
	$response = callResource("/a/{$acc}/c/{$clf}/contacts?fableusername={$userId}", 'GET');
	if ($response['code'] == STATUS_CODE_SUCCESS)
	{
		$contactId = $response['data']['contacts'][0]['contactId'];
	}
	return $contactId;
}

function get_account_id() {
	if ($ic_accountId <= 0)
	{
		echo "Trying to retrieve account Id...";
		$response = callResource("/a", 'GET');
		if ($response['code'] == STATUS_CODE_SUCCESS)
		{
			$ic_accountId = $response['data']['accounts'][0]['accountId'];
			echo "Retrieval successful. Account id = {$ic_accountId}";
		}
		else
		{
			echo "<h1>Error</h1>\n";
			echo "<p>Error Code: {$response['code']}</p>\n";
			dump($response['data']);
		}
	}
	return $ic_accountId;
}

function get_client_folder_id() {
	if ($ic_clientFolderId <= 0)
	{
		$accountId = get_account_id();
		echo "Trying to retrieve clientFolderId in account {$accountId}...";
		$response = callResource("/a/{$accountId}/c/", 'GET');
		if ($response['code'] == STATUS_CODE_SUCCESS)
		{
			$ic_clientFolderId = $response['data']['clientfolders'][0]['clientFolderId'];
			echo "Retrieval successful. ClientFolderId = {$ic_clientFolderId}";
		}
		else
		{
			echo "<h1>Error</h1>\n";
			echo "<p>Error Code: {$response['code']}</p>\n";
			dump($response['data']);
		}
	}
	return $ic_clientFolderId;
}


define('STATUS_CODE_SUCCESS', 200);

function callResource($url, $method, $data = null) {
	$url    = $GLOBALS['config']['apiUrl'] . $url;
	$handle = curl_init();

	$headers = array(
		'Accept: application/json',
		'Content-Type: application/json',
		'Api-Version: 2.0',
		'Api-AppId: ' . $GLOBALS['config']['appId'],
		'Api-Username: ' . $GLOBALS['config']['username'],
		'Api-Password: ' . $GLOBALS['config']['password'],
	);

	curl_setopt($handle, CURLOPT_URL, $url);
	curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);

	switch ($method) {
		case 'POST':
			curl_setopt($handle, CURLOPT_POST, true);
			curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($data));
		break;
		case 'PUT':
			curl_setopt($handle, CURLOPT_PUT, true);
			$file_handle = fopen($data, 'r');
			curl_setopt($handle, CURLOPT_INFILE, $file_handle);
		break;
		case 'DELETE':
			curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
		break;
	}

	$response = curl_exec($handle);
	$response = json_decode($response, true);
	$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

	curl_close($handle);

	return array(
		'code' => $code,
		'data' => $response,
	);
}

function dump($array) {
	echo "<pre>" . print_r($array, true) . "</pre>";
}
