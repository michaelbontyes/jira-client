
<html>
<head>
  <title>Maintenance Reports</title>
  <link rel="stylesheet" href="css/jquery.dynatable.css" />
  <link rel="stylesheet" href="css/reveal.css" />
</head>
<body>

  <?php
  require 'vendor/autoload.php';

  use JiraRestApi\Project\ProjectService;
  use JiraRestApi\JiraException;

  // get the list of projects
  try {
    $proj = new ProjectService();

    // fetch the project objects
    $projects = $proj->getAllProjects();

    // build the html dropdown
    echo '<select name="projects" id="projects">';
    foreach ($projects as $project) {
      echo '<option value="'.$project->key.'">'.$project->name.'</option>';
    }
    echo "</select>";

  } catch (JiraException $e) {
    print("Error Occured! " . $e->getMessage());
  }
  ?>

  <!-- Initial HTML content -->
  <div class="reveal">
    <div id="slides" class="slides">
      <section><h1>Maintenance Report</h1></section>
      <section id="blank"></section><!-- Blank slug -->
    </div>
  </div>
</body>

<script
src="//code.jquery.com/jquery-2.2.4.min.js"
integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
crossorigin="anonymous"></script>
<script src="js/jquery.dynatable.js"></script>
<script src="js/reveal.js"></script>
<script type="text/javascript">

$(document).ready(function(){

  // initialize RevealJS
  Reveal.initialize();

  // prepare the deck
  deck = $('#blank').parent();

  // a blank is initialized and stored as a variable
  wrap = $('#blank').clone()
  .attr('id',null)
  .prop('outerHTML');
  // remove the blank
  $('#blank').remove();

  // fetch the issues on project change
  $("#projects").change(function(){
    var projectName = $(this).val();
    var dataProject = "project="+projectName;
    var id = '';

    // ajax callback
    $.ajax({
      type: "POST",
      url: "getTickets.php",
      data: dataProject,

      // ajax payload
      success: function(result){
        $.each(result, function(idx, obj){

          // convert the status name to an ID
          id = 'table'+idx.replace(/\s+/g, '').toLowerCase();

          // define the HTML variable
          var html = '';

          // build the HTML slide (section)
          html += '<section>';
          html += '<h2>'+idx+'</h2>';
          html += '<table id="'+id+'">';
          html += '<thead><th>Key</th><th>Summary</th><th>Type</th></thead><tbody></tbody>';
          html += '</table></section>';

          // render the HTML slide
          $( "#slides" ).append( html );

          // define the table in the DOM
          var table = $('#'+id);

          // generate the dynatable based on the list of issues
          table.dynatable({
            dataset: {
              records: obj
            }
          });
          table.data('dynatable').settings.dataset.records = obj;

          // refresh the DOM with the new table
          table.data('dynatable').dom.update();

        });

      }
    });

  });

});

</script>
</html>
