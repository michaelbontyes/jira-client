<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

if(!empty($_POST["project"])){

  $project = $_POST["project"];
  //$status = 'Backlog';
  // define the JIRA JQL
  //$jql = 'project = '.$project.' AND status = '.$status;
  //$jql = 'project = "'.$project.'" AND status = "'.$status.'" ORDER BY updated ASC';
  //$jql = 'project = '.$project.' AND issuetype in (Epic, "Scheduled Task") AND status in (Backlog, "Selected for Development", "In Progress", Icebox, Validation) ORDER BY due ASC, priority DESC, updated DESC';

  $statuses = array(
    'Backlog'     => 'Backlog',
    'In Progress' => 'In Progress',
    'Done'        => 'Done'
  );
  $list = [];

  foreach ($statuses as $key => $status) {

    $jql = 'project = '.$project.' AND status = "'.$status.'" ORDER BY updated ASC';

  try {
      $issueService = new IssueService();

      // first fetch
      $response = $issueService->search($jql);

      // initialize list of issues



      // issues walker
      foreach ($response->issues as $issue) {

        // define details
        $issueDetails = array(
              "key"       => $issue->key,
              "summary"   => $issue->fields->summary,
              "type"      => $issue->fields->issuetype->name,
              // "date"      => $issue->fields->duedate->format('Y-m-d H:i:s')
        );
        $list[$status][] = $issueDetails;
        //var_dump($issueDetails);
        // display the results for development
        //print (vsprintf("<li> %s %s %s %s</li>\n", $issueDetails));

        // push Issue details to the list of issues
        //$list['Backlog'] = $issueDetails;
        //array_push ( $list, $issueDetails );

      }

      // json encode and dump
      //$issuesJSON = json_encode($issuesList);

      //var_dump($issuesJSON);

      // generate PDF report based on the reveal slides
      //$output = shell_exec('./phantomjs decktape.js reveal http://webserver-ubuntu.edlfb.net/php/ /vagrant/wordpress/php/report.pdf');
      //echo "<pre>$output</pre>";

  } catch (JiraException $e) {
      $this->assertTrue(false, 'Query Failed : '.$e->getMessage());
  }

}
header('Content-Type: application/json');
echo json_encode($list);

}
