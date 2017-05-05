<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

// function to fetch issues from JIRA
function fetchIssues($project, $statuses){

  // default project
  $project = (isset($project)) ? $project : 'DEVOPS' ;

  // default statuses to fetch
  $defaultStatuses = array(
    'Backlog'     => 'Backlog',
    'In Progress' => 'In Progress',
    'Done'        => 'Done'
  );

  // default statuses
  $statuses = (isset($statuses)) ? $statuses : $defaultStatuses ;

  // initialize list of issues
  $list = [];

  // walk through statuses
  foreach ($statuses as $key => $status) {

    // define the JIRA JQL
    $jql = 'project = '.$project.' AND status = "'.$status.'" ORDER BY updated ASC';

    try {
      $issueService = new IssueService();

      // fetch issues
      $response = $issueService->search($jql);


      // issues walker
      foreach ($response->issues as $issue) {

        // get epic link
        if (isset($issue->fields->customFields['customfield_10008'])) {
          $jql = 'issue = '.$issue->fields->customFields['customfield_10008'];
          $issueService = new issueService();
          $response = $issueService->search($jql);
          $epicLink = $response->issues[0]->fields->summary;
        }

        // define details
        $issueDetails = array(
          "key"       => $issue->key,
          "summary"   => $issue->fields->summary,
          "type"      => $issue->fields->issuetype->name,
          "epiclink"  => $epicLink,
          // "date"      => $issue->fields->duedate->format('Y-m-d H:i:s')
        );

        // push the issue details to the list
        $list[$status][] = $issueDetails;

      }

    } catch (JiraException $e) {
      $this->assertTrue(false, 'Query Failed : '.$e->getMessage());
    }
  }

  // return the list
  return $list;
}

if(!empty($_POST["project"])){

  // get the project - ajax
  $project = $_POST["project"];

  // fetch issues
  $list = fetchIssues($project);

  // ecnode the JSON for the front-end
  header('Content-Type: application/json');
  echo json_encode($list);

}
