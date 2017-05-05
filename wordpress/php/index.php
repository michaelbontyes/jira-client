
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

try {
	$proj = new ProjectService();

	$projects = $proj->getAllProjects();

  echo '<select name="projects" id="projects">';
	foreach ($projects as $project) {
    echo '<option value="'.$project->key.'">'.$project->name.'</option>';
    //var_dump($project->key);
	}
  echo "</select>";
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}



?>


<div class="reveal">
  <div id="slides" class="slides">
      <section><h1>Maintenance Report</h1></section>
      <section id="blank"></section><!-- Blank slug -->
  </div>
</div>
<!-- <div class="reveal">
  <h1>Maintenance report</h1>
	<div id="slides" class="slides"> -->
    <!-- <section id="blank"></section> -->


		<!-- <section>
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
    </section> -->
	<!-- </div>
</div> -->

</body>

<script
src="//code.jquery.com/jquery-2.2.4.min.js"
integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
crossorigin="anonymous"></script>
<script src="js/jquery.dynatable.js"></script>
<script src="js/reveal.js"></script>
<script type="text/javascript">

  // select project

  ;(function(){

})();

function newslide(i){
      // wrap the new content in the blank
      $('<em>Slide added'+i+'</em>').appendTo(deck)
                          .wrap( wrap );
    };

  $(document).ready(function(){


    Reveal.initialize();

    //slide deck wrapper
    deck = $('#blank').parent();

    // a blank is initialized and stored as a variable
    wrap = $('#blank').clone()
                      .attr('id',null)
                      .prop('outerHTML');
    // remove the blank
    $('#blank').remove();



      //Reveal.initialize();
        // Reveal.initialize();
        //
        // //slide deck wrapper
        // deck = $('#blank').parent();
        //
        // // a blank is initialized and stored as a variable
        // wrap = $('#blank').clone()
        //                   .attr('id','blank')
        //                   .prop('outerHTML');
        // // remove the blank
        // $('#blank').remove();


        // function dynaTable(id, data) {
        //   var tableID = $('#' + id);
        //   console.log(tableID);
        //   var table = $('#tableinprogress');
        //   console.log(table);
        //
        //   table.dynatable({
        //             dataset: {
        //                 records: data
        //             }
        //         });
        //   table.data('dynatable').settings.dataset.records = data;
        //   table.data('dynatable').dom.update();
        // }




    $("#projects").change(function(){
      var project = $(this).val();
      var dataString = "project="+project;
      var listName = '';
      var listData = '';
      var html = '';
      var i = 1;
      var id = '';
      var ids = [];



      $.ajax({
        type: "POST",
        url: "getTickets.php",
        data: dataString,

        success: function(result){
          //console.log(result);
          //$("#tickets").html(result);
          $.each(result, function(idx, obj){
            var html = '';
            console.log(idx);
            // listName = idx;
            // listData = obj;
            // console.log(listName);
            // console.log(listData);
            id = 'table'+idx.replace(/\s+/g, '').toLowerCase();
            //ids.push(id);

            //newslide(idx);

            html += '<section>';
            html += '<h2>'+idx+'</h2>';
            html += '<table id="'+id+'">';
            html += '<thead><th>Key</th><th>Summary</th><th>Type</th></thead><tbody></tbody>';
            html += '</table></section>';

            console.log(html);
            $( "#slides" ).append( html );

            //console.log('here');

            //console.log(id);

            var table = $('#'+id);
            console.log(table);

            table.dynatable({
                      dataset: {
                          records: obj
                      }
                  });
            table.data('dynatable').settings.dataset.records = obj;
            table.data('dynatable').dom.update();

            // setTimeout(function(){
            // var tableID = $('#' + id);
            // var table = $(tableID).val();
            //
            // table.dynatable({
            //           dataset: {
            //               records: obj
            //           }
            //       });
            // table.data('dynatable').settings.dataset.records = obj;
            // table.data('dynatable').dom.update();
            //
            // }, 1000);

              //newslide('Woop,woop');

        });



        // ids.forEach(function(entry) {
        //     console.log(entry);
        //
        // });

        // $.each(result, function(idx, obj){
        //   //listName = idx;
        //   var id = 'table'+idx.replace(/\s+/g, '').toLowerCase();
        //   var tableID = $('#' + idx).val();
        //   var table = $('#' + idx).val();
        //
        //   table.dynatable({
        //             dataset: {
        //                 records: obj
        //             }
        //         });
        //   table.data('dynatable').settings.dataset.records = obj;
        //   table.data('dynatable').dom.update();
        //   });




        }
      });

    });

  });

  // prepare json for dynatable
  //var $issuesJSON = <?php print json_encode($issuesJSON); ?>;


  // initialize Reveal

</script>
</html>
