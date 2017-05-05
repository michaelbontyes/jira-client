<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

if(!empty($_POST["project"])){

  // get the project - ajax
  $project = $_POST["project"];

  // define statuses to fetch
  $statuses = array(
    'Backlog'     => 'Backlog',
    'In Progress' => 'In Progress',
    'Done'        => 'Done'
  );

  // initialize list of issues
  $list = [];

  foreach ($statuses as $key => $status) {

    // define the JIRA JQL
    $jql = 'project = '.$project.' AND status = "'.$status.'" ORDER BY updated ASC';

    try {
      $issueService = new IssueService();

      // fetch issues
      $response = $issueService->search($jql);


      // issues walker
      foreach ($response->issues as $issue) {

        // define details
        $issueDetails = array(
          "key"       => $issue->key,
          "summary"   => $issue->fields->summary,
          "type"      => $issue->fields->issuetype->name,
          // "date"      => $issue->fields->duedate->format('Y-m-d H:i:s')
        );

        // push the issue details to the list
        $list[$status][] = $issueDetails;

      }

    } catch (JiraException $e) {
      $this->assertTrue(false, 'Query Failed : '.$e->getMessage());
    }

  }

  // ecnode the JSON for the front-end
  header('Content-Type: application/json');
  echo json_encode($list);

}
