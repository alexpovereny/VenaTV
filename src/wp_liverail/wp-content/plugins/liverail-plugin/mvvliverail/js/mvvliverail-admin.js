$(document).ready(function() {
    $('#example').dataTable({
        "dom": '<"top"i>rt<"bottom"flp><"clear">'
    });
    $("table.dataTable tr td").bind("click", dataClick);
    function dataClick(e) {
        console.log('--- dataClick !!!');
        console.log(e);
    }
    /*  // Work!!!
     $("table.dataTable tr td").bind("click", dataClick);
     function dataClick(e) {
     //console.log('--- dataClick !!!');
     //console.log(e);
     //if (e.currentTarget.innerHTML != "") return;
     if (e.currentTarget.contentEditable != null) {
     $(e.currentTarget).attr("contentEditable", true);
     }
     else {
     $(e.currentTarget).append("<input type='text'>");
     }
     }*/

    //   $("#saveButton").bind("click",saveButton);
    //   function saveButton(){
    //    $("table.table tr td").each(function(td, index){
    //        console.log(td);
    //        console.log(index);
    //    });


});


/*$(document).ready(function() {
 $('#example').dataTable({
 "bSort": false, // Disable sorting
 "iDisplayLength": 5, // records per page
 "sDom": "t<'row'<'col-md-6'i><'col-md-6'p>>",
 "sPaginationType": "bootstrap"
 });
 });*/