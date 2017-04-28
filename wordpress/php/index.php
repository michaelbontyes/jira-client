
<html>
 <head>

  <title>Maintenance Reports</title>
  <link rel="stylesheet" href="css/jquery.dynatable.css" />
  <link rel="stylesheet" href="css/reveal.css" />
 </head>

 <body>
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

// define the JIRA JQL
$jql = 'project = DEVOPS AND issuetype in (Epic, "Scheduled Task") AND status in (Backlog, "Selected for Development", "In Progress", Icebox, Validation) AND due <= 2w AND Lead in (currentUser()) ORDER BY due ASC, priority DESC, updated DESC';

try {
    $issueService = new IssueService();

    // first fetch
    $response = $issueService->search($jql);

    // initialize list of issues
    $issuesList = [];

    // issues walker
    foreach ($response->issues as $issue) {

      // define details
      $issueDetails = array (
            "key"       => $issue->key,
            "summary"   => $issue->fields->summary,
            "type"      => $issue->fields->issuetype->name,
            "date"      => $issue->fields->duedate->format('Y-m-d H:i:s')
      );

      //var_dump($issueDetails);
      // display the results for development
      //print (vsprintf("<li> %s %s %s %s</li>\n", $issueDetails));

      // push Issue details to the list of issues
      array_push ( $issuesList , $issueDetails );
    }

    // json encode and dump
    $issuesJSON = json_encode($issuesList);
    //var_dump($issuesJSON);

    // generate PDF report based on the reveal slides
    $output = shell_exec('./phantomjs decktape.js reveal http://webserver-ubuntu.edlfb.net/php/ /vagrant/wordpress/php/report.pdf');
    echo "<pre>$output</pre>";

} catch (JiraException $e) {
    $this->assertTrue(false, 'Query Failed : '.$e->getMessage());
}
?>

<div class="reveal">
  <h1>Maintenance report</h1>
	<div class="slides">
		<section>
      <h2>Backlog</h2>
      <table id="table1">
      <thead>
       <th>Key</th>
       <th>Summary</th>
       <th>Type</th>
       <th>Date</th>
     </thead>
     <tbody>
     </tbody>
    </table></section>
		<section>
      <h2>Issues in progress</h2>
      <table id="table2">
        <thead>
         <th>Key</th>
         <th>Summary</th>
         <th>Type</th>
         <th>Date</th>
       </thead>
       <tbody>
       </tbody>
      </table>
    </section>
	</div>
</div>

</body>

<script
src="https://code.jquery.com/jquery-2.2.4.min.js"
integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
crossorigin="anonymous"></script>
<script src="js/jquery.dynatable.js"></script>
<script src="js/reveal.js"></script>
<script type="text/javascript">

  // prepare json for dynatable
  var $issuesJSON = <?php print json_encode($issuesJSON); ?>;
  var response = JSON.parse($issuesJSON);

  // set table
  $('#table1').dynatable({
    dataset: {
      records: response
    }
  });
  $('#table2').dynatable({
    dataset: {
      records: response
    }
  });

  // initialize Reveal
  Reveal.initialize();
</script>
</html>
