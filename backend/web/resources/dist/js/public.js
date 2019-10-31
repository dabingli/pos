$(function(){
  $("#table").on("click",".dropdown-toggle",function(){
    $($('.fixed-table-container').height($('#table').height()+200))
  });
})
